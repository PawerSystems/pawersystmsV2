@extends("layouts.backend")

@section('content')

<div class="content-wrapper">
    
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>{{ __('survey.add_question') }}</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="{{ Route('dashboard',session('business_name')) }}">{{ __('keywords.home') }}</a></li>
              <li class="breadcrumb-item active">{{ __('survey.add_question') }}</li>
            </ol>
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>

    <div class="col-md-6" style="margin:auto; padding-top:30px;">
        <div class="card">
            <div class="card-body register-card-body">
                <p class="login-box-msg">{{ __('survey.add_question') }}</p>
                <form action="{{ Route('saveQuestion',session('business_name')) }}" method="post" id="saveCard">
                    @csrf
                    <div class="input-group mb-3">
                        <label style="width: 100%;">{{ __('survey.title') }}:</label>
                        <input type="text" name="title" value="{{ old('title') }}" class="form-control" placeholder="{{ __('survey.title') }}">
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <i class="fas fa-question"></i>
                            </div>
                        </div>
                        @if ($errors->has('title'))
                            <span class="text-danger">{{ $errors->first('title') }}</span>
                        @endif
                    </div>

                    <div class="input-group mb-3">
                        <label style="width: 100%;">{{ __('survey.language') }}:</label>
                        <select name="language" class="form-control" id="language">
                            @foreach ( Config::get('languages') as $key => $val )
                              <option value="{{ $key }}">{{ $val['display'] }}</option>
                            @endforeach
                        </select>
                        <div class="input-group-append">
                            <div class="input-group-text">
                            <i class="fas fa-language"></i>                            
                        </div>
                        </div>
                    </div>

                    <div class="input-group mb-3">
                        <label style="width: 100%;">{{ __('survey.options') }}: 
                        <br><i>{{ __('survey.iowbttlie') }}</i></label>
                        <input type="text" name="options[]" class="form-control" placeholder="{{ __('survey.options') }}">
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <i class="fas fa-check"></i>
                            </div>
                        </div>
                        <a href="javascript:;" class="btn btn-info" onclick="addMore(this)">+</a>
                    </div>

                    <div class="col-12 text-center btn-div">
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
  $('#saveCard').validate({
    rules: {
        title: {
            required: true
        }
    },
    messages: {
      title: {
        required: "{{ __('survey.please_enter_question') }}"
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

function addMore(obj){
    var html = '<div class="input-group mb-3"><input type="text" name="options[]" class="form-control" placeholder="{{ __('survey.options') }}"><div class="input-group-append"><div class="input-group-text"><i class="fas fa-check"></i></div></div><a href="javascript:;" class="btn btn-danger" onclick="removeThis(this)">-</a></div>';

    jQuery('.btn-div').before(html);
}

function removeThis(obj){
    jQuery(obj).parent().remove();
}
</script>
@stop