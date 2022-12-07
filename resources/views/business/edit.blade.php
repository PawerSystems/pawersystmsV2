@extends("layouts.backend")

@section('content')

<div class="content-wrapper">

    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>{{ __('business.edit_business') }}</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="/">{{ __('keywords.home') }}</a></li>
              <li class="breadcrumb-item active">{{ __('business.edit_business') }}</li>
            </ol>
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>

  <div class="container" style="margin:auto; padding-top:30px;">
    <form action="{{ Route('editBusiness',session('business_name')) }}" method="post" id="editBusiness">
    <input type="hidden" name="id" value="{{ $business->id }}" />
        @csrf
      <div class="card card-default">
          <div class="card-header">
            <h3 class="card-title">{{ __('business.edit_business') }}</h3>
          </div>
          <!-- /.card-header -->
          <div class="card-body">
            <div class="row">
              <div class="col-12">
                <div class="form-group">
                  <label>{{ __('business.permission-s') }}</label>
                  <select class="form-control duallistbox" name="permissions[]"  id="permissions" multiple>
                      @php
                        $exist = $business->permissions->pluck('title')->toArray();
                      @endphp
                      @foreach($permissions as $permission)

                        @php $check = ' '; @endphp 

                        @if (in_array(trim($permission),$exist))
                          @php $check = 'selected'; @endphp 
                        @endif

                          <option value="{{ $permission }}" {{ $check }}>
                          {{$permission}}</option>

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
                        <input type="text" value="{{ $business->business_name }}.{{ config('app.domain') }}" class="form-control" readonly>
                        <div class="input-group-append">
                            <div class="input-group-text">
                            <i class="fab fa-internet-explorer"></i>
                            </div>
                        </div>
                    </div>

                    <div class="input-group mb-3">
                        <input type="text" name="business_name" value="{{ $business->business_name }}" class="form-control" placeholder="{{ __('business.business_name') }}" disabled="disabled">
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-building"></span>
                            </div>
                        </div>
                    @if ($errors->has('business_name'))
                        <span class="text-danger">{{ $errors->first('business_name') }}</span>
                    @endif
                    </div>

                    <div class="form-group">
                      <div class="custom-control custom-checkbox">
                        <input type="checkbox" name="status" class="custom-control-input" id="exampleCheck1" {{ $business->is_active ? 'checked' : '' }}>
                        <label class="custom-control-label" for="exampleCheck1">{{ __('business.active') }}</label>
                      </div>
                    </div>

                    <div class="form-group">
                      <div class="custom-control custom-checkbox">
                        <input type="checkbox" name="sms" class="custom-control-input" id="exampleCheck2" 
                        @if($sms) {{ $sms->value == 'true' ? 'checked' : '' }} @endif >
                        <label class="custom-control-label" for="exampleCheck2">{{ __('business.sms_setting') }}</label>
                      </div>
                    </div>
                    
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary btn-block">{{ __('keywords.save') }}</button>
                    </div>
                    <!-- /.col -->
                 
            </div>
        </div> 
        </form>
    </div>
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



jQuery(document).ready(function () {
    jQuery.validator.setDefaults({
    submitHandler: function () {
        return true;
    }
  });
  jQuery('#editBusiness').validate({
    rules: {
      business_name: {
        required: true,
        minlength: 1
      },
    },
    messages: {
        business_name: {
        required: "{{ __('business.pebn') }}",
        minlength: "{{ __('business.bnsbaol') }}"
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
</script>
@stop