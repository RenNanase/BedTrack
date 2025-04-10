<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Room extends Model
{
    use HasFactory;

    protected $fillable = [
        'room_name',
        'room_number',
        'ward_id',
        'capacity',
        'room_type',
        'is_blocked',
        'block_remarks',
        'blocked_at',
        'blocked_by',
        'sequence'
    ];

    protected $casts = [
        'is_blocked' => 'boolean',
        'blocked_at' => 'datetime',
    ];

    /**
     * Get the ward that owns the room.
     */
    public function ward(): BelongsTo
    {
        return $this->belongsTo(Ward::class);
    }

    /**
     * Get the beds for the room.
     */
    public function beds(): HasMany
    {
        return $this->hasMany(Bed::class);
    }

    /**
     * Get the bassinets for the room.
     */
    public function bassinets(): HasMany
    {
        return $this->hasMany(Bassinet::class);
    }

    /**
     * Get the user who blocked the room.
     */
    public function blockedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'blocked_by');
    }

    public function scopeNotBlocked($query)
    {
        return $query->where('is_blocked', false);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sequence', 'asc');
    }
}
