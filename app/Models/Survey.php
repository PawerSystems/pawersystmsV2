<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Survey extends Model
{
    use HasFactory;

    protected $fillable = [
        'business_id',
        'survey_id',
        'question_id',
        'option_id',
        'comment',
        'name',
        'email',
    ];

    public function question()
    {
        return $this->belongsTo('App\Models\Question');
    }

    public function option()
    {
        return $this->belongsTo('App\Models\Option');
    }
}
