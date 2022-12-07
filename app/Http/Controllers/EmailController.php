<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Jobs\SendEmailJob;
use App\Mail\SendMail;
use App\Models\EmailRecord;
use App\Models\Business;
use App\Models\EventSlot;
use App\Models\User;

use Mail;

class EmailController extends Controller
{
    public function list(){
        
        abort_unless(\Gate::any(['Email List View','Email Create','Email View','Email Delete']),403);

        $emails = Business::find(Auth::user()->business_id)->emails;
        $users = Business::find(Auth::user()->business_id)->users->where('role','Customer')->where('is_active',1);
        $events = Business::find(Auth::user()->business_id)->Events->where('date','>=',date('Y-m-d'));
        $dates = Business::find(Auth::user()->business_id)->Dates->where('date','>=',date('Y-m-d'));
        $dateFormat = Business::find(Auth::user()->business_id)->Settings->where('key','date_format')->first(); 
        $timeFormat = Business::find(Auth::user()->business_id)->Settings->where('key','time_format')->first(); 

        return view('email.list',compact('emails','users','events','dates','dateFormat','timeFormat'));
    }

    public function view($subdomain,$id){
        abort_unless(\Gate::allows('Email View'),403);
        $users = '';
        $email = EmailRecord::where(\DB::raw('md5(id)'),$id)->first();
        $dateFormat = Business::find(Auth::user()->business_id)->Settings->where('key','date_format')->first(); 

        if($email->recipients != -1)
            $users = User::whereIn('id',explode(',',$email->recipients))->get();
        return view('email.view',compact('email','users','dateFormat'));
    }

    public function add(Request $request){
        abort_unless(\Gate::allows('Email Create'),403);
        $request->validate([
          'subject' => 'required',
          'content' => 'required',
        ]);
        // if(empty($request->recipients))
        //     $recipients = -1;
        // else  
        //     $recipients = $this->arrImplode(',',$request->recipients);

        $recipients = $this->arrImplode(',',$request->recipients);
        if(empty($recipients))
            $recipients = -1;

        $schedule = $request->_schedule;

        $email = new EmailRecord();
        $email->business_id = Auth::user()->business_id; 
        $email->subject = $request->subject; 
        $email->content = $request->content; 
        $email->recipients = $recipients; 

        //------ Send now if not scheduled -----
        if(empty($schedule)){
            $schedule = date('Y-m-d H:i:s');
            $email->status = 1;
            if($recipients == -1){
                $emails = Business::find(Auth::user()->business_id)->users->where('role','Customer')->where('is_active',1)->pluck('email');
            }
            else{
                $emails = Business::find(Auth::user()->business_id)->users->whereIn('id',explode(',',$recipients))->pluck('email');
            }
        }
        else{
            \Log::channel('custom')->info("Emails adding into EmailRecord table.",['business_id' => Auth::user()->business_id, 'subject' => $request->subject, 'email_text' => $request->content]);
        }

        $email->schedule = $schedule;
        // $email->schedule = \Carbon\Carbon::parse($schedule)->format('Y-m-d h:i:s');
        if($email->save()){
            if(empty($request->_schedule)){
                $brandName = Business::find(Auth::user()->business_id)->Settings->where('key','email_sender_name')->first(); 
                foreach($emails as $email){
                    //--- add in queue --
                    $this->dispatch(new SendEmailJob($email,$request->subject,$request->content,$brandName->value,Auth::user()->business_id));
                    \Log::channel('custom')->info("Emails adding into queue to send now.",['business_id' => Auth::user()->business_id, 'email' => $email, 'subject' => $request->subject, 'email_text' => $request->content]);
                }
            }
            return back()->with(['success' => __("emails.email_has_been_saved")]);
        }    
        else{
            return back()->with(['error' => __("emails.data_not_saved")]);
            \Log::channel('custom')->error("There is an error to save email.",['business_id' => Auth::user()->business_id]);
        }
    }

    public function delete(Request $request){
        abort_unless(\Gate::allows('Email Delete'),403);

        $request->validate([
            'id' => 'required',
        ]);

        $email = EmailRecord::where(\DB::raw('md5(id)') , $request->id)->where('status',0)->first();
        if($email){
            \Log::channel('custom')->info("Deleting email.",['business_id' => Auth::user()->business_id, 'subject' => $email->subject, 'email_text' => $email->content, 'recipients' => $email->recipients,'schedule' => $email->schedule]);

            //------ Add log in DB for location -----//
            $type = "log_for_email";
            $txt = "Schedule email deleted successfully. <br> Email Subject: <b>".$email->subject."</b><br>Email Content: <b>".$email->content."</b>";
            $this->addLocationLog($type,$txt);

            if($email->delete()){
                $data = 'success';
                \Log::channel('custom')->info("Email deleted successfully.",['business_id' => Auth::user()->business_id]);
            } 
            else{
                $data = 'error'; 
                \Log::channel('custom')->error("Error to deleet Email.",['business_id' => Auth::user()->business_id, 'email_id' => $email->id]);
            }
        }
        else{
            $data = 'sent'; 
            \Log::channel('custom')->info("Email cannot deleet because already sent.",['business_id' => Auth::user()->business_id, 'email_id' => $email->id]);
        }
        return json_encode($data);

    }

    public function arrImplode($g,$p) {
        $html = '';
        if(is_array($p)){
            foreach($p as $k => $v){
                if(is_numeric($v))
                    $html .= $v.',';
                else{
                    $va = str_replace('[','',str_replace(']','',$v));
                    $arr = explode(',',$va);
                    $html .= $va.',';
                }    
            }
            return rtrim($html, ',');
        }
        else{
            return $p;
        }
    }

}
