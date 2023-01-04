<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Business;
use App\Models\Treatment;
use App\Models\Date;
use App\Models\Card;
use App\Models\UsedClip;
use App\Models\User;
use App\Models\Links;
use App\Models\TreatmentSlot;
use App\Models\TreatmentWaitingSlot;
use App\Models\Department;
use App\Jobs\SendEmailJob;
use App\Jobs\SendSmsJob;
use App\Notifications\BookingNotification;
use Illuminate\Support\Str;


class TreatmentController extends Controller
{
    //---###################################################################//

    public function treatmentList(){
        abort_unless(\Gate::allows('Treatment View'),403);
        $treatments = Business::find(Auth::user()->business_id)->treatments;
        $settings = Business::find(Auth::user()->business_id)->Settings->where('key','clipboard_treatment')->first();

        return view('treatment.treatment_list',compact('treatments','settings'));
    }
    
    //---##################################################################//

    public function creatTreatment(){
        abort_unless(\Gate::allows('Treatment Create'),403);
        $business = Business::find(Auth::user()->business_id);
        $settings = Business::find(Auth::user()->business_id)->Settings->where('key','clipboard_treatment')->first();
        return view('treatment.create_treatment',compact('business','settings'));
    }

    //---####################################################################//

    public function editTreatment($subdomain,$id){
        abort_unless(\Gate::allows('Treatment Edit'),403);
        $business = Business::find(Auth::user()->business_id);
        $treatment = Business::find(Auth::user()->business_id)->treatment->where(\DB::raw('md5(id)') , $id)->first();
        $settings = Business::find(Auth::user()->business_id)->Settings->where('key','clipboard_treatment')->first();

        return view('treatment.edit_treatment',compact('treatment','business','settings'));
    }

    //---####################################################################//

    public function deleteTreatment($subdomain,$id){
        abort_unless(\Gate::allows('Treatment Edit'),403);
        $business = Business::find(Auth::user()->business_id);
        $treatment = Business::find(Auth::user()->business_id)->treatment->where(\DB::raw('md5(id)') , $id)->first();
        $treatmentInDate = Treatment::find($treatment->id)->allDates->count();
        $treatmentInSlots = TreatmentSlot::where('treatment_id',$treatment->id)->count();
        if($treatmentInDate < 1 && $treatmentInSlots < 1){
            $treatment->delete();
            session()->flash('success',__('treatment.thbds'));
        }
        else
            session()->flash('error',__('treatment.taiuscd'));
        
        return \Redirect::back();        
    }

    //---##################################################################//

    public function updateTreatment(Request $request){
        abort_unless(\Gate::allows('Treatment Edit'),403);
        Validator::make($request->all(), [
            'id' => ['required'],
            'name' => ['required', 'string', 'max:255'],
            'interval' => ['required'],
            'clips' => ['required'],
            'price' => ['required'],
        ])->validate();
        
        $treatment = Treatment::find($request->id);

        $treatment->treatment_name = $request->name;
        $treatment->inter = $request->interval;
        $treatment->clips = $request->clips;
        $treatment->price = $request->price;
        $treatment->time_shown = $request->time_shown;
        $treatment->description = $request->desc;

        if($request->status)
            $treatment->is_active = 1;
        else
            $treatment->is_active = 0;

        if($request->visible)
            $treatment->is_visible = 1;
        else
            $treatment->is_visible = 0;    

        if($request->insurance)
            $treatment->is_insurance = 1;
        else
            $treatment->is_insurance = 0;       
        
        if($treatment->save()){
            \Log::channel('custom')->info("Treatment Updated.",['business_id' => Auth::user()->business_id]);
            $request->session()->flash('success',__('treatment.thbus'));
         
            //------ Add log in DB for location -----//
            $type = "log_for_date_related_details";
            $txt = "Treatment part has been Updated. <br> Treatment Part Name: <b>".$treatment->treatment_name."</b>";
            $this->addLocationLog($type,$txt);

        }
        else{
            \Log::channel('custom')->error("Issue to update treatment.",['business_id' => Auth::user()->business_id]);
            $request->session()->flash('error',__('treatment.tiaetut'));
        }
        return \Redirect::back();
    }

    //---#######################################################################//

    public function addTreatment(Request $request){
        abort_unless(\Gate::allows('Treatment Create'),403);
        Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'interval' => ['required'],
            'clips' => ['required'],
            'price' => ['required'],
        ])->validate();

        $treatment = new Treatment();
        $treatment->treatment_name  = $request['name'];
        $treatment->inter           = $request['interval'];
        $treatment->clips           = $request['clips'];
        $treatment->price           = $request['price'];
        $treatment->business_id     = Auth::user()->business_id;
        $treatment->time_shown      = $request['time_shown'];
        $treatment->description      = $request['desc'];

        if($request->visible)
            $treatment->is_visible = 1;
        else
            $treatment->is_visible = 0; 

        if($request->insurance)
            $treatment->is_insurance = 1;
        else
            $treatment->is_insurance = 0;     

        $treatment->save();
        
        if($treatment->id){
            \Log::channel('custom')->info("Treatment Crated.",['business_id' => Auth::user()->business_id]);
            $request->session()->flash('success',__('treatment.thbcs'));

            //------ Add log in DB for location -----//
            $type = "log_for_date_related_details";
            $txt = "Treatment part has been created. <br> Treatment Part Name: <b>".$treatment->treatment_name."</b>";
            $this->addLocationLog($type,$txt);
        }
        else{
            \Log::channel('custom')->error("Issue to create Treatment.",['business_id' => Auth::user()->business_id]);
            $request->session()->flash('error',__('tiaetct.thbcs'));
        }
        return \Redirect::back();
    }

    //---####################################################################//

    public function createTreatmentDates(){
        abort_unless(\Gate::allows('Date Create'),403);
        $treatments = Business::find(Auth::user()->business_id)->treatments->where('is_active','1');
        return view('treatment.create_dates',compact('treatments'));
    }

    //---####################################################################//

    public function treatmentDatesList(){
        abort_unless(\Gate::allows('Date View'),403);
        // $dates = Business::find(Auth::user()->business_id)->Dates->where('is_active',1)->where('date','>=',date('Y-m-d'));
        $dates = Date::where([['is_active',1],['date','>=',date('Y-m-d')],['business_id',Auth::user()->business_id]])->orderBy('date','asc')->simplePaginate(20);

        $treatments = Business::find(Auth::user()->business_id)->treatments->where('is_active','1');
        $therapists = Business::find(Auth::user()->business_id)->users->where('role','!=','Customer')->where('is_active',1)->where('is_therapist',1);
        $dateFormat = Business::find(auth()->user()->business_id)->Settings->where('key','date_format')->first();

        return view('treatment.dates_list',compact('dates','treatments','therapists','dateFormat'));
    }

    //---#################################################################################//

    public function treatmentDatesDeletedList(){
        abort_unless(\Gate::allows('Deleted Dates View'),403);
        $dates = Date::where([['is_active',0],['business_id',Auth::user()->business_id]])->orderBy('date','desc')->get();
        $treatments = Business::find(Auth::user()->business_id)->treatments->where('is_active','1');
        $dateFormat = Business::find(Auth::user()->business_id)->Settings->where('key','date_format')->first(); 
        $timeFormat = Business::find(Auth::user()->business_id)->Settings->where('key','time_format')->first(); 

        return view('treatment.deleted_dates_list',compact('dates','treatments','dateFormat','timeFormat'));
    }

     //---#################################################################################//

    public function pastdatelist(){
        abort_unless(\Gate::allows('Date View'),403);
        // $dates = Business::find(Auth::user()->business_id)->Dates->where('date','>=',date('Y-m-d'))->where('is_active',1);
        $dates = Date::where([['is_active',1],['date','<',date('Y-m-d')],['business_id',Auth::user()->business_id]])->orderBy('date','desc')->simplePaginate(20);
        $treatments = Business::find(Auth::user()->business_id)->treatments->where('is_active','1');
        $therapists = Business::find(Auth::user()->business_id)->users->where('role','!=','Customer')->where('is_active',1);
        $dateFormat = Business::find(auth()->user()->business_id)->Settings->where('key','date_format')->first();

        return view('treatment.past_dates',compact('dates','treatments','therapists','dateFormat'));
    }

    //---##############################################################################//

    public function saveTreatmentDate(Request $request){
        abort_unless(\Gate::allows('Date Create'),403);
        Validator::make($request->all(), [
            '_date'          => ['required'],
            'treatment'     => ['required'],
            'recurrence'    => ['required'],
            'recurring_num' => ['required','integer'],
            'from'          => ['required'],
            'till'            => ['required'],
            'lunch'            => ['required'],
            'tharapist'     => ['required','max:255'],
        ])->validate();


        //------ Check if lunch time between From and till time ----//
        if($request->lunch != 'none'){

            $lunch = strtotime(\Carbon\Carbon::now()->format('Y-m-d').' '.$request->lunch);
            $form = strtotime(\Carbon\Carbon::now()->format('Y-m-d').' '.$request->from);
            $till = strtotime(\Carbon\Carbon::now()->format('Y-m-d').' '.$request->till);

            if($lunch >= $form && $lunch <= $till){
                $lunchBook = 1;
            } 
            else {
                $request->session()->flash('error',__('treatment.tcnbbstflna'));
                return \Redirect::back();
            }
        }
                
        $dates = array();
        if($request->recurrence == 'd') {
            for( $i = 0; $i < $request->recurring_num; $i++ ){
                $dates[$i] = date("Y-m-d", strtotime('+ '.$i.' days' , strtotime($request->_date)));
            }
        }
        elseif($request->recurrence == 'dd') {
            for( $i = 0; $i < 7; $i++ ){
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

        foreach( $dates as $date ){

            if($request->waiting_list)
                $waitingList = 1;
            else 
                $waitingList = 0;  

            $treatmentId = new Date();
            $treatmentId->business_id   = Auth::user()->business_id;
            $treatmentId->user_id       = $request->tharapist;
            $treatmentId->date          = $date;
            $treatmentId->from          = $request->from;
            $treatmentId->till          = $request->till;
            $treatmentId->recurrence    = $request->recurrence;
            $treatmentId->recurring_num = $request->recurring_num;
            $treatmentId->description   = $request->desc;
            $treatmentId->waiting_list  = $waitingList;
            $treatmentId->save();

            if( $treatmentId->id ){
                foreach( $request->treatment as $treatment ){
                    \DB::table('date_treatment')->insert([
                        'treatment_id'  => $treatment,
                        'date_id'       =>  $treatmentId->id,
                    ]);
                }
            }

            if($request->lunch != 'none'){

                $timeInterval = Business::find(Auth::user()->business_id);
                #--------------- Create new date for lunch everytime ---------------#
                //----- here 30 is static because lunch time willbe min 30 --//
                $limit = ceil(30/$timeInterval->time_interval);
                $slots = $this->getTimeSlots($request->lunch,$timeInterval->time_interval,$limit);

                for($i=0; $i < $limit; $i++){
                    $slot = TreatmentSlot::create([
                        'user_id' => 0,
                        'date_id' => $treatmentId->id,
                        'business_id'       => Auth::user()->business_id,
                        'status'            => 'Lunch',
                        'time'              => $slots[$i],
                    ]);
                }
                $data = 'success';
                \Log::channel('custom')->info("Treatment slot has been booked for lunch.",['business_id' => Auth::user()->business_id]);

                //------ Add log in DB for location -----//
                $type = "log_for_booking_related_details";
                $txt = "Treatment slot has been booked for lunch. <br> Treatment date: <b>".$date."</b><br>Time: <b>".$slots[0]."</b>";
                $this->addLocationLog($type,$txt);                
            }

            //------ Add log in DB for location -----//
            $type = "log_for_date_related_details";
            $txt = "Treatment date has been Created. <br> Treatment Date: <b>".$date."</b><br>Treatment Time: <b>".$request->from.' - '.$request->till."</b><br>Created By: <b>".Auth::user()->email."</b>";
            $this->addLocationLog($type,$txt);

        }
        \Log::channel('custom')->info("Treatment date has been Created.",['business_id' => Auth::user()->business_id]);
        $request->session()->flash('success',__('treatment.thbcs'));
        
        return \Redirect::route('treatmentbookings',session('business_name'));
    }

    //---#########################################################################//

    public function updateDateAjax(Request $request){
        abort_unless(\Gate::allows('Date Edit'),403);
        
        Validator::make($request->all(), [
            'id'          => ['required'],
            '_date'     => ['required'],
            'treatment'    => ['required'],
            'from'          => ['required'],
            'till'            => ['required'],
            'therapist'     => ['required','max:255'],
        ])->validate();

        $date = Date::find($request->id);

        //----- check if already slots booked below 'From' or above 'Till' then don't update date timing 
        $bookingCheck = $this->checkExisingTimes($request->id,$request->from,$request->till);
        if($bookingCheck){
            $data = 'limit';
            return \Response::json($data);
        }

        $changes = array();
        $changesDone = array();

        if( $date->date != $request->_date ){
            array_push($changes,'date');
            $changesDone['date']['before'] = $date->name;
            $changesDone['date']['after'] = $request->_date;
        }
        if($date->user_id != $request->therapist){
            $user = User::find($request->therapist);
            array_push($changes,'therapist');
            $changesDone['therapist']['before'] = $date->user->name;
            $changesDone['therapist']['after'] = $user->name;
        }
        if($date->till != $request->till){
            array_push($changes,'time');
            $changesDone['time']['before'] = $date->till;
            $changesDone['time']['after'] = $request->till;
        }
        if($date->from != $request->from){
            array_push($changes,'time');
            $changesDone['time']['before'] = $date->from;
            $changesDone['time']['after'] = $request->from;
        }
        if($date->description != $request->description){
            array_push($changes,'description');
            $changesDone['description']['before'] = $date->description;
            $changesDone['description']['after'] = $request->description;
        }

        if($request->waiting_list)
            $waitingList = 1;
        else 
            $waitingList = 0;  

        $date->date = $request->_date;
        $date->user_id = $request->therapist;
        $date->till = $request->till;
        $date->from = $request->from;
        $date->description = $request->description;
        $date->waiting_list = $waitingList;

        if( $date->save() ){

            $this->dateUpdateNotification($date->id,$changes,$changesDone);

            \Log::channel('custom')->info("Treatment date has been updated.",['business_id' => Auth::user()->business_id]);

            //------ Add log in DB for location -----//
            $type = "log_for_date_related_details";
            $txt = "Treatment date has been updated. <br> Treatment date: <b>".$request->_date."</b><br>Treatment Time: <b>".$request->from.' - '.$request->till."</b><br>Updated By:<b>".Auth::user()->email."</b>";
            $this->addLocationLog($type,$txt);
            
            //------ Delete Old relation and create new ------
            \DB::table('date_treatment')->where('date_id', $request->id)->delete();
            foreach( $request->treatment as $treatment ){
                \DB::table('date_treatment')->insert([
                    'treatment_id'  => $treatment,
                    'date_id'       =>  $request->id,
                ]);
            }

            if( $request->lunch )
            {
               
                $timeInterval = Business::select('time_interval')->where('id',Auth::user()->business_id)->first();
                #----------- Slot Book For Employee of system --------#
                TreatmentSlot::where([
                    ['date_id','=',$request->id],
                    ['status','=','Lunch'],
                ])->delete();
                
                #----- if lunch set none ---
                 if($request->lunch == 'none'){
                    $data = 'success';
                    \Log::channel('custom')->info("No Treatment slot has been booked for lunch.",['business_id' => Auth::user()->business_id]);
                    return \Response::json($data);
                }

                #--------------- Create new date for lunch everytime ---------------#
                //----- here 30 is static because lunch time willbe min 30 --//
                $limit = ceil(30/$timeInterval->time_interval);
                $slots = $this->getTimeSlots($request->lunch,$timeInterval->time_interval,$limit);
                $check = $this->checkSlotFree($slots,$request->id);

                if( $check == 0 ){
                    for($i=0; $i < $limit; $i++){
                        $slot = TreatmentSlot::create([
                            'user_id' => 0,
                            'date_id' => $request->id,
                            'business_id'       => Auth::user()->business_id,
                            'status'            => 'Lunch',
                            'time'              => $slots[$i],
                        ]);
                    }
                    $data = 'success';
                    \Log::channel('custom')->info("Treatment slot has been booked for lunch.",['business_id' => Auth::user()->business_id]);

                    //------ Add log in DB for location -----//
                    $type = "log_for_booking_related_details";
                    $txt = "Treatment slot has been booked for lunch. <br> Treatment Date: <b>".$request->_date."</b><br>Time: <b>".$slots[0]."</b>";
                    $this->addLocationLog($type,$txt);
                }
                else{
                    $data = 'exist';
                }
            }
        }
        else{
            \Log::channel('custom')->error("Unexpected error to update date of treatment!",['business_id' => Auth::user()->business_id,'treatment_date_id' => $date->id]);
            $data = 'error';
        }
        return \Response::json($data);
    }

    //---####################################################################//

    public function restoreDateAjax(Request $request){
        abort_unless(\Gate::allows('Date Restore'),403);
        Validator::make($request->all(), [
            'id'     => ['required'],
        ])->validate();
        
        $date = Date::find($request->id);
        $date->is_active = 1;
        if($date->save()){
            TreatmentSlot::where([['date_id',$request->id],['status','Lunch'],['is_active',0]])->update(['is_active' => 1]);
            \Log::channel('custom')->info("Treatment date has been restored.",['business_id' => Auth::user()->business_id]);
            $response = 'success';

            //------ Add log in DB for location -----//
            $type = "log_for_date_related_details";
            $txt = "Treatment date has been restored. <br> Treatment Date: <b>".$date->date."</b><br>Restored By:<b>".Auth::user()->email."</b>";
            $this->addLocationLog($type,$txt);
        }    
        else{
            \Log::channel('custom')->error("Issue to restore date of treatment",['business_id' => Auth::user()->business_id, 'treatment_date_id' => $date->id]);

            $response = 'error';
        }
        return \Response::json($response);    
    }

    //---####################################################################//
    
    public function deleteDateAjax(Request $request){
        abort_unless(\Gate::allows('Date Delete'),403);
        Validator::make($request->all(), [
            'id'            => ['required'],
            'isDelete'      => ['required'],
        ])->validate();

        $date = Date::where(\DB::raw('md5(id)'),$request->id)->first();
        $treatments = TreatmentSlot::where('date_id',$date->id)->where('is_active',1)->where('parent_slot', NULL)->get();
        $ids = $treatments->pluck('id')->toArray();

        if($treatments->count() == 0){
            $date->is_active = 0;
            if($date->save()){
             
                //------ Add log in DB for location -----//
                $type = "log_for_date_related_details";
                $txt = "Date has been deleted. <br> Treatment Date: <b>".$date->date."</b><br>Deleted By:<b>".Auth::user()->email."</b>";
                $this->addLocationLog($type,$txt);
                
                #---- deleted here because no bookings exists
                if($request->isDelete == 'true'){
                    \Log::channel('custom')->warning("Deleting date directly.",['business_id' => Auth::user()->business_id, 'date_id' => $date->id]);
                    $date->delete();
                }

                \Log::channel('custom')->info("Treatment date has been deleted.",['business_id' => Auth::user()->business_id,'user_id' => Auth::user()->id,'user_name' => Auth::user()->name]);
                $response['status'] = 'success';
            }    
            else{
                \Log::channel('custom')->error("Error to delete treatment date.",['business_id' => Auth::user()->business_id, 'treatent_date_id' => $date->id,'user_id' => Auth::user()->id,'user_name' => Auth::user()->name]);
                $response['status'] = 'error';
            }    
        }
        else{
            //------ Add log in DB for location -----//
            $type = "log_for_date_related_details";
            $txt = "Date has been deleted. <br> Treatment Date: <b>".$date->date."</b><br>Deleted By:<b>".Auth::user()->email."</b>";
            $this->addLocationLog($type,$txt);

            $date->is_active = 0;
            $date->save();
            \Log::channel('custom')->warning("Try to delete treatmnet date which have already bookings.",['business_id' => Auth::user()->business_id,'user_id' => Auth::user()->id,'user_name' => Auth::user()->name]);
            $response['status'] = 'exist';
            $response['ids'] = json_encode($ids);
        }
        return \Response::json($response);
    }

    //---############################################################################//
    public function treatmentSlotsList($subdomain,$id=''){
        abort_unless(\Gate::allows('Date Bookings View'),403);
        $settings = Business::find(\Auth::user()->business_id)->Settings;
        $countries = $this->countries();
        return view('treatment.slots',compact('settings','countries','id'));
    }
    //---##########################################################################//
    public function treatmentDeletedBookingsList(){
        abort_unless(\Gate::allows('Deleted Dates Bookings View'),403);
        $dates = Business::find(Auth::user()->business_id)->Dates;
        $dateFormat = Business::find(Auth::user()->business_id)->Settings->where('key','date_format')->first(); 
        $timeFormat = Business::find(Auth::user()->business_id)->Settings->where('key','time_format')->first(); 
        
        return view('treatment.deleted_booking_list',compact('dates','dateFormat','timeFormat'));
    }
    //--##############################################################################//
    public function RestoreTreatmentBookingAjax(Request $request){
        abort_unless(\Gate::allows('Dates Booking Restore'),403);
        Validator::make($request->all(), [
            'id'     => ['required'],
        ])->validate();
        
            $slot = TreatmentSlot::where(\DB::raw('md5(id)') , $request->id)->first();
            $check = TreatmentSlot::where('time',$slot->time)->where('is_active',1)->where('date_id',$slot->date_id)->count();
            $childs = TreatmentSlot::where('parent_slot',$slot->id)->get();
            $brandName = Business::find(Auth::user()->business_id)->Settings->where('key','email_sender_name')->first(); 
            $dateFormat = Business::find(Auth::user()->business_id)->Settings->where('key','date_format')->first(); 
            $smsSetting = Business::find(Auth::user()->business_id)->Settings->where('key','sms_setting')->first();

            $trbookingSms = Business::find(Auth::user()->business_id)->Settings->where('key','t_r_booking_sms')->first();
            $clipUsedSms = Business::find(Auth::user()->business_id)->Settings->where('key','clip_used')->first();

            //----- Check if there is any booking on deleted | start ----//
            if(!$check){                                    
                // -------  for this user's will change language ------
                \App::setLocale($slot->user->language);

                $date = Date::where('id',$slot->date_id)->first();
                //----- Check if date is active ----//
                if( $date->is_active ){
                     //----- Check if there is any booking on deleted times ----//
                    foreach($childs as $child){
                        $c = TreatmentSlot::where('time',$child->time)->where('is_active',1)->where('date_id',$child->date_id)->count();
                        if($c){
                            $response = 'bookingExist';
                            break;
                        }
                    }
                    //---- if no booking exist then restore time ----//
                    if(empty($response)){
                        $clip = $slot->deactiveclip;
                        if($clip != Null){
                            $card = $clip->card;
                            if($card->clips >= $clip->amount ){
                                $card->clips = $card->clips-$clip->amount;
                                if($card->save()){
                                    $clip->is_active = 1;
                                    $clip->save();

                                    //-------- Send Email to user about Clips ----------
                                    $subject =  __('emailtxt.clips_used_subject',['name' => $brandName->value]);
                                    $content =  __('emailtxt.clips_used_txt',['name' => $slot->user->name, 'used' => $clip->amount, 'title' => $card->name, 'expiry' => \Carbon\Carbon::parse($card->expiry_date)->format($dateFormat->value), 'clips' => ($card->clips ?: 0), 'for' => __('card.treatment') ]);
                            
                                    //------ Add log in DB for location -----//
                                    $type = "log_for_card_and_clips";
                                    $txt = "Clip has been cut back with booking restored. <br> User name: <b>".$card->user->name."</b><br>Clips Cut: <b>".$clip->amount."</b>";
                                    $this->addLocationLog($type,$txt);

                                    //------- Send sms notification if active from settings -----
                                    if($clipUsedSms->value == 'true' && $smsSetting->value == 'true'){
                                        $sms =  __('smstxt.clips_used_txt',['name' => $slot->user->name, 'used' => $clip->amount, 'title' => $card->name, 'expiry' => \Carbon\Carbon::parse($card->expiry_date)->format($dateFormat->value), 'clips' => ($card->clips ?: 0), 'for' => __('card.treatment') ]);

                                        $this->smsSendWithCheck($sms,$slot->user->id);
                                    }
                    
                                    if($slot->user->email != ''){
                                        $this->dispatch(new SendEmailJob($slot->user->email,$subject,$content,$brandName->value,$slot->business_id));
                                    }
                                }
                            }else{
                                $clip->delete();
                            }
                        }
                        $slot->is_active = 1;
                        $slot->save();
                        \Log::channel('custom')->info("Deleted booking has been restored.",['business_id' => Auth::user()->business_id, 'booking_id' => $slot->id]);

                        //------ Add log in DB for location -----//
                        $type = "log_for_booking_related_details";
                        $txt = "Deleted booking has been restored. <br> Customer Email: <b>".$slot->user->email."</b><br>Treatment Date & Time: <b>".$date->date.' '.$slot->time."</b>";
                        $this->addLocationLog($type,$txt);

                        foreach($childs as $child){
                            $child->is_active = 1;
                            $child->save();
                        }

                        $homeLink = url('/other-account/'.md5($slot->user->id));

                        //-------- Send Email to user ----------
                        $subject =  __('emailtxt.treatment_bookin_restore_subject',['name' => $brandName->value]);
                        $content =  __('emailtxt.treatment_bookin_restore_txt',['name' => $slot->user->name,'treatment' => $slot->treatment->treatment_name, 'date' => \Carbon\Carbon::parse($date->date)->format($dateFormat->value), 'time' => $slot->time, 'url' => $homeLink ]);
        
                        //------ Send SMS to user ----------
                        if($trbookingSms->value == 'true' && $smsSetting->value == 'true'){
                            $sms =  __('smstxt.treatment_bookin_restore_txt',['name' => $slot->user->name,'treatment' => $slot->treatment->treatment_name, 'date' => \Carbon\Carbon::parse($date->date)->format($dateFormat->value), 'time' => $slot->time,'link'=> url('/')]);

                            //--- send sms notification ---
                            \Log::channel('custom')->info("Sending sms to customer about booking",['business_id' => Auth::user()->business_id, 'sms_text' => $sms]);
                            $this->smsSendWithCheck($sms,$slot->user->id,$date->date,$slot->time);

                        }


                        if($slot->user->email != ''){                        
                            \Log::channel('custom')->info("Sending email to customer about booking restored!",['business_id' => Auth::user()->business_id, 'user_id' => $slot->user->id , 'subject' => $subject, 'email_text' => $content]);
                            $this->dispatch(new SendEmailJob($slot->user->email,$subject,$content,$brandName->value,$slot->business_id));

                            #------ Send email to Therapist about this booking ----//
                            $this->notifyTherapist($date->user->id,$subject,$content,$brandName->value);

                        }else{
                            \Log::channel('custom')->warning("User has no email address.",['business_id' => Auth::user()->business_id, 'booking_id' => $slot->user->id]);
                        }

                        $response = 'success';
                    }
                }
                else{
                    \Log::channel('custom')->warning("Treatment date did not active, so booking can't be restoed!",['business_id' => Auth::user()->business_id, 'date_id' => $date->id]);
                    $response = 'dateNotExist';
                }
            }
            else{
                \Log::channel('custom')->warning("Booking on same time exist so can't restore this date back.",['business_id' => Auth::user()->business_id, 'slot_id' => $slot->id]);
                $response = 'bookingExist';
            }
        return \Response::json($response);
    }
    //---###########################################################################//
    public function getDateData(Request $request){
        Validator::make($request->all(), [
            'dates'     => ['required'],
        ])->validate();

        $interval = Business::find(Auth::user()->business_id);
        $dates = Date::whereIn('id',$request->dates)->get();

        $timeDetails = array();
        

        foreach($dates as $key => $date)
        {
            $bookTime = array();
            $lunchTime = array();
            $breakTime = array();
            $bookingStatus = array();
            foreach($date->treatmentSlots as $slot){

                switch($slot->status){
                    case 'Booked':
                        $bookTime[] = $slot->time;
                        $bookingStatus[$slot->time] = $slot->paymentMethodTitle ? $slot->paymentMethodTitle->title : '';
                        break;
                    case 'Lunch':
                        $lunchTime[] = $slot->time;
                        break;
                    case 'Break':
                        $breakTime[] = $slot->time;
                        break;            
                    default:
                        break;
                }
            }    



            if($date->till == '0:00')
                $date->till = '23:59';

            $startTime = strtotime($date->from); 
            $endTime   = strtotime($date->till);
            $returnTimeFormat = 'G:i';
        
            $current   = time(); 
            $addTime   = strtotime('+'.$interval->time_interval." mins", $current); 
            $diff      = $addTime - $current;
            
            $times = array(); 
            while ($startTime < $endTime) { 

                $t = date($returnTimeFormat, $startTime);

                if(in_array($t,$bookTime)){
                    $timeDetails[$key][$date->id][] = '<div class="col-12 booked">'.$t.'</div>';
                }
                elseif(in_array($t,$lunchTime)){
                    $timeDetails[$key][$date->id][] = '<div class="col-12 lunch">'.$t.'</div>';
                }
                elseif(in_array($t,$breakTime)){
                    $timeDetails[$key][$date->id][] = '<div class="col-12 break">'.$t.'</div>';    
                }else{
                    $timeDetails[$key][$date->id][] = '<div onclick="location.href=`#table-'.$date->id.'`" class="open col-12">'.$t.'</div>';
                }
                // if(in_array($t,$bookTime)){
                //     $timeDetails[$key][$date->id][] = '<div class="col-12 booked '.($bookingStatus[$t] ? \str_replace(' ','',$bookingStatus[$t]) : 'PaymentPending' ).' ">'.$t.'</div>';
                // }
                // elseif(in_array($t,$lunchTime)){
                //     $timeDetails[$key][$date->id][] = '<div class="col-12 lunch">'.$t.'</div>';
                // }
                // elseif(in_array($t,$breakTime)){
                //     $timeDetails[$key][$date->id][] = '<div class="col-12 break">'.$t.'</div>';    
                // }else{
                //     $timeDetails[$key][$date->id][] = '<div onclick="location.href=`#table-'.$date->id.'`" class="open col-12">'.$t.'</div>';
                // }

                $startTime += $diff; 
            } 
        }

        return \Response::json($timeDetails);
    }

    //---#############################################################################################//
    public function BookTimeSlotAjax(Request $request){
        abort_unless(\Gate::allows('Date Book'),403);
        
        $departmentCheck = Business::find(Auth::user()->business_id)->Settings->where('key','department')->first();
        $CPRForInsurance = Business::find(Auth::user()->business_id)->Settings->where('key','cpr_emp_fields_insurance')->first();
        
        #--------------- Check if any slot already booked or not ---------------#
        $timeInterval = Business::find(Auth::user()->business_id);
        $teatmentTime = Treatment::find($request->treatment);
        
        
        if($departmentCheck->value == 'true'){
            Validator::make($request->all(), [
                'department'   => ['integer'],
            ])->validate();
        }

        if($CPRForInsurance->value == 'true' && $teatmentTime->is_insurance == 1){
            Validator::make($request->all(), [
                'insurance_cpr'   => ['required'],
            ])->validate();
        }

        Validator::make($request->all(), [
            'user_id'     => ['required'],
            'date_id'     => ['required'],
            'data_time'   => ['required'],
            'treatment'   => ['required'],
        ])->validate();

                
        if($request->department != 'null'){
            $departmentName = Department::find($request->department);
        }else{
            $departmentName = '';
        }
            
        $user = User::find($request->user_id);
        $bookingCount = $user->bookings->count();

        // -------  for this user's will change language ------
        \App::setLocale($user->language);


        $date = Date::find($request->date_id);
        $setting = Business::find(Auth::user()->business_id)->Settings->where('key','stop_booking')->first();
        $brandName = Business::find(Auth::user()->business_id)->Settings->where('key','email_sender_name')->first(); 
        $dateFormat = Business::find(Auth::user()->business_id)->Settings->where('key','date_format')->first(); 
        $timeFormat = Business::find(Auth::user()->business_id)->Settings->where('key','time_format')->first(); 
        $smsSetting = Business::find(Auth::user()->business_id)->Settings->where('key','sms_setting')->first();
        $tbookingSms = Business::find(Auth::user()->business_id)->Settings->where('key','t_booking_sms')->first();
         
        //-------- Minus hours from treatment time, so that can check it can be booked or not now ---
        $bookTime = explode(':',$request->data_time);
        $bookTime[0] = $bookTime[0]-$setting->value;
        $bookTimeFinal = $bookTime[0].':'.$bookTime[1];

        $treatmentTime = strtotime(\Carbon\Carbon::parse($date->date)->format('Y-m-d').' '.$bookTimeFinal);
        $currentTime = strtotime(\Carbon\Carbon::now()->format('Y-m-d H:i'));

        //------ If date is passed then means booking are made from backward -------
        $treatmentDate = strtotime(\Carbon\Carbon::parse($date->date)->format('Y-m-d'));
        $currentDate = strtotime(\Carbon\Carbon::now()->format('Y-m-d'));

        // if( $treatmentTime >= $currentTime || $treatmentDate < $currentDate ){
        if( true ){

            $limit = ceil($teatmentTime->inter/$timeInterval->time_interval);
            $slots = $this->getTimeSlots($request->data_time,$timeInterval->time_interval,$limit);
            $check = $this->checkSlotFree($slots,$request->date_id);
            $parentSlotID = NULL;

            #--------------- If not then booked ---------------#
            if( $check == 0 ){
                for($i=0; $i < $limit; $i++){

                    $slot = new TreatmentSlot();
                    $slot->user_id      = $request->user_id;
                    if($CPRForInsurance->value == 'true'  && $teatmentTime->is_insurance == 1)
                        $slot->CPR      = $request->insurance_cpr;
                    $slot->date_id      = $request->date_id;
                    $slot->treatment_id = $request->treatment;
                    if($request->department != 'null')
                        $slot->department_id = $request->department;
                    $slot->parent_slot  = $parentSlotID;
                    $slot->business_id  = Auth::user()->business_id;
                    $slot->status       = 'Booked';
                    $slot->time         = $slots[$i];
                    $slot->comment      = $request->comment;
                    $slot->save();

                    \Log::channel('custom')->info("Booking has been made of treatment for customer.",['business_id' => Auth::user()->business_id, 'customer_id' => $user->id]);

                    if($i == 0){
                        $parentSlotID = $slot->id;
                    }
                }

                //------ Send User credentials if not login before and has this fist booking ----
                if($user->is_logged_in == 0){

                    $password = Str::random(8);

                    $url = "https://".$timeInterval->business_name.'.'.config('app.name').'/login';
                    $subject =  __('emailtxt.user_register_email_subject',['name' => $brandName->value]);
                    $content =  __('emailtxt.user_register_email_txt',['name' =>$user->name, 'email' => $user->email, 'url' => $url, 'password' => $password]);

                    if($user->email != ''){
                        \Log::channel('custom')->info("Sending email to customer with login credentials.",['business_id' => $timeInterval->id, 'user_id' => $user->id, 'subject' => $subject , 'email_text' => $content]);
                        $this->dispatch(new SendEmailJob($user->email,$subject,$content,$brandName->value,$slot->business_id));
                    }

                    $user->is_logged_in = 1;
                    $user->password = \Hash::make($password);
                    $user->save();
                }

                //-------- Send Booking Email to user ----------
                $table = $this->activeBookings($user->id,Auth::user()->business_id);
                $subject =  __('emailtxt.treatment_booking_subject',['name' => $brandName->value]);
                $content =  __('emailtxt.treatment_booking_txt',['name' => $user->name, 'treatment' => $teatmentTime->treatment_name, 'date' => \Carbon\Carbon::parse($date->date)->format($dateFormat->value), 'time' => $request->data_time ,'bookingtable' => $table, 'description' => $date->description ?: 'N/A' ]);

                if($user->email != ''){
                    \Log::channel('custom')->info("Sending email to customer about new booking!",['business_id' => Auth::user()->business_id, 'user_id' => $user->id , 'subject' => $subject, 'email_text' => $content]);
                    $this->dispatch(new SendEmailJob($user->email,$subject,$content,$brandName->value,$user->business_id));

                    #------ Send email to Therapist about this booking ----//
                    $this->notifyTherapist($date->user->id,$subject,$content,$brandName->value);
                }

                //------ Add log in DB for location -----//
                $type = "log_for_booking_related_details";
                $txt = "New booking has been made. <br> Customer Email: <b>".$user->email."</b><br>Treatment Date & Time: <b>".$date->date.' '.$request->data_time."</b><br>Booked By:<b>".Auth::user()->email."</b>";
                $this->addLocationLog($type,$txt);

                //------- Send sms notification to user ------
                
                //--- check if business allow to send sms ----
                if($tbookingSms->value == 'true' && $smsSetting->value == 'true'){
                    $sms =  __('smstxt.treatment_booking_txt',['name' => $user->name, 'treatment' => $teatmentTime->treatment_name, 'date' => \Carbon\Carbon::parse($date->date)->format($dateFormat->value), 'time' => $request->data_time, 'link' => url('/') ]);
                    //--- send sms notification ---
                    \Log::channel('custom')->info("Sending sms to customer about new booking!",['business_id' => Auth::user()->business_id, 'user_id' => $user->id, 'sms_text' => $sms]);
                    $this->smsSendWithCheck($sms,$user->id,$date->date,$request->data_time);
                }

                //---- Check survey is on in setting -------//
                $surveySetting = Business::find(Auth::user()->business_id)->Settings->where('key','survey_setting')->first();
                $surveyDuration = Business::find(Auth::user()->business_id)->Settings->where('key','survey_duration')->first();

                if($surveySetting->value == 'true'){
                    $checkToSend = 0;
                    //---- Check if this user received survey email before -------//
                    if(!is_null($user->survey_date)){
                        $surveyDate = \Carbon\Carbon::parse($user->survey_date)->addMonth(($surveyDuration ? $surveyDuration->vlaue : 3))->format('Y-m-d');
                        $today = \Carbon\Carbon::now()->format('Y-m-d');
                        //--- If last survey email send time more then 6 month ---
                        if($surveyDate < $today){
                            $checkToSend = 1;
                        }
                    }
                    else
                        $checkToSend = 1;
                    
                    //----- send survey email to user ----
                    if($checkToSend){
                        $surveySendHour = Business::find(Auth::user()->business_id)->Settings->where('key','survey_send_hours')->first();
                        $dateDate = \Carbon\Carbon::parse($date->date)->format('Y-m-d');
                        $delay = \Carbon\Carbon::parse($dateDate.' '.$request->data_time)->addHours($surveySendHour->value);
                        // $delay = \Carbon\Carbon::now()->addHours($surveySendHour->value);

                        $link = url('/Survey/'.$user->language);
                        $sub = __('emailtxt.survey_email_subject',['name' => $brandName->value]);
                        ;
                        $con =  __('emailtxt.survey_email_txt',['name' => $user->name, 'link' => $link]);
                        ;

                        \Log::channel('custom')->info("Queing survey email.",['business_id' => Auth::user()->business_id, 'user_id' => $user->id , 'subject' => $sub, 'email_text' => $con, 'sending_time' => $delay]);

                        $job = (new SendEmailJob($user->email,$sub,$con,$brandName->value,$user->business_id))->delay($delay);
                        $surveyJob = $this->dispatch($job);
                        
                        $TreatmentSlot = TreatmentSlot::find($parentSlotID);
                        $TreatmentSlot->survey_job_id = $surveyJob;
                        $TreatmentSlot->save();
                        
                        $user->survey_date = date('Y-m-d');
                        $user->save();
                    }
                }    

                //------- Make ICS file for this treatment ---------

                $type = __('treatment.treatment');
                $tName = __('treatment.treatment').': '.$teatmentTime->treatment_name;
                $tDuration = $teatmentTime->time_shown ?: $teatmentTime->inter.'min';
                $tDate = $date->date;
                $tTime = $request->data_time;
                $tTherapist = __('treatment.therapist').': '.$date->user->name;

                $this->createIcsFile($type,$tName,$tDuration,$tDate,$tTime,$tTherapist,$parentSlotID);

                //------- Response variables ---------
                
                $data['status'] = 'success';
                $data['cards'] = $user->treatmentCards;
                $data['slots'] = $limit;
                $data['name'] = $user->name;
                $data['email'] = $user->email;
                $data['number'] = $user->number;
                $data['treatment'] = '<a href="javascript:;" data-id="'.$parentSlotID.'" onclick="getAvailableTreatments(this)">'.$teatmentTime->treatment_name.' ('.($teatmentTime->time_shown ?: $teatmentTime->inter).' min)</a>';
                $data['price'] = $teatmentTime->price == NULL ? 0 : $teatmentTime->price;
                if($departmentName)
                    $data['department'] = $departmentName->name;
                else
                    $data['department'] = '';

                $data['slot_id'] = $parentSlotID;
                $data['bookedTime'] = $slot->created_at->format($dateFormat->value.($timeFormat->value == 12 ? ' h:i:s a' : ' H:i:s'));
                $data['bookingCount'] = $bookingCount;
                $data['userHash'] = md5($user->id);
            }
            #--------------- If YES then trow error ---------------#
            else{
                $data['status'] = 'exist';
            }
        }
        else{
            \Log::channel('custom')->warning("Can't book now because time to book has been passed.",['business_id' => Auth::user()->business_id, 'Stop_booking_befoe_hours' => $setting->value, 'current_time' => \Carbon\Carbon::now()->format('Y-m-d H:i') , 'try_to_book_time' => \Carbon\Carbon::parse($date->date)->format('Y-m-d').' '.$request->data_time]);
            $data['status'] = 'exceeded';
        }

        return json_encode($data);
    }

     //---#############################################################################################//
     public function BookWaitingTimeSlotWebAjax(Request $request){
                     
        Validator::make($request->all(), [
            'name'      => ['required','max:255'],
            'email'     => ['required', 'string', 'email', 'max:255'],
            'number'    => ['max:255'],
            'treatment' => ['required'],
            'dateID'    => ['required'],
            'time'      => ['required'],
        ])->validate();

                
        $business =  Business::where('business_name',session('business_name'))->first();
        $departmentCheck = Business::find($business->id)->Settings->where('key','department')->first();
        $dateFormat = Business::find($business->id)->Settings->where('key','date_format')->first(); 
        $teatment = Treatment::find($request->treatment);
        $brandName = Business::find($business->id)->Settings->where('key','email_sender_name')->first(); 
        
        if($departmentCheck->value == 'true'){
            Validator::make($request->all(), [
                'department'   => ['integer'],
            ])->validate();
        }
        //--------- Check if Code enable for customer or not -------
        $bookingCodeCheck = Business::find($business->id)->Settings->where('key','code_for_booking')->first();

        if($bookingCodeCheck->value == 'true'){
            $bookingCode = Business::find($business->id)->Settings->where('key','code')->first();
            if($request->code != $bookingCode->value){
                \Log::channel('custom')->warning("Booking code not correct!",['business_id' => $business->id]);
                $data['status'] = 'error';
                return json_encode($data);
            }
        }

        $date = Date::find($request->dateID);

        //--------------------------- check for user -----------------------------
        if($request->id == ''){
            if($bookingCodeCheck->value == 'true'){
                $data['status'] = 'not';
                return \Response::json($data);
                exit(); 
            }
            $user = $this->addUser($request->email,$business->id,$request->name,$request->country,$request->number,$request->cprnr,$request->mednr,$brandName->value);
            if( $user == 0 ){
                \Log::channel('custom')->warning("Email already exist!",['business_id' => $business->id]);
                $data['status'] = 'Email Exist';
                return \Response::json($data);
                exit(); 
            }
        }            
        else
            $user = $request->id; 
            

        #--------------- If not then booked ---------------#
        $slot = new TreatmentWaitingSlot();
        $slot->user_id      = $user;
        $slot->date_id      = $request->dateID;
        $slot->treatment_id = $request->treatment;
        if($request->department != 'null')
            $slot->department_id = $request->department;
        $slot->business_id  = $business->id;
        $slot->status       = 'Waiting';
        $slot->time         = '00:00';
        $slot->comment      = $request->comment;
        $slot->save();

        \Log::channel('custom')->info("Customer has been added in waiting list.",['business_id' => $business->id, 'customer_id' => $user, 'date_id' => $request->dateID]);
        
        //------ Add log in DB for location -----//
        $type = "log_for_booking_related_details";
        $txt = "New Waiting list booking has been made. <br> Customer Email: <b>".$request->email."</b><br>Treatment Date & Time: <b>".$date->date." 00:00</b><br>Booked By:<b>Website</b>";
        $this->addLocationLog($type,$txt);
            
        // -------  for this user's will change language ------
        $userID = User::find($user);
        \App::setLocale($userID->language);

        //------- Response variables ---------
        $data['status'] = 'success';  
        $bookinglink = url('/MyTreatmentBookings');
        $data['message'] = __('web.treatment_waiting_booking_txt',['name' => $request->email, 'treatment' => $teatment->treatment_name, 'date' => \Carbon\Carbon::parse($date->date)->format($dateFormat->value), 'time' => '00:00', 'link' => $bookinglink, 'description' => $date->description ]);

        return json_encode($data);
    }

    //---#############################################################################################//
    public function BookWaitingTimeSlotAjax(Request $request){
        abort_unless(\Gate::allows('Date Book'),403);
                
        #--------------- Check if any slot already booked or not ---------------#
        $timeInterval = Business::find(Auth::user()->business_id);
        $teatmentTime = Treatment::find($request->treatment);
        
        Validator::make($request->all(), [
            'user_id'     => ['required'],
            'date_id'     => ['required'],
            'data_time'   => ['required'],
            'treatment'   => ['required'],
        ])->validate();

                
        if($request->department != 'null'){
            $departmentName = Department::find($request->department);
        }else{
            $departmentName = '';
        }
            
        $user = User::find($request->user_id);
        // -------  for this user's will change language ------
        \App::setLocale($user->language);

        $date = Date::find($request->date_id);
        $dateFormat = Business::find(Auth::user()->business_id)->Settings->where('key','date_format')->first(); 
        $timeFormat = Business::find(Auth::user()->business_id)->Settings->where('key','time_format')->first(); 
         
        if( true ){
            #--------------- If not then booked ---------------#

            $slot = new TreatmentWaitingSlot();
            $slot->user_id      = $request->user_id;
            $slot->date_id      = $request->date_id;
            $slot->treatment_id = $request->treatment;
            if($request->department != 'null')
                $slot->department_id = $request->department;
            $slot->business_id  = Auth::user()->business_id;
            $slot->status       = 'Waiting';
            $slot->time         = '00:00';
            $slot->comment      = $request->comment;
            $slot->save();

            \Log::channel('custom')->info("Customer has been added in waiting list.",['business_id' => Auth::user()->business_id, 'customer_id' => $user->id, 'date_id' => $request->date_id]);
            
            //------ Add log in DB for location -----//
            // $type = "log_for_booking_related_details";
            // $txt = "New booking has been made. <br> Customer Email: <b>".$user->email."</b><br>Treatment Date & Time: <b>".$date->date.' '.$request->data_time."</b><br>Booked By:<b>".Auth::user()->email."</b>";
            // $this->addLocationLog($type,$txt);
                
            //------- Response variables ---------
            
            $data['status'] = 'success';
            $data['cards'] = $user->treatmentCards;
            $data['name'] = $user->name;
            $data['email'] = $user->email;
            $data['number'] = $user->number;
            $data['treatment'] = $teatmentTime->treatment_name.' ('.($teatmentTime->time_shown ?: $teatmentTime->inter).' min)';
            $data['price'] = $teatmentTime->price == NULL ? 0 : $teatmentTime->price;
            if($departmentName)
                $data['department'] = $departmentName->name;
            else
                $data['department'] = '';

            $data['slot_id'] = $slot->id;
            $data['bookedTime'] = $slot->created_at->format($dateFormat->value.($timeFormat->value == 12 ? ' h:i:s a' : ' H:i:s'));
            $data['userHash'] = md5($user->id);

            $data['treatments'] = Date::find($request->date_id)->treatments->where('is_active',1);
            $data['departments']  = Business::find(Auth::user()->business_id)->Departments->where('is_active',1);
        }
        else{
            \Log::channel('custom')->warning("Can't book now because time to book has been passed.",['business_id' => Auth::user()->business_id, 'current_time' => \Carbon\Carbon::now()->format('Y-m-d H:i') , 'try_to_book_time' => \Carbon\Carbon::parse($date->date)->format('Y-m-d').' '.$request->data_time]);
            $data['status'] = 'exceeded';
        }

        return json_encode($data);
    }

    //---#############################################################################################//
    public function DeleteBookWaitingTimeSlotAjax(Request $request){
        abort_unless(\Gate::allows('Date Book'),403);
                        
        Validator::make($request->all(), [
            'id'     => ['required'],
        ])->validate();

        $slot = TreatmentWaitingSlot::find($request->id);
        if( $slot != NULL ){
            \Log::channel('custom')->warning("User has been removed from waiting list.",['business_id' => Auth::user()->business_id, 'user_id' => $slot->user_id]);
            $slot->delete();
            $data['status'] = 'success';
        }
        else{
            \Log::channel('custom')->warning("There is issue to removed customer from waiting list.",['business_id' => Auth::user()->business_id,]);
            $data['status'] = 'error';
        }

        return json_encode($data);
    }

    //---#############################################################################################//
    public function BookTimeSlotForWebAjax(Request $request){
        
        Validator::make($request->all(), [
            'name'      => ['required','max:255'],
            'email'     => ['required', 'string', 'email', 'max:255'],
            'number'    => ['max:255'],
            'treatment' => ['required'],
            'dateID'    => ['required'],
            'time'      => ['required'],
        ])->validate();

        $business =  Business::where('business_name',session('business_name'))->first();
        $brandName = Business::find($business->id)->Settings->where('key','email_sender_name')->first(); 
        $dateFormat = Business::find($business->id)->Settings->where('key','date_format')->first(); 
        $departmentCheck = Business::find($business->id)->Settings->where('key','department')->first();
        
        if($departmentCheck->value == 'true'){
            Validator::make($request->all(), [
                'department'   => ['integer'],
            ])->validate();
        }
        //--------- Check if Code enable for customer or not -------
        $bookingCodeCheck = Business::find($business->id)->Settings->where('key','code_for_booking')->first();

        if($bookingCodeCheck->value == 'true'){
            $bookingCode = Business::find($business->id)->Settings->where('key','code')->first();
            if($request->code != $bookingCode->value){
                \Log::channel('custom')->warning("Booking code not correct!",['business_id' => $business->id]);
                $data['status'] = 'error';
                return json_encode($data);
            }
        }

        $date = Date::find($request->dateID);
        $setting = Business::find($business->id)->Settings->where('key','stop_booking')->first();

        //-------- Time check --------
        //-------- Minus hours from treatment time, so that can check it can be booked or not now -----
        $treatmentTime = strtotime(\Carbon\Carbon::parse($date->date)->format('Y-m-d').' '.$request->time);
        $currentTime = strtotime(\Carbon\Carbon::now()->addHour($setting->value)->format('Y-m-d H:i'));

        if( $treatmentTime >= $currentTime ){
            //--------------------------- check for user -----------------------------
            if($request->id == ''){
                if($bookingCodeCheck->value == 'true'){
                    $data['status'] = 'not';
                    return \Response::json($data);
                    exit(); 
                }
                $userID = $this->addUser($request->email,$business->id,$request->name,$request->country,$request->number,$request->cprnr,$request->mednr,$brandName->value);
                if( $userID == 0 ){
                    \Log::channel('custom')->warning("Email already exist!",['business_id' => $business->id]);
                    $data['status'] = 'Email Exist';
                    return \Response::json($data);
                    exit(); 
                }
            }            
            else
                $userID = $request->id; 

                //-------------- Check if limit set of active booking and monthly bookings -----
                $bookingsPerMonth = Business::find($business->id)->Settings->where('key','max_bookings_month')->first();
                $activeBookings = Business::find($business->id)->Settings->where('key','max_active_bookings')->first();

                //------- Active Booking Limit -----
                if($activeBookings->value >= 0){

                    //----- Get active dates ------
                    $activeDates = Business::find($business->id)->Dates->where('date','>',\Carbon\Carbon::today())->where('is_active',1)->pluck('id');
                    //----- Get treatments that not passed yet ------
                    $activeBooked = TreatmentSlot::where([['user_id',$userID],['is_active',1],['parent_slot',Null]])->whereIn('date_id',$activeDates)->count();                    

                    //---- If active booking limit reached then not allowd to book more -----
                    if( $activeBookings->value <= $activeBooked ){
                        \Log::channel('custom')->warning("Booking limit reached!",['business_id' => $business->id]);
                        $data['status'] = 'active_limit';
                        return \Response::json($data);
                        exit();
                    }else{

                        $dbServer = \DB::connection()->getPdo()->getAttribute(\PDO::ATTR_DRIVER_NAME);
                        $dateType = date("Y-m-d");
                        $query = "concat('".date('Y-m-d')."', ' ', time) > NOW()";
                        if($dbServer != 'mysql'){
							$timeF = '"TIME"';
                            $dateType = \Carbon\Carbon::today();
                            $query = "('".date("Y-m-d")."' || ' ' || ".$timeF.") > SYSDATE";
                        }
                        //----- Get active dates ------
                        $activeDatesToday = Business::find($business->id)->Dates->where('date','=',$dateType)->where('is_active',1)->pluck('id');
                        //----- Get treatments that not passed yet ------

                        $activeBookedToday = TreatmentSlot::where([['user_id',$userID],['is_active',1],['parent_slot',Null]])->whereIn('date_id',$activeDatesToday)->whereRaw($query)->count(); 

                        $activeBookedToday += $activeBooked;

                        if( $activeBookings->value <= $activeBookedToday ){
                            \Log::channel('custom')->warning("Booking limit reached!",['business_id' => $business->id]);
                            $data['status'] = 'active_limit';
                            return \Response::json($data);
                            exit();
                        }
                    }
                       
        
                }

                //----- Monthly Booking Limit -----
                if($bookingsPerMonth->value >= 0){

                    //----- Get current month dates ------
                    $timestamp = strtotime($date->date);
                    $month = date('m', $timestamp);
                    $year = date('Y', $timestamp);

                    $thisMonthDates =  \DB::table('dates')
                    ->whereMonth('date', $month)
                    ->whereYear('date', $year)
                    ->where('business_id',$business->id)
                    ->pluck('id');

                    $monthlyBooked = TreatmentSlot::where([['user_id',$userID],['is_active',1],['parent_slot',Null]])->whereIn('date_id',$thisMonthDates)->count();
                    //---- If monthly booking limit reached then not allowd to book more -----
                    if( $bookingsPerMonth->value <= $monthlyBooked ){
                        \Log::channel('custom')->warning("Booking Monthly limit reached!",['business_id' => $business->id]);
                        $data['status'] = 'monthly_limit';
                        return \Response::json($data);
                        exit();
                    }    
                }
                
                #--------------- Check if any slot already booked or not ---------------#
                $teatmentTime = Treatment::find($request->treatment);
                $user = User::find($userID);
                $bookingCount = $user->bookings->count();
        
                $limit = ceil($teatmentTime->inter/$business->time_interval);
                $slots = $this->getTimeSlots($request->time,$business->time_interval,$limit);
                $check = $this->checkSlotFree($slots,$request->dateID);
                $parentSlotID = NULL;
        
                #--------------- If not then booked ---------------#
                if( $check == 0 ){
                    for($i=0; $i < $limit; $i++){
        
                        $slot = new TreatmentSlot();
                        $slot->user_id      = $userID;
                        $slot->CPR          = $request->CPRBooking != 'undefined' ? $request->CPRBooking : NULL;
                        $slot->date_id      = $request->dateID;
                        $slot->treatment_id = $request->treatment;
                        if($request->department != 'null')
                            $slot->department_id = $request->department;
                        $slot->parent_slot  = $parentSlotID;
                        $slot->business_id  = $business->id;
                        $slot->status       = 'Booked';
                        $slot->time         = $slots[$i];
                        $slot->comment      = $request->comment;
                        $slot->save();
                        
                        \Log::channel('custom')->info("Booking has been made of treatment from website.",['business_id' => $business->id, 'customer_id' => $user->id]);

                        if($i == 0){
                            $parentSlotID = $slot->id;
                        }
                    }

                    //-------- Send Email to user ----------
                    
                    $email = User::find($userID);
                     // -------  for this user's will change language ------
                    \App::setLocale($email->language);
                    $table = $this->activeBookings($email->id,$business->id);
                    $subject =  __('emailtxt.treatment_booking_subject',['name' => $brandName->value]);
                    $content =  __('emailtxt.treatment_booking_txt',['name' => $email->name, 'treatment' => $teatmentTime->treatment_name, 'date' => \Carbon\Carbon::parse($date->date)->format($dateFormat->value), 'time' => $request->time, 'bookingtable' => $table, 'description' => $date->description ?: 'N/A' ]);
    
                    if($email->email != ''){
                        \Log::channel('custom')->info("Sending booking email to customer!",['business_id' => $business->id, 'user_id' => $email->id , 'subject' => $subject, 'email_text' => $content]);
                        $this->dispatch(new SendEmailJob($email->email,$subject,$content,$brandName->value,$email->business_id));

                        #------ Send email to Therapist about this booking ----//
                        $this->notifyTherapist($date->user->id,$subject,$content,$brandName->value);

                    }
                    else{
                        \Log::channel('custom')->warning("User did not have email address.",['business_id' => $business->id, 'user_id' => $email->id]);
                    }
                
                    //------ Add log in DB for location -----//
                    $type = "log_for_booking_related_details";
                    $txt = "Treatment booking has been made by Website. <br> Customer Email: <b>".$email->email."</b><br>Treatment Date & Time: <b>".$date->date.' '.$request->time."</b>";
                    $this->addLocationLog($type,$txt);


                    //------- Send sms notification to user ------        
                    //--- check if business allow to send sms ----
                    $smsSetting = Business::find($business->id)->Settings->where('key','sms_setting')->first();
                    $tbookingSms = Business::find($business->id)->Settings->where('key','t_booking_sms')->first();
                    if($tbookingSms->value == 'true' && $smsSetting->value == 'true'){
                        $sms =  __('smstxt.treatment_booking_txt',['name' => $email->name, 'treatment' => $teatmentTime->treatment_name, 'date' => \Carbon\Carbon::parse($date->date)->format($dateFormat->value), 'time' => $request->time, 'link' => url('/')]);
                        //--- send sms notification ---
                        \Log::channel('custom')->info("Sending booking sms to customer!",['business_id' => $business->id, 'user_id' => $email->id , 'sms_text' => $sms]);
                        $this->smsSendWithCheck($sms,$email->id,$date->date,$request->time);
                    }


                    //---- Check survey is on in setting -------//
                    $surveySetting = Business::find($email->business_id)->Settings->where('key','survey_setting')->first();
                    $surveyDuration = Business::find($email->business_id)->Settings->where('key','survey_duration')->first();

                    if($surveySetting->value == 'true'){
                        $checkToSend = 0;
                        //---- Check if this user received survey email before -------//
                        if(!is_null($email->survey_date)){
                            $surveyDate = \Carbon\Carbon::parse($email->survey_date)->addMonth(($surveyDuration ? $surveyDuration->vlaue : 3))->format('Y-m-d');
                            $today = \Carbon\Carbon::now()->format('Y-m-d');
                            //--- If last survey email send time more then 6 month ---
                            if($surveyDate < $today){
                                $checkToSend = 1;
                            }
                        }
                        else
                            $checkToSend = 1;
                        
                        //----- send survey email to user ----
                        if($checkToSend){
                            $surveySendHour = Business::find($email->business_id)->Settings->where('key','survey_send_hours')->first();
                            $dateDate = \Carbon\Carbon::parse($date->date)->format('Y-m-d');
                            $delay = \Carbon\Carbon::parse($dateDate.' '.$request->time)->addHours($surveySendHour->value);
                            // $delay = \Carbon\Carbon::now()->addHours($surveySendHour->value);

                            $link = url('/'."Survey/".$email->language);
                            $sub = __('emailtxt.survey_email_subject',['name' => $brandName->value]);
                            ;
                            $con =  __('emailtxt.survey_email_txt',['name' => $email->name, 'link' => $link]);
                            ;
    
                            $job = (new SendEmailJob($email->email,$sub,$con,$brandName->value,$email->business_id))->delay($delay);

                            \Log::channel('custom')->info("Queing survey email.",['business_id' => $email->business_id, 'user_id' => $user->id , 'subject' => $sub, 'email_text' => $con, 'sending_time' => $delay]);
                            
                            $surveyJob = $this->dispatch($job);

                            $TreatmentSlot = TreatmentSlot::find($parentSlotID);
                            $TreatmentSlot->survey_job_id = $surveyJob;
                            $TreatmentSlot->save();

                            $email->survey_date = date('Y-m-d');
                            $email->save();                        
                        }
                    } 

                    //------- Make ICS file for this treatment ---------
                    $type = __('treatment.treatment');
                    $tName = __('treatment.treatment').': '.$teatmentTime->treatment_name;
                    $tDuration = $teatmentTime->time_shown ?: $teatmentTime->inter.'min';
                    $tDate = $date->date;
                    $tTime = $request->time;
                    $tTherapist = __('treatment.therapist').': '.$date->user->name;
    
                    $this->createIcsFile($type,$tName,$tDuration,$tDate,$tTime,$tTherapist,$parentSlotID);
    

                    //------- Response variables ---------
                    \App::setLocale(session()->get('locale'));
                    $bookinglink = url('/MyTreatmentBookings');
                    $data['message'] = __('web.treatment_booking_txt',['name' => $email->name, 'treatment' => $teatmentTime->treatment_name, 'date' => \Carbon\Carbon::parse($date->date)->format($dateFormat->value), 'time' => $request->time, 'link' => $bookinglink, 'description' => $date->description ?: 'N/A' ]);
                    $data['status'] = 'success';
                }
                #--------------- If YES then trow error ---------------#
                else{
                    $data['status'] = 'exist';
                }
        }
        else{
            \Log::channel('custom')->warning("Can't book now because time to book has been passed.",['business_id' => $business->id, 'Stop_booking_befoe_hours' => $setting->value, 'current_time' => \Carbon\Carbon::now()->format('Y-m-d H:i') , 'try_to_book_time' => \Carbon\Carbon::parse($date->date)->format('Y-m-d').' '.$request->time]);
            $data['status'] = 'exceeded';
        }
        return json_encode($data);
    }

    //--####################################################################################--//
    public function BookingBackward(){
        abort_unless(\Gate::allows('Date Past Bookings View'),403);
        $dates = Business::find(Auth::user()->business_id)->Dates->where('date','<',date('Y-m-d'))->where('is_active',1);
        $customers = Business::find(Auth::user()->business_id)->users->where('role','Customer')->where('is_active',1);
        $settings = Business::find(\Auth::user()->business_id)->Settings;
        $countries = $this->countries();

        return view('treatment.backward_bookings',compact('dates','customers','settings','countries'));
    }
    //---#############################################################################################//
    public function DeleteBookingAjax(Request $request){
        if( Auth::user()->role != 'Customer' ){
            abort_unless(\Gate::allows('Date Booking Delete'),403);
        }
        Validator::make($request->all(), [
            'id'     => ['required'],
        ])->validate();
        
        $parentSlot = TreatmentSlot::find($request->id);
        $nextSlots  = TreatmentSlot::where('parent_slot',$request->id)->count();
        $treatments = Date::find($parentSlot->date_id)->treatments->where('is_active',1);
        $departments = Business::find(Auth::user()->business_id)->Departments->where('is_active',1);
        $timeInterval = Business::find(Auth::user()->business_id);
        $date = Date::find($parentSlot->date_id);
        $setting = Business::find(Auth::user()->business_id)->Settings->where('key','stop_cancellation')->first();
        $brandName = Business::find(Auth::user()->business_id)->Settings->where('key','email_sender_name')->first();
        $dateFormat = Business::find(Auth::user()->business_id)->Settings->where('key','date_format')->first(); 
        
        $smsSetting = Business::find(Auth::user()->business_id)->Settings->where('key','sms_setting')->first();
        $tDeleteSms = Business::find(Auth::user()->business_id)->Settings->where('key','t_d_booking_sms')->first();
        $clipRestoreSms = Business::find(Auth::user()->business_id)->Settings->where('key','clip_restore')->first();

        //-------- Minus hours from treatment time, so that can check it can be cancel or not now ---
        // $cancelationTime = explode(':',$parentSlot->time);
        // $cancelationTime[0] = $cancelationTime[0]-$setting->value;
        // $cancelationTimeFinal = $cancelationTime[0].':'.$cancelationTime[1];

        // $treatmentTime = strtotime(\Carbon\Carbon::parse($date->date)->format('Y-m-d').' '.$cancelationTimeFinal);
        $originalTime = \Carbon\Carbon::parse($date->date)->format('Y-m-d').' '.$parentSlot->time;
        $treatmentTime = strtotime(\Carbon\Carbon::parse($originalTime)->subHours($setting->value)->format('Y-m-d H:i'));
        $currentTime = strtotime(\Carbon\Carbon::now()->format('Y-m-d H:i'));

        // -------  for this user's will change language ------
        if($parentSlot->status != 'Break' && $parentSlot->status != 'Lunch')
            \App::setLocale($parentSlot->user->language);

        if( $treatmentTime >= $currentTime || $parentSlot->status == 'Break' || Auth::user()->role != 'Customer' ){
        // if( true ){
                    //----------- Delete clip booking ---------//
            $clip = UsedClip::where('treatment_slot_id',$request->id)->where('is_active',1)->first();

            if($clip != Null){
                Card::find($clip->card_id)->increment('clips',$clip->amount);
                $clip->is_active = 0;
                $clip->save();

                 //------ Add log in DB for location -----//
                $type = "log_for_card_and_clips";
                $txt = "Clips added back in card. <br>Customer Email: <b>".$parentSlot->user->email."</b><br>Clips: <b>".$clip->amount."</b>";
                $this->addLocationLog($type,$txt);
                
                $data['cardID'] = $clip->card_id;
                $data['balance'] = Card::find($clip->card_id)->clips;

                //-------- Send Email to user about Clips ----------
                $subject =  __('emailtxt.clips_restore_subject',['name' => $brandName->value]);
                $content =  __('emailtxt.clips_restore_txt',['name' => $parentSlot->user->name, 'amount' => $clip->amount , 'title' => $clip->card->name, 'expiry' => \Carbon\Carbon::parse($clip->card->expiry_date)->format($dateFormat->value), 'clips' => ($clip->card->clips ?: 0), 'for' => ($clip->card->type == 2 ? __('card.event') : __('card.treatment') )  ]);

                //------- Send sms notification to user ------     
                //--- check if business allow to send sms ----
                if($clipRestoreSms->value == 'true' && $smsSetting->value == 'true'){
                    $sms = __('smstxt.clips_restore_txt',['name' => $parentSlot->user->name, 'amount' => $clip->amount , 'title' => $clip->card->name, 'expiry' => \Carbon\Carbon::parse($clip->card->expiry_date)->format($dateFormat->value), 'clips' => ($clip->card->clips ?: 0), 'for' => ($clip->card->type == 2 ? __('card.event') : __('card.treatment') )  ]);

                    //--- send sms notification ---
                    \Log::channel('custom')->info("Sending sms to customer about clips restored.",['business_id' => Auth::user()->business_id, 'user_id' => $parentSlot->user->id, 'sms_text' => $sms]);
                    $this->smsSendWithCheck($sms,$parentSlot->user->id);

                }

                if($parentSlot->user->email != ''){
                    \Log::channel('custom')->info("Sending email to customer about clips restored.",['business_id' => Auth::user()->business_id, 'user_id' => $parentSlot->user->id, 'subject' => $subject, 'email_text' => $content]);
                    $this->dispatch(new SendEmailJob($parentSlot->user->email,$subject,$content,$brandName->value,$parentSlot->business_id));
                }
                else{
                    \Log::channel('custom')->warning("User did not have email address.",['business_id' => Auth::user()->business_id, 'user_id' => $parentSlot->user->id]);
                }
            }

            $slots = TreatmentSlot::where('id',$request->id)->orWhere('parent_slot', $request->id)->get();
            foreach($slots as $slot){
                $slot->is_active = 0;
                $slot->save();
            }
            
            //-------------- Delete Suvey job against this booking -----------//
            if($parentSlot->survey_job_id != NULL){
                \DB::table('jobs')->where('id',$parentSlot->survey_job_id)->delete();
                $thisuser = User::find($parentSlot->user->id);
                $thisuser->survey_date = NULL;
                $thisuser->save();
            }

            // $minSlots = 100;
            // foreach ($treatments as $treatment){
            //     if($minSlots > ceil($treatment->inter/$interval)){
            //         $minSlots = ceil($treatment->inter/$interval);
            //         $minInter = $treatment->inter;
            //     }
            // }

            $childSlotsTime = $this->getTimeSlots($parentSlot->time,$timeInterval->time_interval,$nextSlots+1);
            if($parentSlot->status == 'Break'){
                $parentSlot->delete();
                \Log::channel('custom')->info("Break has been deleted.",['business_id' => Auth::user()->business_id,'user_id' => Auth::user()->id,'user_name' => Auth::user()->name]);

                //------ Add log in DB for location -----//
                $type = "log_for_booking_related_details";
                $txt = "Break has been deleted. <br> Treatment Date: <b>".$date->date."</b><br>Slot Time: <b>".$parentSlot->time."</b>";
                $this->addLocationLog($type,$txt);
            }
            else{
                \Log::channel('custom')->info("Booking has been deleted of treatment for customer.",['business_id' => Auth::user()->business_id, 'customer_id' => $parentSlot->user->id, 'user_who_delete_booking' => Auth::user()->id, 'user_who_delete_booking_name' => Auth::user()->name]);
            }

            //------- Check if this slot book for any customer not for lunch or break -----
            if($parentSlot->user_id > 0){
                
                if($date->waiting_list == 1)
                    $this->notifyWaitingList($parentSlot->date_id);

                //------ Add log in DB for location -----//
                $type = "log_for_booking_related_details";
                $txt = "Treatment booking has been deleted. <br> Customer Email: <b>".$parentSlot->user->email."</b><br>Treatment Date & Time: <b>".$date->date.' '.$parentSlot->time."</b><br>Deleted By:<b>".Auth::user()->email."</b>";
                $this->addLocationLog($type,$txt);

                //-------- Send Email to user ----------
                $homeLink = url('/booking');
                
                $subject =  __('emailtxt.treatment_booking_cancel_subject',['name' => $brandName->value]);
                $content =  __('emailtxt.treatment_booking_cancel_txt',['name' => $parentSlot->user->name,'treatment' => $parentSlot->treatment->treatment_name, 'date' => \Carbon\Carbon::parse($date->date)->format($dateFormat->value), 'time' => $parentSlot->time, 'homeLink' => $homeLink ]);

                //------- Send sms notification to user ------     
                //--- check if business allow to send sms ----
                if($tDeleteSms->value == 'true' && $smsSetting->value == 'true'){
                    $sms =  __('smstxt.treatment_booking_cancel_txt',['name' => $parentSlot->user->name,'treatment' => $parentSlot->treatment->treatment_name, 'date' => \Carbon\Carbon::parse($date->date)->format($dateFormat->value), 'time' => $parentSlot->time, 'link' => url('/') ]);

                    //--- send sms notification ---
                    \Log::channel('custom')->info("Sending sms to customer about treatment booking canceled.",['business_id' => Auth::user()->business_id, 'user_id' => $parentSlot->user->id, 'sms_text' => $sms]);
                    $this->smsSendWithCheck($sms,$parentSlot->user->id,$date->date,$parentSlot->time);
                }

                if($parentSlot->user->email != ''){
                    \Log::channel('custom')->info("Sending email to customer treatment booking cancel.",['business_id' => Auth::user()->business_id, 'user_id' => $parentSlot->user->id, 'subject' => $subject, 'email_text' => $content]);
                    $this->dispatch(new SendEmailJob($parentSlot->user->email,$subject,$content,$brandName->value,$parentSlot->business_id));

                    #------ Send email to Therapist about this booking cancelation ----//
                    $this->notifyTherapist($date->user->id,$subject,$content,$brandName->value);

                }
                else{
                    \Log::channel('custom')->warning("User did not have email address.",['business_id' => Auth::user()->business_id, 'user_id' => $parentSlot->user->id]);
                }
            } 

            $data['status'] = 'success';
            $data['date_id'] = $parentSlot->date_id;
            $data['nextSlots'] = $nextSlots;
            $data['treatments'] = $treatments;
            $data['departments'] = $departments;
            $data['childSlotsTime'] = $childSlotsTime;

            //------ Delete these bookings if selected from setting ------//
            if(isset($request->isDelete) && $request->isDelete == 'true'){
                foreach($slots as $slot){
                    \Log::channel('custom')->warning("Deleting booking directly.",['business_id' => Auth::user()->business_id, 'booking_id' => $slot->id]);
                    $slot->delete();
                }
            }

        }
        else
        {
            $data['status'] = 'exceeded';
            \Log::channel('custom')->warning("Can't delete booking now because time to delete has been passed.",['business_id' => Auth::user()->business_id, 'Stop_booking_befoe_hours' => $setting->value, 'current_time' => \Carbon\Carbon::now()->format('Y-m-d H:i') , 'try_to_book_time' => \Carbon\Carbon::parse($date->date)->format('Y-m-d').' '.$parentSlot->time]);
        }
        return json_encode($data);
        
    }

    //---##########################################################################################//
    public function AddBreakAjax(Request $request){
        abort_unless(\Gate::allows('Date Time Close'),403);
        
        Validator::make($request->all(), [
            'dateID'     => ['required'],
            'time'     => ['required'],
        ])->validate();

        $data = array();
        $check = TreatmentSlot::where('date_id',$request->dateID)->where('time',$request->time)->where('is_active',1)->count();

        if($check == 0){
            $slot = new TreatmentSlot();
            $slot->user_id = 0;
            $slot->business_id = Auth::user()->business_id;
            $slot->date_id = $request->dateID; 
            $slot->time = $request->time; 
            $slot->status = 'Break'; 

            if($slot->save()){
                \Log::channel('custom')->info("Add break in treatment date.",['business_id' => Auth::user()->business_id,'date_id' => $request->dateID]);

                //------ Add log in DB for location -----//
                $type = "log_for_booking_related_details";
                $txt = "Add break in treatment date.<br> Date: <b>".$slot->date."</b>";
                $this->addLocationLog($type,$txt);

                $data['status'] = 'success';
                $data['slot_id'] = $slot->id;
            }
            else{
                \Log::channel('custom')->error("issue to Add break in treatment date.",['business_id' => Auth::user()->business_id,'date_id' => $request->dateID]);
                $data['status'] = 'error';
            }
        }
        else{
            \Log::channel('custom')->warning("Booking exist on this date so cannot add break at this time.",['business_id' => Auth::user()->business_id,'date_id' => $request->dateID]);
            $data['status'] = 'error';
        }
        return json_encode($data);

    }

    //--#######################################################################################--//
    public function updatePaymentPartAjax(Request $request){
        abort_unless(\Gate::allows('Journal Payment/Treatment Update'),403);
        Validator::make($request->all(), [
            'id'     => ['required'],
        ])->validate();

        $slot = TreatmentSlot::find($request->id);
        $slot->payment_method_id = $request->payment ?: NULL;
        $slot->treatment_part_id = $request->part ?: NULL;
        
        if($slot->save())
        {
            \Log::channel('custom')->info("Update slot payment methord and treatment part.",['business_id' => Auth::user()->business_id,'slot_id' => $slot->id]);
            $data = 'success';

            //------ Add log in DB for location -----//
            $type = "log_for_journal";
            $txt = "Update slot payment methord and treatment part.<br>Updated By:<b>".Auth::user()->email."</b>";
            $this->addLocationLog($type,$txt);
        }
        else
        {   
            \Log::channel('custom')->error("Eror to Update slot payment methord and treatment part.",['business_id' => Auth::user()->business_id,'slot_id' => $slot->id]);         
            $data['status'] = 'error';
        }        
        return json_encode($data);
    }

    //--######################################################--//
    public function dateUpdateNotification($id,$changes,$changesDone){

        if(empty($changes)){
            \Log::channel('custom')->info("Date update email not sent because no change happend");
            return true;
        }

        $date = Date::find($id);
        $users = TreatmentSlot::where('date_id',$id)->where('is_active',1)->where('user_id','!=',0)->groupBy('user_id')->pluck('user_id');

        $brandName = Business::find($date->business_id)->Settings->where('key','email_sender_name')->first(); 
        $dateFormat = Business::find($date->business_id)->Settings->where('key','date_format')->first(); 
        $dateUpdateEmail = Business::find($date->business_id)->Settings->where('key','date_update_email')->first(); 
        
        //----- IF off from settings
        if($dateUpdateEmail->value  == 'false'){
            \Log::channel('custom')->info("Date update email not sent because off from settings");
            return true;
        }

        $instructor = "<a href='".url('/showUser/'.md5($date->user->id))."'>".$date->user->name."</a>";

        $subject = __('emailtxt.date_update_subject',['name' => $brandName->value]);
        foreach($users as $user){            
            $udata = User::find($user);
            // -------  for this user's will change language ------
            $link = url('/other-account/'.md5($udata->id));
            \App::setLocale($udata->language);
            
            $cc = array();
            foreach($changes as $key => $c){
                $cc[$key] = __('treatment.'.$c).' '.$changesDone[$c]['before'].' '.__('keywords.to').' '.$changesDone[$c]['after'];
            }

            $content = __('emailtxt.date_update_txt',['name' => $udata->name,'date' => \Carbon\Carbon::parse($date->date)->format($dateFormat->value), 'time' => $date->from.' - '.$date->till, 'link' => $link, 'instructor' => $instructor, 'changes' => implode(',',$cc) ]);

            if($udata->email != ''){
                \Log::channel('custom')->info("Sending email to customer about teatment date update.",['business_id' => Auth::user()->business_id, 'user_id' => $udata->id, 'subject' => $subject, 'email_text' => $content]);
                $this->dispatch(new SendEmailJob($udata->email,$subject,$content,$brandName->value,$udata->business_id));
            }
        }
    }

 //----------- Common Functions -----------------
//---#############################################################################################//

    public function getTimeSlots($start,$inteval,$end){

        $startTime = strtotime($start); 
        $returnTimeFormat = 'G:i';

        $current   = time(); 
        $addTime   = strtotime('+'.$inteval.' mins', $current); 
        $diff      = $addTime - $current;

        $times = array(); 
        for($j=1; $j < $end; $j++) { 
            $times[] = date($returnTimeFormat, $startTime); 
            $startTime += $diff; 
        } 
        $times[] = date($returnTimeFormat, $startTime);
        return $times;
    }

    //---#############################################################################################//

    public function checkSlotFree($time,$date){
        $business =  Business::where('business_name',session('business_name'))->first();
        $check = TreatmentSlot::whereIn('time',$time)->where('business_id',$business->id)->where('date_id',$date)->where('is_active',1)->count();
        $day = Date::find($date);

        if($day->till == '0:00')
            $day->till = "23:59";
        //---------- Check if last time passed or not ---------
        if( $check == 0){
            foreach( $time as $key => $val ){
                if ( strtotime($val) >= strtotime($day->till)) {
                    $check = 1;
                }
                if(strtotime($day->from) > strtotime($val)){
                    $check = 1;
                }
            }
        }
        return $check;
    }

    //---#############################################################################################//

    public function getUserDataAjax(Request $request){
        Validator::make($request->all(), [
            'search'     => ['required'],
        ])->validate();
        $search = $request->search;
        
        $users = Business::find(Auth::user()->business_id)->users->where('is_active',1)->filter(function($user) use ($search) {
            return strstr(strtolower($user->name), strtolower($search)) ||
                strstr(strtolower($user->email), strtolower($search)) ||
                strstr(strtolower($user->number), strtolower($search));
        });
        return json_encode($users->all());
    }

    public function getUserDataWithNumberAjax(Request $request){
        Validator::make($request->all(), [
            'search'     => ['required'],
        ])->validate();
        $search = $request->search;
        $business = Business::where('business_name',session('business_name'))->first();
        $user = Business::find($business->id)->users->where('number',$request->search)->first();
        return json_encode($user);
    }

    public function getTherapistsAjax(Request $request){
        
        Validator::make($request->all(), [
            'date'     => ['required'],
            'from'     => ['required'],
            'till'     => ['required'],
        ])->validate();
        
        $allFromsAndTills = $this->getAllTimes($request->from,$request->till);
        $froms = $allFromsAndTills->froms;
        $tills = $allFromsAndTills->tills;
        $therapistsNotAllowed = Date::where('business_id',Auth::user()->business_id)
        ->where('date',$request->date)
        ->where('is_active',1)
        ->where(
        function($query) use ($froms,$tills) {
            return $query
                ->WhereIn('till',$tills)
                ->orWhereIn('from',$froms);
        })
        ->get()->pluck('user_id')->toArray();

        $therapists = Business::find(Auth::user()->business_id)->users->where('role','!=','Customer')->where('is_active',1)->where('is_therapist',1)->whereNotIn('id',$therapistsNotAllowed);
        
        $data = '';
        foreach($therapists as $therapist){
            $data .= '<option value="'.$therapist->id.'">'.$therapist->name.'</option>';
        }
        
        return json_encode($data);
    }

    public function getAllTimes($from,$till){

        if($till == '0:00')
            $till = '23:59';

        $business = Business::find(auth()->user()->business_id);
        $startTime = strtotime($from); 
        $endTime   = strtotime($till);
        $returnTimeFormat = 'G:i';
    
        $current   = time(); 
        $addTime   = strtotime('+'.$business->time_interval.' mins', $current); 

        $diff      = $addTime - $current;
   
        
        $this->froms = array(); 
        $this->tills = array(); 
        $firstTime = $startTime;
        while ($startTime < $endTime) { 
            if($startTime != $firstTime)
                array_push($this->tills,date($returnTimeFormat, $startTime));
            
            array_push($this->froms,date($returnTimeFormat, $startTime));
            $startTime += $diff; 
        } 
        array_push($this->tills,date($returnTimeFormat, $startTime));
        
        return $this;
    }

    public function sendMobilePaySms_(Request $request){
        Validator::make($request->all(), [
            'id' => ['required']
        ])->validate();

        $mobilePayOption = Business::find(auth()->user()->business_id)->PaymentMethods->where('title','Mobilepay')->first();

        
        $treatment = TreatmentSlot::find($request->id);
        if($treatment != 'null'){
            $treatment->payment_method_id = $mobilePayOption->id;

            TreatmentSlot::where('parent_slot', $request->id)->update(['payment_method_id' => $mobilePayOption->id]);

            $locationNumber = Business::find(Auth::user()->business_id)->Settings->where('key','mobile_pay_number')->first();

            // -------  for this user's will change language ------
            \App::setLocale($treatment->user->language);

            $link = 'https://www.mobilepay.dk/erhverv/betalingslink/betalingslink-svar?phone='.$locationNumber->value.'&amount='.$treatment->treatment->price.'&comment='.substr(str_replace('','',$treatment->treatment->treatment_name),0,25).'&lock=1';

            $url = $this->getMobilePayLink($link);

            $sms =  __('smstxt.mobile_pay_txt',['name' => $treatment->user->name, 'link' => $url ]);
            
            //----- Send SMS Now ------
            \Log::channel('custom')->info("Mobile pay SMS will send now to customer.",['business_id' => $treatment->business_id, 'user_id' => $treatment->user_id, 'sms_text' => $sms]);
            $this->dispatch(new SendSmsJob($sms,$treatment->user_id));

            $treatment->save();            
            echo 1;
        }else{
            echo 0;
        }
        
        
    }

    public function checkCPForInsurance(Request $request){
        Validator::make($request->all(), [
            'user_id' => ['required']
        ])->validate();

        $check = TreatmentSlot::where([['user_id',$request->user_id]])->whereNotNull('CPR')->first();
        if($check != NULL){
            return json_encode($check->cpr);
        }else{
            return 0;
        }
    }

    public function getMobilePayLink($url){
        $string = Str::random(10);

        $link = new Links();
        $link->string = $string;
        $link->link = $url;
        $link->business_id = auth()->user()->business_id;
        $link->save();

        
        return url('/mobile-pay/'.$string.$link->id);

    }

    public function notifyWaitingList($dateId){

        if($dateId > 0){

            $usersList = TreatmentWaitingSlot::select('user_id')->where('date_id',$dateId)->groupBy('user_id')->pluck('user_id');
            $brandName = Business::find(Auth::user()->business_id)->Settings->where('key','email_sender_name')->first();
            $dateFormat = Business::find(Auth::user()->business_id)->Settings->where('key','date_format')->first(); 
            $date = Date::find($dateId);

            foreach($usersList as $id){

                $user = User::find($id);
                \App::setLocale($user->language);

                //------ Add log in DB for location -----//
                $type = "log_for_booking_related_details";
                $txt = "Sending email to customers about free slots ion treatments. <br> Customer Email: <b>".$user->email."</b><br>Treatment Date: <b>".$date->date."</b>";
                $this->addLocationLog($type,$txt);

                //-------- Send Email to user ----------
                $homeLink = url('/other-account/'.md5($user->id));
                
                $subject =  __('emailtxt.waiting_list_treatment_email_subject',['name' => $brandName->value]);
                $content =  __('emailtxt.waiting_list_treatment_email_txt',['name' => $user->name,'email' => $user->email, 'date' => \Carbon\Carbon::parse($date->date)->format($dateFormat->value), 'pageLink' => $homeLink , 'homeLink' => $homeLink, 'businessName' => $brandName->value ]);

                $this->dispatch(new SendEmailJob($user->email,$subject,$content,$brandName->value,$user->business_id));
            }
        }
        
    }

    public function checkExisingTimes($dateId,$from,$till){

        $timeInterval = Business::find(Auth::user()->business_id);
        $slots = $this->getAllTimes($from,$till);
        $allSlots = array_unique(array_merge($slots->froms,$slots->tills));
        array_pop($allSlots);
        $bookings = TreatmentSlot::whereNotIn('time',$allSlots)->where('date_id',$dateId)->where('is_active',1)->count();        
        if($bookings > 0){
           return true;
        }
        return false;
    }

    public function getAvailableTreatments(Request $request){
        Validator::make($request->all(), [
            'id' => ['required']
        ])->validate();
        
        $slot = TreatmentSlot::find($request->id);
        if($slot != NULL){
            
            $date = Date::find($slot->date_id);
            $times = $this->getAllTimes($date->from,$date->till);
            $treatments = $date->treatments;
            $business = Business::find($slot->business_id);
            $CPRForInsurance = Business::find($slot->business_id)->Settings->where('key','cpr_emp_fields_insurance')->first();

            $html = '';
            foreach($treatments as $treatment){ 
                
                if($CPRForInsurance != Null && $CPRForInsurance->value == 'false' && $treatment->is_insurance == 1 ) 
                    continue;

                $selected = '';
                $timeShow = $treatment->time_shown ?: $treatment->inter;
                $count = ceil($treatment->inter/$business->time_interval);
                
                #---- check if this treatment is selected ----
                if($slot->treatment_id == $treatment->id)
                    $selected = "selected";

                if($count == 1){
                    $html .= "<option value='".$treatment->id."' ".$selected.">".$treatment->treatment_name." (".$timeShow." min)</option>";
                }else{
                    $key = array_search($slot->time,$times->froms);
                    $timeToCheck = array();
                    $flag = 0;
                    for($i=0;$i<$count;$i++){
                        if($i == 0)
                            continue;

                        $newKey = $key+$i;
                        if(array_key_exists($newKey ,$times->froms)){
                            $timeToCheck[] =  $times->froms[$newKey];
                        }else{
                            $flag = 1;
                        }
                    }
                    if($flag == 0){
                        $bookings = TreatmentSlot::whereIn('time',$timeToCheck)->where('date_id',$date->id)->where('is_active',1)->whereNull('parent_slot')->count();
                        if($bookings == 0){
                            $html .= "<option value='".$treatment->id."' ".$selected.">".$treatment->treatment_name." (".$timeShow." min)</option>";
                        }
                    }
                }                
            }
        }

        return $html;
    }

    public function updateTreatmentId(Request $request){
        Validator::make($request->all(), [
            'id' => ['required'],
            'treatment' => ['required'],
        ])->validate();

        $business = Business::find(Auth::user()->business_id);
        $treatment = Treatment::find($request->treatment);
        $slots = TreatmentSlot::where('id',$request->id)->orWhere('parent_slot',$request->id)->where('is_active',1)->orderBy('id')->get();
        $count = ceil($treatment->inter/$business->time_interval);

        if(count($slots) > $count){
            #---- remaning bookings will be delete ---
            $i = 1;
            $a = $b = 0;
            $times = array();
            foreach($slots as $slot){
                if($i==1){
                    $date = Date::find($slot->date_id);
                    $data['date_id'] = $date->id;
                    $data['treatments'] = $date->treatments->toArray();
                    $data['departments'] = $business->departments->toArray();    
                }
                if($i <= $count){
                    #--- update
                    $a++;
                    $slot->treatment_id = $request->treatment;
                    $slot->save();
                }else{
                    #--- delete
                    $times[] = $slot->time;
                    $slot->delete();
                    $b++;
                }
                $i++;
            }

            $data['status'] = 'delete';
            $data['count'] = $b;
            $data['times'] = array_reverse($times);
        }
        elseif(count($slots) < $count){

            //---- check if next slots are free or booked ---
            $ss = TreatmentSlot::find($request->id);
            for($j=0;$j<$count;$j++){
                //------ Creating new time ----
                $bookingTime = strtotime($ss->time);
                $newTime   = strtotime('+'.($business->time_interval * ($j)).' mins', $bookingTime); 
                $newTimes[] = date("G:i", $newTime);
            }
            
            $checkCount = TreatmentSlot::whereIn('time',$newTimes)->whereNotIn('time',$slots->pluck('time')->toArray())->where('date_id',$ss->date_id)->where('is_active',1)->count();        

            if($checkCount > 0){
                $data['status'] = 'error';
                return json_encode($data);
            }

            #---- add more bookings ----
            $i = 0;
            $times = array();
            foreach($slots as $slot){
                if($i == 0){
                    #--- update
                    $date = Date::find($slot->date_id);
                    $data['date_id'] = $date->id;
                    $data['treatments'] = $date->treatments->toArray();
                    $data['departments'] = $business->departments->toArray();    

                    $slot->treatment_id = $request->treatment;
                    $slot->save();
                    $count--;
                }else{
                    $times[] = $slot->time;
                    $slot->delete();
                }  
                $i++;          
            }
            for($j=0;$j<$count;$j++){
                #--- add new bookings ---
                $slot = TreatmentSlot::where('id',$request->id)->where('is_active',1)->orderBy('id','DESC')->first();
                $newSlot = $slot->replicate();
                $newSlot->parent_slot = $slot->id;
                
                //------ Creating new time ----
                $bookingTime = strtotime($slot->time);
                $newTime   = strtotime('+'.($business->time_interval * ($j+1)).' mins', $bookingTime); 
                $newTimeonly = date("G:i", $newTime);

                $newSlot->time = $newTimeonly; 
                
                //---- remove this value from array ---
                if (($key = array_search($newTimeonly, $times)) !== false) {
                    unset($times[$key]);
                }
            
                $newSlot->save();
            }

            $data['status'] = 'add';
            $data['count'] = $j;
            $data['times'] = array_reverse($times);
        }
        else{
            #---- no delete , no add more just update
            $a = 0;
            foreach($slots as $slot){
                #------ update only ---
                $a++;
                $slot->treatment_id = $request->treatment;
                $slot->save();
            }
            $data['status'] = 'update';
            $data['count'] = $a;
        }
        
        return json_encode($data);
    }

//------------------- Common Functions ends ----------------------
    
}
