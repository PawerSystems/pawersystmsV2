<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Business;
use App\Models\visitor;

class visitors
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
        $business = Business::where('business_name',session('business_name'))->first();
        $last_url_segment = $request->segments();
        if(empty($last_url_segment)){
           array_push($last_url_segment,'home');
        }
        
        if(session()->has('visitor')){
            if($last_url_segment[0] != 'getDateData'){
                visitor::create([
                    'business_id' => $business->id,
                    'code' => session('visitor'),
                    'page' => $last_url_segment[0],
                ]);
            }
        }
        else{
            session(['visitor' =>  \Str::random(50)]);
            if($last_url_segment[0] != 'getDateData'){
                visitor::create([
                    'business_id' => $business->id,
                    'code' => session('visitor'),
                    'page' => $last_url_segment[0],
                ]);        
            }
        }

        return $next($request);
    }
}
