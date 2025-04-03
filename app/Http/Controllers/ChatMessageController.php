<?php

namespace App\Http\Controllers;

use App\Events\ChatTyping;
use App\Events\NewChatMessage;
use App\Models\ChatMessage;
use App\Models\ChatRoom;
use App\Models\MessageRead;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ChatMessageController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'message' => 'required|string|max:500',
            'chat_room_id' => 'required|exists:chat_rooms,id',
        ]);

        // Check if user has access to this chat room
        $chatRoom = ChatRoom::findOrFail($validated['chat_room_id']);
        
        // For global chat, any authenticated user can post
        if ($chatRoom->type !== 'global') {
            // For other chat types, check if user is a member
            $hasAccess = $chatRoom->users()->where('users.id', Auth::id())->exists();
            if (!$hasAccess) {
                return response()->json(['message' => 'You do not have access to this chat room'], 403);
            }
        }

        // Create the message
        $message = new ChatMessage();
        $message->message = $validated['message'];
        $message->chat_room_id = $validated['chat_room_id'];
        $message->user_id = Auth::id();
        $message->save();

        // Mark as read by sender
        MessageRead::create([
            'message_id' => $message->id,
            'user_id' => Auth::id(),
        ]);

        // Load user for response
        $message->load('user');

        // Broadcast the message to the channel
        try {
            Log::info('Broadcasting new chat message', [
                'message_id' => $message->id,
                'chat_room_id' => $message->chat_room_id,
                'user_id' => $message->user_id
            ]);
            
            event(new NewChatMessage($message));
            
            Log::info('Broadcast event dispatched successfully');
        } catch (\Exception $e) {
            Log::error('Error broadcasting message:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }

        return response()->json($message);
    }

    public function typing(Request $request)
    {
        $validated = $request->validate([
            'chat_room_id' => 'required|exists:chat_rooms,id',
            'is_typing' => 'required|boolean'
        ]);

        // Get the current user
        $user = Auth::user();

        try {
            Log::info('Broadcasting typing status', [
                'user_id' => $user->id,
                'username' => $user->name,
                'chat_room_id' => $validated['chat_room_id'],
                'is_typing' => $validated['is_typing']
            ]);
            
            // Broadcast the typing status
            event(new ChatTyping(
                $user->id,
                $user->name,
                $validated['chat_room_id'],
                $validated['is_typing']
            ));
            
            Log::info('Typing status broadcast dispatched successfully');
        } catch (\Exception $e) {
            Log::error('Error broadcasting typing status:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }

        return response()->json(['success' => true]);
    }

    public function markAsRead(Request $request)
    {
        $validated = $request->validate([
            'message_id' => 'required|exists:chat_messages,id',
        ]);

        // Check if already marked as read
        $exists = MessageRead::where('message_id', $validated['message_id'])
            ->where('user_id', Auth::id())
            ->exists();

        if (!$exists) {
            MessageRead::create([
                'message_id' => $validated['message_id'],
                'user_id' => Auth::id(),
            ]);
        }

        return response()->json(['success' => true]);
    }
} 