@extends('layouts.backend')

@section('content')

@php $cpr = $mdr = 0; @endphp
@foreach($settings as $setting)

  @if($setting->key == 'cpr_emp_fields')
      @if($setting->value == 'true')
          @php $cpr = 1; @endphp
      @else
          @php $cpr = 0; @endphp
      @endif 
  @endif

  @if($setting->key == 'mdr_field')
      @if($setting->value == 'true')
          @php $mdr = 1; @endphp
      @else
          @php $mdr = 0; @endphp
      @endif 
  @endif

@endforeach

<style>
.timeline>div>.timeline-item>.timeline-body>img { margin:0; }
.editable{ display:none; }
.timeline-body{ padding:10px; }
.invalid-feedback, .text-danger {
    width: 65%;
    margin: 0 auto;
}
.timeline-body.text-center{ text-align:center !important; }
.timeline-body.text-center img{ margin:0 auto !important; }
</style>
<div class="content-wrapper"> 

    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>{{ __('journal.journal') }}</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="{{ Route('dashboard',session('business_name')) }}">{{ __('keywords.home') }}</a></li>
              <li class="breadcrumb-item active">{{ __('journal.journal') }}</li>
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

            <!-- Profile Image -->
            <div class="card card-primary card-outline">
              <div class="card-body box-profile">
                <div class="text-center">
                  <img class="profile-user-img img-fluid img-circle"
                       src="/images/{{ $user->profile_photo_path ?: 'avatar5.png' }}"
                       alt="{{ __('keywords.user_profile_picture') }}">
                </div>

                <h3 class="profile-username text-center">{{ $user->name }}</h3>

                <p class="text-muted text-center">{{ $user->role }}</p>

                <form id="updateProfile" class="form-horizontal" method="POST" action="{{ route('updatecustomer',Session('business_name')) }}" enctype="multipart/form-data">
                  @csrf
                  
                  <input type="hidden" name="id" value="{{$user->id}}">
                  <input type="hidden" name="number" value="{{$user->number}}">
                  <input type="hidden" name="language" value="{{$user->language}}">
                  <input type="hidden" name="gender" value="{{$user->gender}}">
                  <input type="hidden" name="birthYear" value="{{$user->birth_year}}">
                  <input type="hidden" name="country" value="{{$user->country_id}}">
                  <input type="hidden" name="is_therapist" value="{{$user->is_therapist}}">
                  <input type="hidden" name="role" value="{{$user->role}}">

                  <div class="form-group row">
                    <label for="inputName" class="col-sm-3 col-form-label">{{ __('keywords.name') }}</label>
                    <div class="col-sm-9">
                      <input type="text" value="{{$user->name}}" name="name" class="form-control" id="inputName" placeholder="Name">
                    </div>
                  </div>
                  
                  <div class="form-group row">
                    <label for="inputEmail" class="col-sm-3 col-form-label">{{ __('keywords.email') }}</label>
                    <div class="col-sm-9">
                      <input type="email" value="{{$user->email}}" name="email" class="form-control" id="inputEmail" placeholder="Email">
                    </div>
                  </div>
                  
                  @if($cpr)
                    <div class="form-group row">
                      <label for="inputCPR" class="col-sm-3 col-form-label">{{ __('customer.cpnr') }}</label>
                      <div class="col-sm-9">
                        <input type="text" value="{{ $user->cprnr }}" name="cprnr" class="form-control" id="inputCPR" placeholder="{{ __('customer.cpnr') }}">
                      </div>
                    </div>
                  @else
                    <input type="hidden" name="cprnr" value="{{ $user->cprnr }}">  
                  @endif
                  @if($mdr)
                    <div class="form-group row">
                      <label for="inputMEDR" class="col-sm-3 col-form-label">{{ __('customer.mednr') }} </label>
                      <div class="col-sm-9">
                        <input type="text" value="{{ $user->mednr }}" name="mednr" class="form-control" id="inputMEDR" placeholder="{{ __('customer.mednr') }}">
                      </div>
                    </div>
                  @else
                    <input type="hidden" name="mednr" value="{{ $user->mednr }}">   
                  @endif

                  <div class="form-group row">
                    <div class="col-sm-12">
                      <button type="submit" class="btn btn-block btn-info float-right">{{ __('keywords.save') }}</button>
                    </div>
                  </div>
                </form>
              </div>
              <!-- /.card-body -->
            </div>
            <!-- /.card -->
          </div>
          <!-- /.col -->
          <div class="col-md-9">
            <div class="card">
              <div class="card-header p-2">
                <ul class="nav nav-pills">
                @can('Journal Payment/Treatment Update')
                  <li class="nav-item"><a class="nav-link @can('Journal Payment/Treatment Update') active @endcan" href="#treatments" data-toggle="tab"><strong><i class="fas fa-book mr-1"></i> {{ __('journal.treatments') }}</strong></a></li>
                @endcan
                @can('Journal Notes View')
                  <li class="nav-item"><a class="nav-link @cannot('Journal Payment/Treatment Update') active @endcan" href="#notes" data-toggle="tab"><strong><i class="far fa-file-alt mr-1"></i> {{ __('journal.journal') }}</strong></a></li>
                @endcan 
                @can('Journal Notes Create') 
                  <li class="nav-item"><a class="nav-link @cannot('Journal Notes View') active @endcan" href="#addnew" data-toggle="tab"><strong><i class="fas fa-pencil-alt mr-1"></i> {{ __('journal.add_note') }}</strong></a></li>
                @endcan                 
                </ul>
              </div><!-- /.card-header -->
              <div class="card-body">
                <div class="tab-content">
                @can('Journal Notes View')
                  <div class="tab-pane @cannot('Journal Payment/Treatment Update') active @endcan" id="notes">
                  @foreach($notes as $note)
                    <!-- The timeline -->
                    <div class="timeline timeline-inverse">
                      <!-- timeline time label -->
                      <div class="time-label">
                        <span class="bg-danger">
                          {{ \Carbon\Carbon::parse($note->created_at)->format($dateFormat->value) }}
                        </span>
                      </div>
                      <!-- /.timeline-label -->
                      <!-- timeline item -->
                      <div>
                        <i class="fas fa-envelope bg-primary"></i>

                        <div class="timeline-item">
                          <span class="time">{{ __('journal.last_updated') }} <i class="far fa-clock"></i> {{ \Carbon\Carbon::parse($note->updated_at)->format($dateFormat->value.' H:i') }}</span>

                          <h3 class="timeline-header"><a href="javascript:;">
                            {{ $note->user ? $note->user->name : 'N/A' }}</a> {{ __('journal.add_this_comment') }} 
                          @if($note->update_user_id )
                            <b>|</b> <a href="javascript:;" class="updateComment">{{ $note->uUser ? $note->uUser->name : 'N/A' }}</a> {{ __('journal.update_this_comment') }}
                          @else 

                          @endif
                          </h3>
                          <form name="form-id-{{$note->id}}" onsubmit="saveComment(this)">
                            <div class="timeline-body">
                            @csrf
                              <input type="hidden" name="comment_id" value="{{ $note->id }}">
                              <div class="noeditable">{!! $note->comment !!}</div>
                              <textarea name="comment_note" class="form-control editable" value="{{ $note->comment }}">{{ $note->comment }}</textarea>
                            </div>
                            <div class="timeline-footer">
                            @can('Journal Notes Edit')
                            <a href="javascript:;" class="btn btn-warning btn-block noeditable" onclick="editable(this)">{{ __('keywords.edit') }}</a>
                            <button type="submit" class="btn btn-primary btn-block editable">{{ __('keywords.update') }}</button>
                            @endcan
                            </div>
                          </form>
                        </div>
                      </div>
                      <!-- END timeline item -->
                      @if($note->image)
                      <!-- timeline item -->
                      <div>
                        <i class="fas fa-camera bg-purple"></i>

                        <div class="timeline-item">
                          <span class="time"><i class="far fa-clock"></i> {{ \Carbon\Carbon::parse($note->created_at)->format($dateFormat->value.' H:i') }}</span>
                          <h3 class="timeline-header"><a href="#">{{ $note->user ? $note->user->name : 'N/A' }}</a> {{ __('journal.uploaded_this_file') }}</h3>

                          <div class="timeline-body text-center">
                          @php
                            $info = pathinfo(public_path().'/journal/'.$note->image,PATHINFO_EXTENSION);
                          @endphp

                          @if($info == 'pdf')
                              @php $path = '/images/pdf.png'; @endphp
                          @else
                            @php $path = '/journal/'.$note->image; @endphp
                          @endif  

                          <img class="img-fluid d-block" src="{{ $path }}" alt="Attached File">
                          <a href="/journal/{{$note->image}}" class="btn btn-info btn-md mt-2" download>{{ __('keywords.download') }}</a>
                          <a href="/journal/{{$note->image}}" target="__blank" class="btn btn-success btn-md mt-2">{{ __('keywords.view') }}</a>
                          </div>

                        </div>
                      </div>
                      <!-- END timeline item -->
                      @endif
                      <div>
                        <i class="far fa-clock bg-gray"></i>
                      </div>
                    </div>
                  @endforeach  
                  </div>
                @endcan  
                  <!-- /.tab-pane -->
                @can('Journal Notes Create') 
                  <div class="tab-pane @cannot('Journal Notes View') active @endcan" id="addnew">
                    <form class="form-horizontal" action="{{ Route('addJournal',session('business_name')) }}" method="post" id="saveJournal" enctype="multipart/form-data" onsubmit="checkComment(this)">
                      @csrf
                      <input type="hidden" name="customer_id" value="{{ $user->id }}">

                      <div class="form-group row">
                        <label for="inputExperience" class="col-sm-2 col-form-label">{{ __('journal.comment') }}</label>
                        <div class="col-sm-10">
                          <textarea class="form-control textarea" id="comment" value="{{ old('comment') }}" name="comment" placeholder="{{ __('journal.comment') }}">
                            @if($notes->count() > 0 )
                              {{ old('comment') }}
                            @else
                              {!! __('journal.first_note') !!}
                            @endif
                          </textarea>
                        </div>
                        @if ($errors->has('comment'))
                        <span class="text-danger">{{ $errors->first('comment') }}</span>
                        @endif
                      </div>

                      <div class="form-group row"> 
                      <label for="inputExperience" class="col-sm-2 col-form-label">{{ __('journal.ref_file') }}</label>
                        <div class="input-group col-sm-10 pull-right">
                          <div class="custom-file">
                            <input type="file" class="custom-file-input" id="customFile" name="image">
                            <label class="custom-file-label" for="customFile"> {{ __('journal.image_pdf') }}</label>
                          </div>
                        </div>
                        @if ($errors->has('image'))
                            <span class="text-danger">{{ $errors->first('image') }}</span>
                        @endif
                      </div>
                      
                      <div class="form-group row">
                        <div class="offset-sm-2 col-sm-10">
                          <button type="submit" class="btn btn-danger">{{ __('journal.submit') }}</button>
                        </div>
                      </div>
                    </form>
                  </div>
                @endcan  
                  <!-- /.tab-pane -->
                @can('Journal Payment/Treatment Update')
                  <div class="tab-pane @can('Journal Payment/Treatment Update') active @endcan" id="treatments">
                    <table id="datatable" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>{{ __('journal.sr') }}.#</th>
                                <th>{{ __('journal.date') }} - {{ __('journal.time') }}</th>
                                <th>{{ __('keywords.name') }}</th>
                                <th>{{ __('journal.payment') }}</th>
                                <th>{{ __('journal.treatment') }}</th>
                                <th>{{ __('journal.action') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                        @php $count = 1; @endphp

                        @foreach($dates as $date)

                            @foreach($date->treatmentSlots->where('user_id',$user->id)->where('parent_slot',NULL)->where('is_active',1) as $treatment )
                                <tr id="slot-{{ $treatment->id }}">
                                    <td>{{ $count }}</td>
                                    <td>
                                        {{ \Carbon\Carbon::parse($date->date)->format($dateFormat->value) }} - {{ $treatment->time }}                                
                                    </td>
                                    <td class='text-center'>
                                        {{ $user->name }}
                                    </td>
                                    <td class='text-center'>
                                        <select name="payment" class="form-control payment">
                                            <x-payment-methods selected="{{ $treatment->payment_method_id }}" />
                                        </select>
                                    </td>
                                    <td class='text-center'>
                                        <select name="part" class="form-control part">
                                            <x-treatment-part selected="{{ $treatment->treatment_part_id}}" />
                                        </select>
                                    </td>
                                    <td class='text-center'>
                                        <button class="btn btn-info btn-sm" data-id="{{ $treatment->id }}" onclick="updateSlot(this)">
                                            <i class="nav-icon fas fa-edit"></i>
                                            {{ __('keywords.update') }}
                                        </button>
                                    </td>
                                </tr>
                                @php $count++; @endphp
                            @endforeach
                        @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <th>{{ __('journal.sr') }}.#</th>
                                <th>{{ __('journal.date') }} - {{ __('journal.time') }}</th>
                                <th>{{ __('keywords.name') }}</th>
                                <th>{{ __('journal.payment') }}</th>
                                <th>{{ __('journal.treatment') }}</th>
                                <th>{{ __('journal.action') }}</th>
                            </tr>
                        </tfoot>
                    </table>
                  </div>
                @endcan
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
@stop

@section('scripts')
<script>
@can('Journal Payment/Treatment Update')
function updateSlot(obj){

    const Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000
    });

    var id = jQuery(obj).attr('data-id');
    var token = $('meta[name="csrf-token"]').attr('content');
    var payment = '';
    var part = '';
    //----- check if fields in this Tr or in parent Tr -------//
    payment = jQuery(obj).parentsUntil('tr').find('select.payment').val();
    part = jQuery(obj).parentsUntil('tr').find('select.part').val();
    
    if(payment == undefined){
      payment = jQuery('tr#slot-'+id).find('select.payment').val();
    }
    if(part == undefined){
      part = jQuery('tr#slot-'+id).find('select.part').val();
    }

    $.ajax({
      type: 'POST',
      url: '/updatePaymentPartAjax',
      data: { 'id':id,'part':part,'payment':payment,'_token':token },
      dataType: 'json',
      success: function (data) {
        if(data == 'success'){
          Toast.fire({
              icon: 'success',
              title: ' {{ __("journal.dhbus") }}!!'
          });
        }
        else{
          Toast.fire({
              icon: 'error',
              title: ' {{ __("journal.tsauetud") }}'
          })
        }
      },
      error: function (data) {
        Toast.fire({
              icon: 'error',
              title: '{{ __("journal.pcydatta") }}'
          })
      }
    });
}
@endcan
@can('Journal Notes Create')
function saveComment(obj){

  event.preventDefault();

  const Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000
    });

    var form = jQuery(obj).closest('form');

    $.ajax({
      type: 'POST',
      url: '/updateAjax',
      data: jQuery(form).serialize(),
      dataType: 'json',
      success: function (data) {
        if(data['status'] == 'success'){
          Toast.fire({
              icon: 'success',
              title: ' {{ __("journal.dhbus") }}!!'
          });

          //----- Update user name who update this comment -----
          if(jQuery(obj).parent().find('.timeline-header > .updateComment').length > 0){
            jQuery(obj).parent().find('.timeline-header > .updateComment').text(data['user']);
          }else{
            jQuery(obj).parent().find('.timeline-header').append('<b>|</b> <a href="javascript:;" class="updateComment">'+data['user']+'</a> {{ __("journal.update_this_comment") }}');
          }
          
          //----- Update Time and date -----
          jQuery(obj).parent().find('.time').html(data['time']);
          //---- Roll back editor hide ----
          jQuery(form).find('div.noeditable').html(jQuery(form).find('textarea[name=comment_note]').val());
          jQuery(form).find('textarea.editable').summernote('destroy');
          jQuery(form).find('.noeditable').show();
          jQuery(form).find('.editable').hide();

        }
        else{
          Toast.fire({
              icon: 'error',
              title: ' {{ __("journal.tsauetud") }}'
          })
        }
      },
      error: function (data) {
        Toast.fire({
              icon: 'error',
              title: '{{ __("journal.pcydatta") }}'
          })
      }
    });

    
}
@endcan

function checkComment(obj){
  if(jQuery('#comment').summernote('isEmpty')) {
      jQuery('#comment-error').remove();
      jQuery('#comment').parent().append('<span id="comment-error" style="display: inline;margin-top: .25rem;font-size: 80%;color: #dc3545;">{{ __("journal.comment_is_required") }}</span>');
      event.preventDefault();
    }
  else{
    jQuery('#comment-error').remove();
  }  
}
</script>

<!-- Showing error or success messages -->
@if(Session::get('success'))
<script type="text/javascript">
  jQuery(function() {
		const Toast = Swal.mixin({
		  toast: true,
		  position: 'top-end',
		  showConfirmButton: false,
		  timer: 5000
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
		  timer: 5000
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

  $.validator.addMethod('filesize', function(value, element, param) {
    return this.optional(element) || (element.files[0].size <= param)
  }, 'File size must be less than {0} bytes');

  $.validator.setDefaults({
    submitHandler: function () {
        return true;
    }
  });
  $('#saveJournal').validate({
    rules: {
        comment: {
            required: true
        },
        image: {
            extension: "jpeg,png,jpg,gif,svg,pdf",
            filesize:   2097152 // <= 2mb
        }
    },
    messages: {
      comment: {
        required: "Please enter comment",
      },
      image: {
        extension: "{{ __('journal.fmboote') }}",
        filesize:  "{{ __('journal.issnmt2m') }}"
      }
    },
    errorElement: 'span',
    errorPlacement: function (error, element) {
      error.addClass('invalid-feedback');
      element.closest('.input-group').after(error);
    },
    highlight: function (element, errorClass, validClass) {
      $(element).addClass('is-invalid');
    },
    unhighlight: function (element, errorClass, validClass) {
      $(element).removeClass('is-invalid');
    }
  });
});

@can('Journal Notes Edit')
function editable(obj){
  var form = jQuery(obj).closest('form');
  jQuery(form).find('.noeditable').hide();
  jQuery(form).find('button.editable').show();
  jQuery(form).find('textarea.editable').addClass('textarea');
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
}
@endcan

//---------- Validation for user profile --------
jQuery.validator.addMethod("validCpr", function(value, element) {
  return validate_cpr_number(value);
}, "{{ __('treatment.please_enter_valid_number') }}");

$('#updateProfile').validate({
    rules: {
      @if($cpr)
        cprnr: {
            required: true,
            validCpr: true,
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
        email: {
            required: true,
            email: true
        },
    },
    messages: {
    @if($cpr)
      cprnr: {
          required: "{{ __('users.please_enter_CPR_number') }}",
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
      email: {
        required: "{{ __('profile.ppae') }}",
        minlength: "{{ __('profile.peavea') }}"
      },
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

  jQuery(function () {
    // Summernote
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
    })
  })
</script>
@stop