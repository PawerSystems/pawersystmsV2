@extends('layouts.web')

@section('content')

@php $cpr = $cancelationStopHours = $bookingStopHours = $department = $codeForBooking = $maxBookingsMonth = $maxActiveBookings = $businessCode = $dateFormat = $mdr = ''; @endphp
    @foreach($settings as $setting)

        @if($setting->key == 'cpr_emp_fields')
            @if($setting->value == 'true')
                @php $cpr = 1; @endphp
            @else
                @php $cpr = ''; @endphp
            @endif 

        @elseif($setting->key == 'mdr_field')
            @if($setting->value == 'true')
                @php $mdr = 1; @endphp
            @else
                @php $mdr = ''; @endphp
            @endif 

        @elseif($setting->key == 'cpr_emp_fields_insurance')
            @if($setting->value == 'true')
                @php $cprInsurance = 1; @endphp
            @else
                @php $cprInsurance = ''; @endphp
            @endif     
            
        @elseif($setting->key == 'department')
            @if($setting->value == 'true')
                @php $department = 1; @endphp
            @else
                @php $department = ''; @endphp
            @endif    

        @elseif($setting->key == 'code_for_booking')
            @if($setting->value == 'true')
                @php $codeForBooking = 1; @endphp
            @else
                @php $codeForBooking = ''; @endphp
            @endif          
        
       @elseif($setting->key == 'stop_cancellation')
            @php $cancelationStopHours = $setting->value; @endphp

        @elseif($setting->key == 'stop_booking')
            @php $bookingStopHours = $setting->value; @endphp

        @elseif($setting->key == 'code')
            @php $businessCode = $setting->value; @endphp  

        @elseif($setting->key == 'date_format')
            @php $dateFormat = $setting->value; @endphp     

        @endif

    @endforeach
<style>
.event,.contact { border:2px solid black; margin:10px 0; padding:10px; border-radius:10px;background: rgb(107, 104, 104); color:white }
div[class*="event-"]{ display:none; }
.selected{background:black;color:white;}
.nextForm, #nextBtn { display:none; }
#nextBtn { display:none !important; }
.btn{ margin: 2px; }
.hr-color{ background-color: white; }
</style>

<nav class="navbar navbar-expand-lg navbar-dark fixed-bottom" id="nextBtn">
  <div class="container">
    <button class="nextFormNav btn btn-success ml-auto">{{ __('web.next') }}</button>   
  </div>
</nav>
<section class="page-section" id="bod1">
  <div class="container">
    <div class="row">
        <div class="col-lg-12 text-center">
            <h2 class="section-headingbooking text-uppercase">
                <font style="vertical-align: inherit;"><font style="vertical-align: inherit;">{{ __('web.keep_booking') }}</font></font>
            </h2>
        </div>
    </div>
    @if($events->count())
    <div id="accordion">
      <div class="card">
        <div class="card-header" id="heading0">
          <h5 class="mb-0">
            <button class="btn btn-link" data-toggle="collapse" data-target="#collapse0" aria-expanded="true" aria-controls="collapse0">
            <i class="fas fa-calendar-alt"></i>&nbsp;&nbsp;{{ __('web.select_event') }}
            </button>
          </h5>
          <span class="float-right eventDiv"></span>
        </div>

        <div id="collapse0" class="collapse show" aria-labelledby="heading0" data-parent="#accordion">
          <div class="card-body">
            @foreach($events as $event)

            <!-- Check if event time has passed then don't show event here -->
              @php
                $eventDateTime = \Carbon\Carbon::parse($event->date)->format('Y-m-d').' '.$event->time.':00';
                $currentDateTime = date('Y-m-d H:i:s');

                $eventD = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $eventDateTime);
                $currentD = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $currentDateTime);
                $result = $eventD->gt($currentD);
              @endphp
              @if (!$result)
                @continue;
              @endif()
            <!-- end -->

              <div class="event" data-id="{{$event->id}}" data-name="{{ $event->name }}" is-guest="{{ $event->is_guest }}">
                <p><b>{{ $event->name }}</b> ({{ $event->eventActiveSlots->count() }} {{ __('web.out_of') }} {{$event->slots}} {{ __('web.booked') }}) </p>
                <p><b>{{ __('event.date') }}:</b> {{ \Carbon\Carbon::parse($event->date)->format($dateFormat) }} &nbsp;&nbsp; <b>{{ __('event.time') }}:</b> {{$event->time}} &nbsp;&nbsp; <b>{{ __('web.duration') }}:</b> {{ $event->duration }} min &nbsp;&nbsp; 
                @php
                  if($event->price ){
                    echo __('web.price').': </b>'.$event->price;
                  }
                @endphp
                </p>
                <p>{{ $event->description }}</p>

                @if($event->is_guest) <!-- check for guest -->
                  <div class="form-group guest">
                      <div class="custom-control custom-checkbox">
                          <input type="checkbox" name="guest-{{$event->id}}" class="custom-control-input" id="bringGuest-{{$event->id}}" onclick="addEvent(this)">
                          <label class="custom-control-label" for="bringGuest-{{$event->id}}">{{ __('web.bring_guest') }}</label>
                      </div>
                  </div>
                @endif
                <hr class="hr-color">
                <div>
                  <button class='text-center register_btn btn btn-block btn-info'>{{ __('event.click_here_to_register') }}</button>
                </div>
              </div>
            @endforeach
            <button class="nextForm btn btn-primary">{{ __('web.next') }}</button>
          </div>
        </div>
      </div>

      @if(Auth::user())
      <div style="display:none;" class="card">
        <button class="btn btn-link"></button>
        <form  name="contactForm" id="contactForm" onsubmit="return false">
        @csrf
        <input type="hidden" name="id" value="{{ Auth::user()->id }}">
        <input type="hidden" name="name" value="{{ Auth::user()->name }}">
        <input type="hidden" name="email" value="{{ Auth::user()->email }}">
        <input type="hidden" name="number" value="{{ Auth::user()->number }}">
        <input type="hidden" name="code" value="{{ $businessCode }}">
        <button type="submit" class="btn btn-primary submit run">{{ __('keywords.submit') }}</button>
        </form>
      </div>
      @else
      <div class="card">
        <div class="card-header" id="heading1">
          <h5 class="mb-0">
            <button class="btn btn-link collapsed">
            <i class="far fa-address-card"></i> &nbsp;&nbsp;{{ __('web.contact_information') }}
            </button>
          </h5>
          <span class="float-right contactInfo"></span>

        </div>
        <div id="collapse1" class="collapse" aria-labelledby="heading1" data-parent="#accordion">
          <div class="card-body">
            <h3 class="section-subheadingbooking text-muted">
                <font style="vertical-align: inherit;"><font style="vertical-align: inherit;">{{ __('web.ntfifiaiyuypnatsryn') }}</font></font>
            </h3>
            <form name="contactForm" id="contactForm" onsubmit="return false">
              @csrf
              <!-- <p class="text-info"><i>{{ __('keywords.please_did_not_remove_country_code') }}</i></p> -->
              <label for="number">{{ __('keywords.number') }}</label>
              <div class="input-group mb-3">
                <input type="hidden" name="id" id="id">
                <div class="input-group-prepend">
                  <select name="country" class="form-control" style="max-width:150px;">
                    @foreach($countries as $country)
                      <option value="{{ $country->id }}" 
                      @if( $country->name == 'Denmark') selected @endif
                      >{{ $country->name }}</option>
                    @endforeach 
                  </select>
                </div>
                
                <input type="text" class="form-control required" name="number" id="number" placeholder="{{ __('keywords.number') }}" onchange="searchUser(this)" required>
              </div>
              <div class="form-group">
                <label for="name">{{ __('keywords.name') }}</label>
                <input type="text" class="form-control required" name="name" id="name" placeholder="{{ __('keywords.name') }}" required>
              </div>
              <div class="form-group">
                <label for="email">{{ __('keywords.email') }}</label>
                <input type="email" class="form-control required" name="email" id="email" aria-describedby="emailHelp" placeholder="{{ __('keywords.email') }}" required>
              </div>
              @if($codeForBooking)
              <div class="form-group">
                <label for="code">{{ __('web.code') }}</label>
                <input type="code" class="form-control required" name="code" id="code" placeholder="{{ __('web.code') }}" required>
              </div>
              @endif

              @if($cpr) <!-- check  -->
                <div class="form-group">
                  <label for="cprnr">{{ __('web.cprnr') }}</label>
                  <input type="text" class="form-control min10 required" name="cprnr" id="cprnr" placeholder="{{ __('web.cprnr') }}" required>  
                </div>
              @else
                <input type="hidden" name="cprnr" value="0">
              @endif

              @if($mdr)
                <div class="form-group">
                  <label for="mednr">{{ __('web.mednr') }}</label>
                  <input type="text" class="form-control required" name="mednr" id="mednr" placeholder="{{ __('web.mednr') }}" required>
                </div>
              @else
                <input type="hidden" name="mednr" value="0">
              @endif

              <button type="submit" class="btn btn-primary submit">{{ __('web.next') }}</button>
            </form>
          </div>
        </div>
      </div>
      @endif

      <div class="card">
        <div class="card-header" id="heading2">
          <h5 class="mb-0">
            <button class="btn btn-link collapsed">
            <i class="fas fa-file"></i>&nbsp;&nbsp;{{ __('web.complete_you_order') }}
            </button>
          </h5>
        </div>
        <div id="collapse2" class="collapse" aria-labelledby="heading2" data-parent="#accordion">
          <div class="card-body">
            <textarea class="form-control" id="comment" name="comment" placeholder="{{ __('web.comment') }}..."></textarea><br>
            <button type="submit" class="btn btn-primary submit" onclick="addBooking()">{{ __('web.submit') }}</button>
          </div>
        </div>
      </div>
      <p style="color:red;">{!! __('keywords.booking_message') !!}</p>
    </div>
    

    @else
      
        <h4 class="text-center">{{ __('event.no_active_events') }}</h4>
      <p class="text-center">
        {!! __('web.event_book_txt',['name' => '','link' => url('/myEventBookings') ]) !!}
    @endif
      </p>
  </div>
</section>

@stop

@section('scripts')

<script>
  function addEvent(obj){
    if(obj.checked){
      var eventDiv = jQuery(obj).closest('.event');
      if( !eventDiv.hasClass('selected') ){
        eventDiv.find('.register_btn').trigger('click');
      }    
    }
  }
</script>

@if($events->count())
<script>
jQuery('.register_btn').click(function(){
  var that = jQuery(this).closest('.event');
  that.toggleClass('selected');
  var events = '';
  jQuery('.event.selected').each(function(){
    events += ' <i class="fas fa-check"></i> '+that.attr('data-name');
  });
  if(events){
    jQuery('.eventDiv').html(events);
    jQuery('.nextForm, #nextBtn').show();
  } 
  else{
    jQuery('.eventDiv').html('');
    jQuery('.nextForm, #nextBtn').hide();
  } 
  if(that.hasClass('selected')){
    jQuery(this).html("{{ __('event.click_here_to_remove') }}");
  }else{
    jQuery(this).html("{{ __('event.click_here_to_register') }}");
    var thisDataId = that.attr('data-id');
    var guestCheck = jQuery('input#bringGuest-'+thisDataId);
    if(guestCheck.length > 0){
      if(guestCheck.is(":checked")){
        guestCheck.trigger('click');
      }
    }
  }
});

jQuery('.nextFormNav').click(function(){
  jQuery('.nextForm').trigger('click');
  jQuery('#nextBtn').hide();
});

jQuery('.nextForm').click(function(){
  jQuery('#nextBtn').hide();
  jQuery(this).closest('.collapse').removeClass('show');
  jQuery(this).closest('.card').next('.card').find('.collapse').addClass('show');
  nextCollapse(this);
  @if(Auth::user())
    jQuery('.run').trigger('click');
  @endif
});

jQuery('.submit').click(function(){
  if(CheckRequired()){
    var name = jQuery('input[name=name]').val();
    var email = jQuery('input[name=email]').val();
    var number = jQuery('input[name=number]').val();
    jQuery('.contactInfo').html('<i class="fas fa-check"></i> '+name+' - '+email+' - '+number);

    jQuery(this).closest('.collapse').removeClass('show');
    jQuery(this).closest('.card').next('.card').find('.collapse').addClass('show');
    nextCollapse(this);
  }
});

function CheckRequired() {
    @if($codeForBooking)
      if(jQuery('#contactForm').find('input[name=code]').val() != '{{ $businessCode }}'){
        alert("{{ __('web.bcnm') }}");
        return false;
      }
    @endif
    var $form = jQuery('#contactForm');
    if ($form.find('.required').filter(function(){ return this.value === '' }).length > 0) {
        alert("{{ __('web.oomfctb') }}");
        return false;
    }
    
    @if(!Auth::user() && $cpr)
    if($form.find('.min10').val().length < 10){
      alert("{{ __('treatment.please_enter_at_least_10_cha') }}");
      return false;
    }
    @endif

    return true;
}

function nextCollapse(obj){
  var num = jQuery(obj).closest('.card').find('.btn-link').attr('data-target');
  var id = num.replace('#collapse','');
  id = parseInt(id)+1;
  jQuery(obj).closest('.card').next('.card').find('.btn-link').attr({'data-toggle':'collapse','data-target':'#collapse'+id,'aria-expanded':'false','aria-controls':'collapse'+id});
  jQuery("html, body").animate({ scrollTop: 0 }, "slow");

}


function searchUser(obj){
  var number = jQuery(obj).val();
  var token = $('meta[name="csrf-token"]').attr('content');
  $.ajax({
      type: 'POST',
      url: '/getUserDataWithNumberAjax',
      data: { 'search':number,'_token':token },
      dataType: 'json',
      success: function (user) {
        if(jQuery(user).length > 0){
            jQuery('#id').val(user["id"]);
            jQuery('#name').val(user["name"]);
            jQuery('#email').val(user["email"]);
            jQuery('#number').val(user["number"]);
          @if($cpr)  
            jQuery('#cprnr').val(user["cprnr"]);
          @endif
          @if($mdr)  
            jQuery('#mednr').val(user["mednr"]);
          @endif  
        } 
        else{
            jQuery('#id').val('');
            jQuery('#name').val('');
            jQuery('#email').val('');
          @if($cpr)  
            jQuery('#cprnr').val('');
          @endif
          @if($mdr)  
            jQuery('#mednr').val('');
          @endif  
        } 
      }
  });
}

function addBooking(){
  if(CheckRequired()){
    var userData = jQuery('#contactForm').serialize();
    var comment = jQuery('#comment').val();
    var events = [];
    var guests = [];

    jQuery('.event.selected').each(function(){
      var id = jQuery(this).attr('data-id');
      if(jQuery('input[name=guest-'+id+']:checked').length > 0)
        guests.push(1);
      else
        guests.push(0);

      events.push(id);
    });

    $.ajax({
      type: 'POST',
      url: '/eventBookFromSiteAjax',
      data: userData+'&events='+events+'&comment='+comment+'&guests='+guests,
      dataType: 'json',
      success: function (data) {
        jQuery("html, body").animate({ scrollTop: 0 }, "slow");

        if(data['status'] == 'success'){
            jQuery('#accordion').after("<div style='color:blue;text-align: center;'>"+data['message']+"</div>");
            jQuery('#accordion').remove();
        } 
        else if(data['status'] == 'Email Exist'){
          alert("{{ __('web.uwteae') }}");
        }
        else if(data['status'] == 'time_passed'){
          alert(data['message']);
        }
        else if(data['status'] == 'Booking Exist'){
          var msg = data['event']+"{{ __('web.bieae') }}";
          alert(msg);
        }
        else if(data['status'] == 'exceeded'){
          alert("{{ __('web.booking_time_passed') }}");
        }
        else if(data['status'] == 'not'){
          alert("{{ __('web.you_are_not_resig') }}");
        }
        else if(data['status'] == 'active_limit'){
          alert(data['message']);
        }
        else if(data['status'] == 'monthly_limit'){
          alert(data['message']);
        }
      },
      error: function (xhr) {
        var ee = '';
        $.each(xhr.responseJSON.errors, function(key,value) {
            ee += value;
        });
        alert(ee);
      }
    });

  }
}

</script>
@endif
@stop