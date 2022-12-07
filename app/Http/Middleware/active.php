<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Business;


class active
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
        $bus = Business::select('is_active')->where('id',Auth::user()->business_id)->first();
        if (!Auth::user()->is_active || $bus->is_active == 0) {
            $request->session()->flash('warning','Sorry your account or business has been deactivated!');
            return redirect('/deactive');
        }
        return $next($request);
    }
}
