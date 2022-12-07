<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\Business;
use App\Models\Survey;
use App\Models\Question;
use App\Models\Option;

class SurveyController extends Controller
{
    
    public function list(){
        abort_unless(\Gate::allows('Survey List View'),403);
        $surveys = Business::find(Auth::user()->business_id)->Surveys->where('survey_id',NULL);
        $dateFormat = Business::find(Auth::user()->business_id)->Settings->where('key','date_format')->first(); 
        $timeFormat = Business::find(Auth::user()->business_id)->Settings->where('key','time_format')->first(); 
        return view('survey.list',compact('surveys','dateFormat','timeFormat'));
    }

    public function viewSurvey($subdomain,$language){
        $business = Business::where('business_name',session('business_name'))->first();
        foreach(\Config::get('languages') as $key => $value) {
            if($key == $language) {
                $found = 1;
                break;
            }
            else
                $found = 0;
        }
        if($found)
            $questions = Business::find($business->id)->Questions->where('is_active',1)->where('language',$language);
        else
            $questions = Business::find($business->id)->Questions->where('is_active',1)->where('language','en');

        \App::setLocale($language);
        return view('survey.view',compact('questions','language','business'));
    }

    public function questionsList(Request $request){
        abort_unless(\Gate::any(['Survey Question View','Survey Question Create']),403);
        $questions = Business::find(Auth::user()->business_id)->Questions;
        return view('survey.qlist',compact('questions'));
    }
    
    public function addQuestionsOptions(){
        abort_unless(\Gate::allows('Survey Question Create'),403);
        return view('survey.addQ');
    }

    public function edit($subdomain,$id){
        abort_unless(\Gate::allows('Survey Question Edit'),403);
        $question = Question::where(\DB::raw('md5(id)'),$id)->first();
        return view('survey.editQ',compact('question'));
    }

    public function saveQuestionsOptions(Request $request){
        abort_unless(\Gate::allows('Survey Question Create'),403);
        Validator::make($request->all(), [
            'title' => ['required'],
        ])->validate();
        $question = new Question(); 
        $question->title = $request->title;       
        $question->language = $request->language;       
        $question->business_id = Auth::user()->business_id;  
        if($question->save()){
            $options = '';
            if(isset($request->options) && !empty($request->options) ){
                foreach($request->options as $option){
                    if($option != '' && $option != NULL){
                        $op = new Option(); 
                        $op->value = $option;       
                        $op->business_id = Auth::user()->business_id;
                        $op->question_id = $question->id;
                        $op->language = $request->language;       
                        $op->save();
                    }
                }
            }
            $request->session()->flash('success',__('survey.qhbas'));
            \Log::channel('custom')->info("Survey Question has been added successfully!",['business_id' => Auth::user()->business_id]);

            //------ Add log in DB for location -----//
            $type = "log_for_survey";
            $txt = "Survey Question has been added successfully! <br> Question : <b>".$request->title."</b><br>Created By:<b>".Auth::user()->email."</b>";
            $this->addLocationLog($type,$txt);
        }
        else{
            $request->session()->flash('error',__('survey.tiaetad'));
            \Log::channel('custom')->error("Error to add Survey Question.",['business_id' => Auth::user()->business_id]);
        }
        return \Redirect::back();
    }

    public function updateQuestionsOptions(Request $request){
        abort_unless(\Gate::allows('Survey Question Edit'),403);
        Validator::make($request->all(), [
            'id' => ['required'],
            'title' => ['required'],
        ])->validate();

        $question = Question::find($request->id); 
        $question->title = $request->title;       
        $question->language = $request->language;       
        if($request->status)
            $question->is_active = 1;
        else
            $question->is_active = 0;

        if($question->save()){
            $options = '';
            if(isset($request->options)){
                $questions = Option::where('question_id',$request->id)->update(['is_active'=>0]); 
                foreach($request->options as $option){
                    $op = new Option(); 
                    $op->value = $option;       
                    $op->business_id = Auth::user()->business_id;
                    $op->question_id = $question->id;
                    $op->language = $request->language;       
                    $op->save();
                }
            }
            $request->session()->flash('success',__('survey.qhhbus'));
            \Log::channel('custom')->info("Survey Question has been updated successfully!",['business_id' => Auth::user()->business_id]);

            //------ Add log in DB for location -----//
            $type = "log_for_survey";
            $txt = "Survey Question has been updated successfully! <br> Question : <b>".$request->title."</b><br>Updated By:<b>".Auth::user()->email."</b>";
            $this->addLocationLog($type,$txt);
        }
        else{
            $request->session()->flash('error',__('survey.tiaetuq'));
            \Log::channel('custom')->error("Error to update Survey Question.",['business_id' => Auth::user()->business_id]);
        }
        return \Redirect::back();
    }

    public function saveSurvey(Request $request){
        Validator::make($request->all(), [
            'ids' => ['required'],
        ])->validate();

        $business = Business::where('business_name',session('business_name'))->first();
        $ids = explode(',',$request->ids);
        $survey_id = '';
        foreach($ids as $key => $value){
            if($key > 0){
                $qfield = 'question-'.$value;
                $ofield = 'option-'.$value;
                $cfield = 'comment-'.$value;
                $survey = new Survey();
                $survey->business_id = $business->id;
                $survey->question_id = $request->$qfield;
                if(isset($request->$ofield))
                    $survey->option_id = $request->$ofield;
                else
                    $survey->comment = $request->$cfield;
                $survey->survey_id = $survey_id;
                $survey->save();
            }
            else{
                $qfield = 'question-'.$value;
                $ofield = 'option-'.$value;
                $cfield = 'comment-'.$value;
                $survey = new Survey();
                $survey->business_id = $business->id;
                $survey->question_id = $request->$qfield;
                if(isset($request->$ofield))
                    $survey->option_id = $request->$ofield;
                else
                    $survey->comment = $request->$cfield;
                $survey->name = $request->name;
                $survey->email = $request->email;
                $survey->save();

                $survey_id = $survey->id;
            }
        }

        \Log::channel('custom')->info("Customer fill Survey.",['business_id' => $business->id, 'survey_id' => $survey_id]);

        //------ Add log in DB for location -----//
        $type = "log_for_survey";
        $txt = "Customer fill Survey.";
        $this->addLocationLog($type,$txt);

        $request->session()->flash('success','Suvey Saved');
        return \Redirect::back();

    }
    
    public function show(Request $request){
        Validator::make($request->all(), [
            'id' => ['required'],
        ])->validate();
        $html = '<table class="table table-bordered table-striped"><thead><tr><th>'.__('survey.question').'</th><th>'.__('survey.answer').'</th><tr></thead><tbody>';
        $survey = Survey::where('id',$request->id)->orWhere('survey_id',$request->id)->get();
        foreach($survey as $s){
            $html .= '<tr>';
                $html .= '<td>'.$s->question->title.'</td>';
                if($s->option_id == NULL)
                    $html .= '<td>'.$s->comment.'</td>';
                else    
                    $html .= '<td>'.$s->option->value.'</td>';
            $html .= '</tr>';
        }
        $html .= "</tbody></table>";

        echo $html;
    }


}
