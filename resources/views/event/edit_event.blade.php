@extends("layouts.backend")

@section('content')

<div class="content-wrapper"> 

    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>{{ __('event.edit_event') }}</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="{{ Route('dashboard',session('business_name')) }}">{{ __('keywords.home') }}</a></li>
              <li class="breadcrumb-item active">{{ __('event.edit_event') }}</li>
            </ol>
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>

    <div class="row"> 
        <div class="col-sm-3"></div>  
        <div class="col-md-6 col-sm-12">
        <form action="{{ Route('updateEvent',session('business_name')) }}" method="post" id="saveEvent">
            <input type="hidden" name="id" value="{{ $event->id }}">
            @csrf
            <div class="card card-info">
                    <div class="card-header">
                        <h3 class="card-title">{{ __('event.edit_event') }}</h3>
                    </div>
                    
                    <div class="card-body">
                        <!-- Name -->
                        <div class="input-group mb-3">
                            <label style="width: 100%;">{{ __('keywords.name') }}:</label>
                            <div class="input-group-append">
                                <div class="input-group-text">
                                    <span class="fas fa-tags"></span>
                                </div>
                            </div>
                            <input type="text" class="form-control" name="name" id="name" value="{{ $event->name }}">
                            @if ($errors->has('name'))
                                <span class="text-danger">{{ $errors->first('name') }}</span>
                            @endif
                        </div> 
                        <!-- Date -->
                        <div class="form-group">
                            <label>Date:</label>
                            <div class="input-group">
                                <div class="input-group-append" data-target="#date_nolimit" data-toggle="datetimepicker">
                                    <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                </div>
                                <input type="text" name="date" class="form-control float-right" id="date_nolimit" value="{{ $date }}" readonly="readonly">
                                <input type="hidden" name="_date" id="_date" value="{{ $event->date }}">
                                
                            </div>
                        </div>
                        <!-- Clips -->
                        @if($settings->value == 'true')
                            <div class="input-group mb-3">
                                <label style="width: 100%;">{{ __('event.clips') }}:</label>
                                <div class="input-group-append">
                                    <div class="input-group-text">
                                        <i class="fas fa-tags"></i>
                                    </div>
                                </div>
                                <input type="text" name="clips" value="{{ $event->clips }}" class="form-control" placeholder="{{ __('event.clips') }}">
                            @if ($errors->has('clips'))
                                <span class="text-danger">{{ $errors->first('clips') }}</span>
                            @endif
                            </div>
                        @else
                            <input type="hidden" name="clips" value="{{ $event->clips }}" >
                        @endif      
                        <!-- Pice -->
                        <div class="input-group mb-3">
                            <label style="width: 100%;">{{ __('event.price') }}:</label>
                            <div class="input-group-append">
                                <div class="input-group-text">
                                    <i class="fas fa-money-bill-wave"></i>
                                </div>
                            </div>
                            <input type="text" name="price" value="{{ $event->price }}" class="form-control" placeholder="{{ __('event.price') }}">
                        @if ($errors->has('price'))
                            <span class="text-danger">{{ $errors->first('price') }}</span>
                        @endif
                        </div> 
                        <!-- Time -->
                        <div class="input-group mb-3">
                            <label style="width: 100%;">{{ __('event.time') }}:</label>
                            <div class="input-group-append">
                                <div class="input-group-text">
                                    <span class="fas fa-clock"></span>
                                </div>
                            </div>
                            <select class="form-control select2bs4" name="time" id="time" >
                                <x-time-range selected="{{ $event->time }}" :booked="[]" />
                            </select> 
                            @if ($errors->has('time'))
                                <span class="text-danger">{{ $errors->first('time') }}</span>
                            @endif
                        </div>  
                        <!-- Duration -->
                        <div class="input-group mb-3">
                            <label style="width: 100%;">{{ __('event.duration') }}:</label>
                            <div class="input-group-append">
                                <div class="input-group-text">
                                    <span class="fas fa-clock"></span>
                                </div>
                            </div>
                            <input type="number" class="form-control" name="duration" id="duration" value="{{ $event->duration }}">
                            @if ($errors->has('duration'))
                                <span class="text-danger">{{ $errors->first('duration') }}</span>
                            @endif
                        </div> 
                        <!-- Slots -->
                        <div class="input-group mb-3">
                            <label style="width: 100%;">{{ __('event.slots') }}:</label>
                            <div class="input-group-append">
                                <div class="input-group-text">
                                    <span class="fas fa-user"></span>
                                </div>
                            </div>
                            <input type="number" class="form-control" name="slots" id="slots" placeholder="{{ __('event.default_1') }}" min='1' value="{{ $event->slots }}">
                            @if ($errors->has('slots'))
                                <span class="text-danger">{{ $errors->first('slots') }}</span>
                            @endif
                        </div> 
                        <!-- Tharapist -->
                        <div class="input-group mb-3">
                            <label style="width: 100%;">{{ __('event.therapist') }}:</label>
                            <div class="input-group-append">
                                <div class="input-group-text">
                                    <span class="fas fa-user"></span>
                                </div>
                            </div>
                            <select class="form-control select2bs4" name="tharapist" id="tharapist" >
                            @foreach($therapists as $therapist)
                                <option value="{{ $therapist->id }}" {{ ($event->user_id == $therapist->id ? 'selected' : '') }}>{{$therapist->name}}</option>
                            @endforeach
                            </select> 
                            @if ($errors->has('tharapist'))
                                <span class="text-danger">{{ $errors->first('tharapist') }}</span>
                            @endif
                        </div>

                        <!-- Slots -->
                        <div class="input-group mb-3">
                            <label style="width: 100%;">{{ __('event.min_bookings') }}:</label>
                            <div class="input-group-append">
                                <div class="input-group-text">
                                    <span class="fas fa-users"></span>
                                </div>
                            </div>
                            <input type="number" class="form-control" name="min_bookings" id="min_bookings" placeholder="{{ __('event.min_bookings') }}" value="{{ $event->min_bookings ?: 0 }}">
                            @if ($errors->has('min_bookings'))
                                <span class="text-danger">{{ $errors->first('min_bookings') }}</span>
                            @endif
                        </div> 
                       
                        <!-- Description-->
                        <div class="form-group">
                            <label>{{ __('event.description') }}:</label>
                            <textarea class="form-control" name="desc" id="desc" value="{{ $event->description }}">{{ $event->description }}</textarea>                     
                        </div> 

                        <!-- Guests -->
                        <div class="form-group">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" name="guest" id="exampleCheck1" class="custom-control-input" {{ $event->is_guest ? 'checked' : '' }}>
                                <label class="custom-control-label" for="exampleCheck1">Guests Allowed</label>
                            </div>
                        </div>

                    </div>
                    <div class="card-footer">
                    <button type="submit" class="btn btn-info">{{ __('keywords.save') }}</button>
                    </div>
                </form>
            </div>
            <!-- /.card -->
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
  $('#saveEvent').validate({
    rules: {
        id: {
            required: true
        },
        date: {
            required: true
        },
        time: {
            required: true
        },
        name:{
            required: true
        },
        duration: {
            required: true
        },
        slots: {
            required: true
        },
        tharapist: {
            required: true
        }
    },
    messages: {
        name: {
            required: "{{ __('event.pgantte') }}"
        },
        date: {
            required: "{{ __('event.psdf') }}"
        },
        time:{
            required: "{{ __('event.psat') }}"
        },
        duration: {
            required: "{{ __('event.pptdote') }}"
        },
        slots: {
            required: "{{ __('event.pghmswbfte') }}"
        },
        tharapist: {
            required: "{{ __('event.psat') }}"
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