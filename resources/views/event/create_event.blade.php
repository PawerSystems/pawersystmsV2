@extends("layouts.backend")

@section('content')
<style>
    .max_guests{ display:none; }
</style>
<div class="content-wrapper"> 

    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>{{ __('event.create_event') }}</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="{{ Route('dashboard',session('business_name')) }}">{{ __('keywords.home') }}</a></li>
              <li class="breadcrumb-item active">{{ __('event.create_event') }}</li>
            </ol>
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>

    <div class=" row"> 
        <div class="col-sm-3"></div>  
        <div class="col-md-6 col-sm-12">
        <form action="{{ Route('saveEvent',session('business_name')) }}" method="post" id="saveEvent">
                    @csrf
            <div class="card card-info">
                    <div class="card-header">
                        <h3 class="card-title">{{ __('event.create_event') }}</h3>
                    </div>
                    
                    <div class="card-body">
                        <!-- Name -->
                        <div class="input-group mb-3">
                            <label style="width: 100%;">{{ __('keywords.name') }}:</label>
                            <div class="input-group-append">
                                <div class="input-group-text">
                                    <span class="fas fa-tags"></span>
                                </div>
                            </div>
                            <input type="text" class="form-control" name="name" id="name" >
                            @if ($errors->has('name'))
                                <span class="text-danger">{{ $errors->first('name') }}</span>
                            @endif
                        </div> 
                        <!-- Date -->
                        <div class="form-group">
                            <label>{{ __('event.date') }}:</label>
                            <div class="input-group">
                                <div class="input-group-append" data-target="#date" data-toggle="datetimepicker">
                                    <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                </div>
                                <input type="text" name="date" class="form-control float-right" id="date" onchange="getTherapists()" readonly="readonly">
                                <input type="hidden" name="_date"  id="_date">
                            </div>
                        </div>
                        <!-- Time -->
                        <div class="input-group mb-3">
                            <label style="width: 100%;">{{ __('event.time') }}:</label>
                            <div class="input-group-append">
                                <div class="input-group-text">
                                    <span class="fas fa-clock"></span>
                                </div>
                            </div>
                            <select class="form-control select2" name="time" id="time" onchange="getTherapists()">
                                <x-time-range selected="9:00" :booked="[]" />
                            </select> 
                            @if ($errors->has('time'))
                                <span class="text-danger">{{ $errors->first('time') }}</span>
                            @endif
                        </div> 
                        <!-- Clips -->
                        @if($settings->value == 'true')
                            <div class="input-group mb-3">
                                <label style="width: 100%;">{{ __('event.clips') }}:</label>
                                <div class="input-group-append">
                                    <div class="input-group-text">
                                        <i class="fas fa-tags"></i>
                                    </div>
                                </div>
                                <input type="text" name="clips" value="1" class="form-control" placeholder="{{ __('event.clips') }}" >
                                
                            @if ($errors->has('clips'))
                                <span class="text-danger">{{ $errors->first('clips') }}</span>
                            @endif
                            </div>
                        @else
                            <input type="hidden" name="clips" value="1" >
                        @endif    
                        <!-- Pice -->
                        <div class="input-group mb-3">
                            <label style="width: 100%;">{{ __('event.price') }}:</label>
                            <div class="input-group-append">
                                <div class="input-group-text">
                                    <i class="fas fa-money-bill-wave"></i>
                                </div>
                            </div>
                            <input type="text" name="price" value="{{ old('price') }}" class="form-control" placeholder="{{ __('event.price') }}">
                        @if ($errors->has('price'))
                            <span class="text-danger">{{ $errors->first('price') }}</span>
                        @endif
                        </div> 
                        <!-- Duration -->
                        <div class="input-group mb-3">
                            <label style="width: 100%;">{{ __('event.duration') }}:</label>
                            <div class="input-group-append">
                                <div class="input-group-text">
                                    <span class="fas fa-clock"></span>
                                </div>
                            </div>
                            <input type="number" class="form-control" name="duration" id="duration" value="60">
                            @if ($errors->has('duration'))
                                <span class="text-danger">{{ $errors->first('duration') }}</span>
                            @endif
                        </div> 
                        <!-- Slots -->
                        <div class="input-group mb-3">
                            <label style="width: 100%;">{{ __('event.slots') }}:</label>
                            <div class="input-group-append">
                                <div class="input-group-text">
                                    <span class="fas fa-user"></span>
                                </div>
                            </div>
                            <input type="number" class="form-control" name="slots" id="slots" placeholder="{{ __('event.default_1') }}" min='1' value="20">
                            @if ($errors->has('slots'))
                                <span class="text-danger">{{ $errors->first('slots') }}</span>
                            @endif
                        </div> 
                        <!-- Tharapist -->
                        <div class="input-group mb-3">
                            <label style="width: 100%;">{{ __('event.therapist') }}:</label>
                            <div class="input-group-append">
                                <div class="input-group-text">
                                    <span class="fas fa-user"></span>
                                </div>
                            </div>
                            <select class="form-control select2" name="tharapist" id="tharapist" ></select> 
                            @if ($errors->has('tharapist'))
                                <span class="text-danger">{{ $errors->first('tharapist') }}</span>
                            @endif
                        </div>

                        <!-- Recurring -->
                        <div class="input-group mb-3">
                            <label style="width: 100%;">{{ __('event.recurrence') }}:</label>
                            <div class="input-group-append">
                                <div class="input-group-text">
                                    <span class="fas fa-history"></span>
                                </div>
                            </div>
                            <select class="form-control select2" name="recurrence"  id="recurrence">
                                <option value="d">{{ __('keywords.daily') }}</option>
                                <option value="w">{{ __('keywords.weekly') }}</option>
                                <option value="biw">{{ __('keywords.bi_weekly') }}</option>
                                <option value="m">{{ __('keywords.monthly') }}</option>
                                <option value="dd">{{ __('keywords.all_week_days') }}</option>
                            </select> 
                        </div>
                        <div class="input-group mb-3">
                            <label style="width: 100%;">{{ __('event.recurring_number') }}:</label>
                            <div class="input-group-append">
                                <div class="input-group-text">
                                <i class="fab fa-digital-ocean"></i>
                                </div>
                            </div>
                            <input type="number" min="1" class="form-control" name="recurring_num"  id="recurring_num" value="1">
                            @if ($errors->has('recurring_num'))
                                <span class="text-danger">{{ $errors->first('recurring_num') }}</span>
                            @endif
                        </div>

                        <div class="input-group mb-3">
                            <label style="width: 100%;">{{ __('event.min_bookings') }}:</label>
                            <div class="input-group-append">
                                <div class="input-group-text">
                                <i class="fas fa-users"></i>
                                </div>
                            </div>
                            <input type="number" min="0" class="form-control" name="min_bookings"  id="min_bookings" value="0">
                            @if ($errors->has('min_bookings'))
                                <span class="text-danger">{{ $errors->first('min_bookings') }}</span>
                            @endif
                        </div>

                        <!-- Description-->
                        <div class="form-group">
                            <label>{{ __('event.description') }}:</label>
                            <textarea class="form-control" name="desc" id="desc"></textarea>                     
                        </div> 

                        <!-- Guests -->
                        <div class="form-group">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" name="guest" id="guest_check" class="custom-control-input">
                                <label class="custom-control-label" for="guest_check">{{ __('event.guest_allow') }}</label>
                            </div>
                        </div>

                        <div class="input-group mb-3 max_guests">
                            <label style="width: 100%;">{{ __('event.max_guests') }}:</label>
                            <div class="input-group-append">
                                <div class="input-group-text">
                                <i class="fas fa-users"></i>
                                </div>
                            </div>
                            <input type="number" min="1" class="form-control" name="max_guests"  id="max_guests" value="1">
                            @if ($errors->has('max_guests'))
                                <span class="text-danger">{{ $errors->first('max_guests') }}</span>
                            @endif
                        </div>
                    </div>
                    <div class="card-footer">
                    <button type="submit" class="btn btn-info">{{ __('keywords.save') }}</button>
                    </div>
                </form>
            </div>
            <!-- /.card -->
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
$(document).ready(function () {
  $.validator.setDefaults({
    submitHandler: function () {
        return true;
    }
  });
  $('#saveEvent').validate({
    rules: {
        date: {
            required: true
        },
        time: {
            required: true
        },
        name:{
            required: true
        },
        recurring_num:{
            required: true
        },
        duration: {
            required: true
        },
        slots: {
            required: true
        },
        clips: {
            required: true
        },
        tharapist: {
            required: true
        }
    },
    messages: {
        name: {
            required: "{{ __('event.pgantte') }}"
        },
        date: {
            required: "{{ __('event.psdf') }}"
        },
        recurring_num:{
            required: "{{ __('event.ppan') }}"
        },
        time:{
            required: "{{ __('event.psat') }}"
        },
        duration: {
            required: "{{ __('event.pptdote') }}"
        },
        slots: {
            required: "{{ __('event.pghmswbfte') }}"
        },
        clips: {
            required: "{{ __('event.hmcbufte') }}"
        },
        tharapist: {
            required: "{{ __('event.psat') }}"
        }
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

jQuery('#recurrence').on('change',function(){
    if( jQuery(this).val() == 'dd' ){
        jQuery('#recurring_num').val(1);
        jQuery('#recurring_num').attr('readonly','true');
    }else{
        jQuery('#recurring_num').removeAttr('readonly');
    }
});

function getTherapists(){

    jQuery("#tharapist").html('');

    const Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000
    });

    var date = jQuery('#_date').val();
    var time = jQuery('#time').val();
    var token = $('meta[name="csrf-token"]').attr('content');

    $.ajax({
        type: 'POST',
        url: '/getEventTherapists',
        data: { '_token':token, 'date':date,'time':time },
        dataType: 'json',
        success: function (therapists) {
        if(therapists){
            jQuery("#tharapist").append(therapists);
        } 
        else{
            Toast.fire({
                icon: 'error',
                title: '{{ __('treatment.no_therapist_available') }}'
            })
        }
        }
    });

    jQuery('#guest_check').on('click',function(){
        jQuery('.max_guests').fadeToggle().css('display', 'flex');
    });
}
</script>
@stop