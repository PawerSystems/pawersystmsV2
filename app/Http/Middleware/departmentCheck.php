<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Business;
use Illuminate\Support\Facades\Auth;


class departmentCheck
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
        $setting = Business::find(Auth::user()->business_id)->Settings->where('key','department')->first();
        if( $setting->value == 'true' ){
            return $next($request);
        }
        else
            return redirect('/dashboard');
    }
}
