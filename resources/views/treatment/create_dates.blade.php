@extends("layouts.backend")

@section('content')

<div class="content-wrapper"> 

    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>{{ __('treatment.create_date') }}</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="{{ Route('dashboard',session('business_name')) }}">{{ __('keywords.home') }}</a></li>
              <li class="breadcrumb-item active">{{ __('treatment.create_date') }}</li>
            </ol>
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>

    <div class="row"> 
        <div class="col-sm-3"></div>  
        <div class="col-md-6 col-sm-12">
        <form action="{{ Route('savetreatmentdate',session('business_name')) }}" method="post" id="saveBusiness">
                    @csrf
            <div class="card card-info">
                    <div class="card-header">
                        <h3 class="card-title">{{ __('treatment.create_date') }}</h3>
                    </div>
                    
                    <div class="card-body">
                        <!-- Date -->
                        <div class="form-group">
                            <label>{{ __('treatment.date') }}:</label>
                            <div class="input-group">
                                <div class="input-group-append" data-target="#date" data-toggle="datetimepicker">
                                    <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                </div>
                                <input type="text" name="date" class="form-control float-right" id="date" onchange="getTherapists()" readonly="readonly">
                                <input type="hidden" name="_date" id="_date">
                            </div>
                        </div>
                        <!-- Treatment-->
                        <div class="input-group mb-3">
                            <label style="width: 100%;">{{ __('treatment.treatment-s') }}:</label>
                            <div class="form-check" style="width: 100%;">
                                <input class="form-check-input" type="checkbox" id="checkbox">
                                <label class="form-check-label">{{ __('keywords.all') }}</label>
                            </div>
                            <div class="input-group-append">
                                <div class="input-group-text">
                                    <span class="fas fa-shield-alt"></span>
                                </div>
                            </div>
                            <select class="form-control treat select2" name="treatment[]"  id="treatment" multiple>
                                @foreach($treatments as $treatment)
                                    <option value="{{ $treatment->id }}">
                                    {{$treatment->treatment_name}} ( {{ $treatment->time_shown ?: $treatment->inter}} min )</option>
                                @endforeach
                            </select> 
                        </div>
                        
                        @if ($errors->has('treatment'))
                            <span class="text-danger">{{ $errors->first('treatment') }}</span>
                        @endif
                        <!-- Recurring -->
                        <div class="input-group mb-3">
                            <label style="width: 100%;">{{ __('treatment.recurrence') }}:</label>
                            <div class="input-group-append">
                                <div class="input-group-text">
                                    <span class="fas fa-history"></span>
                                </div>
                            </div>
                            <select class="form-control select2" name="recurrence"  id="recurrence">
                                <option value="d">{{ __('keywords.daily') }}</option>
                                <option value="w" selected>{{ __('keywords.weekly') }}</option>
                                <option value="biw">{{ __('keywords.bi_weekly') }}</option>
                                {{-- <option value="m">{{ __('keywords.monthly') }}</option> --}}
                                <option value="dd">{{ __('keywords.all_week_days') }}</option>
                            </select> 
                        </div>
                        <div class="input-group mb-3">
                            <label style="width: 100%;">{{ __('treatment.recurrence_number') }}:</label>
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
                        <!-- From -->
                        <div class="input-group mb-3">
                            <label style="width: 100%;">{{ __('treatment.from') }}:</label>
                            <div class="input-group-append">
                                <div class="input-group-text">
                                    <span class="fas fa-clock"></span>
                                </div>
                            </div>
                            <select class="form-control select2" name="from" id="from" onchange="getTherapists()">
                                <x-time-range selected="9:00" :booked="[]" />
                            </select> 
                            @if ($errors->has('from'))
                                <span class="text-danger">{{ $errors->first('from') }}</span>
                            @endif
                        </div>  
                        <!-- To --> 
                        <div class="input-group mb-3">
                            <label style="width: 100%;">{{ __('treatment.till') }}:</label>
                            <div class="input-group-append">
                                <div class="input-group-text">
                                    <span class="fas fa-clock"></span>
                                </div>
                            </div>
                            <select class="form-control select2" name="till" id="till" onchange="getTherapists()">
                                <x-time-range selected="16:00" :booked="[]"/>
                            </select> 
                            @if ($errors->has('till'))
                                <span class="text-danger">{{ $errors->first('till') }}</span>
                            @endif
                        </div>
                        <!-- Lunch --> 
                        <div class="input-group mb-3">
                            <label style="width: 100%;">{{ __('treatment.lunch') }}:</label>
                            <div class="input-group-append">
                                <div class="input-group-text">
                                    <span class="fas fa-clock"></span>
                                </div>
                            </div>
                            <select class="form-control select2" name="lunch" id="lunch" >
                                <option class="badge d-none" value="none" selected>{{ __('keywords.none') }}</option>
                                <option class="badge" value="12:00">12:00</option>
                                <x-time-range selected="00:00" :booked="[]"/>
                            </select> 
                            @if ($errors->has('lunch'))
                                <span class="text-danger">{{ $errors->first('lunch') }}</span>
                            @endif
                        </div>
                        <!-- Tharapist -->
                        <div class="input-group mb-3">
                            <label style="width: 100%;">{{ __('treatment.therapist') }}:</label>
                            <div class="input-group-append">
                                <div class="input-group-text">
                                    <span class="fas fa-user"></span>
                                </div>
                            </div>
                            <select class="form-control select2" name="tharapist" id="tharapist" >
                            </select> 
                            @if ($errors->has('tharapist'))
                                <span class="text-danger">{{ $errors->first('tharapist') }}</span>
                            @endif
                        </div>

                        <!-- Description-->
                        <div class="form-group">
                            <label>{{ __('treatment.description') }}:</label>
                            <textarea class="form-control" name="desc" id="desc"></textarea>                     
                        </div> 

                        <div class="form-group">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" name="waiting_list" id="waiting_list" class="custom-control-input">
                                <label class="custom-control-label" for="waiting_list">{{ __('treatment.waiting_list') }}</label>
                            </div>
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

  $('#saveBusiness').validate({

    rules: {
        date: {
            required: true
        },
        recurring_num:{
            required: true
        },
        from: {
            required: true
        },
        till: {
            required: true
        },
        tharapist: {
            required: true
        },
        "treatment[]":"required"
    },
    messages: {
        date: {
            required: "{{ __('treatment.please_select_date_first') }}"
        },
        recurring_num:{
            required: "{{ __('treatment.please_put_at_least_one_number') }}"
        },
        till: {
            required: "{{ __('treatment.please_select_end_time') }}"
        },
        from: {
            required: "{{ __('treatment.please_select_start_time') }}"
        },
        tharapist: {
            required: "{{ __('treatment.please_select_a_tharapist') }}"
        },
        "treatment[]":"{{ __('treatment.please_select_a_treatment') }}"
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


<!-- Page script -->
<script>

$("#checkbox").click(function(){
    if($("#checkbox").is(':checked') ){
        $("#treatment > option").prop("selected","selected");
        $("#treatment").trigger("change");
    }else{
        $("#treatment > option").removeAttr("selected");
         $("#treatment").trigger("change");
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
    var from = jQuery('#from').val();
    var till = jQuery('#till').val();
    var token = $('meta[name="csrf-token"]').attr('content');

    $.ajax({
        type: 'POST',
        url: '/getTherapists',
        data: { '_token':token, 'date':date,'from':from,'till':till },
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

}

jQuery('#recurrence').on('change',function(){
    if( jQuery(this).val() == 'dd' ){
        jQuery('#recurring_num').val(1);
        jQuery('#recurring_num').attr('readonly','true');
    }else{
        jQuery('#recurring_num').removeAttr('readonly');
    }
});
</script>
@stop