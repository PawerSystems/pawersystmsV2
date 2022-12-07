<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Receipt extends Model
{
    use HasFactory;


    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'business_id',
        'treatment_slot_id',
        'data',
    ];

    public function slot()
    {
        return $this->belongsTo('App\Models\TreatmentSlot');
    }


}