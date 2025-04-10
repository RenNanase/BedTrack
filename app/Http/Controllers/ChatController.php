<?php

namespace App\Http\Controllers;

use App\Models\ChatRoom;
use App\Models\ChatMessage;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Events\NewChatMessage;

class ChatController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function show(ChatRoom $chatRoom)
    {
        // For global chat, allow all authenticated users
        if ($chatRoom->type === 'global') {
            // Automatically attach the user to the global chat if not already attached
            if (!$chatRoom->users->contains(Auth::id())) {
                $chatRoom->users()->attach(Auth::id());
            }

            $messages = $chatRoom->messages()
                ->with('user')
                ->orderBy('created_at', 'asc')
                ->get();

            // Mark messages as read
            $unreadMessages = $chatRoom->messages()
                ->where('user_id', '!=', Auth::id())
                ->whereDoesntHave('reads', function($query) {
                    $query->where('user_id', Auth::id());
                })
                ->get();

            foreach ($unreadMessages as $message) {
                $message->reads()->create([
                    'user_id' => Auth::id(),
                    'read_at' => now()
                ]);
            }
        } else {
            // Check if user has access to this chat
            if (!$chatRoom->users->contains(Auth::id())) {
                abort(403, 'You do not have access to this chat.');
            }

            $messages = $chatRoom->messages()
                ->with('user')
                ->orderBy('created_at', 'asc')
                ->get();
        }

        return view('chat.show', compact('chatRoom', 'messages'));
    }

    public function store(Request $request)
    {
        \Log::info('Received chat message request:', $request->all());
        
        $request->validate([
            'message' => 'required|string',
            'chat_room_id' => 'required|exists:chat_rooms,id',
            'reply_to_id' => 'nullable|exists:chat_messages,id'
        ]);

        $chatRoom = ChatRoom::findOrFail($request->chat_room_id);

        // For global chat, automatically attach the user if not already attached
        if ($chatRoom->type === 'global' && !$chatRoom->users->contains(Auth::id())) {
            $chatRoom->users()->attach(Auth::id());
        }
        // For other chats, check if user has access
        else if (!$chatRoom->users->contains(Auth::id())) {
            abort(403, 'You do not have access to this chat.');
        }

        $message = $chatRoom->messages()->create([
            'user_id' => Auth::id(),
            'message' => $request->message,
            'reply_to_id' => $request->reply_to_id,
            'type' => 'text'
        ]);

        // Load the user relationship and reply information for the broadcast
        $message->load(['user', 'replyTo.user']);

        try {
            \Log::info('Broadcasting message to channel chat.' . $chatRoom->id, [
                'message_id' => $message->id,
                'user_id' => $message->user_id,
                'content' => $message->message,
                'reply_to_id' => $message->reply_to_id,
            ]);
            
            // Use event helper instead of broadcast to ensure it goes through the event system properly
            event(new NewChatMessage($message));
            
            \Log::info('Message broadcasted successfully');
        } catch (\Exception $e) {
            \Log::error('Error broadcasting message: ' . $e->getMessage(), [
                'exception' => $e,
                'message_id' => $message->id
            ]);
        }

        return response()->json([
            'message' => $message->message,
            'user' => $message->user,
            'created_at' => $message->created_at,
            'reply_to_id' => $message->reply_to_id,
            'reply_to_user' => $message->replyTo ? $message->replyTo->user : null,
            'reply_to_message' => $message->replyTo ? $message->replyTo->message : null
        ]);
    }

    public function getMessages(ChatRoom $chatRoom)
    {
        // Check if user has access to this chat
        if (!$chatRoom->users->contains(Auth::id())) {
            abort(403, 'You do not have access to this chat.');
        }

        $messages = $chatRoom->messages()
            ->with('user')
            ->orderBy('created_at', 'asc')
            ->get();

        return response()->json($messages);
    }

    public function updateName(Request $request, ChatRoom $chatRoom)
    {
        // Check if user has access to this chat
        if (!$chatRoom->users->contains(Auth::id())) {
            abort(403, 'You do not have access to this chat.');
        }

        $request->validate([
            'name' => 'required|string|max:255'
        ]);

        $chatRoom->update([
            'name' => $request->name
        ]);

        return response()->json(['success' => true, 'name' => $chatRoom->name]);
    }

    public function typing(Request $request)
    {
        $request->validate([
            'chat_room_id' => 'required|exists:chat_rooms,id',
            'is_typing' => 'required|boolean'
        ]);

        $user = Auth::user();
        $chatRoom = ChatRoom::findOrFail($request->chat_room_id);

        // Check if user has access to this chat
        if (!$chatRoom->users->contains($user->id)) {
            abort(403, 'You do not have access to this chat.');
        }

        try {
            \Log::info('Broadcasting typing status', [
                'user_id' => $user->id,
                'username' => $user->name,
                'chat_room_id' => $request->chat_room_id,
                'is_typing' => $request->is_typing
            ]);
            
            event(new \App\Events\ChatTyping(
                $user->id,
                $user->name,
                $request->chat_room_id,
                $request->is_typing
            ));
            
            \Log::info('Typing status broadcast dispatched successfully');
        } catch (\Exception $e) {
            \Log::error('Error broadcasting typing status: ' . $e->getMessage(), [
                'exception' => $e
            ]);
        }

        return response()->json(['success' => true]);
    }
}
