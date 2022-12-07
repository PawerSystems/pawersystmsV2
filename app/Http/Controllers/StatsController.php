<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Models\Business;
use App\Models\TreatmentSlot;
use App\Models\Date;
use App\Models\User;
use App\Models\Permission;
use App\Models\visitor;


class StatsController extends Controller
{

    protected $graphDurationValue = 12;
    protected $business;

    public function setGraphValue()
    {
        $this->business = Business::find(auth()->user()->business_id);
        $graphDuration = Business::find(auth()->user()->business_id)->Settings->where('key','graph_duration')->first();
        
        if($graphDuration != null)
            $this->graphDurationValue = $graphDuration->value;
    }

    public function index(){
        $this->setGraphValue();
        $data = $this->getStats();
        $treatments = \DB::table('treatment_slots')
        ->where('treatment_slots.parent_slot', NULL)
        ->where('treatment_slots.business_id',auth()->user()->business_id)
        ->where('treatment_slots.created_at', '>=', \Carbon\Carbon::now()->subMonth($this->graphDurationValue))
        ->Join('treatment_parts as treatment', 'treatment_slots.treatment_part_id', '=', 'treatment.id')
        ->select(array(\DB::raw('COUNT(treatment_slots.id) as ids'),'treatment_slots.treatment_part_id','treatment.title'))
        ->groupBy('treatment.title')
        ->groupBy('treatment_slots.treatment_part_id')
        ->get();

        $treatmentsAgeWise = TreatmentSlot::where('parent_slot', NULL)->where('business_id',auth()->user()->business_id)->where('status','Booked')->where('is_active',1)->where('created_at', '>=', \Carbon\Carbon::now()->subMonth($this->graphDurationValue))->get();

        $treatmentsGenderWise = \DB::table('treatment_slots')
        ->where('treatment_slots.parent_slot', NULL)
        ->where('treatment_slots.business_id',auth()->user()->business_id)
        ->where('treatment_slots.status','Booked')
        ->where('treatment_slots.created_at', '>=', \Carbon\Carbon::now()->subMonth($this->graphDurationValue))
        ->Join('users', 'treatment_slots.user_id', '=', 'users.id')
        ->select(array(\DB::raw('COUNT(treatment_slots.id) as ids'),'users.gender'))
        ->groupBy('users.gender')
        ->get();

        return view('stats.list',['datesOnly' => $data[0],'bookedArray' => $data[1], 'freeArray' => $data[2], 'treatments' => $treatments, 'treatmentsAgeWise' => $treatmentsAgeWise, 'treatmentsGenderWise' => $treatmentsGenderWise,'duration'=> $this->graphDurationValue]);
    }

    public function dashboard(){
        abort_unless(\Gate::allows('Dashboard Graphs'),403);
        $this->setGraphValue();
        $data = $this->getStats();
        $weekWiseDate = $this->getDateBookingWeekWise();
        $treatments = \DB::table('treatment_slots as slot')
        ->where('slot.parent_slot', NULL)
        ->where('slot.business_id',auth()->user()->business_id)
        ->where('slot.status','Booked')
        ->where('slot.created_at', '>=', \Carbon\Carbon::now()->subMonth($this->graphDurationValue))
        ->join('treatment_parts as part', 'slot.treatment_part_id', '=', 'part.id')
        ->select(array(\DB::raw('COUNT(slot.id) as ids'),'slot.treatment_part_id','part.title'))
        ->groupBy('part.title')
        ->groupBy('slot.treatment_part_id')
        ->get();
        $treatmentsAgeWise = TreatmentSlot::where('parent_slot', NULL)->where('business_id',auth()->user()->business_id)->where('status','Booked')->where('created_at', '>=', \Carbon\Carbon::now()->subMonth($this->graphDurationValue))->get();

        $treatmentsGenderWise = \DB::table('treatment_slots as slot')
        ->where('slot.parent_slot', NULL)
        ->where('slot.business_id',auth()->user()->business_id)
        ->where('slot.status','Booked')
        ->where('slot.created_at', '>=', \Carbon\Carbon::now()->subMonth($this->graphDurationValue))
        ->join('users', 'slot.user_id', '=', 'users.id')
        ->select(array(\DB::raw('COUNT(slot.id) as ids'),'users.gender'))
        ->groupBy('users.gender')
        ->get();

        return view('dashboard',['datesOnly' => $data[0],'bookedArray' => $data[1], 'freeArray' => $data[2], 'weekWiseDate' => $weekWiseDate, 'visitorsVsBookings' => $data[3],'treatments' => $treatments , 'treatmentsAgeWise' => $treatmentsAgeWise, 'treatmentsGenderWise' => $treatmentsGenderWise,'duration'=> $this->graphDurationValue]);
    }

    public function getDateBookingWeekWise(){

        $dataArray = array();
        //------ get all dates till today weekwise ------
        $weeks = Date::all()->where('business_id',Auth::user()->business_id)->where('is_active',1)->where('date', '>=', \Carbon\Carbon::now()->subMonths($this->graphDurationValue))->groupBy(function($date) {
            return \Carbon\Carbon::parse($date->date)->format('Y/W');
        });
        
        foreach($weeks as $key => $week){
            $ids = $week->pluck('id');
            $booked = TreatmentSlot::whereIn('date_id',$ids)->where('is_active',1)->where('status','Booked')->get()->count();

            $dataArray[$key]['booked'] = $booked;
            $freeSlots = 0;
            foreach($week as $day){
                $freeSlots = $freeSlots + count($this->getTimeSlots($day->from,$this->business->time_interval,$day->till))-($day->treatmentSlots()->count())-1;
            }

            $dataArray[$key]['free'] = $freeSlots;
        }
        
        ksort($dataArray);
        return $dataArray;
    }

    public function getStats(){
        abort_unless(\Gate::any(['Stats View','Dashboard Graphs']),403);
        $dateFormat = Business::find(Auth::user()->business_id)->Settings->where('key','date_format')->first(); 
        $dates = Date::where('business_id',Auth::user()->business_id)->where('is_active',1)->where("date",">=", \Carbon\Carbon::now()->subMonths($this->graphDurationValue))->orderBy('date','ASC')->get();

        //----- change date format -----
        $format = $dateFormat->value;
        $datesOnly = $dates->pluck('date')->map(function ($date) use ($format) {
            return date($format, strtotime($date));
        })->toArray();

        $bookedArray = array();
        $freeArray = array();
        foreach($dates as $date){
            
            $bookings = $date->treatmentSlots->where('status','Booked')->where('is_active',1);
            array_push($bookedArray, $bookings->count());

            $close = $date->treatmentSlots->where('status','Break')->count();
            $lunch = $date->treatmentSlotLunch->count();
            
            $freeSlots = count($this->getTimeSlots($date->from,$this->business->time_interval,$date->till))-($bookings->count())-($close)-($lunch)-1;
            array_push( $freeArray,$freeSlots);
        }

        //------------------- Get data of visitors vs booking on that date  -----------------//
        $visitsVsBookings = array();

        $visits = visitor::select([
            \DB::raw('created_at'),
            \DB::raw('user_id'),
        ])
        ->where('business_id',Auth::user()->business_id)
        ->orderBy('created_at','ASC')
        ->where("created_at",">", \Carbon\Carbon::now()->subMonths($this->graphDurationValue))
        ->get()
        ->groupBy(function($date) use($format) {
            return \Carbon\Carbon::parse($date->created_at)->format($format);
        });
        
        $bookingDateWise = TreatmentSlot::select()
            ->where([['status','Booked'],['is_active',1],["created_at",">", \Carbon\Carbon::now()->subMonths($this->graphDurationValue)]])
            ->get()
            ->groupBy(function($date) use($format) {
            return \Carbon\Carbon::parse($date->created_at)->format($format);
        });

        foreach($visits as $k => $val){
            $visitsVsBookings[$k]['visitors'] = $val->count();
            foreach( $bookingDateWise as $key => $date){
                if($k == $key){
                    $visitsVsBookings[$k]['bookings'] = ($date->count() > 50 ? 0 : $date->count());
                }
            }
        }

        return array($datesOnly, $bookedArray, $freeArray, $visitsVsBookings);
    }
}
