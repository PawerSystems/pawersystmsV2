@extends("layouts.backend")

@section('content')

<div class="content-wrapper">
    
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>{{ __('treatment.create_treatment') }}</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="{{ Route('dashboard',session('business_name')) }}">{{ __('keywords.home') }}</a></li>
              <li class="breadcrumb-item active">{{ __('treatment.create_treatment') }}</li>
            </ol>
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>

    <div class="col-md-6" style="margin:auto; padding-top:30px;">
        <div class="card">
            <div class="card-body register-card-body">
                <p class="login-box-msg">{{ __('treatment.create_treatment') }}</p>
                <form action="{{ Route('addTreatment',session('business_name')) }}" method="post" id="saveBusiness">
                    @csrf
                    <div class="input-group mb-3">
                        <label style="width: 100%;">{{ __('treatment.treatment_name') }}:</label>
                        <input type="text" name="name" value="{{ old('name') }}" class="form-control" placeholder="{{ __('treatment.treatment_name') }}">
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <i class="fas fa-shield-alt"></i>
                            </div>
                        </div>
                    @if ($errors->has('name'))
                        <span class="text-danger">{{ $errors->first('name') }}</span>
                    @endif
                    </div>

                  @if($settings->value == 'true')
                    <div class="input-group mb-3">
                        <label style="width: 100%;">{{ __('treatment.clips') }}:</label>
                        <input type="text" name="clips" class="form-control" placeholder="{{ __('treatment.clips') }}" value="1">
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <i class="fas fa-tags"></i>
                            </div>
                        </div>
                    @if ($errors->has('clips'))
                        <span class="text-danger">{{ $errors->first('clips') }}</span>
                    @endif
                    </div>
                  @else
                    <input type="hidden" name="clips" value="1">
                  @endif  

                    <div class="input-group mb-3">
                      <label style="width: 100%;">{{ __('treatment.price') }}:</label>
                        <input type="text" name="price" value="{{ old('price') }}" class="form-control" placeholder="{{ __('treatment.price') }}">
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <i class="fas fa-money-bill-wave"></i>
                            </div>
                        </div>
                    @if ($errors->has('price'))
                        <span class="text-danger">{{ $errors->first('price') }}</span>
                    @endif
                    </div>

                    <div class="input-group mb-3">
                      <label style="width: 100%;">{{ __('treatment.interval') }}:</label>
                        <select name="interval" id="interval" class="form-control">
                        <option value="">-- {{ __('treatment.interval') }} --</option>
                          
                            @for( $i = $business->time_interval; $i <= 480; $i += $business->time_interval ) 
                                <option value="{{ $i }}">{{ $i }}</option>
                            @endfor
                        </select>
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-clock"></span>
                            </div>
                        </div>

                      @if ($errors->has('interval'))
                          <span class="text-danger">{{ $errors->first('interval') }}</span>
                      @endif
                    </div>

                    <div class="input-group mb-3">
                      <label style="width: 100%;">{{ __('treatment.time_shown') }}:</label>
                        <input type="text" name="time_shown" id="time_shown" class="form-control">
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-clock"></span>
                            </div>
                        </div>
                        <i style="width: 100%;">*{{ __('treatment.note') }}</i>
                    </div>
                   
                    <div class="input-group mb-3">
                      <label style="width: 100%;">{{ __('treatment.description') }}:</label>
                        <textarea style="border: 1px solid #ced4da;" rows="6" name="desc" id="desc" class="form-control"></textarea>
                    </div>

                    <div class="form-group">
                      <div class="custom-control custom-checkbox">
                      <input type="checkbox" name="visible" class="custom-control-input" id="visible" checked>
                      <label class="custom-control-label" for="visible">{{ __('treatment.visible') }}</label>
                      </div>
                    </div>
                    
                    <div class="form-group">
                      <div class="custom-control custom-checkbox">
                      <input type="checkbox" name="insurance" class="custom-control-input" id="insurance">
                      <label class="custom-control-label" for="insurance">{{ __('treatment.insurance') }}</label>
                      </div>
                    </div>

                    <div class="col-4 offset-4">
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
  $('#saveBusiness').validate({
    rules: {
        name: {
            required: true
        },
        interval: {
            required: true
        },
        clips: {
            required: true
        },
        price: {
            required: true
        },
    },
    messages: {
      name: {
        required: "{{ __('treatment.please_enter_treatment_name') }}"
      },
      interval: {
        required: "{{ __('treatment.please_select_an_interval') }}"
      },
      clips: {
        required: "{{ __('treatment.please_give_number_of_clips') }}"
      },
      price: {
        required: "{{ __('treatment.please_give_price') }}"
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