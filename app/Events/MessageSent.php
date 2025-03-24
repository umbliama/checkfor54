<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\Message;

class MessageSent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $message;
    public function __construct(Message $message)
    {
        $this->message = $message;
    }
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('chat.' . $this->message->user_id),
        ];
    }

    public function broadcastWith()
    {
        return [
            'id'        => $this->message->id,
            'user_id'   => $this->message->user_id,
            'message'   => $this->message->message,
            'sent_at'   => $this->message->created_at->toDateTimeString(),
        ];
    }
}
