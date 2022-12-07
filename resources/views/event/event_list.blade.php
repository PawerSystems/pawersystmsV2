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
            <h1>{{ __('event.event_list') }}</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="{{ Route('dashboard',session('business_name')) }}">{{ __('keywords.home') }}</a></li>
              <li class="breadcrumb-item active">{{ __('event.event_list') }}</li>
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
                <h3 class="card-title">{{ __('event.event_list') }}</h3>
                @if(!empty($var) && $var == 'past')
                  <a class="float-right btn btn-sm btn-info" href="{{ Route('eventsList',[session('business_name')]) }}">{{ __('event.active_events') }}</a>
                @else
                  <a class="float-right btn btn-sm btn-info" href="{{ Route('eventsList',[session('business_name'),'past']) }}">{{ __('event.past_events') }}</a>
                @endif
              </div>
              <!-- /.card-header -->
              <div class="card-body">
                <table id="datatable" class="table table-bordered table-striped">
                  <thead>
                  <tr>
                    <th>{{ __('event.event_name') }}</th>
                    <th>{{ __('event.duration') }}</th>
                    <th>{{ __('event.event_date') }}</th>
                    <th>{{ __('event.time') }}</th>
                    <th>{{ __('event.slots') }}</th>
                    <th>{{ __('event.price') }}</th>
                    @if($settings->value == 'true')
                    <th>{{ __('event.clips') }}</th>
                    @endif
                    <th>{{ __('event.therapist') }}</th>
                    <th>{{ __('event.description') }}</th>
                    <th>{{ __('event.guest_allow') }}</th>
                    <th>{{ __('event.min_bookings') }}</th>
                    <th>{{ __('event.action') }}</th>
                  </tr>
                  </thead>
                  <tbody>
                  @foreach($events as $key => $value)
                        <tr>
                            <td>{{ $value->name }}</td>
                            <td class='text-center'>{{ $value->duration }}</td>
                            <td><span class="v-none">{{ $value->date }}</span>{{ \Carbon\Carbon::parse($value->date)->format($dateFormat->value) }}</td>
                            <td class='text-center'>{{ $value->time}}</td>
                            <td class='text-center'>{{ $value->slots}}</td>
                            <td class='text-center'>{{ $value->price ? $value->price : '--'}}</td>
                            @if($settings->value == 'true')
                            <td class='text-center'>{{ $value->clips ? $value->clips : '--'}}</td>
                            @endif
                            <td class='text-center'>{{ $value->user->name}}</td>
                            <td>{{ $value->description}}</td>
                            <td class='text-center'><span class="badge bg-{{ $value->is_guest ? 'success' : 'warning'}}">{{ $value->is_guest ? __('event.enable') :  __('event.disable') }}</span></td>
                            <td class='text-center'>{{ $value->min_bookings ?: 0}}</td>
                            <td class='text-center'>
                              @can('Event Edit')
                                <a class="btn btn-info btn-sm mt-2" href="{{ Route('editEvent',array(session('business_name'),md5($value->id))) }}">
                                    <i class="nav-icon fas fa-edit"></i>
                                    {{ __('keywords.edit') }}
                                </a>
                              @endcan
                              @can('Event Delete')  
                                <a class="btn btn-danger btn-sm mt-2" href="javascript:;" data-id="{{ md5($value->id) }}" onclick="DeleteConfirm(this)">
                                    <i class="nav-icon fas fa-trash"></i>
                                    {{ __('keywords.delete') }}
                                </a>
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

@can('Event Delete')
function DeleteConfirm(obj){

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
    url: '/deleteEvent',
    data: { 'id':id,'_token':token },
    dataType: 'json',
    success: function (data) {
      if(data == 'success'){
        Toast.fire({
            icon: 'success',
            title: ' {{ __('event.ehbds') }}'
        });
        
        //------- Remove this tr from table ------
        jQuery(obj).closest('tr').remove();

      }
      else if(data == 'exist'){
        Toast.fire({
            icon: 'error',
            title: ' {{ __('event.tasbite') }}'
        })
      }
      else{
        Toast.fire({
            icon: 'error',
            title: ' {{ __('event.tiauetde') }}'
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