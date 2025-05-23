<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ChatMessage extends Model
{
    protected $fillable = [
        'chat_room_id',
        'user_id',
        'message',
        'type',
        'file_url',
        'reply_to_id',
    ];

    protected $with = ['user', 'replyTo'];

    public function chatRoom(): BelongsTo
    {
        return $this->belongsTo(ChatRoom::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function reads(): HasMany
    {
        return $this->hasMany(ChatMessageRead::class);
    }

    public function isReadBy(User $user): bool
    {
        return $this->reads()->where('user_id', $user->id)->exists();
    }

    public function replyTo()
    {
        return $this->belongsTo(ChatMessage::class, 'reply_to_id')->with('user');
    }

    public function replies()
    {
        return $this->hasMany(ChatMessage::class, 'reply_to_id');
    }
}
