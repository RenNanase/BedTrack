<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ChatTyping implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $userId;
    public $username;
    public $chatRoomId;
    public $isTyping;

    /**
     * Create a new event instance.
     */
    public function __construct($userId, $username, $chatRoomId, $isTyping)
    {
        $this->userId = $userId;
        $this->username = $username;
        $this->chatRoomId = $chatRoomId;
        $this->isTyping = $isTyping;
        
        \Log::debug('Constructing ChatTyping event', [
            'user_id' => $userId, 
            'chat_room_id' => $chatRoomId, 
            'is_typing' => $isTyping
        ]);
    }

    /**
     * Get the channels the event should broadcast on.
     */
    public function broadcastOn(): array
    {
        $channel = new Channel('chat.' . $this->chatRoomId);
        \Log::info('Broadcasting typing event on channel: ' . $channel->name, [
            'user_id' => $this->userId,
            'username' => $this->username,
            'is_typing' => $this->isTyping
        ]);
        
        return [
            $channel,
        ];
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'user-typing';
    }

    /**
     * Get the data to broadcast.
     */
    public function broadcastWith(): array
    {
        $data = [
            'user_id' => $this->userId,
            'username' => $this->username,
            'is_typing' => $this->isTyping,
            'timestamp' => now()->toDateTimeString()
        ];
        
        \Log::info('Broadcasting typing data: ' . json_encode($data));
        
        return $data;
    }
}
