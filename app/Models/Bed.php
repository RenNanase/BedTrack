<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Bed extends Model
{
    use HasFactory;

    protected $fillable = [
        'bed_number',
        'room_id',
        'ward_id',
        'status',
        'is_crib',
        'patient_name',
        'patient_category',
        'gender',
        'mrn',
        'notes',
        'status_changed_at',
        'housekeeping_started_at',
        'housekeeping_remarks',
        'has_hazard',
        'hazard_notes',
        'bed_type',
        'occupied_at',
    ];

    protected $casts = [
        'status_changed_at' => 'datetime',
        'housekeeping_started_at' => 'datetime',
        'has_hazard' => 'boolean',
        'is_crib' => 'boolean',
        'occupied_at' => 'datetime',
    ];

    /**
     * Get the room that owns the bed.
     */
    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    public function statusLogs(): HasMany
    {
        return $this->hasMany(BedStatusLog::class);
    }
}
