@extends('layouts.backend')

@section('content')
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>{{ __('profile.treatment_bookings_list') }}</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="/profile">{{ __('keywords.home') }}</a></li>
              <li class="breadcrumb-item active">{{ __('profile.treatment_bookings_list') }}</li>
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
                <h3 class="card-title">{{ __('profile.treatment_bookings_list') }}</h3>
                <a href="/booking" class="btn btn-info float-right">{{ __('treatment.book_treatment') }}</a>
              </div>
              <!-- /.card-header -->
              <div class="card-body">
                <table id="datatable3" class="table table-bordered table-striped">
                  <thead>
                  <tr>
                    <th>{{ __('profile.treatment_name') }}</th>
                    <th>{{ __('profile.date') }} - {{ __('profile.time') }}</th>
                    <th>{{ __('profile.duration') }}</th>
                    <th>{{ __('profile.instructor') }}</th>
                    <th class="cardUsedt">{{ __('profile.card_use') }}</th>
                    <th class="clipUsedt">{{ __('profile.clips_use') }}</th>
                    <th>{{ __('profile.comment') }}</th>
                    <th>{{ __('profile.location') }}</th>
                    <th>{{ __('profile.ordered') }}</th>
                    <th>{{ __('profile.action') }}</th>
                  </tr>
                  </thead>
                  <tbody>
                  @foreach($bookings as $key => $value)
                    @php
                      $card = App\Models\Card::find(App\Models\UsedClip::where([['is_active',1],['treatment_slot_id',$value->id]])->pluck('card_id'))->first();

                      $FromdateTime = \Carbon\Carbon::parse($value->date->date)->format("Y-m-d").' '.$value->time;
                      $TilldateTime = \Carbon\Carbon::parse($value->date->date)->format("Y-m-d").' '.$value->time;
                      $TilldateTime = \Carbon\Carbon::parse($TilldateTime)->addMinutes($value->treatment->inter)->format("Y-m-d H:i");

                      //----- ceating ics link -----//
                      $desc = __('treatment.treatment').': '.$value->treatment->treatment_name.' ('.($value->treatment->time_shown ?: $value->treatment->inter).') , '.__('treatment.therapist').': '.$value->date->user->name;
                      $from = \DateTime::createFromFormat('Y-m-d H:i', $FromdateTime );
                      $to = \DateTime::createFromFormat('Y-m-d H:i', $TilldateTime );
                      $link = \Spatie\CalendarLinks\Link::create(__('treatment.treatment'), $from, $to)->description($desc);

                      if(\Storage::exists('public/ics/Treatment-'.$value->id.'.ics'))
                        $file = 1;
                      else  
                        $file = 0;
                        
                    @endphp
                        <tr>
                            <td>{{ $value->treatment->treatment_name }}</td>
                            <td><span style="display:none;">{{ $FromdateTime }}</span> {{  __('keywords.'.\Carbon\Carbon::parse($value->date->date)->format('l').'') }} - {{ \Carbon\Carbon::parse($value->date->date)->format($dateFormat)}} - {{ $value->time }}</td>
                            <td>{{ $value->treatment->inter }} min</td>
                            <td><a href="{{ Route('showUser',[session('business_name'),md5($value->date->user->id)]) }}" target="_blank">{{ $value->date->user->name }}</a></td>
                            @if($value->clip)
                            <td class="cardUsedt">{{ $card->name }}</td>
                            <td class="clipUsedt">{{ $value->treatment->clips }}</td>
                            @else
                            <td class="cardUsedt">-</td>
                            <td class="clipUsedt">-</td>
                            @endif
                            <td>{{ $value->comment }}</td>
                            <td>{{ $value->date->description ?: 'N/A' }}</td>
                            <td>{{ $value->created_at->format($dateFormat.($timeFormat == 12 ? ' h:i:s a' : ' H:i:s')) }}</td>                            
                            <td> 
                            @if( $value->date->date >= date('Y-m-d') )
                              <button class="btn btn-danger btn-sm mt-1 mb-1" data-id="{{ $value->id }}" onclick="deleteBooking(this)" >{{ __('profile.delete') }}</button>
                              <div class="dropdown">
                                <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown">{{ __('customer.add_to_calendar') }}
                                </button>
                                <div class="dropdown-menu">
                                  <a href="{{ $link->google() }}" class="dropdown-item" target="__blank()" >{{ __('customer.add_to_google_calendar') }}</a>
                                  <a href="{{ $link->yahoo() }}" class="dropdown-item" target="__blank()" >{{ __('customer.add_to_yahoo_calendar') }}</a>
                                  <a href="{{ $link->webOutlook() }}" class="dropdown-item" target="__blank()" >{{ __('customer.add_to_web_outlook_calendar') }}</a>
                                  <a href="{{ $link->webOffice() }}" class="dropdown-item" target="__blank()" >{{ __('customer.add_to_outlook_calendar') }}</a>
                                  @if( $file )
                                    <a href="{{'/getfile/Treatment-'.$value->id.'.ics' }}" class="dropdown-item" download="treatment.ics">{{ __('customer.file_download') }}</a>
                                  @else
                                    <a href="/ics/{{ $link->ics() }}" class="dropdown-item" download="event.ics">{{ __('customer.file_download') }}</a>
                                  @endif 
                                </div>
                              </div>

                            @endif  
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

//------------- Delete booking ---------
function deleteBooking(obj){

  //--------- For notification -----
    const Toast = Swal.mixin({
      toast: true,
      position: 'top-end',
      showConfirmButton: false,
      timer: 5000
    });

  //----------- CSRF token ------------
  var token = $('meta[name="csrf-token"]').attr('content');
  var slotId = jQuery(obj).attr('data-id');
  var thisTr = jQuery(obj).closest('tr');

  $.ajax({
      type: 'POST',
      url: '/DeleteBooking',
      data: {"_token":token,"id":slotId},
      dataType: 'json',
      success: function (data) {
        if(data['status'] == 'success'){

          Toast.fire({
              icon: 'success',
              title: ' {{ __('treatment.bhbds') }}'
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
              title: ' {{ __('treatment.bdthbp') }}'
          })
        }
        else{
          Toast.fire({
              icon: 'error',
              title: ' {{ __('treatment.bcnbd') }}'
          })
        }
        //------ Remove loader ------
      },
      error: function (data) {
        Toast.fire({
              icon: 'error',
              title: ' {{ __('treatment.tiauetdb') }}'
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