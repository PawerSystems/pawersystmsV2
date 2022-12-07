<?php

namespace App\View\Components;

use Illuminate\View\Component;
use App\Models\Business;


class webFooter extends Component
{
    /**
     * Create a new component instance.
     *
     * @return void
     */

    public $email;
    public $business;

    public function __construct()
    {
        $business =  Business::select()->where('business_name',session('business_name'))->first();
        
        if( !$business->brand_name )
            $this->business = $business->business_name;
        else
            $this->business = $business->brand_name;    
        
        if( $business->business_email )
            $this->email = $business->business_email;
        else{
            $user =  Business::find($business->id)->superAdmin;
            if($user)
                $this->email = $user->email;
            else
                $this->email = Business::find($business->id)->superAdminRole->userFromRole->email;
        }

    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|string
     */
    public function render()
    {
        return view('components.web-footer');
    }
}
