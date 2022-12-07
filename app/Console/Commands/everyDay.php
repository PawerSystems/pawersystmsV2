<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\Controller;
use App\Http\Controllers\ReportsController;
// use App\Models\User;
use App\Jobs\SendEmailJob;
use App\Models\Setting;
use App\Models\Business;
use App\Models\Date;
use App\Models\TreatmentSlot;
use App\Models\ScheduleReport;

class everyDay extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'free:spot';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command will send email to users (who are subscribers) about free spots of next day treatments.';

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
        //---------------- Send free spot email ----------------//
        $day = \Carbon\Carbon::now()->format('l');
        $controller = new Controller();
        if($day == 'Friday')
            $dates = Date::whereIn('date',[\Carbon\Carbon::tomorrow(),\Carbon\Carbon::today()->addDays(2),\Carbon\Carbon::today()->addDays(3)])->where('is_active',1)->orderBy('business_id')->get();
        else
            $dates = Date::where('date',\Carbon\Carbon::tomorrow())->where('is_active',1)->orderBy('business_id')->get();
            

        $bID = $slotsForDay = '';
        $bookedUsersList = array();
        $len = count($dates);
        $i = 1;
        foreach($dates as $date){
            
            $business = Business::find($date->business_id);
            $freeSpotEmail = Business::find($date->business_id)->Settings->where('key','free_spot_email')->first();
            $dateFormat = Business::find($date->business_id)->Settings->where('key','date_format')->first(); 

            if($bID == '' OR $bID == $date->business_id){
                $bID = $date->business_id;
            }
            else{
                //--- send email --//
                if($freeSpotEmail->value == 'true' AND !empty($slotsForDay)){
                    $slotsForDay = $slotsForDay;
                    $controller->sendFreeSpotEmail($bID,$bookedUsersList,$slotsForDay);
                }
                
                $slotsForDay = '';
                $bID = $date->business_id;
                $bookedUsersList = array();
            }
                
            //--- check if free spot email enable ---
            if($freeSpotEmail->value == 'true'){
                $bookedslots = TreatmentSlot::where([['date_id',$date->id],['is_active',1]])->get();

                $bookedUsers = $bookedslots->pluck('user_id')->toArray();
                $bookedTimes = $bookedslots->pluck('time')->toArray(); 

                //------- array of all user that already booked -----//
                $bookedUsersList = array_merge($bookedUsersList,$bookedUsers);

                //----- Get all slots for this treatment ---//
                $slots = $controller->getTimeSlots($date->from,$business->time_interval,$date->till);                
                array_pop($slots); //--- remove last time ---

                //---- Remove booked slots from all slots ----//
                $availableSlots = array_values(array_diff($slots, $bookedTimes));

                if(!empty($availableSlots)){
                    // $treatments = $date->treatments;
                    // $names = array();
                    // foreach($treatments as $treatment){
                    //     array_push($names,$treatment->treatment_name.' ('.($treatment->time_shown ?: $treatment->inter).' min)');
                    // }
                    // $slotsForDay .= \Carbon\Carbon::parse($date->date)->format($dateFormat->value.' l').'<br> Slots : '.implode(' - ',$availableSlots).'<br> Treatments : '.implode(',',$names)."<br>";
                    $slotsForDay .= \Carbon\Carbon::parse($date->date)->format($dateFormat->value.' l').'<br> Slots : '.implode(' - ',$availableSlots)."<br>";
                }
                if ($i == $len) {
                    //--- if it's last turn, send email --//   
                    if(!empty($slotsForDay)){  
                        $slotsForDay =$slotsForDay;            
                        $controller->sendFreeSpotEmail($bID,$bookedUsersList,$slotsForDay);
                    }
                }
            }
            
            $i++;
        }

        
    }
}
