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
        'is_crib',
        'patient_name',
        'patient_category',
        'gender',
        'mrn',
        'notes',
        'status_changed_at',
        'housekeeping_started_at',
        'has_hazard',
        'hazard_notes',
    ];

    protected $casts = [
        'status_changed_at' => 'datetime',
        'housekeeping_started_at' => 'datetime',
        'has_hazard' => 'boolean',
        'is_crib' => 'boolean',
    ];

    /**
     * Get the room that owns the bed.
     */
    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }
}
