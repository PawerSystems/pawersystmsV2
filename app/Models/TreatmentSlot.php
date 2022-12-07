<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TreatmentSlot extends Model
{
    use HasFactory;

    protected $fillable = [
        'business_id',
        'date_id',
        'user_id',
        'treatment_id',
        'treatment_part_id',
        'payment_method_id',
        'department_id',
        'status',
        'time',
        'comment',
        'is_active',
        'survey_job_id',
        'CPR',
    ];


    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }

    public function receipt()
    {
        return $this->hasOne('App\Models\Receipt');
    }

    public function clip()
    {
        return $this->hasOne('App\Models\UsedClip')->where('is_active',1);
    }

    public function paymentMethod()
    {
        return $this->hasOne('App\Models\PaymentMethod')->where('is_active',1);
    }

    public function paymentMethodTitle()
    {
        return $this->belongsTo('App\Models\PaymentMethod', 'payment_method_id');
    }

    public function treatmentPartTitle()
    {
        return $this->belongsTo('App\Models\TreatmentPart', 'treatment_part_id');
    }

    public function treatmentPart()
    {
        return $this->hasOne('App\Models\TreatmentPart')->where('is_active',1);
    }

    public function deactiveclip()
    {
        return $this->hasOne('App\Models\UsedClip');
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
