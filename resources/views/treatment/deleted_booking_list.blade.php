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
            <h1>{{ __('treatment.treatment_deleted_slots') }}</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="/">{{ __('keywords.home') }}</a></li>
              <li class="breadcrumb-item active">{{ __('treatment.treatment_deleted_slots') }}</li>
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
                    {{ __('treatment.deleted_bookings') }}
                  </h4>             
                </div>
                <!-- /.card-header -->
                <div class="card-body table-responsive p-0">
                <table class="table table-hover table-bordered text-nowrap">
                    <thead>
                        <tr class="dark">
                            <th>{{ __('treatment.date') }}</th>
                            <th>{{ __('treatment.time') }}</th>
                            <th>{{ __('keywords.name') }} </th>
                            <th>{{ __('keywords.email') }} </th>
                            <th>{{ __('keywords.number') }}</th>
                            <th>{{ __('treatment.treatment') }} </th>
                            <th>{{ __('treatment.odered') }} </th>
                            <th>{{ __('treatment.comment') }} </th>
                            <th>{{ __('treatment.deleted_time') }}</th>
                            @can('Dates Booking Restore')<th>{{ __('treatment.restore') }}</th>@endcan
                        </tr>
                    </thead>
                    <tbody>
                    @foreach($dates as $date)
                      @foreach( $date->deletedSlots as $booking )
                       @if($booking->user)
                        <tr>
                          <td>{{ __('keywords.'.\Carbon\Carbon::parse($date->date)->format('l').'') }}<br>{{ \Carbon\Carbon::parse($date->date)->format($dateFormat->value)}}</td>
                          <td>{{ $booking->time }}</td>
                          <td>{{ $booking->user->name }}</td>
                          <td>{{ $booking->user->email }}</td>
                          <td><a href="Tel:{{ $booking->user->number }}">{{ $booking->user->number }}</a></td>
                          <td>{{ $booking->treatment->treatment_name }} ({{ $booking->treatment->inter }} mins)</td>
                          <td>{{ \Carbon\Carbon::parse($booking->created_at)->format($dateFormat->value.($timeFormat->value == 12 ? ' h:i:s a' : ' H:i:s' ))}}</td>
                          <td>{{ $booking->comment }}</td>
                          <td>
                            {{ \Carbon\Carbon::parse($booking->updated_at)->format($dateFormat->value.($timeFormat->value == 12 ? ' h:i:s a' : ' H:i:s' )) }}
                          </td>
                          @can('Dates Booking Restore')
                            <td><button class="btn btn-danger btn-sm" data-id="{{md5($booking->id)}}" onclick="restoreBooking(this)">{{ __('treatment.restore') }}</button></td>
                          @endcan  
                        </tr>  
                       @endif 
                      @endforeach  
                    @endforeach  
                    </tbody>
                </table>
                </div>
                <!-- /.card-body -->
            </div>
            <!-- /.card -->
          </div>
        </div>
      </div>  
    </section> 
  </div>
  @stop

  @section('scripts')

    <script>
    @can('Dates Booking Restore')
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
          url: '/RestoreTreatmentBookingAjax',
          data: { 'id':id,'_token':token },
          dataType: 'json',
          success: function (data) {
            if(data == 'success'){
              Toast.fire({
                  icon: 'success',
                  title: ' {{ __('treatment.bhbrs') }}'
              });
              
              //------- Remove this tr from table ------
              jQuery(obj).closest('tr').remove();


            }
            else if(data == 'dateNotExist'){
              Toast.fire({
                  icon: 'error',
                  title: ' {{ __('treatment.prdfatttrb') }}'
              })
            }
            else if(data == 'bookingExist'){
              Toast.fire({
                  icon: 'error',
                  title: ' {{ __('treatment.abeost') }}'
              })
            }
          },
          error: function (data) {
            Toast.fire({
                  icon: 'error',
                  title: ' {{ __('treatment.pcydatta') }}'
              })
          }
        });
      }
    @endcan  
    </script>


  @stop
