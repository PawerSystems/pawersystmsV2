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
use App\Models\Journal;

class JournalController extends Controller
{
    
    public function index(){
        abort_unless(\Gate::allows('Journal List View'),403);
        $users = Business::find(Auth::user()->business_id)->users->where('is_active',1)->where('role','Customer');
        $dateFormat = Business::find(Auth::user()->business_id)->Settings->where('key','date_format')->first(); 
        return view('journal.index',compact('users','dateFormat'));
    }
    
    public function userJournal($subdomain,$id){
        abort_unless(\Gate::allows('Journal Open'),403);

        $user = User::where(\DB::raw('md5(id)'),$id)->first();
        $dates = Business::find(Auth::user()->business_id)->JounalDates;
        $notes = Business::find(Auth::user()->business_id)->Notes->where('customer_id',$user->id)->where('is_active',1);
        $dateFormat = Business::find(Auth::user()->business_id)->Settings->where('key','date_format')->first(); 
        $settings = Business::find(auth()->user()->business_id)->Settings;

        return view('journal.detail',compact('user','dates','notes','dateFormat','settings'));
    }

    public function addAjax(Request $request){
        abort_unless(\Gate::allows('Journal Notes Create'),403);
        Validator::make($request->all(), [
            'comment'      => ['required'],
            'customer_id'      => ['required'],
            'image' => 'mimes:jpeg,png,jpg,gif,svg,pdf|max:2048',
        ])->validate();

        $imageName = ''; 
        if( !empty($request->image) ){
            $imageName = time().'.'.$request->image->extension();  
            $request->image->move(public_path('journal'), $imageName);
        }
        
        $cus = Journal::create([
            'customer_id' => $request->customer_id,
            'comment' => $request->comment,
            'business_id' => Auth::user()->business_id,
            'user_id' => Auth::user()->id,
            'image' => $imageName,
        ]);

        if($cus->id){
            $request->session()->flash('success',__('journal.chbas'));
            \Log::channel('custom')->info("Comment in journal has been added successfully!",['business_id' => Auth::user()->business_id, 'user_id' => Auth::user()->id, 'comment' => $request->comment, 'customer_id' => $request->customer_id]);

            //------ Add log in DB for location -----//
            $type = "log_for_journal";
            $txt = "Comment in journal has been added successfully! <br> Comment: <b>". $request->comment."</b><br>Customer Email: <b>".$cus->customer->email."</b><br>Added By:<b>".Auth::user()->email."</b>";
            $this->addLocationLog($type,$txt);
        }
        else{
            $request->session()->flash('error',__('journal.tiaetac'));
            \Log::channel('custom')->error("There is an error to add Comment!",['business_id' => Auth::user()->business_id]);
        }
        return \Redirect::back();    
    }

    public function updateAjax(Request $request){
        abort_unless(\Gate::allows('Journal Notes Edit'),403);
        Validator::make($request->all(), [
            'comment_note'      => ['required'],
            'comment_id'      => ['required'],
        ])->validate();

        $comment = Journal::find($request->comment_id);
        $comment->comment = $request->comment_note;
        $comment->update_user_id = Auth::user()->id;
        $dateFormat = Business::find(Auth::user()->business_id)->Settings->where('key','date_format')->first(); 

        if($comment->save()){
            $data['status'] = 'success';
            $data['time'] = __('journal.last_updated').' <i class="far fa-clock"></i> '.\Carbon\Carbon::parse($comment->updated_at)->format($dateFormat->value.' H:i');
            $data['user'] = $comment->uUser->name;

            \Log::channel('custom')->info("Comment has been updated in journal.",['business_id' => Auth::user()->business_id, 'user_id' => $comment->uUser->id, 'comment_id' =>$comment->id, 'comment_note' => $request->comment_note]);

            //------ Add log in DB for location -----//
            $type = "log_for_journal";
            $txt = "Comment in journal has been Updated. <br> Comment: <b>". $request->comment_note."</b><br>Customer Email: <b>".$comment->customer->email."</b><br>Updated By:<b>".Auth::user()->email."</b>";
            $this->addLocationLog($type,$txt);

        }    
        else{
            $data['status'] = 'error';
            \Log::channel('custom')->error("There is an error to update Comment!",['business_id' => Auth::user()->business_id]);
        }
        return \Response::json($data);
    }
}
