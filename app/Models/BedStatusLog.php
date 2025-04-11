<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BedStatusLog extends Model
{
    protected $fillable = [
        'bed_id',
        'previous_status',
        'new_status',
        'housekeeping_remarks',
        'changed_by',
        'changed_at'
    ];

    protected $casts = [
        'changed_at' => 'datetime'
    ];

    public function bed(): BelongsTo
    {
        return $this->belongsTo(Bed::class);
    }
} 