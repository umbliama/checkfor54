<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('notifications.{userId}', function ($user, $userId) {
    return (int) $user->id === (int) $userId;
});

 
Broadcast::channel('chat.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('notifications.global', function () {
    return true;
});
Broadcast::channel('chat.group.{groupId}', function ($user, $groupId) {
    // Проверьте, имеет ли пользователь доступ к этой группе
    return $user->groups()->where('id', $groupId)->exists();
});