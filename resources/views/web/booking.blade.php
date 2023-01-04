@extends('layouts.web')

@section('content')

@php
    $languages = Config::get('languages');
    $key = $languages[Lang::locale()]['lang-variable'];
    $content = '';
    if($key != 'en'){
      //------- Get translation of datepicker -------
      if(is_file(storage_path( $key."-datepicker.txt"))){
          $file = fopen(storage_path( $key."-datepicker.txt"), "r");
          $content = fread($file,filesize(storage_path( $key."-datepicker.txt")));
      }
    }
@endphp

@php $cpr = $cancelationStopHours = $bookingStopHours = $department = $codeForBooking = $maxBookingsMonth = $maxActiveBookings = $businessCode = $dateFormat = $cprInsurance = $mdr = ''; @endphp
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
@php
  $jsFormates = include(storage_path("/date-format/date.php"));
@endphp
   

<style>
.no-arrow { cursor: default !important; }
.no-arrow:hover{ 
    background-color: #28a745 !important;
    border-color: #28a745 !important;
 }
.no-pedding{ padding: 0; }
.therapistName{ margin: 10px 0; }
.treatment,.contact {
  border:2px solid #007bff; 
  margin:10px 0; 
  padding:10px; 
  border-radius:10px;
  background: #cff0fb; 
  cursor: pointer; 
}
.selected{background:#007bff;color:white;}
.timeSlotsList{ display:none; }
.timeSlotsList ul{ list-style:none; }
.timeSlotsList ul li{
  float: left;
  padding: 5px 15px;
  background: lightgray;
  border-radius: 5px;
  cursor: pointer;
  width: 80px;
  margin: 2px;
  text-align: center;
}
.timeSlotsList ul li.active{ background:gray;color:white; }
.btn{ margin: 2px; }
.min8.error{ border:1px solid red; }
.departmentBtn{ 
  width: 100%; 
  background-color: #99b45f !important;
  border: none;
  color:white; 
}
.threapistBtn{ min-height: 150px; }
</style>

<section class="page-section" id="bod1">
  <div class="container">
    <div class="row">
        <div class="col-lg-12 text-center">
            <h2 class="section-headingbooking text-uppercase">
                <font style="vertical-align: inherit;"><font style="vertical-align: inherit;">{{ __('web.book_consulation') }}</font></font>
            </h2>
            
        </div>
    </div>

  @if($treatments->count() && $dates > 0)
    @php
      $step = 1;
    @endphp
    <div id="accordion">

      <div class="card">
        <div class="card-header" id="heading1">
          <h5 class="mb-0">
            <button class="btn btn-link" data-toggle="collapse" data-target="#collapse1" aria-expanded="true" aria-controls="collapse1">
            @php
              echo $step.' - &nbsp;&nbsp;';
              $step++;
            @endphp 
            <i class="fas fa-shield-alt"></i>&nbsp;&nbsp;{{ __('web.select_treatment') }}
            </button>
          </h5>
          <span class="float-right treatmentName"></span>
        </div>

        <div id="collapse1" class="collapse show" aria-labelledby="heading1" data-parent="#accordion">
          <div class="card-body">
            @foreach($treatments as $treatment)
            <button class="btn btn-warning btn-block treatmentBtn" data-treatment-id="{{ $treatment->id }}">
              <p>
                <span class="text-left nameOnly">{{ $treatment->treatment_name }}</span>
                <br>
                <span class="text-left timeOnly">({{ $treatment->time_shown ?: $treatment->inter}} min)</span>
                @if($treatment->price > 0)<span class="text-right">{{ $treatment->price }} kr</span>@endif
              </p>
              <p class="text-center">{{ $treatment->description }}</p></button>
            @endforeach
          </div>
        </div>
      </div>

      <div class="card">
        <div class="card-header" id="heading2">
          <h5 class="mb-0">
            <button class="btn btn-link collapsed">
            @php
              echo $step.' - &nbsp;&nbsp;';
              $step++;
            @endphp 
            <i class="fas fa-user"></i>&nbsp;&nbsp;{{ __('web.select_therapist') }}
            </button>
          </h5>
          <span class="float-right threapistName"></span>
        </div>

        <div id="collapse2" class="collapse" aria-labelledby="heading2" data-parent="#accordion">
          <div class="card-body"></div>
        </div>
      </div>

      <div class="card">
        <div class="card-header" id="heading3">
          <h5 class="mb-0">
            <button class="btn btn-link collapsed">
            @php
              echo $step.' - &nbsp;&nbsp;';
              $step++;
            @endphp 
            <i class="fas fa-calendar-alt"></i>&nbsp;&nbsp;{{ __('web.select_date') }}
            </button>
            <i class="text-center"> <small class="text-muted">({{ __('web.select_date_message') }})</small></i>
          </h5>
          <span class="float-right dateTime"></span>
        </div>
        <div id="collapse3" class="collapse" aria-labelledby="heading3" data-parent="#accordion">
          <div class="card-body">
            <div class="row">
                <div class="col-md-6" id="dates">
                  <p class="float-left">
                    <strong>
                      * {{ __('web.open_dates_are_marked_with_bold') }}
                    </strong>
                  </p>
                  <input type="text" class="form-control" id="datepicker" name="date" readonly>
                </div>
                <div class="col-md-6" id="timeSlots">
                    
                </div>
            </div>
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
          <button type="submit" class="btn btn-primary submit run">{{ __('web.next') }}</button>
        </form>
      </div>
    @else  
    <div class="card">
      <div class="card-header" id="heading4">
        <h5 class="mb-0">
          <button class="btn btn-link collapsed">
            @php
              echo $step.' - &nbsp;&nbsp;';
              $step++;
            @endphp 
          <i class="far fa-address-card"></i> &nbsp;&nbsp;{{ __('web.contact_information') }}
          </button>
        </h5>
        <span class="float-right contactInfo"></span>
      </div>
      <div id="collapse4" class="collapse" aria-labelledby="heading4" data-parent="#accordion">
        <div class="card-body">
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
              <input type="number" class="form-control required min8" name="number" id="number" placeholder="{{ __('keywords.number') }}" required>
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

            @if($mdr) <!-- check  -->
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

    @php $count = 5; @endphp
    @if($department) <!-- if enable from setting show then -->
      <div class="card">
        <div class="card-header" id="heading{{$count}}">
          <h5 class="mb-0">
            <button class="btn btn-link">
            @php
              echo $step.' - &nbsp;&nbsp;';
              $step++;
            @endphp 
            <i class="fas fa-building"></i>&nbsp;&nbsp;{{ __('web.select_department') }}
            </button>
          </h5>
          <span class="float-right departmentName"></span>
        </div>

        <div id="collapse{{$count}}" class="collapse" aria-labelledby="heading{{$count}}" data-parent="#accordion">
          <div class="card-body">
            @foreach($departments as $department)
            <button class="btn btn-warning departmentBtn {{ ($lastDepOfCurrentUser == $department->id ? 'active' : '') }} " data-department-id="{{ $department->id }}">{{ $department->name }}</button>
            @endforeach
          </div>
        </div>
      </div>
      @php $count++; @endphp
    @endif

    @if($cprInsurance && isset($_GET['_token']) && $_GET['_token'] == \Session::get('_token'))
      <div class="card">
        <div class="card-header" id="heading{{$count}}">
          <h5 class="mb-0">
            <button class="btn btn-link collapsed">
              @php
                echo $step.' - &nbsp;&nbsp;';
                $step++;
              @endphp 
            <i class="fas fa-code"></i>&nbsp;&nbsp;{{ __('web.put_CPR_num') }}
            </button>
          </h5>
          <span class="float-right cprBooking"></span>
        </div>
        <div id="collapse{{$count}}" class="collapse" aria-labelledby="heading{{$count}}" data-parent="#accordion">
          <div class="card-body">
            <input class="form-control require" id="cpr_booking" name="cpr_booking"><br>
            <button type="submit" class="btn btn-primary submit cpr_booking">{{ __('web.next') }}</button>

          </div>
        </div>
      </div>
      @php $count++; @endphp
    @endif

    <div class="card">
      <div class="card-header" id="heading{{$count}}">
        <h5 class="mb-0">
          <button class="btn btn-link collapsed">
            @php
              echo $step.' - &nbsp;&nbsp;';
              $step++;
            @endphp 
          <i class="fas fa-file"></i>&nbsp;&nbsp;{{ __('web.complete_you_order') }}
          </button>
        </h5>
      </div>
      <div id="collapse{{$count}}" class="collapse" aria-labelledby="heading{{$count}}" data-parent="#accordion">
        <div class="card-body">
          <textarea class="form-control" id="comment" name="comment" placeholder="{{ __('web.comment') }}..."></textarea><br>
          
          <button type="submit" class="btn btn-primary submit" onclick="addBooking()">{{ __('web.submit') }}</button>

        </div>
      </div>
    </div>
    <p style="color:red;">{!! __('keywords.booking_message') !!}</p>
     @if($cprInsurance  && isset($_GET['_token']) && $_GET['_token'] == \Session::get('_token'))
        <p style="color:blue;">{!! __('keywords.if_no_found_time') !!}
          <span><a href="{{ Route('contact',array(session('business_name'),__('web.no_available_slots'))) }}">{{ __('keywords.contact_us') }}</a></span>
        </p>
      @endif  
    

    </div>
  @else
    <div class="row  text-center">
      <div class="col-md-12">
        {{-- href="/contact"  --}}
        <h4 style="color:blue;">{!! __('treatment.no_upcoming_treatments') !!}</h4>
        @if($cprInsurance  && isset($_GET['_token']) && $_GET['_token'] == \Session::get('_token'))
          <a class="btn btn-info btn-md" href="{{ Route('contact',array(session('business_name'),__('web.no_available_slots'))) }}">{{ __('keywords.contact_us') }}</a>
        @endif  
      </div>
    </div>
  @endif
  </div>
</section>
@stop

@section('scripts')

<script>


jQuery('.departmentBtn').click(function(){
    jQuery('.departmentBtn').removeClass('active');
    jQuery(this).addClass('active');
    jQuery('.departmentName').html('<i class="fas fa-check"></i> '+jQuery(this).text());
    jQuery(this).closest('.collapse').removeClass('show');
    jQuery(this).closest('.card').next('.card').find('.collapse').addClass('show');
    nextCollapse(this);
});

jQuery('.treatmentBtn').click(function(){
    jQuery('.treatmentBtn').removeClass('active');
    jQuery(this).addClass('active');
    jQuery('.treatmentName').html('<i class="fas fa-check"></i> '+jQuery(this).find('.nameOnly').text()+' '+jQuery(this).find('.timeOnly').text());

    var treatmentID = jQuery(this).attr('data-treatment-id');
    var token = $('meta[name="csrf-token"]').attr('content');
    var that = this;
    
    $.ajax({
      type: 'POST',
      url: '/getTherapistOfTreatmentAjax',
      data: {'treatmentID':treatmentID,'_token':token },
      dataType: 'json',
      success: function (therapists) {

        if(jQuery(therapists).length > 0){

          var html = '';

          var count = 0;
          $.each(therapists,function(key, therapist){
            var freeTxt = '';
            if(therapist['profile_photo_path']){
              img = therapist['profile_photo_path'];
            }
            else{
              if(therapist['gender'] == 'women'){ img = 'avatar2.png'; }
              else{ img =  'avatar5.png'; }
            }

            if(therapist["free_txt"])
              freeTxt = therapist["free_txt"];

            html += '<button class="no-pedding btn btn-block btn-warning threapistBtn" data-threapist-id="'+therapist["id"]+'"><img class="float-left" width="150" src="/images/'+img+'"><p class="therapistName">'+therapist["name"]+'<p><p>'+freeTxt+'</p></button>';
            count++;
          });

          jQuery('#collapse2 .card-body').html(html);

          if(count > 1){
            html = '<button class="no-pedding btn btn-warning btn-block threapistBtn" data-threapist-id="-1"><img class="float-left" width="150" src="/images/avatar5.png"><p class="therapistName">{{ __("web.all") }}</p></button>';
            jQuery('#collapse2 .card-body').prepend(html);
          }

          if(count == 1){
            jQuery('.threapistBtn').click();
          }

          jQuery(that).closest('.collapse').removeClass('show');
          jQuery(that).closest('.card').next('.card').find('.collapse').addClass('show');
          nextCollapse(that);
        } 
        else{
          alert('{{ __('web.no_data_available') }}');
        }
      }
    });
});

jQuery('body').on('click','.threapistBtn',function(){

    jQuery('.threapistBtn').removeClass('active');
    jQuery(this).addClass('active');
    jQuery('.threapistName').html('<i class="fas fa-check"></i> '+jQuery(this).find('.therapistName').text());
    
    var therapistID = jQuery(this).attr('data-threapist-id');
    var treatmentID = jQuery('.treatmentBtn.active').attr('data-treatment-id');
    var token = $('meta[name="csrf-token"]').attr('content');
    jQuery('#timeSlots').html('');
    $("#datepicker").datepicker("destroy");
    var that = this;
    
    $.ajax({
      type: 'POST',
      url: '/getDateOfTreatmentAjax',
      data: { 'therapistID':therapistID,'treatmentID':treatmentID,'_token':token },
      dataType: 'json',
      success: function (dates) {
        if(jQuery(dates).length > 0){
            var enableDays = [];
            for (var key in dates) { 

              //enableDays.push(key);

              var times = '';
              var title = '';
              var maintimes = '';
              var slots = dates[key]['slots'];
              var booked = dates[key]['booked'];
              var dateID = dates[key]['dateID'];
              var description = dates[key]['description'];
              var therapist = dates[key]['therapist'];
              var waiting = dates[key]['waiting'];
             
              //--- remove last element in array ---
              slots.pop();
              //---- ceate slots for date ----
              title += "<li class='text-center' style='width:100%;margin:10px 0;padding:10px;cursor:default;'>";
                if(description){
                  title += "<button class='no-arrow btn btn-success btn-block'>{{__('treatment.therapist')}}</button><i class='fas fa-arrow-right'></i> "+therapist+"<br>";
                  title += "<button class='no-arrow btn btn-success btn-block mt-3'>{{__('treatment.description')}}</button><i class='fas fa-arrow-right'></i> "+description+"<br>";
                }else{
                  title += "<button class='no-arrow btn btn-success btn-block'>{{__('treatment.therapist')}}</button><i class='fas fa-arrow-right'></i> "+therapist+"<br>";
                }
              title += "</li>";

              for (var i in slots) {
                if (jQuery.inArray(slots[i], booked) == -1) {
                  enableDays.push(key);
                  times += "<li data-date-id='"+dateID+"' data-therapist='"+therapist+"' onclick='selectTime(this)'>"+slots[i]+"</li>";
                }
              }

              if(times == '' && waiting == 1){
                enableDays.push(key);
                times += "<li style='width:100% !important;' data-date-id='"+dateID+"' data-therapist='"+therapist+"' onclick='selectTime(this)'>Waiting List</li>";
              }

              //----- if above time not available 
              // if(enableDays.length == 0)
              //   times = '';
              if(times == '')
                title = '';
              else{
                maintimes = title+times;
                title = '';
                times = '';
              }

              if(!jQuery.isEmptyObject(dates[key]['others'])){
                jQuery.each(dates[key]['others'], function (index,value){
                  var slots = value['slots'];
                  var booked = value['booked'];
                  var dateID = value['dateID'];
                  var description = value['description'];
                  var therapist = value['therapist'];
                  var waiting = value['waiting'];

                  //--- remove last element in array ---
                  slots.pop();
                  //---- ceate slots for date ----
                  title += "<li class='text-center' style='width:100%;margin:10px 0;padding:10px;cursor:default;'>";
                  if(description){
                    title += "<button class='no-arrow btn btn-success btn-block'>{{__('treatment.therapist')}}</button><i class='fas fa-arrow-right'></i> "+therapist+"<br>";
                    title += "<button class='no-arrow btn btn-success btn-block mt-3'>{{__('treatment.description')}}</button><i class='fas fa-arrow-right'></i> "+description+"<br>";
                  }else{
                    title += "<button class='no-arrow btn btn-success btn-block'>{{__('treatment.therapist')}}</button><i class='fas fa-arrow-right'></i> "+therapist+"<br>";
                  }
                  title += "</li>";
                  
                  for (var i in slots) {
                    if (jQuery.inArray(slots[i], booked) == -1) {
                      enableDays.push(key);
                      times += "<li data-date-id='"+dateID+"' data-therapist='"+therapist+"' onclick='selectTime(this)'>"+slots[i]+"</li>";
                    }
                  }

                  if(times == '' && waiting == 1){
                    enableDays.push(key);
                    times += "<li style='width:100% !important;' data-date-id='"+dateID+"' data-therapist='"+therapist+"' onclick='selectTime(this)'>Waiting List</li>";
                  }

                  if(times == '')
                    title = '';
                  else{
                    maintimes += title+times;
                    title = '';
                    times = '';
                  }

                });
              }

              jQuery('#timeSlots').append("<div class='timeSlotsList "+key+"'><ul>"+maintimes+"</ul></div>");
            }
            
            if(enableDays.length > 0 ){
              //---------- if dates avaiable then open next slots ------------------
              jQuery(that).closest('.collapse').removeClass('show');
              jQuery(that).closest('.card').next('.card').find('.collapse').addClass('show');
              nextCollapse(that);
              //----- show this treatment dates in calender -----
              $('#datepicker').datepicker({ 
                {!! $content !!}
                beforeShowDay:function(dt)
                  { 
                    return [available(dt,enableDays), available(dt,enableDays) ? "availabe" : "" ];
                  },
                  dateFormat: '{{ $jsFormates[$dateFormat] }}',
                  firstDay: 1,
                  autoclose: true,
                  onSelect: function(){
                    $(this).blur();
                    var dateText = $.datepicker.formatDate("yy-mm-dd", $(this).datepicker("getDate"));
                    $('input#datepicker').attr('value',dateText);
                    jQuery('#timeSlots .timeSlotsList').removeClass('active');
                    jQuery('#timeSlots .timeSlotsList').hide();
                    jQuery('#timeSlots .timeSlotsList.'+dateText).addClass('active').show();
                  }
              }).focus();

              checkIfDateIsThere();

            }else{
              alert('{{ __("web.nesaftt") }} {{ __("web.please_change_traetment") }}');
           }
        } 
        else{
          alert('{{ __('web.no_data_available') }}');
        }
      }
    });
    
});

function checkIfDateIsThere(){
  if(jQuery('td.availabe').length == 0){
    jQuery('.ui-datepicker-next').trigger('click');
    checkIfDateIsThere();
  }else{
    jQuery('td.availabe:first').trigger('click');
    $('#datepicker').datepicker().focus();
  }     
}

function selectTime(obj){
  jQuery('#timeSlots .timeSlotsList li').removeClass('active');
  jQuery(obj).addClass('active');

  //----- update therapist name -----//
  jQuery('.threapistName').html('<i class="fas fa-check"></i> '+jQuery(obj).attr('data-therapist'));

  jQuery('.dateTime').html('<i class="fas fa-check"></i> '+jQuery('#datepicker').val()+" - "+jQuery(obj).text());

  jQuery(obj).closest('.collapse').removeClass('show');
  jQuery(obj).closest('.card').next('.card').find('.collapse').addClass('show');
  nextCollapse(obj);
  @if(Auth::user())
    jQuery('.run').trigger('click');
    @if ( !empty($lastDepOfCurrentUser) || $lastDepOfCurrentUser != 'null')
      jQuery('.departmentBtn').trigger('click');
    @endif
  @endif
}


function available(dy,enableDays) {
    dmy = dy.getFullYear() + "-" + ("0" + (dy.getMonth() + 1)).slice(-2) + "-" + ("0" + dy.getDate()).slice(-2);

    if (jQuery.inArray(dmy, enableDays) != -1) {
        return true;
    } else {
        return false;
    }
}


function nextCollapse(obj){
  var num = jQuery(obj).closest('.card').find('.btn-link').attr('data-target');
  var id = num.replace('#collapse','');
  id = parseInt(id)+1;
  jQuery(obj).closest('.card').next('.card').find('.btn-link').attr({'data-toggle':'collapse','data-target':'#collapse'+id,'aria-expanded':'false','aria-controls':'collapse'+id});
}

jQuery('.submit').click(function(){

  if(jQuery(this).hasClass('cpr_booking')){
    if(!checkInsuranceCPRField()){
      return false;
    }else{
      jQuery('.cprBooking').html('<i class="fas fa-check"></i> '+jQuery('#cpr_booking').val());
    }
  }

  if(CheckRequired()){
    var name = jQuery('input[name=name]').val();
    var email = jQuery('input[name=email]').val();
    var number = jQuery('input[name=number]').val();
    if(name !='' && email !='' && number != ''){
      jQuery('.contactInfo').html('<i class="fas fa-check"></i> '+name+' - '+email+' - '+number);
      jQuery(this).closest('.collapse').removeClass('show');
      jQuery(this).closest('.card').next('.card').find('.collapse').addClass('show');
      nextCollapse(this);
    }
  }
});

function checkInsuranceCPRField(){
  if( jQuery('input[name=cpr_booking]').val() === '' || jQuery('input[name=cpr_booking]').val().length < 10 || jQuery('input[name=cpr_booking]').val().length > 10 ){
    alert("{{ __('treatment.please_enter_at_least_10_cha') }}");
    return false;
  }else{
    if(!validate_cpr_number(jQuery('input[name=cpr_booking]').val())){
      alert("{{ __('treatment.please_enter_valid_number') }}");
      return false;
    }
  }
  return true;
}

function validate_cpr_number( cpr ) {

  if($.isNumeric(cpr)){

    var d = cpr.substring(0, 2);
    var m = cpr.substring(2, 4);
    var y = cpr.substring(4, 6);
    
    var fullDate = d+'/'+m+'/'+y;

    if(!isDate(fullDate)){
      alert("{{ __('treatment.please_enter_valid_number') }}");
      return false;
    }

    return true;     
  }
  alert("{{ __('treatment.please_enter_valid_number') }}");
  return false;  
 
}

function isDate(value) {
  var re = /^(?=\d)(?:(?:31(?!.(?:0?[2469]|11))|(?:30|29)(?!.0?2)|29(?=.0?2.(?:(?:(?:1[6-9]|[2-9]\d)?(?:0[48]|[2468][048]|[13579][26])|(?:(?:16|[2468][048]|[3579][26])00)))(?:\x20|$))|(?:2[0-8]|1\d|0?[1-9]))([-.\/])(?:1[012]|0?[1-9])\1(?:1[6-9]|[2-9]\d)?\d\d(?:(?=\x20\d)\x20|$))?(((0?[1-9]|1[012])(:[0-5]\d){0,2}(\x20[AP]M))|([01]\d|2[0-3])(:[0-5]\d){1,2})?$/;
  var flag = re.test(value);
  return flag;
}


function CheckRequired() {
    @if($codeForBooking)
      if(jQuery('#contactForm').find('input[name=code]').val() != '{{ $businessCode }}'){
        alert("{{ __('web.bcnm') }}");
        return false;
      }
    @endif

    
    if(jQuery('.min8').length  > 0){
      if(jQuery('.min8').val().length != 8 ){
        jQuery('.min8').addClass('error');        
        alert(jQuery('.min8').closest('.input-group').siblings('label').text()+": {{ __('treatment.please_enter_at_least_8_cha') }}");
        return false;
      }
    }

    var $form = jQuery('#contactForm');
    // if ($form.find('.required').filter(function(){ 

    //   return this.value === '';
    // }).length > 0) {
    //     alert("{{ __('web.oomfctb') }}");
    //     return false;
    // }
    jQuery('.required').each(function(){
        if(jQuery(this).val() == ''){
          var field = jQuery('label[for='+this.id+']').text();
          alert(field+": {{ __('web.oomfctb') }}");
          return false;
        }
    });

    @if(!Auth::user() && $cpr)
    if(jQuery('.min10').val().length != 10 || !validate_cpr_number(jQuery('.min10').val())){
      alert(jQuery('.min10').siblings('label').text()+": {{ __('treatment.please_enter_valid_number') }}");
      return false;
    }
    @endif


    return true;
}

jQuery('select[name=country]').on('change',function(){
  jQuery('#number').removeClass('min8');
  var country = jQuery('select[name=country] option:selected').text();
  if(country == 'Denmark'){
    jQuery('#number').addClass('min8');
  }
});

function addBooking(){

  if(CheckRequired()){
    var department = '';
    if(jQuery('.departmentBtn.active').length > 0)
      var department = jQuery('.departmentBtn.active').attr('data-department-id');

    var CPRBooking = '';
    var userData = jQuery('#contactForm').serialize();
    var treatment = jQuery('.treatmentBtn.active').attr('data-treatment-id');
    var dateID = jQuery('.timeSlotsList li.active').attr('data-date-id');
    var time = jQuery('.timeSlotsList li.active').text();
    var comment = jQuery('#comment').val();
    @if($cprInsurance)
      var CPRBooking = jQuery('#cpr_booking').val(); 
    @endif

    if( time == 'Waiting List')
      var url = 'BookWaitingTimeSlotWebAjax';
    else
      var url = 'BookTimeSlotForWebAjax';

    $.ajax({
      type: 'POST',
      url: '/'+url,
      data: userData+'&treatment='+treatment+'&department='+department+'&comment='+comment+'&dateID='+dateID+'&time='+time+'&CPRBooking='+CPRBooking,
      dataType: 'json',
      success: function (data) {
        if(data['status'] == 'success'){
            jQuery('#accordion').after("<div style='color:blue;text-align: center;'>"+data['message']+"</div>");
            jQuery('#accordion').remove();
        } 
        else if(data['status'] == 'Email Exist'){
          alert("{{ __('web.uwteae') }}");
        }
        else if(data['status'] == 'exist'){
          alert("{{ __('web.nesaftt') }}");
        }
        else if(data['status'] == 'exceeded'){
          alert("{{ __('web.thbppttbaat') }}");
        }
        else if(data['status'] == 'not'){
          alert("{{ __('web.you_are_not_resig') }}");
        }
        else if(data['status'] == 'active_limit'){
          alert("{{ __('web.syablr') }}");
        }
        else if(data['status'] == 'monthly_limit'){
          alert("{{ __('web.symblr') }}");
        }
        else{
          alert("{{ __('web.taaue') }}");
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

@stop