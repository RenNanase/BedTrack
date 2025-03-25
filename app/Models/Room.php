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
        'ward_id',
        'capacity',
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
}
