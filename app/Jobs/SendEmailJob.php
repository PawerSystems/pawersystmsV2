<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use App\Mail\SendMail;
use App\Models\LocationLog;
use App\Models\Business;
use App\Http\Controllers\Controller;
use Throwable;



class SendEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */

    private $email;
    private $bid;
    private $subject;
    private $message;
    private $brandName;
    private $attachment;

    public function __construct($email,$subject,$message,$brandName = '',$businessID = '',$attachment = '')
    {
        $businessData = Business::find($businessID);
        if($businessData->count() > 0){
            $brandName = $businessData->brand_name;
        }
        
        //------ removing direct login into account url -------//
        $message = str_replace('/other-account','',$message);
        
        $url = __('emailtxt.mail_from').': '.'https://'.$businessData->business_name.'.'.config('app.domain');

        $this->email = $email;
        $this->bid = $businessID;
        $this->subject = $subject;
        $this->message = $url.'<br><br>'.$message.'<br><br>'.__('keywords.yours_sincerely').',<br><br>';
        if($brandName)
            $this->brandName = $brandName;
        else
            $this->brandName = config('app.name'); 
            
        $this->attachment = $attachment;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {

        Mail::to($this->email)->send(new SendMail($this->subject,$this->message,$this->brandName,$this->attachment));
        if( count(Mail::failures()) > 0 ) {
            \Log::channel('custom')->error("Error to send email. Exception ".Mail::failures());            
        } 
        else{
            //------ Add log in DB for location -----//
            $type = "log_for_email";
            $controller = new Controller();
            $controller->addLocationLog($type,$this->message,$this->bid);
            
            \Log::channel('custom')->info("Email Sent!",['email' => $this->email,'subject' => $this->subject,'message' => $this->message,'brandName' => $this->brandName,'business_id' => $this->bid]);
        }
    }

    /**
     * The job failed to process.
     *
     * @param  Exception  $exception
     * @return void
     */
    public function failed(Throwable $exception)
    {
        \Log::channel('custom')->error("Fail to send email. Exception ". $exception);
    }

}
