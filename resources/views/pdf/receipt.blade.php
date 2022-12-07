<! DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1">  
	<title>{{ __('pdf.payment_receipt') }}</title>
    <style>

      .skyblue{ background-color: skyblue; }
      .lightblue{ background-color: rgb(193, 223, 235); }

    </style>
</head>
<body>
    <page>
        <table class="mainTable" cellpadding="0" cellspacing="0" style="border-bottom: none;">
            <tbody class="innerTable">

                <tr>
                    <td colspan="6" style="font-size:22px;text-align:left;padding:10px;color:#3079bb;vertical-align: middle;font-weight:bold;width:100px;"><br><br><span style="font-size:12px;color:black;font-weight:lighter;"></span></td>
                </tr>
                <tr>
                    <td style="width:25px;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                    <td style="padding: 7px 5px;width:100px;">
                        {{ $treatmentSlot->user->name }}
                    </td>
                    <td style="width:231px;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                    <td style="text-align: left;background:#3079bb;color: white;padding: 7px 5px;width:81px;">{{ __('pdf.invoice') }}</td>
                    <td style="width:81px;text-align: left;background:#c6d7e7;color: white;padding: 7px 5px;">{{ $invoiceNum }}</td>
                    <td style="width:25px;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                </tr>
                <tr>
                    <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                    <td>{{ $treatmentSlot->user->email }}</td>
                    <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                    <td style="text-align: left;background:#3079bb;color: white;padding: 7px 5px;width:81px;">{{ __('pdf.date') }}</td>
                    <td style="width:25px;text-align: left;background:#c6d7e7;color: white;padding: 7px 5px;">{{ \Carbon\Carbon::today()->format($dateFormat->value) }}</td>
                    <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>

                </tr>
                <tr>
                    <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                    <td>{{ $treatmentSlot->user->cprnr }}</td>
                    <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                    <td style="text-align: left;background:#3079bb;color: white;padding: 7px 5px;width:81px;">{{ __('pdf.cvr_no') }}</td>
                    <td style="width:25px;text-align: left;background:#c6d7e7;color: white;padding: 7px 5px;">29859035</td>
                    <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                </tr>
                <tr>
                    <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                    <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                    <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                    <td style="text-align: left;background:#3079bb;color: white;padding: 7px 5px;width:81px;">{{ __('pdf.customer_no') }}</td>
                    <td style="width:25px;text-align: left;background:#c6d7e7;color: white;padding: 7px 5px;">{{ $treatmentSlot->user->id }}</td>
                    <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                </tr>
                <tr>
                    <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                    <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                    <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                    <td style="text-align: left;background:#3079bb;color: white;padding: 7px 5px;width:81px;">{{ __('pdf.page_no') }}</td>
                    <td style="width:25px;text-align: left;background:#c6d7e7;color: white;padding: 7px 5px;">1</td>
                    <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                </tr>
                <tr>
                    <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                    <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                    <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                    <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                    <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                    <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                </tr>
            </tbody>
        </table>
        <table class="mainTable" cellpadding="0" cellspacing="0" style="border-top: none;">
            <tbody class="innerTable">
                <tr>
                    <td colspan="13">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                </tr>
                <tr>
                    <td style="width:25px;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                    <td style="background: #3079bb; color:white; font-size:12px; padding: 5px;width:75px;">{{ __('pdf.invoice_no') }}</td>
                    <td style="width:1px;"></td>
                    <td style="background: #3079bb; color:white; font-size:12px;padding: 5px; width:50px;">{{ __('pdf.quantity') }}</td>
                    <td style="width:1px;"></td>
                    <td style="background: #3079bb; color:white; font-size:12px;padding: 5px; width:250px">{{ __('pdf.description') }}</td>
                    <td style="width:1px;"></td>
                    <td style="background: #3079bb; color:white; font-size:12px;padding: 5px; width:75px;text-align:center;">{{ __('pdf.amount_ex_vat') }}</td>
                    <td style="width:1px;"></td>
                    <td style="background: #3079bb; color:white; font-size:12px; padding: 5px;width:75px;text-align:center;">{{ __('pdf.vat') }}</td>
                    <td style="width:1px;"></td>
                    <td style="background: #3079bb; color:white; font-size:12px;padding: 5px; width:75px;text-align:center;">{{ __('pdf.amount_in_vat') }}</td>
                    <td style="width:25px;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                </tr>
                <tr>
                    <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                    <td style="background: #c6d7e7;padding: 5px;">
                    {{ $invoiceNum }}<br>
                    <p>{{ \Carbon\Carbon::today()->format($dateFormat->value) }}</p>
                    </td>
                    <td style="width:1px;"></td>
                    <td  style="background: #c6d7e7;padding: 5px;">1</td>
                    <td style="width:1px;"></td>
                    <td style="padding: 5px;">
                        {{ $treatmentSlot->treatment->treatment_name }} ({{ $treatmentSlot->treatment->time_shown ?: $treatmentSlot->treatment->inter  }} min) {{ $treatmentSlot->treatment->price ?: '' }}<br>
                        {{ $treatmentSlot->date->user->name }}
                    </td>
                    <td style="width:1px;"></td>
                    <td  style="background: #c6d7e7;padding: 5px;">{{ $treatmentSlot->treatment->price ?: '0' }}</td>
                    <td style="width:1px;"></td>
                    <td style="background: #c6d7e7;padding: 5px;"></td>
                    <td style="width:1px;"></td>
                    <td style="background: #c6d7e7;padding: 5px;">{{ $treatmentSlot->treatment->price ?: '0' }}</td>
                    <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                </tr>
                <tr>
                    <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                    <td style="background: #c6d7e7;padding: 5px;"></td>
                    <td style="width:1px;"></td>
                    <td  style="background: #c6d7e7;padding: 5px;"></td>
                    <td style="width:1px;"></td>
                    <td style="padding: 5px;"></td>
                    <td style="width:1px;"></td>
                    <td  style="background: #c6d7e7;padding: 5px;"></td>
                    <td style="width:1px;"></td>
                    <td style="background: #c6d7e7;padding: 5px;"></td>
                    <td style="width:1px;"></td>
                    <td style="background: #c6d7e7;padding: 5px;"></td>
                    <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                </tr>
                <tr>
                    <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                    <td style="background: #c6d7e7;padding: 5px;"></td>
                    <td style="width:1px;"></td>
                    <td  style="background: #c6d7e7;padding: 5px;"></td>
                    <td style="width:1px;"></td>
                    <td style="padding: 5px;"></td>
                    <td style="width:1px;"></td>
                    <td  style="background: #c6d7e7;padding: 5px;"></td>
                    <td style="width:1px;"></td>
                    <td style="background: #c6d7e7;padding: 5px;"></td>
                    <td style="width:1px;"></td>
                    <td style="background: #c6d7e7;padding: 5px;"></td>
                    <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                </tr>
                <tr>
                    <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                    <td style="background: #c6d7e7;padding: 5px;"></td>
                    <td style="width:1px;"></td>
                    <td  style="background: #c6d7e7;padding: 5px;"></td>
                    <td style="width:1px;"></td>
                    <td style="padding: 5px;">Sum</td>
                    <td style="width:1px;"></td>
                    <td  style="background: #c6d7e7;padding: 5px;">{{ $treatmentSlot->treatment->price ?: '0' }}</td>
                    <td style="width:1px;"></td>
                    <td style="background: #c6d7e7;padding: 5px;">0,00</td>
                    <td style="width:1px;"></td>
                    <td style="background: #c6d7e7;padding: 5px;">{{ $treatmentSlot->treatment->price ?: '0' }}</td>
                    <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                </tr>
                <tr>
                    <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                    <td style="background: #c6d7e7;padding: 5px;"></td>
                    <td style="width:1px;"></td>
                    <td  style="background: #c6d7e7;padding: 5px;"></td>
                    <td style="width:1px;"></td>
                    <td style="padding: 5px;"></td>
                    <td style="width:1px;"></td>
                    <td  style="background: #c6d7e7;padding: 5px;"></td>
                    <td style="width:1px;"></td>
                    <td style="background: #c6d7e7;padding: 5px;"></td>
                    <td style="width:1px;"></td>
                    <td style="background: #c6d7e7;padding: 5px;"></td>
                    <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                </tr>
                <tr>
                    <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                    <td style="background: #c6d7e7;padding: 5px;"></td>
                    <td style="width:1px;"></td>
                    <td  style="background: #c6d7e7;padding: 5px;"></td>
                    <td style="width:1px;"></td>
                    <td style="padding: 5px;">{{ __('pdf.already_paid') }} ({{$treatmentSlot->paymentMethodTitle->title}})</td>
                    <td style="width:1px;"></td>
                    <td  style="background: #c6d7e7;padding: 5px;"></td>
                    <td style="width:1px;"></td>
                    <td style="background: #c6d7e7;padding: 5px;"></td>
                    <td style="width:1px;"></td>
                    <td style="background: #c6d7e7;padding: 5px;">-{{ $treatmentSlot->treatment->price ?: '0' }}</td>
                    <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                </tr>
                <tr>
                    <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                    <td style="background: #c6d7e7;padding: 5px;"></td>
                    <td style="width:1px;"></td>
                    <td  style="background: #c6d7e7;padding: 5px;"></td>
                    <td style="width:1px;"></td>
                    <td style="padding: 5px;"></td>
                    <td style="width:1px;"></td>
                    <td  style="background: #c6d7e7;padding: 5px;"></td>
                    <td style="width:1px;"></td>
                    <td style="background: #c6d7e7;padding: 5px;"></td>
                    <td style="width:1px;"></td>
                    <td style="background: #c6d7e7;padding: 5px;"></td>
                    <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                </tr>
                <tr>
                    <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                    <td style="background: #c6d7e7;padding: 5px;"></td>
                    <td style="width:1px;"></td>
                    <td  style="background: #c6d7e7;padding: 5px;"></td>
                    <td style="width:1px;"></td>
                    <td style="padding: 5px;"></td>
                    <td style="width:1px;"></td>
                    <td  style="background: #c6d7e7;padding: 5px;"></td>
                    <td style="width:1px;"></td>
                    <td style="background: #c6d7e7;padding: 5px;"></td>
                    <td style="width:1px;"></td>
                    <td style="background: #c6d7e7;padding: 5px;"></td>
                    <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                </tr>
                <tr>
                    <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                    <td style="background: #c6d7e7;padding: 5px;"></td>
                    <td style="width:1px;"></td>
                    <td  style="background: #c6d7e7;padding: 5px;"></td>
                    <td style="width:1px;"></td>
                    <td style="padding: 5px;"></td>
                    <td style="width:1px;"></td>
                    <td  style="background: #c6d7e7;padding: 5px;"></td>
                    <td style="width:1px;"></td>
                    <td style="background: #c6d7e7;padding: 5px;"></td>
                    <td style="width:1px;"></td>
                    <td style="background: #c6d7e7;padding: 5px;"></td>
                    <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                </tr>
                <tr>
                    <td style="width:25px;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                    <td colspan="2" style="background: #3079bb; color:white; font-size:12px; padding: 5px;width:75px;"></td>
                    <td colspan="3" style="background: #3079bb; color:white; font-size:12px;padding: 5px; width:50px;"></td>
                    <td colspan="2" style="background: #3079bb; color:white; font-size:12px;padding: 5px; width:75px;text-align:center;"></td>
                    <td colspan="2" style="background: #3079bb; color:white; font-size:16px; padding: 5px;width:75px;text-align:center;">{{ __('pdf.total') }}:</td>
                    <td colspan="2" style="background: #3079bb; color:white; font-size:16px;padding: 5px; width:75px;text-align:center;">0,00</td>
                    <td style="width:25px;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                </tr>
                <tr>
                    <td style="width:25px;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                    <td colspan="2" style="background: white; color:white; font-size:12px; padding: 5px;width:75px;"></td>
                    <td colspan="3" style="background: white; color:white; font-size:12px;padding: 5px; width:50px;"></td>
                    <td colspan="2" style="background: white; color:white; font-size:12px;padding: 5px; width:75px;text-align:center;"></td>
                    <td colspan="2" style="background: white; color:white; font-size:12px; padding: 5px;width:75px;text-align:center;"></td>
                    <td colspan="2" style="background: white; color:white; font-size:12px;padding: 5px; width:75px;text-align:center;"></td>
                    <td style="width:25px;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                </tr>
                <tr>
                    <td style="width:25px;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                    <td colspan="11" style="background: #white; color:black; padding: 5px;width:75px;">Klinik I/S, Ved Sporsløjfen 10, 2100 København Ø, cvr 29859035. tlf. 38882500 / info@klinik.dk
                    </td>
                    <td style="width:25px;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                </tr>

            </tbody>
        </table>  
    </page>
    
    <style>
        
    /*.input{border-bottom: 1px solid lightgray;padding: 2px 5px;}
    .textarea{border-bottom: 1px solid lightgray;padding:5px;min-height: 90px;}
    */	
    td{height:12px;}
    .mainTable{width:900px; line-height: 20px;padding:0;margin:0;}
    .innerTable{width:100%;padding:20px;}
        p.center{ width:100%; text-align:center; }
    </style>
         
</body>
</html>