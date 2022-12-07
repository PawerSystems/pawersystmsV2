<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Stripe\Plan;
use App\Models\Plan as ModelsPlan;
use App\Models\User;
use Laravel\Cashier\Subscription;

class SubscriptionController extends Controller
{
    public function index(){
        // $user = auth()->user();
        // return view('stripe.subscription',[ 'intent' => $user->createSetupIntent() ]);
    }

    //---------------------------------------------//
    // public function singleCharge(Request $request){

    //     $amount = $request->amount * 100;
    //     $paymentMethod = $request->payment_method;

    //     $user = auth()->user();
    //     $user->createOrGetStripeCustomer();

    //     $paymentMethod = $user->addPaymentMethod($paymentMethod);

    //     $user->charge($amount,$paymentMethod->id);

    //     return redirect('/subscriptions');
    // }

    //---------------------------------------------//
    public function createPlan(){
        return view('stripe.plans.create');
    }

    //---------------------------------------------//
    public function savePlan(Request $request){

        \Stripe\Stripe::setApiKey(config('services.stripe.secret'));

        $validatedData = $request->validate([
            'name' => ['required','max:100'],
            'amount' => ['required'],
            'description' => ['required','string'],
            'interval_count' => ['required'],
            'period'  => ['required','string'],
        ]);
        
        try{
            //------ Saving this plan in stripe ----//
            $plan = Plan::create([
                'product' => [ 
                    'name' => $request->name 
                ],
                'amount' => $request->amount * 100,
                'currency' => 'dkk',
                'interval' => $request->period,
                'interval_count' => $request->interval_count, //--- reccurssion type
            ]);

            //------ Adding in DB -----//
            ModelsPlan::create([
                'plan_id' => $plan->id,
                'name' => $request->name,
                'price' => $request->amount,
                'billing_method' => $plan->interval,
                'currency' => $plan->currency,
                'interval_count' => $plan->interval_count,
                'description' => $request->description,
                'trialDays' => $request->trialDays,
            ]);

            $request->session()->flash('success','Plan has been created successfully!');
        }
        catch(Exception $ex){
            $request->session()->flash('error',$ex->getMessage());
            return back();
        }
        return back();
    }
    //----------------------------------------------//
    public function editPlan($subdomain,$id){
        $plan = ModelsPlan::find($id);
        return view('stripe.plans.edit',compact('plan'));
    }

    //---------------------------------------------//
    public function updatePlan(Request $request){

        $validatedData = $request->validate([
            'planId' => ['required'],
            'trialDays' => ['required'],
            'description' => ['required', 'string'],
        ]);

        try{
            //------ updating in DB -----//
            $plan = ModelsPlan::find($request->planId);
            $plan->description = $request->description;
            $plan->trialDays = $request->trialDays;

            if($request->status)
                $plan->status = 1;
            else    
                $plan->status = 0;

            $plan->save();

            $request->session()->flash('success','Plan has been updated!');
        }
        catch(Exception $ex){
            $request->session()->flash('error',$ex->getMessage());
        }
    
        return back();
    }

    //---------------------------------------------//
    public function showPlans(){
        if(!$this->checkUser()){
            return redirect('/subscription/list');
        }
        if(in_array(auth()->user()->role,['owner','Owner']))
            $plans = ModelsPlan::get();
        else
            $plans = ModelsPlan::where('status',1)->get(); 

        return view('stripe.plans.show',compact('plans'));
    }

    //---------------------------------------------//
    public function checkout($subdomain,$planID){
        if(!$this->checkUser()){
            return redirect('/subscription/list');
        }
        $plan = ModelsPlan::where('plan_id',$planID)->first();
        if($plan == null){
            return back()->withErrors([
                'message' => 'Unable to locate the plan!',
            ]);
        }
        return view('stripe.checkout',[ 'intent' => auth()->user()->createSetupIntent(), 'plan' => $plan ]);
    }

    //----------------------------------------//
    public function processSubscription(Request $request){
        if(!$this->checkUser()){
            return redirect('/subscription/list');
        }
        
        $user = auth()->user();
        $user->createOrGetStripeCustomer();

        $paymentMethod = null;
        $paymentMethod = $request->payment_method;
        if($paymentMethod != null){
            $paymentMethod = $user->addPaymentMethod($paymentMethod);
        }
        $plan = ModelsPlan::where('plan_id',$request->plan_id)->first();
        //$plan = $request->plan_id;

        try{
            if($plan->trialDays > 0 ){
                $user->newSubscription($plan->name,$plan->plan_id)
                ->trialDays($plan->trialDays)
                ->create($paymentMethod != null ? $paymentMethod->id : '');
            }else{
                $user->newSubscription($plan->name,$plan->plan_id)
                ->create($paymentMethod != null ? $paymentMethod->id : '');
            }
        }
        catch(Exception $ex){
            return back()->withErrors([
                'error' => 'Unable to create subscription due to :'.$ex->getMessage()
            ]);
        }

        $request->session()->flash('success',__('subscription.successfully_sub'));
        return redirect('/subscription/list');
    }

    //----------------------------------------//
    public function allSubscriptions(){
        if(in_array(auth()->user()->role, ['owner','Owner']))
            $subscriptions = Subscription::get();
        else{    
            $subscriptions = Subscription::where('user_id',auth()->user()->id)->get();
            if($subscriptions->isEmpty()){
                $user = User::where('business_id',auth()->user()->business_id)->where('stripe_id','!=',null)->first();
                $subscriptions = Subscription::where('user_id',$user->id)->get();
            }

        }
        return view('stripe.list',compact('subscriptions'));
    }

    //----------------------------------------//
    public function resumeSubscriptions(Request $request){
        if($request->subName){
            $user = auth()->user();
            $user->subscription($request->subName)->resume();
            $data['status'] = 'success';
            $data['data'] = __('subscription.auto_renew_on');
            return $data;
        }
    }

    //----------------------------------------//
    public function cancelSubscriptions(Request $request){
        if($request->subName){
            $user = auth()->user();
            $user->subscription($request->subName)->cancel();
            $data['status'] = 'success';
            $data['data'] = __('subscription.auto_renew_off');
            return $data;
        }
    }

    //---------------------------------------//
    public function viewInvoices($subdomain,$subscription){
        $subscription = Subscription::where(\DB::raw('md5(id)') , $subscription)->first();
        $invoices = $subscription->user->invoices();
        return view('stripe.invoices.list',compact('invoices'));
    }

    //---------------------------------------//
    public function downloadInvoice(Request $request,$subdomain,$invoiceId){
        return $request->user()->downloadInvoice($invoiceId, [
            'vendor' => 'Pawer Systems',
            'product' => 'Pawer Systems V2',
        ]);
    }

    //---------------------------------------//
    public function checkUser(){
        if(!in_array(auth()->user()->role,['owner','Owner'])){
            $user = User::where([['business_id',auth()->user()->business_id],['stripe_id','!=',null]])->first();
            if($user){
                $subscription = Subscription::where('user_id',$user->id)->count();
                if($subscription > 0)
                    return false;
            }
        }
        return true;
    }

}// class ends
