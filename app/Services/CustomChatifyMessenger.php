<?php

namespace App\Services;

use Chatify\ChatifyMessenger as BaseChatifyMessenger;
use App\Models\ChMessage as Message;
use Illuminate\Support\Facades\Auth;


class CustomChatifyMessenger extends BaseChatifyMessenger
{
    public function newGroupMessage($data)
    {
        $message = new Message();
        $message->from_id = $data['from_id'];
        $message->to_id = $data['to_id'];
        $message->group_id = $data['group_id'] ?? null;
        $message->body = $data['body'];
        $message->attachment = $data['attachment'] ?? null;
        $message->save();

        return $message;
    }

    public function fetchMessagesGQuery($group_id)
    {
        return Message::where('group_id', $group_id);
    }

    public static function getGroupItem($group)
    {
        // Customize this HTML structure as needed
        return view('Chatify::layouts.groupItem', ['group' => $group])->render();
    }
}
