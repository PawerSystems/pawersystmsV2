<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Symfony\Component\HttpFoundation\File\File;
use App\Models\Business;

class SendMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */

    public $subject;
    public $message;
    public $businessName;
    public $file;

    public function __construct($subject,$message,$brandName,$file='')
    {
        $this->subject = $subject;
        $this->message = $message;
        $this->businessName = $brandName;
        $this->file = $file;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {

        session()->flash('businessName',$this->businessName);

        if($this->file != '')
            return $this->from( config('mail.from.address'),$this->businessName )->subject($this->subject)->attach($this->file)->markdown('email.welcome-email');
        else
            return $this->from( config('mail.from.address'),$this->businessName )->subject($this->subject)->markdown('email.welcome-email');

        //return $this->subject($this->subject)->markdown('email.welcome-email');
    }
}
