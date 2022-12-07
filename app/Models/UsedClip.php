<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UsedClip extends Model
{
    use HasFactory;

    protected $fillable = [
        'card_id',
        'treatment_id',
        'treatment_slot_id',
        'event_slot_id',
        'event_id',
        'amount',
        'is_active',
    ];

    public function card(){
        return $this->belongsTo('App\Models\Card');
    }
}
