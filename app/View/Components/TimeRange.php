<?php

namespace App\View\Components;

use Illuminate\View\Component;
use App\Models\Business;

class TimeRange extends Component
{
    /**
     * Create a new component instance.
     *
     * @return void
     */

    public $selected;
    public $range;
    public $start = '00:00';
    public $end = '24:00';
    public $interval;
    public $bookingDetails = array();

    
    public function __construct($selected,$booked, $start='00:00',$end='24:00')
    {
        $this->start = $start;
        $this->end = $end;
        
        foreach( $booked as $key => $b ){
            $this->bookingDetails[$key] = $b->time;
        }

        $interval = Business::select('time_interval')->where('business_name',session('business_name'))->first();
        $this->interval = $interval->time_interval." mins";
        $this->selected = $selected;

        $startTime = strtotime($this->start); 
        $endTime   = strtotime($this->end);
        $returnTimeFormat = 'G:i';
    
        $current   = time(); 
        $addTime   = strtotime('+'.$this->interval, $current); 
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
        return view('components.time-range');
    }
}
