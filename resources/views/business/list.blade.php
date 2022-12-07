@extends("layouts.backend")

@section('content')


  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>{{ __('business.business_location_list') }}</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="{{ Route('dashboard',session('business_name')) }}">{{ __('keywords.home') }}</a></li>
              <li class="breadcrumb-item active">{{ __('business.business_location_list') }}</li>
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
                <h3 class="card-title">{{ __('business.business_location_list') }}</h3>
              </div>
              <!-- /.card-header -->
              <div class="card-body">
                <table id="datatable" class="table table-bordered table-striped">
                  <thead>
                  <tr>
                    <th>{{ __('business.business_id') }}</th>
                    <th>{{ __('business.business_name') }}</th>
                    <th>{{ __('business.status') }}</th>
                    <th>{{ __('business.create_date') }}</th>
                    <th>{{ __('business.last_login') }}</th>
                    <th>{{ __('business.last_booking') }}</th>
                    <th>{{ __('business.action') }}</th>
                  </tr>
                  </thead>
                  <tbody>
                  @foreach( $business as $key => $bz )
                  
                  @php

                    $lastBooking = '';

                    $thisLocationAdmin =  App\Models\User::where('role','!=','Customer')->where('business_id',$bz->id)->orderBy('updated_at','desc')->first()->updated_at;

                    $lastEventBooking = App\Models\EventSlot::where('is_active',1)->where('business_id',$bz->id)->orderBy('created_at','desc')->first();
                    
                    $lastTreatmentBooking = App\Models\TreatmentSlot::where('is_active',1)->where('business_id',$bz->id)->orderBy('created_at','desc')->first();

                    if($lastEventBooking != NULL && $lastTreatmentBooking != NULL){
                      $lastBooking = $lastTreatmentBooking->created_at;
                      if( strtotime($lastEventBooking->created_at) > strtotime($lastTreatmentBooking->created_at)){
                        $lastBooking = $lastEventBooking->created_at;
                      }
                    }elseif($lastEventBooking != NULL){
                      $lastBooking = $lastEventBooking->created_at;
                    }elseif($lastTreatmentBooking != NULL){
                      $lastBooking = $lastTreatmentBooking->created_at;
                    }

                  @endphp
                  <tr>
                    <td>{{ $bz->id }}</td>
                    <td>{{ $bz->business_name }}</td>
                    <td class='text-center'>
                    @if($bz->is_active) 
                      <span class="badge badge-success">{{ __('business.active') }}</span>
                    @else 
                      <span class="badge badge-danger">{{ __('business.deactive') }}</span>
                    @endif
                    </td>
                    <td>{{ $bz->created_at }}</td>
                    <td>{{ $thisLocationAdmin }}</td>
                    <td>{{ $lastBooking ?: 'N/A' }}</td>
                    <td>
                      <a href="{{ Route('businessView',array(session('business_name'),$bz->id) ) }}" class="btn btn-sm btn-success">{{ __('keywords.view') }}</a>&nbsp;
                      <a href="{{ Route('businessEdit',array(session('business_name'),$bz->id) ) }}" class="btn btn-sm btn-info">{{ __('keywords.edit') }}</a>&nbsp;
                      <!-- <a href="javascript:;" class="btn btn-sm btn-danger" onclick="deleteBusiness({{$bz->id}})">{{ __('keywords.delete') }}</a> -->
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

function deleteBusiness(id){
  bootbox.confirm({
    message: "<b>Are you sure you want to delete this Business?</b> <br>You may delete all related users as well as data of this business",
    buttons: {
        confirm: {
            label: 'Yes',
            className: 'btn-success'
        },
        cancel: {
            label: 'No',
            className: 'btn-danger'
        }
    },
    callback: function (result) {
      if(result){
        console.log(result);
      }
    }
  });
}
</script>
@stop