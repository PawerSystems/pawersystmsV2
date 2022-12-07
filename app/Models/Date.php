<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Date extends Model
{
    use HasFactory;

    protected $fillable = [
        'business_id',
        'user_id',
        'date',
        'from',
        'till',
        'recurrence',
        'recurring_num',
        'description',
        'is_active',
        'waiting_list',
    ];

    
    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }

    public function business()
    {
        return $this->belongsTo('App\Models\Business');
    }

    public function treatment()
    {
        return $this->belongsTo('App\Models\Treatment');
    }
      
    public function treatmentNotInsured()
    {
        return $this->belongsToMany('App\Models\Treatment')->where('is_insurance',0);
    }  
    
    public function treatments()
    {
        return $this->belongsToMany('App\Models\Treatment');
    }

    public function treatmentSlotLunch()
    {
        return $this->hasMany('App\Models\TreatmentSlot')->where('status','Lunch')->take(1);
    }   

    public function treatmentSlotLunchTime()
    {
        return $this->hasOne('App\Models\TreatmentSlot')->where('status','Lunch');
    }   

    public function treatmentSlots()
    {
        return $this->hasMany('App\Models\TreatmentSlot')->where('is_active',1);
    }    

    public function deletedSlots()
    {
        return $this->hasMany('App\Models\TreatmentSlot')->where('is_active',0)->where('parent_slot',Null)->where('status','Booked');
    }    

}
