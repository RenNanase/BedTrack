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

    public function index()
    {
        $user = Auth::user();

        // Get the global chat
        $globalChat = ChatRoom::where('type', 'global')->first();

        // Get all users except the current user
        $users = User::where('id', '!=', $user->id)->get();

        return view('chat.index', compact('globalChat', 'users'));
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
            'chat_room_id' => 'required|exists:chat_rooms,id'
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
            'type' => 'text'
        ]);

        // Load the user relationship for the broadcast
        $message->load('user');

        try {
            \Log::info('Broadcasting message to channel chat.' . $chatRoom->id, [
                'message_id' => $message->id,
                'user_id' => $message->user_id,
                'content' => $message->message,
            ]);
            
            broadcast(new NewChatMessage($message))->toOthers();
            
            \Log::info('Message broadcasted successfully');
        } catch (\Exception $e) {
            \Log::error('Error broadcasting message: ' . $e->getMessage(), [
                'exception' => $e,
                'message_id' => $message->id
            ]);
        }

        return response()->json($message);
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

        broadcast(new \App\Events\ChatTyping(
            $user->id,
            $user->name,
            $request->chat_room_id,
            $request->is_typing
        ))->toOthers();

        return response()->json(['success' => true]);
    }
}
