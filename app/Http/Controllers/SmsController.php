<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SmsHistory;

class SmsController extends Controller
{
  
    /**
     * The webhook for Nexmo to receive delivery statuses.
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return  \Illuminate\Http\Response
     */
    public function statusCheck(Request $request)
    {
        if (!isset($request->messageId) AND !isset($request->status)) {
            \Log::channel('custom')->error('Not a valid delivery receipt');
            return;
        }
        else{
            \Log::channel('custom')->info('Sms delivery report. Status:'.$request->status);

            $entries = SmsHistory::where('message_id', $request->messageId)->first();
            if($entries != NULL){
                $time = 'message-timestamp';
                $entries->status = $request->status;
                $entries->received_at = $request->$time;
                $entries->save();
            }
        }

        return response('OK', 200);

        //    {
        //     "msisdn": "447700900000",
        //     "to": "AcmeInc",
        //     "network-code": "12345",
        //     "messageId": "0A0000001234567B",
        //     "price": "0.03330000",
        //     "status": "delivered",
        //     "scts": "2001011400",
        //     "err-code": "0",
        //     "api-key": "abcd1234",
        //     "client-ref": "my-personal-reference",
        //     "message-timestamp": "2020-01-01 12:00:00 +0000",
        //     "timestamp": "1582650446",
        //     "nonce": "ec11dd3e-1e7f-4db5-9467-82b02cd223b9",
        //     "sig": "1A20E4E2069B609FDA6CECA9DE18D5CAFE99720DDB628BD6BE8B19942A336E1C"
        //   }
    }
}
