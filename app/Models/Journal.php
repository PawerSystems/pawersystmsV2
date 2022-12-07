<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Journal extends Model
{
    use HasFactory;


    protected $fillable = [
        'business_id',
        'user_id',
        'customer_id',
        'image',
        'comment',
        'is_active',
        'created_at',
        'updated_at',
    ];

    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }

    public function uUser()
    {
        return $this->belongsTo('App\Models\User','update_user_id','id');
    }

    public function customer()
    {
        return $this->belongsTo('App\Models\User','customer_id','id');
    }
}
