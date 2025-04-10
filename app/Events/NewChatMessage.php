<?php

namespace App\Events;

use App\Models\ChatMessage;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewChatMessage implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $message;

    /**
     * Create a new event instance.
     */
    public function __construct(ChatMessage $message)
    {
        // Eager load user and replyTo relations
        $this->message = $message->load(['user', 'replyTo.user']);
        
        \Log::debug('Constructing NewChatMessage event', ['message_id' => $message->id]);
    }

    /**
     * Get the channels the event should broadcast on.
     */
    public function broadcastOn()
    {
        $channel = new Channel('chat.' . $this->message->chat_room_id);
        \Log::info('Broadcasting message on channel: ' . $channel->name, [
            'message_id' => $this->message->id,
            'chat_room_id' => $this->message->chat_room_id,
            'user_id' => $this->message->user_id
        ]);
        
        return $channel;
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs()
    {
        return 'new-message';
    }

    /**
     * Get the data to broadcast.
     */
    public function broadcastWith()
    {
        $data = [
            'message' => [
                'id' => $this->message->id,
                'user_id' => $this->message->user_id,
                'message' => $this->message->message,
                'created_at' => $this->message->created_at->toDateTimeString(),
                'user' => [
                    'id' => $this->message->user->id,
                    'name' => $this->message->user->name,
                ],
                'reply_to' => $this->message->reply_to ? [
                    'id' => $this->message->reply_to->id,
                    'message' => $this->message->reply_to->message,
                    'user' => [
                        'id' => $this->message->reply_to->user->id,
                        'name' => $this->message->reply_to->user->name,
                    ],
                ] : null,
            ],
        ];
        
        \Log::info('Broadcasting data for message ' . $this->message->id . ': ' . json_encode($data));
        
        return $data;
    }
}
