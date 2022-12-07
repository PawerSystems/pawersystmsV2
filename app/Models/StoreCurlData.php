<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StoreCurlData extends Model
{
    use HasFactory;

    protected $fillable = [
        'business_id',
        'title',
        'price',
        'description',
        'image',
    ];

}
