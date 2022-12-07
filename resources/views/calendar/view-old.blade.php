@extends("layouts.backend")

@section('content')
<style>
  .fc-license-message{
    display: none;
  }
  .off{
    display: none;
  }
</style>
<div class="content-wrapper">
    
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>{{ __('keywords.calendar') }}</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="{{ Route('dashboard',session('business_name')) }}">{{ __('keywords.home') }}</a></li>
              <li class="breadcrumb-item active">{{ __('keywords.calendar') }}</li>
            </ol>
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>
    <!-- Main content -->
    <section class="content" id="calendarDiv">
      {{-- <button class="btn btn-dark on" onclick="goFullScreen()">{{ __('keywords.full_screen') }}</button>
      <button class="btn btn-dark off" onclick="exitFullScreen()">{{ __('keywords.exit_full_screen') }}</button> --}}
      <div id='calendar'></div>
    </section>
</div>
@stop

@section('scripts')
<script>

  document.addEventListener('DOMContentLoaded', function() {
    var calendarEl = document.getElementById('calendar');
    var today  = new Date();

    var calendar = new FullCalendar.Calendar(calendarEl, {
      height: 'auto',
      now: today,
      editable: false,
      aspectRatio: 1.8,
      scrollTime: '00:00',
      headerToolbar: {
        left: 'today prev,next',
        center: 'title',
        right: 'resourceTimelineDay,resourceTimelineWeek,resourceTimelineMonth,resourceTimelineThreeMonth,resourceTimelineYear'
      },
      initialView: 'resourceTimelineDay',
      views: {
        resourceTimelineTenDay: {
          type: 'resourceTimeline',
          duration: { days: 1 },
          buttonText: 'Day'
        },
        resourceTimelineWeek: {
          type: 'resourceTimeline',
          duration: { days: 7 },
          buttonText: 'week'
        },
        resourceTimelineThreeMonth: {
          type: 'resourceTimeline',
          duration: { months: 3 },
          buttonText: '3 months'
        }
      },
      navLinks: true,
      resourceAreaWidth: '20%',
      resourceAreaHeaderContent: "{{ __('treatment.therapist') }}",
      resources: [
        @foreach($therapists as $therapist)
          { id: '{{ $therapist->id }}', title: '{!! $therapist->name !!}' },
        @endforeach
      ],
      events: [
        @foreach($dates as $date)
          @php
              $starttime = explode(":",$date->from);
              if($starttime[0] <= 9 ){
                $starttime[0] = '0'.$starttime[0];
              }
              $newstarttime = implode(":",$starttime);
              
              $endtime = explode(":",$date->till);
              if($endtime[1] == '00'){
                $endtime[0] = $endtime[0]+1;
              }
              $newendtime = implode(":",$endtime);
          @endphp
          { id: '{{ $date->id }}', resourceId: '{{ $date->user_id }}', start: '{{ \Carbon\Carbon::parse($date->date)->format("Y-m-d") }}T{{ $newstarttime}}:00', end: '{{ \Carbon\Carbon::parse($date->date)->format("Y-m-d") }}T{{ $newendtime }}:00', title: '{{ $date->business->brand_name ?: $date->business->business_name }}' },
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