<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Card extends Model
{
    use HasFactory;

    protected $fillable = [
        'business_id',
        'user_id',
        'name',
        'expiry_date',
        'clips',
        'type',
        'is_active',
    ];

    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }

    public function clipUsed()
    {
        return $this->hasMany('App\Models\UsedClip')->where('is_active',1);
    } 
    
    public function allClipsUsed(){
        return $this->hasMany('App\Models\UsedClip');
    }
}
