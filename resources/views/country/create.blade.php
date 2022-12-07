@extends("layouts.backend")

@section('content')

<div class="content-wrapper">
    
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>{{ __('country.add_country') }}</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="{{ Route('dashboard',session('business_name')) }}">{{ __('keywords.home') }}</a></li>
              <li class="breadcrumb-item active">{{ __('country.add_country') }}</li>
            </ol>
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>

    <div class="col-md-4" style="margin:auto; padding-top:30px;">
        <div class="card">
            <div class="card-body register-card-body">
                <p class="login-box-msg">{{ __('country.add_country') }}</p>
                <form action="{{ Route('registerCountry',session('business_name')) }}" method="post" id="saveCountry">
                    @csrf
                    <div class="input-group mb-3">
                        <label style="width: 100%;">{{ __('country.country_name') }}:</label>
                        <input type="text" name="name" value="{{ old('name') }}" class="form-control" placeholder="{{ __('country.country_name') }}">
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <i class="fas fa-globe-europe"></i>
                            </div>
                        </div>
                        @if ($errors->has('name'))
                            <span class="text-danger">{{ $errors->first('name') }}</span>
                        @endif
                    </div>

                    <div class="input-group mb-3">
                        <label style="width: 100%;">{{ __('country.country_code') }}:</label>
                        <input type="number" name="code" value="{{ old('code') }}" class="form-control" min="0" placeholder="{{ __('country.country_code') }}">
                        <div class="input-group-append">
                            <div class="input-group-text">
                            <i class="fas fa-sort-numeric-up-alt"></i>
                            </div>
                        </div>
                        @if ($errors->has('code'))
                            <span class="text-danger">{{ $errors->first('code') }}</span>
                        @endif
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
<script type="text/javascript">
$(function () {
    //Initialize Select2 Elements
    $('.select2bs4').select2({
      theme: 'bootstrap4'
    });
  });
</script>
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
  $('#saveCountry').validate({
    rules: {
        name: {
            required: true
        },
        code: {
            required: true
        },
    },
    messages: {
      name: {
        required: "{{ __('country.pecn') }}"
      },
      code: {
        required: "{{ __('country.pecc') }}"
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