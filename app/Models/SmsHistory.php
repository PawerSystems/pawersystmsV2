<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SmsHistory extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'business_id',
        'user_id',
        'message_id',
        'message_price',
        'remaining_balance',
        'network',
        'to',
        'content',
        'received_at',
        'status',
        'created_at',
        'updated_at',
    ];
}
