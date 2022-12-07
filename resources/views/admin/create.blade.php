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
            <h1>{{ __('users.create_user') }}</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="/">{{ __('keywords.home') }}</a></li>
              <li class="breadcrumb-item active">{{ __('users.create_user') }}</li>
            </ol>
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>

    <div class="col-md-6" style="margin:auto; padding-top:30px;">
        <div class="card">
            <div class="card-body register-card-body">
                <p class="login-box-msg">{{ __('users.create_user') }}</p>

                <form action="{{ Route('registerAdmin',session('business_name')) }}" method="post" id="saveBusiness" enctype="multipart/form-data">                    
                    @csrf
                    <div class="input-group mb-3">
                        <input type="text" name="name" value="{{ old('name') }}" class="form-control" placeholder="{{ __('users.full_name') }}">
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
                              <option value="{{ $country->id }}" @if ($country->name == 'Danmark')
                                selected
                              @endif >{{ $country->name }}</option>
                            @endforeach 
                          </select>
                        </div>
                        <input type="number" name="number" value="{{ old('number') }}" class="form-control" placeholder="{{ __('keywords.number') }}">
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-phone"></span>
                            </div>
                        </div>
                    @if ($errors->has('number'))
                        <span class="text-danger">{{ $errors->first('number') }}</span>
                    @endif
                    </div>

                    <div class="input-group mb-3">
                        <select name="role" class="form-control">
                          @foreach($roles as $role)  
                            <option value="{{ $role->id }}">{{ $role->title }}</option>
                          @endforeach 
                        </select>
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-user-tag"></span>
                            </div>
                        </div>
                    @if ($errors->has('role'))
                        <span class="text-danger">{{ $errors->first('role') }}</span>
                    @endif
                    </div>

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
                        <input type="text" name="cprnr" value="{{ old('cprnr') }}" class="form-control" placeholder="{{ __('users.cpr') }}">
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
                        <input type="text" name="mednr" value="{{ old('mednr') }}" class="form-control" placeholder="{{ __('users.med') }}">
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
                            <label class="custom-file-label" for="customFile">{{ __('users.profile_picture') }}</label>
                          </div>
                        </div>
                        @if ($errors->has('image'))
                            <span class="text-danger">{{ $errors->first('image') }}</span>
                        @endif
                    </div>

                    <div class="form-group row">
                      <div class="col-sm-12">
                        <textarea class="form-control" name="text" id="text"></textarea>
                      </div>  
                    </div>

                    <div class="form-group">
                      <div class="custom-control custom-checkbox">
                          <input type="checkbox" name="is_therapist" id="is_therapist" class="custom-control-input">
                          <label class="custom-control-label" for="is_therapist">{{ __('users.is_therapist') }}</label>
                      </div>
                    </div>

                    <div class="col-4">
                        <button type="submit" class="btn btn-primary btn-block">{{ __('keywords.create') }}</button>
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
  $('#saveBusiness').validate({
    rules: {
      @if($cpr)
        cprnr: {
            // required: true,
            minlength: 10,
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
        email: {
            required: true,
            email: true,
        },
        number: {
            required: true,
        }

    },
    messages: {
    @if($cpr)
      cprnr: {
          // required: "{{ __('users.please_enter_CPR_number') }}",
          minlength: "{{ __('treatment.please_enter_at_least_10_cha') }}",
          maxlength: "{{ __('treatment.please_enter_at_least_10_cha') }}"
      },
    @endif
    @if($mdr)  
      mednr: {
          required: "{{ __('users.please_enter_MED_number') }}"
      },
    @endif
      name: {
        required: "{{ __('users.please_enter_full_name') }}",
      },
      email: {
        required: "{{ __('users.please_enter_a_email_address') }}",
        email: "{{ __('users.please_enter_a_valid__email_address') }}"
      },
      number: {
        required: "{{ __('keywords.please_enter_mobile_number') }}",
        minlength: "{{ __('treatment.please_enter_at_least_8_cha') }}",
        maxlength: "{{ __('treatment.please_enter_at_least_8_cha') }}",
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