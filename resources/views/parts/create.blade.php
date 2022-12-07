@extends("layouts.backend")

@section('content')

<div class="content-wrapper">
    
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>{{ __('part.create_area') }}</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="{{ Route('dashboard',session('business_name')) }}">{{ __('keywords.home') }}</a></li>
              <li class="breadcrumb-item active">{{ __('part.create_area') }}</li>
            </ol>
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>

    <div class="register-box" style="margin:auto; padding-top:30px;">
        <div class="card">
            <div class="card-body register-card-body">
                <p class="login-box-msg">{{ __('part.create_area') }}</p>
                <form action="{{ Route('addPart',session('business_name')) }}" method="post" id="saveMethod">
                    @csrf
                    <div class="input-group mb-3">
                        <input type="text" name="title" value="{{ old('title') }}" class="form-control" placeholder="{{ __('part.title') }}">
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <i class="fas fa-shield-alt"></i>
                            </div>
                        </div>
                    @if ($errors->has('title'))
                        <span class="text-danger">{{ $errors->first('title') }}</span>
                    @endif
                    </div>

                    @foreach ( Config::get('languages') as $key => $val )
                      @if ($key == 'en')
                        @continue
                      @endif
                      <div class="input-group mb-3">
                          <input type="text" name="{{ $key }}" class="form-control" placeholder="{{ $val['display'] }}">
                          <div class="input-group-append">
                              <div class="input-group-text">
                                  <i class="fas fa-shield-alt"></i>
                              </div>
                          </div>
                        @if ($errors->has($key))
                          <span class="text-danger">{{ $errors->first($key) }}</span>
                        @endif
                      </div>
                    @endforeach

                    <div class="input-group mb-3">
                        <input type="text" name="order" value="{{ old('order') }}" class="form-control" placeholder="{{ __('part.order') }}">
                        <div class="input-group-append">
                            <div class="input-group-text">
                            <i class="fas fa-sort-numeric-down"></i>
                            </div>
                        </div>
                    @if ($errors->has('order'))
                        <span class="text-danger">{{ $errors->first('order') }}</span>
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
  $('#saveMethod').validate({
    rules: {
        title: {
            required: true
        }
    },
    messages: {
        title: {
        required: "{{ __('part.please_enter_area_title') }}"
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