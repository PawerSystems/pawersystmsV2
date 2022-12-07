<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TreatmentPartTranslation extends Model
{
    use HasFactory;

    protected $fillable = [
        'business_id',
        'treatment_part_id',
        'key',
        'value',
    ];
}
