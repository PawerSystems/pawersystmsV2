<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Business;
use App\Jobs\SendEmailJob;
use App\Models\Event;
use App\Models\EventSlot;
use App\Models\TreatmentSlot;
use App\Models\Date;
use App\Models\Card;
use App\Models\Journal;


class AdminController extends Controller
{
    public function index(){
        abort_unless(\Gate::allows('Users View'),403);

        $admins = User::where('role','!=','Customer')->where('is_active',1)->where('business_id',Auth::user()->business_id)->where('email','!=','tbilal866@gmail.com')->where('email','!=','paw.nielsen@pawersystems.com')->where('email','!=','paw.nielsen@pawersystems.dk')->orderBy('name','ASC')->get();
        $settings = Business::find(Auth::user()->business_id)->Settings;

        return view('admin.list', compact('admins','settings'));
    }

    public function disableUsers(){
        abort_unless(\Gate::allows('Users View'),403);

        $admins = User::where('role','!=','Customer')->where('is_active',0)->where('business_id',Auth::user()->business_id)->where('email','!=','tbilal866@gmail.com')->where('email','!=','paw.nielsen@pawersystems.com')->where('email','!=','paw.nielsen@pawersystems.dk')->orderBy('name','ASC')->get();
        $settings = Business::find(Auth::user()->business_id)->Settings;
        $totalAdmins = User::where('role','!=','Customer')->where('is_active',0)->where('business_id',Auth::user()->business_id)->count();

        return view('admin.list', compact('admins','settings','totalAdmins'));
    }

    public function create(){
        abort_unless(\Gate::allows('Users Create'),403);
        $roles = Business::find(Auth::user()->business_id)->Roles;
        $countries = $this->countries();
        $settings = Business::find(Auth::user()->business_id)->Settings;

        return view('admin.create', compact('settings','countries','roles'));
    }

    public function save(Request $request){
        abort_unless(\Gate::allows('Users Create'),403);

        $validatedData = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255'],
            'number' => ['max:255','required'],
            'role'  => ['required'],
            'language'  => ['required'],
            'image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        // 'password' => ['required','min:8','same:password_confirmation'],
        ]);

        $already = User::where('email',strtolower($request->email))->where('business_id',Auth::user()->business_id)->count();

        if( $already ){
            $request->session()->flash('error',__('users.user_already_register_with_this_email'));
        }
        else{ 
            $imageName = '';  
            if( !empty($request->image) ){
                $imageName = time().'.'.$request->image->extension();  
                $request->image->move(public_path('images'), $imageName);
            }

            $password = Str::random(8);

            if($request->is_therapist)
                $therapist = 1;
            else 
                $therapist = 0;   

            $adm = User::create([
                'created_by' => auth()->user()->id,
                'name' => $request->name,
                'email' => strtolower($request->email),
                'number' => $request->number,
                'country_id' => $request->country,
                'language' => $request->language,
                'birth_year' => $request->birthYear,
                'gender' => $request->gender,
                'cprnr' => $request->cprnr,
                'mednr' => $request->mednr,
                'role' => $request->role,
                'free_txt' => $request->text,
                'access' => '-1', //----  For now
                'business_id' => Auth::user()->business_id,
                'password' => \Hash::make($password),
                'profile_photo_path' => $imageName,
                'is_therapist' => $therapist,
            ]);

            if($adm->id){

                \DB::table('role_user')->insert([
                    'role_id' => $request->role,
                    'user_id' => $adm->id,
                ]);

                $request->session()->flash('success',__('users.admin_successfully_registered'));
                \Log::channel('custom')->info("Admin successfully Registered!",['business_id' => Auth::user()->business_id]);

                //------ Add log in DB for location -----//
                $type = "log_related_user";
                $txt = "User has been registered. <br> <b>Email: ".$adm->email."</b>";
                $this->addLocationLog($type,$txt);


                //-------- Send Email to admin with credentials ----------
                $brandName = Business::find(Auth::user()->business_id)->Settings->where('key','email_sender_name')->first(); 

                // -------  for this user's will change language ------
                \App::setLocale($adm->language);
                $subject =  __('emailtxt.user_register_email_subject',['name' => $brandName->value]);
                $content = __('emailtxt.user_register_email_txt',['name' => $adm->name,'email' =>$adm->email, 'url' => url("/login") ,'password' => $password ]);

                if($adm->email != ''){
                    \Log::channel('custom')->info("Sending email to Admin about his account.",['business_id' => Auth::user()->business_id, 'subject' => $subject, 'email_text' => $content]);

                    $this->dispatch(new SendEmailJob($adm->email,$subject,$content,$brandName->value,Auth::user()->business_id));
                }
            }    
            else{
                $request->session()->flash('error',__('users.there_is_an_error_to_add_admin'));
                \Log::channel('custom')->error("There is an error to add Admin!",['business_id' => Auth::user()->business_id]);
            }
        }

        return \Redirect::back();
    }

    public function edit($subdomain,$id){
        abort_unless(\Gate::allows('Users Edit'),403);

        $admin = Business::find(Auth::user()->business_id)->user->where(\DB::raw('md5(id)') , $id)->first();
        $roles = Business::find(Auth::user()->business_id)->Roles;
        $settings = Business::find(Auth::user()->business_id)->Settings;

        $countries = $this->countries();

        return view('admin.edit',compact('settings','admin','countries','roles'));
    }

    public function update(Request $request){
        abort_unless(\Gate::allows('Users Edit'),403);

        $validatedData = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255'],
            'number' => ['max:255','required'],
            'role'  => ['required'],
            'language'  => ['required'],
            'image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $admin = User::find($request->id);

        $date = Date::where('user_id',$admin->id)->get()->where('is_active',1)->count();
        $event = Event::where('user_id',$admin->id)->get()->where('is_active',1)->count();
        $journals = Journal::where('user_id',$admin->id)->get()->where('is_active',1)->count();
        if($request->role == 'Customer'){
            if($date > 0 || $event > 0 ||  $journals > 0){
                $request->session()->flash('error',__('keywords.abeissycndic'));
                return \Redirect::back();
            }
        }

        $check = User::where([['business_id',Auth::user()->business_id],['email',strtolower($request->email)],['id','!=',$request->id]])->count();
        if( $check ){
            $request->session()->flash('error',__('users.user_already_register_with_this_email'));
            return \Redirect::back();
        }

        if( !empty($request->image) ){
            $imageName = time().'.'.$request->image->extension();  
            $request->image->move(public_path('images'), $imageName);
            $admin->profile_photo_path = $imageName;
        }

        $admin->name = $request->name;
        $admin->email = strtolower($request->email);
        $admin->number = $request->number;
        $admin->country_id = $request->country;
        $admin->language = $request->language;
        $admin->birth_year = $request->birthYear;
        $admin->gender = $request->gender;
        $admin->cprnr = $request->cprnr;
        $admin->mednr = $request->mednr;
        $admin->role = $request->role;
        $admin->free_txt = $request->text;
        if($request->is_therapist)
            $admin->is_therapist = 1;
        else    
            $admin->is_therapist = 0;

        if($admin->save()){
            if($request->role == 'Customer'){
                \DB::table('role_user')->where('user_id',$admin->id)->delete();
            }
            else{
                $thisUser = \DB::table('role_user')->where('user_id',$admin->id)->first();
                if($thisUser != null){
                    \DB::table('role_user')->where('user_id',$admin->id)->update([
                        'role_id' => $request->role
                    ]);
                }
                else{
                    \DB::table('role_user')->insert([
                        'user_id' => $admin->id,
                        'role_id' => $request->role,
                    ]);
                }
            }
            $request->session()->flash('success',__('users.dhbus'));
            \Log::channel('custom')->info("Admin account has been updated.",['business_id' => Auth::user()->business_id, 'user_id' => $admin->id]);

            //------ Add log in DB for location -----//
            $type = "log_related_user";
            $txt = "User has been updated. <br> <b>Email: ".$admin->email."</b>";
            $this->addLocationLog($type,$txt);
        }
        else{
            \Log::channel('custom')->error("Error to updated Admin account.",['business_id' => Auth::user()->business_id, 'user_id' => $admin->id]);
            $request->session()->flash('error',__('users.tiaetud'));
        }
        return \Redirect::route('adminlist',session('business_name'));
    }

    public function changePass($subdomain,$id){
        abort_unless(\Gate::allows('Users Change Password'),403);

        $admin = Business::find(Auth::user()->business_id)->user->where(\DB::raw('md5(id)') , $id)->first();
        return view('admin.pass',compact('admin'));
    }

    public function updatePass(Request $request){
        abort_unless(\Gate::allows('Users Change Password'),403);

        $validatedData = $request->validate([
            'id' => ['required', 'string', 'max:255'],
            'password' => ['required','min:8','same:password_confirmation'],
        ]);

        $admin = User::find($request->id);
        $admin->password = \Hash::make($request->password); 

        if($admin->save()){
            $request->session()->flash('success',__('users.dhbus'));
            \Log::channel('custom')->info("Admin passwod has been updated.",['business_id' => Auth::user()->business_id, 'user_id' => $admin->id]);

            //------ Add log in DB for location -----//
            $type = "log_related_user";
            $txt = "User password has been updated. <br> <b>Email: ".$admin->email."</b>";
            $this->addLocationLog($type,$txt);

            // -------  for this user's will change language ------
            \App::setLocale($admin->language);

            //-------- Send Email to user with credentials ----------
            $brandName = Business::find(Auth::user()->business_id)->Settings->where('key','email_sender_name')->first(); 

            $url = url("/login");
            $subject =  __('emailtxt.user_password_update_subject',['name' => $brandName->value]);
            $content =  __('emailtxt.user_password_update_email_txt',['name' =>$admin->name, 'email' => $admin->email, 'url' => $url, 'password' => $request->password]);
            $this->dispatch(new SendEmailJob($admin->email,$subject,$content,$brandName->value,Auth::user()->business_id));
        }
        else{
            \Log::channel('custom')->error("Error to update Admin passwod.",['business_id' => Auth::user()->business_id, 'user_id' => $admin->id]);

            $request->session()->flash('error',__('users.tiaetud'));
        }
        return \Redirect::back();
    }

    public function delete(Request $request){
        abort_unless(\Gate::allows('Users Delete'),403);
        $validatedData = $request->validate([
            'id' => ['required'],
        ]);      
        
        $treatments = TreatmentSlot::where(\DB::raw('md5(user_id)') , $request->id)->where('is_active',1)->get()->count();
        $events = EventSlot::where(\DB::raw('md5(user_id)') , $request->id)->get()->where('is_active',1)->count();
        $cards = Card::where(\DB::raw('md5(user_id)') , $request->id)->get()->where('is_active',1)->count();
        $date = Date::where(\DB::raw('md5(user_id)') , $request->id)->get()->where('is_active',1)->count();
        $event = Event::where(\DB::raw('md5(user_id)') , $request->id)->get()->where('is_active',1)->count();
        $journals = Journal::where(\DB::raw('md5(user_id)') , $request->id)->get()->where('is_active',1)->count();

        if($treatments > 0 || $events > 0 || $cards > 0 || $date > 0 || $event > 0 ||  $journals > 0){
            $data['status'] = 'exist'; 
        }else{
            $user = User::where(\DB::raw('md5(id)'), $request->id)->first();
            //------ Add log in DB for location -----//
            $type = "log_related_user";
            $txt = "User has been deleted! <br> <b>Email: ".$user->email."</b> <br> User who delete him: <b>".auth()->user()->email.'</b>';
            $this->addLocationLog($type,$txt);

            \Log::channel('custom')->info('User has been deleted.',['user_id'=>$request->id, 'business_id'=> auth()->user()->business_id, 'user_who_disable_him'=> auth()->user()->id]);
            User::where(\DB::raw('md5(id)'), $request->id)->delete();
            $data['status'] = 'success'; 
        }
        
        return json_encode($data); 

    }
}
