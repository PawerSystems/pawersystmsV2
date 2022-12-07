@foreach($settings as $setting)
    @if($setting->key == 'date_format')
        @php $dateFormat = $setting->value; @endphp
        @php $month_ini = new DateTime("first day of this month"); @endphp
        @php $month_end = new DateTime("last day of this month"); @endphp
    @endif  
@endforeach      

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
            <h1>{{ __('reports.system_reports') }}</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="/">{{ __('keywords.home') }}</a></li>
              <li class="breadcrumb-item active">{{ __('reports.system_reports') }}</li>
            </ol>
          </div>
          <div class="col-sm-12">
            <a class="btn btn-info float-right" href="{{ Route('scheduled-reports',session('business_name')) }}">{{ __('reports.scheduled_report') }}</a>
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>
    
    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-6">
                @can('Reports Users View')
                    <!----- USer Report ----->
                    <div class="card card-secondary">
                        <div class="card-header">
                            <h3 class="card-title">{{ __('reports.users_report') }} <i>({{ __('reports.droabits') }})</i></h3>
                        </div>
                        <div class="card-body">
                        <form id="usersBookingReportForm" action="{{ Route('getReport',session('business_name')) }}" method="POST">
                            @csrf
                            <input type="hidden" name="report_for" value="usersBookingReportForm">
                            <!-- Date and time range -->
                            <div class="form-group">
                                <label>{{ __('reports.date_from_to_till') }}:</label>
                                <div class="input-group">
                                    <div class="input-group-append" data-target="#date1" data-toggle="datetimepicker">
                                        <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                    </div>
                                    <input type="text" name="from" class="form-control float-right reservationtime" id="date1" value="{{ $month_ini->format($dateFormat) }}" readonly="readonly">
                                    <input type="hidden" name="_from" id="_date1" value="{{ $month_ini->format('Y-m-d') }}">
                                    <div class="input-group-append ml-2" data-target="#date2" data-toggle="datetimepicker">
                                        <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                    </div>
                                    <input type="text" name="to" class="form-control float-right reservationtime" id="date2" value="{{ $month_end->format($dateFormat) }}" readonly="readonly">
                                    <input type="hidden" name="_to" id="_date2" value="{{ $month_end->format('Y-m-d') }}">
                                </div>
                            </div>
                            <div class="form-group">
                            <label>{{ __('reports.file_type') }}:</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-paperclip"></i></span>
                                    </div>
                                    <select class="form-control" name="type">
                                        <option value="ExcelReport">Excel</option>
                                        <option value="PdfReport">Pdf</option>
                                        <option value="CSVReport">CSV</option>
                                    </select>
                                </div>
                            </div>
                            <input type="hidden" name="sort_by" value="created_at">
                            <div class="form-group">
                                <button type="submit" class="btn btn-primary btn-md">{{ __('reports.download') }}</button>
                                    <button type="button" class="btn btn-secondary btn-md schedule-btn" data-toggle="modal" data-target="#modal-lg">
                                        {{ __('reports.schedule') }}
                                    </button>
                            </div>
                        </form>
                        </div>
                    </div>
                    <!----- USer Report ----->
                    {{-- <div class="card card-secondary">
                        <div class="card-header">
                            <h3 class="card-title">{{ __('reports.users_report') }} <i>({{ __('reports.roauits') }})</i></h3>
                        </div>
                        <div class="card-body">
                        <form id="usersReportForm" action="{{ Route('getReport',session('business_name')) }}" method="POST">
                            @csrf
                            <input type="hidden" name="report_for" value="userReport">
                            <!-- Date and time range -->
                            <div class="form-group">
                            <label>{{ __('reports.date_from_to_till') }}:</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="far fa-clock"></i></span>
                                    </div>
                                    <input type="text" class="form-control float-right reservationtime" name="date_range" >
                                </div>
                            </div>
                            <div class="form-group">
                            <label>{{ __('reports.file_type') }}:</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-paperclip"></i></span>
                                    </div>
                                    <select class="form-control" name="type">
                                        <option value="PdfReport">Pdf</option>
                                        <option value="CSVReport">CSV</option>
                                        <option value="ExcelReport">Excel</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                            <label>{{ __('reports.sort_by') }}:</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-sort-amount-down-alt"></i></span>
                                    </div>
                                    <select class="form-control" name="sort_by">
                                        <option value="role">{{ __('reports.role') }}</option>
                                        <option value="name">{{ __('reports.name') }}</option>
                                        <option value="is_subscribe">{{ __('reports.subscription') }}</option>
                                        @if(Auth::user()->role == 'Owner')
                                            <option value="business_id">{{ __('reports.business_id') }}</option>
                                        @endif 
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <button type="submit" class="btn btn-primary btn-md">{{ __('reports.download') }}</button>
                                    <button type="button" class="btn btn-secondary btn-md schedule-btn" data-toggle="modal" data-target="#modal-lg">
                                        {{ __('reports.schedule') }}
                                    </button>
                            </div>
                        </form>
                        </div>
                    </div> --}}
                @endcan 
                @if( Auth::user()->role == 'Owner' )
                    <!----- Date Report ----->
                    <div class="card card-secondary">
                        <div class="card-header">
                            <h3 class="card-title">{{ __('reports.sms_report') }}</h3>
                        </div>
                        <div class="card-body">
                        <form id="smsReportForm" action="{{ Route('getReport',session('business_name')) }}" method="POST">
                            @csrf
                            <input type="hidden" name="report_for" value="smsReport">
                            <input type="hidden" name="sort_by" value="business_id">
                            <!-- Date and time range -->
                            <div class="form-group">
                            <label>{{ __('reports.date_from_to_till') }}:</label>
                            <div class="input-group">
                                <div class="input-group-append" data-target="#date33" data-toggle="datetimepicker">
                                    <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                </div>
                                <input type="text" name="from" class="form-control float-right reservationtime" id="date33" value="{{ $month_ini->format($dateFormat) }}" readonly="readonly">
                                <input type="hidden" name="_from" id="_date33" value="{{ $month_ini->format('Y-m-d') }}">

                                <div class="input-group-append ml-2" data-target="#date44" data-toggle="datetimepicker">
                                    <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                </div>
                                <input type="text" name="to" class="form-control float-right reservationtime" id="date44" value="{{ $month_end->format($dateFormat) }}" readonly="readonly">
                                <input type="hidden" name="_to" id="_date44" value="{{ $month_end->format('Y-m-d') }}">
                                </div>
                            </div>
                            <div class="form-group">
                            <label>{{ __('reports.file_type') }}:</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-paperclip"></i></span>
                                    </div>
                                    <select class="form-control" name="type">
                                        <option value="ExcelReport">Excel</option>
                                        <option value="PdfReport">Pdf</option>
                                        <option value="CSVReport">CSV</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <button type="submit" class="btn btn-primary btn-md">{{ __('reports.download') }}</button>
                            </div>
                        </form>
                        </div>
                    </div>
                @endif   

                @can('Reports Date View')
                    <!----- Date Report ----->
                    <div class="card card-secondary">
                        <div class="card-header">
                            <h3 class="card-title">{{ __('reports.date_report') }} <i>({{ __('reports.open_and_book_time') }})</i></h3>
                        </div>
                        <div class="card-body">
                        <form id="usersReportForm" action="{{ Route('getReport',session('business_name')) }}" method="POST">
                            @csrf
                            <input type="hidden" name="report_for" value="dateReport">
                            <input type="hidden" name="sort_by" value="dates.id">
                            <!-- Date and time range -->
                            <div class="form-group">
                            <label>{{ __('reports.date_from_to_till') }}:</label>
                            <div class="input-group">
                                <div class="input-group-append" data-target="#date3" data-toggle="datetimepicker">
                                    <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                </div>
                                <input type="text" name="from" class="form-control float-right reservationtime" id="date3" value="{{ $month_ini->format($dateFormat) }}"  readonly="readonly">
                                <input type="hidden" name="_from" id="_date3" value="{{ $month_ini->format('Y-m-d') }}">

                                <div class="input-group-append ml-2" data-target="#date4" data-toggle="datetimepicker">
                                    <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                </div>
                                <input type="text" name="to" class="form-control float-right reservationtime" id="date4" value="{{ $month_end->format($dateFormat) }}" readonly="readonly">
                                <input type="hidden" name="_to" id="_date4" value="{{ $month_end->format('Y-m-d') }}">
                                </div>
                            </div>
                            <div class="form-group">
                            <label>{{ __('reports.file_type') }}:</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-paperclip"></i></span>
                                    </div>
                                    <select class="form-control" name="type">
                                        <option value="ExcelReport">Excel</option>
                                        <option value="PdfReport">Pdf</option>
                                        <option value="CSVReport">CSV</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <button type="submit" class="btn btn-primary btn-md">{{ __('reports.download') }}</button>
                                    <button type="button" class="btn btn-secondary btn-md schedule-btn" data-toggle="modal" data-target="#modal-lg">
                                        {{ __('reports.schedule') }}
                                    </button>
                            </div>
                        </form>
                        </div>
                    </div>
                @endcan    
                    @if( Auth::user()->role != 'Owner' )
                @can('Reports Srvey View')
                     <!----- Survey Report ----->
                     <div class="card card-secondary">
                        <div class="card-header">
                            <h3 class="card-title">{{ __('reports.survey_report') }} <i>({{ __('reports.rssagisp') }} )</i></h3>
                        </div>
                        <div class="card-body">
                        <form id="usersReportForm" action="{{ Route('getReport',session('business_name')) }}" method="POST">
                            @csrf
                            <input type="hidden" name="report_for" value="suveyReport">
                            <input type="hidden" name="sort_by" value="surveys.id">
                            <!-- Date and time range -->
                            <div class="form-group">
                                <label>{{ __('reports.date_from_to_till') }}:</label>
                                <div class="input-group">
                                    <div class="input-group-append" data-target="#date5" data-toggle="datetimepicker">
                                        <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                    </div>
                                    <input type="text" name="from" class="form-control float-right reservationtime" id="date5" value="{{ $month_ini->format($dateFormat) }}" readonly="readonly">
                                    <input type="hidden" name="_from" id="_date5" value="{{ $month_ini->format('Y-m-d') }}">

                                    <div class="input-group-append ml-2" data-target="#date6" data-toggle="datetimepicker">
                                        <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                    </div>
                                    <input type="text" name="to" class="form-control float-right reservationtime" id="date6" value="{{ $month_end->format($dateFormat) }}" readonly="readonly">
                                    <input type="hidden" name="_to" id="_date6" value="{{ $month_end->format('Y-m-d') }}">
                                </div>
                            </div>
                            <div class="form-group">
                            <label>{{ __('reports.file_type') }}:</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-paperclip"></i></span>
                                    </div>
                                    <select class="form-control" name="type">
                                        <!-- <option value="PdfReport">Pdf</option> -->
                                        <option value="ExcelReport">Excel</option>
                                        <option value="CSVReport">CSV</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <button type="submit" class="btn btn-primary btn-md">{{ __('reports.download') }}</button>
                                    <button type="button" class="btn btn-secondary btn-md schedule-btn" data-toggle="modal" data-target="#modal-lg">
                                        {{ __('reports.schedule') }}
                                    </button>
                            </div>
                        </form>
                        </div>
                    </div>
                @endcan    
                    @endif

                </div>  <!-- End col-md-6 -->

                <div class="col-md-6">  
                @can('Reports Booking View')
                    <!----- Booking Report ----->
                    <div class="card card-secondary">
                        <div class="card-header">
                            <h3 class="card-title">{{ __('reports.booking_report') }} <i>({{ __('reports.cbo') }})</i></h3>
                        </div>
                        <div class="card-body">
                            <form id="usersReportForm" action="{{ Route('getReport',session('business_name')) }}" method="POST">
                                @csrf
                                <input type="hidden" name="report_for" value="bookingReport">
                                <!-- Date and time range -->
                                <div class="form-group">
                                    <label>{{ __('reports.date_from_to_till') }}:</label>
                                    <div class="input-group">
                                        <div class="input-group-append" data-target="#date7" data-toggle="datetimepicker">
                                            <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                        </div>
                                        <input type="text" name="from" class="form-control float-right reservationtime" id="date7" value="{{ $month_ini->format($dateFormat) }}" readonly="readonly">
                                        <input type="hidden" name="_from" id="_date7" value="{{ $month_ini->format('Y-m-d') }}">

                                        <div class="input-group-append ml-2" data-target="#date8" data-toggle="datetimepicker">
                                            <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                        </div>
                                        <input type="text" name="to" class="form-control float-right reservationtime" id="date8" value="{{ $month_end->format($dateFormat) }}" readonly="readonly">
                                        <input type="hidden" name="_to" id="_date8" value="{{ $month_end->format('Y-m-d') }}">
                                    </div>
                                </div>
                                <div class="form-group">
                                <label>{{ __('reports.file_type') }}:</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-paperclip"></i></span>
                                        </div>
                                        <select class="form-control" name="type">
                                            <option value="ExcelReport">Excel</option>
                                            <option value="PdfReport">Pdf</option>
                                            <option value="CSVReport">CSV</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                <label>{{ __('reports.sort_by') }}:</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-sort-amount-down-alt"></i></span>
                                        </div>
                                        <select class="form-control" name="sort_by">
                                            <option value="treatment_id">{{ __('reports.treatment') }}</option>
                                            <option value="treatment_date">{{ __('reports.treatment_date') }}</option>
                                            <option value="created_at">{{ __('reports.book_time') }}</option>
                                            @if(Auth::user()->role == 'Owner')
                                                <option value="business_ID">{{ __('reports.business_id') }}</option>
                                            @endif 
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <button type="submit" class="btn btn-primary btn-md">{{ __('reports.download') }}</button>
                                    <button type="button" class="btn btn-secondary btn-md schedule-btn" data-toggle="modal" data-target="#modal-lg">
                                        {{ __('reports.schedule') }}
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                    <!-- /.card -->
                @endcan

                @can('Reports Unique User View')
                    <!----- Unique User Report ----->
                    <div class="card card-secondary">
                        <div class="card-header">
                            <h3 class="card-title">{{ __('reports.unique_user_report') }} <i>({{ __('reports.rsuuatnotisp') }})</i></h3>
                        </div>
                        <div class="card-body">
                            <form id="usersReportForm" action="{{ Route('getReport',session('business_name')) }}" method="POST">
                                @csrf
                                <input type="hidden" name="report_for" value="uniqueUserReport">
                                <!-- Date and time range -->
                                <div class="form-group">
                                    <label>{{ __('reports.date_from_to_till') }}:</label>
                                    <div class="input-group">
                                        <div class="input-group-append" data-target="#date9" data-toggle="datetimepicker">
                                            <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                        </div>
                                        <input type="text" name="from" class="form-control float-right reservationtime" id="date9" value="{{ $month_ini->format($dateFormat) }}" readonly="readonly">
                                        <input type="hidden" name="_from" id="_date9" value="{{ $month_ini->format('Y-m-d') }}">

                                        <div class="input-group-append ml-2" data-target="#date10" data-toggle="datetimepicker">
                                            <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                        </div>
                                        <input type="text" name="to" class="form-control float-right reservationtime" id="date10" value="{{ $month_end->format($dateFormat) }}" readonly="readonly">
                                        <input type="hidden" name="_to" id="_date10" value="{{ $month_end->format('Y-m-d') }}">
                                    </div>
                                </div>
                                <div class="form-group">
                                <label>{{ __('reports.file_type') }}:</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-paperclip"></i></span>
                                        </div>
                                        <select class="form-control" name="type">
                                            <option value="ExcelReport">Excel</option>
                                            <option value="PdfReport">Pdf</option>
                                            <option value="CSVReport">CSV</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                <label>{{ __('reports.sort_by') }}:</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-sort-amount-down-alt"></i></span>
                                        </div>
                                        <select class="form-control" name="sort_by">
                                            <option value="uname">{{ __('reports.name') }}</option>
                                            <option value="booked">{{ __('reports.bookings') }}</option>
                                            <option value="uemail">{{ __('reports.email') }}</option>
                                            @if(Auth::user()->role == 'Owner')
                                                <option value="business_ID">{{ __('reports.business_id') }}</option>
                                            @endif 
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <button type="submit" class="btn btn-primary btn-md">{{ __('reports.download') }}</button>
                                    <button type="button" class="btn btn-secondary btn-md schedule-btn" data-toggle="modal" data-target="#modal-lg">
                                        {{ __('reports.schedule') }}
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                    <!-- /.card -->
                @endcan
                </div>    
            </div>        
        </div><!-- /.container-fluid -->
    </section>
</div>

<!--------------------------- Schedule report modal ----------------------->
<div class="modal fade" id="modal-lg" style="display: none;" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title"></h4>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">×</span>
          </button>
        </div>
        <form name="schedule-report-form" id="schedule-report-form" onsubmit="return scheduleReport(this)">
            @csrf
            <input type="hidden" id="report_for" name="report_for" value="">

        <div class="modal-body">
            
            <!-- Date and time range -->
            <div class="form-group">
            <label>{{ __('reports.duration') }}:</label>
                <div class="input-group">
                    <div class="input-group-prepend">
                    <span class="input-group-text"><i class="far fa-clock"></i></span>
                    </div>
                    <select class="form-control" name="duration" id="duration">
                       <x-report-duration-list selected='' />
                    </select>
                </div>
            </div>

            <!-- Date and time range -->
            <div class="form-group">
                <label>{{__('reports.schedule_period')}}:</label>
                <div class="input-group">
                    <div class="input-group-prepend">
                    <span class="input-group-text"><i class="fa fa-clock"></i></span>
                    </div>
                    <select class="form-control" name="period" id="period">
                        <option value="daily">{{__('reports.daily')}}</option>
                        <option value="1">{{__('reports.start_of_month')}}</option>
                        <option value="15">{{__('reports.every_15th')}}</option>
                        <option value="end">{{__('reports.end_of_month')}}</option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label>{{__('reports.schedule_time')}}:</label>
                <div class="input-group" id="reservationtime" data-target-input="nearest">
                    <div class="input-group-append" data-target="#reservationtime" data-toggle="datetimepicker" readonly="readonly">
                        <div class="input-group-text"><i class="far fa-clock"></i></div>
                    </div>
                    <input type="text" name="time" class="form-control datetimepicker-input" data-target="#reservationtime" value="{{ date('H:i') }}" readonly="readonly"/>                
                </div>
                @if ($errors->has('time'))
                    <span class="text-danger">{{ $errors->first('time') }}</span>
                @endif
            </div>

             <!-- Date and time range -->
             <div class="form-group">
                <label>{{__('reports.send_to')}}:</label>
                <div class="input-group">
                    <select class="form-control select2" name="users[]" id="users" multiple="multiple" style="width:100% !important">
                        @foreach ($admins as $admin)
                            <option value="{{ $admin->id }}">{{ $admin->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

        </div>
        <div class="modal-footer justify-content-between">
          <button type="button" class="btn btn-default" data-dismiss="modal">{{ __('reports.cancel') }}</button>
          <button type="submit" class="btn btn-primary">{{ __('reports.schedule') }}</button>
        </div>
        </form>
      </div>
      <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
  </div>

@stop

@section('scripts')

<script>

$('.schedule-btn').click(function(){
    var reportfor = jQuery(this).closest('form').find('input[name="report_for"]').val();
    var title = jQuery(this).closest('.card').find('.card-title').html();
    
    jQuery('.modal-title').html("{{ __('reports.schedule_report') }} - "+title);
    jQuery('#schedule-report-form #report_for').val(reportfor);
});

function scheduleReport(obj){

    const Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000
    });

    $.ajax({
      type: 'POST',
      url: '/schedule-report',
      data: jQuery(obj).serialize(),
      dataType: 'json',
      success: function (data) {
        if(data == 'success'){
          Toast.fire({
              icon: 'success',
              title: ' {{ __('reports.report_has_been_scheduled') }}'
          });
        }
        else{
          Toast.fire({
              icon: 'error',
              title: ' {{ __('reports.can_not_schedule_report') }}'
          })
        }
      },
      error: function (data) {
        Toast.fire({
              icon: 'error',
              title: ' {{ __('reports.error_to_schedule_report') }}'
          })
      }
    });

    return false;
}

//---------------- Ajax call to change value ----------
function getReport(obj){

    event.preventDefault();

    const Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000
    });

    $.ajax({
      type: 'POST',
      url: '/getReport',
      data: jQuery(obj).serialize(),
      dataType: 'json',
      success: function (data) {
        if(data == 'success'){
          Toast.fire({
              icon: 'success',
              title: ' {{ __('reports.dhbus') }}'
          });
        }
        else{
          Toast.fire({
              icon: 'error',
              title: ' {{ __('reports.tiauetus') }}'
          })
        }
      },
      error: function (data) {
        Toast.fire({
              icon: 'error',
              title: ' {{ __('reports.pcydatta') }}'
          })
      }
    });

}



$(document).ready(function () {
  $.validator.setDefaults({
    submitHandler: function () {
        return true;
    }
  });
  $('#schedule-report-form').validate({
    rules: {
        users: {
            required: true
        },
    },
    messages: {
      users: {
        required: "Please select atleast one user.",
      },
    },
    errorElement: 'span',
    errorPlacement: function (error, element) {
      error.addClass('invalid-feedback');
      element.closest('.input-group').append(error);
    },
    highlight: function (element, errorClass, validClass) {
      $(element).addClass('is-invalid');
    },
    unhighlight: function (element, errorClass, validClass) {
      $(element).removeClass('is-invalid');
    }
  });
});
</script>
@stop