<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'business_id',
        'user_id',
        'name',
        'date',
        'time',
        'duration',
        'slots',
        'description',
        'price',
        'clips',
        'status',
        'is_guest',
        'is_active',
    ];

    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }
    
    public function eventActiveSlots()
    {
        return $this->hasMany('App\Models\EventSlot')->where('is_active',1)->orderBy('status','DESC');
    }    

    public function eventWaitingSlots()
    {
        return $this->hasMany('App\Models\EventSlot')->where([['is_active',1],['status',0]]);
    }   
    public function eventGuestSlots()
    {
        return $this->hasMany('App\Models\EventSlot')->where([['is_active',1],['status',1],['is_guest',1]]);
    }   
    public function eventBookedSlots()
    {
        return $this->hasMany('App\Models\EventSlot')->where([['is_active',1],['status',1]]);
    }    

    
}
