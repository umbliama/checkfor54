<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('notifications.{userId}', function ($user, $userId) {
    \Log::info("Авторизация для канала notifications.$userId", ['текущий user_id' => $user->id]);
    return (int) $user->id === (int) $userId;
});

Broadcast::channel('notifications.global', function () {
    return true;
});