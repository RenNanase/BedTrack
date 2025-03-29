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
        $request->validate([
            'message' => 'required|string',
            'chat_room_id' => 'required|exists:chat_rooms,id'
        ]);

        $chatRoom = ChatRoom::findOrFail($request->chat_room_id);

        // Check if user has access to this chat
        if (!$chatRoom->users->contains(Auth::id())) {
            abort(403, 'You do not have access to this chat.');
        }

        $message = $chatRoom->messages()->create([
            'user_id' => Auth::id(),
            'message' => $request->message,
            'type' => 'text'
        ]);

        broadcast(new NewChatMessage($message))->toOthers();

        return response()->json($message->load('user'));
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
}
