<?php

namespace App\View\Components;

use Illuminate\View\Component;
use App\Models\Business;
use App\Models\Department;


class DepartmentsList extends Component
{
    /**
     * Create a new component instance.
     *
     * @return void
     */

    public $departments;

    public function __construct()
    {
        $this->departments = Business::find(\Auth::user()->business_id)->Departments->where('is_active',1);
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|string
     */
    public function render()
    {
        return view('components.departments-list');
    }
}
