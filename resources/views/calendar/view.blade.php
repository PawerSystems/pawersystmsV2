@extends("layouts.backend")

@section('content')
<style>
  .fc-license-message{
    display: none;
  }
  .closeAll{
    display: none;
  }
  .tooltip{ opacity: 10; }
  .openAll, .closeAll { margin-left: 1rem; }
  .tooltip-inner {
    white-space: pre-line;
}
</style>
<div class="content-wrapper">

    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>{{ __('leftnav.schedule') }}</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="{{ Route('dashboard',session('business_name')) }}">{{ __('keywords.home') }}</a></li>
              <li class="breadcrumb-item active">{{ __('leftnav.schedule') }}</li>
            </ol>
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>
    <!-- Main content -->
    <section class="content" id="calendarDiv">
      <button class="btn btn-dark openAll" onclick="showOrHideAll(this)">{{ __('keywords.expand_all') }}</button>
      <button class="btn btn-dark closeAll" onclick="showOrHideAll(this)">{{ __('keywords.collapse_all') }}</button>
      <a class="btn btn-dark" href="{{ Route('calendarUserWise',session('business_name')) }}">{{ __('keywords.user_wise') }}</a>
      <div id='calendar'></div>
    </section>
</div>
@stop

@section('scripts')
<script>

  function showOrHideAll(obj){
    jQuery('.openAll').toggle();
    jQuery('.closeAll').toggle();
    if(jQuery(obj).hasClass('openAll')){
      jQuery('.fc-datagrid-expander').each(function(){
        if(jQuery(this).find('.fc-icon-plus-square').length == 1){
          jQuery(this).trigger('click');
        }
      });
    }else{
      jQuery('.fc-datagrid-expander').each(function(){
        if(jQuery(this).find('.fc-icon-minus-square').length == 1){
          jQuery(this).trigger('click');
        }
      });
    }
  }

  document.addEventListener('DOMContentLoaded', function() {
    var calendarEl = document.getElementById('calendar');
    var today  = new Date();

    var calendar = new FullCalendar.Calendar(calendarEl, {
      height: 'auto',
      now: today,
      // slotLabelFormat: [
      //   { month: 'long', year: 'numeric' },
      //   { weekday: 'short', day: 'numeric' },
      //   { hour: 'numeric', minute: '2-digit', hour12: false} 
      // ],
      locale: @if(Lang::locale() == 'en') 'en-gb' @else 'da' @endif,   
      editable: false,
      aspectRatio: 1.8,
      resourcesInitiallyExpanded: false,
      scrollTime: '00:00',
      headerToolbar: {
        left: 'prev,next',
        center: 'title',
        right: 'resourceTimelineDay,resourceTimelineWeek,resourceTimelineMonth,resourceTimelineThreeMonth,resourceTimelineYear'
      },
      initialView: 'resourceTimelineMonth',
      views: {
        resourceTimelineDay: {
          type: 'resourceTimeline',
          duration: { days: 1 },
          buttonText: '{{ __("keywords.day") }}'
        },
        resourceTimelineWeek: {
          type: 'resourceTimeline',
          duration: { days: 7 },
          buttonText: '{{ __("keywords.week") }}'
        },
        resourceTimelineMonth: {
          type: 'resourceTimeline',
          duration: { months: 1 },
          buttonText: '{{ __("keywords.month") }}'
        },
        resourceTimelineThreeMonth: {
          type: 'resourceTimeline',
          duration: { months: 3 },
          buttonText: '{{ __("keywords.3months") }}'
        },
        resourceTimelineYear: {
          type: 'resourceTimeline',
          duration: { year: 1 },
          buttonText: '{{ __("keywords.year") }}'
        }
      },
      navLinks: true,
      displayEventTime: false,
      resourceAreaWidth: '20%',
      resourceAreaHeaderContent: "{{ __('treatment.locations') }}",
      resourceGroupField: 'location',
      eventDidMount: function(info) {
        var tooltip = new Tooltip(info.el, {
          title: info.event.extendedProps.description,
          placement: 'top',
          trigger: 'hover',
          container: 'body'
        });
      },
      resourceOrder: 'title,location',
      resources: [
        @foreach($businesses as $business)
            @foreach($business->users->where('role','!=','Customer')->where('is_therapist',1) as $therapist)
              { id: '{{ $therapist->id }}', title: '{!! ucwords($therapist->name) !!} ({{ $therapist->email }})', location: '{!! ucwords($business->brand_name) ?: ucwords($business->business_name) !!} ( {{ $business->business_name.'.'.config('app.domain') }})'},
            @endforeach
        @endforeach
      ],
      eventOrder:'title',
      events: [
      @foreach($businesses as $business)
        @foreach($business->Dates->where('is_active',1) as $date)
          @php
            $starttime = explode(":",$date->from);
            if($starttime[0] <= 9 ){
              $starttime[0] = '0'.$starttime[0];
            }
            $newstarttime = implode(":",$starttime);
            
            $endtime = explode(":",$date->till);
            if($endtime[1] == '00'){
              $endtime[0] =  (int)$endtime[0]+1;
            }
            $newendtime = implode(":",$endtime);

            $treatments = '';
            $count = 1;
            foreach($date->treatments as $treatment){
              if( $count == $date->treatments->count() )
                $treatments .= $treatment->treatment_name.' ('.($treatment->time_shown ?: $treatment->inter).')';
              else
                $treatments .= $treatment->treatment_name.' ('.($treatment->time_shown ?: $treatment->inter).') | ';  
              $count++;
            }
          @endphp
          
          { id: '{{ $date->id }}', resourceId: '{{ $date->user_id }}', start: '{{ \Carbon\Carbon::parse($date->date)->format("Y-m-d") }}T{{ $newstarttime}}:00', end: '{{ \Carbon\Carbon::parse($date->date)->format("Y-m-d") }}T{{ $newendtime }}:00', title: '{{ $date->from.' - '.$date->till }}', description: '{{ __("treatment.lunch") }}: {{ ($date->treatmentSlotLunchTime ? $date->treatmentSlotLunchTime->time : "N/A") }} \n {{ __("treatment.treatments") }}: {{ $treatments }}'},
        @endforeach
      @endforeach
      ]
    });

    calendar.render();

  });
 

  function goFullScreen(){

    jQuery('.off, .on').toggle();
    jQuery('.fc-datagrid-cell-main').css('color','white');

    var elem = document.getElementById("calendarDiv");

    if(elem.requestFullscreen){
        elem.requestFullscreen();
    }
    else if(elem.mozRequestFullScreen){
        elem.mozRequestFullScreen();
    }
    else if(elem.webkitRequestFullscreen){
        elem.webkitRequestFullscreen();
    }
    else if(elem.msRequestFullscreen){
        elem.msRequestFullscreen();
    }
  }

  function exitFullScreen(){
    jQuery('.off, .on').toggle();
    jQuery('.fc-datagrid-cell-main').css('color','black');
    if(document.exitFullscreen){
        document.exitFullscreen();
    }
    else if(document.mozCancelFullScreen){
        document.mozCancelFullScreen();
    }
    else if(document.webkitExitFullscreen){
        document.webkitExitFullscreen();
    }
    else if(document.msExitFullscreen){
        document.msExitFullscreen();
    }
  }

</script>

@stop