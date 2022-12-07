<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\Business;
use App\Models\User;
use App\Models\Website;
use App\Models\TreatmentPart;
use App\Models\TreatmentPartTranslation;
use App\Models\PaymentMethod;
use App\Models\Setting;
use App\Models\Permission;
use App\Models\Role;
use App\Models\Question;
use App\Models\Option;
use App\Models\Treatment;
use App\Jobs\SendEmailJob;


class BusinessController extends Controller
{
    //----------------------------------//
    public function index(){
        $business =  Business::select()->get();
        return view('business.list')->with(compact('business'));
    } 
    //----------------------------------//
    public function view($subdomain,$id){
        $business =  Business::select()->where('id',$id)->first();
        $users = Business::find($id)->users;
        return view('business.view')->with(compact('business','users'));
    }
    //----------------------------------//
    public function create(){
        $permissions = array();
        $selectedPermissions = array();
        $file = fopen(storage_path("permissions.txt"), "r");
        $file2 = fopen(storage_path("default-permissions.txt"), "r");

        while(!feof($file)) {
            array_push($permissions,fgets($file));
        }
        fclose($file);

        while(!feof($file2)) {
            array_push($selectedPermissions,fgets($file2));
        } 
        fclose($file2);
        
        return view('business.create',compact('permissions','selectedPermissions'));
    }
    //----------------------------------//
    public function register(Request $request){

        Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'business_name' => ['required', 'string', 'max:255','unique:businesses,business_name'],
            'role' => ['required', 'string', 'max:255'],
            'language' => ['required', 'string'],
            'interval' => ['required', 'string', 'max:2'],
            'email' => ['required', 'string', 'email', 'max:255'],
            'password' => ['required','min:8','same:password_confirmation'],
            'permissions' => ['required'],
        ])->validate();

        $business = Business::create([
            'business_name' => strtolower($request['business_name']),
            'languages' => '-1',    // For now it's all
            'access_modules' => '-1', // For now it's all
            'time_interval' => $request['interval'],
        ]);
        
        \Log::channel('custom')->info("New bussiness/location has been created.",['business_id' => $business->id]);

        $this->makePages($business->id);
        $this->makeSettings($business->id,$business->business_name);
        $this->makePaymentMethods($business->id);
        $this->makeTeatmentAreas($business->id);
        $this->makeSurvey($business->id);
        $this->makeUser($request['name'],$business->id,$request['role'],$request['language'],$request['email'],$request['password'],$request['permissions']);
        $this->makeAdminRole($business->id);
        $this->makeTeatments($business->id);

        $request->session()->flash('success',__('business.bcs'));
        return \Redirect::back();
    }
    //----------------------------------//
    public function makeAdminRole($bid){
        $permissions = Permission::where('business_id',$bid)->get();
        $adminPermissions = $this->adminPermissions();
        //----- Make role for Admin ----//
        $role = Role::create([
            'title' => 'Admin',
            'business_id' => $bid,
        ]);
        //---- Add permissions to Admin ----//
        foreach($permissions as $permission){
            if(in_array($permission->title,$adminPermissions)){
                //------- Assign Permissions to this role ----//
                \DB::table('permission_role')->insert([
                    'role_id' => $role->id,
                    'permission_id' => $permission->id,
                ]);
            }
        }

        \Log::channel('custom')->info("Admin role for business has been created.",['business_id' => $bid]);
    }
    //----------------------------------//
    public function addPermissions($bid,$permissions,$uid){
        //----- Make role for Super Admin ----//
        $role = Role::create([
            'title' => 'Super Admin',
            'business_id' => $bid,
        ]);
         //------- Assign role to this User ----//
         \DB::table('role_user')->insert([
            'role_id' => $role->id,
            'user_id' => $uid,
        ]);

        //---- Add permissions to Super Admin ----//
        foreach($permissions as $title){
            if($title){
                $permission = Permission::create([
                    'title' => $title,
                    'business_id' => $bid,
                ]);
                //------- Assign Permissions to this role ----//
                \DB::table('permission_role')->insert([
                    'role_id' => $role->id,
                    'permission_id' => $permission->id,
                ]);
            }
        }

        \Log::channel('custom')->info("Permissions for business has been created.",['business_id' => $bid]);
    }
    //----------------------------------//
    public function makePages($bid){

        for($i=0; $i < 10; $i++){
            $active = 0;
            if( $i == 0 ){
                $name = 'INFORMATION';
                $active = 1;
            }
            elseif( $i == 1 ){
                $name = 'BOOKING'; 
                $active = 1;
            }   
            elseif( $i == 2 ){
                $name = 'EVENTS';
            }
            elseif( $i == 3 ){
                $name = 'PRICES';
            }
            elseif( $i == 4 ){
                $name = 'GDPR'; 
            }
            elseif( $i == 5 ){
                $name = 'NOTIFICATION'; 
            }
            elseif( $i == 6 ){
                $name = 'COVID'; 
            }  
            elseif( $i == 7 ){
                $name = 'INSURANCE'; 
            }  
            elseif( $i == 8 ){
                $name = 'CONTACT'; 
            } 
            elseif( $i == 9 ){
                $name = 'STATUS'; 
            }  

            //----- this for loop is for languages ---
            foreach ( \Config::get('languages') as $key => $val ){
                $content = '';
                \App::setLocale($key);
                if($name == 'GDPR'){
                    if(is_file(storage_path( $key."-gdrp.txt"))){
                        $file = fopen(storage_path( $key."-gdrp.txt"), "r");
                        $content = fread($file,filesize(storage_path( $key."-gdrp.txt")));
                    }
                }
                if($name == 'INFORMATION'){
                    if(is_file(storage_path( $key."-home.txt"))){
                        $file = fopen(storage_path( $key."-home.txt"), "r");
                        $content = fread($file,filesize(storage_path( $key."-home.txt")));
                    }
                }
                if($name == 'INSURANCE'){
                    if(is_file(storage_path( $key."-insurance.txt"))){
                        $file = fopen(storage_path( $key."-insurance.txt"), "r");
                        $content = fread($file,filesize(storage_path( $key."-insurance.txt")));
                    }
                }
                $pages = Website::create([
                    'business_id' => $bid,
                    'title' => __('web.'.$name),
                    'page' => $name,
                    'language' => $key,
                    'is_active' => $active,
                    'content' => $content,
                ]);
            }  
            \Log::channel('custom')->info("Webpages for business has been created.",['business_id' => $bid]);

        }
    }
    //----------------------------------//
    public function makeUser($name,$bid,$role,$language,$email,$password,$permissions){
        $user = User::create([
            'name' => $name,
            'business_id' => $bid,
            'role' => $role,
            'language' => $language,
            'access' => '-1', // For now it's all
            'email' => strtolower($email),
            'password' => \Hash::make($password),
        ]);
        $this->addPermissions($bid,$permissions,$user->id);
        \Log::channel('custom')->info("Super Admin for business has been created.",['business_id' => $bid]);

         //-------- Send Email to admin with credentials ----------
         $brandName = Business::find($bid)->Settings->where('key','email_sender_name')->first(); 
         $business = Business::find($bid);
         // -------  for this user's will change language ------
         \App::setLocale($user->language);
         $subject =  __('emailtxt.user_register_email_subject',['name' => $brandName->value]);
         $content = __('emailtxt.user_register_email_txt',['name' => $user->name,'email' =>$user->email, 'url' => 'https://'.$business->business_name.'.'.config('app.domain').'/login' ,'password' => $password ]);

         if($user->email != ''){
             \Log::channel('custom')->info("Sending email to Super Admin about his account.",['business_id' => $bid, 'subject' => $subject, 'email_text' => $content]);

             $this->dispatch(new SendEmailJob($user->email,$subject,$content,$brandName->value,$bid));
         }
    }
    //----------------------------------//
    public function makePaymentMethods($bid){
        $data = [
            ['title'=>'Mobilepay', 'business_id'=> $bid],
            ['title'=>'Cash', 'business_id'=> $bid],
            ['title'=>'Clip', 'business_id'=> $bid],
            ['title'=>'Insurance', 'business_id'=> $bid],
            ['title'=>'Insurance - New Case', 'business_id'=> $bid],
            ['title'=>'Insurance - Chronicles', 'business_id'=> $bid],
            ['title'=>'Insurance - Completed', 'business_id'=> $bid],
            ['title'=>'Absence', 'business_id'=> $bid],
            ['title'=>'Late Cancellation', 'business_id'=> $bid],
            ['title'=>'Credit', 'business_id'=> $bid],
            ['title'=>'Online', 'business_id'=> $bid],
            ['title'=>'Online Completed', 'business_id'=> $bid],
            ['title'=>'Free Treatment', 'business_id'=> $bid],
        ];
        PaymentMethod::insert($data);
        \Log::channel('custom')->info("Default Payment Methods for business has been created.",['business_id' => $bid]);

    }
    //----------------------------------//
    public function makeTeatmentAreas($bid){
        $rows = [
            ['title'=>'Second Area', 'business_id'=> $bid],
            ['title'=>'Foot', 'business_id'=> $bid],
            ['title'=>'Hip / Plvis', 'business_id'=> $bid],
            ['title'=>'Knee', 'business_id'=> $bid],
            ['title'=>'Lower Back', 'business_id'=> $bid],
            ['title'=>'Neck / Shoulder', 'business_id'=> $bid],
            ['title'=>'Island', 'business_id'=> $bid],
            ['title'=>'UE', 'business_id'=> $bid],
            ['title'=>'Upper Back', 'business_id'=> $bid],
            ['title'=>'Lower Extremerty', 'business_id'=> $bid],
            ['title'=>'Other Area', 'business_id'=> $bid],
            ['title'=>'Upper Extremerty', 'business_id'=> $bid],
        ];

        foreach($rows as $row){
            $id = TreatmentPart::insertGetId($row);
            foreach ( \Config::get('languages') as $key => $val ){
                if ($key == 'en')
                    continue;
                else{
                    \App::setLocale($key);
                    TreatmentPartTranslation::insert([
                        'business_id'=> $bid,
                        'treatment_part_id'=> $id, 
                        'key' => $key,
                        'value' => __('part.'.$row['title']),
                    ]);
                }
            }    
        }
        
        \Log::channel('custom')->info("Default Treatment Part for business has been created.",['business_id' => $bid]);

    }
     //----------------------------------//
    public function makeTeatments($bid){
        $data = [
            ['treatment_name'=>'Massage(30)','business_id'=> $bid,'clips' => 1, 'inter' => 30, 'price' => 1],
            ['treatment_name'=>'Massage(60)','business_id'=> $bid,'clips' => 1, 'inter' => 60, 'price' => 1],
        ];
        Treatment::insert($data);
        \Log::channel('custom')->info("Default Treatments for business has been created.",['business_id' => $bid]);
    }
    //----------------------------------//
    public function makeSurvey($bid){
        $data = $this->surveyData();
        //------ Language loop ------//
        foreach($data as $key => $value){
            //----- Question loop ----//
            foreach($value as $question => $answers){
                $q = new Question(); 
                $q->title = $question;       
                $q->language = $key;       
                $q->business_id = $bid;  
                if($q->save()){
                    //----- Answers loop ----//
                    if(!empty($answers)){
                        foreach($answers as $answer){
                            $op = new Option();
                            $op->value = $answer;       
                            $op->business_id = $bid;
                            $op->question_id = $q->id;
                            $op->language = $key;  
                            $op->save();
                        }
                    }  
                }     
            }
        }
        \Log::channel('custom')->info("Create survey questions and answers.",['business_id' => $bid]);
    }
    //----------------------------------//
    public function makeSettings($bid,$bname){
        $data = [
            ['key'=>'sms_reminder_time','value' => '4' ,'business_id'=> $bid],
            ['key'=>'email_reminder_time','value' => '4' ,'business_id'=> $bid],
            ['key'=>'stop_cancellation','value' => '2' ,'business_id'=> $bid],
            ['key'=>'stop_booking','value' => '0' ,'business_id'=> $bid],
            ['key'=>'survey_percentage','value' => '100' ,'business_id'=> $bid],
            ['key'=>'free_spot_email','value' => 'true' ,'business_id'=> $bid],
            ['key'=>'free_spot_email_for_event','value' => 'false' ,'business_id'=> $bid],
            ['key'=>'clipboard_event','value' => 'false' ,'business_id'=> $bid],
            ['key'=>'clipboard_treatment','value' => 'false' ,'business_id'=> $bid],
            ['key'=>'cpr_emp_fields','value' => 'false' ,'business_id'=> $bid],
            ['key'=>'department','value' => 'false' ,'business_id'=> $bid],
            ['key'=>'email_sender_name','value' => $bname ,'business_id'=> $bid],
            ['key'=>'max_active_bookings','value' => -1 ,'business_id'=> $bid],
            ['key'=>'max_bookings_month','value' => -1 ,'business_id'=> $bid],
            ['key'=>'code_for_booking','value' => 'false' ,'business_id'=> $bid],
            ['key'=>'code','value' => $bname ,'business_id'=> $bid],
            ['key'=>'survey_send_hours','value' => '4' ,'business_id'=> $bid],
            ['key'=>'survey_setting','value' => 'true' ,'business_id'=> $bid],
            ['key'=>'date_format','value' => 'd-m-Y' ,'business_id'=> $bid],
            ['key'=>'sms_setting','value' => 'false' ,'business_id'=> $bid],
            ['key'=>'t_booking_sms','value' => 'false' ,'business_id'=> $bid],
            ['key'=>'t_d_booking_sms','value' => 'false' ,'business_id'=> $bid],
            ['key'=>'t_r_booking_sms','value' => 'false' ,'business_id'=> $bid],
            ['key'=>'e_booking_sms','value' => 'false' ,'business_id'=> $bid],
            ['key'=>'e_d_booking_sms','value' => 'false' ,'business_id'=> $bid],
            ['key'=>'e_r_booking_sms','value' => 'false' ,'business_id'=> $bid],
            ['key'=>'clip_add','value' => 'false' ,'business_id'=> $bid],
            ['key'=>'clip_used','value' => 'false' ,'business_id'=> $bid],
            ['key'=>'clip_restore','value' => 'false' ,'business_id'=> $bid],
            ['key'=>'sms_stop_from','value' => '23:00' ,'business_id'=> $bid],
            ['key'=>'sms_stop_till','value' => '06:00' ,'business_id'=> $bid],
            ['key'=>'time_format','value' => '24' ,'business_id'=> $bid],
            ['key'=>'event_update_email','value' => 'false' ,'business_id'=> $bid],
            ['key'=>'date_update_email','value' => 'false' ,'business_id'=> $bid],
            ['key'=>'survey_duration','value' => 3 ,'business_id'=> $bid],            
            ['key'=>'google_analytics_code','value' => '' ,'business_id'=> $bid],            
            ['key'=>'offer_section_home','value' => 'false' ,'business_id'=> $bid],            
            ['key'=>'offer_url','value' => '' ,'business_id'=> $bid], 
            ['key'=>'landing_page','value' => 'profile' ,'business_id'=> $bid], 
            ['key'=>'graph_duration','value' => 12 ,'business_id'=> $bid], 
            ['key'=>'mobile_pay','value' => 'false' ,'business_id'=> $bid], 
            ['key'=>'mobile_pay_number','value' => '' ,'business_id'=> $bid], 
            ['key'=>'email_for_contact_form','value' => '' ,'business_id'=> $bid], 
            ['key'=>'cpr_emp_fields_insurance','value' => 'false' ,'business_id'=> $bid],
            ['key'=>'mdr_field','value' => 'false' ,'business_id'=> $bid],
            ['key'=>'log_for_email','value' => 'true' ,'business_id'=> $bid],
            ['key'=>'log_for_sms','value' => 'false' ,'business_id'=> $bid],
            ['key'=>'log_for_booking_related_details','value' => 'true' ,'business_id'=> $bid],
            ['key'=>'log_for_event_related_details','value' => 'false' ,'business_id'=> $bid],
            ['key'=>'log_for_date_related_details','value' => 'false' ,'business_id'=> $bid],
            ['key'=>'log_for_card_and_clips','value' => 'false' ,'business_id'=> $bid],
            ['key'=>'log_for_pages','value' => 'false' ,'business_id'=> $bid],
            ['key'=>'log_for_settings','value' => 'false' ,'business_id'=> $bid],
            ['key'=>'log_related_user','value' => 'false' ,'business_id'=> $bid],
            ['key'=>'log_for_survey','value' => 'false' ,'business_id'=> $bid],
            ['key'=>'log_for_roles','value' => 'false' ,'business_id'=> $bid],
            ['key'=>'log_for_journal','value' => 'false' ,'business_id'=> $bid],
            ['key'=>'log_for_login','value' => 'true' ,'business_id'=> $bid],
            ['key'=>'log_for_department','value' => 'false' ,'business_id'=> $bid],
            ['key'=>'log_for_treatment_part','value' => 'false' ,'business_id'=> $bid],
            ['key'=>'log_for_payment_method','value' => 'false' ,'business_id'=> $bid],
            ['key'=>'treatment_free_spot_time','value' => '00:00' ,'business_id'=> $bid],
            ['key'=>'event_free_spot_time','value' => '00:00' ,'business_id'=> $bid],
            ['key'=>'system_language','value' => 'dk' ,'business_id'=> $bid],
            ['key'=>'event_cancel','value' => '0' ,'business_id'=> $bid],
        ];
        Setting::insert($data);

        \Log::channel('custom')->info("Default Settings for business has been created.",['business_id' => $bid]);

    }
    //----------------------------------//
    public function edit($subdomain,$id){
        $business =  Business::select()->where('id',$id)->first();
        $sms = Business::find($id)->Settings->where('key','sms_setting')->first(); 
        $permissions = array();
        $file = fopen(storage_path("permissions.txt"), "r");
        while(!feof($file)) {
            array_push($permissions,fgets($file));
        } 
        fclose($file);
        return view('business.edit')->with(compact('business','sms','permissions'));
    }
    //----------------------------------//
    public function editSave(Request $request){
        Validator::make($request->all(), [
            'id'            => ['required'],
            'permissions'   => ['required'],
        ])->validate();


        $business = Business::find($request->id);
        $this->permissionEditAndAssign($request->id,$request->permissions);

        if($request->status)
            $business->is_active = 1;
        else
            $business->is_active = 0;
        
        if($business->save()){
            $setting = Setting::where('business_id',$request->id)->where('key','sms_setting')->first();
            if($request->sms)
                $setting->value = 'true';
            else
                $setting->value = 'false';
            $setting->save();

            $request->session()->flash('success',__('business.bhbus'));

            \Log::channel('custom')->info("Business has been updated.",['business_id' => $request->id]);

        }    
        else{
            $request->session()->flash('error',__('business.tiaetub'));
            \Log::channel('custom')->error("Error to update Business.",['business_id' => $request->id]);
        }
        return \Redirect::back();
    }

    public function generateSettings(){
        $business = Business::find(Auth::user()->business_id);
        Setting::where('business_id',$business->id)->delete();
        $data = [
            ['key'=>'sms_reminder_time','value' => '4' ,'business_id'=> $business->id],
            ['key'=>'email_reminder_time','value' => '4' ,'business_id'=> $business->id],
            ['key'=>'stop_cancellation','value' => '2' ,'business_id'=> $business->id],
            ['key'=>'stop_booking','value' => '0' ,'business_id'=> $business->id],
            ['key'=>'survey_percentage','value' => '100' ,'business_id'=> $business->id],
            ['key'=>'free_spot_email','value' => 'true' ,'business_id'=> $business->id],
            ['key'=>'free_spot_email_for_event','value' => 'false' ,'business_id'=> $business->id],
            ['key'=>'clipboard_event','value' => 'false' ,'business_id'=> $business->id],
            ['key'=>'clipboard_treatment','value' => 'false' ,'business_id'=> $business->id],
            ['key'=>'cpr_emp_fields','value' => 'false' ,'business_id'=> $business->id],
            ['key'=>'department','value' => 'false' ,'business_id'=> $business->id],
            ['key'=>'email_sender_name','value' => $business->name ,'business_id'=> $business->id],
            ['key'=>'max_active_bookings','value' => -1 ,'business_id'=> $business->id],
            ['key'=>'max_bookings_month','value' => -1 ,'business_id'=> $business->id],
            ['key'=>'code_for_booking','value' => 'false' ,'business_id'=> $business->id],
            ['key'=>'code','value' => $business->name ,'business_id'=> $business->id],
            ['key'=>'survey_send_hours','value' => '4' ,'business_id'=> $business->id],
            ['key'=>'survey_setting','value' => 'true' ,'business_id'=> $business->id],
            ['key'=>'date_format','value' => 'd-m-Y' ,'business_id'=> $business->id],
            ['key'=>'sms_setting','value' => 'true' ,'business_id'=> $business->id],
            ['key'=>'t_booking_sms','value' => 'false' ,'business_id'=> $business->id],
            ['key'=>'t_d_booking_sms','value' => 'false' ,'business_id'=> $business->id],
            ['key'=>'t_r_booking_sms','value' => 'false' ,'business_id'=> $business->id],
            ['key'=>'e_booking_sms','value' => 'false' ,'business_id'=> $business->id],
            ['key'=>'e_d_booking_sms','value' => 'false' ,'business_id'=> $business->id],
            ['key'=>'e_r_booking_sms','value' => 'false' ,'business_id'=> $business->id],
            ['key'=>'clip_add','value' => 'false' ,'business_id'=> $business->id],
            ['key'=>'clip_used','value' => 'false' ,'business_id'=> $business->id],
            ['key'=>'clip_restore','value' => 'false' ,'business_id'=> $business->id],
            ['key'=>'sms_stop_from','value' => '23:00' ,'business_id'=> $business->id],
            ['key'=>'sms_stop_till','value' => '06:00' ,'business_id'=> $business->id],
            ['key'=>'time_format','value' => '24' ,'business_id'=> $business->id],
            ['key'=>'event_update_email','value' => 'false' ,'business_id'=> $business->id],
            ['key'=>'date_update_email','value' => 'false' ,'business_id'=> $business->id],
            ['key'=>'survey_duration','value' => 3,'business_id'=> $business->id],
            ['key'=>'google_analytics_code','value' => '' ,'business_id'=> $business->id],            
            ['key'=>'offer_section_home','value' => 'false' ,'business_id'=> $business->id],            
            ['key'=>'offer_url','value' => '' ,'business_id'=> $business->id], 
            ['key'=>'landing_page','value' => 'profile' ,'business_id'=> $business->id], 
            ['key'=>'graph_duration','value' => 12 ,'business_id'=> $business->id], 
            ['key'=>'mobile_pay','value' => 'false' ,'business_id'=> $business->id], 
            ['key'=>'mobile_pay_number','value' => '' ,'business_id'=> $business->id], 
            ['key'=>'email_for_contact_form','value' => '' ,'business_id'=> $business->id],
            ['key'=>'cpr_emp_fields_insurance','value' => 'false' ,'business_id'=> $business->id],
            ['key'=>'mdr_field','value' => 'false' ,'business_id'=> $business->id],
            ['key'=>'receipt_option','value' => 'false' ,'business_id'=> $business->id],
            ['key'=>'log_for_email','value' => 'true' ,'business_id'=> $business->id],
            ['key'=>'log_for_sms','value' => 'false' ,'business_id'=> $business->id],
            ['key'=>'log_for_booking_related_details','value' => 'true' ,'business_id'=> $business->id],
            ['key'=>'log_for_event_related_details','value' => 'false' ,'business_id'=> $business->id],
            ['key'=>'log_for_date_related_details','value' => 'false' ,'business_id'=> $business->id],
            ['key'=>'log_for_card_and_clips','value' => 'false' ,'business_id'=> $business->id],
            ['key'=>'log_for_pages','value' => 'false' ,'business_id'=> $business->id],
            ['key'=>'log_for_settings','value' => 'false' ,'business_id'=> $business->id],
            ['key'=>'log_related_user','value' => 'false' ,'business_id'=> $business->id],
            ['key'=>'log_for_survey','value' => 'false' ,'business_id'=> $business->id],
            ['key'=>'log_for_roles','value' => 'false' ,'business_id'=> $business->id],
            ['key'=>'log_for_journal','value' => 'false' ,'business_id'=> $business->id],
            ['key'=>'log_for_login','value' => 'true' ,'business_id'=> $business->id],
            ['key'=>'log_for_department','value' => 'false' ,'business_id'=> $business->id],
            ['key'=>'log_for_treatment_part','value' => 'false' ,'business_id'=> $business->id],
            ['key'=>'log_for_payment_method','value' => 'false' ,'business_id'=> $business->id],
            ['key'=>'treatment_free_spot_time','value' => '00:00' ,'business_id'=> $business->id],
            ['key'=>'event_free_spot_time','value' => '00:00' ,'business_id'=> $business->id],
            ['key'=>'system_language','value' => 'dk' ,'business_id'=> $business->id],
            ['key'=>'event_cancel','value' => '0' ,'business_id'=> $business->id],
        ];
        Setting::insert($data);
        \Log::channel('custom')->info("Refresh business settings.",['business_id' => $business->id]);

        return \Redirect::back();
    }

    public function generatePages(){
        $business = Business::find(Auth::user()->business_id);
        //Website::where('business_id',$business->id)->delete();

        for($i=0; $i < 10; $i++){
            $active = 0;
            if( $i == 0 ){
                $name = 'INFORMATION';
                $active = 1;
            }
            elseif( $i == 1 ){
                $name = 'BOOKING'; 
                $active = 1;
            }   
            elseif( $i == 2 ){
                $name = 'EVENTS';
            }
            elseif( $i == 3 ){
                $name = 'PRICES';
            }
            elseif( $i == 4 ){
                $name = 'GDPR'; 
            }
            elseif( $i == 5 ){
                $name = 'NOTIFICATION'; 
            } 
            elseif( $i == 6 ){
                $name = 'COVID'; 
            } 
            elseif( $i == 7 ){
                $name = 'INSURANCE'; 
            } 
            elseif( $i == 8 ){
                $name = 'CONTACT'; 
            } 
            elseif( $i == 9 ){
                $name = 'STATUS'; 
            }           

            //----- this for loop is for languages ---
            foreach ( \Config::get('languages') as $key => $val ){
                $content = '';
                \App::setLocale($key);
                if($name == 'GDPR'){
                    if(is_file(storage_path( $key."-gdrp.txt"))){
                        $file = fopen(storage_path( $key."-gdrp.txt"), "r");
                        $content = fread($file,filesize(storage_path( $key."-gdrp.txt")));
                    }
                }
                if($name == 'INFORMATION'){
                    if(is_file(storage_path( $key."-home.txt"))){
                        $file = fopen(storage_path( $key."-home.txt"), "r");
                        $content = fread($file,filesize(storage_path( $key."-home.txt")));
                    }
                }
                if($name == 'INSURANCE'){
                    if(is_file(storage_path( $key."-insurance.txt"))){
                        $file = fopen(storage_path( $key."-insurance.txt"), "r");
                        $content = fread($file,filesize(storage_path( $key."-insurance.txt")));
                    }
                }
                $check = Website::where([['business_id',$business->id],['page',$name],['language',$key]])->get()->count();
                \Log::channel('custom')->info("Page name.",['name' => $name,'language' => $key]);
                if($check == 0){
                    $pages = Website::create([
                        'business_id' => $business->id,
                        'title' => __('web.'.$name),
                        'page' => $name,
                        'language' => $key,
                        'is_active' => $active,
                        'content' => $content,
                    ]);
                }
            }    
        }
        \Log::channel('custom')->info("Refresh business webpages.",['business_id' => $business->id]);

        return \Redirect::back();
    }

    public function permissionEditAndAssign($bid,$permissions){
        //---- Delete role so that all permission link with this role removed , and user relation with this role also removed---//
        $role = \DB::table('roles')->where('business_id',$bid)->where('title','Super Admin')->first();
        $users = User::where('business_id',$bid)->where('role','Super Admin')->orWhere('role',$role->id)->get();

        \DB::table('roles')->where('id',$role->id)->delete();

        $this->deleteAndAddNewPermissions($bid,$permissions);

        //-------- Create Super Admin role and link with user --------//
        $newRole = Role::create([
            'title' => 'Super Admin',
            'business_id' => $bid
        ]);

        foreach($users as $user){

            \DB::table('role_user')->insert([
                'role_id' => $newRole->id,
                'user_id' => $user->id
            ]);
            
            if($user->role == $role->id){
                $user->role = $newRole->id;
                $user->save();
            }
        }
        //---------- Assign all permission to this role now ------//
        $pre_permissions = Permission::where('business_id',$bid)->get();
        $data = array();
        foreach( $pre_permissions as $val ){
            $data[] = [
                'permission_id' => $val->id,
                'role_id' => $newRole->id
            ];
        }
        \DB::table('permission_role')->insert($data);
        
    }

    public function surveyData(){
        return [
            'en' => [
                'Please rate your level of satisfaction at the inhouse clinic' =>  ['Very Satisfied','Satisfied','Neither nor','Unsatisfied','Very unsatisfied'],
                'How satisfied are you with the level of information you received from your therapist?' => ['Very Satisfied','Satisfied','Neither nor','Unsatisfied','Very unsatisfied'],
                'What was the purpose of your visit?' => ['wellbeing','Prevention of genes and tenderness','A combination of well-being and treatment','Treatment of an injury','Treatment of an acute injury'],
                'Feel free to add a comment or feed back in the box below' => '',
            ],
            'dk' => [
                'Hvor tilfreds var du med dit besøg i in house klinikken?' =>  ['Meget tilfreds','Tilfreds','Hverken eller','Utilfreds','Meget utilfreds'],
                'Hvor tilfreds var du med den information du fik af din behandler?' => ['Meget tilfreds','Tilfreds','Hverken eller','Utilfreds','Meget utilfreds'],
                'Hvad var formålet med din konsultation' => ['Velvære','Forebyggelse af gener og ømhed','En kombination af forebyggelse og behandling','Behandling af en skade','Behandling af en akut skade'],
                'Hvis du har yderligere kommentarer til din besvarelse, ris eller ros, må du meget gerne skrive det i nedenstående boks.' => '',
            ],
        ];
    }

    public function adminPermissions(){
        return [
            'Customer Create','Customer Edit','Customer View','Customer Delete','Journal List View','Journal Open','Journal Payment/Treatment Update','Journal Notes View','Journal Notes Create','Journal Notes Edit','Email List View','Email Create','Email View','Email Delete','Reports Users View','Reports Booking View','Reports Date View','Reports Unique User View','Reports Srvey View','Stats View','Website Pages View','Website Pages Edit','Brand Details View','Event Book','Event Booking Delete','Event Booking View','Date Book','Date Bookings View','Date Booking Delete','Dashboard Graphs'
        ];
    }

    public function deleteAndAddNewPermissions($bid,$permissions){
        $pre_permissions = Permission::where('business_id',$bid)->get();
        $deleteThesePermissions = array_diff($pre_permissions->pluck('title')->toArray(),$permissions);

        Permission::where('business_id',$bid)->whereIn('title',$deleteThesePermissions)->delete();

       //------- Insert new permissions --------//
        $pre_permissions = Permission::where('business_id',$bid)->get();
        $addThesePermissions = array_diff($permissions,$pre_permissions->pluck('title')->toArray());
        $data = array();
        foreach( $addThesePermissions as $key => $val ){
            $data[] = [
                'title' => $val,
                'business_id' => $bid
            ];
        }
        Permission::insert($data);
    }

    public function temp(){
        $user = User::where('role','Owner')->first();

        $permissions = array();
        $file = fopen(storage_path("permissions.txt"), "r");
        while(!feof($file)) {
            array_push($permissions,fgets($file));
        } 
        fclose($file);
        Permission::where('business_id',$user->business_id)->delete();
        foreach( $permissions as $key => $val ){
            $data[] = [
                'title' => trim($val),
                'business_id' => $user->business_id
            ];
        }
        Permission::insert($data);

        $role = Role::create([
            'title' => 'Owner',
            'business_id' => $user->business_id
        ]);

        \DB::table('role_user')->insert([
            'role_id' => $role->id,
            'user_id' => $user->id
        ]);
        $perms = Permission::where('business_id',$user->business_id)->get();
        $data = array();
        foreach( $perms as $val ){
            $data[] = [
                'permission_id' => $val->id,
                'role_id' => $role->id
            ];
        }
        \DB::table('permission_role')->insert($data);
    }

    public function changeUser(Request $request){
        Validator::make($request->all(), [
            'domain'        => ['required'],
            'superadmin'    => ['required'],
        ])->validate();

        $url = '//'.$request->domain.'.'.config('app.domain').'/other-account/'.$request->superadmin.'/medium';
        session()->flash('newurl', $url);
        return redirect()->back();

    }
    
    public function switch($subdomain,$user,$medium=''){
        if($medium != 'medium'){
            return redirect('/login');
        }

        $business  = Business::where('business_name',$subdomain)->first();
        $landing_page = Business::find($business->id)->Settings->where('key','landing_page')->first();        
        $u = User::where(\DB::raw('md5(id)') , $user)->first();
        Auth::login($u);
        if($u->role != 'Customer')
            return redirect('/'.$landing_page->value);
        else{
            $eventBooking = Permission::where('business_id',$business->id)->where('title','Event Create')->count();
            $treatmentBooking = Permission::where('business_id',$business->id)->where('title','Date Create')->count();
            if($treatmentBooking > 0){
                return redirect('MyTreatmentBookings');
            }else{
                return redirect('myEventBookings');
            }
        }
    }

}

