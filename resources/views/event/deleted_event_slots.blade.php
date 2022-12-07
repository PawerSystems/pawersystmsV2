@extends('layouts.backend')

@section('content')
<style>
.table td, .table th{
  vertical-align: inherit;
}
.card-body.p-0 .table tbody>tr>td:first-of-type, .card-body.p-0 .table tbody>tr>th:first-of-type, .card-body.p-0 .table thead>tr>td:first-of-type, .card-body.p-0 .table thead>tr>th:first-of-type{
  padding-left: 0.7rem !important;
}
ul.p-a{ position:absolute; z-index:99; }
ul.p-a li{ cursor:pointer; }
</style>
  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>{{ __('event.event_deleted_bookings') }}</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">{{ __('keywords.home') }}</a></li>
              <li class="breadcrumb-item active">{{ __('event.event_deleted_bookings') }}</li>
            </ol>
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>

    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        <div class="row">
          <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                  <div class="card-tools">
                      <button type="button" class="btn bg-default btn-sm" data-card-widget="collapse">
                      <i class="fas fa-minus"></i>
                      </button>
                  </div>
                  <h4> 
                  {{ __('event.event_deleted_bookings') }}
                  </h4>             
                </div>
                <!-- /.card-header -->
                <div class="card-body table-responsive p-0">
                <table class="table table-hover table-bordered text-nowrap">
                    <thead>
                        <tr class="dark">
                            <th>{{ __('keywords.name') }} </th>
                            <th>{{ __('keywords.email') }}  </th>
                            <th>{{ __('keywords.number') }} </th>
                            <th>{{ __('event.guest') }}  </th>
                            <th>{{ __('event.event_name') }}  </th>
                            <th>{{ __('event.ordered') }}  </th>
                            <th>{{ __('event.deleted_time') }}  </th>
                            <th>{{ __('event.comment') }} </th>
                            <th>{{ __('event.restore') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach($slots as $key => $slot)
                        <tr>
                            <td>{{ $slot->user->name }}</td>
                            <td>{{ $slot->user->email }}</td>
                            <td>{{ $slot->user->number }}</td>
                            <td>{{ $slot->parent_slot ? 'Guest' : ($slot->is_guest ? 'Yes' : 'No') }}</td>
                            <td>{{ $slot->event->name }} - {{ \Carbon\Carbon::parse($slot->event->date)->format($dateFormat->value) }} - {{ $slot->event->time }} ({{ $slot->event->duration }} mins)</td>
                            <td>{{ \Carbon\Carbon::parse($slot->created_at)->format($dateFormat->value.($timeFormat->value == 12 ? ' h:i:s a' : ' H:i:s' )) }}</td>
                            <td>{{ \Carbon\Carbon::parse($slot->updated_at)->format($dateFormat->value.($timeFormat->value == 12 ? ' h:i:s a' : ' H:i:s' )) }}</td>
                            <td>{{ $slot->comment }}</td>
                            <td>
                            @can('Event Booking Restore')
                              <button class="btn btn-danger btn-sm" data-id="{{md5($slot->id)}}" onclick="restoreBooking(this)">{{ __('event.restore') }}</button>
                            @endcan  
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

          <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <nav aria-label="Contacts Page Navigation">
                        <ul class="pagination justify-content-center m-0">
                        {{$slots->links()}} 
                        </ul>
                    </nav>
                </div>
              </div> 
          </div>

        </div>
      </div>  
    </section> 
    
  </div>
  @stop

  @section('scripts')

    <script>
    @can('Event Booking Restore')
    function restoreBooking(obj)
    {
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
          url: '/RestoreEventBookingAjax',
          data: { 'id':id,'_token':token },
          dataType: 'json',
          success: function (data) {
            if(data['status'] == 'success'){
              var title = ' {{ __('event.bhbrs') }}';
              if(data['clipStatus'] == 'deleted')
              {
                title = ' {{ __('event.bhbrsbchbdboicic') }}';
              }
              Toast.fire({
                  icon: 'success',
                  title: title
              });
              //------- Remove this tr from table ------
              jQuery(obj).closest('tr').remove();
            }
            else if(data['status'] == 'eventNotExist'){
              Toast.fire({
                  icon: 'error',
                  title: ' {{ __('event.prefatttrb') }}'
              })
            }
            else if(data['status'] == 'bookingExist'){
              Toast.fire({
                  icon: 'error',
                  title: ' {{ __('event.abeose') }}'
              })
            }
          },
          error: function (data) {
            Toast.fire({
                  icon: 'error',
                  title: ' {{ __('event.pcydatta') }}'
              })
          }
        });
      }
      @endcan
    </script>


  @stop
