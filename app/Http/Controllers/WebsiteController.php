<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Jobs\SendEmailJob;
use App\Models\Business;
use App\Models\User;
use App\Models\Website;
use App\Models\Event;
use App\Models\EventSlot;
use App\Models\Treatment;
use App\Models\TreatmentSlot;
use App\Models\Department;
use App\Models\Date;
use App\Models\Links;
use App\Models\Plan;
use Illuminate\Support\Facades\Validator;

class WebsiteController extends Controller
{

    public function index(){
        if(session('business_name') == 'admin' || session('business_name') == 'www' ){
            return redirect('/profile');
        }

        $business =  Business::select()->where('business_name',session('business_name'))->first();
        $page = Business::find($business->id)->websites->where('page','INFORMATION')->where('language',session('locale'))->first();
        $bookingPage = Business::find($business->id)->websites->where('page','BOOKING')->where('language','en')->first();
        $eventPage = Business::find($business->id)->websites->where('page','EVENTS')->where('language','en')->first();

        $offerData = $offerTitle = '';
        $offerSection = Business::find($business->id)->Settings->where('key','offer_section_home')->first();
        $offerUrl = Business::find($business->id)->Settings->where('key','offer_url')->first();
        $offerTitle = Business::find($business->id)->Settings->where('key','offer_title')->first();
        if($offerSection->value == 'true') 
            $offerData = Business::find($business->id)->offer; 

        //------- Get HTML content -------
        // if(is_file(storage_path( session('locale')."-home.txt"))){
        //     $file = fopen(storage_path( session('locale')."-home.txt"), "r");
        //     $content = fread($file,filesize(storage_path( session('locale')."-home.txt")));
        // }else{
        //     $file = fopen(storage_path("en-home.txt"), "r");
        //     $content = fread($file,filesize(storage_path("en-home.txt")));
        // }

        return view('web.home',compact('business','page','bookingPage','eventPage','offerSection','offerData','offerUrl','offerTitle'));
    }

    public function booking(){
        $incurance = 0;
        if(isset($_GET['_token']) && $_GET['_token'] == \Session::get('_token'))
            $incurance = 1;

        $lastDepOfCurrentUser = '';
        if(Auth::user()){
            $lastDepOfCurrentUser = TreatmentSlot::select('department_id')->where('user_id',Auth::user()->id)->where('parent_slot',NULL)->where('is_active',1)->where('department_id','!=',NULL)->where('status','Booked')->orderBy('id','desc')->first();
            if($lastDepOfCurrentUser != null)
                $lastDepOfCurrentUser = $lastDepOfCurrentUser->department_id;
        }

        $Tids = array();
        $business = Business::where('business_name',session('business_name'))->first();
        $dates = Date::where('business_id',$business->id)->where('is_active',1)->where('date','>=',date('Y-m-d'))->get();
        //----- Check if slots not availabe then remove date from list ----- 
        foreach($dates as $key => $date){
            $date1 = "2014-05-27 ".$date->from.":00";
            $date2 = "2014-05-27 ".$date->till.":00";
            $timestamp1 = strtotime($date1);
            $timestamp2 = strtotime($date2);
            $mins = abs($timestamp2 - $timestamp1)/60;
            $totalBslots = $mins/$business->time_interval;
            if($date->treatmentSlots->count() == $totalBslots && $date->waiting_list == 0){
                $dates->forget($key);
            }
            $treatmentsIDS = $date->treatments->where('is_visible',1)->where('is_insurance',$incurance)->pluck('id')->toArray();

            foreach($treatmentsIDS as $treatmentsID){
                \array_push($Tids, $treatmentsID);
            }
        }
        $ids = $dates->pluck('user_id')->toArray();
        $threapists = Business::find($business->id)->users->where('role','!=','Customer')->where('is_active',1)->whereIn('id',$ids); 
        $dates = $dates->count();
        
        $treatments = Treatment::where('business_id',$business->id)->where('is_active',1)->where('is_visible',1)->whereIn('id',$Tids)->get();
        $departments = Business::find($business->id)->Departments->where('is_active',1); 
        $settings = Business::find($business->id)->Settings; 
        $countries = $this->countries();
        

        return view('web.booking',compact('treatments','threapists','departments','settings','countries','dates','lastDepOfCurrentUser'));
    }

    public function keepBooking(){        
        $business = Business::where('business_name',session('business_name'))->first();
        if (Auth::check()) {
            $bookings = EventSlot::where([['user_id',Auth::user()->id],['business_id',Auth::user()->business_id],['is_active',1]])->orderBy('id','DESC')->pluck('event_id');

            if($bookings->count() > 0)
                $events = Event::where('business_id',$business->id)->where('is_active',1)->where('status','Active')->where('date','>=',date('Y-m-d'))->whereNotIn('id',$bookings)->orderBy('date','ASC')->get();
            else
                $events = Event::where('business_id',$business->id)->where('is_active',1)->where('status','Active')->where('date','>=',date('Y-m-d'))->orderBy('date','ASC')->get();
        }
        else{
            $events = Event::where('business_id',$business->id)->where('is_active',1)->where('status','Active')->where('date','>=',date('Y-m-d'))->orderBy('date','ASC')->get();
        }
        $settings = Business::find($business->id)->Settings; 
        $countries = $this->countries();

        return view('web.keep_booking',compact('events','settings','countries'));
    }

    public function prices(){
        $business =  Business::select()->where('business_name',session('business_name'))->first();
        $page = Business::find($business->id)->websites->where('page','PRICES')->where('language',session('locale'))->first();
        return view('web.prices',compact('page'));
    }

    public function contact($subdomain,$msg=''){
        return view('web.contact',compact('msg'));
    }

    public function contactSave(Request $request){
        Validator::make($request->all(), [
            'name'      => ['required','max:255'],
            'email'     => ['required', 'string', 'email', 'max:255'],
            'number'     => ['required', 'max:255'],
            'subject'    => ['required', 'string', 'max:255'],
            'description'  => ['required', 'string'],
        ])->validate();

        $useID = NULL;
        $business =  Business::select()->where('business_name',session('business_name'))->first();
        $brandName = Business::find($business->id)->Settings->where('key','email_sender_name')->first(); 
        $contactFormEmail = Business::find($business->id)->Settings->where('key','email_for_contact_form')->first(); 

        if(\Auth::user())
            $useID = \Auth::user()->id;

        \DB::table('contact_us')->insert([
            'business_id' => $business->id,
            'subject' => $request->subject,
            'user_id' => $useID,
            'name' => \Auth::user() ? \Auth::user()->name : $request->name,
            'email' => \Auth::user() ? \Auth::user()->email : $request->email,
            'number' => \Auth::user() ? \Auth::user()->number : $request->number,
            'description' => $request->description,
        ]);

        $message = "<b>".__('keywords.subject')."</b>: ".$request->subject."<br>";
        $message .= "<b>".__('keywords.name')."</b>: ".$request->name."<br>";
        $message .= "<b>".__('keywords.email')."</b>: ".$request->email."<br>";
        $message .= "<b>".__('keywords.number')."</b>: ".$request->number."<br>";
        $message .= "<b>".__('keywords.description')."</b>: ".$request->description."<br><br>";

        if($contactFormEmail != NULL)
            $emailTo = $contactFormEmail->value;
        else
            $emailTo = $business->superAdminRole->userFromRole->email;    
       
        $this->dispatch(new SendEmailJob($emailTo,$request->subject,$message,$brandName->value,$business->id));

        $request->session()->flash('success');
        \Log::channel('custom')->info("Contact form has been save successfully!",['business_id' => $business->id]);

        return \Redirect::back();

    }

    public function covid(){
        $business =  Business::select()->where('business_name',session('business_name'))->first();
        $page = Business::find($business->id)->websites->where('page','COVID')->where('language',session('locale'))->first();
        return view('web.covid',compact('page'));
    }

    public function status(){
        $business =  Business::where('business_name',session('business_name'))->first();
        $events = Event::where([['is_active',1],['date','>=',date('Y-m-d')],['status','Active'],['business_id',$business->id]])->orderBy('date','ASC')->get();
        $dateFormat = Business::find($business->id)->Settings->where('key','date_format')->first(); 

        return view('web.status',compact('events','dateFormat'));
    }

    public function termForInsurance(){
        $business =  Business::select()->where('business_name',session('business_name'))->first();
        $page = Business::find($business->id)->websites->where('page','INSURANCE')->where('language',session('locale'))->first();
        return view('web.insurance',compact('page'));
    }

    public function gdpr(){
        $business =  Business::select()->where('business_name',session('business_name'))->first();
        $page = Business::find($business->id)->websites->where('page','GDPR')->where('language',session('locale'))->first();
        
        //------- Get HTML content -------
        // if(is_file(storage_path( session('locale')."-gdrp.txt"))){
        //     $file = fopen(storage_path( session('locale')."-gdrp.txt"), "r");
        //     $content = fread($file,filesize(storage_path( session('locale')."-gdrp.txt")));
        // }else{
        //     $file = fopen(storage_path("en-gdrp.txt"), "r");
        //     $content = fread($file,filesize(storage_path("en-gdrp.txt")));
        // }

        return view('web.gdpr',compact('page'));
    }

    public function resendEmail(){
        return view('web.resend_email');
    }

    public function pageList(){
        abort_unless(\Gate::allows('Website Pages View'),403);

        $pages = Business::find(Auth::user()->business_id)->websites->where('language',\Lang::locale());
        return view('web.page_list',compact('pages'));
    }

    public function editPage($subdomain,$id){
        abort_unless(\Gate::allows('Website Pages Edit'),403);

        $page = Website::where(\DB::raw('md5(id)') , $id)->first();
        $pages =  Website::where('page',$page->page)->where('business_id',$page->business_id)->get();
        return view('web.page_edit',compact('pages'));
    }

    public function savePage(Request $request){
        abort_unless(\Gate::allows('Website Pages Edit'),403);

        foreach($request->language as $key => $val){

            $page = Website::where(['page' => $request->name,'business_id' => Auth::user()->business_id,'language'=> $val])->first();

            if($page){
                $page->title = $request->title[$key];
                $page->language = $val;
                $page->content = $request->content[$key];
                if($request->status){
                    $page->is_active = 1;

                    # IF page is active then 'cpr_emp_fields_insurance' setting should turned on
                    if($page->page == 'INSURANCE'){
                        $setting = Business::find(auth()->user()->business_id)->Settings->where('key','cpr_emp_fields_insurance')->first();
                        $setting->value = 'true';
                        $setting->save();
                    }
                }
                else
                    $page->is_active = 0;
                
                if($page->save()){
                    $request->session()->flash('success',__('web.dhbus'));
                    \Log::channel('custom')->info("Page has been updated.", ['business_id' => Auth::user()->business_id, 'page_id' => $page->id]);
                    
                    //------ Add log in DB for location -----//
                    $type = "log_for_pages";
                    $txt = "Page has been updated. <br>Page : <b>".$page->title."</b><br>Updated By:<b>".Auth::user()->email."</b>";
                    $this->addLocationLog($type,$txt);

                }
                else{
                    \Log::channel('custom')->error("Error to update Page.", ['business_id' => Auth::user()->business_id, 'page_id' => $page->id]); 
                    $request->session()->flash('error',__('web.tiaetuyd'));
                }
            }
            else{
                //----- add page here ----
                if($request->status)
                    $status = 1;
                else
                    $status = 0;

                $pages = Website::create([
                    'business_id' => Auth::user()->business_id,
                    'page' => $request->name,
                    'title' => $request->title[$key],
                    'language' => $val,
                    'content' => $request->content[$key],
                    'is_active' => $status,
                ]);
            }
     
        }
        
        return \Redirect::back();
    }

    public function brandInfo(){
        abort_unless(\Gate::allows('Brand Details View'),403);

        $brand = Business::find(Auth::user()->business_id);
        return view('web.brandinfo',compact('brand'));
    }

    public function update(Request $request){
        abort_unless(\Gate::allows('Brand Details Edit'),403);

        $validatedData = $request->validate([
            'name' => ['max:255'],
            'image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'banner' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        //------------ For Logo ----------
        if( !empty($request->image) ){
            $imageName = time().'i.'.$request->image->extension();  
            $request->image->move(public_path('images'), $imageName);
        }

        //------------ For Banner ----------
        if( !empty($request->banner) ){
            $bannerName = time().'b.'.$request->banner->extension();  
            $request->banner->move(public_path('images'), $bannerName);
        }

        $brand = Business::find(Auth::user()->business_id);
        $brand->brand_name = $request->name;
        $brand->business_email = $request->email;

        if( !empty($request->image) )
            $brand->logo = $imageName;

        if( !empty($request->banner) )
            $brand->banner = $bannerName;
        
        if($brand->save()){
            $request->session()->flash('success',__('web.dhbus'));
            \Log::channel('custom')->info("Business info has been updated.", ['business_id' => Auth::user()->business_id]);
            
            //------ Add log in DB for location -----//
            $type = "log_for_pages";
            $txt = "Business info has been updated. <br>Updated By:<b>".Auth::user()->email."</b>";
            $this->addLocationLog($type,$txt);
        }
        else{
            $request->session()->flash('error',__('web.tiaetuyd'));
            \Log::channel('custom')->error("Error to update business info.", ['business_id' => Auth::user()->business_id]); 
        }
        return \Redirect::back();

    }

    public function eventBookFromSiteAjax(Request $request){
        Validator::make($request->all(), [
            'name'      => ['required','max:255'],
            'email'     => ['required', 'string', 'email', 'max:255'],
            'number'    => ['max:255'],
            'events'     => ['required'],
        ])->validate();

        $business =  Business::where('business_name',session('business_name'))->first();
        $brandName = Business::find($business->id)->Settings->where('key','email_sender_name')->first(); 
        $dateFormat = Business::find($business->id)->Settings->where('key','date_format')->first(); 
        
        //--------- Check if Code enable for customer or not -------
        $bookingCodeCheck = Business::find($business->id)->Settings->where('key','code_for_booking')->first();
        
        if($bookingCodeCheck->value == 'true'){
            $bookingCode = Business::find($business->id)->Settings->where('key','code')->first();
            if($request->code != $bookingCode->value){
                $data['status'] = 'error';
                return json_encode($data);
            }
        }
        //--------------------------- check for user -----------------------------
        if($request->id == ''){
            if($bookingCodeCheck->value == 'true'){
                $data['status'] = 'not';
                return \Response::json($data);
                exit(); 
            }
            $userID = $this->addUser($request->email,$business->id,$request->name,$request->country,$request->number,$request->cprnr,$request->mednr,$brandName->value);
            if( $userID == 0 ){
                $data['status'] = 'Email Exist';
                return \Response::json($data);
                exit(); 
            }
        }            
        else
            $userID = $request->id; 
                            
        //--------------------------- check for booking -----------------------------
        $eventsArr = explode(',',$request->events);
        $guestsArr = explode(',',$request->guests);
        $guestsCountArr = explode(',',$request->guestCount);
        foreach($eventsArr as $key => $eventID){ 
            
            $event = Event::find($eventID);
            $currentBookings = $event->eventActiveSlots->count();
            $limit = $event->slots;
            $check = $event->eventActiveSlots->where('user_id',$userID)->count();

            //---- Check if user not book for this event before ----
            if($check > 0){
                $data['status'] = 'Booking Exist';
                $data['event'] = $event->name;
                return \Response::json($data);
            }
        }

        //-------------- Check if limit set of active booking and monthly bookings -----
        $bookingsPerMonth = Business::find($business->id)->Settings->where('key','max_bookings_month')->first();
        $activeBookings = Business::find($business->id)->Settings->where('key','max_active_bookings')->first();
        //----- Get active events ------
        $activeEvents = Business::find($business->id)->Events->where('date','>=',date('Y-m-d'))->where('is_active',1)->pluck('id');

        $data['message'] = '';
        foreach($eventsArr as $key => $eventID){ 

            $event = Event::find($eventID);
            $maxGuest = ($guestsCountArr[$key] > $event->max_guests ? $event->max_guests : $guestsCountArr[$key] );

            $stopBooking = Business::find($event->business_id)->Settings->where('key','stop_booking')->first();
            //-------- Minus hours from event time, so that can check it can be booked or not now ---
            $eventTime = strtotime(\Carbon\Carbon::parse($event->date)->format('Y-m-d').' '.$event->time);
            $currentTime = strtotime(\Carbon\Carbon::now()->addHour($stopBooking->value)->format('Y-m-d H:i'));

            //------ Check if booking time passed o not --------
            if($eventTime < $currentTime){
                $data['status'] = 'time_passed';
                if($key > 0){
                        $data['message'] = __('web.booking_time_passed');
                        \Log::channel('custom')->info("Event book from website.",['business_id' => $business->id,'user_id' => $userID, 'text' => "Your first ".$key." event booking has been booked but rest booking can not book because of active bookings limit reached!"]);

                        //------ Add log in DB for location -----//
                        $type = "log_for_booking_related_details";
                        $txt = "Event book from website. <br>Event : <b>".$event->name."</b><br>Event Date:<b>".$event->date."</b>";
                        $this->addLocationLog($type,$txt);
                    }    
                    else{
                        $data['message'] =  __('web.booking_time_passed');
                        \Log::channel('custom')->info("Event book from website.",['business_id' => $business->id,'user_id' => $userID, 'text' => "Sorry your active bookings limit reached!"]); 
                    }
                    return \Response::json($data);
                    exit();
            }


            //------- Active Booking Limit -----
            if($activeBookings->value >= 0){
                //----- Get events bookings that not passed yet ------
                $activeBooked = EventSlot::where([['user_id',$userID],['is_active',1],['parent_slot',Null]])->whereIn('event_id',$activeEvents)->count();
                //---- If active booking limit reached then not allowd to book more -----
                if( $activeBookings->value <= $activeBooked ){
                    $data['status'] = 'active_limit';
                    if($key > 0){
                        $data['message'] = __('web.active_booking_limit_error', ['num' => $key]);
                        \Log::channel('custom')->info("Event book from website.",['business_id' => $business->id,'user_id' => $userID, 'text' => "Your first ".$key." event booking has been booked but rest booking can not book because of active bookings limit reached!"]);

                        //------ Add log in DB for location -----//
                        $type = "log_for_booking_related_details";
                        $txt = "Event book from website. <br>Event : <b>".$event->name."</b><br>Event Date:<b>".$event->date."</b>";
                        $this->addLocationLog($type,$txt);
                    }    
                    else{
                        $data['message'] =  __('web.syablr');
                        \Log::channel('custom')->info("Event book from website.",['business_id' => $business->id,'user_id' => $userID, 'text' => "Sorry your active bookings limit reached!"]); 
                    }
                    return \Response::json($data);
                    exit();
                }     
            }

            //----- Monthly Booking Limit -----
            if($bookingsPerMonth->value >= 0){

                //----- Get current month dates ------
                $timestamp = strtotime($event->date);
                $month = date('m', $timestamp);
                
                $thisMonthEvents =  \DB::table('events')
                ->whereMonth('date', $month)
                ->where('business_id',$business->id)
                ->pluck('id');

                $monthlyBooked = EventSlot::where([['user_id',$userID],['is_active',1],['parent_slot',Null]])->whereIn('event_id',$thisMonthEvents)->count();

                //---- If monthly booking limit reached then not allowd to book more -----
                if( $bookingsPerMonth->value <= $monthlyBooked ){
                    $data['status'] = 'monthly_limit';
                    if($key > 0){
                        $data['message'] = __('web.monthly_limit_error', ['name' => $event->name]);
                        \Log::channel('custom')->info("Event book from website.",['business_id' => $business->id,'user_id' => $userID, 'text' => "Event '".$event->name."' can not book because of monthly bookings limit reached!"]); 
                    }    
                    else{
                        $data['message'] = __('web.symblr');
                        \Log::channel('custom')->info("Event book from website.",['business_id' => $business->id,'user_id' => $userID, 'text' => "Sorry your monthly bookings limit reached!"]); 
                    }
                    return \Response::json($data);
                    exit();
                }    
            }

            $booking = new EventSlot();
            $booking->user_id       = $userID;
            $booking->event_id      = $eventID;
            $booking->business_id   = $business->id;
            $booking->comment       = $request->comment;
            $booking->is_guest      = ($guestsArr[$key] == 1 ? '1':'0');
            $booking->status        = ($currentBookings < $limit ? 1 : 0 );
            if($booking->save())
            {
                //-------- Send Email to user ----------
                $email = User::find($userID);
                // -------  for this user's will change language ------
                \App::setLocale($email->language);
                $guestMessage = '';
                $guestMessageSms = '';
                
                $currentBookings++;
                //---- If guest YES -----
                if( $guestsArr[$key] == 1 ){
                    for($k=0;$k<$maxGuest;$k++){
                        $guest = new EventSlot();
                        $guest->user_id       = $userID;
                        $guest->event_id      = $eventID;
                        $guest->business_id   = $business->id;
                        $guest->comment       = $request->comment;
                        $guest->status        = ($currentBookings < $limit ? 1 : 0 );
                        $guest->is_guest      = 0;
                        $guest->parent_slot   = $booking->id;
                        $guest->save();
                        $currentBookings++;

                        $guestMessage .=  __('event.guest_booking_status').': <b>'.($guest->status ? __('event.booked') : __('event.waiting_list')).'</b>';
                        $guestMessageSms .= __('event.guest_booking_status').($guest->status ? __('event.booked') : __('event.waiting_list'));
                    }
                }


                $subject = __('emailtxt.event_book_subject',['name' => $brandName->value]);
                
                $instructor = '<a target="_blank" href="'.url('/showUser/'.md5($event->user->id)).'">'.$event->user->name.'</a>';
                $link = url('/other-account/',md5($event->user->id));                
                $content = __('emailtxt.event_book_txt',['name' => $email->name,'date' => \Carbon\Carbon::parse($event->date)->format($dateFormat->value), 'time' => $event->time, 'status' => ($booking->status ? __('event.booked') : __('event.waiting_list')), 'guest' => $guestMessage, 'ename' => $event->name ,'link' => $link, 'instructor' => $instructor ]);

                //------ Add log in DB for location -----//
                $type = "log_for_booking_related_details";
                $txt = "Event book from website. <br>Event : <b>".$event->name."</b><br>Event Date:<b>".$event->date."</b>";
                $this->addLocationLog($type,$txt);

                //------------ Send email of booking -----    
                if($email->email != ''){
                    \Log::channel('custom')->info("Sending email to customer about event booking from website.",['business_id' => $business->id,'user_id' => $email->id, 'subject' => $subject, 'email_text' => $content]); 
                    $this->dispatch(new SendEmailJob($email->email,$subject,$content,$brandName->value,$business->id));

                    #------ Send email to Therapist about this booking ----//
                    $this->notifyTherapist($event->user->id,$subject,$content,$brandName->value);

                }

                //------- Send sms notification to user ------        
                //--- check if business allow to send sms ----
                $smsSetting = Business::find($business->id)->Settings->where('key','sms_setting')->first();
                $tbookingSms = Business::find($business->id)->Settings->where('key','e_booking_sms')->first();
                if($tbookingSms->value == 'true' && $smsSetting->value == 'true'){

                    $sms = __('smstxt.event_book_txt',['name' , $email->name,'date' => \Carbon\Carbon::parse($event->date)->format($dateFormat->value), 'time' => $event->time, 'status' => ($booking->status ? __('event.booked') : __('event.waiting_list')), 'guest' => $guestMessage, 'ename' => $event->name  ]);

                    //--- send sms notification ---
                    \Log::channel('custom')->info("Sending sms to customer about event booking from website.",['business_id' => $business->id,'user_id' => $email->id, 'sms_text' => $sms]); 
                    $this->smsSendWithCheck($sms,$email->id);
                }
                
                if(false){//-----remove when functional ---
                    //---- Check survey is on in setting -------//
                    $surveySetting = Business::find($email->business_id)->Settings->where('key','survey_setting')->first();
                    $surveyDuration = Business::find($email->business_id)->Settings->where('key','survey_duration')->first();

                    if($surveySetting->value == 'true'){
                        $checkToSend = 0;
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
                        
                        //----- send survey email to user ----
                        if($checkToSend){
                            $surveySendHour = Business::find($email->business_id)->Settings->where('key','survey_send_hours')->first();
                            $edate = \Carbon\Carbon::parse($event->date)->format('Y-m-d');
                            $delay = \Carbon\Carbon::parse($edate.' '.$event->time)->addHours($surveySendHour->value);
                            //$delay = \Carbon\Carbon::now()->addHours($surveySendHour->value);
                            
                            $link = url('/'."Survey/".$email->language);
                            $sub = __('emailtxt.survey_email_subject',['name' => $brandName->value]);
                            ;
                            $con =  __('emailtxt.survey_email_txt',['name' => $email->name, 'link' => $link]);
                            $job = (new SendEmailJob($email->email,$sub,$con,$brandName->value,$business->id))->delay($delay);

                            \Log::channel('custom')->info("Sending survey email to customer.",['business_id' => $business->id, 'user_id' => $email->id, 'Subject' => $sub, 'email_text' => $con]); 

                            $this->dispatch($job);
                            $email->survey_date = date('Y-m-d');
                            $email->save();
                        }
                    } 
                }    
                //---------------------------------
                \Log::channel('custom')->info("Customer booked for event from website.",['business_id' => $business->id, 'user_id' => $email->id]); 

                //------- Make ICS file for this event ---------

                $type = __('event.events');
                $tName = __('event.events').': '.$event->name;
                $tDuration = $event->duration.'min';
                $tDate = $event->date;
                $tTime = $event->time;
                $tTherapist = __('event.therapist').': '.$event->user->name;

                $this->createIcsFile($type,$tName,$tDuration,$tDate,$tTime,$tTherapist,$booking->id);


                $bookinglink = url('/myEventBookings');
                $data['message'] = __('web.event_book_txt',['name' => $email->name,'link' => $bookinglink ]);
                if($request->id == '')
                    $data['message'] .= __('web.check_email_for_credentials');
                $data['status'] = 'success';
            }
            else{
                $data['status'] = 'error';
                \Log::channel('custom')->error("Error to book for event from website",['business_id' => $business->id]); 
            }
        }
        return \Response::json($data);
    }

    public function getDateOfTreatmentAjax(Request $request){
        Validator::make($request->all(), [
            'treatmentID'      => ['required','max:255'],
            'therapistID'      => ['required','max:255'],
        ])->validate();

        $timeSlots = array();
        $business =  Business::select()->where('business_name',session('business_name'))->first();
        $treatment = Treatment::find($request->treatmentID);
        if($request->therapistID == -1)
            $dates = $treatment->dates->where('date','>=',date('Y-m-d'));
        else
            $dates = $treatment->dates->where('date','>=',date('Y-m-d'))->where('user_id',$request->therapistID);

        $i = 0;
        foreach($dates as $date){
            $dateOnly = date('Y-m-d', strtotime($date->date));
            $bookings = TreatmentSlot::where([['business_id',$business->id],['date_id',$date->id],['is_active',1]])->get();
            
            if(array_key_exists($dateOnly,$timeSlots)){
                $timeSlots[$dateOnly]['others'][$i]['dateID'] = $date->id;
                $timeSlots[$dateOnly]['others'][$i]['description'] = $date->description;
                $timeSlots[$dateOnly]['others'][$i]['therapist'] = $date->user->name;
                $timeSlots[$dateOnly]['others'][$i]['waiting'] = $date->waiting_list;
                
                $businessTimeSlots = $this->getTimeSlots($date->from,$business->time_interval,$date->till);
                $timeSlots[$dateOnly]['others'][$i]['slots'] = $businessTimeSlots;
                $limit = ceil($treatment->inter/$business->time_interval);
                $bookedArray = $this->checkSlotIsBooked($businessTimeSlots,$limit,$bookings->pluck('time')->toArray(),$dateOnly);

                foreach($bookings as $key => $booking){
                    $timeSlots[$dateOnly]['others'][$i]['booked'][] = $booking->time;
                }

                foreach($bookedArray as $booked){
                    $timeSlots[$dateOnly]['others'][$i]['booked'][] = $booked;
                }
                $i++;
            }else{
                $i = 0;
                $timeSlots[$dateOnly]['dateID'] = $date->id;
                $timeSlots[$dateOnly]['description'] = $date->description;
                $timeSlots[$dateOnly]['therapist'] = $date->user->name;
                $timeSlots[$dateOnly]['waiting'] = $date->waiting_list;
                
                $businessTimeSlots = $this->getTimeSlots($date->from,$business->time_interval,$date->till);
                $timeSlots[$dateOnly]['slots'] = $businessTimeSlots;
                $limit = ceil($treatment->inter/$business->time_interval);
                $bookedArray = $this->checkSlotIsBooked($businessTimeSlots,$limit,$bookings->pluck('time')->toArray(),$dateOnly);

                foreach($bookings as $key => $booking){
                    $timeSlots[$dateOnly]['booked'][] = $booking->time;
                }

                foreach($bookedArray as $booked){
                    $timeSlots[$dateOnly]['booked'][] = $booked;
                }
            }
        }
        return \Response::json($timeSlots);
    }

    public function getTherapistOfTreatmentAjax(Request $request){
        Validator::make($request->all(), [
            'treatmentID'      => ['required','max:255'],
        ])->validate();

        $business = Business::where('business_name',session('business_name'))->first();
        $dates = Treatment::find($request->treatmentID)->upCommingDates;
        $ids = $dates->pluck('user_id')->toArray();
        $threapists = Business::find($business->id)->users->where('role','!=','Customer')->where('is_active',1)->whereIn('id',$ids)->toArray(); 
        return \Response::json($threapists);
    }

    public function addLink(Request $request){
        Validator::make($request->all(), [
            'titleEN'      => ['required','max:255'],
            'urlEN'      => ['required'],
            'titleDK'      => ['required','max:255'],
            'urlDK'      => ['required'],
        ])->validate();

        $count = Website::where('business_id',Auth::user()->business_id)->count();
        Website::create([
            'business_id' => Auth::user()->business_id,
            'title' => $request->titleEN,
            'page' => 'Link-'.$count,
            'content' => $request->urlEN,
            'is_active' => 1,
            'language' => 'en',
        ]);
        Website::create([
            'business_id' => Auth::user()->business_id,
            'title' => $request->titleDK,
            'page' => 'Link-'.$count,
            'content' => $request->urlDK,
            'is_active' => 1,
            'language' => 'dk',
        ]);
        
        
        //------ Add log in DB for location -----//
        $type = "log_for_pages";
        $txt = "Link add in web section. <br>Link Title : <b>".$request->titleDK.' - '.$request->titleEN."</b><br>Added By:<b>".Auth::user()->email."</b>";
        $this->addLocationLog($type,$txt);

        $request->session()->flash('success',__('web.new_link_added'));
        \Log::channel('custom')->info("Link add in web section",['business_id' => Auth::user()->business_id]);
        return \Redirect::back();
    }

    public function checkSlotIsBooked($times,$limit,$booked,$date){
        $arry = array();
        $length = count($times)-1;
        $escape = 0;
        
        array_push($booked,$times[$length]);

        foreach($times as $key => $time){

            #check if date is today and time is passed then mark this time slot booked
            # fist we check if this date is today
            $todaydate = \Carbon\Carbon::now()->format('Y-m-d');
            if(\Carbon\Carbon::parse($todaydate) == \Carbon\Carbon::parse($date)){
                $thisSlot = \Carbon\Carbon::now()->format('Y-m-d ').$time;
                # add 5 mints gap in current time
                $last5mints = \Carbon\Carbon::now()->subMinutes(5)->format('Y-m-d H:i');
                if(\Carbon\Carbon::parse($thisSlot) < \Carbon\Carbon::parse($last5mints) ){
                    array_push($arry,$time);
                }
            }

            if($escape > 0){
                $escape--;
                array_push($arry,$time);
                continue;
            }

            // for($i = 0; $i < $limit; $i++){
            //     $offset = $key+$i;
            //     if($length > $offset){
            //         if(array_key_exists($offset, $times)){
            //             if(in_array($times[$offset], $booked)){
            //                 array_push($arry,$time);
            //             }
            //         }
            //     }else{ #this add last time in booking list eg "16:00,22:00"
            //         array_push($arry,$time);
            //     }
            // }

            //----- if this time is booked then no need to check after fields
            if(in_array($time,$booked)){
                array_push($arry,$time);
                continue;
            }

            //------ if current time is not booked then check upcoming times
            for($j=1; $j<$limit; $j++){
                if(array_key_exists($key+$j,$times) ){
                    $next = $times[$key+$j];
                    if( !in_array($next,$booked) ){
                        $escape = $limit-1;
                    }else{
                        $escape = 0;
                        array_push($arry,$time);
                        break;
                    }
                }else{
                    $escape = 0;
                    array_push($arry,$time);
                    break;
                }
            }

            #this add last time in booking list eg "16:00,22:00"
            if($length <= $key){
                array_push($arry,$time);
            }
        }
        return $arry;
    }
    //---#############################################################################################//
    public function changeDate(Request $request){
        return \Carbon\Carbon::parse($request->date)->format('Y-m-d');
    }

    public function mobilePayLink($subdomain,$string){
        $id = substr($string,10);
        $string = substr($string, 0, 10);

        $link = Links::where([['id',$id],['string',$string]])->first();

        if($link != Null){
            $link->status = 1;
            $link->save();

            \Log::channel('custom')->info("User rediected to MobilPay site.",['link_id' => $id]);

            //------ Add log in DB for location -----//
            $type = "log_for_pages";
            $txt = "User click MobilPay link. <br>Link : <b>".$link->link."</b>";
            $this->addLocationLog($type,$txt);

            return redirect($link->link);
        }

        return redirect('/');
    }

    //---#############################################################################################//
    public function registration(){
        $plans = Plan::where('status',1)->get();
        return view('web.registration',compact('plans'));
    }
}
