<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Option extends Model
{
    use HasFactory;

    protected $fillable = [
        'business_id',
        'question_id',
        'value',
        'is_active',
    ];

    public function question()
    {
        return $this->belongsTo('App\Models\Question');
    }  

}
