<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PDF;
use App\Models\Business;
use App\Models\Treatment;
use App\Models\Date;
use App\Models\User;
use App\Models\TreatmentSlot;
use App\Models\Receipt;
use App\Jobs\SendEmailJob;


class PdfController extends Controller
{
    public function create($subdomain,$id)
    {
    	$data = ['treatment' => 'Laravel 7 Generate PDF From View Example Tutorial'];
        return view('pdf.receipt',compact('data'));

        $pdf = PDF::loadView('pdf.receipt', $data);
  
        return $pdf->download('Nicesnippets.pdf');
    }

    public function sendReceipt($subdomain,$id){

        $treatmentSlot = TreatmentSlot::where(\DB::raw('md5(id)') , $id)->first();
        $dateFormat = Business::find(auth()->user()->business_id)->Settings->where('key','date_format')->first(); 
        $brandName = Business::find(auth()->user()->business_id)->Settings->where('key','email_sender_name')->first(); 

        if($treatmentSlot->receipt != NULL){
            \App::setLocale($treatmentSlot->user->language);

            $subject = __('emailtxt.pdf_receipt_email_subject',['name' => $brandName->value ]);    
            $message = __('emailtxt.pdf_receipt_email_txt',['name' => $treatmentSlot->user->name]);
            \dispatch(new SendEmailJob($treatmentSlot->user->email,$subject,$message,$brandName->value,$treatmentSlot->user->business_id,public_path('receipts'.$treatmentSlot->receipt->data))); 

            \Log::channel('custom')->info("Payment receipt has been sent again!",['business_id' => auth()->user()->business_id, 'user_id' => $treatmentSlot->user->id,'fileName' => $treatmentSlot->receipt->data]);
            return back()->with(['success' => __("pdf.rhbss")]);
        }
        else{
            $receiptID = \DB::table('receipts')->latest('id')->first();
            if($receiptID == NULL)
                $invoiceNum = 1;
            else
                $invoiceNum = $receiptID->id+1;    
            // $invoiceNum = random_int(100000, 999999);
            if($treatmentSlot != NULL && $treatmentSlot->status == 'Booked'){

                $name = "\-receipt-".date('Y-m-d')."-".\Str::random(8).".pdf";
                
                $pdf = PDF::loadView('pdf.receipt', compact('treatmentSlot','dateFormat','invoiceNum'))->save(public_path('receipts'.$name));

                \App::setLocale($treatmentSlot->user->language);

                $subject = __('emailtxt.pdf_receipt_email_subject',['name' => $brandName->value ]);    
                $message = __('emailtxt.pdf_receipt_email_txt',['name' => $treatmentSlot->user->name]);
                \dispatch(new SendEmailJob($treatmentSlot->user->email,$subject,$message,$brandName->value,auth()->user()->business_id,public_path('receipts'.$name)));

                $data = [
                    ['business_id'=> auth()->user()->business_id,'user_id' => $treatmentSlot->user->id,'data' => $name ,'treatment_slot_id' => $treatmentSlot->id],
                ];
                Receipt::insert($data);
                
                \Log::channel('custom')->info("Payment receipt has been sent!",['business_id' => auth()->user()->business_id, 'user_id' => $treatmentSlot->user->id,'fileName' => $name]);

                return back()->with(['success' => __("pdf.rhbss")]);
            }else{
                return back()->with(['error' => __("pdf.tiaetsr")]);
            }
        }

    }
}
