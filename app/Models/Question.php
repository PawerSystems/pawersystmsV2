<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    use HasFactory;

    protected $fillable = [
        'business_id',
        'title',
        'is_active',
    ];

    public function options()
    {
        return $this->hasMany('App\Models\Option');
    }  

}
