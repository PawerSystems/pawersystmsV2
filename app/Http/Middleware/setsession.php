<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class setsession
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
        
        if( session('business_name')){
            return $next($request);
        }
        else{
            $host = explode('.', $_SERVER['HTTP_HOST'])[0];
            session(['business_name' =>  $host]);
            return $next($request);
        }   
        
    }
    
}
