<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Models\Business;
use App\Models\User;
use App\Models\Role;

class RoleController extends Controller
{
    public function list(){
        abort_unless(\Gate::allows('Role View'),403);
        $roles = Business::find(Auth::user()->business_id)->Roles;
        return view('roles.list',compact('roles'));
    }

    public function create(){
        abort_unless(\Gate::allows('Role Create'),403);
        $permissions = Business::find(Auth::user()->business_id)->Permissions;
        return view('roles.create',compact('permissions'));
    }

    public function save(Request $request){
        abort_unless(\Gate::allows('Role Create'),403);

        Validator::make($request->all(), [
            'title' => ['required', 'string', 'max:255'],
            'permissions' => ['required'],
        ])->validate();

        $already = \DB::table('roles')->Where('title','=',$request->title)->Where('business_id','=',Auth::user()->business_id)->get()->count();

        if( $already ){
            $request->session()->flash('error',__('roles.role_already_exist_with_same_name'));
        }
        else{ 
            $role = new Role();
            $role->business_id   = Auth::user()->business_id;
            $role->title         = $request->title;
            $role->save();

            if( $role->id ){
                foreach( $request->permissions as $permission ){
                    \DB::table('permission_role')->insert([
                        'permission_id'  => $permission,
                        'role_id'       =>  $role->id,
                    ]);
                }
            }
            \Log::channel('custom')->info("Role has been Crated.",['business_id' => Auth::user()->business_id]);
            $request->session()->flash('success','Role has been created successfully!!');

            //------ Add log in DB for location -----//
            $type = "log_for_roles";
            $txt = "Role has been Crated. <br> Role: <b>".$request->title."</b><br>Created By:<b>".Auth::user()->email."</b>";
            $this->addLocationLog($type,$txt);
        }
        return \Redirect::back();
    }

    public function edit($subdomain,$id){
        abort_unless(\Gate::allows('Role Edit'),403);

        $permissions = Business::find(Auth::user()->business_id)->Permissions;
        $role = Role::where(\DB::raw('md5(id)') , $id)->first();
        return view('roles.edit',compact('role','permissions'));
    }

    public function update(Request $request){
        abort_unless(\Gate::allows('Role Edit'),403);

        Validator::make($request->all(), [
            'title' => ['required', 'string', 'max:255'],
            'permissions' => ['required'],
        ])->validate();

        $already = \DB::table('roles')->Where('title','=',$request->title)->where(\DB::raw('md5(id)'),'!=',$request->id)->Where('business_id','=',Auth::user()->business_id)->get()->count();

        if( $already ){
            $request->session()->flash('error',__('roles.role_already_exist_with_same_name'));
        }
        else{ 
            $role = Role::where(\DB::raw('md5(id)'),$request->id)->first();
            $role->title = $request->title;

            if( $role->save() ){
                \DB::table('permission_role')->where('role_id',$role->id)->delete();   
                foreach( $request->permissions as $permission ){
                    \DB::table('permission_role')->insert([
                        'permission_id'  => $permission,
                        'role_id'       =>  $role->id,
                    ]);
                }
            }
            \Log::channel('custom')->info("Role has been Updated.",['business_id' => Auth::user()->business_id]);
            $request->session()->flash('success',__('roles.role_has_been_updated_successfully'));

            //------ Add log in DB for location -----//
            $type = "log_for_roles";
            $txt = "Role has been Updated. <br> Role: <b>".$request->title."</b><br>Updated By:<b>".Auth::user()->email."</b>";
            $this->addLocationLog($type,$txt);
        }
        return \Redirect::back();
    }

    public function delete(Request $request){
        abort_unless(\Gate::allows('Role Edit'),403);

        Validator::make($request->all(), [
            'id' => ['required'],
        ])->validate();

        $role = Role::where(\DB::raw('md5(id)'),$request->id)->first();
        $inUse = \DB::table('role_user')->where(\DB::raw('md5(role_id)'),$request->id)->count();
        if($inUse){
            \Log::channel('custom')->warning("Role can not be delete.",['business_id' => Auth::user()->business_id,'user_id' => Auth::user()->id]);
            $data['status'] = 'exist'; 
        }else{
            
            //------ Add log in DB for location -----//
            $type = "log_for_roles";
            $txt = "Role has been deleted. <br> Role: <b>".$role->title."</b><br>Deleted By:<b>".Auth::user()->email."</b>";
            $this->addLocationLog($type,$txt);

            $role->delete();
            \Log::channel('custom')->info("Role has been deleted successfully!",['business_id' => Auth::user()->business_id,'user_id' => Auth::user()->id]);
            $data['status'] = 'success';
        }
        return json_encode($data);
    }
}
