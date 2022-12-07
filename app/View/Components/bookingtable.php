<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\Support\Facades\Auth;
use App\Models\Business;
use App\Models\Date;
use Carbon\Carbon;

class bookingtable extends Component
{
    /**
     * Create a new component instance.
     *
     * @return void
     */

    public $dates;
    public $dateFormat;
    public $order;
    public $CPRForInsurance;

    public function __construct($dimention,$therapist='')
    {   
        $this->dateFormat = Business::find(Auth::user()->business_id)->Settings->where('key','date_format')->first(); 
        $this->CPRForInsurance = Business::find(Auth::user()->business_id)->Settings->where('key','cpr_emp_fields_insurance')->first();

        $this->order = 'ASC';

        if($dimention == '<') 
            $this->order = 'DESC';

        if($therapist)
            $this->dates = Date::where('business_id',Auth::user()->business_id)->where('date',$dimention,date('Y-m-d'))->where('is_active','1')->where(\DB::raw('md5(user_id)') , $therapist)->orderBy('date',$this->order)->simplePaginate(6);
        else
            $this->dates = Date::where('business_id',Auth::user()->business_id)->where('date',$dimention,date('Y-m-d'))->where('is_active','1')->orderBy('date',$this->order)->simplePaginate(6);    
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|string
     */
    public function render()
    {
        return view('components.bookingtable');
    }
}
