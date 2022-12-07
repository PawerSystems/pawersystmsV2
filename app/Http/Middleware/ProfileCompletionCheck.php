<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Business;


class ProfileCompletionCheck
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if(\Request::is('profile') || \Request::is('updateProfile'))
            return $next($request);
        else{
            //--------------------- number field required --------------
            if(empty(auth()->user()->number)){
                $request->session()->flash('error', __('customer.fill_missing_details'));
                return redirect('profile');
            }

            //---------------- Check for age and gender field -------------------------
            $field = Business::find(auth()->user()->business_id)->Settings->where('key','age_gender_mandatory')->first(); 
            $check = ($field ? $field->value : 'false');
            if( $check == 'true' && empty(auth()->user()->gender) || empty(auth()->user()->birth_year)){
                $request->session()->flash('error', __('customer.fill_missing_details'));
                return redirect('profile');
            }

            //---------------- Check for Employee Number field -------------------------
            $mdr = Business::find(auth()->user()->business_id)->Settings->where('key','mdr_field')->first(); 
            $checkMdr = ($mdr ? $mdr->value : 'false');
            if( $checkMdr == 'true' && empty(auth()->user()->mednr) ){
                $request->session()->flash('error', __('customer.fill_missing_details'));
                return redirect('profile');
            }
            
            return $next($request);
        }
    }
}
