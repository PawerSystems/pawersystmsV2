<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Business;
use App\Models\TreatmentSlot;
use App\Models\EventSlot;
use App\Models\Card;
use App\Models\Date;
use App\Models\Event;
use App\Models\Journal;
use Illuminate\Support\Facades\Validator;

class UserProfileController extends Controller
{

    public function show($subdomain, $id){
        $user = User::where(\DB::raw('md5(id)'),$id)->first();
        if($user == null){
            echo __('keywords.no_record_found');
            exit();
        }
        return view('profile.show',compact('user'));
    }

    public function view(){
        $countries = $this->countries();
        $settings = Business::find(Auth::user()->business_id)->Settings;
        $txt = User::find(Auth::user()->id);
        
        return view('profile.profileView',compact('settings','countries','txt'));
    }

    public function update(Request $request){

        $validatedData = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string','email' ,'max:255'],
            'number' => ['required','max:255'],
            'language' => ['required', 'string'],
            'image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $check = User::where([['email',$request->email],['business_id',auth()->user()->business_id],['id','!=',auth()->user()->id]])->count();
        if( $check ){
            \Log::channel('custom')->warning("User try to update existing email.",['business_id' => auth()->user()->business_id, 'user_id' => auth()->user()->id]);
            $request->session()->flash('error',' Email exist in system.');
            return \Redirect::back();
        }

        if( !empty($request->image) ){
            $imageName = time().'.'.$request->image->extension();  
            $request->image->move(public_path('images'), $imageName);
        }

        $user = User::find(auth()->user()->id);
        $user->free_txt = $request->text;
        $user->name = $request->name;
        $user->number = $request->number;
        $user->cprnr = $request->cprnr;
        $user->mednr = $request->mednr;
        $user->gender = $request->gender;
        $user->birth_year = $request->birthYear;
        $user->country_id = $request->country;
        $user->language = $request->language;
        if( !empty($request->image) )
            $user->profile_photo_path = $imageName;

        $user->email = strtolower($request->email);
        if($request->is_therapist)
            $user->is_therapist = 1;
        else    
            $user->is_therapist = 0;

        if($request->will_notify)
            $user->will_notify = 1;
        else    
            $user->will_notify = 0;    

            
        
        if($user->save()){
            $request->session()->flash('success',__('customer.dhbus'));
            \Log::channel('custom')->info("Update user profile.",['business_id' => auth()->user()->business_id, 'user_id' => auth()->user()->id]);

            //------ Update langauge in session -----//
            session(['locale' =>  $request->language]);

            //------ Add log in DB for location -----//
            $type = "log_related_user";
            $txt = "Update user profile. <br> <b>Email: ".$user->email."</b>";
            $this->addLocationLog($type,$txt);
        }
        else{
            $request->session()->flash('error',__('customer.tiaetud'));
            \Log::channel('custom')->error("Fail to update user profile.",['business_id' => auth()->user()->business_id, 'user_id' => auth()->user()->id]);
        }
        return \Redirect::back();

    }
    
    public function passwordReset(Request $request){
        
        $validatedData = $request->validate([
            'cpass' => ['required', function ($attribute, $value, $fail) {
                if (!\Hash::check($value, Auth::user()->password)) {
                    \Log::channel('custom')->warning("The Current password is incorrect.",['business_id' => Auth::user()->business_id, 'user_id' => Auth::user()->id]);
                    return $fail(__('The Current password is incorrect.'));
                }
            }],
            'npass' => ['required','min:8','same:rpass'],
            'rpass' => ['required','min:8'],
        ]);
        
        $user = User::find(Auth::user()->id);
        $user->password = \Hash::make($request->npass);
        if($user->save()){
            $request->session()->flash('success',__('customer.phbus'));
            \Log::channel('custom')->info("Update user password.",['business_id' => Auth::user()->business_id, 'user_id' => Auth::user()->id]);

            //------ Add log in DB for location -----//
            $type = "log_related_user";
            $txt = "Update user password. <br> <b>Email: ".$user->email."</b>";
            $this->addLocationLog($type,$txt);
        }
        else{
            $request->session()->flash('error',__('customer.tiaetup'));
            \Log::channel('custom')->error("Fail to update user password.",['business_id' => Auth::user()->business_id, 'user_id' => Auth::user()->id]);
        }
        return \Redirect::back();

    }

    public function unsub(Request $request) {
        $user = User::where(\DB::raw('md5(id)') , $request->user)->first();
        $business = Business::find($user->business_id);
        \Log::channel('custom')->info('User come here with unsub link. UserID:'.$user->id);
        //------ set session for language -------
        session(['locale' =>  $user->language]);
        return view('web.unsub',compact('user','business'));
    }

    public function unsubUser(Request $request){
        $data['status'] = 'error';

        $user = User::find($request->id);
        $user->is_subscribe = 0;
        if($user->save()){
            $data['status'] = 'success';
            \Log::channel('custom')->info('User has been unsubscribed: UserID:'.$user->id);

            //------ Add log in DB for location -----//
            $type = "log_related_user";
            $txt = "User has been unsubscribed. <br> <b>Email: ".$user->email."</b>";
            $this->addLocationLog($type,$txt);
        }
        return json_encode($data);
    }

    public function subUser(Request $request){
        $data['status'] = 'error';

        $user = User::find(Auth::user()->id);

        if( $user->is_subscribe == 1 ){
            $user->is_subscribe = 0;
            if($user->save()){
                Auth::user()->is_subscribe = 0;
                \Log::channel('custom')->info('User has been unsubscribed: UserID:'.$user->id);
                $data['status'] = 'success';
                $data['data'] = __('profile.you_are_unsubscribe');

                //------ Add log in DB for location -----//
                $type = "log_related_user";
                $txt = "User has been unsubscribed. <br> <b>Email: ".$user->email."</b>";
                $this->addLocationLog($type,$txt);
            }
        }    
        else{
            $user->is_subscribe = 1;
            if($user->save()){
                Auth::user()->is_subscribe = 1;
                \Log::channel('custom')->info('User has been subscribed: UserID:'.$user->id);
                $data['status'] = 'success';
                $data['data'] = __('profile.you_are_subscribe');

                //------ Add log in DB for location -----//
                $type = "log_related_user";
                $txt = "User has been subscribed. <br> <b>Email: ".$user->email."</b>";
                $this->addLocationLog($type,$txt);
            }
        }    
        return json_encode($data);
    }

    public function disableUser(Request $request) {

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
            return json_encode($data);
        }

        $user = User::where(\DB::raw('md5(id)') , $request->id)->first();
        $user->is_active = 0;
        if($user->save()){
            \Log::channel('custom')->info('User has been disable.',['user_id'=>$user->id, 'business_id'=> auth()->user()->business_id, 'user_who_disable_him'=> auth()->user()->id]);
            $data['status'] = 'success';

            //------ Add log in DB for location -----//
            $type = "log_related_user";
            $txt = "User has been disable. <br> <b>Email: ".$user->email."</b><br>User who disable him: ".auth()->user()->email;
            $this->addLocationLog($type,$txt);
        }
        else
            $data['status'] = 'error';    
        
        return json_encode($data);
    }

    public function enableUser(Request $request) {

        $validatedData = $request->validate([
            'id' => ['required'],
        ]);

        $user = User::where(\DB::raw('md5(id)') , $request->id)->first();
        $user->is_active = 1;
        if($user->save()){
            \Log::channel('custom')->info('User has been enable.',['user_id'=>$user->id, 'business_id'=> auth()->user()->business_id, 'user_who_enable_him'=> auth()->user()->id]);
            $data['status'] = 'success';

            //------ Add log in DB for location -----//
            $type = "log_related_user";
            $txt = "User has been enable. <br> <b>Email: ".$user->email."</b><br>User who enable him: ".auth()->user()->email;
            $this->addLocationLog($type,$txt);
        }
        else
            $data['status'] = 'error';    
        
        return json_encode($data);
    }

    public function updateTharapist(Request $request) {

        $validatedData = $request->validate([
            'id' => ['required'],
            'check' => ['required'],
        ]);

        $user = User::find($request->id);

        if($request->check === 'true'){
            $user->is_therapist = 1;
        }else{
            $user->is_therapist = 0;
        }
        
        if($user->save()){
            \Log::channel('custom')->info('User has been updated.',['user_id'=>$user->id, 'business_id'=> auth()->user()->business_id, 'user_who_update_him'=> auth()->user()->id]);
            $data['status'] = 'success';

            //------ Add log in DB for location -----//
            $type = "log_related_user";
            $txt = "User has been updated. <br> <b>Email: ".$user->email."</b><br>User who update him: ".auth()->user()->email;
            $this->addLocationLog($type,$txt);
        }
        else
            $data['status'] = 'error';    
        
        return json_encode($data);
    }

    

}
