<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\Business;
use App\Models\Setting;
use App\Models\StoreCurlData;
use Goutte\Client;


class SettingController extends Controller
{
    //-------------------------------------//
    public function list(){
        abort_unless(\Gate::allows('Settings View'),403);
        $settings = Business::find(Auth::user()->business_id)->Settings;
        return view('setting.list',compact('settings'));
    }

    //---------------------------------//
    public function update(Request $request){
        abort_unless(\Gate::allows('Settings Edit'),403);

        Validator::make($request->all(), [
            'key'    => ['required', 'string', 'max:255'],
            'value'    => ['required'],
        ])->validate();

        $settings = Business::find(Auth::user()->business_id)->Settings->where('key',$request->key)->first();
        if($settings){
            $settings->value = $request->value;
            if($settings->save()){
                $data = 'success';
                \Log::channel('custom')->info("Setting updated.",['business_id' => Auth::user()->business_id]);

                //------ Add log in DB for location -----//
                $type = "log_for_settings";
                $txt = "Setting updated. <br> Setting : <b>".str_replace("_"," ",$request->key)."</b><br>Updated By:<b>".Auth::user()->email."</b>";
                $this->addLocationLog($type,$txt);
            }
            else{
                $data = 'error';
                \Log::channel('custom')->error("Error to update settings.",['business_id' => Auth::user()->business_id]);
            }
        }
        else{
            $setting = new Setting();
            $setting->key = $request->key;
            $setting->value = $request->value;
            $setting->business_id = Auth::user()->business_id;
           
            if($setting->save()){
                $data = 'success';
                \Log::channel('custom')->info("Setting updated.",['business_id' => Auth::user()->business_id]);

                //------ Add log in DB for location -----//
                $type = "log_for_settings";
                $txt = "Setting updated. <br> Setting : <b>".str_replace("_"," ",$request->key)."</b><br>Updated By:<b>".Auth::user()->email."</b>";
                $this->addLocationLog($type,$txt);
            }
            else{
                $data = 'error';
                \Log::channel('custom')->error("Error to update settings.",['business_id' => Auth::user()->business_id]);
            }
        }

        return \Response::json($data);
    }

    //---------------------------------//
    public function navSettings(){
        
        if( session('navFixed') == 'on')
            session(['navFixed' => 'off']);
        else
            session(['navFixed' => 'on']);

        return true;    
    }

    //---------------------------------//
    public function fetchOfferData(Request $request){
        abort_unless(\Gate::allows('Settings Edit'),403);

        Validator::make($request->all(), [
            'url'    => ['required'],
        ])->validate();

        $client = new Client();
        $page = $client->request('GET', $request->url);
        $title = $desc = $image = $price = '';
        
        #--------- Get all meta tags ---------
        $metas = $page->filter('meta')->each(function($node) {
            return [
                'name' => $node->attr('name'),
                'content' => $node->attr('content'),
                'property' => $node->attr('property'),
            ];
        });
        #-------- Fetch description tag value -----
        foreach($metas as $meta){
            if($meta['name'] == 'description'){
                $desc = $meta['content'];
            }
            if($meta['property'] == 'og:image'){
                $image = $meta['content'];
            }
        }

        $title = $page->filter('title')->text();
        
        $check = StoreCurlData::where('business_id',Auth::user()->business_id)->first();
        if($check == null){   
            $offer = StoreCurlData::create([
                'title' => ($title ?: 'N/A'),
                'price' => ($price ?: 'N/A'),
                'description' => ($desc ?: 'N/A'),
                'image' => ($image ?: 'N/A'),
                'business_id' => Auth::user()->business_id,
            ]);
            if($offer->id > 0){
                $data = 'success';
            }else{
                $data = 'error to save data'; 
            }
        }else{
            $check->title = ($title ?: 'N/A');
            $check->price = ($price ?: 'N/A');
            $check->description = ($desc ?: 'N/A');
            $check->image = ($image ?: 'N/A');

            if($check->save()){
                $data = 'success';
            }else{
                $data = 'error to save data'; 
            }
        }
        
        return \Response::json($data);

    }

    public function checkFreeTreatment(){
        abort_unless(\Gate::allows('Settings Edit'),403);
        $treatments = Business::find(auth()->user()->business_id)->treatments->where('price','<=',0)->where('is_active',1)->count();
        echo $treatments;
    }

}//----- class ends ----//
