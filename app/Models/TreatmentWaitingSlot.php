<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TreatmentWaitingSlot extends Model
{
    use HasFactory;

    protected $fillable = [
        'business_id',
        'date_id',
        'user_id',
        'treatment_id',
        'department_id',
        'status',
        'time',
        'comment',
        'is_active',
    ];


    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }

    public function treatment()
    {
        return $this->belongsTo('App\Models\Treatment');
    }

    public function date()
    {
        return $this->belongsTo('App\Models\Date');
    }

    public function department()
    {
        return $this->belongsTo('App\Models\Department');
    }
}
