<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LocationLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'business_id',
        'model',
        'comment',
        'created_at',
        'updated_at',
        'action_by',
    ];

}
