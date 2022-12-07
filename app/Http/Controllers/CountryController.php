<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Models\Country;


class CountryController extends Controller
{
    //#################################################//
    public function list(){
        $countries = Country::where('status',1)->get();
        return view('country.list',compact('countries'));
    }

    //#################################################//
    public function create(){
        return view('country.create');
    }

    //#################################################//
    public function save(Request $request){
        Validator::make($request->all(), [
            'code' => ['required','numeric','digits_between:1,10'],
            'name' => ['required', 'string', 'max:255'],
        ])->validate();

        $country = new Country();
        $country->name        = $request['name'];
        $country->code       = $request['code'];
        if($country->save()){    
            $request->session()->flash('success',__('country.chbas'));
            \Log::channel('custom')->info("New country added.",['business_id' => Auth::user()->business_id, 'country_id' => $country->id]);
        }
        else{
            \Log::channel('custom')->error("Error to add country.",['business_id' => Auth::user()->business_id]);
            $request->session()->flash('error',__('country.tiaetac'));
        }

        return \Redirect::back();

    }

    //#################################################//
    public function edit($subdomain,$id){
        $country = Country::where(\DB::raw('md5(id)') , $id)->first(); 
        return view('country.edit')->with(compact('country'));
    }
        
    //#################################################//
    public function editSave(Request $request){
        Validator::make($request->all(), [
            'code' => ['required','numeric','digits_between:1,10'],
            'name' => ['required', 'string', 'max:255'],
        ])->validate();

        $country = Country::where(\DB::raw('md5(id)') , $request->id)->first();
        $country->name        = $request['name'];
        $country->code       = $request['code'];
        if($country->save()){    
            $request->session()->flash('success',__('country.chbus'));
            \Log::channel('custom')->info("New country updated.",['business_id' => Auth::user()->business_id, 'country_id' => $country->id]);
        }
        else{
            \Log::channel('custom')->error("Error to updated country.",['business_id' => Auth::user()->business_id]);
            $request->session()->flash('error',__('country.tiaetuc'));
        }

        return \Redirect::back();
    }

    //#################################################//
    public function delete($subdomain,$id){
        if($id){
            if(Country::where(\DB::raw('md5(id)'),$id)->delete()){
                session()->flash('success',__('country.chbds'));
                \Log::channel('custom')->info("New country added.",['business_id' => Auth::user()->business_id]);
            }else{
                session()->flash('error','Error to delete country!');
                \Log::channel('custom')->info("Error to delete country",['business_id' => Auth::user()->business_id]);
            }
        }
        else{
            session()->flash('error',__('country.etdc'));
            \Log::channel('custom')->info("No id found to delete country!",['business_id' => Auth::user()->business_id]);
        }
        return \Redirect::back();
    }

}
