@extends('layouts.backend')

@section('content')
<style>
i{ font-size:14px; }
</style>
  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>{{ __('leftnav.system_settings') }}</h1><br>
            @can('Settings Edit')
            @if( Auth::user()->role == 'Owner' )
            <a href="{{ route('settingGenerate',session('business_name')) }}" class="btn btn-info">{{ __('settings.reset_settings') }}</a>
            @endif
            @endcan
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="/">{{ __('keywords.home') }}</a></li>
              <li class="breadcrumb-item active">{{ __('leftnav.system_settings') }}</li>
            </ol>
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>
    @php $clipT = $clipE = $cpr = $cprInsurance = $emailFreeSpot = $emailFreeSpotEvent = $emailSenderName = $emailReminderHours = $smsReminderHours = $cancelationStopHours = $bookingStopHours = $survey = $surveyHours = $department = $maxBookingsMonth = $maxActiveBookings = $codeForBooking = $businessCode = $surveySetting = $dateFormat = $timeFormat = $smsSettings = $tbookingSms = $tdbookingSms = $trbookingSms = $ebookingSms = $edbookingSms = $erbookingSms = $clipAddSms = $clipUsedSms = $clipRestoreSms = $smsStopFrom = $smsStopTill = $dateUpdateEmail = $eventUpdateEmail = $surveyDuration = $googleAnalyticsCode = $offerSection = $offerURL = $landingPage = $graphDuration = $offerTitle = $ageGenderMandatory = $mobilePay = $mobilePayNumber = $emailForContactForm = $mdr = $receiptOption = $eventFreeSpotTime = $treatmentFreeSpotTime = ''; 
    $logForJournal = $logForRoles = $logForSurvey = $logRelatedUser = $logForSettings = $logForPages = $logForCardAndClips = $logForDateRelatedDetails = $logForEventRelatedDetails = $logForBookingRelatedDetails = $logForSms = $logForEmail = $logForLogin = $logForDepartment = $logForTreatmentPart = $logForPaymentMethod = $language = $eventCancel = '';
    @endphp
    @foreach($settings as $setting)

    
        @if($setting->key == 'cpr_emp_fields')
            @if($setting->value == 'true')
                @php $cpr = 'checked'; @endphp
            @else
                @php $cpr = ''; @endphp
            @endif 

        @elseif($setting->key == 'cpr_emp_fields_insurance')
            @if($setting->value == 'true')
                @php $cprInsurance = 'checked'; @endphp
            @else
                @php $cprInsurance = ''; @endphp
            @endif

        @elseif($setting->key == 'offer_section_home')
            @if($setting->value == 'true')
                @php $offerSection = 'checked'; @endphp
            @else
                @php $offerSection = ''; @endphp
            @endif

        @elseif($setting->key == 'clipboard_treatment')
            @if($setting->value == 'true')
                @php $clipT = 'checked'; @endphp
            @else
                @php $clipT = ''; @endphp
            @endif

        @elseif($setting->key == 'date_update_email')
            @if($setting->value == 'true')
                @php $dateUpdateEmail = 'checked'; @endphp
            @else
                @php $dateUpdateEmail = ''; @endphp
            @endif 

        @elseif($setting->key == 'event_update_email')
            @if($setting->value == 'true')
                @php $eventUpdateEmail = 'checked'; @endphp
            @else
                @php $eventUpdateEmail = ''; @endphp
            @endif        

        @elseif($setting->key == 'clipboard_event')
            @if($setting->value == 'true')
                @php $clipE = 'checked'; @endphp
            @else
                @php $clipE = ''; @endphp
            @endif      

        @elseif($setting->key == 'free_spot_email')
            @if($setting->value == 'true')
                @php $emailFreeSpot = 'checked'; @endphp
            @else
                @php $emailFreeSpot = ''; @endphp
            @endif 

        @elseif($setting->key == 'free_spot_email_for_event')
            @if($setting->value == 'true')
                @php $emailFreeSpotEvent = 'checked'; @endphp
            @else
                @php $emailFreeSpotEvent = ''; @endphp
            @endif     

            
        
        @elseif($setting->key == 'department')
            @if($setting->value == 'true')
                @php $department = 'checked'; @endphp
            @else
                @php $department = ''; @endphp
            @endif
        
        @elseif($setting->key == 'code_for_booking')
            @if($setting->value == 'true')
                @php $codeForBooking = 'checked'; @endphp
            @else
                @php $codeForBooking = ''; @endphp
            @endif 

        @elseif($setting->key == 'survey_setting')
            @if($setting->value == 'true')
                @php $surveySetting = 'checked'; @endphp
            @else
                @php $surveySetting = ''; @endphp
            @endif 

        @elseif($setting->key == 't_booking_sms')
            @if($setting->value == 'true')
                @php $tbookingSms = 'checked'; @endphp
            @else
                @php $tbookingSms = ''; @endphp
            @endif 
             
        @elseif($setting->key == 't_d_booking_sms')
            @if($setting->value == 'true')
                @php $tdbookingSms = 'checked'; @endphp
            @else
                @php $tdbookingSms = ''; @endphp
            @endif 

        @elseif($setting->key == 't_r_booking_sms')
            @if($setting->value == 'true')
                @php $trbookingSms = 'checked'; @endphp
            @else
                @php $trbookingSms = ''; @endphp
            @endif 

        @elseif($setting->key == 'e_booking_sms')
            @if($setting->value == 'true')
                @php $ebookingSms = 'checked'; @endphp
            @else
                @php $ebookingSms = ''; @endphp
            @endif 
             
        @elseif($setting->key == 'e_d_booking_sms')
            @if($setting->value == 'true')
                @php $edbookingSms = 'checked'; @endphp
            @else
                @php $edbookingSms = ''; @endphp
            @endif 

        @elseif($setting->key == 'e_r_booking_sms')
            @if($setting->value == 'true')
                @php $erbookingSms = 'checked'; @endphp
            @else
                @php $erbookingSms = ''; @endphp
            @endif  

        @elseif($setting->key == 'clip_add')
            @if($setting->value == 'true')
                @php $clipAddSms = 'checked'; @endphp
            @else
                @php $clipAddSms = ''; @endphp
            @endif 

        @elseif($setting->key == 'clip_used')
            @if($setting->value == 'true')
                @php $clipUsedSms = 'checked'; @endphp
            @else
                @php $clipUsedSms = ''; @endphp
            @endif 

        @elseif($setting->key == 'clip_restore')
            @if($setting->value == 'true')
                @php $clipRestoreSms = 'checked'; @endphp
            @else
                @php $clipRestoreSms = ''; @endphp
            @endif

        @elseif($setting->key == 'age_gender_mandatory')
            @if($setting->value == 'true')
                @php $ageGenderMandatory = 'checked'; @endphp
            @else
                @php $ageGenderMandatory = ''; @endphp
            @endif

        @elseif($setting->key == 'mobile_pay')
            @if($setting->value == 'true')
                @php $mobilePay = 'checked'; @endphp
            @else
                @php $mobilePay = ''; @endphp
            @endif  
         
        @elseif($setting->key == 'mdr_field')
            @if($setting->value == 'true')
                @php $mdr = 'checked'; @endphp
            @else
                @php $mdr = ''; @endphp
            @endif  
         
        @elseif($setting->key == 'receipt_option')
            @if($setting->value == 'true')
                @php $receiptOption = 'checked'; @endphp
            @else
                @php $receiptOption = ''; @endphp
            @endif 

        @elseif($setting->key == 'log_for_email')
            @if($setting->value == 'true')
                @php $logForEmail = 'checked'; @endphp
            @else
                @php $logForEmail = ''; @endphp
            @endif 

        @elseif($setting->key == 'log_for_sms')
            @if($setting->value == 'true')
                @php $logForSms = 'checked'; @endphp
            @else
                @php $logForSms = ''; @endphp
            @endif 

        @elseif($setting->key == 'log_for_booking_related_details')
            @if($setting->value == 'true')
                @php $logForBookingRelatedDetails = 'checked'; @endphp
            @else
                @php $logForBookingRelatedDetails = ''; @endphp
            @endif 

        @elseif($setting->key == 'log_for_event_related_details')
            @if($setting->value == 'true')
                @php $logForEventRelatedDetails = 'checked'; @endphp
            @else
                @php $logForEventRelatedDetails = ''; @endphp
            @endif 

        @elseif($setting->key == 'log_for_date_related_details')
            @if($setting->value == 'true')
                @php $logForDateRelatedDetails = 'checked'; @endphp
            @else
                @php $logForDateRelatedDetails = ''; @endphp
            @endif 

        @elseif($setting->key == 'log_for_card_and_clips')
            @if($setting->value == 'true')
                @php $logForCardAndClips = 'checked'; @endphp
            @else
                @php $logForCardAndClips = ''; @endphp
            @endif 

        @elseif($setting->key == 'log_for_pages')
            @if($setting->value == 'true')
                @php $logForPages = 'checked'; @endphp
            @else
                @php $logForPages = ''; @endphp
            @endif 

        @elseif($setting->key == 'log_for_settings')
            @if($setting->value == 'true')
                @php $logForSettings = 'checked'; @endphp
            @else
                @php $logForSettings = ''; @endphp
            @endif 

        @elseif($setting->key == 'log_related_user')
            @if($setting->value == 'true')
                @php $logRelatedUser = 'checked'; @endphp
            @else
                @php $logRelatedUser = ''; @endphp
            @endif 

        @elseif($setting->key == 'log_for_survey')
            @if($setting->value == 'true')
                @php $logForSurvey = 'checked'; @endphp
            @else
                @php $logForSurvey = ''; @endphp
            @endif 

        @elseif($setting->key == 'log_for_roles')
            @if($setting->value == 'true')
                @php $logForRoles = 'checked'; @endphp
            @else
                @php $logForRoles = ''; @endphp
            @endif 

        @elseif($setting->key == 'log_for_journal')
            @if($setting->value == 'true')
                @php $logForJournal = 'checked'; @endphp
            @else
                @php $logForJournal = ''; @endphp
            @endif 

        @elseif($setting->key == 'log_for_login')
            @if($setting->value == 'true')
                @php $logForLogin = 'checked'; @endphp
            @else
                @php $logForLogin = ''; @endphp
            @endif  
            
        @elseif($setting->key == 'log_for_department')
            @if($setting->value == 'true')
                @php $logForDepartment = 'checked'; @endphp
            @else
                @php $logForDepartment = ''; @endphp
            @endif  
            
            @elseif($setting->key == 'log_for_treatment_part')
            @if($setting->value == 'true')
                @php $logForTreatmentPart = 'checked'; @endphp
            @else
                @php $logForTreatmentPart = ''; @endphp
            @endif  
            
            @elseif($setting->key == 'log_for_payment_method')
            @if($setting->value == 'true')
                @php $logForPaymentMethod = 'checked'; @endphp
            @else
                @php $logForPaymentMethod = ''; @endphp
            @endif  
            

        @elseif($setting->key == 'mobile_pay_number')
            @php $mobilePayNumber = $setting->value; @endphp
        
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

        @elseif($setting->key == 'survey_send_hours')
            @php $surveyHours = $setting->value; @endphp

        @elseif($setting->key == 'survey_duration')
            @php $surveyDuration = $setting->value; @endphp
            
        @elseif($setting->key == 'max_active_bookings')
            @php $maxActiveBookings = $setting->value; @endphp  

        @elseif($setting->key == 'max_bookings_month')
            @php $maxBookingsMonth = $setting->value; @endphp            
            
        @elseif($setting->key == 'code')
            @php $businessCode = $setting->value; @endphp            

        @elseif($setting->key == 'date_format')
            @php $dateFormat = $setting->value; @endphp 

        @elseif($setting->key == 'time_format')
            @php $timeFormat = $setting->value; @endphp    

        @elseif($setting->key == 'sms_setting')
            @php $smsSettings = $setting->value; @endphp 

        @elseif($setting->key == 'sms_stop_from')
            @php $smsStopFrom = $setting->value; @endphp      

        @elseif($setting->key == 'sms_stop_till')
            @php $smsStopTill = $setting->value; @endphp 
         
        @elseif($setting->key == 'google_analytics_code')
            @php $googleAnalyticsCode = $setting->value; @endphp     
        
        @elseif($setting->key == 'offer_url')
            @php $offerURL = $setting->value; @endphp     

        @elseif($setting->key == 'landing_page')
            @php $landingPage = $setting->value; @endphp        
           
        @elseif($setting->key == 'graph_duration')
            @php $graphDuration = $setting->value; @endphp

        @elseif($setting->key == 'offer_titlez')
            @php $offerTitle = $setting->value; @endphp
           
        @elseif($setting->key == 'email_for_contact_form')
            @php $emailForContactForm = $setting->value; @endphp  

        @elseif($setting->key == 'event_free_spot_time')
            @php $eventFreeSpotTime = $setting->value; @endphp 
            
        @elseif($setting->key == 'treatment_free_spot_time')
            @php $treatmentFreeSpotTime = $setting->value; @endphp  

        @elseif($setting->key == 'system_language')
            @php $language = $setting->value; @endphp  
            
        @elseif($setting->key == 'event_cancel')
            @php $eventCancel = $setting->value; @endphp      
            
        @endif

    @endforeach
    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-6">
                    <!----- Clips for treatments ----->
                    <div class="card card-secondary">
                        <div class="card-header">
                            <h3 class="card-title">{{ __('settings.clipboard') }}</h3>
                        </div>
                        <div class="card-body">
                            <div class="form-group">
                                <label>{{ __('settings.clips_for_treatment') }}:</label>
                                <div class="input-group">
                                    <input type="checkbox" name="clipboard_treatment" id="clipboard_treatment" {{ $clipT }} data-bootstrap-switch>
                                </div>
                            </div> 
                            <div class="form-group">
                                <label>{{ __('settings.clips_for_event') }}:</label>
                                <div class="input-group">
                                    <input type="checkbox" name="clipboard_event" id="clipboard_event" {{ $clipE }} data-bootstrap-switch>
                                </div>
                            </div> 
                        </div>
                    </div>

                    <!------ Email Settings --------->      
                    <div class="card card-secondary">
                        <div class="card-header">
                            <h3 class="card-title">{{ __('settings.email_settings') }}</h3>
                        </div>
                        <div class="card-body">
                            <div class="form-group">
                                <label>{{ __('settings.email_for_contact_form') }}:</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">
                                        <i class="fas fa-signature"></i>
                                        </span>
                                    </div>
                                    <input type="text" class="form-control" id="email_for_contact_form" name="email_for_contact_form" value="{{ $emailForContactForm }}" onfocusout="getVal(this)">
                                </div>
                            </div>

                            <div class="form-group">
                                <label>{{ __('settings.email_sender_name') }}:</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">
                                        <i class="fas fa-signature"></i>
                                        </span>
                                    </div>
                                    <input type="text" class="form-control" id="email_sender_name" name="email_sender_name" value="{{ $emailSenderName }}" onfocusout="getVal(this)">
                                </div>
                            </div>

                            <!-- /.form group -->
                            <div class="form-group">
                                <label>{{ __('settings.h_b_e_r_t_b_s') }}:</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">
                                        <i class="fas fa-hourglass-start"></i>
                                        </span>
                                    </div>
                                    <input type="number" class="form-control" id="email_reminder_time" value="{{ $emailReminderHours }}" min="0" name="email_reminder_time" onfocusout="getVal(this)">
                                </div>
                            </div>

                            <!-- Free Spot Email -->
                            <div class="form-group">
                                <label>{{ __('settings.free_spot_email_for_treatment') }}:</label>
                                <div class="input-group">
                                    <input type="checkbox" name="free_spot_email" id="free_spot_email" {{$emailFreeSpot}} data-bootstrap-switch>
                                </div>
                            </div>

                            <div class="form-group">
                                <label>{{ __('settings.treatment_free_spot_time') }} <i>({{ __('settings.time_format_note') }})</i>:</label>
                                <div class="input-group date" id="timepicker3" data-target-input="nearest">
                                    <div class="input-group-append" data-target="#timepicker3" data-toggle="datetimepicker">
                                        <div class="input-group-text"><i class="far fa-clock"></i></div>
                                    </div>
                                    <input type="text" id="treatment_free_spot_time" name="treatment_free_spot_time" class="form-control datetimepicker-input" value="{{ $treatmentFreeSpotTime }}" data-target="#timepicker3" onfocusout="getVal(this)"/>
                                </div>
                            </div>

                            <!-- Free Spot Email -->
                            <div class="form-group">
                                <label>{{ __('settings.free_spot_email_for_event') }}:</label>
                                <div class="input-group">
                                    <input type="checkbox" name="free_spot_email_for_event" id="free_spot_email_for_event" {{$emailFreeSpotEvent}} data-bootstrap-switch>
                                </div>
                            </div> 

                            <div class="form-group">
                                <label>{{ __('settings.event_free_spot_time') }} <i>({{ __('settings.time_format_note') }})</i>:</label>
                                <div class="input-group date" id="timepicker4" data-target-input="nearest">
                                    <div class="input-group-append" data-target="#timepicker4" data-toggle="datetimepicker">
                                        <div class="input-group-text"><i class="far fa-clock"></i></div>
                                    </div>
                                    <input type="text" id="event_free_spot_time" name="event_free_spot_time" class="form-control datetimepicker-input" value="{{ $eventFreeSpotTime }}" data-target="#timepicker4" onfocusout="getVal(this)"/>
                                </div>
                            </div>

                            <!-- Treatment Date update Email -->
                            <div class="form-group">
                                <label>{{ __('settings.treatment_date_update') }}:</label>
                                <div class="input-group">
                                    <input type="checkbox" name="date_update_email" id="date_update_email" {{$dateUpdateEmail}} data-bootstrap-switch>
                                </div>
                            </div> 

                            <!--Event Update Email -->
                            <div class="form-group">
                                <label>{{ __('settings.event_update') }}:</label>
                                <div class="input-group">
                                    <input type="checkbox" name="event_update_email" id="event_update_email" {{$eventUpdateEmail}} data-bootstrap-switch>
                                </div>
                            </div> 

                        </div>
                    </div>

                    <!------ SMS Settings --------->   
                    @if($smsSettings == 'true')   
                    <div class="card card-secondary">
                        <div class="card-header">
                            <h3 class="card-title">{{ __('settings.sms_settings') }} </h3>
                        </div>
                        <div class="card-body">
                            <div class="form-group">
                                <label>{{ __('settings.h_b_s_r_t_b_s') }}:</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">
                                        <i class="fas fa-hourglass-start"></i>
                                        </span>
                                    </div>
                                    <input type="number" class="form-control" id="sms_reminder_time" min="0" name="sms_reminder_time" value="{{ $smsReminderHours }}" onfocusout="getVal(this)">
                                </div>
                                
                            </div>

                            <div class="form-group">
                                <label>{{ __('settings.sms_stop_from') }} <i>({{ __('settings.time_format_note') }})</i>:</label>
                                <div class="input-group date" id="timepicker" data-target-input="nearest">
                                    <div class="input-group-append" data-target="#timepicker" data-toggle="datetimepicker">
                                        <div class="input-group-text"><i class="far fa-clock"></i></div>
                                    </div>
                                    <input type="text" id="sms_stop_from" name="sms_stop_from" class="form-control datetimepicker-input" value="{{ $smsStopFrom }}" data-target="#timepicker" onfocusout="getVal(this)"/>
                                </div>
                            </div>

                            <div class="form-group">
                                <label>{{ __('settings.sms_stop_till') }} <i>({{ __('settings.time_format_note') }})</i>:</label>
                                <div class="input-group date" id="timepicker2" data-target-input="nearest">
                                    <div class="input-group-append" data-target="#timepicker2" data-toggle="datetimepicker">
                                        <div class="input-group-text"><i class="far fa-clock"></i></div>
                                    </div>
                                    <input type="text" id="sms_stop_till" name="sms_stop_till" class="form-control datetimepicker-input" value="{{ $smsStopTill }}" data-target="#timepicker2" onfocusout="getVal(this)"/>
                                </div>
                            </div>

                            <div class="form-group">
                                <label>{{ __('settings.treatment_booking_sms') }}:</label>
                                <div class="input-group">
                                    <input type="checkbox" name="t_booking_sms" id="t_booking_sms" {{$tbookingSms}} data-bootstrap-switch>
                                </div>
                            </div> 

                            <div class="form-group">
                                <label>{{ __('settings.treatment_booking_delete_sms') }}:</label>
                                <div class="input-group">
                                    <input type="checkbox" name="t_d_booking_sms" id="t_d_booking_sms" {{$tdbookingSms}} data-bootstrap-switch>
                                </div>
                            </div> 
                            
                            <div class="form-group">
                                <label>{{ __('settings.treatment_booking_restore_sms') }}:</label>
                                <div class="input-group">
                                    <input type="checkbox" name="t_r_booking_sms" id="t_r_booking_sms" {{$trbookingSms}} data-bootstrap-switch>
                                </div>
                            </div>

                            <div class="form-group">
                                <label>{{ __('settings.event_booking_sms') }}:</label>
                                <div class="input-group">
                                    <input type="checkbox" name="e_booking_sms" id="e_booking_sms" {{$ebookingSms}} data-bootstrap-switch>
                                </div>
                            </div> 

                            <div class="form-group">
                                <label>{{ __('settings.event_booking_delete_sms') }}:</label>
                                <div class="input-group">
                                    <input type="checkbox" name="e_d_booking_sms" id="e_d_booking_sms" {{$edbookingSms}} data-bootstrap-switch>
                                </div>
                            </div> 
                            
                            <div class="form-group">
                                <label>{{ __('settings.event_booking_restore_sms') }}:</label>
                                <div class="input-group">
                                    <input type="checkbox" name="e_r_booking_sms" id="e_r_booking_sms" {{$erbookingSms}} data-bootstrap-switch>
                                </div>
                            </div>

                            <div class="form-group">
                                <label>{{ __('settings.clips_add_sms') }}:</label>
                                <div class="input-group">
                                    <input type="checkbox" name="clip_add" id="clip_add" {{$clipAddSms}} data-bootstrap-switch>
                                </div>
                            </div>

                            <div class="form-group">
                                <label>{{ __('settings.clips_used_sms') }}:</label>
                                <div class="input-group">
                                    <input type="checkbox" name="clip_used" id="clip_used" {{$clipUsedSms}} data-bootstrap-switch>
                                </div>
                            </div>

                            <div class="form-group">
                                <label>{{ __('settings.clips_restore_sms') }}:</label>
                                <div class="input-group">
                                    <input type="checkbox" name="clip_restore" id="clip_restore" {{$clipRestoreSms}} data-bootstrap-switch>
                                </div>
                            </div>

                        </div>
                    </div>
                    @endif
                    <!-- /.card -->

                    <!----- Offer section on home page ----->
                    <div class="card card-secondary">
                        <div class="card-header">
                            <h3 class="card-title">{{ __('settings.offer_section_home_page') }}</h3>
                        </div>
                        <div class="card-body">
                            
                            <div class="form-group">
                                <label>{{ __('settings.offer_title') }}:</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">
                                        <i class="fas fa-signature"></i>
                                        </span>
                                    </div>
                                    <input type="text" class="form-control" id="offer_title" name="offer_title" value="{{ $offerTitle }}" onfocusout="getVal(this)">
                                </div>

                                <label class="mt-4">{{ __('settings.offer_url') }}:</label>
                                <div class="input-group">
                                    <textarea class="form-control" id="offer_url" name="offer_url" value="{{ $offerURL }}" onfocusout="getVal(this)">{{ $offerURL }}</textarea>
                                </div>                                
                            </div>

                            <input type="checkbox" name="offer_section_home" id="offer_section_home" {{$offerSection}} data-bootstrap-switch>
                        </div>
                    </div>
                    <!-- /.card -->

                    <!----- Lading page after login ----->
                    <div class="card card-secondary">
                        <div class="card-header">
                            <h3 class="card-title">{{ __('settings.landing_page') }}</h3>
                        </div>
                        <div class="card-body">

                            <div class="form-group">
                                <div class="input-group">
                                    <select class="form-control" name="landing_page" id="landing_page" onchange="getVal(this)">
                                        <option value="dashboard" {{ $landingPage == 'dashboard' ? 'selected' : '' }}>{{ __('settings.dashboard_page') }}</option>
                                        <option value="profile" {{ $landingPage == 'profile' ? 'selected' : '' }}>{{ __('settings.profile_page') }}</option>
                                        <option value="treatmentbookings" {{ $landingPage == 'treatmentbookings' ? 'selected' : '' }}>{{ __('settings.treatment_booking_page') }}</option>
                                        <option value="eventBookingList" {{ $landingPage == 'eventBookingList' ? 'selected' : '' }}>{{ __('settings.event_booking_page') }}</option>
                                    </select>
                                </div>
                            </div> 

                            <div class="form-group">
                                <label>{{ __('settings.age_gender_mandatory') }}:</label>
                                <div class="input-group">
                                    <input type="checkbox" name="age_gender_mandatory" id="age_gender_mandatory" {{$ageGenderMandatory}} data-bootstrap-switch>
                                </div>
                            </div>
                            
                        </div>
                    </div>
                    <!-- /.card -->

                    <!----- Graph duration limit ----->
                    <div class="card card-secondary">
                        <div class="card-header">
                            <h3 class="card-title">{{ __('settings.graph_duration') }}</h3>
                        </div>
                        <div class="card-body">

                            <div class="form-group">
                                <div class="input-group">
                                    <select class="form-control" name="graph_duration" id="graph_duration" onchange="getVal(this)">
                                        @for ($i = 1; $i < 61; $i++)
                                            <option value="{{ $i }}" {{ $graphDuration == $i ? 'selected' : '' }}>{{ $i }}</option>
                                        @endfor
                                    </select>
                                </div>
                            </div>                            
                        </div>
                    </div>
                    <!-- /.card -->

                    <!----- System Default Language ----->
                    <div class="card card-secondary">
                        <div class="card-header">
                            <h3 class="card-title">{{ __('settings.system_language') }}</h3>
                        </div>
                        <div class="card-body">

                            <div class="form-group">
                                <div class="input-group">
                                    <select class="form-control" name="system_language" id="system_language" onchange="getVal(this)">
                                        @foreach ( Config::get('languages') as $key => $val )
                                            <option value="{{ $key }}" {{ $language == $key ? 'selected' : '' }} >{{ $val['display'] }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div> 
                            
                        </div>
                    </div>
                    <!-- /.card -->
                    
                </div>  

                <div class="col-md-6">  
                     <!------ Bookings Settings --------->      
                     <div class="card card-secondary">
                        <div class="card-header">
                            <h3 class="card-title">{{ __('settings.bookings_settings') }}</h3>
                        </div>
                        <div class="card-body">
                            <div class="form-group">
                                <label>{{ __('settings.hbtcod') }}:</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">
                                        <i class="fas fa-times"></i>
                                        </span>
                                    </div>
                                    <input type="text" class="form-control" id="stop_cancellation" name="stop_cancellation" value="{{ $cancelationStopHours }}" onfocusout="getVal(this)">
                                </div>
                            </div>
                            <!-- /.form group -->

                            <div class="form-group">
                                <label>{{ __('settings.nohbbos') }}:</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">
                                        <i class="fas fa-check"></i>
                                        </span>
                                    </div>
                                    <input type="number" class="form-control" id="stop_booking" min="0" name="stop_booking" value="{{ $bookingStopHours }}" onfocusout="getVal(this)">
                                </div>
                            </div>
                            <!-- /.form group -->

                            <div class="form-group">
                                <label>{{ __('settings.mnoabits') }}:<br><i>( -1 {{ __('settings.represent_unlimited') }}  )</i></label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">
                                        <i class="fas fa-sort-numeric-up-alt"></i>  
                                        </span>
                                    </div>
                                    <input type="number" class="form-control" id="max_active_bookings" min="-1" name="max_active_bookings" value="{{ $maxActiveBookings }}" onfocusout="getVal(this)">
                                </div>
                            </div>
                            <!-- /.form group -->

                            <div class="form-group">
                                <label>{{ __('settings.mnobpm') }}:<br><i>( -1 {{ __('settings.represent_unlimited') }}  )</i></label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">
                                        <i class="far fa-calendar-alt"></i>                                      
                                        </span>
                                    </div>
                                    <input type="number" class="form-control" id="max_bookings_month" min="-1" name="max_bookings_month" value="{{ $maxBookingsMonth }}" onfocusout="getVal(this)">
                                </div>
                            </div>
                            <!-- /.form group -->

                            <div class="form-group">
                                <label>{{ __('settings.event_cancel') }}:<br><i>( {{ __('settings.ewbcbh') }} )</i></label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">
                                        <i class="far fa-calendar-alt"></i>                                      
                                        </span>
                                    </div>
                                    <input type="number" class="form-control" id="event_cancel" min="-1" name="event_cancel" value="{{ $eventCancel }}" onfocusout="getVal(this)">
                                </div>
                            </div>
                            <!-- /.form group -->

                            <div class="form-group">
                                <label>{{ __('settings.business_code') }}:</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">
                                        <i class="fas fa-key"></i>                                      
                                        </span>
                                    </div>
                                    <input type="text" class="form-control" id="code" name="code" value="{{ $businessCode }}" onfocusout="getVal(this)">
                                </div>
                            </div>
                            <!-- /.form group -->
                            
                            <div class="form-group">
                                <label>{{ __('settings.code_for_customer_to_book') }}:</label>
                                <div class="input-group">
                                    <input type="checkbox" name="code_for_booking" id="code_for_booking" {{$codeForBooking}} data-bootstrap-switch>
                                </div>
                            </div>
                            <!-- /.form group -->

                            {{-- <div class="form-group">
                                <label>{{ __('settings.receipt') }}:</label>
                                <div class="input-group">
                                    <input type="checkbox" name="receipt_option" id="receipt_option" {{$receiptOption}} data-bootstrap-switch>
                                </div>
                            </div> --}}
                            <!-- /.form group -->

                        </div>
                    </div>


                    <!----- CPR / employee no field ----->
                    <div class="card card-secondary">
                        <div class="card-header">
                            <h3 class="card-title">{{ __('settings.cpr_emp_field_settings') }}</h3>
                        </div>

                        <div class="card-body">

                            <div class="form-group">
                                <label>{{ __('settings.cpr_for_general') }}:</label>
                                <div class="input-group">
                                    <input type="checkbox" name="cpr_emp_fields" id="cpr_emp_fields" {{$cpr}} data-bootstrap-switch>
                                </div>
                            </div>
                            <!-- /.form group -->

                            <div class="form-group">
                                <label>{{ __('settings.cpr_for_insurance') }}:</label>
                                <div class="input-group">
                                    <input type="checkbox" name="cpr_emp_fields_insurance" id="cpr_emp_fields_insurance" {{$cprInsurance}} data-bootstrap-switch>
                                </div>
                            </div>
                            <!-- /.form group -->

                            <div class="form-group">
                                <label>{{ __('settings.mdr_field') }}:</label>
                                <div class="input-group">
                                    <input type="checkbox" name="mdr_field" id="mdr_field" {{$mdr}} data-bootstrap-switch>
                                </div>
                            </div>
                            <!-- /.form group -->

                        </div>                        
                    </div>
                    <!-- /.card -->

                    <!------ Department Settings ---------> 
                    <div class="card card-secondary">
                    <div class="card-header">
                            <h3 class="card-title">{{ __('settings.department_field_settings') }}</h3>
                        </div>
                        <div class="card-body">
                            <input type="checkbox" name="department" id="department" {{$department}} data-bootstrap-switch>
                        </div>
                    </div>
                    <!-- /.card -->

                    <!------ Department Settings --------->      
                    <div class="card card-secondary">
                    <div class="card-header">
                            <h3 class="card-title">{{ __('settings.date_format_settings') }}</h3>
                        </div>
                        <div class="card-body">
                        <div class="form-group">
                                <label>{{ __('settings.date_format') }}:</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">
                                        <i class="fas fa-calendar"></i>
                                        </span>
                                    </div>
                                    <select name="date_format" id="date_format" class="form-control" onchange="getVal(this)">
                                        <option value="d-m-Y" {{ $dateFormat == 'd-m-Y' ? 'selected' : '' }}>28-02-1900</option>
                                        {{-- <option value="d-M-Y" {{ $dateFormat == 'd-M-Y' ? 'selected' : '' }}>28-Feb-1900</option>
                                        <option value="Y-M-d" {{ $dateFormat == 'Y-M-d' ? 'selected' : '' }}>1900-Feb-28</option> --}}
                                        <option value="Y-m-d" {{ $dateFormat == 'Y-m-d' ? 'selected' : '' }}>1900-02-28</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label>{{ __('settings.time_format') }}:</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">
                                        <i class="fas fa-clock"></i>
                                        </span>
                                    </div>
                                    <select name="time_format" id="time_format" class="form-control" onchange="getVal(this)">
                                        <option value="24" {{ $timeFormat == '24' ? 'selected' : '' }}>24h</option>
                                        <option value="12" {{ $timeFormat == '12' ? 'selected' : '' }}>12h</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- /.card -->

                    <!------ Survey Settings --------->      
                    <div class="card card-secondary">
                        <div class="card-header">
                            <h3 class="card-title">{{ __('settings.survey_settings') }}</h3>
                        </div>
                        <div class="card-body">
                        
                            <div class="form-group">
                                <label>{{ __('settings.per_rec_survey') }}:</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">
                                            <i class="fas fa-poll"></i>
                                        </span>
                                    </div>
                                    <input type="number" class="form-control" id="survey_percentage" min="0" name="survey_percentage" value="{{ $survey }}" onfocusout="getVal(this)">
                                </div>
                            </div>

                            <div class="form-group">
                                <label>{{ __('settings.survey_duration') }}:</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">
                                            <i class="fas fa-calendar"></i>
                                        </span>
                                    </div>
                                    <input type="number" class="form-control" id="survey_duration" min="0" name="survey_duration" value="{{ $surveyDuration }}" onfocusout="getVal(this)">
                                </div>
                            </div>

                            

                            <div class="form-group">
                                <label>{{ __('settings.hasetbs') }}:</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">
                                            <i class="fas fa-hourglass-start"></i>
                                        </span>
                                    </div>
                                    <input type="number" class="form-control" id="survey_send_hours" min="0" name="survey_send_hours" value="{{ $surveyHours }}" onfocusout="getVal(this)">
                                </div>
                            </div>

                            
                            <div class="form-group">
                                <label>{{ __('settings.survey_enable_disable') }}:</label>
                                <div class="input-group">
                                    <input type="checkbox" name="survey_setting" id="survey_setting" {{$surveySetting}} data-bootstrap-switch>
                                </div>
                            </div>
                            <!-- /.form group -->

                        </div>
                    </div>
                    <!-- /.card -->

                    <!------ Google Analytics Code Settings --------->      
                    <div class="card card-secondary">
                        <div class="card-header">
                            <h3 class="card-title">{{ __('settings.analytics') }}</h3>
                        </div>
                        <div class="card-body">
                        
                            <div class="form-group">
                                <label>{{ __('settings.google_code') }}:</label>
                                <div class="input-group">
                                    <textarea class="form-control" id="google_analytics_code" name="google_analytics_code" value="{{ $googleAnalyticsCode }}" onfocusout="getVal(this)">{{ $googleAnalyticsCode }}</textarea>
                                </div>
                            </div>

                        </div>
                    </div>
                    <!-- /.card -->
                    
                    <!----- MobilePay option ----->
                    <div class="card card-secondary">
                        <div class="card-header">
                            <h3 class="card-title">{{ __('settings.mobile_pay_settings') }}</h3>
                        </div>
                        <div class="card-body">
                            
                            <div class="form-group">
                                <label>{{ __('settings.mobile_pay_number') }}:</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">
                                        <i class="fas fa-signature"></i>
                                        </span>
                                    </div>
                                    <input type="text" class="form-control" id="mobile_pay_number" name="mobile_pay_number" value="{{ $mobilePayNumber }}" onfocusout="getVal(this)" placeholder="12345678">
                                </div>
                            </div>

                            <input type="checkbox" name="mobile_pay" id="mobile_pay" {{$mobilePay}} data-bootstrap-switch>
                        </div>
                    </div>
                    <!-- /.card -->

                    <!------ Log Settings ---------> 
                    @can('View Log')  
                    <div class="card card-secondary">
                        <div class="card-header">
                            <h3 class="card-title">{{ __('settings.log_setting') }} </h3>
                        </div>
                        <div class="card-body">

                            <div class="form-group">
                                <label>{{ __('settings.log_for_email') }}:</label>
                                <div class="input-group">
                                    <input type="checkbox" name="log_for_email" id="log_for_email" {{ $logForEmail }} data-bootstrap-switch>
                                </div>
                            </div> 

                            <div class="form-group">
                                <label>{{ __('settings.log_for_sms') }}:</label>
                                <div class="input-group">
                                    <input type="checkbox" name="log_for_sms" id="log_for_sms" {{ $logForSms }} data-bootstrap-switch>
                                </div>
                            </div> 

                            <div class="form-group">
                                <label>{{ __('settings.log_for_booking_related_details') }}:</label>
                                <div class="input-group">
                                    <input type="checkbox" name="log_for_booking_related_details" id="log_for_booking_related_details" {{ $logForBookingRelatedDetails }} data-bootstrap-switch>
                                </div>
                            </div>

                            <div class="form-group">
                                <label>{{ __('settings.log_for_event_related_details') }}:</label>
                                <div class="input-group">
                                    <input type="checkbox" name="log_for_event_related_details" id="log_for_event_related_details" {{ $logForEventRelatedDetails }} data-bootstrap-switch>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label>{{ __('settings.log_for_date_related_details') }}:</label>
                                <div class="input-group">
                                    <input type="checkbox" name="log_for_date_related_details" id="log_for_date_related_details" {{ $logForDateRelatedDetails }} data-bootstrap-switch>
                                </div>
                            </div>

                            <div class="form-group">
                                <label>{{ __('settings.log_for_card_and_clips') }}:</label>
                                <div class="input-group">
                                    <input type="checkbox" name="log_for_card_and_clips" id="log_for_card_and_clips" {{ $logForCardAndClips }} data-bootstrap-switch>
                                </div>
                            </div> 
                            
                            <div class="form-group">
                                <label>{{ __('settings.log_for_pages') }}:</label>
                                <div class="input-group">
                                    <input type="checkbox" name="log_for_pages" id="log_for_pages" {{ $logForPages }} data-bootstrap-switch>
                                </div>
                            </div> 

                            <div class="form-group">
                                <label>{{ __('settings.log_for_settings') }}:</label>
                                <div class="input-group">
                                    <input type="checkbox" name="log_for_settings" id="log_for_settings" {{ $logForSettings }} data-bootstrap-switch>
                                </div>
                            </div> 

                            <div class="form-group">
                                <label>{{ __('settings.log_related_user') }}:</label>
                                <div class="input-group">
                                    <input type="checkbox" name="log_related_user" id="log_related_user" {{ $logRelatedUser }} data-bootstrap-switch>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label>{{ __('settings.log_for_survey') }}:</label>
                                <div class="input-group">
                                    <input type="checkbox" name="log_for_survey" id="log_for_survey" {{ $logForSurvey }} data-bootstrap-switch>
                                </div>
                            </div> 

                            <div class="form-group">
                                <label>{{ __('settings.log_for_roles') }}:</label>
                                <div class="input-group">
                                    <input type="checkbox" name="log_for_roles" id="log_for_roles" {{ $logForRoles }} data-bootstrap-switch>
                                </div>
                            </div> 

                            <div class="form-group">
                                <label>{{ __('settings.log_for_journal') }}:</label>
                                <div class="input-group">
                                    <input type="checkbox" name="log_for_journal" id="log_for_journal" {{ $logForJournal }} data-bootstrap-switch>
                                </div>
                            </div> 

                            <div class="form-group">
                                <label>{{ __('settings.log_for_login') }}:</label>
                                <div class="input-group">
                                    <input type="checkbox" name="log_for_login" id="log_for_login" {{ $logForLogin }} data-bootstrap-switch>
                                </div>
                            </div>     

                            <div class="form-group">
                                <label>{{ __('settings.log_for_department') }}:</label>
                                <div class="input-group">
                                    <input type="checkbox" name="log_for_department" id="log_for_department" {{ $logForDepartment }} data-bootstrap-switch>
                                </div>
                            </div>  
                            
                            <div class="form-group">
                                <label>{{ __('settings.log_for_treatment_part') }}:</label>
                                <div class="input-group">
                                    <input type="checkbox" name="log_for_treatment_part" id="log_for_treatment_part" {{ $logForTreatmentPart }} data-bootstrap-switch>
                                </div>
                            </div>  

                            <div class="form-group">
                                <label>{{ __('settings.log_for_payment_method') }}:</label>
                                <div class="input-group">
                                    <input type="checkbox" name="log_for_payment_method" id="log_for_payment_method" {{ $logForPaymentMethod }} data-bootstrap-switch>
                                </div>
                            </div>

                        </div>
                    </div>
                    @endcan
                    <!-- /.card -->

                </div>    
            </div>        
        </div><!-- /.container-fluid -->
    </section>
</div>
@stop

@section('scripts')
<script>
//----------- Switch initilized ------
$(function () {
    $("input[data-bootstrap-switch]").each(function(){
        $(this).bootstrapSwitch('state', $(this).prop('checked'));
    });
})

//----------- For Log settings ------------
$("#log_for_email").bootstrapSwitch({
  onSwitchChange: function(e, state) {
    statusChange(state,'log_for_email');
  }
});
$("#log_for_sms").bootstrapSwitch({
  onSwitchChange: function(e, state) {
    statusChange(state,'log_for_sms');
  }
});
$("#log_for_booking_related_details").bootstrapSwitch({
  onSwitchChange: function(e, state) {
    statusChange(state,'log_for_booking_related_details');
  }
});
$("#log_for_event_related_details").bootstrapSwitch({
  onSwitchChange: function(e, state) {
    statusChange(state,'log_for_event_related_details');
  }
});
$("#log_for_date_related_details").bootstrapSwitch({
  onSwitchChange: function(e, state) {
    statusChange(state,'log_for_date_related_details');
  }
});
$("#log_for_card_and_clips").bootstrapSwitch({
  onSwitchChange: function(e, state) {
    statusChange(state,'log_for_card_and_clips');
  }
});
$("#log_for_pages").bootstrapSwitch({
  onSwitchChange: function(e, state) {
    statusChange(state,'log_for_pages');
  }
});
$("#log_for_settings").bootstrapSwitch({
  onSwitchChange: function(e, state) {
    statusChange(state,'log_for_settings');
  }
});
$("#log_related_user").bootstrapSwitch({
  onSwitchChange: function(e, state) {
    statusChange(state,'log_related_user');
  }
});
$("#log_for_survey").bootstrapSwitch({
  onSwitchChange: function(e, state) {
    statusChange(state,'log_for_survey');
  }
});
$("#log_for_roles").bootstrapSwitch({
  onSwitchChange: function(e, state) {
    statusChange(state,'log_for_roles');
  }
});
$("#log_for_journal").bootstrapSwitch({
  onSwitchChange: function(e, state) {
    statusChange(state,'log_for_journal');
  }
});
$("#log_for_login").bootstrapSwitch({
  onSwitchChange: function(e, state) {
    statusChange(state,'log_for_login');
  }
});
$("#log_for_department").bootstrapSwitch({
  onSwitchChange: function(e, state) {
    statusChange(state,'log_for_department');
  }
});

$("#log_for_treatment_part").bootstrapSwitch({
  onSwitchChange: function(e, state) {
    statusChange(state,'log_for_treatment_part');
  }
});
$("#log_for_payment_method").bootstrapSwitch({
  onSwitchChange: function(e, state) {
    statusChange(state,'log_for_payment_method');
  }
});


//----------- For switch values -----------
$("#clipboard_treatment").bootstrapSwitch({
  onSwitchChange: function(e, state) {
    statusChange(state,'clipboard_treatment');
  }
});
$("#age_gender_mandatory").bootstrapSwitch({
  onSwitchChange: function(e, state) {
    statusChange(state,'age_gender_mandatory');
  }
});
$("#clipboard_event").bootstrapSwitch({
  onSwitchChange: function(e, state) {
    statusChange(state,'clipboard_event');
  }
});
$("#cpr_emp_fields").bootstrapSwitch({
  onSwitchChange: function(e, state) { 
    statusChange(state,'cpr_emp_fields');
  }
});
$("#cpr_emp_fields_insurance").bootstrapSwitch({
  onSwitchChange: function(e, state) { 
    statusChange(state,'cpr_emp_fields_insurance');
  }
});
$("#mdr_field").bootstrapSwitch({
  onSwitchChange: function(e, state) { 
    statusChange(state,'mdr_field');
  }
});
$("#free_spot_email").bootstrapSwitch({
  onSwitchChange: function(e, state) { 
    statusChange(state,'free_spot_email');
  }
});
$("#free_spot_email_for_event").bootstrapSwitch({
  onSwitchChange: function(e, state) { 
    statusChange(state,'free_spot_email_for_event');
  }
});
$("#department").bootstrapSwitch({
  onSwitchChange: function(e, state) { 
    statusChange(state,'department');
  }
});
$("#code_for_booking").bootstrapSwitch({
  onSwitchChange: function(e, state) { 
    statusChange(state,'code_for_booking');
  }
});
$("#receipt_option").bootstrapSwitch({
  onSwitchChange: function(e, state) { 
    statusChange(state,'receipt_option');
  }
});
$("#survey_setting").bootstrapSwitch({
  onSwitchChange: function(e, state) { 
    statusChange(state,'survey_setting');
  }
});
$("#t_booking_sms").bootstrapSwitch({
  onSwitchChange: function(e, state) { 
    statusChange(state,'t_booking_sms');
  }
});
$("#t_d_booking_sms").bootstrapSwitch({
  onSwitchChange: function(e, state) { 
    statusChange(state,'t_d_booking_sms');
  }
});
$("#t_r_booking_sms").bootstrapSwitch({
  onSwitchChange: function(e, state) { 
    statusChange(state,'t_r_booking_sms');
  }
});
$("#e_booking_sms").bootstrapSwitch({
  onSwitchChange: function(e, state) { 
    statusChange(state,'e_booking_sms');
  }
});
$("#e_d_booking_sms").bootstrapSwitch({
  onSwitchChange: function(e, state) { 
    statusChange(state,'e_d_booking_sms');
  }
});
$("#e_r_booking_sms").bootstrapSwitch({
  onSwitchChange: function(e, state) { 
    statusChange(state,'e_r_booking_sms');
  }
});
$("#clip_add").bootstrapSwitch({
  onSwitchChange: function(e, state) { 
    statusChange(state,'clip_add');
  }
});
$("#clip_used").bootstrapSwitch({
  onSwitchChange: function(e, state) { 
    statusChange(state,'clip_used');
  }
});
$("#clip_restore").bootstrapSwitch({
  onSwitchChange: function(e, state) { 
    statusChange(state,'clip_restore');
  }
});
$("#date_update_email").bootstrapSwitch({
  onSwitchChange: function(e, state) { 
    statusChange(state,'date_update_email');
  }
});
$("#event_update_email").bootstrapSwitch({
  onSwitchChange: function(e, state) { 
    statusChange(state,'event_update_email');
  }
});
$("#offer_section_home").bootstrapSwitch({
  onSwitchChange: function(e, state) { 
    getAndStoreData(jQuery('#offer_url').val());
    statusChange(state,'offer_section_home');
  }
});
$("#mobile_pay").bootstrapSwitch({
  onSwitchChange: function(e, state) { 

    const Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 7000
    });

    if(jQuery('#mobile_pay_number').val()){
        statusChange(state,'mobile_pay');
    }else{
        
        Toast.fire({
            icon: 'error',
            title: ' {{ __('settings.pptmpnftycots') }}'
        });
        jQuery('#mobile_pay').trigger('click');
    }  
  }
});
// $("#mobile_pay").bootstrapSwitch({
//   onSwitchChange: function(e, state) { 
    
//     if(state){

//         const Toast = Swal.mixin({
//             toast: true,
//             position: 'top-end',
//             showConfirmButton: false,
//             timer: 7000
//         });

//         //----- Send ajax call to check if any treatment without price exist or not ----//
//         var token = $('meta[name="csrf-token"]').attr('content');
//         $.ajax({
//             type: 'POST',
//             url: '/check-free-treatments',
//             data: { '_token':token },
//             dataType: 'json',
//             success: function (data) {
//                 if(data == 0){
//                     if(jQuery('#mobile_pay_number').val()){
//                         statusChange(state,'mobile_pay');
//                     }else{
                        
//                         Toast.fire({
//                             icon: 'error',
//                             title: ' {{ __('settings.pptmpnftycots') }}'
//                         });
//                         jQuery('#mobile_pay').trigger('click');
//                     }
//                 }else{
//                     Toast.fire({
//                         icon: 'error',
//                         title: ' {{ __('settings.taftthnpm') }}'
//                     });
//                     jQuery('#mobile_pay').trigger('click');
//                 }
//             }
//         });
//     }
//     else{
//         statusChange(state,'mobile_pay');
//     }
//   }
// });



//Timepicker
$('#timepicker, #timepicker2, #timepicker3, #timepicker4').datetimepicker({
    format: 'HH:mm'
})

//----------------- Function to get field name and value ----------
function getVal(obj){

    if(jQuery(obj).attr('id') == 'offer_url'){
        jQuery('#offer_section_home').trigger('click');
    }

    var key = jQuery(obj).attr('id');
    var value = jQuery(obj).val();
    statusChange(value,key);
}

//---------------- Ajax call to change value ----------
@can('Settings Edit')

function getAndStoreData(url){

    var token = $('meta[name="csrf-token"]').attr('content');
    $.ajax({
    type: 'POST',
    url: '/fetch-offer-data',
    data: { 'url':url,'_token':token },
    dataType: 'json',
    success: function (data) {
        console.log(data);
    },
    error: function (data) {
        console.log(data);
    }
    });
}

function statusChange(value,key){
    const Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000
    });

    var token = $('meta[name="csrf-token"]').attr('content');

    $.ajax({
      type: 'POST',
      url: '/updateSetting',
      data: { 'value':value,'key':key,'_token':token },
      dataType: 'json',
      success: function (data) {
        if(data == 'success'){
          Toast.fire({
              icon: 'success',
              title: ' {{ __('settings.dhbus') }}'
          });
        }
        else{
          Toast.fire({
              icon: 'error',
              title: ' {{ __('settings.tiauetus') }}'
          })
        }
      },
      error: function (data) {
        Toast.fire({
              icon: 'error',
              title: ' {{ __('settings.pcydatta') }}'
          })
      }
    });

}
@endcan

</script>
@stop