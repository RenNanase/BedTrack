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
}
