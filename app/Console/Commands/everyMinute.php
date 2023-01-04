<?php

namespace App\Console\Commands;

use App\Http\Controllers\Controller;
use App\Http\Controllers\ReportsController;
use Illuminate\Console\Command;
use App\Models\EmailRecord;
use App\Models\User;
use App\Jobs\SendEmailJob;
use App\Models\Setting;
use App\Models\TreatmentSlot;
use App\Models\Business;
use App\Models\Date;
use App\Models\Event;
use App\Models\EventSlot;
use App\Models\ScheduleReport;


class everyMinute extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:add';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command will check the time of email/sms to send. And then add recipents into jobs table.';

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
        $time = \Carbon\Carbon::now()->format('H:i');
        $day = \Carbon\Carbon::now()->format('l');
        $controller = new Controller();

        
        if($day == 'Friday'){
            $dates = Date::whereIn('date',[\Carbon\Carbon::tomorrow(),\Carbon\Carbon::today()->addDays(2),\Carbon\Carbon::today()->addDays(3)])->where('is_active',1)->orderBy('business_id')->get();

            $events = Event::whereIn('date',[\Carbon\Carbon::tomorrow(),\Carbon\Carbon::today()->addDays(2),\Carbon\Carbon::today()->addDays(3)])->where('is_active',1)->orderBy('business_id')->get();
        }
        else{
            $dates = Date::where('date',\Carbon\Carbon::tomorrow())->where('is_active',1)->orderBy('business_id')->get();

            $events = Event::where('date',\Carbon\Carbon::tomorrow())->where('is_active',1)->orderBy('business_id')->get();
        }    

        //-------- Cancel all events where min bookings does not exist --------//
        $settings = Setting::where('key','event_cancel')->get();
        #----- loop of location ----//
        foreach($settings as $setting){
            // continue;

            if($setting->value < 1)
                continue;
            //------ get all active events of this location -----//
            $thisEventdate = \Carbon\Carbon::now()->addHours($setting->value)->format('Y-m-d');
            $thisEventtime = \Carbon\Carbon::now()->addHours($setting->value)->format('H:i');

            $events = Event::where([['date',$thisEventdate],['time',$thisEventtime],['is_active',1],['business_id',$setting->business_id]])->get();

            #----- loop fo this location events ----
            foreach($events as $event){
                #--- get this event bookings ----
                $bookings = EventSlot::where([['event_id',$event->id],['is_active',1]])->count();
                if($event->min_bookings > $bookings && $bookings > 0){

                    $controller->deleteEventBookings($event->id);

                    #---- bookings should be delete ---
                    \Log::channel('custom')->info("Event bookings has been deleted because num of bookings does not reached as set!",['business_id' => $setting->business_id,'event_id'=>$event->id,'current_bookings_exist' => $bookings, 'min_bookings_limit_set' => $event->min_bookings, 'hours_set_for_canceltaion' => $setting->value]);

                    //------ Add log in DB for location -----//
                    $type = "log_for_event_related_details";
                    $txt = "Event bookings has been deleted because num of bookings does not reached as set! <br> Event Name: <b>".$event->name."</b><br>Event Date & Time: <b>".$event->date.' '.$event->time."</b><br>Number of bookings was: <b>".$bookings.'</b>';
                    $controller->addLocationLog($type,$txt,$event->business_id);

                }else{
                    \Log::channel('custom')->info("Event has sufficient bookings, so not going to delete bookings. ",['business_id' => $setting->business_id,'event_id'=>$event->id,'current_bookings_exist' => $bookings, 'min_bookings_limit_set' => $event->min_bookings, 'hours_set_for_canceltaion' => $setting->value]);
                }
    
            }
        }
        //--------------- Send event free spot email ------------//

        $bID = $slotsForDay = '';
        $bookedUsersList = array();
        $len = count($events);
        $i = 1;

        foreach($events as $event){
            // continue;

            $type = 'Event';

            $business = Business::find($event->business_id);
            $eventFreeSpotEmail = Business::find($event->business_id)->Settings->where('key','free_spot_email_for_event')->first();
            $eventFreeSpotEmailTime = Business::find($event->business_id)->Settings->where('key','event_free_spot_time')->first();
            $dateFormat = Business::find($event->business_id)->Settings->where('key','date_format')->first(); 

            # if time field is empty then default time will be 12:00
            if($eventFreeSpotEmailTime == NULL)
                $eventFreeSpotEmailTime = '12:00';
            else
                $eventFreeSpotEmailTime = $eventFreeSpotEmailTime->value;
            # check if time reached to send email
            if( $eventFreeSpotEmailTime == $time){
                if($bID == '' OR $bID == $event->business_id){
                    $bID = $event->business_id;
                }
                else{
                    //--- send email --//
                    if($eventFreeSpotEmail->value == 'true' AND !empty($slotsForDay)){
                        $slotsForDay = $slotsForDay;
                        $controller->sendFreeSpotEmail($bID,$bookedUsersList,$slotsForDay,$type);
                    }
                    
                    $slotsForDay = '';
                    $bID = $event->business_id;
                    $bookedUsersList = array();
                }

                //--- check if free spot email enable ---
                if($eventFreeSpotEmail->value == 'true'){
                    $bookings = EventSlot::where([['event_id',$event->id],['business_id',$bID],['is_active',1]])->get();

                    $bookedUsers = $bookings->pluck('user_id')->toArray();
                    $bookedSlots = $bookings->count(); 

                    //------- array of all user that already booked -----//
                    $bookedUsersList = array_merge($bookedUsersList,$bookedUsers);

                    //---- Remove booked slots from all slots ----//
                    $availableSlots = $event->slots - $bookedSlots;

                    if($availableSlots > 0){
                        $slotsForDay .= \Carbon\Carbon::parse($event->date)->format($dateFormat->value.' l').' - '.$event->name.' : '.$availableSlots.' Slots <br>';
                    }
                    if ($i == $len) {
                        //--- if it's last turn, send email --//   
                        if(!empty($slotsForDay)){  
                            $slotsForDay =$slotsForDay;            
                            $controller->sendFreeSpotEmail($bID,$bookedUsersList,$slotsForDay,$type);
                        }
                    }
                }
            }
            $i++;

        }

        //--------------- Send treatment free spot email ------------//        

        $bID = $slotsForDay = '';
        $bookedUsersList = array();
        $len = count($dates);
        $i = 1;
        foreach($dates as $date){
            $type = 'Treatment';

            $business = Business::find($date->business_id);
            $freeSpotEmail = Business::find($date->business_id)->Settings->where('key','free_spot_email')->first();
            $treatmentFreeSpotEmailTime = Business::find($date->business_id)->Settings->where('key','treatment_free_spot_time')->first();
            $dateFormat = Business::find($date->business_id)->Settings->where('key','date_format')->first(); 

            // $minSlots = 100;
            // foreach ($date->treatments as $treatment){
            //     if($minSlots > ceil($treatment->inter/$business->time_interval))
            //         $minSlots = ceil($treatment->inter/$business->time_interval);
            // }

            # if time field is empty then default time will be 12:00
            if($treatmentFreeSpotEmailTime == NULL){
                $treatmentFreeSpotEmailTime = '12:00';
            }
            else{
                $treatmentFreeSpotEmailTime = $treatmentFreeSpotEmailTime->value;
            }    
            # check if time reached to send email
            if($treatmentFreeSpotEmailTime == $time){

                if($bID == '' OR $bID == $date->business_id){
                    $bID = $date->business_id;
                }
                else{
                    //--- send email --//
                    if($freeSpotEmail->value == 'true' AND !empty($slotsForDay)){
                        $slotsForDay = $slotsForDay;
                        $controller->sendFreeSpotEmail($bID,$bookedUsersList,$slotsForDay,$type);
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

                    //---- Grouped slots as per shotest teatment ------//
                    // for($v=0; $v < ){

                    // }

                    if(!empty($availableSlots)){
                        // $slotsForDay .= \Carbon\Carbon::parse($date->date)->format($dateFormat->value.' l').'<br> Slots : '.implode(' - ',$availableSlots)."<br>";
                        $slotsForDay .= \Carbon\Carbon::parse($date->date)->format($dateFormat->value.' l').'<br>'.__('profile.location').' : '.$date->description ?: 'N/A'.'<br> '.$date->user->name.' : '.implode(' - ',$availableSlots)."<br>";
                    }
                    if ($i == $len) {
                        //--- if it's last turn, send email --//   
                        if(!empty($slotsForDay)){  
                            $slotsForDay =$slotsForDay;            
                            $controller->sendFreeSpotEmail($bID,$bookedUsersList,$slotsForDay,$type);
                        }
                    }
                }
            }
            $i++;
        }

        //----------------- Send schedule or instent emails --------------//
        $emails = EmailRecord::where([['schedule','<=',\Carbon\Carbon::now()],['status',0],['is_active',1]])->get();
        foreach($emails as $email){
            $bName = '';
            $brandName = Business::find($email->business_id)->Settings->where('key','email_sender_name')->first();
            if($brandName->value)
                $bName = $brandName->value;
            else
                $bName = config('app.name');   
            //----- get recipents -----//
            if($email->recipients == -1){
                $users = User::where([['role','Customer'],['business_id',$email->business_id]])->pluck('email');
            }
            else{
                $users = User::whereIn('id',explode(',',$email->recipients))->pluck('email');
            }

            //--- Add in queue ---//
            foreach($users as $user){
                \dispatch(new SendEmailJob($user,$email->subject,$email->content,$bName,$email->business_id));
            }
            $email->status = 1;
            $email->save();
        }

        //----------------- Send treatment booking reminder email / sms --------------//
        $businesses = Business::where('is_active',1)->get();
        
        foreach($businesses as $business){
            
            $emailTime = $smsTime = $smsSetting = $senderName = $dateFormat = '';

            $settings = Setting::where('business_id',$business->id)->get();
            
             foreach($settings as $setting){
                    switch ($setting->key) {
                        case 'email_sender_name':
                            $senderName = $setting->value;
                            break;
                        case 'email_reminder_time':
                            $emailTime = $setting->value;
                            break;
                        case 'sms_reminder_time':
                            $smsTime = $setting->value;
                            break;
                        case 'date_format':
                            $dateFormat = $setting->value;
                            break; 
                        case 'sms_setting':
                            $smsSetting = $setting->value;
                            break;            
                        default:
                            break;
                    }
                }
    
            //---- For Email ----//
            if( $emailTime > 0){
                //---- Get treatments bookings ------
                $onlyDate = \Carbon\Carbon::createFromFormat('H:i', $time)->addHour($emailTime)->format('Y-m-d');
                $onlyTime = ltrim(\Carbon\Carbon::createFromFormat('H:i', $time)->addHour($emailTime)->format('H:i'),"0");

                $dates = Date::where('date',$onlyDate)->where('is_active',1)->where('business_id',$business->id)->pluck('id')->toArray();

                $tbookings = TreatmentSlot::whereIn('date_id',$dates)->where('is_active','1')->where('parent_slot',NULL)->where('status','Booked')->where('time',ltrim($onlyTime, '0'))->where('business_id',$business->id)->get();

                foreach($tbookings as $tbooking){
                    
                    $user = User::find($tbooking->user_id);

                    $url = 'https://'.$business->business_name.'.'.config('app.domain').'/other-account/'.md5($tbooking->user_id);

                    \App::setLocale($user->language);
                     
                    $subject =  __('emailtxt.reminder_treatment_email_subject',['name' => $senderName]);
                    $content = __('emailtxt.reminder_treatment_email_txt',['name' => $user->name,'treatment' =>$tbooking->treatment->treatment_name, 'date' => \Carbon\Carbon::parse($tbooking->date->date)->format($dateFormat), 'time' => $tbooking->time, 'url' => $url, 'description' => $tbooking->date->description ?: 'N/A' ]);
                    
                    //----- Send email ----//
                   \dispatch(new SendEmailJob($user->email,$subject,$content,$senderName,$user->business_id));
                }
            } 

            //---- For SMS ----//
            if( $smsTime > 0 && $smsSetting == 'true'){
                
                //---- Get treatments bookings ------
                $onlyDate = \Carbon\Carbon::createFromFormat('H:i', $time)->addHour($smsTime)->format('Y-m-d');
                $onlyTime = ltrim(\Carbon\Carbon::createFromFormat('H:i', $time)->addHour($smsTime)->format('H:i'),"0");

                $dates = Date::where('date',$onlyDate)->where('is_active',1)->where('business_id',$business->id)->pluck('id')->toArray();

                $tbookings = TreatmentSlot::whereIn('date_id',$dates)->where('is_active','1')->where('parent_slot',NULL)->where('status','Booked')->where('time',ltrim($onlyTime, '0'))->where('business_id',$business->id)->get();

                foreach($tbookings as $tbooking){

                    $user = User::find($tbooking->user_id);
                    \App::setLocale($user->language);

                    $url = 'https://'.$business->business_name.'.'.config('app.domain').'/other-account/'.md5($tbooking->user_id);

                    //----- Send SMS ----//
                    $sms = __('smstxt.reminder_treatment_sms_txt',['name' => $user->name,'treatment' =>$tbooking->treatment->treatment_name, 'date' => \Carbon\Carbon::parse($tbooking->date->date)->format($dateFormat), 'time' => $tbooking->time,'link'=>$url]);
                    
                    $controller->smsSendWithCheck($sms,$user->id,$tbooking->date->date,$tbooking->time);
                }
            }
        }

        //----------------- Send event booking reminder email / sms --------------//        
        foreach($businesses as $business){

            $emailTimeE = $smsTimeE = $smsSetting = $senderNameE = $dateFormatE = '';

            $settings = Setting::where('business_id',$business->id)->get();

            foreach($settings as $setting){
                switch ($setting->key) {
                    case 'email_sender_name':
                        $senderNameE = $setting->value;
                        break;
                    case 'email_reminder_time':
                        $emailTimeE = $setting->value;
                        break;
                    case 'sms_reminder_time':
                        $smsTimeE = $setting->value;
                        break;
                    case 'date_format':
                        $dateFormatE = $setting->value;
                        break;                      
                    case 'sms_setting':
                        $smsSetting = $setting->value;
                        break;        
                    default:
                        break;
                }
            }

            //------ For Email ----//
            if( $emailTimeE > 0){
                //----- Event time for whom we have to send notification now -----
                $onlyDate = \Carbon\Carbon::createFromFormat('H:i', $time)->addHour($emailTimeE)->format('Y-m-d');
                $onlyTime = ltrim(\Carbon\Carbon::createFromFormat('H:i', $time)->addHour($emailTimeE)->format('H:i'),"0");
    
                $events = Event::where('date',$onlyDate)->where('time',$onlyTime)->where('is_active',1)->where('business_id',$business->id)->pluck('id')->toArray();

                $ebookings = EventSlot::whereIn('event_id',$events)->where('is_active','1')->where('business_id',$business->id)->where('parent_slot',NULL)->get();

                foreach($ebookings as $ebooking){

                    $user = User::find($ebooking->user_id);

                    $url = 'https://'.$business->business_name.'.'.config('app.domain').'/other-account/'.md5($ebooking->user_id);

                    \App::setLocale($user->language);
                    
                    if($ebooking->status)
                        $status = __('event.booked');
                    else
                        $status = __('event.waiting_list');

                    $subject =  __('emailtxt.reminder_event_email_subject',['name' => $senderNameE]);
                    $content = __('emailtxt.reminder_event_email_txt',['name' => $user->name,'event' =>$ebooking->event->name, 'date' => \Carbon\Carbon::parse($ebooking->event->date)->format($dateFormatE), 'time' => $ebooking->event->time ,'status' => $status, 'url' => $url]);

                    \dispatch(new SendEmailJob($user->email,$subject,$content,$senderNameE,$user->business_id));
                }
            }

            //---- For SMS ----//
            if( $smsTimeE > 0 && $smsSetting == 'true'){
                //----- Event time for whom we have to send notification now -----
                $onlyDate = \Carbon\Carbon::createFromFormat('H:i', $time)->addHour($smsTimeE)->format('Y-m-d');
                $onlyTime = ltrim(\Carbon\Carbon::createFromFormat('H:i', $time)->addHour($smsTimeE)->format('H:i'),"0");
    
                $events = Event::where('date',$onlyDate)->where('time',$onlyTime)->where('is_active',1)->where('business_id',$business->id)->pluck('id')->toArray();

                //---- Get treatments bookings ------
                $ebookings = EventSlot::whereIn('event_id',$events)->where('is_active','1')->where('business_id',$business->id)->where('parent_slot',NULL)->get();
            
                foreach($ebookings as $ebooking){

                    $user = User::find($ebooking->user_id);
                    \App::setLocale($user->language);

                    $url = 'https://'.$business->business_name.'.'.config('app.domain').'/other-account/'.md5($ebooking->user_id);


                    if($ebooking->status)
                        $status = __('event.booked');
                    else
                        $status = __('event.waiting_list');

                    //----- Send SMS ----//
                    $sms = __('smstxt.reminder_event_sms_txt',['name' => $user->name,'event' =>$ebooking->event->name, 'date' => \Carbon\Carbon::parse($ebooking->event->date)->format($dateFormatE), 'time' => $ebooking->event->time ,'status' => $status, 'url' => $url]);
                    
                    $controller->smsSendWithCheck($sms,$user->id,$ebooking->event->date,$ebooking->event->time);
                }
            }
        }   
        
        //---------------- Send schedule report email ----------------//
        $reports = new ReportsController();
        $todayDay = \Carbon\Carbon::now()->day;
        $schedules = ScheduleReport::where('is_active',1)->get();
        foreach($schedules as $schedule){
            $sendReport = 0;
            
            if($schedule->period == $todayDay){
                if($schedule->time == $time)
                    $sendReport = 1;
            }
            elseif($schedule->period == 'daily'){
                if($schedule->time == $time)
                    $sendReport = 1;
            }
            elseif($schedule->period == 'end'){
                $today = \Carbon\Carbon::now()->format('Y-m-d');
                $lastDayofMonth =  \Carbon\Carbon::parse($today)->endOfMonth()->toDateString();
                if($today == $lastDayofMonth){
                    if($schedule->time == $time)
                        $sendReport = 1;
                }
            }

            //------- If report has to be send -----
            if($sendReport == 1){

                $fromDate = \Carbon\Carbon::now()->subDays($schedule->duration)->format('Y-m-d');
                $toDate = \Carbon\Carbon::now()->subDays(1)->format('Y-m-d');
                $type = "ExcelReport";

                if($schedule->type == 'userReport'){
                    $sortBy = 'name';
                    $file = $reports->getUsers($type,$fromDate,$toDate,$sortBy,$schedule->business_id);
                }
                elseif( $schedule->type == 'bookingReport'){
                    $sortBy = 'treatment_date';
                    $file = $reports->getTreatentBookings($type,$fromDate,$toDate,$sortBy,$schedule->business_id);
                }
                elseif( $schedule->type == 'usersBookingReportForm'){
                    $sortBy = 'created_at';
                    $file = $reports->getUsersBookings($type,$fromDate,$toDate,$sortBy,$schedule->business_id);
                }
                elseif( $schedule->type == 'dateReport'){
                    $sortBy = 'dates.id';
                    $file = $reports->getDateReport($type,$fromDate,$toDate,$sortBy,$schedule->business_id);
                }
                elseif( $schedule->type == 'uniqueUserReport'){
                    $sortBy = 'uname';
                    $file = $reports->getUniqueUserReport($type,$fromDate,$toDate,$sortBy,$schedule->business_id);
                }
                elseif( $schedule->type == 'suveyReport'){
                    $sortBy = 'surveys.id';
                    $file = $reports->getSuveyReport($type,$fromDate,$toDate,$sortBy,$schedule->business_id);
                }

                $schedule->last_run = \Carbon\Carbon::now();
                $schedule->save();
                
                $controller->sendScheduleReport($schedule->business_id,$schedule->users,$file);
            }
        }
    }
}
