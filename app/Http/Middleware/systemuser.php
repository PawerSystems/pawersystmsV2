<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Business;

class systemuser
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
        $bus = Business::find(Auth::user()->business_id);
        if( Session('business_name') != null ){
            if ($bus->business_name != Session('business_name')) {
                $request->session()->flash('warning','You are not part of this system!');
                return redirect('/deactive');
            }
            else{
                return $next($request);
            }
        }
        else{
            if( Auth::user()->role == "Owner" ){
                return $next($request);
            }
            else{
                $request->session()->flash('warning','You are not part of this system!');
                return redirect('/deactive');
            }
        }
        
    }
}
