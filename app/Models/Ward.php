<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Ward extends Model
{
    use HasFactory;

    protected $fillable = [
        'ward_name',
        'is_blocked',
        'block_remarks',
        'blocked_at',
        'blocked_by'
    ];

    protected $casts = [
        'is_blocked' => 'boolean',
        'blocked_at' => 'datetime'
    ];

    /**
     * The users that belong to the ward.
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_wards');
    }

    /**
     * Get the rooms for the ward.
     */
    public function rooms(): HasMany
    {
        return $this->hasMany(Room::class);
    }

    public function blockedByUser()
    {
        return $this->belongsTo(User::class, 'blocked_by');
    }

    public function scopeNotBlocked($query)
    {
        return $query->where('is_blocked', false);
    }
}
