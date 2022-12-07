<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Card;
use App\Models\Business;
use App\Models\UsedClip;
use App\Models\Treatment;
use App\Models\Event;
use App\Jobs\SendEmailJob;
use App\Notifications\BookingNotification;

class CardController extends Controller
{
    //#################################################
    public function list(){
        abort_unless(\Gate::allows('Card List View'),403);
        $cards = Business::find(Auth::user()->business_id)->cards;
        $dateFormat = Business::find(Auth::user()->business_id)->Settings->where('key','date_format')->first(); 
        return view('card.list',compact('cards','dateFormat'));
    }

    //#################################################
    public function add(){
        abort_unless(\Gate::allows('Card Create'),403);

        $users = Business::find(Auth::user()->business_id)->users->where('role','Customer');
        return view('card.add',compact('users'));
    }

    //#################################################
    public function edit($subdomain,$id){
        abort_unless(\Gate::allows('Card Edit'),403);

        $dateFormat = Business::find(Auth::user()->business_id)->Settings->where('key','date_format')->first(); 
        $users = Business::find(Auth::user()->business_id)->users->where('role','Customer');
        $card = Card::where(\DB::raw('md5(id)') , $id)->first();
        return view('card.edit',compact('card','users','dateFormat'));
    }

    //#################################################
    public function update(Request $request){
        abort_unless(\Gate::allows('Card Edit'),403);

        Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            '_date' => ['required'],
            'type' => ['required'],
        ])->validate();

        $card = Card::find($request->id);
        //--- if change card type then check if clips are in use
        $used = 0;
        if($request->type != $card->type){
            $used = Card::find($request->id)->allClipsUsed->count();
        }
        if($used == 0){
            $card->name = $request->name;
            $card->expiry_date = $request->_date;
            $card->type        = $request['type'];

            if($request->status)
                $card->is_active = 1;
            else
                $card->is_active = 0;
            
            if($card->save()){
                $request->session()->flash('success',__('card.chbus'));
                \Log::channel('custom')->info("Card has been updated successfully!",['business_id' => Auth::user()->business_id, 'card_id' => $request->id]);

                //------ Add log in DB for location -----//
                $type = "log_for_card_and_clips";
                $txt = "Card has been updated successfully! <br> Card: <b>".$card->name."</b><br> Card User: <b>".$card->user->name."</b>";
                $this->addLocationLog($type,$txt);
            }
            else{
                \Log::channel('custom')->warning("Error to update card information.",['business_id' => Auth::user()->business_id, 'card_id' => $request->id]);
                $request->session()->flash('error',__('card.tisetuc'));
            }
        }
        else{
            $request->session()->flash('error',__('card.cfcaiuscnuc'));
            \Log::channel('custom')->info("Clips from card aleady in use so can not update Card.",['business_id' => Auth::user()->business_id, 'card_id' => $request->id]);

        }
        return \Redirect::back();
    }

    //#################################################
    public function create(Request $request){
        abort_unless(\Gate::allows('Card Create'),403);

        Validator::make($request->all(), [
            'user_id' => ['required'],
            'name' => ['required', 'string', 'max:255'],
            '_date' => ['required'],
            'type' => ['required'],
        ])->validate();

        $dateFormat = Business::find(Auth::user()->business_id)->Settings->where('key','date_format')->first(); 

        $card = new Card();
        $card->name        = $request['name'];
        $card->user_id     = $request['user_id'];
        $card->clips       = $request['clips'];
        $card->type        = $request['type'];
        $card->expiry_date = $request['_date'];
        $card->business_id = Auth::user()->business_id;
        $card->save();

        \Log::channel('custom')->info("New card added.",['business_id' => Auth::user()->business_id, 'card_id' => $card->id]);

        //------ Add log in DB for location -----//
        $type = "log_for_card_and_clips";
        $txt = "Card has been created successfully! <br> Card: <b>".$card->name."</b><br> Card User: <b>".$card->user->name."</b>";
        $this->addLocationLog($type,$txt);

        if($card->id){


            //-------- Send Email to user about Card ----------
            $brandName = Business::find( Auth::user()->business_id)->Settings->where('key','email_sender_name')->first(); 
            
            // -------  for this user's will change language ------
            \App::setLocale($card->user->language);

            $subject =  __('emailtxt.card_created_subject',['name' => $brandName->value]);
            $content =  __('emailtxt.card_created_txt',['name' => $card->user->name,'title' => $card->name, 'expiry' => \Carbon\Carbon::parse($card->expiry_date)->format($dateFormat->value), 'clips' => ($card->clips ?: 0), 'for' => ($card->type == 2 ? __('card.event') : __('card.treatment')  )  ]);

            if($card->user->email != ''){
                \Log::channel('custom')->info("Sending email to customer about card.",['business_id' => Auth::user()->business_id, 'user_id' => $card->user->id, 'subject' => $subject, 'content' => $content]);

                $this->dispatch(new SendEmailJob($card->user->email,$subject,$content,$brandName->value,$card->user->business_id));
            }
            else{
                \Log::channel('custom')->warning("Email not exist of customer.",['business_id' => Auth::user()->business_id, 'user_id' => $card->user->id]);
            }

            $request->session()->flash('success',__('card.chbcs'));
        }
        else{
            \Log::channel('custom')->error("Error to card added.",['business_id' => Auth::user()->business_id]);

            $request->session()->flash('error',__('card.tiaetcc'));
        }
        return \Redirect::back();
    }
    //#################################################
    public function updateAjax(Request $request){
        abort_unless(\Gate::allows('Card Clip Puchase'),403);

        Validator::make($request->all(), [
            'id' => ['required'],
            'purchase' => ['required', 'string', 'max:255'],
        ])->validate();

        $card = Card::find($request->id);
        $card->clips += $request->purchase;

        if($card->save()){
            \Log::channel('custom')->info("Card has been updated | Add clips in card.",['business_id' => Auth::user()->business_id, 'card_id' => $card->id]);

            //------ Add log in DB for location -----//
            $type = "log_for_card_and_clips";
            $txt = "Clips has been added in card. <br> Card: <b>".$card->name."</b><br> Card User: <b>".$card->user->name."</b><br>Clips Added: <b>".$request->purchase."</b>";
            $this->addLocationLog($type,$txt);

            //-------- Send Email to user about Card ----------
            $brandName = Business::find(Auth::user()->business_id)->Settings->where('key','email_sender_name')->first(); 
            $dateFormat = Business::find(Auth::user()->business_id)->Settings->where('key','date_format')->first(); 
            $smsSetting = Business::find(Auth::user()->business_id)->Settings->where('key','sms_setting')->first();
            $clipAdd = Business::find(Auth::user()->business_id)->Settings->where('key','clip_add')->first();

            
            // -------  for this user's will change language ------
            \App::setLocale($card->user->language);

            $subject =  __('emailtxt.card_clip_purchased_subject',['name' => $brandName->value]);
            $content =  __('emailtxt.card_clip_purchased_txt',['name' => $card->user->name, 'purchase' => $request->purchase ,'title' => $card->name, 'expiry' => \Carbon\Carbon::parse($card->expiry_date)->format($dateFormat->value), 'clips' => ($card->clips ?: 0), 'for' => ($card->type == 2 ? __('card.event') : __('card.treatment')  ) ]);

            //------- Send sms notification if active from settings -----
            if($clipAdd->value == 'true' && $smsSetting->value == 'true'){
                $sms =  __('smstxt.card_clip_purchased_txt',['name' => $card->user->name, 'purchase' => $request->purchase ,'title' => $card->name, 'expiry' => \Carbon\Carbon::parse($card->expiry_date)->format($dateFormat->value), 'clips' => ($card->clips ?: 0), 'for' => ($card->type == 2 ? __('card.event') : __('card.treatment')  ) ]);
                //--- send sms notification ---
                \Log::channel('custom')->info("Sending sms to customer about card.",['business_id' => Auth::user()->business_id, 'user_id' => $card->user->id, 'sms' => $sms]);

                //$card->user->notify(new BookingNotification($sms));
                $this->smsSendWithCheck($sms,$card->user->id);
            }

            if($card->user->email != ''){
                \Log::channel('custom')->info("Sending email to customer about card.",['business_id' => Auth::user()->business_id, 'user_id' => $card->user->id, 'subject' => $subject, 'email_text' => $content]);

                $this->dispatch(new SendEmailJob($card->user->email,$subject,$content,$brandName->value,$card->user->business_id));
            }
            else{
                \Log::channel('custom')->warning("Email not exist of customer.",['business_id' => Auth::user()->business_id, 'user_id' => $card->user->id]);
            }

            $data = 'success';
        }    
        else{
            \Log::channel('custom')->error("Error to update card.",['business_id' => Auth::user()->business_id]);
            $data =  'error';   
        }
        return json_encode($data);    

    }

    //#################################################
    public function getCardUsedClipsAjax(Request $request){
        Validator::make($request->all(), [
            'id' => ['required'],
        ])->validate();

        $card = Card::find($request->id);
        $clip = UsedClip::where('card_id',$request->id)->where(function ($query) use ($request) {
            $query->orWhere('treatment_slot_id',$request->slotID)
            ->orWhere('event_slot_id',$request->slotID);
        })->where('is_active',1)->first();
        
        if($clip != Null){
            $data['status'] = 'used';
            $data['id']  = $clip->id;
            $data['balance'] = $card->clips;
        }
        else{
            $data['status'] = 'new';
            $data['balance'] = $card->clips;
        }    

        return json_encode($data);    
    }

    //#################################################
    public function bookClipsAjax(Request $request){
        Validator::make($request->all(), [
            'cardID' => ['required'],
            'slotID' => ['required']
        ])->validate();

        $smsSetting = Business::find(Auth::user()->business_id)->Settings->where('key','sms_setting')->first();
        $clipUsed = Business::find(Auth::user()->business_id)->Settings->where('key','clip_used')->first();


        if($request->treatmentID){
            $howManyClips = Treatment::find($request->treatmentID);
            $columName = 'treatment_slot_id';
            $columName2 = 'treatment_id';
            $EorTid = $request->treatmentID;
        }
        else if($request->eventID){
            $howManyClips = Event::find($request->eventID);
            $columName = 'event_slot_id';
            $columName2 = 'event_id';
            $EorTid = $request->eventID;
        }
        else{
            $data['status'] = 'error';
            return json_encode($data);
        }

        $check = UsedClip::where($columName,$request->slotID)->count();
        if($check == 0){
            //------ Get treatment clips -----
            
            //------ Get card details -----
            $card = Card::find($request->cardID);
            //--- Check if card has sufficent clips -----
            if($card->clips >= $howManyClips->clips){

                $clip = new UsedClip();
                $clip->card_id = $request->cardID;
                $clip->$columName = $request->slotID;
                $clip->$columName2 = $EorTid;
                $clip->amount = $howManyClips->clips;

                if($clip->save()){

                    \Log::channel('custom')->info("Clips used from card.",['business_id' => Auth::user()->business_id, 'card_id' => $clip->card_id, 'clips' => $clip->amount]);

                    //------ Add log in DB for location -----//
                    $type = "log_for_card_and_clips";
                    $txt = "Clips used from card. <br> Card: <b>".$card->name."</b><br> Card User: <b>".$card->user->name."</b><br>Clips Used: <b>".$howManyClips->clips."</b>";
                    $this->addLocationLog($type,$txt);

                    $cc = $card->clips;
                    $ccc = $cc-$howManyClips->clips;
                    $card->clips = $ccc;
                    $card->save();
                    
                    $data['status'] = 'success';
                    $data['id'] = $clip->id;
                    $data['balance'] = $ccc;

                    //-------- Send Email to user about Clips Used ----------
                    $brandName = Business::find(Auth::user()->business_id)->Settings->where('key','email_sender_name')->first(); 
                    $dateFormat = Business::find(Auth::user()->business_id)->Settings->where('key','date_format')->first(); 

                    // -------  for this user's will change language ------
                    \App::setLocale($card->user->language);

                    $subject =  __('emailtxt.clips_used_subject',['name' => $brandName->value]);
                    $content =  __('emailtxt.clips_used_txt',['name' => $card->user->name, 'used' => $howManyClips->clips, 'title' => $card->name, 'expiry' => \Carbon\Carbon::parse($card->expiry_date)->format($dateFormat->value), 'clips' => ($card->clips ?: 0), 'for' => ($columName == 'treatment_slot_id' ? __('card.treatment') :  __('card.event') ) ]);

                    //------- Send sms notification if active from settings -----
                    if($clipUsed->value == 'true' && $smsSetting->value == 'true'){
                        $sms =  __('smstxt.clips_used_txt',['name' => $card->user->name, 'used' => $howManyClips->clips, 'title' => $card->name, 'expiry' => \Carbon\Carbon::parse($card->expiry_date)->format($dateFormat->value), 'clips' => ($card->clips ?: 0), 'for' => ($columName == 'treatment_slot_id' ? __('card.treatment') :  __('card.event') ) ]);

                        //--- send sms notification ---
                        \Log::channel('custom')->info("Sending sms to customer about use of clips.",['business_id' => Auth::user()->business_id, 'user_id' => $card->user->id, 'sms' => $sms]);

                        $this->smsSendWithCheck($sms,$card->user->id);

                    }

                    if($card->user->email != ''){
                        \Log::channel('custom')->info("Sending email to customer about use of clips.",['business_id' => Auth::user()->business_id, 'user_id' => $card->user->id, 'subject' => $subject, 'email_text' => $content]);

                        $this->dispatch(new SendEmailJob($card->user->email,$subject,$content,$brandName->value,$card->user->business_id));
                    }else{
                        \Log::channel('custom')->warning("User did not have email address.",['business_id' => Auth::user()->business_id, 'user_id' => $card->user->id]);
                    }
                }
                else{
                    $data['status'] = 'error';
                    \Log::channel('custom')->warning("Error to cut clips from card .",['business_id' => Auth::user()->business_id]);
                }
            }
            else{
                \Log::channel('custom')->info("Trying to use clips from card but card did not have sufficent clips.",['business_id' => Auth::user()->business_id, 'card_id' => $card->id]);
                $data['status'] = 'less';
            }
        }
        else{
            \Log::channel('custom')->info("Clips already used for this booking.",['business_id' => Auth::user()->business_id, 'slot_id' => $request->slotID, 'columName' => $columName]);
            $data['status'] = 'exist';
        }
        return json_encode($data);        
    }

    //##########################################
    public function deleteClipAjax(Request $request){
        Validator::make($request->all(), [
            'id' => ['required']
        ])->validate();

        $smsSetting = Business::find(Auth::user()->business_id)->Settings->where('key','sms_setting')->first();
        $clipUsed = Business::find(Auth::user()->business_id)->Settings->where('key','clip_used')->first();

        $dateFormat = Business::find(Auth::user()->business_id)->Settings->where('key','date_format')->first(); 
        $clip = UsedClip::find($request->id);

        if($clip != Null){
            Card::find($clip->card_id)->increment('clips',$clip->amount);

            $data['balance'] = Card::find($clip->card_id)->clips;
            $data['card'] = $clip->card_id;
            $data['treatment'] = $clip->treatment_id;
            $data['event'] = $clip->event_id;
            if($clip->treatment_id != Null)
                $data['slot'] = $clip->treatment_slot_id;
            else    
                $data['slot'] = $clip->event_slot_id;

            $data['status'] = 'success';

            $clip->delete();

            \Log::channel('custom')->info("Clips restored into card.",['business_id' => Auth::user()->business_id, 'card_id' => $clip->card_id]);

            //-------- Send Email to user about Clips Used ----------
            $brandName = Business::find(Auth::user()->business_id)->Settings->where('key','email_sender_name')->first(); 
            $card = Card::find($clip->card_id);

            //------ Add log in DB for location -----//
            $type = "log_for_card_and_clips";
            $txt = "Clips restored into card. <br> Card: <b>".$card->name."</b><br> Card User: <b>".$card->user->name."</b><br>Clips restored: <b>".$clip->amount."</b>";
            $this->addLocationLog($type,$txt);

             // -------  for this user's will change language ------
             \App::setLocale($card->user->language);

             $subject =  __('emailtxt.clips_restore_subject',['name' => $brandName->value]);
             $content =  __('emailtxt.clips_restore_txt',['name' => $card->user->name, 'amount' => $clip->amount, 'title' => $card->name, 'expiry' => \Carbon\Carbon::parse($card->expiry_date)->format($dateFormat->value), 'clips' => ($card->clips ?: 0), 'for' => ($card->type == 2 ? __('card.event') : __('card.treatment') ) ]);

            //------- Send sms notification if active from settings -----
            if($clipUsed->value == 'true' && $smsSetting->value == 'true'){

                $sms =  __('smstxt.clips_restore_txt',['name' => $card->user->name, 'amount' => $clip->amount, 'title' => $card->name, 'expiry' => \Carbon\Carbon::parse($card->expiry_date)->format($dateFormat->value), 'clips' => ($card->clips ?: 0), 'for' => ($card->type == 2 ? __('card.event') : __('card.treatment') ) ]);

                //--- send sms notification ---
                \Log::channel("custom")->info("Sending sms to customer about restore clips.",["business_id" => Auth::user()->business_id, "user_id" => $card->user->id, "sms" => $sms]);

                $this->smsSendWithCheck($sms,$card->user->id);

            }

            if($card->user->email != ''){
                \Log::channel('custom')->info("Sending email to customer about use of clips.",['business_id' => Auth::user()->business_id, 'user_id' => $card->user->id, 'subject' => $subject, 'email_text' => $content]);

                $this->dispatch(new SendEmailJob($card->user->email,$subject,$content,$brandName->value,$card->user->business_id));
            }
        }
        else{
            \Log::channel('custom')->error("There is an error to restore clips back in card | not found any clips in used_clips table.",['business_id' => Auth::user()->business_id, 'clip_used_id' => $request->id]);

            $data['status'] = 'error';
        }

        return json_encode($data);
    }

}//---------- End Class ----------
