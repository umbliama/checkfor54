<?php

namespace App\Services;

use Chatify\ChatifyMessenger as BaseChatifyMessenger;
use App\Models\ChMessage as Message;

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
}
