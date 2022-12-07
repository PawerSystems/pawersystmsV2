<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;
use Laravel\Cashier\Billable;

class User extends Authenticatable
{
    use HasApiTokens;
    use HasFactory;
    use HasProfilePhoto;
    use Notifiable;
    use TwoFactorAuthenticatable;
    use Billable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'number',
        'password',
        'role',
        'language',
        'access',
        'free_txt',
        'country_id',
        'is_active',
        'is_therapist',
        'is_subscribe',
        'is_logged_in',
        'will_notify',
        'business_id',
        'cprnr',
        'mednr',
        'profile_photo_path',
        'birth_year',
        'gender',
        'created_by',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'profile_photo_url',
    ];

    /**
     * Route notifications for the Nexmo channel.
     *
     * @param  \Illuminate\Notifications\Notification  $notification
     * @return string
     */
    public function routeNotificationForNexmo($notification)
    {
        return $this->number;
        //return '923058270599';
        //return '4520778747';
        
    }

    public function bookings()
    {
        return $this->hasMany('App\Models\TreatmentSlot')->where('parent_slot',NULL)->where('is_active',1);
    }

    public function cards()
    {
        return $this->hasMany('App\Models\Card');
    }

    public function treatmentCards()
    {
        return $this->hasMany('App\Models\Card')->where([['is_active',1],['type',1]]);
    }

    public function enentCards()
    {
        return $this->hasMany('App\Models\Card')->where([['is_active',1],['type',2]]);
    }

    public function LastTreatment(){
        return $this->hasMany('App\Models\TreatmentSlot')->where('is_active',1)->orderBy('id','DESC');
    }

    public function roles(){
        return $this->belongsToMany('App\Models\Role')->orderBy('id','DESC');
    }

    public function roleName(){
        return $this->belongsTo('App\Models\Role');
    }
}
