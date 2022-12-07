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
use App\Models\TreatmentSlot;
use App\Models\TreatmentPart;
use App\Models\Department;
use App\Models\EmailRecord;
use App\Models\Website;
use App\Models\PaymentMethod;
use App\Models\Setting;
use App\Models\Permission;
use App\Models\Role;
use App\Models\Question;
use App\Models\Option;
use App\Models\Country;
use App\Models\Event;
use App\Models\EventSlot;
use App\Models\Jurnal;
use App\Models\ScheduledReport;
use App\Models\SmsHistory;
use App\Models\StoreCurlData;
use App\Models\Survey;
use App\Models\visitor;

class DbController extends Controller
{
    //--------------- Business tables -------------------//
    public function businessDB(){
        $businesses = Business::select()->get();
        return view('database.business', compact('businesses'));
    }

    public function businessDBAdd(Request $request){
        if( auth()->user()->role == 'Owner' ){

            Validator::make($request->all(), [
                'business_name' => ['required'],
                'brand_name' => ['required'],
                'logo' => ['required'],
                'banner' => ['required'],
                'time_interval' => ['required'],
                'business_email' => ['required'],
                'is_active' => ['required'],
            ])->validate();

            $check = Business::where('business_name',$request->business_name)->count();
            if($check == 0){
                $data = 'error';
                $business = Business::create([
                    'business_name' => $request->business_name,
                    'brand_name' => $request->brand_name,
                    'logo' => $request->logo,
                    'banner' => $request->banner,
                    'time_interval' => $request->time_interval,
                    'business_email' => $request->business_email,
                    'is_active' => $request->is_active,
                    'languages' => '-1',
                    'access_modules' => '-1',
                ]);
                if($business->id > 0)
                    $data = 'success';
            }else{
                $data = 'exist';
            }
            return json_encode($data);
        }
    }

    public function businessDBEdit(Request $request){
        if( auth()->user()->role == 'Owner' ){

            Validator::make($request->all(), [
                'business_name' => ['required'],
                'brand_name' => ['required'],
                'logo' => ['required'],
                'banner' => ['required'],
                'time_interval' => ['required'],
                'business_email' => ['required'],
                'is_active' => ['required'],
            ])->validate();

            $check = Business::where('business_name',$request->business_name)->where('id','!=',$request->id)->count();
            if($check == 0){
                $data = 'error';
                $business = Business::find($request->id);
                $business->business_name = $request->business_name;
                $business->brand_name = $request->brand_name;
                $business->logo = $request->logo;
                $business->banner = $request->banner;
                $business->time_interval = $request->time_interval;
                $business->business_email = $request->business_email;
                $business->is_active = $request->is_active;
               
                if($business->save())
                    $data = 'success';
            }else{
                $data = 'exist';
            }
            return json_encode($data);
        }
    }

    public function businessDBDelete(Request $request){
        Validator::make($request->all(), [
            'id' => ['required'],
        ])->validate();

        $business = Business::where('id',$request->id)->delete();
        $data = 'success';
        return json_encode($data);
    }
}
