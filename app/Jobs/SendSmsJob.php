<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\User;
use App\Models\Country;
use App\Models\SmsHistory;
use App\Http\Controllers\Controller;
use Throwable;


class SendSmsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;


    private $sms;
    private $user;
    private $countryCode;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($sms,$user)
    {
        $this->sms = $sms;
        $this->user = User::find($user);
        $this->countryCode = Country::find($this->user->country_id);
        if($this->countryCode == 'NULL' || $this->countryCode == NULL)
            $this->countryCode = 45;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {

        $row = ltrim($this->user->number, '0');
        $number = $this->countryCode->code.$row;

        $message = \Nexmo::message()->send([
            'to' => $number,
            'from' => 'PSBooking',
            'text' => $this->sms,
            'status-report-req' => 1,
            'callback' => url('/webhooks/smsStatus'),
        ]);

        $data = [
            'message_id' => $message['message-id'], 
            'message_price'=> $message['message-price'], 
            'network' => $message['network'], 
            'remaining_balance' => $message['remaining-balance'],
            'to' => $message['to'], 
            'status' => $message['status'], 
            'content' => $this->sms, 
            'business_id' => $this->user->business_id,
            'user_id' => $this->user->id,
        ];
        
        //----- insert in db -----
        SmsHistory::create($data);

        //------ Add log in DB for location -----//
        $type = "log_for_sms";
        $controller = new Controller();
        $controller->addLocationLog($type,$data,$this->user->business_id);
        
        //---- make log for it -----
        \Log::channel('custom')->info("Sms Sent!",$data);

    }

    /**
     * The job failed to process.
     *
     * @param  Exception  $exception
     * @return void
     */
    public function failed(Throwable $exception)
    {
        \Log::channel('custom')->error("Fail to send sms. Exception ". $exception);
    }
}
