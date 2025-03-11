<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\ChatGroup;
use App\Models\ChatGroupMember;
use Illuminate\Http\Request;

class ChatGroupController extends Controller
{
    public function createGroup(Request $request)
    {

        $request->validate([
            'name'    => 'required|string|max:255',
            'members' => 'required|array',
        ]);

        $group = ChatGroup::create([
            'name'       => $request->name,
            'created_by' => $request->created_by,
        ]);

        foreach ($request->members as $memberId) {
            ChatGroupMember::create([
                'group_id' => $group->id,
                'user_id'  => $memberId,
            ]);
        }

        return response()->json(['message' => 'Group created successfully', 'group' => $group], 201);
    }

    public function getGroupMessages($groupId)
    {
        $group = ChatGroup::with('messages')->find($groupId);
    
        if (!$group) {
            return response()->json(['error' => 'Group not found'], 404);
        }
    
        return response()->json([
            'messages' => $group->messages->map(function ($message) {
                return [
                    'id' => $message->id,
                    'body' => $message->body,
                    'from_id' => $message->from_id,
                    'created_at' => $message->created_at->format('Y-m-d H:i:s')
                ];
            })
        ]);
    }
    

    public function getUserGroups(Request $request, $userId)
    {
        $groups = ChatGroup::whereHas('members', function ($query) use ($userId) {
            $query->where('user_id', $userId);
        })->get();

        return response()->json($groups);
    }

    public function getGroupInfo(Request $request)
    {
        $groupId = $request->input('group_id');
        $group   = ChatGroup::with('messages')->find($groupId);

        if (! $group) {
            return response()->json(['error' => 'Group not found'], 404);
        }

        return response()->json([
            'fetch'        => true,
            'name'         => $group->name,
            'group_avatar' => asset($group->avatar ?? 'images/default-group.png'),
            'messages'     => $group->messages,
        ]);
    }

}
