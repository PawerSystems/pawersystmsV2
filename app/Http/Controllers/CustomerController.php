<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\Business;
use App\Models\Event;
use App\Models\EventSlot;
use App\Models\Treatment;
use App\Models\TreatmentSlot;
use App\Models\Date;
use App\Models\Card;
use App\Jobs\SendEmailJob;


class CustomerController extends Controller
{
    public function index(){
        abort_unless(\Gate::any(['Customer View','Customer Edit','Customer Delete']),403);
        $customers = User::where('role','Customer')->where('is_active',1)->where('business_id',Auth::user()->business_id)->orderBy('name','ASC')->get();
        $settings = Business::find(Auth::user()->business_id)->Settings;

        return view('customer.list', compact('customers','settings'));
    }

    public function disableCustomers(){
        abort_unless(\Gate::any(['Customer View','Customer Edit','Customer Delete']),403);
        $customers = User::where('role','Customer')->where('is_active',0)->where('business_id',Auth::user()->business_id)->orderBy('name','ASC')->get();
        $settings = Business::find(Auth::user()->business_id)->Settings;

        return view('customer.list', compact('customers','settings'));
    }

    public function create(){
        abort_unless(\Gate::allows('Customer Create'),403);
        $countries = $this->countries();
        $settings = Business::find(Auth::user()->business_id)->Settings;

        return view('customer.create',compact('settings','countries'));
    }

    public function save(Request $request){
        abort_unless(\Gate::allows('Customer Create'),403);

        $validatedData = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255'],
            'number' => ['max:255','required'],
            'language' => ['required', 'string'],
            'image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $already = User::where('email',strtolower($request->email))->Where('business_id',Auth::user()->business_id)->count();

        if( $already ){
            \Log::channel('custom')->info("Trying to add new customer, but email aleady exist in business.",['business_id' => Auth::user()->business_id, 'email' => $request->email]);

            $request->session()->flash('error',__('customer.uarwte'));
        }
        else{ 
            $imageName = '';  
            if( !empty($request->image) ){
                $imageName = time().'.'.$request->image->extension();  
                $request->image->move(public_path('images'), $imageName);
            }

            $password = Str::random(8);

            $cus = User::create([
                'name' => $request->name,
                'email' => strtolower($request->email),
                'number' => $request->number,
                'country_id' => $request->country,
                'language' => $request->language,
                'birth_year' => $request->birthYear,
                'gender' => $request->gender,
                'cprnr' => $request->cprnr,
                'mednr' => $request->mednr,
                'role' => 'Customer',
                'access' => '-1', //----  For now
                'business_id' => Auth::user()->business_id,
                'password' => \Hash::make($password),
                'profile_photo_path' => $imageName,
                'is_therapist' => 0,
            ]);

            if($cus->id){
                $request->session()->flash('success',__('customer.customer_successfully_egistered'));
                \Log::channel('custom')->info("New customer added.",['business_id' => Auth::user()->business_id, 'user_id' => $cus->id]);

                //------ Add log in DB for location -----//
                $type = "log_related_user";
                $txt = "New customer has been added. <br> <b>Email: ".$cus->email."</b>";
                $this->addLocationLog($type,$txt);

                //-------- Send Email to user with credentials ----------
                $brandName = Business::find(Auth::user()->business_id)->Settings->where('key','email_sender_name')->first(); 

                // -------  for this user's will change language ------
                \App::setLocale($cus->language);

                $url = url("/login");
                $subject =  __('emailtxt.user_register_email_subject',['name' => $brandName->value]);
                $content =  __('emailtxt.user_register_email_txt',['name' =>$cus->name, 'email' => $cus->email, 'url' => $url,'password' => $password ]);

                if($cus->email != ''){
                    \Log::channel('custom')->info("Sending email to customer with login credentials.",['business_id' => Auth::user()->business_id, 'user_id' => $cus->id, 'subject' => $subject , 'email_text' => $content]);

                    $this->dispatch(new SendEmailJob($cus->email,$subject,$content,$brandName->value,Auth::user()->business_id));
                }
            }    
            else{
                $request->session()->flash('error',__('customer.tiaetac'));
                \Log::channel('custom')->error("Error to add new customer.",['business_id' => Auth::user()->business_id, 'controller' => 'CustomerController@save']);
            }
        }

        return \Redirect::back();
    }

    public function add(Request $request){
        abort_unless(\Gate::allows('Customer Create'),403);

        $validatedData = $request->validate([
            'cname' => ['required', 'string', 'max:255'],
            'cemail' => ['required', 'string', 'email', 'max:255'],
            'cnumber' => ['max:255'.'required'],
            'clanguage' => ['required', 'string'],
        ]);
        
        $already = User::where('email',strtolower($request->cemail))->where('business_id','=',Auth::user()->business_id)->count();
        $data = array();

        if( $already ){
            $data['status'] = 'exist';
        }
        else{ 
            $password = Str::random(8);

            $customer = new User();
            $customer->name = $request->cname;
            $customer->email = strtolower($request->cemail);
            $customer->number = $request->cnumber;
            $customer->country_id = $request->ccountry;
            $customer->language = $request->clanguage;
            $customer->birth_year = $request->birthYear;
            $customer->cprnr = $request->cprnr;
            $customer->mednr = $request->mednr;
            $customer->role = 'Customer';
            $customer->business_id = Auth::user()->business_id;
            $customer->password = \Hash::make($password);
            $customer->access = '-1';
  

            if($customer->save()){
                $data['cid'] = $customer->id;
                $data['status'] = 'success';
                \Log::channel('custom')->info("Customer added successfully.",['business_id' => Auth::user()->business_id, 'user_id' => $customer->id]);

                //------ Add log in DB for location -----//
                $type = "log_related_user";
                $txt = "New customer has been added. <br> <b>Email: ".$customer->email."</b>";
                $this->addLocationLog($type,$txt);

                //-------- Send Email to user with credentials ----------
                $brandName = Business::find(Auth::user()->business_id)->Settings->where('key','email_sender_name')->first(); 


                // -------  for this user's will change language ------
                \App::setLocale($customer->language);

                $url = url("/login");
                $subject =  __('emailtxt.user_register_email_subject',['name' => $brandName->value]);
                $content =  __('emailtxt.user_register_email_txt',['name' =>$customer->name, 'email' => $customer->email, 'url' => $url, 'password' => $password]);
                
                if($customer->email != ''){
                    \Log::channel('custom')->info("Sending email to customer with login credentials.",['business_id' => Auth::user()->business_id, 'user_id' => $customer->id, 'subject' => $subject , 'email_text' => $content]);

                    $this->dispatch(new SendEmailJob($customer->email,$subject,$content,$brandName->value,Auth::user()->business_id));
                }
            }
            else{
                $data['status'] = 'error';
                \Log::channel('custom')->error("Error to add new customer.",['business_id' => Auth::user()->business_id, 'controller' => 'CustomerController@add']);
            }
        }
        return \Response::json($data);
    }

    public function edit($subdomain,$id){
        $customer = Business::find(Auth::user()->business_id)->user->where(\DB::raw('md5(id)') , $id)->first();
        $countries = $this->countries();
        $settings = Business::find(Auth::user()->business_id)->Settings;
        $roles = Business::find(Auth::user()->business_id)->Roles;

        return view('customer.edit',compact('settings','customer','countries','roles'));
    }

    public function update(Request $request){
        abort_unless(\Gate::allows('Customer Edit'),403);

        $validatedData = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255'],
            'language' => ['required', 'string'],
            'number' => ['max:255','required'],
            'image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $customer = User::find($request->id);
        $check = User::where([['email',strtolower($request->email)],['id','!=',$request->id],['business_id',Auth::user()->business_id]])->count();
        if( $check ){
            \Log::channel('custom')->info("Same email already exist in system so can not use this email.",['business_id' => Auth::user()->business_id, 'email' => $request->email]);

            $request->session()->flash('error',__('customer.uarwte'));
            return \Redirect::back();
        }
        if( !empty($request->image) ){
            $imageName = time().'.'.$request->image->extension();  
            $request->image->move(public_path('images'), $imageName);
            $customer->profile_photo_path = $imageName;
        }

        $customer->name = $request->name;
        $customer->email = strtolower($request->email);
        $customer->language = $request->language;
        $customer->number = $request->number;
        $customer->country_id = $request->country;
        $customer->cprnr = $request->cprnr;
        $customer->mednr = $request->mednr;
        $customer->role = $request->role;
        
        if($customer->save()){

            if($request->role != 'Customer'){
                \DB::table('role_user')->insert([
                    'user_id' => $customer->id,
                    'role_id' => $request->role,
                ]);
            }

            //------ Add log in DB for location -----//
            $type = "log_related_user";
            $txt = "Customer has been updated. <br> <b>Email: ".$customer->email."</b>";
            $this->addLocationLog($type,$txt);

            \Log::channel('custom')->info("Customer data has been updated.",['business_id' => Auth::user()->business_id, 'user_id' => $customer->id]);
            $request->session()->flash('success',__('customer.dhbus'));
        }
        else{
            \Log::channel('custom')->error("Error to update customer data.",['business_id' => Auth::user()->business_id, 'user_id' => $customer->id]);
            $request->session()->flash('error',__('customer.tiaetud'));
            return \Redirect::back();
        }
        return \Redirect::route('customerlist',session('business_name'));
    }

    public function delete(Request $request){
        abort_unless(\Gate::allows('Customer Delete'),403);
        $validatedData = $request->validate([
            'id' => ['required'],
        ]);      
        
        $treatments = TreatmentSlot::where(\DB::raw('md5(user_id)') , $request->id)->where('is_active',1)->get()->count();
        $events = EventSlot::where(\DB::raw('md5(user_id)') , $request->id)->get()->where('is_active',1)->count();
        $cards = Card::where(\DB::raw('md5(user_id)') , $request->id)->get()->where('is_active',1)->count();

        if($treatments > 0 || $events > 0 || $cards > 0){
            $data['status'] = 'exist'; 
        }else{
            $user = User::where(\DB::raw('md5(id)'), $request->id)->first();
            //------ Add log in DB for location -----//
            $type = "log_related_user";
            $txt = "Customer has been deleted. <br> <b>Email: ".$user->email."</b>";
            $this->addLocationLog($type,$txt);

            User::where(\DB::raw('md5(id)'), $request->id)->delete();
            $data['status'] = 'success'; 
        }
        
        return json_encode($data); 

    }

    public function changePass($subdomain,$id){
        abort_unless(\Gate::allows('Customer Edit'),403);

        $customer = Business::find(Auth::user()->business_id)->user->where(\DB::raw('md5(id)') , $id)->first();
        return view('customer.pass',compact('customer'));
    }

    public function updatePass(Request $request){
        abort_unless(\Gate::allows('Customer Edit'),403);

        $validatedData = $request->validate([
            'id' => ['required', 'string', 'max:255'],
            'password' => ['required','min:8','same:password_confirmation'],
        ]);
                
        $customer = User::find($request->id);
        $customer->password = \Hash::make($request->password);

        if($customer->save()){
            $request->session()->flash('success',__('customer.dhbus'));
            \Log::channel('custom')->info("Customer password has been updated.",['business_id' => Auth::user()->business_id, 'user_id' => $customer->id]);
            
            //------ Add log in DB for location -----//
            $type = "log_related_user";
            $txt = "Customer password has been updated. <br> <b>Email: ".$customer->email."</b>";
            $this->addLocationLog($type,$txt);

            // -------  for this user's will change language ------
            \App::setLocale($customer->language);

            //-------- Send Email to user with credentials ----------
            $brandName = Business::find(Auth::user()->business_id)->Settings->where('key','email_sender_name')->first(); 

            $url = url("/login");
            $subject =  __('emailtxt.user_password_update_email_subject',['name' => $brandName->value]);
            $content =  __('emailtxt.user_password_update_email_txt',['name' =>$customer->name, 'email' => $customer->email, 'url' => $url, 'password' => $request->password]);
            $this->dispatch(new SendEmailJob($customer->email,$subject,$content,$brandName->value,Auth::user()->business_id));
            
        }
        else{
            \Log::channel('custom')->error("Error to update Customer passwod.",['business_id' => Auth::user()->business_id, 'user_id' => $customer->id]);

            $request->session()->flash('error',__('customer.tiaetud'));
        }
        return \Redirect::back();
    }

    public function myTreatmentBooking(){
        $dates = Date::where('date','>=',date('Y-m-d'))->where('business_id',Auth::user()->business_id)->pluck('id');
        $bookings = TreatmentSlot::where([['user_id',Auth::user()->id],['business_id',Auth::user()->business_id],['is_active',1],['parent_slot',NULL]])->whereIn('date_id',$dates)->orderBy('id','DESC')->get();
        $dateFormat = Business::find(Auth::user()->business_id)->Settings->where('key','date_format')->first(); 
        $timeFormat = Business::find(Auth::user()->business_id)->Settings->where('key','time_format')->first(); 

        return view('customer.treatment_bookings', ['bookings' => $bookings,'dateFormat'=>$dateFormat->value,'timeFormat'=>$timeFormat->value]);
    }

    public function myEventBooking(){
        $events = Event::where('date','>=',date('Y-m-d'))->where('business_id',Auth::user()->business_id)->pluck('id');
        $bookings = EventSlot::where([['user_id',Auth::user()->id],['business_id',Auth::user()->business_id],['is_active',1]])->whereIn('event_id', $events)->orderBy('id','DESC')->get();
        $dateFormat = Business::find(Auth::user()->business_id)->Settings->where('key','date_format')->first(); 
        $timeFormat = Business::find(Auth::user()->business_id)->Settings->where('key','time_format')->first(); 

        return view('customer.event_bookings', ['bookings' => $bookings,'dateFormat'=>$dateFormat->value,'timeFormat'=>$timeFormat->value]);
    }

    public function myCards(){
        $cards = Card::where([['user_id',Auth::user()->id],['business_id',Auth::user()->business_id],['is_active',1]])->orderBy('id','DESC')->get();
        $dateFormat = Business::find(Auth::user()->business_id)->Settings->where('key','date_format')->first(); 

        return view('customer.card_list', ['cards' => $cards,'dateFormat'=>$dateFormat->value]);
    }
}
