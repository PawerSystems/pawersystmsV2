<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Spatie\CalendarLinks\Link;
use Illuminate\Support\Str;
use App\Models\LocationLog;
use App\Models\visitor;
use App\Models\User;
use App\Models\Business;
use App\Models\Date;
use App\Models\Country;
use App\Models\Event;
use App\Models\EventSlot;
use App\Models\UsedClip;
use App\Models\Card;
use App\Jobs\SendSmsJob;
use App\Jobs\SendEmailJob;
use Illuminate\Support\Facades\URL;



class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    //--############ Send SMS with check #######################--//
    public function smsSendWithCheck($sms,$userID,$date = '',$time = ''){
        $business = User::find($userID);
        $smsSF = Business::find($business->business_id)->Settings->where('key','sms_stop_from')->first();
        $smsST = Business::find($business->business_id)->Settings->where('key','sms_stop_till')->first();
        $startTime = \Carbon\Carbon::createFromFormat('H:i', $smsSF->value)->subDays(1);
        $endTime = \Carbon\Carbon::createFromFormat('H:i', $smsST->value);
        $currentTime = \Carbon\Carbon::now();

        //---- If we aer not checking for event or treatment, so date and time will be empty ----
        if($date != ''){
            //---- SMS will send max if event/treatment remaning time more then 30 min ----
            $eventTime = \Carbon\Carbon::createFromFormat('Y-m-d H:i', \Carbon\Carbon::parse($date)->format('Y-m-d').' '.$time)->subMinute(30);
            $passCheck = false;
        }
        else{
            $passCheck = true;
        }

        //------ Check if event time not pass then proceed futher ----
        if($passCheck OR $eventTime > $currentTime){
            //--- sms resticted time ----
            if ($currentTime->between($startTime, $endTime, true)){
                //---- Sms should scheduld OR skip ---
                if($passCheck OR $eventTime > $endTime){
                    $diff_in_minutes = $endTime->diffInMinutes($currentTime);

                    \Log::channel('custom')->info("SMS Schedule after ristricted time pass. ",['timeNowis' => $currentTime,'After Minutes' => $diff_in_minutes]);

                    $job = (new SendSmsJob($sms,$userID))->delay(now()->addMinutes($diff_in_minutes));
                    $this->dispatch($job);
                }
                else
                    \Log::channel('custom')->warning("SMS Skip because time of booking less then 30 min and sms restriction not allow to send sms now.",['timeNowis' => $currentTime]);                    
            }
            else{
                //----- Send SMS Now ------
                \Log::channel('custom')->info("SMS will send now.",['business_id' => $business->business_id, 'user_id' => $userID, 'sms_text' => $sms]);
                $this->dispatch(new SendSmsJob($sms,$userID));
            }
        }
        else{
            \Log::channel('custom')->info("SMS skip because treatment/event time less then 30 minutes.",['currentTime' => $currentTime, 'eventTime' => $eventTime]);
        }
    }

    //--########### Add New User From Booking #################--//
    public function addUser($email,$bID,$name,$country,$number,$cprnr = '',$mednr = '',$brandName){

        $U = User::where([['email',strtolower($email)],['business_id',$bID]])->first();
    
        if($U == null){

            if(session()->has('locale')){
                $language = session('locale');
            }
            else{
                $language = 'en';
            }
            
            $password = Str::random(8);

            $user = new User();
            $user->name = $name;
            $user->number = $number;
            $user->email = strtolower($email);
            $user->country_id = $country;
            $user->language = $language;
            $user->business_id = $bID;
            $user->role = 'Customer';
            $user->access = '-1';
            $user->cprnr = ($cprnr ?: Null);
            $user->mednr = ($mednr ?: Null);
            $user->password = \Hash::make($password);
            $user->save();

            //-------- Send Email to user ----------

            // -------  for this user's will change language ------
            \App::setLocale($user->language);

            $url = "https://".session('business_name').'.'.config('app.domain').'/login';
            $subject =  __('emailtxt.user_register_email_subject',['name' => $brandName]);
            $content =  __('emailtxt.user_register_email_txt',['name' => $name, 'email' => $email, 'url' => $url,'password' => $password ]);

            if($user->email != ''){
                \Log::channel('custom')->info("Sending email to customer about account registered.",['business_id' => $bID,'user_id' => $user->id,'subject'=>$subject, 'email_text' => $content]);
                $this->dispatch(new SendEmailJob($user->email,$subject,$content,$brandName,$bID));
            }

            //------ Add visitor -----
            visitor::create([
                'business_id' => $bID,
                'user_id' => $user->id,
                'page' => 'BookFromWeb',
            ]); 

            return $user->id;
        }
        else{
            return $U->id;
        }
    }

    //--########## use in WebsiteControlle, reportsController and everyDay cron #############--//
    public function getTimeSlots($start,$interval,$end){

        if($end == '0:00')
            $end = '23:59';

        $startTime = strtotime($start); 
        $endTime   = strtotime($end);
        $returnTimeFormat = 'G:i';
    
        $current   = time(); 
        $addTime   = strtotime('+'.$interval.' mins', $current); 
        $diff      = $addTime - $current;
    
        $times = array(); 
        while ($startTime < $endTime) { 
            $times[] = date($returnTimeFormat, $startTime); 
            $startTime += $diff; 
        } 
        $times[] = date($returnTimeFormat, $startTime); 
        return $times;
    }

    //--######### send email  to list for free spot ################## --//
    public function sendFreeSpotEmail($bid,$bookedUsers,$content,$type){
        // ---- Users are not booked in this treatment, or not unsub ------//  
        $codeMessage = '';
        $business = Business::find($bid);
        $users = User::where([['is_active',1],['is_subscribe',1],['business_id',$bid]])->whereNotIn('id',$bookedUsers)->get();
        $brandName = Business::find($bid)->Settings->where('key','email_sender_name')->first(); 
        $code_for_booking = Business::find($bid)->Settings->where('key','code_for_booking')->first(); 


        if( !$business->brand_name )
            $businessName = $business->business_name;
        else
            $businessName = $business->brand_name;    
        
        if( $business->business_email )
            $email = $business->business_email;
        else{
            $user =  Business::find($business->id)->user;
            $email = $user->email;
        }

        if($code_for_booking->value == 'true' &&  $type == 'Treatment'){
            $code = Business::find($bid)->Settings->where('key','code')->first();
            $codeMessage = 1; 
        }

        foreach($users as $user){
                \App::setLocale($user->language);
                if($codeMessage)
                    $codeMessage = __('emailtxt.code_you_need_for_booking',['code' => $code->value]);
                    
                $link = "https://".$business->business_name.'.'.\Config::get('app.domain').'/other-account/'.md5($user->id);
                $unsubLink = "https://".$business->business_name.".".\Config::get('app.domain')."/unsubscribe/".md5($user->id);
            //if($user->email == "tbilal866@gmail.com"){

                //----- Replace Day's name and variable ---
                $eng = ["Monday", "Tuesday", "Wednesday","Thursday","Friday","Saturday","Sunday","Slots","Treatments","open_times"];
                $trans = [__('keywords.Monday'), __('keywords.Tuesday'), __('keywords.Wednesday'),__('keywords.Thursday'),__('keywords.Friday'),__('keywords.Saturday'),__('keywords.Sunday'),__('keywords.slots'),__('keywords.treatments'),__('keywords.open_times')];
                $newcontent = str_replace($eng, $trans, $content);

                if($type == 'Treatment'){
                    $subject = __('emailtxt.free_spots_email_subject');    
                    $message = __('emailtxt.free_spots_email_txt',['name' => $user->name, 'content' => $newcontent , 'pageLink' => $link , 'codeMessage' => $codeMessage, 'email' => $email ,'businessName' => $businessName, 'unsubLink' => $unsubLink ]);
                }else{
                    $subject = __('emailtxt.free_event_email_subject');    
                    $message = __('emailtxt.free_spots_email_txt_for_events',['name' => $user->name, 'content' => $newcontent , 'pageLink' => $link , 'email' => $email ,'businessName' => $businessName, 'unsubLink' => $unsubLink ]);
                }
                
                \dispatch(new SendEmailJob($user->email,$subject,$message,$brandName->value,$business->id));
                \Log::channel('custom')->info("Free spot email dispached!",['business_id' => $bid, 'user_id' => $user->id,'content' => $message]);

            //}
        }
    }

    //--########### All Country List ###################--//
    public function countries(){
        $countries = Country::where('status',1)->get();
        return $countries;
    }

    //--########## Active treatment bookings in system ############--//
    public function activeBookings($userID,$businessID){
        if(!empty($userID) && !empty($businessID) ){
        
            $business = Business::find($businessID);
            $dateFormat = Business::find($businessID)->Settings->where('key','date_format')->first(); 

            // $table = '<style> table.booking, table.booking th, table.booking td { border: 1px solid black; text-align:center; } </style><table class="booking"><tr><th>'.__('treatment.date').'</th><th>'.__('treatment.time').'</th><th>'.__('treatment.treatment').'</th><th>'.__('treatment.treatment_duration').'</th><th>'.__('profile.instructor').'</th><th>'.__('keywords.name').'</th><th>'.__('keywords.email').'</th><th>'.__('keywords.number').'</th><th>'.__('treatment.add_to_calender').'</th></tr>';
            $table = '<style> table.booking, table.booking th, table.booking td { border: 1px solid black; text-align:center; } </style><table class="booking"><tr><th>'.__('treatment.date').'</th><th>'.__('treatment.time').'</th><th>'.__('treatment.treatment').'</th><th>'.__('treatment.treatment_duration').'</th><th>'.__('profile.instructor').'</th><th>'.__('keywords.name').'</th><th>'.__('keywords.email').'</th><th>'.__('keywords.number').'</th><th>'.__('profile.location').'</th></tr>';

            //------ Get active dates that are not passed yet -----//
            $activeTreatments = Date::where('business_id',$businessID)->where('date','>=',date('Y-m-d'))->where('is_active','1')->pluck('id');
            $bookings = User::find($userID)->bookings->whereIN('date_id',$activeTreatments);
            $login = "https://".$business->business_name.".".config('app.domain').'/other-account/'.md5($userID);
            
            foreach( $bookings as $booking ){
                $dateTimeFrom = \Carbon\Carbon::parse($booking->date->date)->format("Y-m-d").' '.$booking->time;
                $dateTimeTill = \Carbon\Carbon::parse($booking->date->date)->format("Y-m-d").' '.$booking->time;
                $dateTimeTill = \Carbon\Carbon::parse($dateTimeTill)->addMinutes($booking->treatment->inter)->format("Y-m-d H:i");
                //----- ceating ics link -----//
                $from = \DateTime::createFromFormat('Y-m-d H:i', $dateTimeFrom );
                $to = \DateTime::createFromFormat('Y-m-d H:i', $dateTimeTill );

                $link = Link::create(__('treatment.treatment'), $from, $to);

                // $cancel = "https://".$business->business_name.".".config('app.domain').'/login';
                $instructor = "https://".$business->business_name.".".config('app.domain').'/showUser/'.md5($booking->date->user->id);

                $table .= '<tr>';
                $table .= '<td>'.\Carbon\Carbon::parse($booking->date->date)->format($dateFormat->value).'</td>';
                $table .= '<td>'.$booking->time.'</td>';
                $table .= '<td>'.$booking->treatment->treatment_name.'</td>';
                $table .= '<td>'.($booking->treatment->time_shown ?: $booking->treatment->inter).' min</td>';
                $table .= '<td><a href="'.$instructor.'" target="_blank">'.$booking->date->user->name.'</a></td>';
                $table .= '<td>'.$booking->user->name.'</td>';
                $table .= '<td>'.$booking->user->email.'</td>';
                $table .= '<td>'.$booking->user->number.'</td>';
                $table .= '<td>'.($booking->date->description ?: 'N/A').'</td>';
                // $table .= "<td><a download='treatment.ics' href='https://".$business->business_name.'.'.config('app.domain')."/ics/".$link->ics()."'>".__('treatment.add')."</a></td>";
                // $table .= '<td><a  href="'.$cancel.'">'.__('treatment.cancel').'</a></td>';
                $table .= '</tr>';
            }

            $table .= '</table><br><p>'.__('treatment.yntltmctyb').' <a href="'.$login.'">'.__('treatment.loginLink').'</a></p>';
            
        }
        else{
            $table = 'No Data Found';
        }
            // print_r($table);
            // exit();
        return $table;
    } 

    //--######## Send schedule report to selected Admins #########--//
    public function sendScheduleReport($bid,$users,$attachment){
        $users = explode(',',$users);
        $business = Business::find($bid);
        $admins = User::where([['is_active',1]])->whereIn('id',$users)->get();
        $brandName = Business::find($bid)->Settings->where('key','email_sender_name')->first();
        
        if( !$business->brand_name )
            $businessName = $business->business_name;
        else
            $businessName = $business->brand_name;    

        foreach($admins as $user){
            \Log::channel('custom')->info("Schedule eport sent successfully!",['business_id' => $bid, 'user_id' => $user->id]);
            \App::setLocale($user->language);

            $subject = __('emailtxt.schedule_report_email_subject',['name' => $businessName ]);    
            $message = __('emailtxt.schedule_report_email_txt',['name' => $user->name]);
            \dispatch(new SendEmailJob($user->email,$subject,$message,$brandName->value,$user->business_id,$attachment->getFile()->getRealPath()));
        }
    } 

    public function createIcsFile($type,$name,$duration,$date,$time,$therapist,$id){

        $Fname = $type.'-'.$id.'.ics';
        $subject = $name;
        $dd = \Carbon\Carbon::parse($date)->format("Y-m-d");
        $start = \Carbon\Carbon::parse($dd.' '.$time)->format("Y-m-d H:i");
        $description = $therapist.', '.$name.'('.$duration.')';

        // $file = "BEGIN:VCALENDAR\nVERSION:2.0\nMETHOD:PUBLISH\nBEGIN:VEVENT\nDTSTART;TZID=Romance Standard Time:".date("Ymd\THis\Z",strtotime($start))."\nTRANSP: OPAQUE\nSEQUENCE:0\nUID:\nDTSTAMP;TZID=Romance Standard Time:".date("Ymd\THis\Z")."\nSUMMARY:".$subject."\nDESCRIPTION:".$description."\nPRIORITY:1\nCLASS:PUBLIC\nBEGIN:VALARM\nTRIGGER:-PT10080M\nACTION:DISPLAY\nDESCRIPTION:Reminder\nEND:VALARM\nEND:VEVENT\nEND:VCALENDAR\n";

        $file = "BEGIN:VCALENDAR\nVERSION:2.0\nCALSCALE:GREGORIAN\nBEGIN:VTIMEZONE\nTZID:Romance Standard Time\nBEGIN:STANDARD\nDTSTART:16011028T030000\nRRULE:FREQ=YEARLY;BYDAY=-1SU;BYMONTH=10\nTZOFFSETFROM:+0200\nTZOFFSETTO:+0100\nEND:STANDARD\nBEGIN:DAYLIGHT\nDTSTART:16010325T020000\nRRULE:FREQ=YEARLY;BYDAY=-1SU;BYMONTH=3\nTZOFFSETFROM:+0100\nTZOFFSETTO:+0200\nEND:DAYLIGHT\nEND:VTIMEZONE\nMETHOD:PUBLISH\nBEGIN:VEVENT\nDTSTART;TZID=Romance Standard Time:".date("Ymd\THis",strtotime($start))."\nTRANSP: OPAQUE\nSEQUENCE:0\nUID:\nDTSTAMP;TZID=Romance Standard Time:".date("Ymd\THis")."\nSUMMARY:".$subject."\nDESCRIPTION:".$description."\nPRIORITY:1\nCLASS:PUBLIC\nBEGIN:VALARM\nTRIGGER:-PT15M\nACTION:DISPLAY\nDESCRIPTION:Reminder\nEND:VALARM\nEND:VEVENT\nEND:VCALENDAR\n";
        
        \Storage::put('public/ics/'.$Fname, $file);
    }

    //--######## Add custom Log Location wise in DB #############--//
    public function addLocationLog($type,$text,$bid=''){
        \Log::channel('custom')->info("In Add Location Log Function.",['type' => $type,'text' => $text,'business_id' => $bid]);

        if($bid == '')
            $business = Business::where('business_name',session('business_name'))->first();
        else
            $business = Business::find($bid); 
            
        $setting = Business::find($business->id)->Settings->where('key',$type)->first();
        if($setting != NULL){
            if($setting->value == 'true'){
                # add location log data
                LocationLog::create([
                    'business_id' => $business->id,
                    'model' => $type,
                    'comment' => $text,
                    'action_by' => \Auth::check() ? \Auth::user()->email : 'System',
                ]);
                \Log::channel('custom')->info("Adding log in Db in Add Location Log Function.",['type' => $type,'text' => $text,'business_name' => $business->business_name]);
            }
        }
    } 

    //--####### Send email to Therapist about booking ##########--//
    public function notifyTherapist($therapistId,$subject,$content,$brandName){
        $therapist = User::find($therapistId);
        if($therapist->will_notify == 1 && $therapist->email != ''){
            $content = preg_replace('/<a.*>(.*)<\/a>/isU','$1',$content);
            $this->dispatch(new SendEmailJob($therapist->email,$subject,$content,$brandName,$therapist->business_id));
            \Log::channel('custom')->info("Sending email to Therapist about new booking!",['business_id' => $therapist->business_id, 'user_id' => $therapist->id , 'subject' => $subject, 'email_text' => $content]);
        }
    }

    //--###### Deleting bookings of event ###########--//
    public function deleteEventBookings($eventId){

        $event = Event::find($eventId);
        $brandName = Business::find($event->business_id)->Settings->where('key','email_sender_name')->first(); 
        $dateFormat = Business::find($event->business_id)->Settings->where('key','date_format')->first(); 
        $smsSetting = Business::find($event->business_id)->Settings->where('key','sms_setting')->first();
        $eDeleteSms = Business::find($event->business_id)->Settings->where('key','e_d_booking_sms')->first();
        $clipRestoreSms = Business::find($event->business_id)->Settings->where('key','clip_restore')->first();

        $bookings = EventSlot::where('event_id',$eventId)->where('is_active',1)->get();
        foreach($bookings as $booking){
            \App::setLocale($booking->user->language);

            //----------- Delete clip of this booking ---------//
            $clip = UsedClip::where('event_slot_id',$booking->id)->where('is_active',1)->first();
            if($clip != Null){
                Card::find($clip->card_id)->increment('clips',$clip->amount);
                $clip->is_active = 0;
                $clip->save();

                //------ Add log in DB for location -----//
                $type = "log_for_card_and_clips";
                $txt = "Clips has been deleted. <br> Card Name: <b>".$clip->card->name."</b><br>Clips Added: <b>".$clip->amount."</b>";
                $this->addLocationLog($type,$txt,$event->business_id);
                
                $data['cardID'] = $clip->card_id;
                $data['balance'] = Card::find($clip->card_id)->clips;

                //-------- Send Email to user about Clips Restored ----------
                $card = Card::find($clip->card_id);

                $subject =  __('emailtxt.clips_restore_subject',['name' => $brandName->value]);
                $content =  __('emailtxt.clips_restore_txt',['name' => $card->user->name, 'amount' => $clip->amount, 'title' => $card->name, 'expiry' => \Carbon\Carbon::parse($card->expiry_date)->format($dateFormat->value), 'clips' => ($card->clips ?: 0), 'for' => ($card->type == 2 ? __('card.event') : __('card.treatment') ) ]);

                //------- Send sms notification to user ------     
                //--- check if business allow to send sms ----
                if($clipRestoreSms->value == 'true' && $smsSetting->value == 'true'){

                    $sms =  __('smstxt.clips_restore_txt',['name' => $card->user->name, 'amount' => $clip->amount, 'title' => $card->name, 'expiry' => \Carbon\Carbon::parse($card->expiry_date)->format($dateFormat->value), 'clips' => ($card->clips ?: 0), 'for' => ($card->type == 2 ? __('card.event') : __('card.treatment') ) ]);

                    //--- send sms notification ---
                    \Log::channel('custom')->info("Sending sms to customer about clips.",['business_id' => $event->business_id, 'user_id' => $card->user->id, 'sms_text' => $sms]);
                    $this->smsSendWithCheck($sms,$card->user->id);
                }

                if($card->user->email != ''){
                    \Log::channel('custom')->info("Sending email to customer about clips.",['business_id' => $event->business_id, 'user_id' => $card->user->id, 'subject' => $subject, 'email_text' => $content]);
                    $this->dispatch(new SendEmailJob($booking->user->email,$subject,$content,$brandName->value,$card->user->business_id));
                }
            }

            $booking->is_active = 0;
            $booking->save();

            //-------- Send Email to user about Booking Canceled ----------
            $isGuest = '';
            if($booking->parent_slot != NULL || $booking->parent_slot = ''){
                $isGuest = '('.  __('event.guest').')';
            }
            $bookingStatus = __('event.booking_status').$isGuest.': <b>'. __('event.canceled').'</b><br>';
            $bookingStatusSMS = __('event.booking_status').$isGuest.': '. __('event.canceled').'.';

            $homeLink = url('/');
                
            $subject =  __('emailtxt.event_booking_cancel_subject',['name' => $brandName->value]);
            $content = __('emailtxt.event_booking_cancel_txt', ['name' => $booking->user->name, 'ename' => $booking->event->name, 'date' => \Carbon\Carbon::parse($booking->event->date)->format($dateFormat->value), 'time' => $booking->event->time, 'status' => $bookingStatus, 'homeLink' => $homeLink ] );

            //------- Send sms notification to user ------     
            //--- check if business allow to send sms ----
            if($eDeleteSms->value == 'true' && $smsSetting->value == 'true'){
                $sms = __('smstxt.event_booking_cancel_txt', ['name' => $booking->user->name, 'ename' => $booking->event->name, 'date' => \Carbon\Carbon::parse($booking->event->date)->format($dateFormat->value), 'time' => $booking->event->time, 'status' => $bookingStatusSMS ] );
                //--- send sms notification ---
                \Log::channel('custom')->info("Sending sms to customer event booking canceled.",['business_id' => $event->business_id, 'user_id' => $booking->user->id, 'sms_text' => $sms]);
                $this->smsSendWithCheck($sms,$booking->user->id,$booking->event->date,$booking->event->time);
            }

            if($booking->user->email != ''){
                \Log::channel('custom')->info("Sending email to customer about event booking canceled.",['business_id' => $event->business_id, 'user_id' => $booking->user->id, 'subject' => $subject, 'email_text' => $content]);

                //------ Add log in DB for location -----//
                $type = "log_for_booking_related_details";
                $txt = "Event booking has been deleted. <br> User Name: <b>".$booking->user->email."</b><br>Event Date & Time: <b>".$booking->event->date.' '.$booking->event->time."</b><br>Deleted By:<b>System</b>";
                $this->addLocationLog($type,$txt,$event->business_id);

                $this->dispatch(new SendEmailJob($booking->user->email,$subject,$content,$brandName->value,$booking->user->business_id));

                #------ Send email to Therapist about this booking cancelation ----//
                $this->notifyTherapist($event->user->id,$subject,$content,$brandName->value);

            }
            else{
                \Log::channel('custom')->warning("Email not found of this user.",['business_id' => $event->business_id, 'user_id' => $booking->user->id]); 
            }


        }
        

    }

}
