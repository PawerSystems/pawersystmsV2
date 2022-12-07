<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\PaymentMethod;
use App\Models\Business;
use App\Models\TreatmentSlot;



class PaymentMethodController extends Controller
{
    //---#############################################################################################//

    public function list(){
        abort_unless(\Gate::allows('Payment Method View'),403);
        $methods = Business::find(Auth::user()->business_id)->PaymentMethods;
        return view('payment.list',compact('methods'));
    }

    //---#############################################################################################//

    public function create(){
        abort_unless(\Gate::allows('Payment Method Create'),403);
        return view('payment.create');
    }

    //---#############################################################################################//

    public function add(Request $request){
        abort_unless(\Gate::allows('Payment Method Create'),403);
        Validator::make($request->all(), [
            'title' => ['required', 'string', 'max:255'],
        ])->validate();
        
        $check = PaymentMethod::where([['title',$request->title],['business_id',Auth::user()->business_id]])->count();
        if($check == 0){
            $method = new PaymentMethod(); 
            $method->title = $request->title;       
            $method->business_id = Auth::user()->business_id;  

            if($method->save()){
                \Log::channel('custom')->info("Payment Method has been added successfully!!",['business_id' => Auth::user()->business_id]);
                $request->session()->flash('success',__('payment.payment_method_has_been_added_successfully'));

                //------ Add log in DB for location -----//
                $type = "log_for_payment_method";
                $txt = "Payment Method  has been added successfully. <br>Payment Method: <b>".$request->title."</b><br>Added By:<b>".Auth::user()->email."</b>";
                $this->addLocationLog($type,$txt);
            }
            else{
                \Log::channel('custom')->error("Error to add Payment Method.",['business_id' => Auth::user()->business_id]);
                $request->session()->flash('error',__('payment.tiauetapm'));
            }
        }
        else{
            \Log::channel('custom')->warning("Payment Method with same name already exist.",['business_id' => Auth::user()->business_id]);
            $request->session()->flash('error',__('payment.payment_method_already_exist'));
        }
        return \Redirect::back();
    }

    //---#############################################################################################//

    public function updateMethodAjax(Request $request){
        abort_unless(\Gate::allows('Payment Method Edit'),403);
        Validator::make($request->all(), [
            'id'    => ['required'],
            'title' => ['required', 'string', 'max:255'],
        ])->validate();

        $check = PaymentMethod::where([['title',$request->title],['business_id',Auth::user()->business_id]])->count();
        if($check == 0){
            $method = PaymentMethod::find($request->id); 
            $method->title = $request->title;       

            if($method->save()){
                \Log::channel('custom')->info("Payment Method has been updated successfully!.",['business_id' => Auth::user()->business_id]);
                $data = 'success';

                //------ Add log in DB for location -----//
                $type = "log_for_payment_method";
                $txt = "Payment Method  has been updated successfully. <br>Payment Method: <b>".$request->title."</b><br>Updated By:<b>".Auth::user()->email."</b>";
                $this->addLocationLog($type,$txt);
            }
            else{
                \Log::channel('custom')->error("Error to update Payment Method.",['business_id' => Auth::user()->business_id]);
                $data = 'error';
            }
        }
        else{
            \Log::channel('custom')->warning("Payment Method can't be update because link with booking.",['business_id' => Auth::user()->business_id]);
            $data = 'exist';        
        }
        return \Response::json($data);
    }
    
    //---#############################################################################################//

    public function updateStatusAjax(Request $request){
        abort_unless(\Gate::allows('Payment Method Edit'),403);
        Validator::make($request->all(), [
            'id'    => ['required'],
        ])->validate();

        $method = PaymentMethod::find($request->id); 

        if($method->is_active)
            $method->is_active = 0;
        else
            $method->is_active = 1;    

        if($method->save()){
            \Log::channel('custom')->info("Payment Method status changed.",['business_id' => Auth::user()->business_id]);
            $data = 'success';

            //------ Add log in DB for location -----//
            $type = "log_for_payment_method";
            $txt = "Payment Method status has been updated successfully. <br>Payment Method: <b>".$method->title."</b><br>Updated By:<b>".Auth::user()->email."</b>";
            $this->addLocationLog($type,$txt);
        }
        else{
            \Log::channel('custom')->error("Error to change Payment Method status.",['business_id' => Auth::user()->business_id]);
            $data = 'error';
        }
        return \Response::json($data);
    }
    //---#############################################################################################//

    public function deleteAjax(Request $request){
        abort_unless(\Gate::allows('Payment Method Delete'),403);
        Validator::make($request->all(), [
            'id'    => ['required'],
        ])->validate();

        $check = TreatmentSlot::where('payment_method_id',$request->id)->count();
        $method = PaymentMethod::find($request->id);
        $methodName = $method->title;

        if($check == 0){
            if($method->delete()){
                \Log::channel('custom')->info("Payment Method deleted.",['business_id' => Auth::user()->business_id]);
                $data = 'success';

                //------ Add log in DB for location -----//
                $type = "log_for_payment_method";
                $txt = "Payment Method deleted. <br>Payment Method: <b>".$methodName."</b><br>Updated By:<b>".Auth::user()->email."</b>";
                $this->addLocationLog($type,$txt);
            }
            else{
                \Log::channel('custom')->info("Error to delete Payment Method.",['business_id' => Auth::user()->business_id]);
                $data = 'error';    
            }
        }
        else{
            \Log::channel('custom')->info("Payment Method can't be delete because link with booking.",['business_id' => Auth::user()->business_id]);
            $data = 'exist';    
        }
        return \Response::json($data);
    }

    

}
