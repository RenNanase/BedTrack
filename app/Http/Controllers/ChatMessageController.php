<?php

namespace App\Http\Controllers;

use App\Events\ChatTyping;
use App\Events\NewChatMessage as NewChatMessageEvent;
use App\Models\ChatMessage;
use App\Models\ChatRoom;
use App\Models\MessageRead;
use App\Notifications\NewChatMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ChatMessageController extends Controller
{
    public function store(Request $request, ChatRoom $chatRoom)
    {
        $request->validate([
            'message' => 'required|string',
            'reply_to_id' => 'nullable|exists:chat_messages,id',
        ]);

        $message = $chatRoom->messages()->create([
            'user_id' => auth()->id(),
            'message' => $request->message,
            'reply_to_id' => $request->reply_to_id,
        ]);

        // Broadcast the message
        broadcast(new NewChatMessageEvent($message))->toOthers();

        // Notify other users in the room
        $chatRoom->users()
            ->where('users.id', '!=', auth()->id())
            ->each(function ($user) use ($message) {
                $user->notify(new NewChatMessage($message));
            });

        return response()->json($message->load('user', 'replyTo'));
    }

    public function reply(Request $request, ChatMessage $message)
    {
        $request->validate([
            'message' => 'required|string',
        ]);

        $reply = $message->chatRoom->messages()->create([
            'user_id' => auth()->id(),
            'message' => $request->message,
            'reply_to_id' => $message->id,
        ]);

        // Notify other users in the room
        $message->chatRoom->users()
            ->where('users.id', '!=', auth()->id())
            ->each(function ($user) use ($reply) {
                $user->notify(new NewChatMessage($reply));
            });

        return response()->json($reply->load('user', 'replyTo'));
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