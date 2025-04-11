<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Bassinet extends Model
{
    use HasFactory;

    protected $fillable = [
        'bassinet_number',
        'room_id',
        'ward_id',
        'status',
        'patient_name',
        'mrn',
        'gender',
        'patient_category',
        'has_hazard',
        'hazard_notes',
        'status_changed_at',
        'mother_name',
        'mother_mrn',
        'occupied_at',
        'notes'
    ];

    protected $casts = [
        'has_hazard' => 'boolean',
        'status_changed_at' => 'datetime',
        'occupied_at' => 'datetime'
    ];

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    public function ward(): BelongsTo
    {
        return $this->belongsTo(Ward::class);
    }
} 