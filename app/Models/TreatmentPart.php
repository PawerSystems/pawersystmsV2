<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TreatmentPart extends Model
{
    use HasFactory;

    protected $fillable = [
        'business_id',
        'title',
        'Torder',
        'is_active',
    ];

    
    public function translations()
    {
        return $this->hasMany('App\Models\TreatmentPartTranslation');
    }
}
