<?php

namespace App\View\Components;

use Illuminate\View\Component;
use App\Models\Business;


class paymentMethods extends Component
{
    /**
     * Create a new component instance.
     *
     * @return void
     */

    public $selected;
    public $methods;


    public function __construct($selected)
    {
        $this->selected = $selected;
        $this->methods = Business::find(\Auth::user()->business_id)->PaymentMethods->where('is_active',1);
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|string
     */
    public function render()
    {
        return view('components.payment-methods');
    }
}
