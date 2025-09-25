<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TransferLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'source_bed_id',
        'source_room_id',
        'source_bassinet_id',
        'destination_bed_id',
        'destination_bassinet_id',
        'destination_room_id',
        'patient_name',
        'patient_category',
        'gender',
        'mrn',
        'notes',
        'transferred_at',
        'had_hazard',
        'maintained_hazard',
        'transfer_remarks',
    ];

    protected $casts = [
        'transferred_at' => 'datetime',
    ];

    /**
     * Get the source bed of the transfer.
     */
    public function sourceBed(): BelongsTo
    {
        return $this->belongsTo(Bed::class, 'source_bed_id');
    }

    /**
     * Get the destination bed of the transfer.
     */
    public function destinationBed(): BelongsTo
    {
        return $this->belongsTo(Bed::class, 'destination_bed_id');
    }

    /**
     * Get the source room of the transfer.
     */
    public function sourceRoom(): BelongsTo
    {
        return $this->belongsTo(Room::class, 'source_room_id');
    }

    /**
     * Get the destination room of the transfer.
     */
    public function destinationRoom(): BelongsTo
    {
        return $this->belongsTo(Room::class, 'destination_room_id');
    }

    public function sourceBassinet()
    {
        return $this->belongsTo(Bassinet::class, 'source_bassinet_id');
    }
    
    public function destinationBassinet()
    {
        return $this->belongsTo(Bassinet::class, 'destination_bassinet_id');
    }
}
