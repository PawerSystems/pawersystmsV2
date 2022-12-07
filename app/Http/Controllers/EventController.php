<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Models\Business;
use App\Models\Card;
use App\Models\UsedClip;
use App\Models\User;
use App\Models\Event;
use App\Models\EventSlot;
use App\Jobs\SendEmailJob;
use App\Jobs\SendSmsJob;
use App\Notifications\BookingNotification;


class EventController extends Controller
{
    //#############################################//
    public function list($subdomain,$var = ''){
        abort_unless(\Gate::allows('Event List View'),403);

        $where = ">=";
        if(!empty($var) && $var == 'past' )
            $where = "<";

        $events = Business::find(Auth::user()->business_id)->Events->where('is_active',1)->where('status','Active')->where('date',$where,date('Y-m-d'));

        $dateFormat = Business::find(Auth::user()->business_id)->Settings->where('key','date_format')->first(); 
        $settings = Business::find(Auth::user()->business_id)->Settings->where('key','clipboard_event')->first();

        return view('event.event_list',compact('events','dateFormat','settings','var'));
    }

    //#############################################//
    public function deletedEvents(){
        abort_unless(\Gate::allows('Deleted Events View'),403);
        $events = Business::find(Auth::user()->business_id)->Events->where('is_active',0);
        $dateFormat = Business::find(Auth::user()->business_id)->Settings->where('key','date_format')->first(); 
        $timeFormat = Business::find(Auth::user()->business_id)->Settings->where('key','time_format')->first(); 

        return view('event.deleted_events',compact('events','dateFormat','timeFormat'));
    }

    //#############################################//
    public function slots(){
        abort_unless(\Gate::allows('Event Booking View'),403);

        $events = Business::find(Auth::user()->business_id)->Events->where('is_active',1)->where('status','Active')->where('date','>=',date('Y-m-d'));
        $settings = Business::find(\Auth::user()->business_id)->Settings;
        $countries = $this->countries();

        return view('event.event_slots',compact('events','settings','countries'));
    }

    //#############################################//
    public function pastEvents(){
        abort_unless(\Gate::allows('Event Past Bookings View'),403);

        $events = Event::where('business_id',Auth::user()->business_id)->where('is_active',1)->where('status','Active')->where('date','<',date('Y-m-d'))->orderBy('date','DESC')->simplePaginate(10);
        $settings = Business::find(\Auth::user()->business_id)->Settings;
        $countries = $this->countries();

        return view('event.past_event_slots',compact('events','settings','countries'));
    }

    //#############################################//
    public function deletedSlots(){
        abort_unless(\Gate::allows('Deleted Events Bookings View'),403);
        $slots = EventSlot::where('business_id',Auth::user()->business_id)->where('is_active',0)->orderBy('updated_at','DESC')->simplePaginate(50);
        $dateFormat = Business::find(Auth::user()->business_id)->Settings->where('key','date_format')->first(); 
        $timeFormat = Business::find(Auth::user()->business_id)->Settings->where('key','time_format')->first(); 
        //$slots = Business::find(Auth::user()->business_id)->EventDeletedSlots;
        return view('event.deleted_event_slots',compact('slots','dateFormat','timeFormat'));
    }

    //#############################################//
    public function create(){
        abort_unless(\Gate::allows('Event Create'),403);
        $therapists = Business::find(Auth::user()->business_id)->users->where('role','!=','Customer')->where('is_active',1)->where('is_therapist',1);
        $dateFormat = Business::find(Auth::user()->business_id)->Settings->where('key','date_format')->first(); 
        $settings = Business::find(Auth::user()->business_id)->Settings->where('key','clipboard_event')->first();

        return view('event.create_event',compact('therapists','dateFormat','settings'));
    }

    //#############################################//
    public function edit($subdomain,$id){
        abort_unless(\Gate::allows('Event Edit'),403);
        $event = Event::where(\DB::raw('md5(id)'),$id)->first();
        $therapists = Business::find(Auth::user()->business_id)->users->where('role','!=','Customer')->where('is_active',1)->where('is_therapist',1);
        $settings = Business::find(Auth::user()->business_id)->Settings->where('key','clipboard_event')->first();
        $dateFormat = Business::find(auth()->user()->business_id)->Settings->where('key','date_format')->first();
        $date = \Carbon\Carbon::parse($event->date)->format($dateFormat->value);
        return view('event.edit_event',compact('event','therapists','settings','date'));
    }

    //#############################################//
    public function save(Request $request){
        abort_unless(\Gate::allows('Event Create'),403);
        Validator::make($request->all(), [
            'name'      => ['required'],
            '_date'      => ['required'],
            'time'      => ['required'],
            'duration'  => ['required','integer'],
            'slots'     => ['required','integer'],
            'tharapist' => ['required'],
            'clips'     => ['required'],
            'recurrence'    => ['required'],
            'recurring_num' => ['required','integer'],
            'min_bookings' => ['required'],
        ])->validate();

        $dates = array();
        if($request->recurrence == 'd') {
            for( $i = 0; $i < $request->recurring_num; $i++ ){
                $dates[$i] = date("Y-m-d", strtotime('+ '.$i.' days' , strtotime($request->_date)));
            }
        }
        elseif($request->recurrence == 'w'){
            $j = 0;
            for( $i = 0; $i < $request->recurring_num; $i++ ){
                $dates[$i] = date("Y-m-d", strtotime('+ '.$j.' days' , strtotime($request->_date)));
                $j = $j+7;
            }
        }
        elseif($request->recurrence == 'biw'){
            $j = 0;
            for( $i = 0; $i < $request->recurring_num; $i++ ){
                $dates[$i] = date("Y-m-d", strtotime('+ '.$j.' days' , strtotime($request->_date)));
                $j = $j+14;
            }
        }
        elseif($request->recurrence == 'dd') {
            for( $i = 0; $i < 7; $i++ ){
                $dates[$i] = date("Y-m-d", strtotime('+ '.$i.' days' , strtotime($request->_date)));
            }
        }
        elseif($request->recurrence == 'm'){
            $k = 0;
            $count = 0;
            for( $i = 0; $i < $request->recurring_num; $i++ ){
                for($j=0;$j < 4; $j++){
                    $dates[$count] = date("Y-m-d", strtotime('+ '.$k.' days' , strtotime($request->_date)));
                    $k = $k+7;
                    $count++;
                }    
            }    
        }
        $check = 0;

        foreach( $dates as $date ){

            $event = new Event();
            $event->business_id = Auth::user()->business_id;
            $event->name = $request->name;
            $event->date = $date;
            $event->time = $request->time;
            $event->duration = $request->duration;
            $event->slots = $request->slots;
            $event->clips = $request->clips;
            $event->price = $request->price;
            $event->user_id = $request->tharapist;
            $event->min_bookings = $request->min_bookings;
            $event->description = $request->desc;
            $event->status = 'Active';
            if($request->guest)
                $event->is_guest = 1;
            if($event->save())    
                $check = 1;
            else
                $check = 0;    
        }      

        if($check)
            {
                $request->session()->flash('success',__('event.ehbcs'));
                \Log::channel('custom')->info("Create new events.",['business_id' => Auth::user()->business_id]);

                //------ Add log in DB for location -----//
                $type = "log_for_event_related_details";
                $txt = "Create new event. <br> Event Name: <b>".$request->name."</b><br>Event Date & Time: <b>".$date.' '.$request->time."</b>";
                $this->addLocationLog($type,$txt);
            }
        else
            {
                $request->session()->flash('error',__('event.eotce'));
                \Log::channel('custom')->error("Error occure to create event!",['business_id' => Auth::user()->business_id]);
            }

        return \Redirect::back();
    }

    //#############################################//
    public function update(Request $request){
        abort_unless(\Gate::allows('Event Edit'),403);
        Validator::make($request->all(), [
            'id'        => ['required','integer'],
            'name'      => ['required'],
            '_date'      => ['required'],
            'time'      => ['required'],
            'duration'  => ['required','integer'],
            'slots'     => ['required','integer'],
            'tharapist' => ['required'],
            'clips'     => ['required'],
            'min_bookings'  => ['required'],
        ])->validate();

        //------ Update event details -----
        $event = Event::find($request->id);

        if($request->guest){
            if($event->is_guest != 1){
                $bookings = EventSlot::where('event_id',$event->id)->where('is_active',1)->count();
                if($bookings > 0){
                    $request->session()->flash('error',__('event.beie'));
                    \Log::channel('custom')->error("Bokings already exist in this event so can't change guest status",['business_id' => Auth::user()->business_id,'event_id' => $event->id]);  
                    return \Redirect::back();
                }
                else{
                    $event->is_guest = 1;
                }
            }
        }
        else{
            if($event->is_guest != 0){
                $bookings = EventSlot::where('event_id',$event->id)->where('is_active',1)->count();
                if($bookings > 0){
                    $request->session()->flash('error',__('event.beie'));
                    \Log::channel('custom')->error("Bokings already exist in this event so can't change guest status",['business_id' => Auth::user()->business_id,'event_id' => $event->id]);  
                    return \Redirect::back();
                }
                else{
                    $event->is_guest = 0;
                }
            }
        }

        $changes = array();
        $changesDone = array();
        
        if($event->name != $request->name ){
            array_push($changes,'name');
            $changesDone['name']['before'] = $event->name;
            $changesDone['name']['after'] = $request->name;
        }
        if($event->date != $request->_date){
            array_push($changes,'date');
            $changesDone['date']['before'] = $event->date;
            $changesDone['date']['after'] = $request->_date;
        }
        if($event->time != $request->time){
            array_push($changes,'time');
            $changesDone['time']['before'] = $event->time;
            $changesDone['time']['after'] = $request->time;
        }
        if($event->duration != $request->duration){
            array_push($changes,'duration');
            $changesDone['duration']['before'] = $event->duration;
            $changesDone['duration']['after'] = $request->duration;
        }
        if($event->price != $request->price){
            array_push($changes,'price');
            $changesDone['price']['before'] = $event->price;
            $changesDone['price']['after'] = $request->price;
        }
        if($event->clips != $request->clips){
            array_push($changes,'clips');
            $changesDone['clips']['before'] = $event->clips;
            $changesDone['clips']['after'] = $request->clips;
        }
        if($event->user_id != $request->tharapist){
            array_push($changes,'instructor');
            $user = User::find($request->tharapist);
            $changesDone['instructor']['before'] = $event->user->name;
            $changesDone['instructor']['after'] = $user->name;
        }
        if($event->description != $request->desc){
            array_push($changes,'description');
            $changesDone['description']['before'] = $event->description;
            $changesDone['description']['after'] = $user->desc;
        }

        $event->name = $request->name;
        $event->date = $request->_date;
        $event->time = $request->time;
        $event->duration = $request->duration;
        $event->slots = $request->slots;
        $event->price = $request->price;
        $event->clips = $request->clips;
        $event->user_id = $request->tharapist;
        $event->description = $request->desc;
        $event->min_bookings = $request->min_bookings;
        $event->status = 'Active';

        //---- Update bookings status accouding to num of slots of event ----//
        
        if($event->save()){
            $this->eventUpdateNotification($event->id,$changes,$changesDone);
            $slots = Event::find($request->id)->eventActiveSlots;
            foreach($slots as $key => $val){
                if($key < $request->slots )
                    $val->status = 1;
                else
                    $val->status = 0;

                $val->save();    
            }

            $request->session()->flash('success',__('event.ehbus'));
            \Log::channel('custom')->info("Event has been updates successfully!",['business_id' => Auth::user()->business_id]);

            //------ Add log in DB for location -----//
            $type = "log_for_event_related_details";
            $txt = "Event has been updated. <br> Event Name: <b>".$event->name."</b><br>Event Date & Time: <b>".$event->date.' '.$event->time."</b>";
            $this->addLocationLog($type,$txt);
        }
        else
            {
                $request->session()->flash('error',__('event.eotue'));
                \Log::channel('custom')->error("Error occure to update event!",['business_id' => Auth::user()->business_id]);
            }

        return \Redirect::back();
    }

    //--###########################################--//
    public function restoreEvent(Request $request){
        abort_unless(\Gate::allows('Event Restore'),403);
        Validator::make($request->all(), [
            'id'      => ['required'],
        ])->validate();
        $event = Event::where(\DB::raw('md5(id)'),$request->id)->where('is_active',0)->first();

        $event->is_active = 1;
            if($event->save())
                {
                    $response = 'success';
                    \Log::channel('custom')->info("Event has been Restored!",['business_id' => Auth::user()->business_id]);

                    //------ Add log in DB for location -----//
                    $type = "log_for_event_related_details";
                    $txt = "Event has been Restored. <br> Event Name: <b>".$event->name."</b><br>Event Date & Time: <b>".$event->date.' '.$event->time."</b>";
                    $this->addLocationLog($type,$txt);
                }
            else
                {
                    $response = 'error';
                    \Log::channel('custom')->error("Error to restore event.",['business_id' => Auth::user()->business_id]);

                }
    
        return \Response::json($response);
    }

    //--##################### Delete Event ######################--//
    public function deleteEvent(Request $request){
        abort_unless(\Gate::allows('Event Delete'),403);
        Validator::make($request->all(), [
            'id'      => ['required'],
        ])->validate();
        $event = Event::where(\DB::raw('md5(id)'),$request->id)->where('is_active',1)->first();
        $bookings = EventSlot::where('event_id',$event->id)->where('is_active',1)->count();

        if($bookings == 0){
            $event->is_active = 0;
            if($event->save())
                {
                    $response = 'success';
                    \Log::channel('custom')->info("Event has been deleted!",['business_id' => Auth::user()->business_id,'user_id' => Auth::user()->id,'user_name' => Auth::user()->name]);

                    //------ Add log in DB for location -----//
                    $type = "log_for_event_related_details";
                    $txt = "Event has been deleted. <br> Event Name: <b>".$event->name."</b><br>Event Date & Time: <b>".$event->date.' '.$event->time."</b>";
                    $this->addLocationLog($type,$txt);
                }
            else
                {
                    $response = 'error';
                    \Log::channel('custom')->error("Error to delete event.",['business_id' => Auth::user()->business_id,'user_id' => Auth::user()->id,'user_name' => Auth::user()->name]);
                }
        }
        else
            {
                $response = 'exist';
                \Log::channel('custom')->warning("Booking exist in event so can't delete event!",['business_id' => Auth::user()->business_id,'user_id' => Auth::user()->id,'user_name' => Auth::user()->name]);
            }

        return \Response::json($response);
        
    }

    //--##################### Delete bookings ######################--//
    public function delete(Request $request){
        if( Auth::user()->role != 'Customer' ){
            abort_unless(\Gate::allows('Event Booking Delete'),403);
        }
        Validator::make($request->all(), [
            'id'      => ['required'],
        ])->validate();
        $slots = EventSlot::where(\DB::raw('md5(id)'),$request->id)->orWhere(\DB::raw('md5(parent_slot)'),$request->id)->where('is_active',1)->get();

        $theEvent = EventSlot::where(\DB::raw('md5(id)'),$request->id)->first();
        $event = Event::find($theEvent->event_id);
        $setting = Business::find(Auth::user()->business_id)->Settings->where('key','stop_cancellation')->first();
        $brandName = Business::find(Auth::user()->business_id)->Settings->where('key','email_sender_name')->first(); 
        $dateFormat = Business::find(Auth::user()->business_id)->Settings->where('key','date_format')->first(); 
        $smsSetting = Business::find(Auth::user()->business_id)->Settings->where('key','sms_setting')->first();
        $eDeleteSms = Business::find(Auth::user()->business_id)->Settings->where('key','e_d_booking_sms')->first();
        $clipRestoreSms = Business::find(Auth::user()->business_id)->Settings->where('key','clip_restore')->first();

        // -------  for this user's will change language ------
        \App::setLocale($theEvent->user->language);

        //-------- Minus hours from event time, so that can check it can be booked or not now ---
        // $bookTime = explode(':',$event->time);
        // $bookTime[0] = $bookTime[0]-$setting->value;
        // $bookTimeFinal = $bookTime[0].':'.$bookTime[1];

        $eventOriginalTime = \Carbon\Carbon::parse($event->date)->format('Y-m-d').' '.$event->time;
        $eventTime = strtotime(\Carbon\Carbon::parse($eventOriginalTime)->subHours($setting->value)->format('Y-m-d H:i'));
        $currentTime = strtotime(\Carbon\Carbon::now()->format('Y-m-d H:i'));

        if($eventTime >= $currentTime || Auth::user()->role != 'Customer'){
            if(count($slots) > 0){
            
                foreach($slots as $slot){
                    //----- If event deleted------
                    if( $event->is_active == 0 )
                        exit();

                    //----------- Delete clip of this booking ---------//
                    $clip = UsedClip::where('event_slot_id',$slot->id)->where('is_active',1)->first();
                    if($clip != Null){
                        Card::find($clip->card_id)->increment('clips',$clip->amount);
                        $clip->is_active = 0;
                        $clip->save();

                        //------ Add log in DB for location -----//
                        $type = "log_for_card_and_clips";
                        $txt = "Clips has been deleted. <br> Card Name: <b>".$clip->card->name."</b><br>Clips Added: <b>".$clip->amount."</b>";
                        $this->addLocationLog($type,$txt);
                        
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
                            \Log::channel('custom')->info("Sending sms to customer about clips.",['business_id' => Auth::user()->business_id, 'user_id' => $card->user->id, 'sms_text' => $sms]);
                            $this->smsSendWithCheck($sms,$card->user->id);
                        }

                        if($card->user->email != ''){
                            \Log::channel('custom')->info("Sending email to customer about clips.",['business_id' => Auth::user()->business_id, 'user_id' => $card->user->id, 'subject' => $subject, 'email_text' => $content]);
                            $this->dispatch(new SendEmailJob($card->user->email,$subject,$content,$brandName->value,$card->user->business_id));
                        }
                    }

                    $slot->is_active = 0;
                    $slot->save();
                }
                //----- Update next slots status ----
                $booked = $event->eventBookedSlots->count();
                $limit = $event->slots - $booked;
                if( $limit > 0 ){
                    $waiting = $event->eventWaitingSlots->skip(0)->take($limit);                   
                    foreach($waiting as $key => $val){

                        if($key <= count($slots)){
                            $val->status = 1;
                            $val->save(); 
                            // -------  for this user's will change language ------
                            \App::setLocale($val->user->language);
                            //-------------- Send booking status change email --------
                            $bookingStatus = __('event.booking_status').': <b>'.($val->status ? __('event.booked') : __('event.waiting_list')).'</b><br>';
                            if($val->parent_slot != NULL || $val->parent_slot != '')
                                $bookingStatus = __('event.guest_booking_status').': <b>'.($val->status ? __('event.booked') : __('event.waiting_list')).'</b><br>';


                            $subject = __('emailtxt.event_booking_status_subject',['name' => $brandName->value]);

                            $content = __('emailtxt.event_booking_status_txt',['name' => $val->user->name, 'ename' => $val->event->name, 'date' => \Carbon\Carbon::parse($val->event->date)->format($dateFormat->value), 'time' => $val->event->time, 'status' => $bookingStatus ]);
        
                            if($val->user->email != ''){
                                \Log::channel('custom')->info("Sending email to customer about booking status changed.",['business_id' => Auth::user()->business_id, 'user_id' => $val->user->id, 'subject' => $subject, 'email_text' => $content]);
                                $this->dispatch(new SendEmailJob($val->user->email,$subject,$content,$brandName->value,$val->user->business_id));
                            }  
                        }
                    }
                }
    
                // -------  for this user's will change language ------
                \App::setLocale($theEvent->user->language);
                //-------- Send Email to user about Booking Canceled ----------
                $isGuest = '';
                if($theEvent->parent_slot != NULL || $theEvent->parent_slot = ''){
                    $isGuest = '('.  __('event.guest').')';
                }
                $bookingStatus = __('event.booking_status').$isGuest.': <b>'. __('event.canceled').'</b><br>';
                $bookingStatusSMS = __('event.booking_status').$isGuest.': '. __('event.canceled').'.';

               $homeLink = url('/booking');
                
               $subject =  __('emailtxt.event_booking_cancel_subject',['name' => $brandName->value]);
               $content = __('emailtxt.event_booking_cancel_txt', ['name' => $theEvent->user->name, 'ename' => $theEvent->event->name, 'date' => \Carbon\Carbon::parse($theEvent->event->date)->format($dateFormat->value), 'time' => $theEvent->event->time, 'status' => $bookingStatus, 'homeLink' => $homeLink ] );

                //------- Send sms notification to user ------     
                //--- check if business allow to send sms ----
                if($eDeleteSms->value == 'true' && $smsSetting->value == 'true'){
                    $sms = __('smstxt.event_booking_cancel_txt', ['name' => $theEvent->user->name, 'ename' => $theEvent->event->name, 'date' => \Carbon\Carbon::parse($theEvent->event->date)->format($dateFormat->value), 'time' => $theEvent->event->time, 'status' => $bookingStatusSMS ] );
                    //--- send sms notification ---
                    \Log::channel('custom')->info("Sending sms to customer event booking canceled.",['business_id' => Auth::user()->business_id, 'user_id' => $theEvent->user->id, 'sms_text' => $sms]);
                    $this->smsSendWithCheck($sms,$theEvent->user->id,$theEvent->event->date,$theEvent->event->time);
                }

                if($theEvent->user->email != ''){
                    \Log::channel('custom')->info("Sending email to customer about event booking canceled.",['business_id' => Auth::user()->business_id, 'user_id' => $theEvent->user->id, 'subject' => $subject, 'email_text' => $content]);

                    //------ Add log in DB for location -----//
                    $type = "log_for_booking_related_details";
                    $txt = "Event booking has been deleted. <br> User Name: <b>".$theEvent->user->email."</b><br>Event Date & Time: <b>".$theEvent->event->date.' '.$theEvent->event->time."</b><br>Deleted By:<b>".Auth::user()->email."</b>";
                    $this->addLocationLog($type,$txt);

                    $this->dispatch(new SendEmailJob($theEvent->user->email,$subject,$content,$brandName->value,$theEvent->user->business_id));

                    #------ Send email to Therapist about this booking cancelation ----//
                    $this->notifyTherapist($event->user->id,$subject,$content,$brandName->value);

                }
                else{
                    \Log::channel('custom')->warning("Email not found of this user.",['business_id' => Auth::user()->business_id, 'user_id' => $theEvent->user->id]); 
                }

                $data['status'] = 'success';
                $data['slots'] = count($slots);
                $data['limit'] = $limit;
            }    
            else
                $data['status'] = 'error';
        }
        else{
            $data['status'] = 'exceeded'; 
            \Log::channel('custom')->warning("Can't delete booking now because time to delete has been passed.",['business_id' => Auth::user()->business_id, 'Stop_booking_befoe_hours' => $setting->value, 'current_time' => \Carbon\Carbon::now()->format('Y-m-d H:i') , 'try_to_book_time' => \Carbon\Carbon::parse($event->date)->format('Y-m-d').' '.$event->time]);
        }               

        return json_encode($data);
    }
    
    //#############################################//
    public function book(Request $request){
        abort_unless(\Gate::allows('Event Book'),403);
        Validator::make($request->all(), [
            'event_id'      => ['required'],
            'user_id'      => ['required'],
        ])->validate();

        $html = '';
        //----- Check existing bookings ----
        $event = Event::find($request->event_id);
        //------- check if event active ----
        if( $event->is_active == 0 )
            exit();

        $setting = Business::find(Auth::user()->business_id)->Settings->where('key','stop_booking')->first();
        $brandName = Business::find(Auth::user()->business_id)->Settings->where('key','email_sender_name')->first(); 
        $dateFormat = Business::find(Auth::user()->business_id)->Settings->where('key','date_format')->first(); 

        //-------- Minus hours from event time, so that can check it can be booked or not now ---
        $bookTime = explode(':',$event->time);
        $bookTime[0] = $bookTime[0]-$setting->value;
        $bookTimeFinal = $bookTime[0].':'.$bookTime[1];

        $eventTime = strtotime(\Carbon\Carbon::parse($event->date)->format('Y-m-d').' '.$bookTimeFinal);
        $currentTime = strtotime(\Carbon\Carbon::now()->format('Y-m-d H:i'));

         //------ If date is passed then means booking are made from backward -------
         $eventDate = strtotime($event->date);
         $currentDate = strtotime(\Carbon\Carbon::now()->format('Y-m-d'));

        //  if($eventTime >= $currentTime || $eventDate < $currentDate){
        if(true){
            $currentBookings = $event->eventActiveSlots->count();
            $limit = $event->slots;
            $check = $event->eventActiveSlots->where('user_id',$request->user_id)->count();

            
            //---- Check if user not book for this event before ----
            if($check == 0)
            {
                $booking = new EventSlot();
                $booking->user_id       = $request->user_id;
                $booking->event_id      = $request->event_id;
                $booking->business_id   = Auth::user()->business_id;
                $booking->comment       = $request->comment;
                $booking->is_guest      = ($request->guest ? '1':'0');
                $booking->status        = ($currentBookings < $limit ? 1 : 0 );
                if($booking->save())
                {
                    $currentBookings++;
                    $html .= $this->getTrData($booking);
                    $bookingStatusGuest = $bookingStatusGuestSms = '';
                    //---- If guest YES -----
                    if( $request->guest == 1 ){
                        $guest = new EventSlot();
                        $guest->user_id       = $request->user_id;
                        $guest->event_id      = $request->event_id;
                        $guest->business_id   = Auth::user()->business_id;
                        $guest->comment       = $request->comment;
                        $guest->status        = ($currentBookings < $limit ? 1 : 0 );
                        $guest->is_guest    = 0;
                        $guest->parent_slot   = $booking->id;
                        $guest->save();
                        $currentBookings++;
                        $html .= $this->getTrData($guest);
                        $bookingStatusGuest = __('event.guest_booking_status').' <b>'.($guest->status ? __('event.booked') : __('event.waiting_list')).'</b><br>';
                        $bookingStatusGuestSms = __('event.guest_booking_status').($guest->status ? __('event.booked') : __('event.waiting_list'));
                    }

                    // -------  for this user's will change language ------
                    \App::setLocale($booking->user->language);

                    //-------------- Send booking email --------
                    
                    $instructor = '<a target="_blank" href="'.url('/showUser/'.md5($event->user->id)).'">'.$event->user->name.'</a>';
                    $link = url('/other-account/'.md5($event->user->id));
                    $subject = __('emailtxt.event_book_subject',['name' => $brandName->value]);
                    $content = __('emailtxt.event_book_txt',['name' => $booking->user->name,'date' => \Carbon\Carbon::parse($booking->event->date)->format($dateFormat->value), 'time' => $booking->event->time, 'status' => ($booking->status ? __('event.booked') : __('event.waiting_list')), 'guest' => $bookingStatusGuest, 'ename' => $booking->event->name, 'link' => $link, 'instructor' => $instructor ]);

                    if($booking->user->email != ''){
                        \Log::channel('custom')->info("Sending email to customer about event booking.",['business_id' => Auth::user()->business_id, 'user_id' => $booking->user->id, 'subject' => $subject, 'email_text' => $content]);
                        $this->dispatch(new SendEmailJob($booking->user->email,$subject,$content,$brandName->value,$booking->user->business_id));

                        //------ Add log in DB for location -----//
                        $type = "log_for_booking_related_details";
                        $txt = "Event booking has been made. <br> Customer Email: <b>".$booking->user->email."</b><br>Event Date & Time: <b>".$booking->event->date.' '.$booking->event->time."</b>";
                        $this->addLocationLog($type,$txt);

                        #------ Send email to Therapist about this booking ----//
                        $this->notifyTherapist($event->user->id,$subject,$content,$brandName->value);

                    }

                    //---------------------- Send sms notification to user --------------------        
                    //--- check if business allow to send sms ----
                    $smsSetting = Business::find(Auth::user()->business_id)->Settings->where('key','sms_setting')->first();
                    $tbookingSms = Business::find(Auth::user()->business_id)->Settings->where('key','e_booking_sms')->first();

                    if($tbookingSms->value == 'true' && $smsSetting->value == 'true'){
                        $sms = __('smstxt.event_book_txt',['name' => $booking->user->name,'date' => \Carbon\Carbon::parse($booking->event->date)->format($dateFormat->value), 'time' => $booking->event->time, 'status' => ($booking->status ? __('event.booked') : __('event.waiting_list')), 'guest' => $bookingStatusGuestSms, 'ename' => $booking->event->name  ]);

                        //--- send sms notification ---
                        \Log::channel('custom')->info("Sending sms to customer about event booking.",['business_id' => Auth::user()->business_id, 'user_id' => $booking->user->id, 'sms_text' => $sms]);
                        $this->smsSendWithCheck($sms,$booking->user->id,$booking->event->date,$booking->event->time);
                       
                    }

                    if(false){//-----remove when functional ---
                    //---------------------- Check survey is on in setting ---------------------------//
                    $surveySetting = Business::find(Auth::user()->business_id)->Settings->where('key','survey_setting')->first();
                    $surveyDuration = Business::find(Auth::user()->business_id)->Settings->where('key','survey_duration')->first();

                    if($surveySetting->value == 'true'){
                        $checkToSend = 0;
                        $email = User::find($booking->user->id);
                        //---- Check if this user received survey email before -------//
                        if(!empty($email->survey_date)){
                            $surveyDate = \Carbon\Carbon::parse($email->survey_date)->addMonth(($surveyDuration ? $surveyDuration->vlaue : 3))->format('Y-m-d');
                            $today = \Carbon\Carbon::now()->format('Y-m-d');
                            //--- If last survey email send time more then 6 month ---
                            if($surveyDate < $today){
                                $checkToSend = 1;
                            }
                        }
                        else
                            $checkToSend = 1;
                        
                        //----------------------------- send survey email to user -------------------------//
                        if($checkToSend){
                            $surveySendHour = Business::find(Auth::user()->business_id)->Settings->where('key','survey_send_hours')->first();
                            $edate = \Carbon\Carbon::parse($booking->event->date)->format('Y-m-d');
                            $delay = \Carbon\Carbon::parse($edate.' '.$booking->event->time)->addHours($surveySendHour->value);

                            // $delay = \Carbon\Carbon::now()->addHours($surveySendHour->value);

                            $link = url('/'."Survey/".$email->language);
                            $sub = __('emailtxt.survey_email_subject',['name' => $brandName->value]);
                            ;
                            $con =  __('emailtxt.survey_email_txt',['name' => $email->name, 'link' => $link]);
                        ;
                            $job = (new SendEmailJob($email->email,$sub,$con,$brandName->value,$email->business_id))->delay($delay);

                            \Log::channel('custom')->info("Sending survey email to customer about event booking.",['business_id' => Auth::user()->business_id, 'user_id' => $email->id, 'subject' => $sub, 'email_text' => $con]);

                            $this->dispatch($job);
                            $email->survey_date = date('Y-m-d');
                            $email->save();
                        }
                    } 
                    }//-----remove when functional ---

                    //------- Make ICS file for this event ---------

                    $type = __('event.events');
                    $tName = __('event.events').': '.$booking->event->name;
                    $tDuration = $booking->event->duration.'min';
                    $tDate = $booking->event->date;
                    $tTime = $booking->event->time;
                    $tTherapist = __('event.therapist').': '.$event->user->name;

                    $this->createIcsFile($type,$tName,$tDuration,$tDate,$tTime,$tTherapist,$booking->id);


                    $response['status'] = 'success';
                    $response['bookings'] = $html;
                    $response['count'] = $currentBookings;
                    if( $currentBookings < $limit )
                        $response['label'] = 1;
                    else 
                        $response['label'] = 0;
                }
                else{
                    \Log::channel('custom')->error("There is an error to book event.",['business_id' => Auth::user()->business_id, 'event_id' => $event->id ]);
                    $response['status'] = 'error';
                }
            }
            else
                $response['status'] = 'exist';
        }
        else{
            $response['status'] = 'exceeded'; 
            \Log::channel('custom')->warning("Can't book now because time to book has been passed.",['business_id' => Auth::user()->business_id, 'Stop_booking_befoe_hours' => $setting->value, 'current_time' => \Carbon\Carbon::now()->format('Y-m-d H:i') , 'try_to_book_time' => \Carbon\Carbon::parse($event->date)->format('Y-m-d').' '.$event->time]);
        } 


        return json_encode($response);
    }

    //--##############################################################################################//
    public function RestoreEventBookingAjax(Request $request){
        abort_unless(\Gate::allows('Event Booking Restore'),403);
        Validator::make($request->all(), [
            'id'     => ['required'],
        ])->validate();
        
            $slot = EventSlot::where(\DB::raw('md5(id)') , $request->id)->first();
            $brandName = Business::find(Auth::user()->business_id)->Settings->where('key','email_sender_name')->first(); 
            $dateFormat = Business::find(Auth::user()->business_id)->Settings->where('key','date_format')->first(); 
            $smsSetting = Business::find(Auth::user()->business_id)->Settings->where('key','sms_setting')->first();
            $bookingRestored = Business::find(Auth::user()->business_id)->Settings->where('key','e_r_booking_sms')->first();
            $bookingRestored = Business::find(Auth::user()->business_id)->Settings->where('key','e_r_booking_sms')->first();
            $clipUsedSms = Business::find(Auth::user()->business_id)->Settings->where('key','clip_used')->first();

            //---- check if it's gust booking ----
            if($slot->parent_slot != Null){
                $slot = EventSlot::where('id', $slot->parent_slot)->first();
            }

            $check = EventSlot::where('user_id',$slot->user_id)->where('is_active',1)->where('event_id',$slot->event_id)->count();
            $totalBooked = EventSlot::where('is_active',1)->where('event_id',$slot->event_id)->count();
            $childs = EventSlot::where('parent_slot',$slot->id)->get();

            //----- Check if there is any booking on deleted ----//
            if(!$check){
                $event = Event::where('id',$slot->event_id)->first();
                //----- Check if event is active ----//
                if( $event->is_active ){
                    
                    // -------  for this user's will change language ------
                    \App::setLocale($slot->user->language);

                    //---- if no booking exist then restore clips  ----//
                    $clip = $slot->deactiveclip;
                    if($clip != Null){
                        $card = $clip->card;
                        if($card->clips >= $clip->amount ){
                            $card->clips = $card->clips-$clip->amount;
                            if($card->save()){
                                $clip->is_active = 1;
                                $clip->save();

                                //------ Add log in DB for location -----//
                                $type = "log_for_card_and_clips";
                                $txt = "Clip has been cut back with booking restored. <br> User email: <b>".$card->user->email."</b><br>Clips Cut: <b>".$clip->amount."</b>";
                                $this->addLocationLog($type,$txt);

                                //-------- Send Email to user about Clips ----------
                                $subject =  __('emailtxt.clips_restore_subject',['name' => $brandName->value]);
                                $content =  __('emailtxt.clips_restore_txt',['name' => $card->user->name, 'amount' => $clip->amount , 'title' => $card->name, 'expiry' => \Carbon\Carbon::parse($card->expiry_date)->format($dateFormat->value), 'clips' => ($card->clips ?: 0), 'for' => ($card->type == 2 ? __('card.event') : __('card.treatment') )  ]);
                
                                 //------- Send sms notification if active from settings -----
                                 if($clipUsedSms->value == 'true' && $smsSetting->value == 'true'){
                                    $sms = __('smstxt.clips_restore_txt',['name' => $card->user->name, 'amount' => $clip->amount , 'title' => $card->name, 'expiry' => \Carbon\Carbon::parse($card->expiry_date)->format($dateFormat->value), 'clips' => ($card->clips ?: 0), 'for' => ($card->type == 2 ? __('card.event') : __('card.treatment') )  ]);

                                    //--- send sms notification ---
                                    \Log::channel('custom')->info("Sending sms to customer about Clips.",['business_id' => Auth::user()->business_id, 'sms_text' => $sms]);
                                    $this->smsSendWithCheck($sms,$card->user->id);
                                }
                
                                if($card->user->email != ''){
                                    \Log::channel('custom')->info("Sending email to customer about Clips.",['business_id' => Auth::user()->business_id, 'user_id' => $card->user->id, 'subject' => $subject, 'email_text' => $content]);
                                    $this->dispatch(new SendEmailJob($card->user->email,$subject,$content,$brandName->value,$card->user->business_id));
                                }
                            }
                        }else{
                            $clip->delete();
                            $response['clipStatus'] = 'deleted';
                        }
                    }
                    //----- if already event booking limit reached ----
                    if($totalBooked >= $event->slots)
                        $slot->status = 0;

                    $slot->is_active = 1;
                    $slot->save();


                    $bookingStatusGuest = '';
                    //-------------- Send booking email --------
                    $link = url('/other-account/',md5($slot->user->id));

                    $subject = __('emailtxt.event_book_subject',['name' , $brandName->value]);

                    $content = __('emailtxt.event_book_txt',['name' ,$slot->user->name,'date' => \Carbon\Carbon::parse($slot->event->date)->format($dateFormat->value), 'time' => $slot->event->time, 'status' => ($slot->status ? __('event.booked') : __('event.waiting_list')), 'guest' => $bookingStatusGuest, 'link' => $link, 'ename' => $slot->event->name ]);
                    
                    //------ Add log in DB for location -----//
                    $type = "log_for_booking_related_details";
                    $txt = "Event booking restored. <br> Customer Email: <b>".$slot->user->email."</b><br>Event Name: <b>".$slot->event->name."</b><br>Event Date & Time: <b>".$slot->event->date.' '.$slot->event->time."</b>";
                    $this->addLocationLog($type,$txt);

                    //------- Send sms notification to user ------        
                    //--- check if business allow to send sms ----
                    if($bookingRestored->value == 'true' && $smsSetting->value == 'true'){
                        $guestMessage = '';
                        $sms = __('smstxt.event_book_txt',['name' , $slot->user->name,'date' => \Carbon\Carbon::parse($slot->event->date)->format($dateFormat->value), 'time' => $slot->event->time, 'status' => ($slot->status ? __('event.booked') : __('event.waiting_list')), 'guest' => $guestMessage, 'ename' => $slot->event->name  ]);

                        //--- send sms notification ---
                        \Log::channel('custom')->info("Sending sms to customer about event booking restored.",['business_id' => Auth::user()->business_id, 'user_id' => $slot->user->id, 'sms_text' => $sms]);
                        $this->smsSendWithCheck($sms,$slot->user->id,$slot->event->date,$slot->event->time);

                    }

                    if($slot->user->email != ''){
                        \Log::channel('custom')->info("Sending email to customer about event booking restored.",['business_id' => Auth::user()->business_id, 'user_id' => $slot->user->id, 'subject' => $subject,'email_text' => $content]);
                        $this->dispatch(new SendEmailJob($slot->user->email,$subject,$content,$brandName->value,$slot->user->business_id));

                        #------ Send email to Therapist about this booking ----//
                        $this->notifyTherapist($event->user->id,$subject,$content,$brandName->value);

                    }
                    else{
                        \Log::channel('custom')->warning("User did not have email address.",['business_id' => Auth::user()->business_id, 'user_id' => $slot->user->id]);
                    }

                    foreach($childs as $child){
                        $totalBooked++;
                        //---- if no booking exist then restore clips  ----//
                        $clip = $child->deactiveclip;
                        if($clip != Null){
                            $card = $clip->card;
                            if($card->clips >= $clip->amount ){
                                $card->clips = $card->clips-$clip->amount;
                                if($card->save()){
                                    $clip->is_active = 1;
                                    $clip->save();

                                    //-------- Send Email to user about Clips ----------

                                    $subject =  __('emailtxt.clips_restore_subject',['name' => $brandName->value]);
                                    $content =  __('emailtxt.clips_restore_txt',['name' => $card->user->name, 'amount' => $clip->amount , 'title' => $card->name, 'expiry' => \Carbon\Carbon::parse($card->expiry_date)->format($dateFormat->value), 'clips' => ($card->clips ?: 0), 'for' => ($card->type == 2 ? __('card.event') : __('card.treatment') )  ]);
                        
                                    if($card->user->email != ''){
                                        \Log::channel('custom')->info("Sending email to custome about clips.",['business_id' => Auth::user()->business_id, 'user_id' => $card->user->id, 'subject' => $subject, 'email_text' => $content]);
                                        $this->dispatch(new SendEmailJob($card->user->email,$subject,$content,$brandName->value,$card->user->business_id));
                                    }
                                    else{
                                        \Log::channel('custom')->warning("Email did not exist of this customer.",['business_id' => Auth::user()->business_id, 'user_id' => $card->user->id]);
                                    }
                                }
                            }else{
                                $clip->delete();
                                $response['clipStatus'] = 'deleted';
                            }
                        }
                        //----- if already event booking limit reached ----
                        if($totalBooked >= $event->slots)
                            $child->status = 0;
                        $child->is_active = 1;
                        $child->save();

                        $guestMessage = __('event.guest_booking_status').': <b>'.($child->status ? __('event.booked') : __('event.waiting_list')).'</b><br>';

                        //------------ Send booking email to user ------
                        $subject = __('emailtxt.event_book_subject',['name' , $brandName->value]);

                        $content = __('emailtxt.event_book_txt',['name' , $child->user->name,'date' => \Carbon\Carbon::parse($child->event->date)->format($dateFormat->value), 'time' => $child->event->time, 'status' => ($child->event->status ? __('event.booked') : __('event.waiting_list')), 'guest' => $guestMessage, 'ename' => $child->event->name]);

                        if($child->user->email != ''){
                            \Log::channel('custom')->info("Sending email to custome about event Booking (GUEST).",['business_id' => Auth::user()->business_id, 'user_id' => $child->user->id, 'subject' => $subject, 'email_text' => $content]);
                            $this->dispatch(new SendEmailJob($child->user->email,$subject,$content,$brandName->value,$child->user->business_id));
                        }
                    }
                    $response['status'] = 'success';

                }
                else{
                    $response['status'] = 'eventNotExist';
                    \Log::channel('custom')->warning("Event has been deleted or not exist so can't book.",['business_id' => Auth::user()->business_id, 'event_id' => $event->id]);
                }
            }
            else{
                $response['status'] = 'bookingExist';
                \Log::channel('custom')->warning("Customer already booked in this event.",['business_id' => Auth::user()->business_id, 'user_id' => $slot->user_id, 'event_id' => $slot->event_id]);
            }
        return \Response::json($response);
    }

    //--#######################################################--//
    public function getTrData($data){

        $dateFormat = Business::find(Auth::user()->business_id)->Settings->where('key','date_format')->first(); 
        $timeFormat = Business::find(Auth::user()->business_id)->Settings->where('key','time_format')->first(); 

        $guest = '';
        if($data->event->is_guest){
            $guest = '<td class="guest">'.($data->parent_slot ? __('event.guest') : ($data->is_guest ? __('event.yes') : __('event.no'))).'</td>';
        }

        //------- Cards list for this user ----
        $card = '';
        $cards = User::find($data->user_id)->enentCards;
        if($cards->count() > 0){
            foreach($cards as $key => $value){
                $date = strtotime($value->expiry_date);
                $today = strtotime('today');

                if($date < $today)
                    $card .= '<option value="'.$value->id.'" disabled>'.$value->name.' ('.__("event.expired").')</option>';
                else    
                    $card .= '<option value="'.$value->id.'" >'.$value->name.'</option>';
            }
        }
        else{
            $card = '<option value="" selected>'.__('card.no_cards').'</option>';
        }
        

        $html = '<tr>
        <td class="text-center status '.($data->status ? 'bg-info' : 'bg-warning').'">
          <span>'.($data->status ? __("event.booked") : __("event.waiting_list") ).'</span> 
        </td>
        <td>'.$data->event->time.'</td>
        <td class="name">
        '.$data->user->name.'
        </td>
        <td class="email">
        '.$data->user->email.'
        </td>
        <td class="number">
            <a href="Tel:'.$data->user->number.'">'.$data->user->number.'</a>
        </td>
        <td class="text-center clipCard">
            <select name="card" class="form-control select2" data-time="'.$data->event->time.'" onchange="cardSelect(this)">
            <option value="">-- '.__("event.select").' --</option>
                '.$card.'
            </select> 
        </td>
        <td class="text-center cutBack"></td>
        <td class="text-center cutCard" data-id="'.$data->id.'" data-event-id="'.$data->event_id.'"></td>
        <td>'.\Carbon\Carbon::parse($data->created_at)->format($dateFormat->value.($timeFormat->value == 12 ? ' h:i:s a' : ' H:i:s' )).'</td>
        <td class="comment">
        '.$data->comment.'       
        </td>
        '.$guest.'
        <td class="submit">
          <button type="submit" class="btn btn-danger btn-sm" data-id="'.md5($data->id).'" onclick="deleteBooking(this)">'.__("keywords.delete").'</button>
        </td>
      </tr>';

      return $html;
    }

    //--######################################################--//
    public function eventUpdateNotification($id,$changes,$changesDone){
        if(empty($changes)){
            \Log::channel('custom')->info("Event update email not sent because no change happend");
            return true;
        }   

        $event = Event::find($id);
        $users = EventSlot::where('event_id',$id)->where('is_active',1)->groupBy('user_id')->pluck('user_id');
        $brandName = Business::find($event->business_id)->Settings->where('key','email_sender_name')->first(); 
        $dateFormat = Business::find($event->business_id)->Settings->where('key','date_format')->first(); 
        $eventUpdateEmail = Business::find($event->business_id)->Settings->where('key','event_update_email')->first(); 
            
        //----- IF off from settings
        if($eventUpdateEmail->value == 'false'){
            \Log::channel('custom')->info("Event update email not sent because off from settings");
            return true;
        }    

        //------ Add log in DB for location -----//
        $type = "log_for_event_related_details";
        $txt = "Event has been updated. <br> Event Name: <b>".$event->name."</b><br>Event Date & Time: <b>".$event->date.' '.$event->time."</b>";
        $this->addLocationLog($type,$txt);

        $instructor = "<a href='".url('/showUser/'.md5($event->user->id))."'>".$event->user->name."</a>";

        $subject = __('emailtxt.event_update_subject',['name' => $brandName->value]);
        foreach($users as $user){

            $udata = User::find($user);
            // -------  for this user's will change language ------
            $link = url('/other-account/'.md5($udata->id));

            \App::setLocale($udata->language);

            $cc = array();
            foreach($changes as $key => $c){
                $cc[$key] = __('event.'.$c).' '.$changesDone[$c]['before'].' '.__('keywords.to').' '.$changesDone[$c]['after'];
            }

            $content = __('emailtxt.event_update_txt',['name' => $udata->name,'date' => \Carbon\Carbon::parse($event->date)->format($dateFormat->value), 'time' => $event->time, 'ename' => $event->name, 'link' => $link, 'instructor' => $instructor, 'changes' => implode(',',$cc) ]);

            if($udata->email != ''){
                \Log::channel('custom')->info("Sending email to customer about event update.",['business_id' => Auth::user()->business_id, 'user_id' => $udata->id, 'subject' => $subject, 'email_text' => $content]);
                $this->dispatch(new SendEmailJob($udata->email,$subject,$content,$brandName->value,$udata->business_id));
            }
        }
    }

    
    public function getTherapistsAjax(Request $request){
        
        Validator::make($request->all(), [
            'date'     => ['required'],
            'time'     => ['required'],
        ])->validate();

        $time = $request->time;
        
        $therapistsNotAllowed = Event::where('business_id',Auth::user()->business_id)
        ->where('date',$request->date)
        ->where('is_active',1)
        ->where(
        function($query) use ($time) {
            return $query
                ->where('time',$time);
        })
        ->get()->pluck('user_id')->toArray();

        $therapists = Business::find(Auth::user()->business_id)->users->where('role','!=','Customer')->where('is_active',1)->where('is_therapist',1)->whereNotIn('id',$therapistsNotAllowed);
        
        $data = '';
        foreach($therapists as $therapist){
            $data .= '<option value="'.$therapist->id.'">'.$therapist->name.'</option>';
        }

        return json_encode($data);
    }

    
}//------ End Class

