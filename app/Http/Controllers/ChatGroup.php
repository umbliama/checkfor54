<?php

namespace App\Http\Controllers;
use Chatify\Facades\ChatifyMessenger as Chatify;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class ChatGroup extends Controller
{
    public function createGroup(Request $request) {
        $request->validate([
            'name' => 'required|string|max:255',
            'members' => 'required|array'
        ]);

        $group = ChatGroup::create([
            'name' => $request->name,
            'created_by' => Auth::id()
        ]);

        foreach ($request->members as $memberId) {
            ChatGroupMember::create([
                'group_id' => $group->id,
                'user_id' => $memberId
            ]);
        }

        return response()->json(['message' => 'Group created successfully', 'group' => $group], 201);
    }

    public function getGroupMessages($groupId) {
        $group = ChatGroup::with('messages')->findOrFail($groupId);
        return response()->json($group);
    }

    public function idFetchData(Request $request)
    {
        $id = $request['id'];
        
        // Check if ID belongs to a group
        $isGroup = \App\Models\ChatGroup::where('id', $id)->exists();
    
        if ($isGroup) {
            // Fetch group details
            $group = \App\Models\ChatGroup::find($request['id']);
    
            if (!$group) {
                return Response::json([
                    'error' => 'Group not found.',
                ], 404);
            }
    
            return Response::json([
                'favorite' => false, // Adjust as needed for groups
                'fetch' => [
                    'id' => $group->id,
                    'name' => $group->name,
                    'is_group' => true,
                ],
            ]);
        } 
    
        // If not a group, handle as user
        $fetch = User::find($request['id']);
        $favorite = Chatify::inFavorite($request['id']);
        $userAvatar = $fetch ? Chatify::getUserWithAvatar($fetch)->avatar : null;
    
        return \Response::json([
            'favorite' => $favorite,
            'fetch' => $fetch,
            'user_avatar' => $userAvatar,
        ]);
    }
    public function send(Request $request)
    {
        // default variables
        $error = (object)[
            'status' => 0,
            'message' => null
        ];
        $attachment = null;
        $attachment_title = null;

        // if there is attachment [file]
        if ($request->hasFile('file')) {
            // allowed extensions
            $allowed_images = Chatify::getAllowedImages();
            $allowed_files  = Chatify::getAllowedFiles();
            $allowed        = array_merge($allowed_images, $allowed_files);

            $file = $request->file('file');
            // check file size
            if ($file->getSize() < Chatify::getMaxUploadSize()) {
                if (in_array(strtolower($file->extension()), $allowed)) {
                    // get attachment name
                    $attachment_title = $file->getClientOriginalName();
                    // upload attachment and store the new name
                    $attachment = Str::uuid() . "." . $file->extension();
                    $file->storeAs(config('chatify.attachments.folder'), $attachment, config('chatify.storage_disk_name'));
                } else {
                    $error->status = 1;
                    $error->message = "File extension not allowed!";
                }
            } else {
                $error->status = 1;
                $error->message = "File size you are trying to upload is too large!";
            }
        }

        if (!$error->status) {
            $message = Chatify::newMessage([
                'from_id' => Auth::user()->id,
                'to_id' => $request['id'],
                'group_id' => $request['group_id'],
                'body' => htmlentities(trim($request['message']), ENT_QUOTES, 'UTF-8'),
                'attachment' => ($attachment) ? json_encode((object)[
                    'new_name' => $attachment,
                    'old_name' => htmlentities(trim($attachment_title), ENT_QUOTES, 'UTF-8'),
                ]) : null,
            ]);
            $messageData = Chatify::parseMessage($message);
            if (Auth::user()->id != $request['id']) {
                Chatify::push("private-chatify.".$request['id'], 'messaging', [
                    'from_id' => Auth::user()->id,
                    'to_id' => $request['id'],
                    'message' => Chatify::messageCard($messageData, true)
                ]);
            }
        }

        // send the response
        return Response::json([
            'status' => '200',
            'error' => $error,
            'message' => Chatify::messageCard(@$messageData),
            'tempID' => $request['temporaryMsgId'],
        ]);
    }
}
