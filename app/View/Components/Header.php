<?php

namespace App\View\Components;

use Illuminate\View\Component;
use App\Models\Business;
use Illuminate\Support\Facades\Auth;

class Header extends Component
{
    /**
     * Create a new component instance.
     *
     * @return void
     */

    public $class; 
    public $wrapper;
    public $settings;
    public $business;

    public function __construct($class,$wrapper)
    {
        $this->class = $class;
        $this->wrapper = $wrapper;
        $this->business = Business::find(\Auth::user()->business_id);
        $this->settings = Business::find(\Auth::user()->business_id)->Settings;

    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|string
     */
    public function render()
    {
        return view('components.header');
    }
}
