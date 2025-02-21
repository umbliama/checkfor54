<?php

use Illuminate\Support\Facades\Broadcast;



Broadcast::channel('notifications.{userId}', function ($user, $userId) {
    return (int) $user->id === (int) $userId;
});
Broadcast::channel('notifications.global', function () {
    // Allow anyone to listen to this channel
    return true;
});