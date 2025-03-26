<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Bed extends Model
{
    use HasFactory;

    protected $fillable = [
        'bed_number',
        'room_id',
        'status',
        'patient_name',
        'patient_category',
        'gender',
        'mrn',
        'notes',
        'status_changed_at',
    ];

    protected $casts = [
        'status_changed_at' => 'datetime',
    ];

    /**
     * Get the room that owns the bed.
     */
    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }
}
