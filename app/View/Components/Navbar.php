<?php

namespace App\View\Components;

use Illuminate\View\Component;
use App\Models\Business;
use App\Models\TreatmentSlot;
use App\Models\EventSlot;
use App\Models\Permission;
use Illuminate\Support\Facades\Auth;


class Navbar extends Component
{
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public $type;
    public $treatmentBooking;
    public $eventBooking;
    public $settings;
    public $business;
    public $businesses;
    

    public function __construct($type)
    {
        $this->type = $type;
        $this->business = Business::find(\Auth::user()->business_id);
        $this->businesses = Business::where('is_active',1)->where('business_name','!=','www')->get();
        $this->settings = Business::find(\Auth::user()->business_id)->Settings;
        // $this->treatmentBooking = TreatmentSlot::where('user_id',Auth::user()->id)->count();
        // $this->eventBooking = EventSlot::where('user_id',Auth::user()->id)->count();
        $this->eventBooking = Permission::where('business_id',Auth::user()->business_id)->where('title','Event Create')->count();
        $this->treatmentBooking = Permission::where('business_id',Auth::user()->business_id)->where('title','Date Create')->count();
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|string
     */
    public function render()
    {
        return view('components.navbar');
    }
}
