<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Business;
use App\Models\User;
use App\Models\LocationLog;


class LocationLogController extends Controller
{
    public function index(){
        $settings = Business::find(auth()->user()->business_id)->Settings;
        $logs = LocationLog::where('business_id',auth()->user()->business_id)->orderBy('created_at','DESC')->paginate(25);
        return view('logs.list',compact('settings','logs'));
    }

    public function logSearch(Request $request){
        
        $settings = Business::find(auth()->user()->business_id)->Settings;

        if(!empty($request->value)){
            $logs = LocationLog::where('business_id',auth()->user()->business_id)->where('comment', 'LIKE', '%' . $request->value . '%')->orWhere('model', 'LIKE', '%' . $request->value . '%')->orderBy('created_at','DESC')->paginate(25);
            return view('logs.list',compact('settings','logs'));
        }
        else{
            return redirect()->route('log',session('business_name'));
        }
    }

    
    
}
