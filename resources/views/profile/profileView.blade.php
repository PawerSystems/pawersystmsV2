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

<style>
  .select2-container--bootstrap4{ width:100% !important; }
</style>
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>{{ __('profile.profile') }}</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="/">{{ __('keywords.home') }}</a></li>
              <li class="breadcrumb-item active">{{ __('profile.profile') }}</li>
            </ol>
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>

    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        <div class="row">
          <div class="col-md-3">
            @php
              if(Auth::user()->profile_photo_path){
                $img = Auth::user()->profile_photo_path;
              }
              else{
                if(Auth::user()->gender == 'women'){ $img = 'avatar2.png'; }
                else{ $img =  'avatar5.png'; }
              }
            @endphp
            <!-- Profile Image -->
            <div class="card card-primary card-outline">
              <div class="card-body box-profile">
                <div class="text-center">
                  <img class="profile-user-img img-fluid img-circle"
                       src="images/{{ $img }}"
                       alt="{{ __('keywords.user_profile_picture') }}"
                       style="width:88px; height:88px;"
                       >
                </div>

                <h3 class="profile-username text-center">{{  Auth::user()->name }}</h3>

                <p class="text-muted text-center">
             
                @if(is_numeric(Auth::user()->role))
                    @php
                        $role = App\Models\Role::find(Auth::user()->role);
                    @endphp
                @else
                    @php $role = ''; @endphp
                @endif

                @if($role)    
                    {{ $role->title }}
                @else
                    {{ Auth::user()->role }}
                @endif    
                </p>

                <ul class="list-group list-group-unbordered mb-3">
                  <li class="list-group-item">
                    <b>{{ __('profile.business_name') }}</b> <a class="float-right">{{ session('business_name') }}</a>
                  </li>
                  <li class="list-group-item">
                    <b>{{ __('keywords.email') }}</b> <a class="float-right">{{ Auth::user()->email }}</a>
                  </li>
                </ul>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <a class="btn btn-danger btn-block" href="{{ route('logout') }}"
                        onclick="event.preventDefault();
                        this.closest('form').submit();">
                        {{ __('leftnav.logout') }}
                    </a>
                </form><br>

                @if(!Auth::user()->is_subscribe)
                  <button class="btn btn-success btn-block sub_btn" onclick="sub(this)">{{ __('profile.subscribe_for_newsletter') }}</button>
                @else
                  <button class="btn btn-warning btn-block unsub_btn" onclick="sub(this)">{{ __('profile.unsubscribe_for_newsletter') }}</button>
                  <i style="color:blue; font-size:14px;">* {{ __('profile.sub_button_info') }}</i>
                @endif  
              </div>
              <!-- /.card-body -->
            </div>
            <!-- /.card -->

          </div>
          <!-- /.col -->
          <div class="col-md-9">
          @if(Session::get('status'))
          <div class="alert alert-warning alert-dismissible">
            <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
              {{ Session::get('status') }}
          </div>
          @endif
            <div class="card">
              <div class="card-header p-2">
                <ul class="nav nav-pills">
                  <li class="nav-item"><a class="nav-link active" href="#details" data-toggle="tab">{{ __('profile.update_details') }}</a></li>
                  <li class="nav-item"><a class="nav-link" href="#password" data-toggle="tab">{{ __('profile.change_password') }}</a></li>
                </ul>
              </div><!-- /.card-header -->
              <div class="card-body">
                <div class="tab-content">
               
                  <div class="active tab-pane" id="details">
                    <form id="updateProfile" class="form-horizontal" method="POST" action="{{ route('updateProfile',Session('business_name')) }}" enctype="multipart/form-data">
                    @csrf
                      <div class="form-group row">
                        <label for="inputName" class="col-sm-2 col-form-label">{{ __('keywords.name') }} <span style="color:red">*</span></label>
                        <div class="col-sm-10">
                          <input type="name" value="{{ Auth::user()->name }}" name="name" class="form-control" id="inputName" placeholder="{{ __('keywords.name') }}">
                          @if ($errors->has('name'))
                              <span class="text-danger">{{ $errors->first('name') }}</span>
                          @endif
                        </div>
                      </div>

                      <div class="form-group row">
                        <label for="inputEmail" class="col-sm-2 col-form-label">{{ __('keywords.email') }} <span style="color:red">*</span></label>
                        <div class="col-sm-10">
                          <input type="email" value="{{ Auth::user()->email }}" name="email" class="form-control" id="inputEmail" placeholder="{{ __('keywords.email') }}">
                          @if ($errors->has('email'))
                              <span class="text-danger">{{ $errors->first('email') }}</span>
                          @endif
                        </div>
                      </div>

                      <div class="form-group row">
                        <label for="inputNumber" class="col-sm-2 col-form-label">{{ __('keywords.number') }} <span style="color:red">*</span></label>
                        <div class="col-sm-10">
                          <div class="input-group-prepend">
                            <select name="country" class="form-control" style="max-width:150px;">
                              @foreach($countries as $country)
                                <option value="{{ $country->id }}" @if( $country->name == 'Denmark') selected @endif>{{ $country->name }}</option>
                              @endforeach 
                            </select>
                            <input type="number" name="number" value="{{ Auth::user()->number }}" class="form-control" placeholder="{{ __('keywords.number') }}">
                          </div>
                        </div>  
                        @if ($errors->has('number'))
                            <span class="text-danger">{{ $errors->first('number') }}</span>
                        @endif
                      </div>

                      @if($cpr)
                        <div class="form-group row">
                          <label for="inputCPR" class="col-sm-2 col-form-label">{{ __('customer.cpnr') }} 
                            @if(auth()->user()->role == 'Customer')
                              <span style="color:red">*</span>
                            @endif
                          </label>
                          <div class="col-sm-10">
                            <input type="text" value="{{ Auth::user()->cprnr }}" name="cprnr" class="form-control" id="inputCPR" placeholder="{{ __('customer.cpnr') }}">
                            @if ($errors->has('cprnr'))
                                <span class="text-danger">{{ $errors->first('cprnr') }}</span>
                            @endif
                          </div>
                        </div>
                      @else
                        <input type="hidden" name="cprnr" value="">
                      @endif
        
                      @if($mdr)  
                        <div class="form-group row">
                          <label for="inputMED" class="col-sm-2 col-form-label">{{ __('users.med') }} <span style="color:red">*</span></label>
                          <div class="col-sm-10">
                            <input type="text" value="{{ Auth::user()->mednr }}" name="mednr" class="form-control" id="inputMED" placeholder="{{ __('users.med') }}">
                            @if ($errors->has('mednr'))
                                <span class="text-danger">{{ $errors->first('mednr') }}</span>
                            @endif
                          </div>
                        </div>
                      @else
                        <input type="hidden" name="mednr" value="">
                      @endif

                      <div class="form-group row">
                        <label for="inputEmail" class="col-sm-2 col-form-label">{{ __('profile.gender') }} <span style="color:red">*</span></label>
                        <div class="col-sm-10">
                          <select name="gender" class="form-control select2">
                            <x-gender-list selected="{{ Auth::user()->gender }}" />
                          </select>
                          @if ($errors->has('gender'))
                              <span class="text-danger">{{ $errors->first('gender') }}</span>
                          @endif
                        </div>
                      </div>

                      <div class="form-group row">
                        <label for="inputEmail" class="col-sm-2 col-form-label">{{ __('profile.birth_year') }} <span style="color:red">*</span></label>
                        <div class="col-sm-10">
                          <select name="birthYear" class="form-control select2">
                            <x-birth-years-list selected="{{ Auth::user()->birth_year }}" />
                          </select>
                          @if ($errors->has('birthYear'))
                              <span class="text-danger">{{ $errors->first('birthYear') }}</span>
                          @endif
                        </div>
                      </div>

                    <div class="form-group row">
                      <label for="inputLanguage" class="col-sm-2 col-form-label">{{ __('profile.language') }}</label>
                      <div class="col-sm-10">
                      <select class="form-control" name="language" id="language">
                      @foreach ( Config::get('languages') as $key => $val )
                        <option value="{{ $key }}" 
                        @if( Auth::user()->language == $key )
                          selected
                        @endif
                        >{{ $val['display'] }}</option>
                      @endforeach
                      </select>
                      </div>
                  </div>
                  @if ($errors->has('language'))
                      <span class="text-danger">{{ $errors->first('language') }}</span>
                  @endif

                      <div class="form-group row">
                        <label for="customFile"class="col-sm-2 col-form-label">{{ __('profile.profile_photo') }}</label>
                        <div class="input-group col-sm-10 pull-right">
                          <div class="custom-file">
                            <input type="file" class="custom-file-input" id="customFile" name="image">
                            <label class="custom-file-label" for="customFile">{{ __('profile.choose_file') }}</label>
                          </div>
                        </div>
                        @if ($errors->has('image'))
                          <div class="input-group col-sm-10 pull-right offset-md-2">
                            <span class="text-danger">{{ $errors->first('image') }}</span>
                          </div>
                        @endif
                      </div>

                      <div class="form-group row">
                        <label for="inputName" class="col-sm-2 col-form-label"></label>
                        <div class="col-sm-10">
                          <textarea class="form-control" name="text" id="text" value="{{ $txt->free_txt }}">{{ $txt->free_txt }}</textarea>
                          @if(Auth::user()->role != 'Customer')
                            <br>
                            <div class="custom-control custom-checkbox">
                              <input type="checkbox" name="is_therapist" id="is_therapist" class="custom-control-input" @if(Auth::user()->is_therapist) checked @endif >
                              <label class="custom-control-label" for="is_therapist">{{ __('users.is_therapist') }}</label>
                            </div>
                            <br>
                            <div class="custom-control custom-checkbox">
                              <input type="checkbox" name="will_notify" id="will_notify" class="custom-control-input" @if(Auth::user()->will_notify) checked @endif >
                              <label class="custom-control-label" for="will_notify">{{ __('users.wtrboceoc') }}</label>
                            </div>
                          @endif 
                        </div>
                      </div>

                      <div class="form-group row">
                        <div class="col-sm-12">
                          <button type="submit" class="btn btn-info float-right">{{ __('keywords.submit') }}</button>
                        </div>
                      </div>
                    </form>
                  </div>
                  <div class="tab-pane" id="password">
                  {{-- @livewire('profile.update-password-form') --}}
                  <form class="form-horizontal" id="changePass" method="POST" action="{{ route('passreset',Session('business_name')) }}">
                    @csrf
                      <div class="form-group row">
                        <label for="cpass" class="col-sm-3 col-form-label">{{ __('profile.current_pass') }}</label>
                        <div class="col-sm-9">
                          <input type="password" name="cpass" class="form-control" id="cpass" placeholder="{{ __('profile.current_pass') }}">
                          @if ($errors->has('cpass'))
                              <span class="text-danger">{{ $errors->first('cpass') }}</span>
                          @endif
                        </div>
                      </div>
                      <div class="form-group row">
                        <label for="npass" class="col-sm-3 col-form-label">{{ __('profile.new_pass') }}</label>
                        <div class="col-sm-9">
                        <input type="password" name="npass" class="form-control" id="npass" placeholder="{{ __('profile.new_pass') }}">
                          @if ($errors->has('npass'))
                              <span class="text-danger">{{ $errors->first('npass') }}</span>
                          @endif
                        </div>
                      </div>
                      <div class="form-group row">
                        <label for="rpass" class="col-sm-3 col-form-label">{{ __('profile.confirm_pass') }}</label>
                        <div class="col-sm-9">
                        <input type="password" name="rpass" class="form-control" id="rpass" placeholder="{{ __('profile.confirm_pass') }}">
                          @if ($errors->has('rpass'))
                              <span class="text-danger">{{ $errors->first('rpass') }}</span>
                          @endif
                        </div>
                      </div>
                      <div class="form-group row">
                        <div class="col-sm-12">
                          <button type="submit" class="btn btn-info float-right">{{ __('keywords.submit') }}</button>
                        </div>
                      </div>
                    </form>
                  </div>
                  <!-- /.tab-pane -->
                </div>
                <!-- /.tab-content -->
              </div><!-- /.card-body -->
            </div>
            <!-- /.nav-tabs-custom -->
          </div>
          <!-- /.col -->
        </div>
        <!-- /.row -->
      </div><!-- /.container-fluid -->
    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->



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

function sub(obj){

  const Toast = Swal.mixin({
		  toast: true,
		  position: 'top-end',
		  showConfirmButton: false,
		  timer: 3000
		});

  var token = "{{csrf_token()}}";

  $.ajax({
    type: 'POST',
    url: '/subUser',
    data: {'_token':token},
    dataType: 'json',
    success: function (data) {
      if(data['status'] == 'success'){
        Toast.fire({
            icon: 'success',
            title: data['data']
        });
        
        if(jQuery(obj).hasClass('sub_btn')){
          jQuery(obj).removeClass('sub_btn btn-success').addClass('unsub_btn btn-warning').text('{{ __('profile.unsubscribe_for_newsletter') }}');
        }
        else{
          jQuery(obj).removeClass('unsub_btn btn-warning').addClass('sub_btn btn-success').text('{{ __('profile.subscribe_for_newsletter') }}');
        }
      }
    },
    error: function (data) {
      Toast.fire({
            icon: 'error',
            title: data['data']
        });
    }
  });

}


$(document).ready(function () {
  $.validator.setDefaults({
    submitHandler: function () {
        return true;
    }
  });

  //---------- Validation for change password --------
  $('#changePass').validate({
    rules: {
        cpass: {
            required: true,
            minlength: 8
        },
        npass: {
            required: true,
            minlength: 8
        },
        rpass: {
            required: true,
            minlength: 8
        }
    },
    messages: {
      cpass: {
        required: "{{ __('profile.ppcp') }}",
        minlength: "{{ __('profile.ypmbal8cl') }}"
      },
      npass: {
        required: "{{ __('profile.ppnp') }}",
        minlength: "{{ __('profile.ypmbal8cl') }}"
      },
      rpass: {
        required: "{{ __('profile.ppcip') }}",
        minlength: "{{ __('profile.ypmbal8cl') }}"
      }
    },
    errorElement: 'span',
    errorPlacement: function (error, element) {
      error.addClass('invalid-feedback');
      element.closest('.form-group .col-sm-9').append(error);
    },
    highlight: function (element, errorClass, validClass) {
      $(element).addClass('is-invalid');
    },
    unhighlight: function (element, errorClass, validClass) {
      $(element).removeClass('is-invalid');
    }
  });

  //---------- Validation for user profile --------
  jQuery.validator.addMethod("validCpr", function(value, element) {
    return validate_cpr_number(value);
  }, "{{ __('treatment.please_enter_valid_number') }}");

  $('#updateProfile').validate({
    rules: {
      @if($cpr)
        cprnr: {
          @if(auth()->user()->role == 'Customer')
            required: true,
            validCpr: true,
          @endif  
          minlength: 10,
          maxlength: 10
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
            email: true
        },
        gender: {
          required: true
        },
        birthYear: {
          required: true
        }
    },
    messages: {
    @if($cpr)
      cprnr: {
        @if(auth()->user()->role == 'Customer')
          required: "{{ __('users.please_enter_CPR_number') }}",
        @endif  
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
        required: "{{ __('profile.ppan') }}"
      },
      number: {
        required: "{{ __('keywords.please_enter_mobile_number') }}",
        minlength: "{{ __('treatment.please_enter_at_least_8_cha') }}",
        maxlength: "{{ __('treatment.please_enter_at_least_8_cha') }}",
      },
      email: {
        required: "{{ __('profile.ppae') }}",
        minlength: "{{ __('profile.peavea') }}"
      },
      gender: {
        required: "{{ __('profile.psygf') }}",
      },
      birthYear: {
        required: "{{ __('profile.psyby') }}",
      }
    },
    errorElement: 'span',
    errorPlacement: function (error, element) {
      error.addClass('invalid-feedback');
      element.closest('.form-group .col-sm-10').append(error);
    },
    highlight: function (element, errorClass, validClass) {
      $(element).addClass('is-invalid');
    },
    unhighlight: function (element, errorClass, validClass) {
      $(element).removeClass('is-invalid');
    }
  });

});

jQuery('#updateProfile').submit(function(){
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

function validate_cpr_number( cpr ) {

if($.isNumeric(cpr)){

  var d = cpr.substring(0, 2);
  var m = cpr.substring(2, 4);
  var y = cpr.substring(4, 6);
  
  var fullDate = d+'/'+m+'/'+y;

  if(!isDate(fullDate))
    return false;

  return true;     
}

return false;  

}

function isDate(value) {
  var re = /^(?=\d)(?:(?:31(?!.(?:0?[2469]|11))|(?:30|29)(?!.0?2)|29(?=.0?2.(?:(?:(?:1[6-9]|[2-9]\d)?(?:0[48]|[2468][048]|[13579][26])|(?:(?:16|[2468][048]|[3579][26])00)))(?:\x20|$))|(?:2[0-8]|1\d|0?[1-9]))([-.\/])(?:1[012]|0?[1-9])\1(?:1[6-9]|[2-9]\d)?\d\d(?:(?=\x20\d)\x20|$))?(((0?[1-9]|1[012])(:[0-5]\d){0,2}(\x20[AP]M))|([01]\d|2[0-3])(:[0-5]\d){1,2})?$/;
  var flag = re.test(value);
  return flag;
}


</script>
@stop