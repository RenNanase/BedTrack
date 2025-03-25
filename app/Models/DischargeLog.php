<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DischargeLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'bed_id',
        'room_id',
        'patient_name',
        'patient_info',
        'discharged_at',
    ];

    protected $casts = [
        'discharged_at' => 'datetime',
    ];

    /**
     * Get the bed associated with the discharge.
     */
    public function bed(): BelongsTo
    {
        return $this->belongsTo(Bed::class);
    }

    /**
     * Get the room associated with the discharge.
     */
    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }
}
