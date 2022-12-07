<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Business;
use App\Models\visitor;
use App\Models\User;

class LoginController extends Controller
{
    public function authenticate(Request $request){
        $bus = \DB::table('businesses')->select('id')->Where('business_name','=',Session('business_name'))->first();
        $landingPage = Business::find($bus->id)->Settings->where('key','landing_page')->first(); 

        if($bus){
            $request->request->add(['business_id' => $bus->id]);
            $request->merge(['email' => strtolower($request->email)]);
            $remember_me = $request->has('remember_me') ? true : false;
            $credentials = $request->only('email', 'password', 'business_id');
            if (Auth::attempt($credentials,$remember_me)) {

                $user = User::find(Auth::user()->id);
                $user->is_logged_in = 1;
                $user->updated_at = date('Y-m-d H:i:s');
                $user->save();

                //------ set session for language -------
                session(['locale' =>  Auth::user()->language]);

                \Log::channel('custom')->info("User successfully login into system.",['business_id' => $bus->id, 'user_id' => Auth::user()->id]);

                //------ Add log in DB for location -----//
                $type = "log_for_login";
                $txt = "User has been login in system <br> <b>Email: ".auth()->user()->email."</b>";
                $this->addLocationLog($type,$txt);

                //------ Add visit of login user ------
                $checkUser = visitor::where('user_id',Auth::user()->id)->whereDate('created_at','=', \Carbon\Carbon::today()->toDateString())->first();
                
                if($checkUser == null){
                    visitor::create([
                        'business_id' => Auth::user()->business_id,
                        'user_id' => Auth::user()->id,
                        'page' => 'login',
                    ]); 
                }else{
                    $checkUser->updated_at = \Carbon\Carbon::now();
                    $checkUser->save();
                }  

                if(Auth::user()->role == 'Owner' || Auth::user()->role == 'owner')
                    return redirect('business');
                elseif(Auth::user()->role == 'Customer')
                    return redirect('MyTreatmentBookings');
                elseif( $landingPage != null )
                    return redirect($landingPage->value);    
                else 
                    return redirect('dashboard');   
            }
            else{
                \Log::channel('custom')->warning("Sorry Your email or password not correct!",['business_id' => $bus->id, 'user_email' => $request->email]);

                $request->session()->flash('status','Sorry Your email or password not correct!');
                return redirect('login');
            }
        }
        // if failed login
        \Log::channel('custom')->warning("User not part of this system",['business_id' => $bus->id, 'user_email' => $request->email]);

        $request->session()->flash('status','Sorry You are not part of this system!');
        return redirect('login');
    }
}
