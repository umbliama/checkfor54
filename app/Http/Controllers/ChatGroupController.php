<?php

namespace App\Http\Controllers;
use App\Services\CustomChatifyMessenger;
use Chatify\Facades\ChatifyMessenger as Chatify;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\ChatGroup;
use App\Models\ChatGroupMember;

class ChatGroupController extends Controller
{
    public function createGroup(Request $request)
    {
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

    public function getGroupMessages($groupId)
    {
        $group = ChatGroup::with('messages')->findOrFail($groupId);
        return response()->json($group);
    }

    public function idFetchData(Request $request)
    {
        $id = $request['id'];

        $isGroup = \App\Models\ChatGroup::where('id', $id)->exists();

        $group = \App\Models\ChatGroup::find($request['id']);

        if (!$group) {
            return Response::json([
                'error' => 'Group not found.',
            ], 404);
        }

        return Response::json([
            'favorite' => false, 
            'fetch' => [
                'id' => $group->id,
                'name' => $group->name,
                'is_group' => true,
            ],
        ]);


    }
    public function send(Request $request)
    {
        \Log::info('Group ID:', ['group_id' => $request->input('group_id')]);

        // default variables
        $error = (object) [
            'status' => 0,
            'message' => null
        ];
        $attachment = null;
        $attachment_title = null;

        // if there is attachment [file]
        if ($request->hasFile('file')) {
            // allowed extensions
            $allowed_images = Chatify::getAllowedImages();
            $allowed_files = Chatify::getAllowedFiles();
            $allowed = array_merge($allowed_images, $allowed_files);

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
        $n = new CustomChatifyMessenger();
        if (!$error->status) {
            $message = $n->newGroupMessage([
                'from_id' => Auth::user()->id,
                'to_id' => $request['id'],
                'group_id' => $request->input('group_id'),
                'body' => htmlentities(trim($request['message']), ENT_QUOTES, 'UTF-8'),
                'attachment' => ($attachment) ? json_encode((object) [
                    'new_name' => $attachment,
                    'old_name' => htmlentities(trim($attachment_title), ENT_QUOTES, 'UTF-8'),
                ]) : null,
            ]);
            $messageData = Chatify::parseMessage($message);
            if (Auth::user()->id != $request['id']) {
                Chatify::push("private-chatify." . $request['id'], 'messaging', [
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

    public function fetchMessagesByGroup(Request $request)
    {
        $groupId = $request->group_id; // Ensure the request includes a group_id
    
        if (!$groupId) {
            return Response::json(['error' => 'Group ID is required'], 400);
        }
        $n = new CustomChatifyMessenger();

        $query = $n->fetchMessagesGQuery($request['id'])
            ->where('group_id', $groupId) // Filter by group ID
            ->latest();
    
        $messages = $query->paginate($request->per_page);
        $totalMessages = $messages->total();
        $lastPage = $messages->lastPage();
    
        $response = [
            'total' => $totalMessages,
            'last_page' => $lastPage,
            'last_message_id' => collect($messages->items())->last()->id ?? null,
            'messages' => '',
        ];
    
        if ($totalMessages < 1) {
            $response['messages'] = '<p class="message-hint center-el"><span>No messages found for this group.</span></p>';
            return Response::json($response);
        }
    
        if (count($messages->items()) < 1) {
            $response['messages'] = '';
            return Response::json($response);
        }
    
        $allMessages = null;
        foreach ($messages->reverse() as $message) {
            $allMessages .= Chatify::messageCard(Chatify::parseMessage($message));
        }
    
        $response['messages'] = $allMessages;
        return Response::json($response);
    }
}
