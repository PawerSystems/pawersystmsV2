@extends("layouts.backend")

@section('content')
@php $cpr = $dateFormat = $timeFormat = $mdr = ''; @endphp
@foreach($settings as $setting)

    @if($setting->key == 'cpr_emp_fields')
        @if($setting->value == 'true')
            @php $cpr = 1; @endphp
        @else
            @php $cpr = 0; @endphp
        @endif 
    
    @elseif($setting->key == 'mdr_field')
        @if($setting->value == 'true')
            @php $mdr = 1; @endphp
        @else
            @php $mdr = 0; @endphp
        @endif 
    
    @elseif($setting->key == 'date_format')
      @php $dateFormat = $setting->value; @endphp

    @elseif($setting->key == 'time_format')
      @php $timeFormat = $setting->value; @endphp 

    @endif

@endforeach

<style>
.btn { min-width:60px; }
.table td, .table th{
  vertical-align: inherit;
}
.card-body.p-0 .table tbody>tr>td:first-of-type, .card-body.p-0 .table tbody>tr>th:first-of-type, .card-body.p-0 .table thead>tr>td:first-of-type, .card-body.p-0 .table thead>tr>th:first-of-type{
  padding-left: 0.7rem !important;
}
ul.p-a{ position:absolute; z-index:99; }
ul.p-a li{ cursor:pointer; }
span.select2{
    width:100% !important;
}
</style>
<div class="content-wrapper"> 

    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>{{ __('event.events') }}</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="{{ Route('dashboard',session('business_name')) }}">{{ __('keywords.home') }}</a></li>
              <li class="breadcrumb-item active">{{ __('event.events') }}</li>
            </ol>
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>

    <!-- Main content -->
    <section class="content event">
      <div class="container-fluid">
        <div class="row">
        @foreach($events as $key => $event)
        <div class="col-md-12" id="table-{{$event->id}}">
          <div class="card">
              <div class="card-header">
              <div class="card-tools">
                  <button type="button" class="btn bg-default btn-sm" data-card-widget="collapse">
                  <i class="fas fa-minus"></i>
                  </button>
              </div>
              <h4> 
                  {{ $event->name}} -  
                  {{  __('keywords.'.\Carbon\Carbon::parse($event->date)->format('l').'') }} 
                  {{ \Carbon\Carbon::parse($event->date )->format($dateFormat)}}
                  {{ $event->time }}
              </h4>
              <p>
                ({{ $event->duration }} min) (<span class='currentBookings'>{{ $event->eventActiveSlots->count() }}</span> {{ __('keywords.out_of') }} {{ $event->slots }} {{ __('keywords.booked') }})<br>{{ $event->description }}
              </p>
              </div>
              <!-- /.card-header -->
              <div class="card-body table-responsive p-0">
              <table class="table table-hover table-bordered text-nowrap">
                  <thead>
                      <tr class="dark">
                          <th>{{ __('event.status') }}</th>
                          <th>{{ __('event.time') }}</th>
                          <th>{{ __('keywords.name') }} </th>
                          <th>{{ __('keywords.email') }} </th>
                          <th>{{ __('keywords.number') }}</th>
                          <th class="clipCard">{{ __('event.cards') }}</th>
                          <th class="cutBack">{{ __('event.cut_back') }}</th>
                          <th class="cutCard">{{ __('event.team_cut_card') }}</th>
                          <th>{{ __('event.ordered') }} </th>
                          <th>{{ __('event.comment') }} </th>
                          @if($event->is_guest)
                          <th>{{ __('event.bring_guest') }} </th>
                          <th>{{ __('event.num_of_guests') }} ({{$event->max_guests.' '.__('event.max')}}) </th>
                          @endif
                          <th>{{ __('event.action') }} </th>
                      </tr>
                  </thead>
                  <tbody>
                    @foreach($event->eventActiveSlots as $slot)
                      @php
                        $card = null;
                        $clip = App\Models\UsedClip::where('event_slot_id',$slot->id)->first();
                        if($clip){
                          $card = App\Models\Card::find($clip->card_id);
                        }
                      @endphp
                   
                      <tr>
                      <td class="{{ ($slot->status ? 'bg-info' : 'bg-warning' ) }} text-center status"><span>{{ ($slot->status ? __('event.booked') : __('event.waiting_list') )}}</span></td>
                      <td>{{ $event->time }}</td>
                      <td>{{ $slot->user->name }}</td>
                      <td>{{ $slot->user->email }}</td>
                      <td><a href="Tel:{{ $slot->user->number }}">{{ $slot->user->number }}</a></td>
                      <td class="text-center clipCard">
                        <select name="card" class="form-control select2" data-time="{{$event->time}}" onchange="cardSelect(this)">
                            <x-cards-list type="2" card="{{ $card != null ? $card->id : $card }}" user="{{$slot->user_id}}" />
                        </select> 
                      </td>
                      <td class="text-center cutBack">@if( $card != Null ) {{$card->clips}} @endif</td>
                      <td class="text-center cutCard" data-id="{{ $slot->id }}" data-event-id="{{ $event->id }}">
                      @if( $card != Null )
                      <button class="btn btn-warning btn-sm" onclick="cutBackClips(this)" data-clip-id="{{$clip->id}}">{{ __('event.undo_clips_in_clipboard') }}</button>
                      @endif
                      </td>
                      <td>{{\Carbon\Carbon::parse($slot->created_at)->format($dateFormat.($timeFormat == 12 ? ' h:i:s a' : ' H:i:s' ))}}</td>
                      <td>{{$slot->comment}}</td>
                      @if($event->is_guest)
                      <td class="guest">{{ $slot->parent_slot ? __('event.guest') : ($slot->is_guest ? __('event.yes') : __('event.no')) }}</td>
                      <td class="max_guests"></td>
                      @endif
                      <td>
                      @can('Event Booking Delete')
                        <button class="btn btn-danger btn-sm" data-id="{{ md5($slot->id) }}" onclick="deleteBooking(this)">{{ __('keywords.delete') }}</button> 
                      @endcan   
                      </td>
                      </tr>
                    @endforeach
                    <tr>
                      <td class="{{ ($event->eventActiveSlots->count() >= $event->slots ? 'bg-gray' : 'bg-success' ) }} text-center status">
                        <form id="form-{{$event->id}}">
                          @csrf
                          <input type="hidden" name="event_id" value="{{ $event->id }}">
                        </form>
                        <span>{{ ($event->eventActiveSlots->count() >= $event->slots ? __('event.waiting_list') : __('event.available') ) }}</span> 
                      </td>
                      <td>{{ $event->time }}</td>
                      <td class="name" style="min-width:200px;">
                        <input type="text" class="form-control" name="name" placeholder="{{ __('keywords.name') }}" event-id="{{ $event->id }}" onkeyup="suggestUser(this)" autocomplete="off">
                        <ul class="list-group p-a" id="name-{{ $event->id }}"></ul>
                      </td>
                      <td class="email" style="min-width:200px;">
                        <input type="email" class="form-control" name="email" placeholder="{{ __('keywords.email') }}" event-id="{{ $event->id }}" onkeyup="suggestUser(this)" autocomplete="off">
                      </td>
                      <td class="number" style="min-width:200px;">
                        <input type="text" class="form-control" name="number" placeholder="{{ __('keywords.number') }}" event-id="{{ $event->id }}" onkeyup="suggestUser(this)" autocomplete="off">
                      </td>
                      <td class="clipCard"></td>
                      <td class="cutBack"></td>
                      <td class="cutCard"></td>                      
                      <td></td>
                      <td class="comment" style="min-width:200px;">
                        <input type="text" class="form-control" name="comment" placeholder="{{ __('event.comment') }}" event-id="{{ $event->id }}" >
                      </td>
                      @if($event->is_guest)
                      <td class="guest">
                        <select class="form-control" name="guest" event-id="{{ $event->id }}" >
                          <option value="0">{{ __('event.no') }}</option>
                          <option value="1">{{ __('event.yes') }}</option>
                        </select>
                      </td>
                      <td class="max_guests">
                        <input type="number" min="0" max="{{ $event->max_guests ?: 1 }}" class="form-control max_guest" name="max_guest" event-id="{{ $event->id }}" >
                      </td>
                      @endif
                      
                      <td class="submit">
                        @can('Event Book')
                          <button type="submit" class="btn btn-info btn-sm" event-id="{{ $event->id }}" onclick="bookEvent(this)">{{ __('event.book') }}</button>
                        @endcan
                      </td>
                      
                    </tr>  
                  </tbody>
              </table>
              </div>
              <!-- /.card-body -->
          </div>
          <!-- /.card -->
        </div>
        @endforeach
        </div>
      </div>  
    </section>    
  </div>  
@can('Customer Create')  
<div id="myModal" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
        <div class="modal-header">
            <h4 class="modal-title">{{ __('event.add_new_customer') }}</h4>
            <button type="button" class="close" data-dismiss="modal">&times;</button>

        </div>
        <div class="modal-body">

        <form method="post" id="saveCustomer">                    
                @csrf
                <input type="hidden" name="formId" id="formId">
                <div class="input-group mb-3">
                    <input type="text" name="cname" value="{{ old('cname') }}" class="form-control" placeholder="{{ __('event.full_name') }}">
                    <div class="input-group-append">
                        <div class="input-group-text">
                            <span class="fas fa-user"></span>
                        </div>
                    </div>
                @if ($errors->has('cname'))
                    <span class="text-danger">{{ $errors->first('cname') }}</span>
                @endif
                </div>

                <div class="input-group mb-3">
                    <input type="email" name="cemail" value="{{ old('cemail') }}" class="form-control" placeholder="{{ __('keywords.email') }}">
                    <div class="input-group-append">
                        <div class="input-group-text">
                            <span class="fas fa-envelope"></span>
                        </div>
                    </div>
                @if ($errors->has('cemail'))
                    <span class="text-danger">{{ $errors->first('cemail') }}</span>
                @endif
                </div>

                <!-- <span class="text-info"><i>{{ __('keywords.please_did_not_remove_country_code') }}</i></span> -->
                <div class="input-group mb-3">
                    <div class="input-group-prepend">
                      <select name="ccountry" class="form-control" style="max-width:200px;">
                      <!-- <option value="45">Denmark</option> -->
                        @foreach($countries as $country)
                          <option value="{{ $country->id }}" @if( $country->name == 'Denmark') selected @endif>{{ $country->name }}</option>
                        @endforeach 
                      </select>
                    </div>
                    <input type="number" name="cnumber" value="{{ old('cnumber') }}" class="form-control" placeholder="{{ __('keywords.number') }}">
                    <div class="input-group-append">
                        <div class="input-group-text">
                            <span class="fas fa-phone"></span>
                        </div>
                    </div>
                @if ($errors->has('cnumber'))
                    <span class="text-danger">{{ $errors->first('cnumber') }}</span>
                @endif
                </div>

                <div class="input-group mb-3">
                <select class="form-control" name="clanguage" id="clanguage">
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
            @if ($errors->has('clanguage'))
                <span class="text-danger">{{ $errors->first('clanguage') }}</span>
            @endif

            @if($cpr)
              <div class="input-group mb-3">
                  <input type="text" name="cprnr" value="{{ old('cprnr') }}" class="form-control" placeholder="{{ __('event.cprnr') }}">
                  <div class="input-group-append">
                      <div class="input-group-text">
                          <span class="fas fa-key"></span>
                      </div>
                  </div>
                @if ($errors->has('cprnr'))
                    <span class="text-danger">{{ $errors->first('cprnr') }}</span>
                @endif
              </div>
            @else
              <input type="hidden" name="cprnr" value="">
            @endif

            @if($mdr)  
              <div class="input-group mb-3">
                  <input type="text" name="mednr" value="{{ old('mednr') }}" class="form-control" placeholder="{{ __('event.mednr') }}">
                  <div class="input-group-append">
                      <div class="input-group-text">
                          <span class="fas fa-key"></span>
                      </div>
                  </div>
                @if ($errors->has('mednr'))
                    <span class="text-danger">{{ $errors->first('mednr') }}</span>
                @endif
              </div> 
            @else
              <input type="hidden" name="mednr" value="">
            @endif
        </div>
        <div class="modal-footer">
            <button type="submit" class="btn btn-info">{{ __('keywords.save') }}</button>
            <button type="button" class="btn btn-default" data-dismiss="modal">{{ __('keywords.close') }}</button>
        </div>
        </form>
        </div>

    </div>
    </div>
</div>
@endcan
 @stop   

 @section('scripts')

 <script type="text/javascript">
$(document).ready(function () {


  jQuery('.select2').select2({
    theme: 'bootstrap4'
  });

   //--------- For notification -----
   const Toast = Swal.mixin({
    toast: true,
    position: 'top-end',
    showConfirmButton: false,
    timer: 3000
  });


  $.validator.setDefaults({
    submitHandler: function () {
        
      $.ajax({
        type: 'POST',
        url: '/addCustomer',
        data: jQuery("#saveCustomer").serialize(),
        dataType: 'json',
        success: function (data) {
          if(data['status'] == 'success'){
              Toast.fire({
                icon: 'success',
                title: ' {{ __('event.nchba') }}'
              });

              //----- add values in customer fields --------
              var name = jQuery("#saveCustomer input[name=cname]").val();
              var email = jQuery("#saveCustomer input[name=cemail]").val();
              var number = jQuery("#saveCustomer input[name=cnumber]").val();
              var thisForm = jQuery("#saveCustomer input[name=formId]").val();
              
              addValues(name,email,number,thisForm,data['cid']);

              jQuery('#myModal').modal('hide');
              jQuery('input[name=cname],input[name=cemail],input[name=cnumber],input[name=cprnr],input[name=mednr],input[name=formId]').val('');

          }
          else if(data['status'] == 'exist'){
              Toast.fire({
                icon: 'error',
                title: ' {{ __('event.cwteal') }}'
              });
          }else{
            Toast.fire({
                icon: 'error',
                title: ' {{ __('event.tiaetac') }}'
              });
          }
        },  
      });
    }
  });


function validate_cpr_number( cpr ) {

  if($.isNumeric(cpr)){

    var d = cpr.substring(0, 2);
    var m = cpr.substring(2, 4);
    var y = cpr.substring(4, 6);
    
    var fullDate = d+'/'+m+'/'+y;

    if(!isDate(fullDate)){
      return false;
    }

    return true;     
  }
  return false;  

}

function isDate(value) {
  var re = /^(?=\d)(?:(?:31(?!.(?:0?[2469]|11))|(?:30|29)(?!.0?2)|29(?=.0?2.(?:(?:(?:1[6-9]|[2-9]\d)?(?:0[48]|[2468][048]|[13579][26])|(?:(?:16|[2468][048]|[3579][26])00)))(?:\x20|$))|(?:2[0-8]|1\d|0?[1-9]))([-.\/])(?:1[012]|0?[1-9])\1(?:1[6-9]|[2-9]\d)?\d\d(?:(?=\x20\d)\x20|$))?(((0?[1-9]|1[012])(:[0-5]\d){0,2}(\x20[AP]M))|([01]\d|2[0-3])(:[0-5]\d){1,2})?$/;
  var flag = re.test(value);
  return flag;
}
//---------- Validation for user profile --------
  jQuery.validator.addMethod("validCpr", function(value, element) {
    return validate_cpr_number(value);
  }, "{{ __('treatment.please_enter_valid_number') }}");

  $('#saveCustomer').validate({
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
        cname: {
            required: true
        },
        cemail: {
            required: true,
            email: true,
        },
        cnumber: {
            required: true,
        },
    },
    messages: {
      @if($cpr)
        cprnr: {
            required: "{{ __('event.please_enter_cpr_number') }}",
            minlength: "{{ __('treatment.please_enter_at_least_10_cha') }}",
            maxlength: "{{ __('treatment.please_enter_at_least_10_cha') }}"
        },
      @endif
      @if($mdr)  
        mednr: {
            required: "{{ __('event.please_enter_med_number') }}"
        },
      @endif
      cname: {
        required: "{{ __('event.please_enter_full_name') }}",
      },
      cemail: {
        required: "{{ __('event.please_enter_a_email_address') }}",
        email: "{{ __('event.please_enter_a_valid_email_address') }}"
      },
      cnumber: {
        required: "{{ __('keywords.please_enter_mobile_number') }}",
        minlength: "{{ __('treatment.please_enter_at_least_8_cha') }}",
        maxlength: "{{ __('treatment.please_enter_at_least_8_cha') }}",
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

jQuery('#saveCustomer').submit(function(){
  var cou = jQuery('select[name=ccountry] option:selected').text();
  jQuery('input[name=cnumber]').rules('remove','minlength');
  jQuery('input[name=cnumber]').rules('remove','maxlength');
    if(cou == 'Denmark'){
      jQuery('input[name=cnumber]').rules('add',{
        minlength:8,
        maxlength:8
      });
    }
});

function suggestUser(obj){
  var html = '';
  var search = jQuery(obj).val();
  var eventID = jQuery(obj).attr('event-id');
  var ulID = 'name-'+eventID;
  jQuery("ul#"+ulID).html('');

  if( search.length > 2 ){
    $.ajax({
      type: 'POST',
      url: '/getUserDataAjax',
      data: {"_token":"{{ csrf_token() }}",'search':search},
      dataType: 'json',
      success: function (users) {
        if(!$.trim(users)){
            jQuery('#myModal #saveCustomer #formId').val(jQuery(obj).closest('tr').find('td.name ul').attr('id'));
            jQuery('#myModal').modal('show');
        }else{
          jQuery.each(users, function(index, value){
            var u = value["id"];
            var n = value["name"];
            var e = value["email"];
            var o = value["number"];
            var i = ulID;
              html += '<li class="list-group-item" onclick="addValues(`'+n+'`,`'+e+'`,`'+o+'`,`'+i+'`,`'+u+'`)" >'+value["name"]+'('+value["email"]+')</li>';
          });
          html += '<li class="list-group-item" data-toggle="modal" data-target="#myModal">{{ __('event.add_new') }}</li>';
          jQuery("ul#"+ulID).html(html);
        }
      },  
    });
  }
}


function addValues(name,email,number,id,userId){
  jQuery('#'+id).html('');
  var tr = jQuery('#'+id).closest('tr');
  jQuery(tr).find('td').each(function(){

    if(jQuery(this).hasClass('status')){
      var form = jQuery(this).find('form');
      if(jQuery(form).find('input[name=user_id]').val())
        jQuery(form).find('input[name=user_id]').val(userId);
      else
        jQuery(form).append("<input type='hidden' name='user_id' value='"+userId+"'>");
    }

    if(jQuery(this).hasClass('name')){
        jQuery(this).find('input').val(name);
    }
    if(jQuery(this).hasClass('email')){
        jQuery(this).find('input').val(email);
    }
    if(jQuery(this).hasClass('number')){
        jQuery(this).find('input').val(number);
    }
  });
}


@can('Event Book')
function bookEvent(obj){
  
  //--------- For notification -----
  const Toast = Swal.mixin({
    toast: true,
    position: 'top-end',
    showConfirmButton: false,
    timer: 3000
  });

  //--------- Getting data from button to get form id -----
  var eventID = jQuery(obj).attr('event-id');
  var FormID = 'form-'+eventID;
  var comment = guest = maxGuest = '';

  //--------- Getting comment AND treatment of current data -----
  var tr = jQuery(obj).closest('tr');
  jQuery(tr).find('td').each(function(){
      if(jQuery(this).hasClass('comment')){
        comment =  jQuery(this).find('input').val();
      }
      if(jQuery(this).hasClass('guest')){
        guest =  jQuery(this).find('select').val();
      }
      if(jQuery(this).hasClass('max_guests')){
        maxGuest =  jQuery(this).find('.max_guest').val();
      }
  });
  //----------- CSRF token ------------
  var token = $('meta[name="csrf-token"]').attr('content');

  //--------- submit fom with data now -----
  $.ajax({
      type: 'POST',
      url: '/BookEvent',
      data: jQuery("#"+FormID).serialize()+"&guest="+guest+"&comment="+comment+"&maxGuest="+maxGuest+"&_token="+token,
      dataType: 'json',
      success: function (data) {
        console.log(data);
        if(data['status'] == 'success'){

          Toast.fire({
              icon: 'success',
              title: ' {{ __('event.shbbs') }}'
          });

          //-------- Now updating slots ----
          var tr = jQuery(obj).closest('tr');
          if(data['label'] == 0)
            var label = '{{ __('event.waiting_list') }}';

          jQuery(data['bookings']).insertBefore(tr);
          jQuery(tr).find('td').each(function(){
              if(jQuery(this).hasClass('status')){
                //---- change lable ----//
                jQuery(this).find('span').text(label);
                //---- change field color if needed ----//
                if(label == '{{ __('event.waiting_list') }}')
                  jQuery(this).addClass('bg-gray');
                //---- delete user id from form ----//
                jQuery(this).find('input[name=user_id]').val('');
              }
              if(jQuery(this).hasClass('name') || jQuery(this).hasClass('email') || jQuery(this).hasClass('number') || jQuery(this).hasClass('comment')){
                jQuery(this).find('input').val('');
              }
          });
          jQuery(obj).closest('.card').find('span.currentBookings').text(data['count']);

          jQuery('.select2').select2({
            theme: 'bootstrap4'
          });

        }
        else if(data['status'] == 'exist'){
          Toast.fire({
              icon: 'error',
              title: ' {{ __('event.caefte') }}'
          });
        }
        else{
          Toast.fire({
              icon: 'error',
              title: ' {{ __('event.tiauetbe') }}'
          });
        }
      },
      error: function (data) {
        Toast.fire({
              icon: 'error',
              title: ' {{ __('event.pmsfafatta') }}'
          });
      }
  }); 
}
@endcan

@can('Event Booking Delete')
function deleteBooking(obj){

  //--------- For notification -----
  const Toast = Swal.mixin({
    toast: true,
    position: 'top-end',
    showConfirmButton: false,
    timer: 3000
  });

  var id = jQuery(obj).attr('data-id');
  var token = $('meta[name="csrf-token"]').attr('content');
  var thisTr = jQuery(obj).closest('tr');

  $.ajax({
      type: 'POST',
      url: '/deleteEventBooking',
      data: {'id':id,'_token':token},
      dataType: 'json',
      success: function (data) {
        console.log(data);
        if(data['status'] == 'success'){

          Toast.fire({
              icon: 'success',
              title: ' {{ __('event.bhbds') }}'
          });

          //---- Update count of booking ----
          var count = jQuery(thisTr).closest('.card').find('span.currentBookings').text();
          jQuery(thisTr).closest('.card').find('span.currentBookings').text(count-data['slots']);

          //-------- Updating current card balance in all slots where it used ------//
          if(data['cardID'])
            updateCardBalance(data['cardID'],data['balance']);

          //----- Updating slots ------
          // var limit = 1;
          if(data['slots'] > 1){
            // jQuery(thisTr).next('tr').remove();
            for(var k=1; k<data['slots']; k++){
              jQuery(thisTr).next('tr').remove();
            }            
            //----- Update next slots status ------
            if(data['limit'] > 0){
              // if(data['limit'] > 1)
              //   limit = 2;
              var trs = jQuery(thisTr).nextAll().find('td.bg-warning').slice(0,data['limit']);
              jQuery(trs).each(function(i,v){
                  jQuery(this).removeClass('bg-warning').addClass('bg-info').find('span').text('{{ __("event.booked") }}');
              });
              if(data['limit'] > jQuery(trs).length ){
                //console.log("wating length : "+jQuery(trs).length);
                var mainSlot = jQuery(thisTr).nextAll().find('td.bg-gray').slice(0,1);
                jQuery(mainSlot).each(function(i,v){
                  jQuery(this).removeClass('bg-gray').addClass('bg-success').find('span').text('{{ __("event.available") }}');
                });
              } 
            }
            //----- Remove slots ------
            jQuery(thisTr).remove();
          }
          else{
            if(data['limit'] > 0){
              
              var trs = jQuery(thisTr).nextAll().find('td.bg-warning').slice(0,1);
              jQuery(trs).each(function(i,v){
                  jQuery(this).removeClass('bg-warning').addClass('bg-info').find('span').text('{{ __("event.booked") }}');
              });
              //console.log(jQuery(trs).length);
              if(data['limit'] > 1 || jQuery(trs).length == 0 ){
                var mainSlot = jQuery(thisTr).nextAll().find('td.bg-gray').slice(0,1);
                jQuery(mainSlot).each(function(i,v){
                  jQuery(this).removeClass('bg-gray').addClass('bg-success').find('span').text('{{ __("event.available") }}');
                });
              }
            }
            jQuery(thisTr).remove();
          }
          
        }
        else if(data['status'] == 'exceeded'){
          Toast.fire({
              icon: 'error',
              title: ' {{ __('event.bdthbp') }}'
          });
        }
      },  
      error: function (data) {
        Toast.fire({
              icon: 'error',
              title: ' {{ __('event.eotdb') }}'
          });
      }
  });  
}
@endcan

function cardSelect(obj){
    var token = $('meta[name="csrf-token"]').attr('content');
    var cardID = jQuery(obj).val();
    //----- Getting ids of slot and event from cutCard td ---
    var slotID = jQuery(obj).closest('tr').find('.cutCard').attr('data-id');
    var eventID = jQuery(obj).closest('tr').find('.cutCard').attr('data-event-id');

    $.ajax({
      type: "POST",
      data: { 'id':cardID,'_token':token,'slotID':slotID},
      url: "/getCardUsedClipsAjax",
      dataType: 'json',
      success: function(response){
        if(response['status'] == 'new'){
          jQuery(obj).closest('tr').find('.cutBack').html(response['balance']);
          jQuery(obj).closest('tr').find('.cutCard').html("<button data-event-id='"+eventID+"' data-slot-id='"+slotID+"' data-card-id='"+cardID+"' onclick='cutClips(this)' class='btn btn-info btn-sm'>{{ __('event.cut_in_clipboard') }}</button>");
        }
        else{
          jQuery(obj).closest('tr').find('.cutBack').html(response['balance']);
          jQuery(obj).closest('tr').find('.cutCard').html('<button class="btn btn-warning btn-sm" data-clip-id="'+response['id']+'" onclick="cutBackClips(this)">{{ __("event.undo_clips_in_clipboard") }}</button>');
        }
      }
    });
  }

  function cutClips(obj){
    //--------- For notification -----
    const Toast = Swal.mixin({
      toast: true,
      position: 'top-end',
      showConfirmButton: false,
      timer: 3000
    });

    var token = $('meta[name="csrf-token"]').attr('content');
    var cardID = jQuery(obj).attr('data-card-id');
    var eventID = jQuery(obj).attr('data-event-id');
    var slotID = jQuery(obj).attr('data-slot-id');

console.log(eventID);

    $.ajax({
      type: "POST",
      data: { 'cardID':cardID,'_token':token,'eventID':eventID,'slotID':slotID},
      url: "/bookClipsAjax",
      dataType: 'json',
      success: function(response){
          console.log(response);
          if(response['status'] == 'success'){
            Toast.fire({
              icon: 'success',
              title: ' {{ __("event.clips_used_s") }}'
            });

            //-------- Updating current card balance in all slots where it used ------//
            updateCardBalance(cardID,response['balance']);

            jQuery(obj).closest('tr').find('.cutBack').html(response['balance']);
            jQuery(obj).closest('tr').find('.cutCard').html('<button class="btn btn-warning btn-sm" data-clip-id="'+response['id']+'" onclick="cutBackClips(this)">{{ __("event.undo_clips_in_clipboard") }}</button>');
          }
          else if(response['status'] == 'less'){
            Toast.fire({
              icon: 'error',
              title: ' {{ __("event.not_enough_clips_are_available") }}'
            })
          }
          else if(response['status'] == 'exist'){
            Toast.fire({
              icon: 'error',
              title: ' {{ __("event.cauftb") }}'
            })
          }
          else{
            Toast.fire({
              icon: 'error',
              title: ' {{ __("event.tiaetuc") }}'
            })
          }
      }
    });
  }

  function cutBackClips(obj){
    //--------- For notification -----
    const Toast = Swal.mixin({
      toast: true,
      position: 'top-end',
      showConfirmButton: false,
      timer: 3000
    });

    var token = $('meta[name="csrf-token"]').attr('content');
    id = jQuery(obj).attr('data-clip-id');  

    $.ajax({
      type: "POST",
      data: { 'id':id,'_token':token},
      url: "/deleteClipAjax",
      dataType: 'json',
      success: function(response){
          console.log(response);
          if(response['status'] == 'success'){
            Toast.fire({
              icon: 'success',
              title: ' {{ __("event.clips_delete_s") }}'
            });

            //-------- Updating current card balance in all slots where it used ------//
            updateCardBalance(response['card'],response['balance']);

            jQuery(obj).closest('tr').find('.cutBack').html(response['balance']);
            jQuery(obj).closest('tr').find('.cutCard').html("<button data-event-id='"+response['event']+"' data-slot-id='"+response['slot']+"' data-card-id='"+response['card']+"' onclick='cutClips(this)' class='btn btn-info btn-sm'>{{ __('event.cut_in_clipboard') }}</button>");
          }
          else{
            Toast.fire({
              icon: 'error',
              title: ' {{ __("event.tiaetdc") }}'
            })
          }
      }
    });
  }
    
  function updateCardBalance(cardID,balance){
    jQuery('body').find('.clipCard').each(function(){
      var cardId = jQuery(this).find('select').val();
      if( cardId != '' || cardId != 'undefined' ){
        if(cardID == cardId){
            jQuery(this).next().html(balance);
        }
      }
    });
  }  
</script>
 @stop