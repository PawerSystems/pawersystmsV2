<?php

namespace App\View\Components;

use Illuminate\View\Component;
use App\Models\Business;


class WebMenu extends Component
{
    /**
     * Create a new component instance.
     *
     * @return void
     */

    public $menu; 

    public function __construct()
    {
        $business =  Business::select()->where('business_name',session('business_name'))->first();
        $this->menu = Business::find($business->id)->websites->where('is_active',1);
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|string
     */
    public function render()
    {
        return view('components.web-menu');
    }
}
