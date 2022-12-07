<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmailRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'business_id',
        'subject',
        'content',
        'recipients',
        'schedule',
        'status',
        'is_active',
    ];
}
