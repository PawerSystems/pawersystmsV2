<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\User;
use Laravel\Cashier\Subscription;

class SubscriptionCheck
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if(\Request::is('plans/checkout/*') || \Request::is('plans/process') || \Request::is('plans/show'))
            return $next($request);
        else{
            if(in_array(auth()->user()->role,['owner','Owner'])){
                return $next($request);
            }
            //--------------------- check subscription --------------
            $user = User::where([['business_id',auth()->user()->business_id],['stripe_id','!=',null]])->first();
            if($user){
                $subscription = Subscription::where('user_id',$user->id)->first();
            }else{
                return redirect('/plans/checkout/plan_Munxr7zkyxxiuD');
            }

            return $next($request);
        }
    }
}
