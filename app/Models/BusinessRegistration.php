<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BusinessRegistration extends Model
{
    use HasFactory;

    protected $fillable = [
        'business_name',
        'name',
        'email',
        'number',
        'status',
        'plan',
        'message',
    ];
}
