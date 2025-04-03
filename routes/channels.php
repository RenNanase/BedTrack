<?php

use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Log;

// Changed to public channel - no authentication needed for global chat
Broadcast::channel('chat.{chatRoomId}', function ($user = null, $chatRoomId) {
    Log::info('Public channel access for chat.' . $chatRoomId, [
        'user_id' => $user ? $user->id : 'guest',
        'username' => $user ? $user->name : 'guest'
    ]);
    
    // Allow access to all authenticated users for global chat
    return true;
});
