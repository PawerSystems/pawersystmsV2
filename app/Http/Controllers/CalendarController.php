<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Date;
use App\Models\Business;
use App\Notifications\BookingNotification;

class CalendarController extends Controller
{
    public function view(){
        $businesses = Business::where('is_active',1)->where('id','!=',1)->get();
        return view('calendar.view',compact('businesses'));
    }

    public function viewUserWise(){
        $activeUsers = Date::where('date','>=',date('Y-m-d'))->where('is_active',1)->get()->pluck('user_id');
        $uniqueUsers = User::where('is_active',1)->where('role','!=','Customer')->where('id','!=',1)->whereIn('id',$activeUsers)->get();
        // $uniqueUsers = User::where('is_active',1)->where('role','!=','Customer')->where('id','!=',1)->get();
        $users = $uniqueUsers->unique('email');
        $businesses = Business::where('is_active',1)->where('id','!=',1)->get();
        return view('calendar.userview',compact('businesses','users'));
    }
}