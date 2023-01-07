<?php

namespace App\Console\Commands;

use App\Http\Controllers\Controller;
use Illuminate\Console\Command;
use App\Models\User;
use App\Jobs\SendEmailJob;
use App\Models\Setting;
use App\Models\Business;
use App\Models\Website;
use Laravel\Cashier\Subscription;
use Illuminate\Support\Facades\Mail;
use App\Mail\SendMail;

class SubscriptionStatusCheck extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'subscription:check';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command is use to check subscription status and take action.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $subscriptions = Subscription::all();
        foreach($subscriptions as $subscription){
            
            $user = User::find($subscription->user_id);
            if($user){
                $business = Business::find($user->business_id); 
                $pages = $business->websites->all(); 
            }else{
                Mail::to('tbilal866@gmail.com')->send(new SendMail("Their is subscription with no user existing in location. Subscription ID: ".$subscription->id." ","pawerbookings.com"));
                return false;
            }

            //------- If subscription has canceled then disable location pages -----//
            if(in_array($subscription->stripe_status,['canceled','unpaid','past_due','incomplete','incomplete_expired','subscription.canceled'])){
                foreach($pages as $page){
                    if(in_array($page->page,['BOOKING','EVENT'])){
                        if($page->is_active == 1){
                            \Log::channel('custom')->warning('Page has disable by system because of payment issue.');
                            Website::where('id',$page->id)->update(['is_active' => 0]);
                        }
                    }
                }

                //----- disable location is subscription has been canceled ----//
                // if($subscription->stripe_status == 'canceled'){
                //     $business->is_active = 0;
                //     $business->save();
                // }else{
                //     if($subscription->stripe_status != 'canceled'){
                //         $business->is_active = 1;
                //         $business->save();
                //     }
                // }
            }elseif(in_array($subscription->stripe_status,['active','trialing'])){
                //------- If subscription active or reactive then enable location -----//
                foreach($pages as $page){
                    if(in_array($page->page,['BOOKING'])){
                        if($page->is_active == 0){
                            \Log::channel('custom')->warning('Page has enable by system.');
                            Website::where('id',$page->id)->update(['is_active' => 1]);
                        }
                    }
                }
            }
        }
    }
}
