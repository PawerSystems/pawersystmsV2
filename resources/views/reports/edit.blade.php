@extends("layouts.backend")

@section('content')
<div class="content-wrapper">
    
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>{{ __('reports.edit_schedule_report') }}</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="/">{{ __('keywords.home') }}</a></li>
              <li class="breadcrumb-item active">{{ __('reports.edit_schedule_report') }}</li>
            </ol>
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>

    <div class="col-md-6" style="margin:auto; padding-top:30px;">
        <div class="card">
            <div class="card-body register-card-body">
                <p class="login-box-msg">{{ __('reports.edit_schedule_report') }}</p>

                <form action="{{ Route('schedule-report-edit',session('business_name')) }}" method="post" id="saveBusiness" enctype="multipart/form-data">                    
                <input type="hidden" name="id" value="{{ $report->id }}">
                    @csrf
                    <label>{{ __('reports.duration') }}:</label>
                    <div class="input-group mb-3">
                        <select class="form-control" name="duration" id="duration">
                            <x-report-duration-list selected="{{ $report->duration }}" />
                        </select>
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-clock"></span>
                            </div>
                        </div>
                    @if ($errors->has('duration'))
                        <span class="text-danger">{{ $errors->first('duration') }}</span>
                    @endif
                    </div>

                    <label>{{__('reports.schedule_period')}}:</label>
                    <div class="input-group mb-3">
                        <select class="form-control" name="period" id="period">
                            <option value="daily" {{ $report->period == 'daily' ? 'selected' : '' }}>{{__('reports.daily')}}</option>
                            <option value="1" {{ $report->period == '1' ? 'selected' : '' }}>{{__('reports.start_of_month')}}</option>
                            <option value="15" {{ $report->period == '15' ? 'selected' : '' }}>{{__('reports.every_15th')}}</option>
                            <option value="end" {{ $report->period == 'end' ? 'selected' : '' }}>{{__('reports.end_of_month')}}</option>
                        </select>
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-caledar"></span>
                            </div>
                        </div>
                    @if ($errors->has('period'))
                        <span class="text-danger">{{ $errors->first('period') }}</span>
                    @endif
                    </div>

                    <div class="form-group mb-3">
                        <label>{{__('reports.schedule_time')}}:</label>
                        <div class="input-group" id="reservationtime" data-target-input="nearest">
                            <input type="text" name="time" class="form-control datetimepicker-input" data-target="#reservationtime" value="{{ $report->time }}"/>  
                            <div class="input-group-append" data-target="#reservationtime" data-toggle="datetimepicker">
                                <div class="input-group-text"><i class="far fa-clock"></i></div>
                            </div>              
                        </div>
                        @if ($errors->has('time'))
                            <span class="text-danger">{{ $errors->first('time') }}</span>
                        @endif
                    </div>

                    <label>{{__('reports.send_to')}}:</label>
                    <div class="input-group mb-3">
                        @php 
                            $users = explode(',',$report->users); 
                        @endphp
                        <select class="form-control select2" name="users[]" id="users" multiple="multiple" style="width:100% !important">
                            @foreach ($admins as $admin)
                                @php $class = ''; @endphp
                                    @php if(in_array($admin->id,$users)) $class="selected";  @endphp
                                @endphp    
                                <option value="{{ $admin->id }}" {{ $class }}>{{ $admin->name }}</option>
                            @endforeach
                        </select>
                    @if ($errors->has('users'))
                        <span class="text-danger">{{ $errors->first('users') }}</span>
                    @endif
                    </div>

                    <div class="form-group">
                        <div class="custom-control custom-checkbox">
                        <input type="checkbox" name="status" class="custom-control-input" id="exampleCheck1" {{ $report->is_active ? 'checked' : '' }}>
                        <label class="custom-control-label" for="exampleCheck1">{{ __('card.active') }}</label>
                        </div>
                    </div>

                    <div class="col-4">
                        <button type="submit" class="btn btn-primary btn-block">{{ __('keywords.save') }}</button>
                    </div>
                    <!-- /.col -->
                 
                </form>
            </div>
        </div> 
    </div>
</div>
@stop

@section('scripts')


<!-- Showing error or success messages -->
@if(Session::get('success'))
<script type="text/javascript">
  jQuery(function() {
		const Toast = Swal.mixin({
		  toast: true,
		  position: 'top-end',
		  showConfirmButton: false,
		  timer: 3000
		});
        Toast.fire({
            icon: 'success',
            title: '{{ Session::get("success") }}'
      })
  }); 
</script> 
@elseif( Session::get('error') )
<script type="text/javascript">
  jQuery(function() {
		const Toast = Swal.mixin({
		  toast: true,
		  position: 'top-end',
		  showConfirmButton: false,
		  timer: 3000
		});
        Toast.fire({
            icon: 'error',
            title: '{{ Session::get("error") }}'
        })
  }); 
</script> 
@endif


<script type="text/javascript">


//Initialize Select2 Elements
$('.select2').select2();

</script>
@stop