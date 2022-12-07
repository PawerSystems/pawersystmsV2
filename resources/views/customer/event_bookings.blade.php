@extends('layouts.backend')

@section('content')

<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>{{ __('profile.event_booking_list') }}</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="/profile">{{ __('keywords.home') }}</a></li>
              <li class="breadcrumb-item active">{{ __('profile.event_booking_list') }}</li>
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
              <h3 class="card-title">{{ __('profile.event_booking_list') }}</h3>
              <a href="/events" class="btn btn-info float-right">{{ __('event.book_event') }}</a>
              </div>
              <!-- /.card-header -->
              <div class="card-body">
                <table id="datatable3" class="table table-bordered table-striped">
                  <thead>
                  <tr>
                    <th>{{ __('profile.event_name') }}</th>
                    <th>{{ __('profile.date') }} - {{ __('profile.time') }}</th>
                    <th>{{ __('profile.duration') }}</th>
                    <th>{{ __('profile.instructor') }}</th>
                    <th class="cardUsede">{{ __('profile.card_use') }}</th>
                    <th class="clipUsede">{{ __('profile.clips_use') }}</th>
                    <th>{{ __('profile.comment') }}</th>
                    <th>{{ __('profile.bring_guest') }}</th>
                    <th>{{ __('profile.status') }}</th>
                    <th>{{ __('profile.ordered') }}</th>
                    <th>{{ __('profile.action') }}</th>
                  </tr>
                  </thead>
                  <tbody>
                  @foreach($bookings as $key => $value)
                    @php
                      $card = App\Models\Card::find(App\Models\UsedClip::where([['is_active',1],['event_slot_id',$value->id]])->pluck('card_id'))->first();
                      
                      $dateTimeFrom = \Carbon\Carbon::parse($value->event->date)->format("Y-m-d").' '.$value->event->time;
                      $dateTimeTill = \Carbon\Carbon::parse($value->event->date)->format("Y-m-d").' '.$value->event->time;
                      $dateTimeTill = \Carbon\Carbon::parse($dateTimeTill)->addMinutes($value->event->duration)->format("Y-m-d H:i");

                      //----- ceating ics link -----//
                      $desc = __('event.events').': '.$value->event->name.' ('.$value->event->duration.') , '.__('event.therapist').': '.$value->event->user->name;
                      $from = \DateTime::createFromFormat('Y-m-d H:i', $dateTimeFrom );
                      $to = \DateTime::createFromFormat('Y-m-d H:i', $dateTimeTill );
                      $link = \Spatie\CalendarLinks\Link::create(__('event.events'), $from, $to)->description($desc);

                      if(\Storage::exists('public/ics/Events-'.$value->id.'.ics'))
                        $file = 1;
                      else  
                        $file = 0;
                    @endphp

                        <tr>
                            <td>{{ $value->event->name }}</td>
                            <td><span style="display:none;">{{ $value->event->date}}</span> {{  __('keywords.'.\Carbon\Carbon::parse($value->event->date)->format('l').'') }}  - {{ \Carbon\Carbon::parse($value->event->date)->format($dateFormat)}} - {{ $value->event->time }}</td>
                            <td>{{ $value->event->duration }} min</td>
                            <td><a href="{{ Route('showUser',[session('business_name'),md5($value->event->user->id)]) }}" target="_blank">{{ $value->event->user->name }}</a></td>
                            
                            <!-- Check for card -->
                            @if($card)
                            <td class="cardUsede">{{ $card->name }}</td>
                            <td class="clipUsede">{{ $value->event->clips }}</td>
                            @else
                            <td class="cardUsede">-</td>
                            <td class="clipUsede">-</td>
                            @endif

                            <td>{{ $value->comment }}</td>

                            <!-- Check if guest -->
                            @if($value->event->is_guest)
                            <td>{{ $value->parent_slot ? __('profile.guest') : ($value->is_guest ? __('profile.yes') : __('profile.no')) }}</td>
                            @else
                            <td>{{ __('profile.no') }}</td>
                            @endif

                            <td>
                                @if($value->status)
                                <span class="badge bg-info">{{ __('profile.booked') }}</span>
                                @else
                                <span class="badge bg-warning">{{ __('profile.waiting_list') }}</span>
                                @endif
                            </td>

                            <td>{{ $value->created_at->format($dateFormat.($timeFormat == 12 ? ' h:i:s a' : ' H:i:s')) }}</td> 
                            <td>
                              <button class="btn btn-danger btn-sm mt-1 mb-1" data-id="{{ md5($value->id) }}" onclick="deleteBooking(this)">{{ __('profile.delete') }}</button>
                              <div class="dropdown">
                                <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown">{{ __('customer.add_to_calendar') }}
                                </button>
                                <div class="dropdown-menu">
                                  <a href="{{ $link->google() }}" class="dropdown-item" target="__blank()" >{{ __('customer.add_to_google_calendar') }}</a>
                                  <a href="{{ $link->yahoo() }}" class="dropdown-item" target="__blank()" >{{ __('customer.add_to_yahoo_calendar') }}</a>
                                  <a href="{{ $link->webOutlook() }}" class="dropdown-item" target="__blank()" >{{ __('customer.add_to_web_outlook_calendar') }}</a>
                                  <a href="{{ $link->webOffice() }}" class="dropdown-item" target="__blank()" >{{ __('customer.add_to_outlook_calendar') }}</a>
                                  @if( $file )
                                    <a href="{{'/getfile/Events-'.$value->id.'.ics' }}" class="dropdown-item" download="event.ics">{{ __('customer.file_download') }}</a>
                                  @else
                                    <a href="/ics/{{ $link->ics() }}" class="dropdown-item" download="event.ics">{{ __('customer.file_download') }}</a>
                                  @endif 
                                </div>
                              </div>
                            </td> 
                        </tr>
                    @endforeach
                  </tbody>
                </table>
              </div>
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
@stop

@section('scripts')

<script>

function deleteBooking(obj){

  //--------- For notification -----
  const Toast = Swal.mixin({
    toast: true,
    position: 'top-end',
    showConfirmButton: false,
    timer: 5000
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
        if(data['status'] == 'success'){
          Toast.fire({
              icon: 'success',
              title: ' {{ __('event.bhbds') }}'
          }); 
           //----- check if users on mobile device then remove paent tr also -----
          if(jQuery(thisTr).hasClass('child')){
            jQuery(thisTr).prev().remove();
          }
          jQuery(thisTr).remove();

          setTimeout(function () {
            location.reload(true);
          }, 1000);
                  
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


jQuery(function () {
  //---- For data tables ---------
  if( jQuery('#datatable3').length > 0 ){

    @foreach ( Config::get('languages') as $key => $val )
      @if(Lang::locale() == $key)
        var lang = '{{ $val['display'] }}';
      @endif
    @endforeach

    jQuery('#datatable3').DataTable({
      language: {
          url: '//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/'+lang+'.json'
      },
      "paging": true,
      "lengthChange": true,
      "searching": true,
      "ordering": true,
      "info": true,
      "autoWidth": false,
      "responsive": true,
      "order": [ 1, "asc" ],
    });
  }  
});
</script>

@stop