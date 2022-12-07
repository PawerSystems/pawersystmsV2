<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Department;
use App\Models\Business;
use App\Models\TreatmentSlot;

class DepartmentController extends Controller
{
      //---#############################################################################################//

      public function list(){
        abort_unless(\Gate::allows('Department View'),403);
        $departments = Business::find(Auth::user()->business_id)->Departments;
        return view('department.list',compact('departments'));
    }

    //---#############################################################################################//

    public function create(){
        abort_unless(\Gate::allows('Department Create'),403);
        return view('department.create');
    }

    //---#############################################################################################//

    public function add(Request $request){
        abort_unless(\Gate::allows('Department Create'),403);
        Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
        ])->validate();
        
        $check = Department::where([['name',$request->name],['business_id',Auth::user()->business_id]])->count();

        //----- Check this name should not exist in this business -----
        if($check == 0){
            $Department = new Department(); 
            $Department->name = $request->name;       
            $Department->business_id = Auth::user()->business_id;  

            if($Department->save()){
                \Log::channel('custom')->info("Department has been added successfully!",['business_id' => Auth::user()->business_id, 'dep_name' => $request->name]);

                //------ Add log in DB for location -----//
                $type = "log_for_department";
                $txt = "Department has been added successfully! <br> Department Name: <b>".$request->name."</b>";
                $this->addLocationLog($type,$txt);

                $request->session()->flash('success',__('department.dhbas'));
            }
            else{
                $request->session()->flash('error','There is an error to add Department!');
                \Log::channel('custom')->error("There is an error to add Department.",['business_id' => Auth::user()->business_id]);
            }
        }
        else{
            \Log::channel('custom')->info("Department with same name already exist in this business.",['business_id' => Auth::user()->business_id, 'dep_name' => $request->name]);

            $request->session()->flash('error',__('department.department_already_exist'));
        }
        return \Redirect::back();
    }

    //---#############################################################################################//

    public function updateDepartmentAjax(Request $request){
        abort_unless(\Gate::allows('Department Edit'),403);
        Validator::make($request->all(), [
            'id'    => ['required'],
            'name' => ['required', 'string', 'max:255'],
            
        ])->validate();

        $check = Department::where([['name',$request->name],['business_id',Auth::user()->business_id]])->count();
        if($check == 0){
            $department = Department::find($request->id); 
            $department->name = $request->name;       

            if($department->save()){
                $data = 'success';
                \Log::channel('custom')->info("Department name has been updated.",['business_id' => Auth::user()->business_id, 'dep_id' => $department->id]);

                //------ Add log in DB for location -----//
                $type = "log_for_department";
                $txt = "Department name has been updated. <br> Department Name: <b>".$department->name."</b>";
                $this->addLocationLog($type,$txt);
            }
            else{
                \Log::channel('custom')->error("Error to update Department name.",['business_id' => Auth::user()->business_id, 'dep_id' => $department->id]);
                $data = 'error';
            }
        }
        else{
            \Log::channel('custom')->info("Department name already exist in business.",['business_id' => Auth::user()->business_id, 'dep_name' => $request->name]);
            $data = 'exist';        
        }
        return \Response::json($data);
    }
    
    //---#############################################################################################//

    public function updateStatusAjax(Request $request){
        abort_unless(\Gate::allows('Department Edit'),403);
        Validator::make($request->all(), [
            'id'    => ['required'],
        ])->validate();

        $Department = Department::find($request->id); 

        if($Department->is_active)
            $Department->is_active = 0;
        else
            $Department->is_active = 1;    

        if($Department->save()){
            \Log::channel('custom')->info("Department status changed.",['business_id' => Auth::user()->business_id, 'dep_id' => $Department->id]);
            $data = 'success';

            //------ Add log in DB for location -----//
            $type = "log_for_department";
            $txt = "Department status changed. <br> Department Name: <b>".$Department->name."</b>";
            $this->addLocationLog($type,$txt);
        }
        else{
            \Log::channel('custom')->error("Error to change Department status.",['business_id' => Auth::user()->business_id, 'dep_id' => $Department->id]);

            $data = 'error';
        }
        return \Response::json($data);
    }

    //---#############################################################################################//

    public function deleteAjax(Request $request){
        abort_unless(\Gate::allows('Department Delete'),403);
        Validator::make($request->all(), [
            'id'    => ['required'],
        ])->validate();

        $check = TreatmentSlot::where('department_id',$request->id)->count();

        if($check == 0){
            \Log::channel('custom')->info("Deleting Department...",['business_id' => Auth::user()->business_id, 'dep_id' => $request->id]);
            
            $Department = Department::find($request->id);
            //------ Add log in DB for location -----//
            $type = "log_for_department";
            $txt = "Department deleted successfully. <br> Department Name: <b>".$Department->name."</b>";
            $this->addLocationLog($type,$txt);

            if($Department->delete()){
                $data = 'success';
                \Log::channel('custom')->info("Department deleted successfully.",['business_id' => Auth::user()->business_id]);
            }
            else{
                \Log::channel('custom')->error("Error to delete department",['business_id' => Auth::user()->business_id, 'dep_id' => $request->id]);

                $data = 'error';    
            }
        }
        else{
            \Log::channel('custom')->warning("Department is in used so can't delete it.",['business_id' => Auth::user()->business_id, 'dep_id' => $request->id]);

            $data = 'exist';    
        }
        return \Response::json($data);
    }
}
