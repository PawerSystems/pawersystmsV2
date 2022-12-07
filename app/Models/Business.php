<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Business extends Model
{
    use HasFactory;


    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'business_name',
        'languages',
        'access_modules',
        'brand_name',
        'logo',
        'business_email',
        'time_interval',
        'is_active',
    ];

    public function users()
    {
        return $this->hasMany('App\Models\User')->orderBy('name');
    }

    public function logs()
    {
        return $this->hasMany('App\Models\LocationLog');
    }

    public function superAdmin()
    {
        return $this->hasOne('App\Models\User')->where('role','Super Admin')->orderBy('id','ASC');  
    }

    public function superAdminRole()
    {
        return $this->hasOne('App\Models\Role')->where('title','Super Admin');
    }
    
    public function user()
    {
        return $this->hasOne('App\Models\User');
    }

    public function offer()
    {
        return $this->hasOne('App\Models\StoreCurlData');
    }

    public function websites()
    {
        return $this->hasMany('App\Models\Website')->orderBy('id','ASC');
    }

    public function treatments()
    {
        return $this->hasMany('App\Models\Treatment')->orderBy('id','DESC');
    }

    public function treatment()
    {
        return $this->hasOne('App\Models\Treatment');
    }

    public function Dates()
    {
        return $this->hasMany('App\Models\Date')->orderBy('date','ASC');
    }

    public function JounalDates()
    {
        return $this->hasMany('App\Models\Date')->orderBy('date','DESC');
    }

    public function cards()
    {
        return $this->hasMany('App\Models\Card')->orderBy('id','DESC');
    }

    public function Events()
    {
        return $this->hasMany('App\Models\Event')->orderBy('date','ASC');
    }

    public function Event()
    {
        return $this->hasOne('App\Models\Event');
    }

    public function EventDeletedSlots()
    {
        return $this->hasMany('App\Models\EventSlot')->where('is_active',0)->orderBy('updated_at','DESC');
    }

    public function PaymentMethods()
    {
        return $this->hasMany('App\Models\PaymentMethod')->orderBy('title');
    }

    public function TreatmentParts()
    {
        return $this->hasMany('App\Models\TreatmentPart')->orderBy('Torder');
    }

    public function Notes()
    {
        return $this->hasMany('App\Models\Journal')->orderBy('id','DESC');
    }

    public function Departments()
    {
        return $this->hasMany('App\Models\Department')->orderBy('name');
    }

    public function Settings()
    {
        return $this->hasMany('App\Models\Setting');
    }

    public function Surveys()
    {
        return $this->hasMany('App\Models\Survey')->orderBy('id','desc');
    }

    public function Questions()
    {
        return $this->hasMany('App\Models\Question');
    }

    public function Options()
    {
        return $this->hasMany('App\Models\Option');
    }

    public function emails()
    {
        return $this->hasMany('App\Models\EmailRecord')->orderBy('id','DESC');
    }

    public function Roles(){
        return $this->hasMany('App\Models\Role')->orderBy('id','DESC');
    }

    public function Permissions(){
        return $this->hasMany('App\Models\Permission');
    }

}
