@extends("layouts.backend")

@section('content')

<div class="content-wrapper">
    
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>{{ __('card.create_card') }}</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="{{ Route('dashboard',session('business_name')) }}">{{ __('keywords.home') }}</a></li>
              <li class="breadcrumb-item active">{{ __('card.create_card') }}</li>
            </ol>
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>

    <div class="col-md-4" style="margin:auto; padding-top:30px;">
        <div class="card">
            <div class="card-body register-card-body">
                <p class="login-box-msg">{{ __('card.create_card') }}</p>
                <form action="{{ Route('createCard',session('business_name')) }}" method="post" id="saveCard">
                    @csrf
                    <div class="input-group mb-3">
                        <label style="width: 100%;">{{ __('card.card_title') }}:</label>
                        <input type="text" name="name" value="{{ old('name') }}" class="form-control" placeholder="{{ __('card.card_title') }}">
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <i class="fas fa-shield-alt"></i>
                            </div>
                        </div>
                        @if ($errors->has('name'))
                            <span class="text-danger">{{ $errors->first('name') }}</span>
                        @endif
                    </div>

                    <div class="input-group mb-3">
                        <label style="width: 100%;">{{ __('card.user') }}:</label>
                        <select name="user_id" id="user_id" class="form-control select2bs4">
                        <option value="">-- {{ __('card.user') }} --</option>
                            @foreach($users as $user) 
                                <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                            @endforeach
                        </select>
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-user"></span>
                            </div>
                        </div>

                        @if ($errors->has('user_id'))
                            <span class="text-danger">{{ $errors->first('user_id') }}</span>
                        @endif
                    </div>

                    <div class="input-group mb-3">
                      <label style="width: 100%;">{{ __('card.card_type') }}:</label>
                      <select name="type" id="type" class="form-control select2bs4">
                        <option value="">-- {{ __('card.type') }} --</option>
                        <option value="1">{{ __('card.treatment') }}</option>
                        <option value="2">{{ __('card.event') }}</option>
                            
                        </select>
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-credit-card"></span>
                            </div>
                        </div>

                        @if ($errors->has('type'))
                            <span class="text-danger">{{ $errors->first('type') }}</span>
                        @endif
                    </div>

                    <div class="input-group mb-3">
                        <div class="input-group">
                        <label style="width: 100%;">{{ __('card.expiry_date') }}:</label>
                        <input type="text" name="expiry_date" class="form-control float-left" id="date" placeholde="{{ __('card.expiry_date') }}" readonly="readonly">
                        <input type="hidden" name="_date"  id="_date">
                                <div class="input-group-append" data-target="#date" data-toggle="datetimepicker">
                                    <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                </div>
                            </div>
                        @if ($errors->has('_date'))
                            <span class="text-danger">{{ $errors->first('_date') }}</span>
                        @endif
                    </div>

                    <div class="input-group mb-3">
                        <label style="width: 100%;">{{ __('card.clips_amount') }}:</label>
                        <input type="number" name="clips" value="{{ old('clips') }}" class="form-control" min="0" placeholder="{{ __('card.clips_amount') }}:">
                        <div class="input-group-append">
                            <div class="input-group-text">
                            <i class="fas fa-sort-numeric-up-alt"></i>
                            </div>
                        </div>
                        @if ($errors->has('clips'))
                            <span class="text-danger">{{ $errors->first('clips') }}</span>
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
  $('#saveCard').validate({
    rules: {
        name: {
            required: true
        },
        expiry_date: {
            required: true
        },
        user_id: {
            required: true
        },
        type: {
            required: true
        },
    },
    messages: {
      name: {
        required: "{{ __('card.pecn') }}:"
      },
      expiry_date: {
        required: "{{ __('card.psedoc') }}:"
      },
      user_id: {
        required: "{{ __('card.psau') }}:"
      },
      type: {
        required: "{{ __('card.psct') }}:"
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