@extends("layouts.backend")

@section('content')

@php $cpr = $mdr = ''; @endphp
@foreach($settings as $setting)
  @if($setting->key == 'cpr_emp_fields')
    @if($setting->value == 'true')
        @php $cpr = 1; @endphp
    @else
        @php $cpr = 0; @endphp
    @endif 

  @elseif($setting->key == 'mdr_field')
    @if($setting->value == 'true')
        @php $mdr = 1; @endphp
    @else
        @php $mdr = 0; @endphp
    @endif
     
  @endif

@endforeach


<div class="content-wrapper">
    
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>{{ __('customer.create_customer') }}</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="/">{{ __('keywords.home') }}</a></li>
              <li class="breadcrumb-item active">{{ __('customer.create_customer') }}</li>
            </ol>
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>

    <div class="col-md-6" style="margin:auto; padding-top:30px;">
        <div class="card">
            <div class="card-body register-card-body">
                <p class="login-box-msg">{{ __('customer.register_a_new_customer') }}</p>

                <form action="{{ Route('registerCustomer',session('business_name')) }}" method="post" id="saveBusiness" enctype="multipart/form-data">                    
                    @csrf
                    <div class="input-group mb-3">
                        <input type="text" name="name" value="{{ old('name') }}" class="form-control" placeholder="{{ __('customer.full_name') }}">
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-user"></span>
                            </div>
                        </div>
                    </div>
                    @if ($errors->has('name'))
                        <span class="text-danger">{{ $errors->first('name') }}</span>
                    @endif

                    <div class="input-group mb-3">
                        <input type="email" name="email" value="{{ old('email') }}" class="form-control" placeholder="{{ __('keywords.email') }}">
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-envelope"></span>
                            </div>
                        </div>
                    </div>
                    @if ($errors->has('email'))
                        <span class="text-danger">{{ $errors->first('email') }}</span>
                    @endif

                    <!-- <span class="text-info"><i>{{ __('keywords.please_did_not_remove_country_code') }}</i></span> -->
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                          <select name="country" class="form-control" style="max-width:150px;">
                            @foreach($countries as $country)
                              <option value="{{ $country->id }}" @if($country->name == 'Danmak') selected @endif>{{ $country->name }}</option>
                            @endforeach 
                          </select>
                        </div>
                        <input type="number" name="number" value="{{ old('number') }}" class="form-control" placeholder="{{ __('keywords.number') }}">
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-phone"></span>
                            </div>
                        </div>
                    </div>
                    @if ($errors->has('number'))
                        <span class="text-danger">{{ $errors->first('number') }}</span>
                    @endif  

                    <div class="input-group mb-3">
                        <select class="form-control" name="language" id="language">
                        @foreach ( Config::get('languages') as $key => $val )
                          <option value="{{ $key }}">{{ $val['display'] }}</option>
                        @endforeach
                        </select>
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-language"></span>
                            </div>
                        </div>
                    </div>
                    @if ($errors->has('language'))
                        <span class="text-danger">{{ $errors->first('language') }}</span>
                    @endif

                    <div class="input-group mb-3">
                      <select name="gender" class="form-control select2">
                        <x-gender-list selected="man" />
                      </select>
                      <div class="input-group-append">
                        <div class="input-group-text">
                            <span class="fas fa-venus-mars"></span>
                        </div>
                      </div>
                    </div>
                    @if ($errors->has('gender'))
                      <span class="text-danger">{{ $errors->first('gender') }}</span>
                    @endif

                    <div class="input-group mb-3">
                        <select name="birthYear" class="form-control select2">
                          <x-birth-years-list selected="" />
                        </select>
                        <div class="input-group-append">
                          <div class="input-group-text">
                              <span class="fas fa-calendar-alt"></span>
                          </div>
                      </div>
                    </div>
                    @if ($errors->has('birthYear'))
                      <span class="text-danger">{{ $errors->first('birthYear') }}</span>
                    @endif

                    @if($cpr)
                      <div class="input-group mb-3">
                          <input type="text" name="cprnr" value="{{ old('cprnr') }}" class="form-control" placeholder="{{ __('customer.cpnr') }}">
                          <div class="input-group-append">
                              <div class="input-group-text">
                                  <span class="fas fa-key"></span>
                              </div>
                          </div>
                        @if ($errors->has('cprnr'))
                            <span class="text-danger">{{ $errors->first('cprnr') }}</span>
                        @endif
                      </div>
                    @else
                      <input type="hidden" name="cprnr" value="">
                    @endif
                    
                    @if($mdr)
                      <div class="input-group mb-3">
                          <input type="text" name="mednr" value="{{ old('mednr') }}" class="form-control" placeholder="{{ __('customer.mednr') }}">
                          <div class="input-group-append">
                              <div class="input-group-text">
                                  <span class="fas fa-key"></span>
                              </div>
                          </div>
                        @if ($errors->has('mednr'))
                            <span class="text-danger">{{ $errors->first('mednr') }}</span>
                        @endif
                      </div>
                    @else
                      <input type="hidden" name="mednr" value="">
                    @endif

                    <div class="form-group row">
                        <div class="input-group col-sm-12 pull-right">
                          <div class="custom-file">
                            <input type="file" class="custom-file-input" id="customFile" name="image">
                            <label class="custom-file-label" for="customFile">{{ __('customer.profile_picture') }}</label>
                          </div>
                        </div>
                        @if ($errors->has('image'))
                            <span class="text-danger">{{ $errors->first('image') }}</span>
                        @endif
                    </div>

                    <div class="col-4">
                        <button type="submit" class="btn btn-primary btn-block">{{ __('keywords.register') }}</button>
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
$(document).ready(function () {
  $.validator.setDefaults({
    submitHandler: function () {
        return true;
    }
  });

  function validate_cpr_number( cpr ) {

    if($.isNumeric(cpr)){

      var d = cpr.substring(0, 2);
      var m = cpr.substring(2, 4);
      var y = cpr.substring(4, 6);
      
      var fullDate = d+'/'+m+'/'+y;

      if(!isDate(fullDate)){
        return false;
      }

      return true;     
    }
    return false;  

    }

    function isDate(value) {
      var re = /^(?=\d)(?:(?:31(?!.(?:0?[2469]|11))|(?:30|29)(?!.0?2)|29(?=.0?2.(?:(?:(?:1[6-9]|[2-9]\d)?(?:0[48]|[2468][048]|[13579][26])|(?:(?:16|[2468][048]|[3579][26])00)))(?:\x20|$))|(?:2[0-8]|1\d|0?[1-9]))([-.\/])(?:1[012]|0?[1-9])\1(?:1[6-9]|[2-9]\d)?\d\d(?:(?=\x20\d)\x20|$))?(((0?[1-9]|1[012])(:[0-5]\d){0,2}(\x20[AP]M))|([01]\d|2[0-3])(:[0-5]\d){1,2})?$/;
      var flag = re.test(value);
      return flag;
    }
    //---------- Validation for user profile --------
    jQuery.validator.addMethod("validCpr", function(value, element) {
      return validate_cpr_number(value);
    }, "{{ __('treatment.please_enter_valid_number') }}");

  $('#saveBusiness').validate({
    rules: {
      @if($cpr)
        cprnr: {
            required: true,
            validCpr: true,
            minlength:10,
            maxlength:10
        },
      @endif
      @if($mdr)  
        mednr: {
            required: true
        },
      @endif
        name: {
            required: true
        },
        number: {
            required: true
        },
        email: {
            required: true,
            email: true,
        }
    },
    messages: {
    @if($cpr)
      cprnr: {
          required: "{{ __('customer.please_enter_CPR_number') }}",
          minlength: "{{ __('treatment.please_enter_at_least_10_cha') }}",
          maxlength: "{{ __('treatment.please_enter_at_least_10_cha') }}"
      },
    @endif
    @if($mdr)  
      mednr: {
          required: "{{ __('customer.please_enter_MED_number') }}"
      },
    @endif
      name: {
        required: "{{ __('customer.please_enter_full_name') }}",
      },
      number: {
        required: "{{ __('keywords.please_enter_mobile_number') }}",
        minlength: "{{ __('treatment.please_enter_at_least_8_cha') }}",
        maxlength: "{{ __('treatment.please_enter_at_least_8_cha') }}",
      },
      email: {
        required: "{{ __('customer.please_enter_a_email_address') }}",
        email: "{{ __('customer.please_enter_a_valid_email_address') }}"
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

jQuery('#saveBusiness').submit(function(){
  var cou = jQuery('select[name=country] option:selected').text();
  jQuery('input[name=number]').rules('remove','minlength');
  jQuery('input[name=number]').rules('remove','maxlength');
    if(cou == 'Denmark'){
      jQuery('input[name=number]').rules('add',{
        minlength:8,
        maxlength:8
      });
    }
});
</script>
@stop