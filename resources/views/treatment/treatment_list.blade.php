@extends('layouts.backend')

@section('content')
  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>{{ __('treatment.treatments') }}</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="{{ Route('dashboard',session('business_name')) }}">{{ __('keywords.home') }}</a></li>
              <li class="breadcrumb-item active">{{ __('treatment.treatments') }}</li>
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
                <h3 class="card-title">{{ __('treatment.treatment_list') }}</h3>
              </div>
              <!-- /.card-header -->
              <div class="card-body">
                <table id="datatable" class="table table-bordered table-striped">
                  <thead>
                  <tr>
                    <th>{{ __('treatment.treatment_name') }}</th>
                    <th>{{ __('treatment.interval') }}</th>
                    <th>{{ __('treatment.time_shown') }}</th>
                    @if($settings->value == 'true')
                    <th>{{ __('treatment.number_clips') }}</th>
                    @endif
                    <th>{{ __('treatment.price') }}</th>
                    <th>{{ __('treatment.status') }}</th>
                    <th>{{ __('treatment.visibility') }}</th>
                    <th>{{ __('treatment.insurance') }}</th>
                    @can('Treatment Edit')<th>{{ __('treatment.action') }}</th>@endcan
                  </tr>
                  </thead>
                  <tbody>
                  @foreach($treatments as $key => $value)
                        <tr>
                            <td>{{ $value->treatment_name }}</td>
                            <td>{{ $value->inter }}</td>
                            <td>{{ $value->time_shown }}</td>
                            @if($settings->value == 'true')
                            <td>{{ $value->clips }}</td>
                            @endif
                            <td>{{ $value->price ? $value->price : '--' }}</td>
                            <td class='text-center'>
                                <span class="badge bg-{{ $value->is_active ? 'success' : 'danger' }}">
                                    {{ $value->is_active ? __('treatment.active') :  __('treatment.deactive') }}
                                </span>
                            </td>
                            <td class='text-center'>
                              <span class="badge bg-{{ $value->is_visible ? 'success' : 'warning' }}">
                                  {{ $value->is_visible ? __('treatment.all') :  __('treatment.private') }}
                              </span>
                            </td>
                            <td class='text-center'>
                              <span class="badge bg-{{ $value->is_insurance ? 'success' : 'warning' }}">
                                  {{ $value->is_insurance ? __('treatment.yes') :  __('treatment.no') }}
                              </span>
                            </td>
                            @can('Treatment Edit')
                            <td class='text-center'>
                                <a class="btn btn-info btn-sm" href="{{ Route('edittreatment',array(session('business_name'),md5($value->id))) }}">
                                    <i class="nav-icon fas fa-edit"></i>
                                    {{ __('keywords.edit') }}
                                </a>
                                <a class="btn btn-danger btn-sm" href="{{ Route('deletetreatment',array(session('business_name'),md5($value->id))) }}">
                                    <i class="nav-icon fas fa-trash"></i>
                                    {{ __('keywords.delete') }}
                                </a>
                            </td>
                            @endcan
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
  
jQuery(document).ready(function(){  
  if( jQuery(window).width() < 768 ){
    jQuery('table.table').addClass('table-responsive');
  }
});
</script>

<!-- Showing error or success messages -->
@if(Session::get('success'))
<script type="text/javascript">
  jQuery(function() {
		const Toast = Swal.mixin({
		  toast: true,
		  position: 'top-end',
		  showConfirmButton: false,
		  timer: 3000
		});
        Toast.fire({
            icon: 'success',
            title: '{{ Session::get("success") }}'
      })
  }); 
</script> 
@elseif( Session::get('error') )
<script type="text/javascript">
  jQuery(function() {
		const Toast = Swal.mixin({
		  toast: true,
		  position: 'top-end',
		  showConfirmButton: false,
		  timer: 3000
		});
        Toast.fire({
            icon: 'error',
            title: '{{ Session::get("error") }}'
        })
  }); 
</script> 
@endif

@stop