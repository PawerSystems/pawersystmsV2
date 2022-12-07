<?php

namespace App\View\Components;

use Illuminate\View\Component;
use App\Models\Business;


class treatmentsList extends Component
{

    public $treatments;
    public $CPRForInsurance;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($treatments)
    {
        $this->CPRForInsurance = Business::find(\Auth::user()->business_id)->Settings->where('key','cpr_emp_fields_insurance')->first();
        $this->treatments = $treatments;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|string
     */
    public function render()
    {
        return view('components.treatments-list');
    }
}
