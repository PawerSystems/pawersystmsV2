<?php

namespace App\Http\Controllers;

use Importer;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Business;
use App\Models\Treatment;
use App\Models\Date;
use App\Models\Card;
use App\Models\UsedClip;
use App\Models\User;
use App\Models\Role;
use App\Models\TreatmentSlot;
use App\Models\Department;
use App\Jobs\SendEmailJob;
use App\Models\Event;
use App\Models\EventSlot;
use App\Models\TreatmentPart;
use App\Models\PaymentMethod;
use App\Models\Journal;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Str;


class ImportDataController extends Controller
{

    private $treatmentsFile,$datesFile,$dateBookingsFile,$customersDetailFile,$departmentsFile,$treatmentPartsFile,$paymentMethodsFile,$cardsFile,$cardUsesFile,$journalDetailFile,$businessID,$brandName,$eventsFile,$eventBookingsFile;

    //--------- Show View Functions ------//
    public function eventPage(){
        if(Auth::user()->role != 'Owner')
            return \Redirect::back();
        
        $businesses = Business::get();
        return view('import.event',compact('businesses'));
    }

    public function treatmentPage(){
        if(Auth::user()->role != 'Owner')
            return \Redirect::back();
    
        $businesses = Business::get();
        return view('import.treatment',compact('businesses'));
    }

    public function usersPage(){
        if(Auth::user()->role != 'Owner')
            return \Redirect::back();
    
        $businesses = Business::get();
        return view('import.users',compact('businesses'));
    }

    //--------- Import data functions ------//

    public function importTreatmentData(Request $request){
        Validator::make($request->all(), [
            'treatments' => ['required','max:5000','mimes:csv,txt'],
            'dates' => ['required','max:5000','mimes:csv,txt'],
            'date_bookings' => ['required','max:5000','mimes:csv,txt'],
            'customers_details' => ['required','max:5000','mimes:csv,txt'],
            'departments' => ['required','max:5000','mimes:csv,txt'],
            'treatment_parts' => ['required','max:5000','mimes:csv,txt'],
            'payment_method' => ['required','max:5000','mimes:csv,txt'],
            'cards_details' => ['required','max:5000','mimes:csv,txt'],
            'card_use_detail' => ['required','max:5000','mimes:csv,txt'],
            'journals_detail' => ['required','max:5000','mimes:csv,txt'],
            'business_id' => ['required'],
        ])->validate();

        $brandName = Business::find($request->business_id)->Settings->where('key','email_sender_name')->first(); 
        $this->brandName = $brandName->value;

        $this->businessID = $request->business_id;
        $this->business = Business::find($request->business_id);
        $this->treatmentsFile = $request->file('treatments');
        $this->datesFile = $request->file('dates');
        $this->dateBookingsFile = $request->file('date_bookings');
        $this->customersDetailFile = $request->file('customers_details');
        $this->departmentsFile = $request->file('departments');
        $this->treatmentPartsFile = $request->file('treatment_parts');
        $this->paymentMethodsFile = $request->file('payment_method');
        $this->cardsFile = $request->file('cards_details');
        $this->cardUsesFile = $request->file('card_use_detail');
        $this->journalDetailFile = $request->file('journals_detail');

        # add treatments function
        $this->addTreatments();

        $request->session()->flash('success',__('import.data_has_been_imported_successfully'));
        return \Redirect::back();
    }

    public function addTreatments(){

        $collection = Excel::toCollection(new User(),$this->treatmentsFile);

        $j = 0;
        # File's sheets loop
        foreach($collection as $row){
            # If there are multiple sheets then ignore all except first
            if($j > 0)
                continue;
            $i = 0;
            # Records loop
            foreach($row as $value){
                $i++;
                # If it's first row then ignore
                if($i == 1)
                    continue;

                $treatmentID = Treatment::create([
                    'business_id' => $this->businessID,
                    'treatment_name' => $value[1],
                    'inter' => ($value[2] ?: 30),
                    'clips' => ($value[3] ?: 1),
                    'price' => ($value[4] ?: 0),
                    'is_active' => 1,
                ]);

                \Log::channel('custom')->info("Treatment has been created successfully.",['business_id' => $this->businessID, 'Treatment_id' => $treatmentID->id]);

                # Add dates against this treatmet
                $this->createDates($treatmentID->id,$value[0]);
            }
            $j++;
        }

    }

    public function createDates($TnewID,$ToldID){

        $collection = Excel::toCollection(new User(),$this->datesFile);
        
        $j = 0;
        # File's sheets loop
        foreach($collection as $row){
            # If there are multiple sheets then ignore all except first
            if($j > 0)
                continue;
            $i = 0;
            # Records loop
            foreach($row as $value){
                $i++;
                # If it's first row then ignore
                if($i == 1)
                    continue;

                if($value[6] == $ToldID){

                    # Add therapist
                    $adminID = $this->createAdmin($value[1]);

                    $dateID = Date::create([
                        'business_id' => $this->businessID,
                        'user_id' => $adminID,
                        'date' => $value[2],
                        'from' => $value[3],
                        'till' => $value[4],
                        'recurrence' => 'd',
                        'recurring_num' => 1,
                        'description' => $value[5],
                        'is_active' => 1,
                    ]);

                    \Log::channel('custom')->info("Date has been created successfully.",['business_id' => $this->businessID, 'date_id' => $dateID->id]);

                    //------- Assign date treatment ----//
                    \DB::table('date_treatment')->insert([
                        'treatment_id' => $TnewID,
                        'date_id' => $dateID->id,
                    ]);

                    # Add bookings against this date
                    $this->addDateBookings($dateID->id,$value[0],$TnewID,$ToldID);
                }
            }
            $j++;
        }
    }

    public function addDateBookings($DnewId,$DoldId,$tid,$oldTid){

        $collection = Excel::toCollection(new User(),$this->dateBookingsFile);
        
        $j = 0;
        # File's sheets loop
        foreach($collection as $row){
            # If there are multiple sheets then ignore all except first
            if($j > 0)
                continue;
            $i = 0;
            # Records loop
            foreach($row as $value){
                $i++;
                # If it's first row then ignore
                if($i == 1)
                    continue;

                if($value[2] == $DoldId){

                    # Add customer
                    if($value[1] == 0){
                        $customerID = 0;
                        $treatmentID = NULL;
                    }
                    else{
                        $customerID = $this->createCustomer($value[1]);
                        $treatmentID = $tid;
                    }

                    # Add payment method
                    if($value[6] > 0)
                        $paymentMethodID = $this->addPaymentMethod($value[6]);
                    else
                        $paymentMethodID = NULL;

                    # Add treatment Part
                    if($value[7] > 0)
                        $treatmentPartID = $this->addTreatmentPart($value[7]);
                    else
                        $treatmentPartID = NULL;  

                    # Add department 
                    if($value[8] > 0)
                        $departmentID = $this->addDepartment($value[8]);
                    else
                        $departmentID = NULL;

                    $dateBookingID = TreatmentSlot::create([
                        'business_id' => $this->businessID,
                        'user_id' => $customerID,
                        'date_id' => $DnewId,
                        'treatment_id' => $treatmentID,
                        'status' => $value[3],
                        'time' => $value[4],
                        'comments' => $value[5],
                        'payment_method_id' => $paymentMethodID,
                        'treatment_part_id' => $treatmentPartID,
                        'department_id' => $departmentID,
                        'is_active' => 1,
                    ]);

                    \Log::channel('custom')->info("Date bookings has been added successfully.",['business_id' => $this->businessID, 'date booking id' => $dateBookingID->id]);

                    # Add card used details against this booking
                    # If booking is for user 
                    if($value[1] > 0){
                        $newEBid = $oldEBtid = $newEid = $oldEid = NULL;
                        $this->addClipsDetails($customerID,$value[1],$tid,$oldTid,$dateBookingID->id,$value[0],$newEBid,$oldEBtid,$newEid,$oldEid);
                    }
                }
            }
            $j++;
        }
    }

    public function createAdmin($AoldId){

        $collection = Excel::toCollection(new User(),$this->customersDetailFile);
        $j = 0;
        # File's sheets loop
        foreach($collection as $row){
            # If there are multiple sheets then ignore all except first
            if($j > 0)
                continue;
            $i = 0;
            # Records loop
            foreach($row as $value){
                $i++;
                # If it's first row then ignore
                if($i == 1)
                    continue;

                if($value[0] == $AoldId){

                    $check = User::where('email',strtolower($value[3]))->where('business_id',$this->businessID)->first();

                    if($check == null){

                        $role = Role::where('title','Admin')->where('business_id',$this->businessID)->first();

                        $password = Str::random(8);

                        $admin = User::create([
                            'business_id' => $this->businessID,
                            'name' => $value[1],
                            'role' => $role->id,
                            'language' => ($value[2] ?: 'dk'),
                            'email' => strtolower($value[3]),
                            'number' => $value[4],
                            'country_id' => 1,
                            'access' => -1,
                            'cprnr' => $value[5],
                            'password' => \Hash::make($password),
                            'is_subscribe' => 1,
                            'survey_date' => \Carbon\Carbon::parse($value[6])->format('Y-m-d'),
                            'is_active' => 0,
                            'is_therapist' => 0,
                        ]);

                        //------- Assign role to this User ----//
                        \DB::table('role_user')->insert([
                            'role_id' => $role->id,
                            'user_id' => $admin->id,
                        ]);

                        \Log::channel('custom')->info("Admin added successfully.",['business_id' => $this->businessID, 'admin_id' => $admin->id]);

                        //------ Send email for registration ----
                        \App::setLocale($admin->language);

                        $url = "https://".$this->business->business_name.'.'.config('app.name').'/login';
                        $subject =  __('emailtxt.user_register_email_subject',['name' => $this->brandName]);
                        $content =  __('emailtxt.user_register_email_txt',['name' =>$admin->name, 'email' => $admin->email, 'url' => $url, 'password' => $password]);
                        
                        if($admin->email != ''){
                            \Log::channel('custom')->info("Sending email to admin with login credentials.",['business_id' => $this->businessID, 'user_id' => $admin->id, 'subject' => $subject , 'email_text' => $content]);

                            $this->dispatch(new SendEmailJob($admin->email,$subject,$content,$this->brandName,$this->businessID));
                        }


                        return $admin->id;
                    }
                    else{
                        return $check->id;
                    }
                }
            }
            $j++;
        }
    }

    public function createCustomer($ColdId){

        $collection = Excel::toCollection(new User(),$this->customersDetailFile);
        $j = 0;
        # File's sheets loop
        foreach($collection as $row){
            # If there are multiple sheets then ignore all except first
            if($j > 0)
                continue;
            $i = 0;
            # Records loop
            foreach($row as $value){
                $i++;
                # If it's first row then ignore
                if($i == 1)
                    continue;

                if($value[0] == $ColdId){

                    $check = User::where('email',strtolower($value[3]))->where('business_id',$this->businessID)->first();

                    if($check == null){

                        $password = Str::random(8);

                        $customer = User::create([
                            'business_id' => $this->businessID,
                            'name' => $value[1],
                            'role' => 'Customer',
                            'language' => ($value[2] ?: 'dk'),
                            'email' => strtolower($value[3]),
                            'number' => $value[4],
                            'country_id' => 1,
                            'access' => -1,
                            'cprnr' => $value[5],
                            'password' => \Hash::make($password),
                            'is_subscribe' => 1,
                            'survey_date' => \Carbon\Carbon::parse($value[6])->format('Y-m-d'),
                            'is_active' => 1,
                            'is_logged_in' => 0,
                        ]);

                        \Log::channel('custom')->info("Customer added successfully.",['business_id' => $this->businessID, 'customer_id' => $customer->id]);

                        //------ Send email for registration ----
                        // \App::setLocale($customer->language);

                        // $url = "https://".$this->business->business_name.'.'.config('app.name').'/login';
                        // $subject =  __('emailtxt.user_register_email_subject',['name' => $this->brandName]);
                        // $content =  __('emailtxt.user_register_email_txt',['name' =>$customer->name, 'email' => $customer->email, 'url' => $url, 'password' => $password]);
                        
                        // if($customer->email != ''){
                        //     \Log::channel('custom')->info("Sending email to customer with login credentials.",['business_id' => $this->businessID, 'user_id' => $customer->id, 'subject' => $subject , 'email_text' => $content]);

                        //     $this->dispatch(new SendEmailJob($customer->email,$subject,$content,$this->brandName));
                        // }
                        if(!empty($this->treatmentsFile))
                            $this->addJournal($customer->id,$ColdId);

                        return $customer->id;
                    }
                    else{
                        return $check->id;
                    }
                }
            }
            $j++;
        }
    }

    public function addPaymentMethod($PoldId){

        $collection = Excel::toCollection(new User(),$this->paymentMethodsFile);
        $j = 0;
        # File's sheets loop
        foreach($collection as $row){
            # If there are multiple sheets then ignore all except first
            if($j > 0)
                continue;
            $i = 0;
            # Records loop
            foreach($row as $value){
                $i++;
                # If it's first row then ignore
                if($i == 1)
                    continue;

                if($value[0] == $PoldId){

                    $check = PaymentMethod::where('title',$value[1])->where('business_id',$this->businessID)->first();

                    if($check == null){

                        $PaymentMethod = PaymentMethod::create([
                            'business_id' => $this->businessID,
                            'title' => $value[1],
                            'is_active' => 1,
                        ]);

                        \Log::channel('custom')->info("Payment Method added successfully.",['business_id' => $this->businessID, 'PaymentMethodID' => $PaymentMethod->id,'PaymentMethodTitle' => $PaymentMethod->title]);

                        return $PaymentMethod->id;
                    }
                    else{
                        return $check->id;
                    }
                }
            }
            $j++;
        }
    }

    public function addTreatmentPart($ToldId){
        // $this->departmentsFile = $request->file('departments');

        $collection = Excel::toCollection(new User(),$this->treatmentPartsFile);
        $j = 0;
        # File's sheets loop
        foreach($collection as $row){
            # If there are multiple sheets then ignore all except first
            if($j > 0)
                continue;
            $i = 0;
            # Records loop
            foreach($row as $value){
                $i++;
                # If it's first row then ignore
                if($i == 1)
                    continue;

                if($value[0] == $ToldId){

                    $check = TreatmentPart::where('title',$value[1])->where('business_id',$this->businessID)->first();

                    if($check == null){

                        $TreatmentPart = TreatmentPart::create([
                            'business_id' => $this->businessID,
                            'title' => $value[1],
                            'Torder' => 1,
                            'is_active' => 1,
                        ]);

                        \Log::channel('custom')->info("Treatment Part added successfully.",['business_id' => $this->businessID, 'TreatmentPartID' => $TreatmentPart->id,'TreatmentPartTitle' => $TreatmentPart->title]);

                        return $TreatmentPart->id;
                    }
                    else{
                        return $check->id;
                    }
                }
            }
            $j++;
        }
    }

    public function addDepartment($DoldId){

        $collection = Excel::toCollection(new User(),$this->departmentsFile);
        $j = 0;
        # File's sheets loop
        foreach($collection as $row){
            # If there are multiple sheets then ignore all except first
            if($j > 0)
                continue;
            $i = 0;
            # Records loop
            foreach($row as $value){
                $i++;
                # If it's first row then ignore
                if($i == 1)
                    continue;

                if($value[0] == $DoldId){

                    $check = Department::where('name',$value[1])->where('business_id',$this->businessID)->first();

                    if($check == null){

                        $department = Department::create([
                            'business_id' => $this->businessID,
                            'name' => $value[1],
                            'is_active' => 1,
                        ]);

                        \Log::channel('custom')->info("Department added successfully.",['business_id' => $this->businessID, 'DepartmentID' => $department->id,'DepartmentTitle' => $department->name]);

                        return $department->id;
                    }
                    else{
                        return $check->id;
                    }
                }
            }
            $j++;
        }
    }

    public function addClipsDetails($newUId,$oldUID,$newTid,$oldTid,$newDBId,$oldDBId,$newEBid,$oldEBtid,$newEid,$oldEid){

        if($newEBid == NULL){ # If this function hit for treatments
            $type = 1;
            $oldBId = $oldDBId;
        }    
        else{ # else this function hit for events
            $type = 2;  
            $oldBId = $oldEBtid;  
        }

        $collection = Excel::toCollection(new User(),$this->cardUsesFile);
        $j = 0;
        # File's sheets loop
        foreach($collection as $row){
            # If there are multiple sheets then ignore all except first
            if($j > 0)
                continue;
            $i = 0;
            # Records loop
            foreach($row as $value){
                $i++;
                # If it's first row then ignore
                if($i == 1)
                    continue;

                if($value[3] == $oldBId){

                    # Get card id
                    $card = $this->addCard($value[1],$newUId,$oldUID,$type);

                    if($card){

                        if($newEBid == NULL)                        
                            $check = UsedClip::where('card_id',$card)->where('treatment_slot_id',$newDBId)->first();
                        else                      
                            $check = UsedClip::where('card_id',$card)->where('event_slot_id',$newEBid)->first();

                        if($check == null){

                            $UsedClip = UsedClip::create([
                                'card_id' => $card,
                                'treatment_id' => $newTid,
                                'treatment_slot_id' => $newDBId,
                                'event_id' => $newEid,
                                'event_slot_id' => $newEBid,
                                'amount' => $value[4],
                                'is_active' => 1,
                            ]);

                            \Log::channel('custom')->info("Clip used added successfully.",['business_id' => $this->businessID, 'UsedClipID' => $UsedClip->id]);
                        }
                    }    
                }
            }
            $j++;
        }

    }

    public function addCard($oldCardId,$newUID,$oldUID,$type){

        $collection = Excel::toCollection(new User(),$this->cardsFile);
        $j = 0;
        # File's sheets loop
        foreach($collection as $row){
            # If there are multiple sheets then ignore all except first
            if($j > 0)
                continue;
            $i = 0;
            # Records loop
            foreach($row as $value){
                $i++;
                # If it's first row then ignore
                if($i == 1)
                    continue;

                if($value[1] == $oldUID && $value[0] == $oldCardId){

                    $check = Card::where('user_id',$newUID)->where('type',$type)->where('business_id',$this->businessID)->first();

                    if($check == null){

                        $card = Card::create([
                            'business_id' => $this->businessID,
                            'user_id' => $newUID,
                            'name' => $value[2],
                            'expiry_date' => \Carbon\Carbon::parse($value[3])->format('Y-m-d'),
                            'clips' => $value[4],
                            'type' => $type,
                            'is_active' => 1,
                        ]);

                        \Log::channel('custom')->info("Card added successfully.",['business_id' => $this->businessID,'CardID' => $card->id,'cardTitle' => $card->name]);

                        return $card->id;
                    }
                    else{
                        return $check->id;
                    }
                }
            }
            $j++;
            return false;
        }
    }

    public function addJournal($CNewId,$COldId){

        $collection = Excel::toCollection(new User(),$this->journalDetailFile);
        $j = 0;
        # File's sheets loop
        foreach($collection as $row){
            # If there are multiple sheets then ignore all except first
            if($j > 0)
                continue;
            $i = 0;
            # Records loop
            foreach($row as $value){
                $i++;
                # If it's first row then ignore
                if($i == 1)
                    continue;

                if($value[1] == $COldId){

                    $check = Journal::where('user_id',$CNewId)->where('business_id',$this->businessID)->count();
                    $admin = User::where('role','Super Admin')->where('business_id',$this->businessID)->first();

                    if($check == 0){

                        $card = Journal::create([
                            'business_id' => $this->businessID,
                            'user_id' => $admin->id,
                            'customer_id' => $CNewId,
                            'comment' => $value[2],
                            'created_at' => \Carbon\Carbon::parse($value[3])->format('Y-m-d H:i:s'),
                            'updated_at' => \Carbon\Carbon::parse($value[3])->format('Y-m-d H:i:s'),
                            'is_active' => 1,
                        ]);

                        \Log::channel('custom')->info("Jounal Details added successfully.",['business_id' => $this->businessID,'customer_id' => $CNewId]);

                    }
                }
            }
            $j++;
            return false;
        }
    }

    //--------- Import User Data Functions ------//
    public function importUserData(Request $request){
        Validator::make($request->all(), [
            'customers_details' => ['required','max:5000','mimes:csv,txt'],
            'business_id' => ['required'],
        ])->validate();

        $brandName = Business::find($request->business_id)->Settings->where('key','email_sender_name')->first(); 
        $this->brandName = $brandName->value;

        $this->businessID = $request->business_id;
        $this->business = Business::find($request->business_id);
        $this->customersDetailFile = $request->file('customers_details');

        # add treatments function
        $this->addUsers();

        $request->session()->flash('success',__('import.data_has_been_imported_successfully'));
        return \Redirect::back();
    }

    public function addUsers(){

        $collection = Excel::toCollection(new User(),$this->customersDetailFile);
        $j = 0;
        # File's sheets loop
        foreach($collection as $row){
            # If there are multiple sheets then ignore all except first
            if($j > 0)
                continue;
            $i = 0;
            # Records loop
            foreach($row as $value){
                $i++;
                # If it's first row then ignore
                if($i == 1)
                    continue;

                $check = User::where('email',strtolower($value[3]))->where('business_id',$this->businessID)->first();
                if($value[7] == 'Admin'){
                    $r = Role::where('business_id',$this->businessID)->where('title','Admin')->first();
                    $role = $r->id;
                }
                else{
                    $role = $value[7];   
                }

                if($check == null){

                    $password = Str::random(8);

                    $customer = User::create([
                        'business_id' => $this->businessID,
                        'name' => $value[1],
                        'role' => $role,
                        'language' => ($value[2] ?: 'dk'),
                        'email' => strtolower($value[3]),
                        'number' => $value[4],
                        'country_id' => 1,
                        'access' => -1,
                        'cprnr' => $value[5],
                        'password' => \Hash::make($password),
                        'is_subscribe' => 1,
                        'survey_date' => \Carbon\Carbon::parse($value[6])->format('Y-m-d'),
                        'is_active' => 0,
                        'is_logged_in' => 0,
                        'is_therapist' => 0,
                    ]);

                    \Log::channel('custom')->info("User added successfully.",['business_id' => $this->businessID, 'customer_id' => $customer->id,'Role' => $role]);

                    //------ Send email for registration ----
                    // \App::setLocale($customer->language);

                    // $url = "https://".$this->business->business_name.'.'.config('app.domain').'/login';
                    // $subject =  __('emailtxt.user_register_email_subject',['name' => $this->brandName]);
                    // $content =  __('emailtxt.user_register_email_txt',['name' =>$customer->name, 'email' => $customer->email, 'url' => $url, 'password' => $password]);
                    
                    // if($customer->email != ''){
                    //     \Log::channel('custom')->info("Sending email to customer with login credentials.",['business_id' => $this->businessID, 'user_id' => $customer->id, 'subject' => $subject , 'email_text' => $content]);

                    //     $this->dispatch(new SendEmailJob($customer->email,$subject,$content,$this->brandName));
                    // }
                }
            }
            $j++;
        }
    }

    //--------- Import events functions ------//

    public function importEventData(Request $request){
        Validator::make($request->all(), [
            'events'            => ['required','max:5000','mimes:csv,txt'],
            'event_bookings'    => ['required','max:5000','mimes:csv,txt'],
            'customers_details' => ['required','max:5000','mimes:csv,txt'],
            'cards_details'     => ['required','max:5000','mimes:csv,txt'],
            'card_use_detail'   => ['required','max:5000','mimes:csv,txt'],
            'business_id'       => ['required'],
        ])->validate();

        $brandName = Business::find($request->business_id)->Settings->where('key','email_sender_name')->first(); 
        $this->brandName = $brandName->value;

        $this->businessID = $request->business_id;
        $this->business = Business::find($request->business_id);
        $this->eventsFile = $request->file('events');
        $this->eventBookingsFile = $request->file('event_bookings');
        $this->customersDetailFile = $request->file('customers_details');
        $this->cardsFile = $request->file('cards_details');
        $this->cardUsesFile = $request->file('card_use_detail');

        # add treatments function
        $this->addEvent();

        $request->session()->flash('success',__('import.data_has_been_imported_successfully'));
        return \Redirect::back();
    }

    public function addEvent(){

        $collection = Excel::toCollection(new User(),$this->eventsFile);

        $j = 0;
        # File's sheets loop
        foreach($collection as $row){
            # If there are multiple sheets then ignore all except first
            if($j > 0)
                continue;
            $i = 0;
            # Records loop
            foreach($row as $value){
                $i++;
                # If it's first row then ignore
                if($i == 1)
                    continue;

                $adminID = $this->createAdmin($value[1]);

                $eventID = Event::create([
                    'business_id' => $this->businessID,
                    'user_id' => $adminID,
                    'name' => $value[2],
                    'date' => $value[3],
                    'time' => $value[4],
                    'duration' => $value[5],
                    'slots' => $value[6],
                    'price' => ($value[7] ?: 0),
                    'clips' => ($value[8] ?: 0),
                    'description' => $value[9],
                    'status' => 'Active',
                    'is_guest' => ($value[10] ?: 0),
                    'is_active' => 1,
                ]);

                \Log::channel('custom')->info("Event has been created successfully.",['business_id' => $this->businessID, 'Event_id' => $eventID->id]);

                # Add dates against this treatmet
                $this->addEventBookings($eventID->id,$value[0]);
            }
            $j++;
        }
    }

    public function addEventBookings($EnewId,$EoldId){

        $collection = Excel::toCollection(new User(),$this->eventBookingsFile);
        
        $j = 0;
        # File's sheets loop
        foreach($collection as $row){
            # If there are multiple sheets then ignore all except first
            if($j > 0)
                continue;
            $i = 0;
            # Records loop
            foreach($row as $value){
                $i++;
                # If it's first row then ignore
                if($i == 1)
                    continue;

                if($value[1] == $EoldId){

                    # Add customer
                    if($value[2] == 0){
                        $customerID = 0;
                    }
                    else{
                        $customerID = $this->createCustomer($value[2]);
                    }

                    $eventBookingID = EventSlot::create([
                        'business_id' => $this->businessID,
                        'event_id' => $EnewId,
                        'user_id' => $customerID,
                        'parent_slot' => ($value[3] != 'NULL' ? $eventBookingID->id : NULL ),
                        'is_guest' => ($value[4] ?: 0),
                        'comment' => $value[5],
                        'status' => $value[6],
                        'is_active' =>  1,
                    ]);                    

                    \Log::channel('custom')->info("Event bookings has been added successfully.",['business_id' => $this->businessID, 'date_booking_id' => $eventBookingID->id]);

                    # Add card used details against this booking
                    # If booking is for user 
                    $newDBId = $oldDBId = $tid = $oldTid = NULL;
                    $this->addClipsDetails($customerID,$value[2],$tid,$oldTid,$newDBId,$oldDBId,$eventBookingID->id,$value[0],$EnewId,$EoldId);

                }
            }
            $j++;
        }
    }
}
