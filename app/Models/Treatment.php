<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Treatment extends Model
{
    use HasFactory;


    protected $fillable = [
        'business_id',
        'treatment_name',
        'inter',
        'clips',
        'price',
        'is_active',
        'is_visible',
        'description',
    ];


    // public function treatmentDates()
    // {
    //     return $this->hasMany('App\Models\Date')->where('is_active',1);
    // }  

    public function dates()
    {
        return $this->belongsToMany('App\Models\Date')->where('is_active',1);
    }   

    public function upCommingDates()
    {
        return $this->belongsToMany('App\Models\Date')->where('is_active',1)->where('date','>=',date('Y-m-d'));
    }   
    
    public function allDates()
    {
        return $this->belongsToMany('App\Models\Date');
    }   
}
