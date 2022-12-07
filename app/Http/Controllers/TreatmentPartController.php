<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\TreatmentPart;
use App\Models\TreatmentPartTranslation;
use App\Models\Business;
use App\Models\TreatmentSlot;

class TreatmentPartController extends Controller
{
     //---#############################################################################################//

     public function list(){
        abort_unless(\Gate::allows('Treatment Area View'),403);
        $parts = Business::find(Auth::user()->business_id)->TreatmentParts;
        return view('parts.list',compact('parts'));
    }

    //---#############################################################################################//

    public function create(){
        abort_unless(\Gate::allows('Treatment Area Create'),403);
        return view('parts.create');
    }

    //---#############################################################################################//

    public function add(Request $request){
        abort_unless(\Gate::allows('Treatment Area Create'),403);
        Validator::make($request->all(), [
            'title' => ['required', 'string', 'max:255'],
        ])->validate();
        
        $check = TreatmentPart::where([['title',$request->title],['business_id',Auth::user()->business_id]])->count();
        if($check == 0){
            $parts = new TreatmentPart(); 
            $parts->title = $request->title;       
            $parts->Torder = $request->order;       
            $parts->business_id = Auth::user()->business_id;  

            if($parts->save()){

                //---- add translations -----
                foreach ( \Config::get('languages') as $key => $val ){
                    if ($key == 'en')
                        continue;
                    
                    if($request->$key){
                        $partTra = new TreatmentPartTranslation(); 
                        $partTra->business_id = Auth::user()->business_id;  
                        $partTra->treatment_part_id = $parts->id;  
                        $partTra->key = $key;  
                        $partTra->value = $request->$key;  
                        $partTra->save();
                    }
                }

                $request->session()->flash('success','Treatment area has been added successfully!!');
                \Log::channel('custom')->info("Treatment area has been added successfully!!",['business_id' => Auth::user()->business_id]);

                //------ Add log in DB for location -----//
                $type = "log_for_treatment_part";
                $txt = "Treatment area has been added successfully. <br>Part Title : <b>".$request->title."</b><br>Added By:<b>".Auth::user()->email."</b>";
                $this->addLocationLog($type,$txt);
            }
            else{
                $request->session()->flash('error','There is an error to add Treatment area!');
                \Log::channel('custom')->error("There is an error to add Treatment area!",['business_id' => Auth::user()->business_id]);
            }
        }
        else{
            $request->session()->flash('error','Treatment area with same name already exist!');
            \Log::channel('custom')->warning("Treatment area with same name already exist!",['business_id' => Auth::user()->business_id]);
        }
        return \Redirect::back();
    }

    //---#############################################################################################//

    public function updatePartAjax(Request $request){
        abort_unless(\Gate::allows('Treatment Area Edit'),403);
        Validator::make($request->all(), [
            'id'    => ['required'],
            'title' => ['required', 'string', 'max:255'],
        ])->validate();

        $check = 0;
        $part = TreatmentPart::find($request->id); 
        if($part->title != $request->title ){
            $check = TreatmentPart::where([['title',$request->title],['business_id',Auth::user()->business_id]])->count();
        }    
        if($check == 0){
            
            $part->title = $request->title;       
            $part->Torder = $request->order;       

            if($part->save()){

                //---- add translations -----
                foreach ( \Config::get('languages') as $key => $val ){
                    if ($key == 'en')
                        continue;
                    
                    if($request->$key){
                        TreatmentPartTranslation::updateOrCreate(
                            ['business_id' => Auth::user()->business_id, 'treatment_part_id' => $request->id,'key' => $key],
                            ['value' => $request->$key]
                        );
                    }
                }

                \Log::channel('custom')->info("Treatment area has been updated successfully!!",['business_id' => Auth::user()->business_id]);
                $data = 'success';

                //------ Add log in DB for location -----//
                $type = "log_for_treatment_part";
                $txt = "Treatment area has been updated successfully. <br>Part Title : <b>".$request->title."</b><br>Updated By:<b>".Auth::user()->email."</b>";
                $this->addLocationLog($type,$txt);
            }
            else{
                \Log::channel('custom')->error("Error to update Treatment area!!",['business_id' => Auth::user()->business_id]);
                $data = 'error';
            }
        }
        else{
            \Log::channel('custom')->warning("Treatment area can't update, already exist with same name!",['business_id' => Auth::user()->business_id]);
            $data = 'exist';        
        }
        return \Response::json($data);
    }
    
    //---#############################################################################################//

    public function updatePartStatusAjax(Request $request){
        abort_unless(\Gate::allows('Treatment Area Edit'),403);
        Validator::make($request->all(), [
            'id'    => ['required'],
        ])->validate();

        $part = TreatmentPart::find($request->id); 

        if($part->is_active)
            $part->is_active = 0;
        else
            $part->is_active = 1;    

        if($part->save()){
            $data = 'success';
            \Log::channel('custom')->info("Treatment status has been updated successfully!!",['business_id' => Auth::user()->business_id]);

            //------ Add log in DB for location -----//
            $type = "log_for_treatment_part";
            $txt = "Treatment area status has been updated successfully. <br>Part Title : <b>".$part->title."</b><br>Updated By:<b>".Auth::user()->email."</b>";
            $this->addLocationLog($type,$txt);
        }
        else{
            $data = 'error';
            \Log::channel('custom')->error("Error to update Treatment status!",['business_id' => Auth::user()->business_id]);
        }
        return \Response::json($data);
    }

      //---#############################################################################################//

      public function deleteAjax(Request $request){
        abort_unless(\Gate::allows('Treatment Area Delete'),403);
        Validator::make($request->all(), [
            'id'    => ['required'],
        ])->validate();

        $check = TreatmentSlot::where('treatment_part_id',$request->id)->count();
        $part = TreatmentPart::find($request->id);
        $partName = $part->title;
        if($check == 0){

            //----- delete part translations -------//
            TreatmentPartTranslation::where('treatment_part_id',$request->id)->delete();

            if($part->delete()){
                $data = 'success';
                \Log::channel('custom')->info("Treatment part has been deleted.",['business_id' => Auth::user()->business_id]);

                //------ Add log in DB for location -----//
                $type = "log_for_treatment_part";
                $txt = "Treatment area has been deleted successfully. <br>Part Title : <b>".$partName."</b><br>Updated By:<b>".Auth::user()->email."</b>";
                $this->addLocationLog($type,$txt);
            }
            else{
                $data = 'error';  
                \Log::channel('custom')->error("Error to delete Treatment part.",['business_id' => Auth::user()->business_id]);  
            }
        }
        else{
            \Log::channel('custom')->warning("Treatment part is in use so you can't delete it.",['business_id' => Auth::user()->business_id]);
            $data = 'exist';    
        }
        return \Response::json($data);
    }
}
