<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserWard extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'ward_id',
    ];
}
