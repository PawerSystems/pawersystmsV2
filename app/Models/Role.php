<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    protected $fillable = [
        'business_id',
        'title',
    ];

    public function permissions(){
        return $this->belongsToMany('App\Models\Permission')->orderBy('id','DESC');
    }

    public function userFromRole(){
        return $this->hasOne('App\Models\User', 'role');
    }

    public function activeUserFromRole(){
        return $this->hasOne('App\Models\User', 'role')->where('is_active',1);
    }
}
