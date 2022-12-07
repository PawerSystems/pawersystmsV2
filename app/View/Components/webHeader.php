<?php

namespace App\View\Components;

use Illuminate\View\Component;
use App\Models\Business;
use App\Models\TreatmentSlot;
use App\Models\EventSlot;
use App\Models\Permission;

class webHeader extends Component
{
    /**
     * Create a new component instance.
     *
     * @return void
     */

    public $menu;
    public $business;
    public $treatmentBooking;
    public $eventBooking;
    public $settings;

    public function __construct()
    {
        $this->business =  Business::select()->where('business_name',session('business_name'))->first();
        $this->settings = $this->business->Settings;
        $this->menu = $this->business->websites->where('is_active',1);
        if(auth()->user()){
            $this->eventBooking = Permission::where('business_id',auth()->user()->business_id)->where('title','Event Create')->count();
            $this->treatmentBooking = Permission::where('business_id',auth()->user()->business_id)->where('title','Date Create')->count();
            // $this->treatmentBooking = TreatmentSlot::where('user_id',auth()->user()->id)->count();
            // $this->eventBooking = EventSlot::where('user_id',auth()->user()->id)->count();
        }
        else{
            $this->treatmentBooking = 0;
            $this->eventBooking = 0;
        }

    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|string
     */
    public function render()
    {
        return view('components.web-header');
    }
}
