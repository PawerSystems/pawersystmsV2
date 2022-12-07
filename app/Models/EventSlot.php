<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventSlot extends Model
{
    use HasFactory;

    protected $fillable = [
        'business_id',
        'event_id',
        'user_id',
        'parent_slot',
        'is_guest',
        'status',
        'comment',
        'is_active',
        'min_bookings',
    ];


    public function event()
    {
        return $this->belongsTo('App\Models\Event');
    }

    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }
    
    public function deactiveclip()
    {
        return $this->hasOne('App\Models\UsedClip');
    }
}
