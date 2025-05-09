<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;


class NotificationCountUpdated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    public $count;
    public $userId;

    /**
     * Create a new event instance.
     */
    public function __construct($count, $userId)
    {
        $this->count = $count;
        $this->userId = $userId;

    }
    public function broadcastAs()
    {
        return 'NotificationCountUpdated';
    }
    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('notifications.' . $this->userId)
        ];
    }

    public function broadcastWith()
    {
        return [
            'count' => $this->count,
        ];
    }

    public function failed(\Exception $exception)
    {
        // Log the exception or notify the development team
        \Log::error('NotificationCountUpdated job failed', ['error' => $exception->getMessage()]);
    }
}
