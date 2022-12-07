<?php

namespace App\Http\Controllers;

use PdfReport;
use ExcelReport;
use CSVReport;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Models\Business;
use App\Models\TreatmentSlot;
use App\Models\Treatment;
use App\Models\User;
use App\Models\Date;
use App\Models\Survey;
use App\Models\Role;
use App\Models\ScheduleReport;
use App\Models\LocationLog;

class ReportsController extends Controller
{

    public function index(){
        abort_unless(\Gate::any(['Reports Users View','Reports Booking View','Reports Date View','Reports Unique User View','Reports Srvey View']),403);
        $settings = Business::find(Auth::user()->business_id)->settings;
        $business = Business::select()->get();
        $admins = Business::find(Auth::user()->business_id)->users->where('role','!=','Customer');
        return view('reports.list',compact('settings','business','admins'));
    }

    public function scheduleReport(Request $request){

        Validator::make($request->all(), [
            'users' => ['required'],
            'duration' => ['required'],
            'period' => ['required'],
            'time' => ['required'],
        ])->validate();

        $uses = implode(',',$request->users);

        $report = ScheduleReport::create([
            'business_id' => Auth::user()->business_id,
            'user_id' => Auth::user()->id,
            'users' => $uses,
            'duration' => $request->duration,
            'period' => $request->period,
            'time' => $request->time,
            'type' => $request->report_for,
        ]);

        if($report->id){
            $data = 'success';
        }
        else{
            $data = 'fail';
        }

        return json_encode($data); 

    }

    public function edit($subdomain,$id){
        $admins = Business::find(Auth::user()->business_id)->users->where('role','!=','Customer');
        $report = ScheduleReport::where(\DB::raw('md5(id)'),$id)->first();
        return view('reports.edit',compact('report','admins'));
    }

    public function ReportEdit(Request $request){
        
        Validator::make($request->all(), [
            'users' => ['required'],
            'duration' => ['required'],
            'period' => ['required'],
            'time' => ['required'],
        ])->validate();

        $report = ScheduleReport::find($request->id);
        $uses = implode(',',$request->users);

        $report->users = $uses;
        $report->user_id = Auth::user()->id;
        $report->period = $request->period;
        $report->duration = $request->duration;
        $report->time = $request->time;
        
        if($request->status)
            $report->is_active = 1;
        else
            $report->is_active = 0;

        if($report->save()){
            $request->session()->flash('success',__('card.chbus'));
            \Log::channel('custom')->info("Schedule Report has been updated successfully!",['business_id' => Auth::user()->business_id]);    
        }
        else{
            \Log::channel('custom')->warning("Error to update Schedule Report information.",['business_id' => Auth::user()->business_id]);
            $request->session()->flash('error',__('card.tisetuc'));
        }

        return \Redirect::back();
    }

    public function scheduledReports(){
        $dateFormat = Business::find(Auth::user()->business_id)->Settings->where('key','date_format')->first(); 
        $reports = ScheduleReport::where([['business_id',auth()->user()->business_id]])->get();
        return view('reports.scheduled_list',compact('reports','dateFormat'));
    }

    public function displayReport(Request $request){
        
        // $dateRange = $request->input('date_range');
        $fromDate =  $request->input('_from');
        $toDate =  $request->input('_to');
        $type = $request->input('type');
        // $dates = explode(' - ',$dateRange);
        // $fromDate = $dates[0];
        // $toDate = $dates[1];
        $sortBy = $request->input('sort_by');
        if(Auth::user()->role == 'Owner')
            $bID = -1;
        else
            $bID = Auth::user()->business_id;    

        if($request->input('report_for') == 'userReport')
            return $this->getUsers($type,$fromDate,$toDate,$sortBy,$bID);
        elseif( $request->input('report_for') == 'usersBookingReportForm')
            return $this->getUsersBookings($type,$fromDate,$toDate,$sortBy,$bID);
        elseif( $request->input('report_for') == 'bookingReport')
            return $this->getTreatentBookings($type,$fromDate,$toDate,$sortBy,$bID);
        elseif( $request->input('report_for') == 'dateReport')    
            return $this->getDateReport($type,$fromDate,$toDate,$sortBy,$bID);
        elseif( $request->input('report_for') == 'uniqueUserReport')    
            return $this->getUniqueUserReport($type,$fromDate,$toDate,$sortBy,$bID);
        elseif( $request->input('report_for') == 'suveyReport')    
            return $this->getSuveyReport($type,$fromDate,$toDate,$sortBy,$bID);
        elseif( $request->input('report_for') == 'smsReport')    
            return $this->getSmsReport($type,$fromDate,$toDate,$sortBy,$bID);
        elseif( $request->input('report_for') == 'logReportForm')    
            return $this->getLogReport($type,$fromDate,$toDate,$sortBy,$bID);    
        else
            return false;

    }

    //---###############################################################---//
    public function getLogReport($type,$fromDate,$toDate,$sortBy,$bID){
        //$title = __('reports.log_report_title'); // Report title
        $title = ''; // Report title

        if($bID == -1){
            $dateFormatValue = "d-m-Y";
        }
        else{
            $business = Business::find($bID);
            $businessName = $business->business_name;
            $dateFormat = Business::find($bID)->Settings->where('key','date_format')->first();
            $dateFormatValue = $dateFormat->value;
        }

        $meta = [ // For displaying filters description on header
            __('keywords.time_period') => \Carbon\Carbon::parse($fromDate)->format($dateFormatValue).' '.__('keywords.to').' '.\Carbon\Carbon::parse($toDate)->format($dateFormatValue),
        ];

        //----- Report for Owner -----
        if($bID == -1){}
        else{
            $queryBuilder =  LocationLog::select(['model','comment','action_by','created_at'])
                            ->where("business_id",$bID)
                            ->whereBetween("created_at", [$fromDate.' 00:00:00', $toDate.' 23:59:59'])
                            ->orderBy('created_at');
            $columns = [
                __('reports.type') => 'model',
                __('reports.content') => 'comment',
                __('reports.action_by') => 'action_by',
                __('reports.date') => 'created_at',
            ];  
            
            return $type::of($title, $meta, $queryBuilder, $columns)
                        ->editColumn(__('reports.date'), [ 
                            'displayAs' => function($result) use ($dateFormatValue) {
                                return \Carbon\Carbon::parse($result->created_at)->format($dateFormatValue);
                            },
                            'class' => 'left'
                        ]) 
                        ->editColumn(__('reports.type'), [ 
                            'displayAs' => function($result){
                                return  ucfirst(str_replace('_',' ',$result->model));
                            },
                        ]) 
                        ->setPaper('a3')  
                        ->showMeta(false)                     
                        ->download($businessName.'_'.__('reports.log_report_title').'_'.\Carbon\Carbon::now()->format('Y-M-d-H:i:s'));
        }
    }//----- End getLogReport -----//

    //---###############################################################---//
    public function getSuveyReport($type,$fromDate,$toDate,$sortBy,$bID){
        //abort_unless(\Gate::allows('Reports Srvey View'),403);
        
        //$title = __('reports.suvey_report_title'); // Report title
        $title = '';

        if($bID == -1){
            $dateFormatValue = "d-m-Y";
        }
        else{
            $business = Business::find($bID);
            $businessName = $business->business_name;
            $dateFormat = Business::find($bID)->Settings->where('key','date_format')->first();
            $dateFormatValue = $dateFormat->value;
        }

        $meta = [ // For displaying filters description on header
            __('keywords.time_period') => \Carbon\Carbon::parse($fromDate)->format($dateFormatValue).' '.__('keywords.to').' '.\Carbon\Carbon::parse($toDate)->format($dateFormatValue),
        ];

        //----- Report for Owner -----
        if($bID == -1){}
        else{
            

            $queryBuilder =  Survey::where("business_id",$bID)
                            ->whereBetween("created_at", [$fromDate.' 00:00:00', $toDate.' 23:59:59'])
                            ->where("survey_id",NULL)
                            ->orderBy('created_at');
            $columns = [
                __('reports.date') => 'created_at',
                __('reports.email') => 'email',
                __('reports.name') => 'name',
            ];  

            $questions = \DB::table("questions")->where('business_id',$bID)->where('is_active',1)->get();
            $result = $queryBuilder->first();

            foreach($questions as $question){  
                $columns[$question->title] = function($result) use($question) {
                    $surveys = Survey::where('id',$result->id)->orWhere('survey_id',$result->id)->get();
                    foreach($surveys as $survey){
                        if($question->id == $survey->question_id){
                            if($survey->option_id == NULL)
                                return $survey->comment;
                            else    
                                return $survey->option->value;
                        }
                    }                    
                };
            } 
            
            return $type::of($title, $meta, $queryBuilder, $columns)
                        ->editColumn(__('reports.date'), [ 
                            'displayAs' => function($result) use ($dateFormatValue) {
                                return \Carbon\Carbon::parse($result->created_at)->format($dateFormatValue);
                            },
                            'class' => 'left'
                        ]) 
                        ->setPaper('a3') 
                        ->showMeta(false)                     
                        ->download($businessName.'_'.__('reports.suvey_report_title').'_'.\Carbon\Carbon::now()->format('Y-M-d-H:i:s'));
        }
    }//----- End getSuveyReport -----//

    //---###############################################################---//
    public function getUniqueUserReport($type,$fromDate,$toDate,$sortBy,$bID){
        //abort_unless(\Gate::allows('Reports Unique User View'),403);
        //$title = __('reports.unique_user_report'); // Report title
        $title = '';

        if($bID == -1){
            $dateFormatValue = "d-m-Y";
        }
        else{
            $business = Business::find($bID);
            $businessName = $business->business_name;
            $dateFormat = Business::find($bID)->Settings->where('key','date_format')->first();
            $dateFormatValue = $dateFormat->value;
        }

        $meta = [ // For displaying filters description on header
            __('keywords.booked_at') => \Carbon\Carbon::parse($fromDate)->format($dateFormatValue).' '.__('keywords.to').' '.\Carbon\Carbon::parse($toDate)->format($dateFormatValue),
            __('keywords.sort_by') => $sortBy
        ];

        //----- Report for Owner -----
        if($bID == -1){
            // $queryBuilder =  \DB::table("users")
            //                 ->leftJoin('treatment_slots AS slots', function($join) use ($fromDate,$toDate) {
            //                     $join->on("slots.user_id", "=", "users.id")->where("slots.status",'=',"Booked")->where("slots.parent_slot",NULL)->where("slots.is_active",1)->whereBetween("slots.created_at", [$fromDate.' 00:00:00', $toDate.' 23:59:00']);
            //                 })                            
            //                 ->where("users.is_active",1)
            //                 ->where("users.role",'Customer')
                            
            //                 ->select(["users.business_id AS business_id","users.name AS uname",'users.email as uemail',"users.number AS unumber",\DB::raw('COUNT(slots.id) AS booked')])

            //                 ->groupBy('users.name')
            //                 ->groupBy('users.email')
            //                 ->groupBy('users.number')
            //                 ->groupBy('users.business_id')
            //                 ->orderBy($sortBy);

            $queryBuilder =  \DB::table("users")
            ->leftJoin('treatment_slots AS slots', function($join) {
                $join->on("slots.user_id", "=", "users.id")->where("slots.status",'=',"Booked")->where("slots.parent_slot",NULL)->where("slots.is_active",1);
            })  
            ->join('dates', function($join) use ($fromDate,$toDate) {
                $join->on("dates.id", "=", "slots.date_id")->whereBetween("dates.date", [$fromDate, $toDate]);
            })                           
            ->where("users.is_active",1)
            ->where("users.role",'!=',0)            
            ->select(["users.name AS uname","users.birth_year AS birth_year",'users.email as uemail',"users.number AS unumber",\DB::raw('COUNT(slots.id) AS booked')])

            ->groupBy('users.name')
            ->groupBy('users.email')
            ->groupBy('users.number')
            ->groupBy('users.birth_year')
            ->groupBy('users.business_id')
            ->orderBy($sortBy);

                $columns = [
                    __('reports.business_id') => 'business_id',
                    __('reports.name') => 'uname',
                    __('reports.email') => 'uemail',
                    __('reports.number') => 'unumber',
                    __('reports.birth_year') => 'birth_year',
                    __('reports.bookings') => 'booked',
                ];

                return $type::of($title, $meta, $queryBuilder, $columns)
                        ->setPaper('a3')
                        ->download(__('reports.unique_user_report').'_'.\Carbon\Carbon::now()->format('Y-M-d-H:i:s'));
        }
        else{
            // $queryBuilder =  \DB::table("users")
            //                 ->leftJoin('treatment_slots AS slots', function($join) use ($fromDate,$toDate) {
            //                     $join->on("slots.user_id", "=", "users.id")->where("slots.status",'=',"Booked")->where("slots.parent_slot",NULL)->where("slots.is_active",1)->whereBetween("slots.created_at", [$fromDate.' 00:00:00', $toDate.' 23:59:00']);
            //                 })                            
            //                 ->where("users.business_id",$bID)
            //                 ->where("users.is_active",1)
            //                 ->where("users.role",'Customer')
                            
            //                 ->select(["users.name AS uname",'users.email as uemail',"users.number AS unumber",\DB::raw('COUNT(slots.id) AS booked')])

            //                 ->groupBy('users.name')
            //                 ->groupBy('users.email')
            //                 ->groupBy('users.number')
            //                 ->orderBy($sortBy);
            $queryBuilder =  \DB::table("users")
                            ->leftJoin('treatment_slots AS slots', function($join) {
                                $join->on("slots.user_id", "=", "users.id")->where("slots.status",'=',"Booked")->where("slots.parent_slot",NULL)->where("slots.is_active",1);
                            })  
                            ->join('dates', function($join) use ($fromDate,$toDate) {
                                $join->on("dates.id", "=", "slots.date_id")->whereBetween("dates.date", [$fromDate, $toDate]);
                            })                           
                            ->where("users.business_id",$bID)
                            ->where("users.is_active",1)
                            ->where("users.role",'!=',0)
                            
                            ->select(["users.name AS uname","users.birth_year AS birth_year",'users.email as uemail',"users.number AS unumber",\DB::raw('COUNT(slots.id) AS booked')])

                            ->groupBy('users.name')
                            ->groupBy('users.email')
                            ->groupBy('users.number')
                            ->groupBy('users.birth_year')
                            ->orderBy($sortBy);

                $columns = [
                    __('reports.name') => 'uname',
                    __('reports.email') => 'uemail',
                    __('reports.number') => 'unumber',
                    __('reports.birth_year') => 'birth_year',
                    __('reports.bookings') => 'booked',
                ];
                

                return $type::of($title, $meta, $queryBuilder, $columns)
                        ->setPaper('a3')
                        ->showMeta(false)
                        ->download($businessName.'_'.__('reports.unique_user_report').'_'.\Carbon\Carbon::now()->format('Y-M-d-H:i:s'));
        }

    }//----- End getUniqueUserReport -----//

    //---###############################################################---//
    public function getUsersBookings($type,$fromDate,$toDate,$sortBy,$bID){
        //abort_unless(\Gate::allows('Reports Unique User View'),403);
       // $title = __('reports.users_bookings'); // Report title
        $title = '';

        if($bID == -1){
            $dateFormatValue = "d-m-Y";
        }
        else{
            $business = Business::find($bID);
            $businessName = $business->business_name;
            $dateFormat = Business::find($bID)->Settings->where('key','date_format')->first();
            $dateFormatValue = $dateFormat->value;
        }

        $meta = [ // For displaying filters description on header
            __('keywords.booked_at') => \Carbon\Carbon::parse($fromDate)->format($dateFormatValue).' '.__('keywords.to').' '.\Carbon\Carbon::parse($toDate)->format($dateFormatValue),
            __('keywords.sort_by') => $sortBy
        ];

        //----- Report for Owner -----
        if($bID == -1){
            
            $queryBuilder =  \DB::table('TREATMENT_details')->whereBetween("date", [$fromDate.' 00:00:00', $toDate.' 23:59:00']);

            $columns = [
                __('reports.treatment_date') => 'time_combined',
                __('reports.name') => 'uname',
                __('reports.gender') => 'gender',
                __('reports.birth_year') => 'birth_year',
                __('reports.status') => 'status',
                __('reports.email') => 'uemail',
                __('reports.number') => 'phone_number',
                __('reports.cprnr') => 'cprnr',
                __('reports.mednr') => 'mednr',
                __('reports.therapist') => 'therapist',
                __('reports.comment') => 'comment',
                __('reports.treatment') => 'treatment_name',
                __('reports.treatment_area') => 'treatment_area',
                __('reports.department_name') => 'department_name',
                __('reports.payment_type') => 'title',
                __('reports.bookingTime') => 'book_time',
                __('reports.time') => 'minutes',
            ];
            

            return $type::of($title, $meta, $queryBuilder, $columns)
                    ->editColumn(__('reports.bookingTime'), [ 
                        'displayAs' => function($result) use ($dateFormatValue) {
                            return \Carbon\Carbon::parse($result->book_time)->format($dateFormatValue);
                        },
                        'class' => 'left'
                    ])
                    ->setOrientation('landscape')
                    ->setPaper('a3')
                    ->download($businessName.'_'.__('reports.users_bookings').'_'.\Carbon\Carbon::now()->format('Y-M-d-H:i:s'));
        }
        else{

            $queryBuilder =  \DB::table('TREATMENT_details')->whereBetween("date", [$fromDate.' 00:00:00', $toDate.' 23:59:00'])->where('business_id',$bID);

            $columns = [
                __('reports.treatment_date') => 'time_combined',
                __('reports.name') => 'uname',
                __('reports.gender') => 'gender',
                __('reports.birth_year') => 'birth_year',
                __('reports.status') => 'status',
                __('reports.email') => 'uemail',
                __('reports.number') => 'phone_number',
                __('reports.cprnr') => 'cprnr',
                __('reports.mednr') => 'mednr',
                __('reports.therapist') => 'therapist',
                __('reports.comment') => 'comment',
                __('reports.treatment') => 'treatment_name',
                __('reports.treatment_area') => 'treatment_area',
                __('reports.department_name') => 'department_name',
                __('reports.payment_type') => 'title',
                __('reports.bookingTime') => 'book_time',
                __('reports.time') => 'minutes',
            ];
            

            return $type::of($title, $meta, $queryBuilder, $columns)
                    ->editColumn(__('reports.bookingTime'), [ 
                        'displayAs' => function($result) use ($dateFormatValue) {
                            return \Carbon\Carbon::parse($result->book_time)->format($dateFormatValue);
                        },
                        'class' => 'left'
                    ])
                    ->setOrientation('landscape')
                    ->setPaper('a3')
                    ->showMeta(false)
                    ->download($businessName.'_'.__('reports.users_bookings').'_'.\Carbon\Carbon::now()->format('Y-M-d-H:i:s'));
            
        }

    }
    //----- End getUniqueUserReport -----//

    //---###############################################################--//
    public function getDateReport($type,$fromDate,$toDate,$sortBy,$bID){
        //abort_unless(\Gate::allows('Reports Date View'),403);
        //$title = __('reports.date_report'); // Report title
        $title = '';

        if($bID == -1){
            $dateFormatValue = "d-m-Y";
        }
        else{
            $business = Business::find($bID);
            $businessName = $business->business_name;
            $dateFormat = Business::find($bID)->Settings->where('key','date_format')->first();
            $dateFormatValue = $dateFormat->value;
        }
        
        $meta = [ // For displaying filters description on header
            __('keywords.booked_at') => \Carbon\Carbon::parse($fromDate)->format($dateFormatValue).' '.__('keywords.to').' '.\Carbon\Carbon::parse($toDate)->format($dateFormatValue),
            // 'Sort By' => $sortBy
        ];

        //----- Report for Owner -----
        if($bID == -1){

            $queryBuilder =  Date::whereBetween("date", [$fromDate, $toDate])
                            ->where("is_active",1)
                            ->orderBy('date');

            $columns = [
                __('reports.week') => function($result) {
                    return \Carbon\Carbon::parse($result->date)->format('W');
                },
                __('reports.date') => function($result) use ($dateFormatValue) {
                    return \Carbon\Carbon::parse($result->date)->format($dateFormatValue);
                },
                __('reports.from') => function($result){
                    return $result->from;
                },
                __('reports.till') => function($result){
                    return $result->till;
                },
                __('reports.lunch') => function($result){
                    $lunch = $result->treatmentSlots->where('status','Lunch')->first();
                    if($lunch)
                        return $lunch->time;
                    else
                        return '';
                },
                __('reports.booked') => function($result){
                    return $result->treatmentSlots->where('status','Booked')->count();
                },
                __('reports.booking_percentage') => function($result) use ($bID) {
                    $controller = new Controller();
                    $inter = Business::find($bID);
                    $totalSlots = count($controller->getTimeSlots($result->from,$inter->time_interval,$result->till))-($result->treatmentSlots->where('status','Break')->count())-($result->treatmentSlots->where('status','Lunch')->count())-1;
                    $bookedSlots = $result->treatmentSlots->where('status','Booked')->count();
                    
                    if($bookedSlots > 0)
                        $pecentage = ($bookedSlots/$totalSlots)*100;
                    else
                        $pecentage = 0;

                        return number_format($pecentage, 2, '.', '');
                        //return number_format($pecentage, 2, '.', '').'%';
                    },
                __('reports.open_slots') => function($result) {
                    $controller = new Controller();
                    $freeSlots = 0;
                    $inter = Business::find(auth()->user()->business_id);
                    $freeSlots = count($controller->getTimeSlots($result->from,$inter->time_interval,$result->till))-($result->treatmentSlots->where('status','Booked')->count())-($result->treatmentSlots->where('status','Break')->count())-($result->treatmentSlots->where('status','Lunch')->count())-1;
                    return $freeSlots;                        
                },
                __('reports.description') => function($result){
                    return $result->description;
                },
                __('reports.therapist') => function($result){
                    return $result->user->name;
                },
                __('reports.services_performed') => function($result){
                    $tre = $result->treatments;
                    $treatmentsAndSlotsBooked = array();
                    foreach($tre as $t){
                        $bookedSlots = $result->treatmentSlots->where('status','Booked')->where('treatment_id',$t->id)->count();
                        if($bookedSlots > 0){
                            $values = $t->treatment_name.' - '.($bookedSlots ?: 0);
                            array_push($treatmentsAndSlotsBooked,$values);
                        }
                    }

                    return implode(' | ',$treatmentsAndSlotsBooked); 
                },
                __('reports.treatment_and_duration') => function($result) {
                    $controller = new Controller();
                    $tre = $result->treatments;
                    $treatmentsAndSlots = array();
                    foreach($tre as $t){
                        $freeSlots = count($controller->getTimeSlots($result->from,$t->inter,$result->till))-($result->treatmentSlots->where('status','Booked')->count())-($result->treatmentSlots->where('status','Break')->count())-($result->treatmentSlots->where('status','Lunch')->count())-1;
                        $values = $t->treatment_name.' - '.($freeSlots ?: 0);
                        array_push($treatmentsAndSlots,$values);
                    }

                    return implode(' | ',$treatmentsAndSlots);                        
                },
                __('reports.close_time') => function($result){
                    return $result->treatmentSlots->where('status','Break')->count();
                }
            ];

            return $type::of($title, $meta, $queryBuilder, $columns)
                        ->editColumn(__('reports.date'), [ 
                            'class' => 'left'
                        ])
                        ->editColumn(__('reports.booked'), [ 
                            'class' => 'right'
                        ])
                        ->editColumn(__('reports.open_slots'), [ 
                            'class' => 'right'
                        ])
                        ->editColumn(__('reports.close_time'), [ 
                            'class' => 'right'
                        ])
                        ->showTotal([
                            __('reports.booked') =>  '',
                            __('reports.open_slots') => '',
                            __('reports.close_time') => '',
                            __('reports.booking_percentage') => ''
                        ])
                        ->setPaper('a3')
                        ->download(__('reports.date_report').'_'.\Carbon\Carbon::now()->format('Y-M-d-H:i:s'));
        }
        else{
            $queryBuilder =  Date::whereBetween("date", [$fromDate, $toDate])
                            ->where("business_id",$bID)
                            ->where("is_active",1)
                            ->orderBy('date');
            $columns = [
                __('reports.week') => function($result) {
                    return \Carbon\Carbon::parse($result->date)->format('W');
                },
                __('reports.date') => function($result) use ($dateFormatValue) {
                    return \Carbon\Carbon::parse($result->date)->format($dateFormatValue);
                },
                __('reports.from') => function($result){
                    return $result->from;
                },
                __('reports.till') => function($result){
                    return $result->till;
                },
                __('reports.lunch') => function($result){
                    $lunch = $result->treatmentSlots->where('status','Lunch')->first();
                    if($lunch)
                        return $lunch->time;
                    else
                        return '';
                },
                __('reports.booked') => function($result){
                    return $result->treatmentSlots->where('status','Booked')->count();
                },
                __('reports.booking_percentage') => function($result) use ($bID) {
                    $controller = new Controller();
                    $inter = Business::find($bID);
                    $totalSlots = count($controller->getTimeSlots($result->from,$inter->time_interval,$result->till))-($result->treatmentSlots->where('status','Break')->count())-($result->treatmentSlots->where('status','Lunch')->count())-1;
                    $bookedSlots = $result->treatmentSlots->where('status','Booked')->count();
                    
                    if($bookedSlots > 0)
                        $pecentage = ($bookedSlots/$totalSlots)*100;
                    else
                        $pecentage = 0;

                    return number_format($pecentage, 2, '.', '');
                    //return number_format($pecentage, 2, '.', '').'%';
                },
                __('reports.open_slots') => function($result) use ($bID) {
                    $controller = new Controller();
                    $freeSlots = 0;
                    $inter = Business::find($bID);
                    $freeSlots = count($controller->getTimeSlots($result->from,$inter->time_interval,$result->till))-($result->treatmentSlots->where('status','Booked')->count())-($result->treatmentSlots->where('status','Break')->count())-($result->treatmentSlots->where('status','Lunch')->count())-1;
                    return $freeSlots;                        
                },
                __('reports.description') => function($result){
                    return $result->description;
                },
                __('reports.therapist') => function($result){
                    return $result->user->name;
                },
                __('reports.services_performed') => function($result){
                    $tre = $result->treatments;
                    $treatmentsAndSlotsBooked = array();
                    foreach($tre as $t){
                        $bookedSlots = $result->treatmentSlots->where('status','Booked')->where('treatment_id',$t->id)->count();
                        if($bookedSlots > 0){
                            $values = $t->treatment_name.' - '.($bookedSlots ?: 0);
                            array_push($treatmentsAndSlotsBooked,$values);
                        }
                    }

                    return implode(' | ',$treatmentsAndSlotsBooked);  
                },
                __('reports.treatment_and_duration') => function($result) {
                    $controller = new Controller();
                    $tre = $result->treatments;
                    $treatmentsAndSlots = array();
                    foreach($tre as $t){
                        $freeSlots = count($controller->getTimeSlots($result->from,$t->inter,$result->till))-($result->treatmentSlots->where('status','Booked')->count())-($result->treatmentSlots->where('status','Break')->count())-($result->treatmentSlots->where('status','Lunch')->count())-1;
                        $values = $t->treatment_name.' - '.($freeSlots ?: 0);
                        array_push($treatmentsAndSlots,$values);
                    }

                    return implode(' | ',$treatmentsAndSlots);                        
                },
                __('reports.close_time') => function($result){
                    return $result->treatmentSlots->where('status','Break')->count();
                }
            ];

            return $type::of($title, $meta, $queryBuilder, $columns)
                        ->editColumn(__('reports.date'), [ 
                            'class' => 'left'
                        ])
                        ->editColumn(__('reports.booked'), [ 
                            'class' => 'right'
                        ])
                        ->editColumn(__('reports.open_slots'), [ 
                            'class' => 'right'
                        ])
                        ->editColumn(__('reports.close_time'), [ 
                            'class' => 'right'
                        ])
                        ->showTotal([
                            __('reports.booked') =>  '',
                            __('reports.open_slots') => '',
                            __('reports.close_time') => '',
                            __('reports.booking_percentage') => ''
                        ])
                        ->setPaper('a3')
                        ->showMeta(false)
                        ->download($businessName.'_'.__('reports.date_report').'_'.\Carbon\Carbon::now()->format('Y-M-d-H:i:s'));
        }
    }//----- End getDateReport -----//

    //---###############################################################--//
    public function getTreatentBookings($type,$fromDate,$toDate,$sortBy,$bID){
        //abort_unless(\Gate::allows('Reports Booking View'),403);
        //$title = __('reports.treatment_booking_report'); // Report title
        $title = '';

        if($bID == -1){
            $dateFormatValue = "d-m-Y";
        }
        else{
            $business = Business::find($bID);
            $businessName = $business->business_name;
            $dateFormat = Business::find($bID)->Settings->where('key','date_format')->first();
            $dateFormatValue = $dateFormat->value;
        }

        $meta = [ // For displaying filters description on header
            __('keywords.booked_at') => \Carbon\Carbon::parse($fromDate)->format($dateFormatValue).' '.__('keywords.to').' '.\Carbon\Carbon::parse($toDate)->format($dateFormatValue),
            __('keywords.sort_by') => $sortBy
        ];
        //----- Report for Owner -----
        if($bID == -1){
            $queryBuilder =  \DB::table("treatment_slots AS slots")
                            ->select(["slots.business_id as business_ID","users.name AS customer_name","users.number AS customer_number","users.email AS customer_email","time","comment","slots.created_at AS booked_at",'treatments.treatment_name AS treatment','dates.date AS treatment_date','payment_methods.title AS payment'])
                            //,'treatment_parts.title AS treatment_part'
                            ->whereBetween("slots.created_at", [$fromDate, $toDate])
                            ->where("slots.parent_slot",NULL)
                            ->where("slots.status",'Booked')
                            ->join("users", "users.id", "=", "slots.user_id")
                            ->join("treatments", "treatments.id", "=", "slots.treatment_id")
                            ->join("dates", "dates.id", "=", "slots.date_id")
                            ->leftjoin("payment_methods", "payment_methods.id", "=", "slots.payment_method_id")
                            // ->leftjoin("treatment_parts", "treatment_parts.id", "=", "slots.treatment_part_id")
                            ->orderBy($sortBy);

            $columns = [
                __('reports.business_id') => 'business_ID',
                __('reports.customer_name') => 'customer_name',
                __('reports.customer_number') => 'customer_number',
                __('reports.email') => 'customer_email',
                __('reports.time') => 'time',
                __('reports.treatment_date') => 'treatment_date',
                __('reports.treatment') => 'treatment',
                __('reports.comment') => 'comment',
                __('reports.payment') => 'payment',
                __('keywords.booked_at') => 'booked_at',
            ];

            return $type::of($title, $meta, $queryBuilder, $columns)
                        ->editColumn(__('reports.treatment_date'), [ 
                            'displayAs' => function($result) {
                                return \Carbon\Carbon::parse($result->treatment_date)->format('d-M-Y');
                            },
                            'class' => 'left'
                        ])
                        ->setPaper('a3')
                        ->download(__('reports.treatment_booking_report').'_'.\Carbon\Carbon::now()->format('Y-M-d-H:i:s'));
        }
        else{
            $queryBuilder =  \DB::table("treatment_slots AS slots")
                            ->select(["users.name AS customer_name","users.number AS customer_number","users.email AS customer_email","time","comment","slots.created_at AS booked_at",'treatments.treatment_name AS treatment','dates.date AS treatment_date','payment_methods.title AS payment'])
                            //,'treatment_parts.title AS treatment_part'
                            ->whereBetween("slots.created_at", [$fromDate, $toDate])
                            ->where("slots.business_id",$bID)
                            ->where("slots.parent_slot",NULL)
                            ->join("users", "users.id", "=", "slots.user_id")
                            ->join("treatments", "treatments.id", "=", "slots.treatment_id")
                            ->join("dates", "dates.id", "=", "slots.date_id")
                            ->leftjoin("payment_methods", "payment_methods.id", "=", "slots.payment_method_id")
                            // ->leftjoin("treatment_parts", "treatment_parts.id", "=", "slots.treatment_part_id")
                            ->orderBy($sortBy);

            $columns = [
                __('reports.customer_name') => 'customer_name',
                __('reports.customer_number') => 'customer_number',
                __('reports.email') => 'customer_email',
                __('reports.time') => 'time',
                __('reports.treatment_date') => 'treatment_date',
                __('reports.treatment') => 'treatment',
                __('reports.comment') => 'comment',
                __('reports.payment') => 'payment',
                __('keywords.booked_at') => 'booked_at',
            ];

            return $type::of($title, $meta, $queryBuilder, $columns)
                        ->editColumn(__('reports.treatment_date'), [ 
                            'displayAs' => function($result) use ($dateFormatValue) {
                                return \Carbon\Carbon::parse($result->treatment_date)->format($dateFormatValue);
                            },
                            'class' => 'left'
                        ])
                        ->setPaper('a3')
                        ->showMeta(false)
                        ->download($businessName.'_'.__('reports.treatment_booking_report').'_'.\Carbon\Carbon::now()->format('Y-M-d-H:i:s'));
            
        }                    

    }//----- End getTreatentBookings -----//

    //---###############################################################--//
    public function getUsers($type,$fromDate,$toDate,$sortBy,$bID){
        //abort_unless(\Gate::allows('Reports Users View'),403);
        //$title = __('reports.registered_user_report'); // Report title
        $title = '';

        if($bID == -1){
            $dateFormatValue = "d-m-Y";
        }
        else{
            $business = Business::find($bID);
            $businessName = $business->business_name;
            $dateFormat = Business::find($bID)->Settings->where('key','date_format')->first();
            $dateFormatValue = $dateFormat->value;
        }

        $meta = [ // For displaying filters description on header
            __('keywords.registered_on') => \Carbon\Carbon::parse($fromDate)->format($dateFormatValue).' '. __('keywords.to').' '.\Carbon\Carbon::parse($toDate)->format($dateFormatValue),
            __('keywords.sort_by') => $sortBy
        ];
        //----- Report for Owner -----
        if($bID == -1){
            $queryBuilder = User::select(['role','name','business_id','email','number','business_id','created_at'])
                            ->whereBetween('created_at', [$fromDate, $toDate])
                            ->orderBy($sortBy);
            $columns = [ // Set Column to be displayed
                __('reports.name') => 'name',
                __('reports.registere_at') => 'created_at',
                __('reports.business_id')  => 'business_id',
                __('reports.number') => 'number',
                __('reports.email') => 'email',
                __('reports.role') => 'role',
            ]; 
            return $type::of($title, $meta, $queryBuilder, $columns)
                        ->editColumn(__('reports.registere_at'), [ 
                            'displayAs' => function($result) {
                                return $result->created_at->format('d-M-Y');
                            },
                            'class' => 'left'
                        ])
                        ->editColumn(__('reports.role'), [ 
                            'displayAs' => function($result) {
                            if (is_numeric($result->role)) {
                                $role = Role::find($result->role);
                                return $role->title;
							}
                            else
                                return $result->role;
                            },
                            'class' => 'left'
                        ])
                        ->groupBy(__('reports.business_id'))
                        ->showTotal([
                            'Total' => 'point'
                        ])
                        ->setPaper('a3')
                        ->download(__('reports.registered_user_report').'_'.\Carbon\Carbon::now()->format('Y-M-d-H:i:s'));
        }
        else{
            $queryBuilder = User::select(['name','role','email','number','is_subscribe','created_at'])
                            ->whereBetween('created_at', [$fromDate, $toDate])
                            ->where('business_id',$bID)
                            ->orderBy($sortBy);
            $columns = [ // Set Column to be displayed
                __('reports.name') => 'name',
                __('reports.registere_at') => 'created_at',
                __('reports.subscriber')  => 'is_subscribe',
                __('reports.number') => 'number',
                __('reports.email') => 'email',
                __('reports.role') => 'role',
            ]; 
            return $type::of($title, $meta, $queryBuilder, $columns)
                        ->editColumn(__('reports.registere_at'), [ 
                            'displayAs' => function($result) use ($dateFormatValue) {
                                return $result->created_at->format($dateFormatValue);
                            },
                            'class' => 'left'
                        ])
                        ->editColumn(__('reports.subscriber'), [
                            'displayAs' => function($result) { 
                                return ($result->is_subscribe == 1) ? __('reports.subscribed') : __('reports.unsubscribed');
                            },
                        ])
                        ->editColumn(__('reports.role'), [ 
                            'displayAs' => function($result) {
                            if (is_numeric($result->role)) {
                                $role = Role::find($result->role);
                                return $role->title;
                            }
                            else
                                return $result->role;
                            },
                            'class' => 'left'
                        ])
                        ->setPaper('a3')
                        ->showMeta(false)
                        ->download($businessName.'_'.__('reports.registered_user_report').'_'.\Carbon\Carbon::now()->format('Y-M-d-H:i:s'));
        }                    

        
        // return $type::of($title, $meta, $queryBuilder, $columns)
        //                 ->editColumn('Registered At', [ 
        //                     'displayAs' => function($result) {
        //                         return $result->created_at->format('d-M-Y');
        //                     },
        //                     'class' => 'left'
        //                 ])
                        // ->editColumn('Subscriber', [
                        //     'displayAs' => function($result) { 
                        //         return ($result->is_subscribe == 1) ? 'Subscribed' : 'UnSubscribed';
                        //     },
                        // ])
                        // ->editColumns(['Total Balance', 'Status'], [ // Mass edit column
                        //     'class' => 'right bold'
                        // ])
                        // ->showTotal([ // Used to sum all value on specified column on the last table (except using groupBy method). 'point' is a type for displaying total with a thousand separator
                        //     'Total Balance' => 'point' // if you want to show dollar sign ($) then use 'Total Balance' => '$'
                        // ])
                        //->limit(20) // Limit record to be showed
                        //->download('Registered_User_Report_'.\Carbon\Carbon::now()->format('Y-M-d-H:i:s')); // other available method: download('filename') to download pdf / make() that will producing DomPDF / SnappyPdf instance so you could do any other DomPDF / snappyPdf method such as stream() or download()
    }//----- End getUsers -----//

    //---############################## SMS report #################################---//
    
    public function getSmsReport($type,$fromDate,$toDate,$sortBy,$bID){
        //abort_unless(\Gate::allows('Reports Unique User View'),403);
        //$title = __('reports.sms_report'); // Report title
        $title = '';

        if($bID == -1){
            $dateFormatValue = "d-m-Y";
        }
        else{
            $business = Business::find($bID);
            $businessName = $business->business_name;
            $dateFormat = Business::find($bID)->Settings->where('key','date_format')->first();
            $dateFormatValue = $dateFormat->value;
        }
        
        $meta = [ // For displaying filters description on header
            __('keywords.booked_at') => \Carbon\Carbon::parse($fromDate)->format('Y-m-d').' '.__('keywords.to').' '.\Carbon\Carbon::parse($toDate)->format('Y-m-d'),
            __('keywords.sort_by') => $sortBy
        ];

        //----- Report for Owner -----
        if($bID == -1){

            $queryBuilder =  \DB::table("sms_histories")
                            ->whereBetween("created_at", [$fromDate.' 00:00:00', $toDate.' 23:59:00'])                      
                            ->select(["business_id","message_id","message_price","to","content","status"])

                            ->groupBy('message_id')
                            ->groupBy('message_price')
                            ->groupBy('to')
                            ->groupBy('content')
                            ->groupBy('status')
                            ->groupBy('business_id')
                            ->orderBy($sortBy);

                $columns = [
                    __('reports.business_id') => 'business_id',
                    __('reports.id') => 'message_id',
                    __('reports.price') => 'message_price',
                    __('reports.to') => 'to',
                    __('reports.content') => 'content',
                    __('reports.status') => 'status',
                ];

                return $type::of($title, $meta, $queryBuilder, $columns)
                        ->editColumn(__('reports.status'), [ 
                            'displayAs' => function($result) { 
                                return ($result->status == 1) ? __('reports.sent') : __('reports.pending');
                            },
                        ])
                        ->setPaper('a3')
                        ->groupBy(__('reports.business_id'))
                        ->showTotal([
                            __('reports.price') => '€'
                        ])
                        ->download(__('reports.sms_report').'_'.\Carbon\Carbon::now()->format('Y-M-d-H:i:s'));
        }
        else{
            $queryBuilder =  \DB::table("sms_histories")
                            ->whereBetween("created_at", [$fromDate.' 00:00:00', $toDate.' 23:59:00'])                      
                            ->select(["business_id","message_id","message_price","to","content","status"])
                            ->where('business_id',$bID)
                            ->groupBy('message_id')
                            ->groupBy('message_price')
                            ->groupBy('to')
                            ->groupBy('content')
                            ->groupBy('status')
                            ->groupBy('business_id')
                            ->orderBy($sortBy);

                $columns = [
                    __('reports.business_id') => 'business_id',
                    __('reports.id') => 'message_id',
                    __('reports.price') => 'message_price',
                    __('reports.to') => 'to',
                    __('reports.content') => 'content',
                    __('reports.status') => 'status',
                ];

                return $type::of($title, $meta, $queryBuilder, $columns)
                        ->editColumn(__('reports.status'), [ 
                            'displayAs' => function($result) { 
                                return ($result->status == 1) ? __('reports.sent') : __('reports.pending');
                            },
                        ])
                        ->setPaper('a3')
                        ->groupBy(__('reports.business_id'))
                        ->showTotal([
                            __('reports.price') => '€'
                        ])
                        ->showMeta(false)
                        ->download($businessName.'_'.__('reports.sms_report').'_'.\Carbon\Carbon::now()->format('Y-M-d-H:i:s'));
        }
    }#---------- SMS report --------

}
