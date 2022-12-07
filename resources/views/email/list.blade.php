@extends('layouts.backend')

@section('content')
  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>{{ __('emails.email') }}</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="{{ Route('dashboard',session('business_name')) }}">{{ __('keywords.home') }}</a></li>
              <li class="breadcrumb-item active">{{ __('emails.email') }}</li>
            </ol>
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>

    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        <div class="row">
          <div class="col-12">

          <div class="card">
              <div class="card-header">
                <h3 class="card-title">{{ __('emails.email_list') }}</h3> 
                @can('Email Create')
                <button class="btn btn-success btn-sm float-right" data-toggle="modal" data-target="#modal-default">{{ __('emails.compose_new') }}</button>
                @endcan
              </div>
              <!-- /.card-header -->
              @can('Email List View')
              <div class="card-body">
                <table id="datatable" class="table table-bordered table-striped">
                  <thead>
                    <tr>
                        <th>{{ __('emails.subject') }}</th>
                        <th>{{ __('emails.status') }}</th>
                        <th>{{ __('emails.schedule') }}</th>
                        <th>{{ __('emails.created_time') }}</th>
                        @canany(['Email Delete','Email View'])
                        <th>{{ __('emails.action') }}</th>
                        @endcan
                    </tr>
                  </thead>
                  <tbody>
                  @foreach($emails as $key => $value)
                        <tr>
                            <td>
                                {{ $value->subject }}
                            </td>
                            <td class='text-center'>
                                <span class="badge bg-{{ $value->status ? 'success' : 'danger' }}">
                                    {{ $value->status ? __('emails.sent') : __('emails.waiting') }}
                                </span>
                            </td>
                            <td>
                              <span class="v-none">{{ $value->schedule }}</span>{{ \Carbon\Carbon::parse($value->schedule)->format($dateFormat->value.($timeFormat->value == 12 ? ' h:i:s a' : ' H:i:s')) }}
                            </td>
                            <td>
                                {{ \Carbon\Carbon::parse($value->created_at)->format($dateFormat->value.($timeFormat->value == 12 ? ' h:i:s a' : ' H:i:s')) }}  
                            </td>
                            @canany(['Email Delete','Email View'])
                            <td class='text-center'>
                              @can('Email View')
                                <a class="btn btn-info btn-sm" href="{{ Route('viewEmail',array(session('business_name'),md5($value->id))) }}">
                                    <i class="nav-icon fas fa-eye"></i>
                                    {{ __('keywords.view') }}
                                </a>
                              @endcan
                              @if(!$value->status)
                                @can('Email Delete')
                                <button class="btn btn-danger btn-sm" data-id="{{ md5($value->id) }}" onclick="deleteEmail(this)">
                                    <i class="nav-icon fas fa-trash"></i>
                                    {{ __('keywords.delete') }}
                                </button>
                                @endcan
                              @endif
                            </td>
                            @endcan
                        </tr>
                    @endforeach
                  </tbody>
                </table>
              </div>
              @endcan
              <!-- /.card-body -->
            </div>
            <!-- /.card -->
            </div>
          <!-- /.col -->
        </div>
        <!-- /.row -->
      </div>
      <!-- /.container-fluid -->
    </section>
    <!-- /.content -->
</div>

@can('Email Create')
<div class="modal fade" id="modal-default">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">{{ __('emails.compose_email') }}</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form action="{{ Route('addEmail',session('business_name')) }}" method="post" id="saveEmail">
          @csrf

          {{-- <span class="text-warning">{{ __('emails.leiwtsa') }}</span> --}}
          <span class="text-warning">{{ __('emails.select_recipient') }}</span>
          <div class="input-group mb-3">
            <select class="form-control select2 required" name="recipients[]" id="recipients" multiple required="required">          
              @if (count($events)>0)    
              <optgroup label="{{ __('emails.send_to_all_customers_on_the_following_event') }}">
              @endif
                @foreach($events as $event)
                  @php 
                    $userIds = $event->eventActiveSlots->where('parent_slot',NULL)->unique('user_id')->implode('user_id', ', ');
                  @endphp
                    <option value="[{{ $userIds }}]">
                    {{ $event->name }} - {{ \Carbon\Carbon::parse($event->date)->format($dateFormat->value) }} ({{ $event->time }})
                    </option>
                @endforeach
              @if (count($events)>0) 
                </optgroup>
              @endif
              @if (count($dates)>0)
                <optgroup label="{{ __('emails.send_to_all_customers_on_the_following_treatment') }}">
              @endif
                @foreach($dates as $date)
                  @php 
                    $userIds = $date->treatmentSlots->where('parent_slot',NULL)->unique('user_id')->implode('user_id', ', '); 
                  @endphp
                  <option value="[{{ $userIds }}]">
                  {{ \Carbon\Carbon::parse($date->date)->format($dateFormat->value) }} - {{$date->user->name}}
                  </option>
                @endforeach
              @if (count($dates)>0)  
                </optgroup>
              @endif
              @if (count($users)>0)
                <optgroup label="{{ __('emails.all_users') }}">
              @endif    
                @foreach($users as $user)
                    <option value="{{ $user->id }}">
                    {{$user->name}} ( {{$user->email}} )
                    </option>
                @endforeach
              @if (count($users)>0)  
                </optgroup>
              @endif
              <option value="">{{ __('keywords.all') }}</option>
            </select> 
            <div class="input-group-append">
                <div class="input-group-text">
                    <i class="fas fa-users"></i>
                </div>
            </div>
            @if ($errors->has('recipients'))
                <span class="text-danger">{{ $errors->first('recipients') }}</span>
            @endif
          </div>

          <span class="text-warning">{{ __('emails.leiwtsn') }}</span>
          <div class="input-group  mb-3" id="reservationdate" data-target-input="nearest">
                <input type="text" name="schedule" class="form-control datetimepicker-input" data-target="#reservationdate" placeholder="{{ __('emails.schedule') }}" readonly="readonly"/>
                <input type="hidden" name="_schedule"/>
                
                <div class="input-group-append" data-target="#reservationdate" data-toggle="datetimepicker">
                    <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                </div>
                @if ($errors->has('_schedule'))
                    <span class="text-danger">{{ $errors->first('_schedule') }}</span>
                @endif
          </div>

          <div class="input-group mb-3">
              <input type="text" name="subject" id="subject"  value="{{ old('subject') }}" class="form-control" placeholder="{{ __('emails.subject') }}">
              <div class="input-group-append">
                  <div class="input-group-text">
                    <i class="fas fa-quote-right"></i>                      
                  </div>
              </div>
            @if ($errors->has('subject'))
                <span class="text-danger">{{ $errors->first('subject') }}</span>
            @endif
          </div>

          <div class="input-group">
              <textarea name="content" class="form-control textarea" id="content" cols="30" rows="10" placeholder="{{ __('emails.type_your_msg_here') }}" ></textarea>
            @if ($errors->has('content'))
                <span class="text-danger">{{ $errors->first('content') }}</span>
            @endif
          </div>
        </div>
        <div class="modal-footer justify-content-between">
          <button type="button" class="btn btn-default" data-dismiss="modal">{{ __('emails.close') }}</button>
          <button type="submit" class="btn btn-primary">{{ __('emails.save') }}</button>
        </div>
      </form>
    </div>
    <!-- /.modal-content -->
  </div>
  <!-- /.modal-dialog -->
</div>
<!-- /.modal -->
@endcan
@stop

@section('scripts')
<script>

jQuery('#saveEmail').submit(function(){
  jQuery('.modal-footer button[type=submit]').addClass();
});

jQuery(function(){
  jQuery('.textarea').summernote({
      toolbar: [
          [ 'style', [ 'style' ] ],
          [ 'font', [ 'bold', 'italic', 'underline', 'strikethrough', 'superscript', 'subscript', 'clear'] ],
          [ 'fontname', [ 'fontname' ] ],
          [ 'fontsize', [ 'fontsize' ] ],
          [ 'color', [ 'color' ] ],
          [ 'para', [ 'ol', 'ul', 'paragraph', 'height' ] ],
          [ 'table', [ 'table' ] ],
          [ 'insert', [ 'link'] ],
          [ 'view', [ 'undo', 'redo', 'fullscreen', 'codeview', 'help' ] ]
      ]
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
  $('#saveEmail').validate({
    rules: {
        subject: {
            required: true
        },
        content: {
            required: true
        },
        recipients:{
          required: true
        }

    },
    messages: {
        subject: {
            required: '{{ __("emails.please_fill_subject_of_email") }}'
        },
        content: {
            required: '{{ __("emails.please_write_your_message") }}'
        },
        recipients: {
            required: '{{ __("emails.select_min_one_recipient") }}'
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

@can('Email Delete')
function deleteEmail(obj){
  //--------- For notification -----
  const Toast = Swal.mixin({
      toast: true,
      position: 'top-end',
      showConfirmButton: false,
      timer: 3000
  });

  var id = jQuery(obj).attr('data-id');
  var token = $('meta[name="csrf-token"]').attr('content');

  $.ajax({
      type: 'POST',
      url: '/deleteEmail',
      data: { 'id':id,'_token':token },
      dataType: 'json',
      success: function (data) {
        if(data == 'success'){
            Toast.fire({
              icon: 'success',
              title: ' {{ __("emails.email_has_been_deleted") }}'
          });
          jQuery(obj).closest('tr').remove();
        }
        else if(data == 'sent'){
            Toast.fire({
              icon: 'error',
              title: ' {{ __("emails.eascnbdn") }}'
            });
        }
        else{
            Toast.fire({
              icon: 'error',
              title: ' {{ __("emails.tiaetde") }}'
            });
        }
      },
      error: function (data) {
        Toast.fire({
              icon: 'error',
              title: ' {{ __("emails.tiaetde") }}'
          })
      }
    });
}
@endcan
</script>

@stop