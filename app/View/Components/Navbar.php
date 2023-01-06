<?php

namespace App\View\Components;

use Illuminate\View\Component;
use App\Models\Business;
use App\Models\TreatmentSlot;
use App\Models\EventSlot;
use App\Models\Permission;
use App\Models\User;
use Laravel\Cashier\Subscription;
use Illuminate\Support\Facades\Auth;


class Navbar extends Component
{
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public $type;
    public $treatmentBooking;
    public $eventBooking;
    public $settings;
    public $business;
    public $businesses;
    public $message;
    public $status;
    

    public function __construct($type)
    {
        $this->type = $type;
        $this->business = Business::find(auth()->user()->business_id);
        $this->businesses = Business::where('is_active',1)->where('business_name','!=','www')->get();
        $this->settings = Business::find(auth()->user()->business_id)->Settings;
        $dateFormat = Business::find(auth()->user()->business_id)->Settings->where('key','date_format')->first();

        // $this->treatmentBooking = TreatmentSlot::where('user_id',Auth::user()->id)->count();
        // $this->eventBooking = EventSlot::where('user_id',Auth::user()->id)->count();
        $this->eventBooking = Permission::where('business_id',Auth::user()->business_id)->where('title','Event Create')->count();
        $this->treatmentBooking = Permission::where('business_id',Auth::user()->business_id)->where('title','Date Create')->count();

        if(!in_array(auth()->user()->role,['owner','Owner'])){
            $user = User::where([['business_id',auth()->user()->business_id],['stripe_id','!=',null]])->first();
            if($user){
                $subscription = Subscription::where('user_id',$user->id)->first();
                if($subscription != null){
                    //----- check if location has active subscription ----//
                    if($user->subscribed($subscription->name)){
                        //----- get date after 10 days ---//
                        $noticePeriod = \Carbon\Carbon::now()->addDays(10)->timestamp;
                        $currentPeriodEnd = $subscription->asStripeSubscription()->current_period_end;

                        if($currentPeriodEnd < $noticePeriod && $currentPeriodEnd >= \Carbon\Carbon::now()->timestamp){
                            $this->message = __('subscription.period_ends',['date'=>\Carbon\Carbon::parse($currentPeriodEnd)->format($dateFormat->value)]);
                            $this->status = 'alert-warning';
                        }elseif( $currentPeriodEnd < \Carbon\Carbon::now()->timestamp){
                            $this->message = __('subscription.invoice_due');
                            $this->status = 'alert-danger';
                        }else{
                            $this->message = $this->status = '';
                        }
                    }else{
                        echo 'Your subscription has been canceled, Please contact with admin.';
                        exit();
                    }
                }else{
                    echo 'Your subscription has been canceled, Please contact with admin.';
                    exit();
                }
            }else{
                return redirect('/plans/checkout/plan_Munxr7zkyxxiuD');
            }
        }else{
            $this->message = $this->status = '';
        }

    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|string
     */
    public function render()
    {
        return view('components.navbar');
    }
}
