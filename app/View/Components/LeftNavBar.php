<?php

namespace App\View\Components;

use Illuminate\View\Component;
use App\Models\Business;
use App\Models\TreatmentSlot;
use App\Models\EventSlot;
use App\Models\Permission;
use App\Models\BusinessRegistration;
use Illuminate\Support\Facades\Auth;


class LeftNavBar extends Component
{
    /**
     * Create a new component instance.
     *
     * @return void
     */

    public $settings;
    public $treatmentBooking;
    public $eventBooking;
    public $requests;

    public function __construct()
    {
        $this->settings = Business::find(Auth::user()->business_id)->Settings;
        $this->eventBooking = Permission::where('business_id',Auth::user()->business_id)->where('title','Event Create')->count();
        $this->treatmentBooking = Permission::where('business_id',Auth::user()->business_id)->where('title','Date Create')->count();
        $this->requests = BusinessRegistration::where('status',1)->get()->count();
        // $this->treatmentBooking = TreatmentSlot::where('user_id',Auth::user()->id)->count();
        // $this->eventBooking = EventSlot::where('user_id',Auth::user()->id)->count();
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|string
     */
    public function render()
    {
        return view('components.left-nav-bar');
    }
}
