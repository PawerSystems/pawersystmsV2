<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Setting;
use App\Models\Business;
use App\Models\StoreCurlData;
use Goutte\Client;

class FetchOfferData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fetch:offer';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command will fetch offer data on every midnight';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        #----- Fetch all offer urls
        $urls = Setting::where('key','offer_url')->get();
        foreach($urls as $url){
            if(!empty($url->value)){
                #--------------
                $client = new Client();
                $page = $client->request('GET', $url->value);
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
        
                $check = StoreCurlData::where('business_id',$url->business_id)->first();
                if($check == null){   
                    StoreCurlData::create([
                        'title' => ($title ?: 'N/A'),
                        'price' => ($price ?: 'N/A'),
                        'description' => ($desc ?: 'N/A'),
                        'image' => ($image ?: 'N/A'),
                        'business_id' => $url->business_id,
                    ]);
                }else{
                    $check->title = ($title ?: 'N/A');
                    $check->price = ($price ?: 'N/A');
                    $check->description = ($desc ?: 'N/A');
                    $check->image = ($image ?: 'N/A');
                    $check->save();
                }
            }
        }
    }
}
