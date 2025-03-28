<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('chat.{chatRoomId}', function ($user, $chatRoomId) {
    return $user->chatRooms()->where('chat_rooms.id', $chatRoomId)->exists();
});
