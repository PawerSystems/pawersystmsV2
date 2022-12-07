@extends("layouts.backend")

@section('content')

<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>{{ __('business.register_business') }}</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="/">{{ __('keywords.home') }}</a></li>
              <li class="breadcrumb-item active">{{ __('business.register_business') }}</li>
            </ol>
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>
    
    <div class="container" style="margin:auto; padding-top:30px;">
    <form action="{{ Route('registerBusiness',session('business_name')) }}" method="post" id="saveBusiness">
    <input type="hidden" name="role" value="Super Admin" />
    @csrf
    <div class="card card-default">
          <div class="card-header">
            <h3 class="card-title">{{ __('business.register_business') }}</h3>
          </div>
          <!-- /.card-header -->
          <div class="card-body">
            <div class="row">
              <div class="col-12">
                <div class="form-group">
                  <label>{{ __('business.permission-s') }}</label>
                  <select class="form-control duallistbox" name="permissions[]"  id="permissions" multiple>
                    @foreach($permissions as $permission)
                      <option value="{{ $permission }}" {{ in_array(trim($permission), array_map('trim',$selectedPermissions)) ? 'selected' : '' }}>{{$permission}}</option>
                    @endforeach
                  </select>
                  @if ($errors->has('permissions'))
                      <span class="text-danger">{{ $errors->first('permissions') }}</span>
                  @endif
                </div>
                <!-- /.form-group -->
              </div>
              <!-- /.col -->
            </div>
            <!-- /.row -->
          </div>
          <!-- /.card-body -->
        </div>
        <!-- /.card -->
        
        <div class="card">
            <div class="card-body register-card-body">
              <label>{{ __('business.business_detail') }}</label>
                    <div class="input-group mb-3">
                    <label style="width: 100%;">{{ __('business.full_name') }}:</label>

                        <input type="text" name="name" value="{{ old('name') }}" class="form-control" placeholder="{{ __('business.full_name') }}">
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
                    <label style="width: 100%;">{{ __('keywords.email') }}:</label>
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

                    <div class="input-group mb-3">
                      <label style="width: 100%;">{{ __('business.business_name') }}:</label>
                        <input type="text" name="business_name" value="{{ old('business_name') }}" class="form-control" placeholder="{{ __('business.business_name') }}">
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-building"></span>
                            </div>
                        </div>
                    </div>
                    @if ($errors->has('business_name'))
                        <span class="text-danger">{{ $errors->first('business_name') }}</span>
                    @endif

                    <div class="input-group mb-3">
                      <label style="width: 100%;">{{ __('business.language') }}:</label>
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
                      <label style="width: 100%;">{{ __('business.time_interval') }}:</label>

                        <select name="interval" value="{{ old('interval') }}" class="form-control" placeholder="{{ __('business.time_interval') }}">
                          <option value="5">5 Min</option>
                          <option value="10">10 Min</option>
                          <option value="15">15 Min</option>
                          <option value="20">20 Min</option>
                          <option value="30" selected>30 Min</option>
                        </select>
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-clock"></span>
                            </div>
                        </div>
                    </div>
                    @if ($errors->has('interval'))
                        <span class="text-danger">{{ $errors->first('interval') }}</span>
                    @endif

                    <div class="input-group mb-3">
                    <label style="width: 100%;">{{ __('business.password') }}:</label>
                        <input type="password" id="password" name="password" class="form-control" placeholder="{{ __('business.password') }}">
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-lock"></span>
                            </div>
                        </div>
                    </div>
                    @if ($errors->has('password'))
                        <span class="text-danger">{{ $errors->first('password') }}</span>
                    @endif

                    
                    <div class="input-group mb-3">
                    <label style="width: 100%;">{{ __('business.confirm_password') }}:</label>
                        <input type="password" name="password_confirmation" class="form-control" placeholder="{{ __('business.confirm_password') }}">
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-lock"></span>
                            </div>
                        </div>
                    </div>
                    @if ($errors->has('password_confirmation'))
                        <span class="text-danger">{{ $errors->first('password_confirmation') }}</span>
                    @endif
                    
                    
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary btn-block">{{ __('keywords.register') }}</button>
                    </div>
                    <!-- /.col -->
                 
            </div>
        </div> 
    </div>
  </form>
</div>
@stop

@section('scripts')

<!-- Bootstrap4 Duallistbox -->
<script src="{{asset('plugins/bootstrap4-duallistbox/jquery.bootstrap-duallistbox.min.js')}}"></script>

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

$(function () {
  
      
    //Bootstrap Duallistbox
    $('.duallistbox').bootstrapDualListbox()
 
});


$(document).ready(function () {
  $.validator.setDefaults({
    submitHandler: function () {
        return true;
    }
  });
  $('#saveBusiness').validate({
    rules: {
        name: {
            required: true
        },
        email: {
            required: true,
            email: true,
        },
        business_name: {
            required: true
        },
        interval: {
            required: true
        },
        password: {
            required: true,
            minlength: 8
        },
        password_confirmation: {
            required: true,
            equalTo : "#password",
            minlength: 8
        },
        language: {
            required: true,
        },
    },
    messages: {
      name: {
        required: " {{ __('business.pefn') }}",
      },
      email: {
        required: "{{ __('business.peaea') }}",
        email: "{{ __('business.peavea') }}"
      },
      business_name: {
        required: "{{ __('business.pebn') }}",
      },
      interval: {
          required: "{{ __('business.tiqr') }}",
      },
      password: {
        required: "{{ __('business.ppap') }}",
        minlength: "{{ __('business.ypmbal8cl') }}"
      },
      password_confirmation: {
        required: "{{ __('business.ppcp') }}",
        equalTo: "{{ __('business.ppspaa') }}",
        minlength: "{{ __('business.ypmbal8cl') }}"
      },
      language: {
        required: "{{ __('business.psal') }}",
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