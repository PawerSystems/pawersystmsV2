<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Business;

class Language
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
        $host = explode('.', $_SERVER['HTTP_HOST'])[0];
        $local = config('app.locale');

        $business = Business::where('business_name',$host)->first();
        if($business == NULL){
            echo "Opps.... Not a valid URL!";
            exit();
        }

        $setting = $business->Settings->where('key','system_language')->first();
        if($setting)
            $local = $setting->value;
        if(session()->has('locale')){
            \App::setLocale(session('locale', $local));
        }
        else{
            session(['locale' =>  $local]);
            \App::setLocale($local);
        }
        
        return $next($request);
    }
}
