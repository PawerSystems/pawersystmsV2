<?php

namespace App\View\Components;

use Illuminate\View\Component;
use App\Models\Business;
use App\Models\Department;
use App\Models\Date;

class bookingslots extends Component
{
    /**
     * Create a new component instance.
     *
     * @return void
     */

    public $start;
    public $end;
    public $interval;
    public $range;
    public $bookingDetails = array();
    public $time;
    public $treatments;
    public $waitingList;
    public $dateID;
    public $mobilePaySetting;
    public $receiptOption;

    public function __construct($booked,$start,$end,$treatments,$dateID)
    {
        $this->mobilePaySetting = Business::find(\Auth::user()->business_id)->Settings->where('key','mobile_pay')->first(); 
        $dateFormat = Business::find(\Auth::user()->business_id)->Settings->where('key','date_format')->first(); 
        $timeFormat = Business::find(\Auth::user()->business_id)->Settings->where('key','time_format')->first(); 
        $this->dateID = $dateID;
        $this->waitingList = Date::find($dateID);
        $interval = Business::select('time_interval')->where('business_name',session('business_name'))->first();
        $this->receiptOption = Business::find(\Auth::user()->business_id)->Settings->where('key','receipt_option')->first(); 
        
        $this->interval = $interval->time_interval;
        $this->treatments = $treatments;

        foreach( $booked as $b ){

            $treatmentName = '';
            $departmentName = '';
            $price  = '';
            
            $treatment = $treatments->firstWhere('id', $b->treatment_id);
            $treatment = collect($treatment)->all();

            if( !empty($treatment) ){
                $treatmentName = $treatment['treatment_name'].' ('.($treatment['time_shown'] ?: $treatment['inter']).' min)';
                $price = $treatment['price'] == NULL ? 0 : $treatment['price'];
            }

            if( !empty($b->department_id) )
                $departmentName = $b->department->name;

            $this->time = $b->time;
            $this->bookingDetails[$this->time]['status'] = $b->status;
            $this->bookingDetails[$this->time]['comment'] = $b->comment;
            $this->bookingDetails[$this->time]['created'] = $b->created_at;
            $this->bookingDetails[$this->time]['user_id'] = $b->user_id;
            $this->bookingDetails[$this->time]['treatment'] = $treatmentName;
            $this->bookingDetails[$this->time]['price'] = $price;
            $this->bookingDetails[$this->time]['department'] = $departmentName;
            $this->bookingDetails[$this->time]['parent'] = $b->parent_slot;
            $this->bookingDetails[$this->time]['bookedtime'] = \Carbon\Carbon::parse($b->created_at)->format($dateFormat->value.($timeFormat->value == 12 ? ' h:i:s a' : ' H:i:s' ));
            $this->bookingDetails[$this->time]['id'] = $b->id;
            $this->bookingDetails[$this->time]['treatment_id'] = $b->treatment_id;
            $this->bookingDetails[$this->time]['clips'] = $b->clip;
            $this->bookingDetails[$this->time]['payment'] = ($b->paymentMethodTitle ? $b->paymentMethodTitle->title : NULL);
            $this->bookingDetails[$this->time]['part'] = $b->treatment_part_id;
            $this->bookingDetails[$this->time]['receipt'] = $b->receipt;


            if($b->clip != NULL){
                $this->bookingDetails[$this->time]['card'] = $b->clip->card_id;
            }
        }

        if($end == '0:00')
            $end = '23:59';

        $this->start = $start;
        $this->end = $end;

        $startTime = strtotime($this->start); 
        $endTime   = strtotime($this->end);
        $returnTimeFormat = 'G:i';
    
        $current   = time(); 
        $addTime   = strtotime('+'.$this->interval.' mins', $current); 
        $diff      = $addTime - $current;
    
        $times = array(); 
        while ($startTime < $endTime) { 
            $times[] = date($returnTimeFormat, $startTime); 
            $startTime += $diff; 
        } 
        $times[] = date($returnTimeFormat, $startTime); 
        $this->range = $times; 
    }


    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|string
     */
    public function render()
    {
        return view('components.bookingslots');
    }
}
