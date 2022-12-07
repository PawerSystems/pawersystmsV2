@php $clipT = $clipE = $cpr =  $department = $emailSenderName = $emailReminderHours = $smsReminderHours = $cancelationStopHours = $bookingStopHours = $survey = $googleCode = ''; @endphp
    @foreach($settings as $setting)

        @if($setting->key == 'cpr_emp_fields')
            @if($setting->value == 'true')
                @php $cpr = 0; @endphp
            @else
                @php $cpr = 1; @endphp
            @endif 

        @elseif($setting->key == 'clipboard_treatment')
            @if($setting->value == 'true')
                @php $clipT = 0; @endphp
            @else
                @php $clipT = 1; @endphp
            @endif 

        @elseif($setting->key == 'clipboard_event')
            @if($setting->value == 'true')
                @php $clipE = 0; @endphp
            @else
                @php $clipE = 1; @endphp
            @endif      

        @elseif($setting->key == 'department')
            @if($setting->value == 'true')
                @php $department = 0; @endphp
            @else
                @php $department = 1; @endphp
            @endif      
        
        @elseif($setting->key == 'email_sender_name')
            @php $emailSenderName = $setting->value; @endphp
        
        @elseif($setting->key == 'email_reminder_time')
            @php $emailReminderHours = $setting->value; @endphp

        @elseif($setting->key == 'sms_reminder_time')
            @php $smsReminderHours = $setting->value; @endphp

       @elseif($setting->key == 'stop_cancellation')
            @php $cancelationStopHours = $setting->value; @endphp

        @elseif($setting->key == 'stop_booking')
            @php $bookingStopHours = $setting->value; @endphp

        @elseif($setting->key == 'survey_percentage')
            @php $survey = $setting->value; @endphp 
            
        @elseif($setting->key == 'google_analytics_code')
            @php $googleCode  = $setting->value; @endphp     

        @endif

    @endforeach
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta http-equiv="x-ua-compatible" content="ie=edge">
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <title>{{ $business->brand_name ?: $business->business_name }}</title>
  <link rel="icon" href="/images/{{ $business->logo ?: 'ps_logo.jpg' }}" type="image/icon type">

  <!-- Font Awesome Icons -->
  <link rel="stylesheet" href="{{asset('plugins/fontawesome-free/css/all.min.css')}}">
  <!-- IonIcons -->
  <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
  <!-- flag-icon-css -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/flag-icon-css/3.3.0/css/flag-icon.min.css">
  <!-- SweetAlert2 -->
  <link rel="stylesheet" href="{{asset('plugins/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css')}}">
  <!-- Toastr -->
  <link rel="stylesheet" href="{{asset('plugins/toastr/toastr.min.css')}}">
  <!-- DataTables -->
  <link rel="stylesheet" href="{{asset('plugins/datatables-bs4/css/dataTables.bootstrap4.min.css')}}">
  <link rel="stylesheet" href="{{asset('plugins/datatables-responsive/css/responsive.bootstrap4.min.css')}}">
  <!-- Theme style -->
  <link rel="stylesheet" href="{{asset('dist/css/adminlte.min.css')}}">
  <!-- iCheck for checkboxes and radio inputs -->
  <link rel="stylesheet" href="{{asset('plugins/icheck-bootstrap/icheck-bootstrap.min.css')}}">
  <!-- Bootstrap Color Picker -->
  <link rel="stylesheet" href="{{asset('plugins/bootstrap-colorpicker/css/bootstrap-colorpicker.min.css')}}">
  <!-- Tempusdominus Bbootstrap 4 -->
  <link rel="stylesheet" href="{{asset('plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css')}}">
  <!-- Select2 -->
  <link rel="stylesheet" href="{{asset('plugins/select2/css/select2.min.css')}}">
  <link rel="stylesheet" href="{{asset('plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css')}}">
  <!-- Bootstrap4 Duallistbox -->
  <link rel="stylesheet" href="{{asset('plugins/bootstrap4-duallistbox/bootstrap-duallistbox.min.css')}}">
  <!-- Google Font: Source Sans Pro -->
  <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet">
  <!-- summernote -->
  <link rel="stylesheet" href="{{asset('plugins/summernote/summernote-bs4.css')}}">
  <!-- overlayScrollbars -->
  <link rel="stylesheet" href="{{asset('plugins/overlayScrollbars/css/OverlayScrollbars.min.css')}}">
<!-- fullCalendar -->
{{-- <link rel="stylesheet" href="{{asset('plugins/fullcalendar/main.min.css')}}">
<link rel="stylesheet" href="{{asset('plugins/fullcalendar-daygrid/main.min.css')}}">
<link rel="stylesheet" href="{{asset('plugins/fullcalendar-timegrid/main.min.css')}}">
<link rel="stylesheet" href="{{asset('plugins/fullcalendar-bootstrap/main.min.css')}}"> --}}
<link href="{{asset('plugins/fullcalendargrid/lib/main.css')}}" rel='stylesheet' />
<style>
.goog-te-banner-frame.skiptranslate{ display:none !important; }
body{ top:0 !important; }
.v-none{display: none; }
.magin-color-icon{ color:limegreen; margin-right:5px; }
ul.pagination nav span { display:none; }
.web-logo{ width: 120px; height:70px; }
.select2-container, td{
    max-width: 100% !important;
}
</style>

<!-- IF clip setting For Treatment off then hide it -->
@if($clipT)
<style>
  .treatment .cutBack, .treatment .clipCard, .treatment .cutCard, .cardUsedt, .clipUsedt { display:none; }
</style>
@endif
<!-- IF clip setting for event off then hide it -->
@if($clipE)
<style>
  .event .cutBack, .event .clipCard, .event .cutCard , .cardUsede, .clipUsede { display:none; }
</style>
@endif
<!-- IF department setting off then hide it -->
@if($department)
<style>
  .department { display:none; }
</style>
@endif

<script>
    //---- For Push menu ----
    function checkpushmenu(){
        var token = $('meta[name="csrf-token"]').attr('content');
        $.ajax({
            type: 'POST',
            url: '/navSettings',
            data: {'_token':token },
            success: function (data) {
                console.log('Changed');
            },
            error: function (data) {
                console.log(data);
            }
        });

    }
</script>

{!! $googleCode  !!}
</head>


<body class="{{ $class }}" style="overflow-x:hidden;">
<div class="{{ $wrapper }}" style="overflow-x:hidden;">
  