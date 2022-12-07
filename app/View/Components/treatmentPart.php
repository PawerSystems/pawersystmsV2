<?php

namespace App\View\Components;

use Illuminate\View\Component;
use App\Models\Business;


class treatmentPart extends Component
{
    /**
     * Create a new component instance.
     *
     * @return void
     */

    public $selected;
    public $parts;

    public function __construct($selected)
    {
        $this->selected = $selected;
        $this->parts = Business::find(\Auth::user()->business_id)->TreatmentParts->where('is_active',1);
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|string
     */
    public function render()
    {
        return view('components.treatment-part');
    }
}
