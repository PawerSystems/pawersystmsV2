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
            <h1>{{ __('roles.role') }}</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="{{ Route('dashboard',session('business_name')) }}">{{ __('keywords.home') }}</a></li>
              <li class="breadcrumb-item active">{{ __('roles.role') }}</li>
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
                <h3 class="card-title">{{ __('roles.role_list') }}</h3>
              </div>
              <!-- /.card-header -->
              <div class="card-body">
                <table id="datatable" class="table table-bordered table-striped">
                  <thead>
                  <tr>
                    <th>{{ __('roles.title') }}</th>
                    <th>{{ __('roles.access') }}</th>
                    @can('Role Edit')
                    <th>{{ __('roles.action') }}</th>
                    @endcan
                  </tr>
                  </thead>
                  <tbody>
                  @foreach($roles as $value)
                    <tr class="{{ md5($value->id) }}">
                      <td>{{ $value->title }}</td>
                      <td>
                        @foreach($value->permissions as $permission)
                          <span class="badge bg-info badge-pill" style="font-size:15px;">{{ $permission->title }}</span>
                        @endforeach
                      </td> 
                      @can('Role Edit')
                      <td class='text-center'>
                        @if($value->title != 'Super Admin')
                          <a class="btn btn-success btn-sm mt-2" href="{{ Route('edirole',[session('business_name'),md5($value->id)]) }}">{{ __('keywords.edit') }}</a>
                          <button class="btn btn-danger btn-sm mt-2" onclick="deleteRole('{{ md5($value->id) }}')">{{ __('keywords.delete') }}</button>
                        @endif
                      </td>
                      @endcan
                    </tr>
                  @endforeach
                  </tbody>
                  <tfoot>
                    <tr>
                        <th>{{ __('roles.title') }}</th>
                        <th>{{ __('roles.access') }}</th>
                        @can('Role Edit')
                        <th>{{ __('roles.action') }}</th>
                        @endcan
                    </tr>
                  </tfoot>
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
  function deleteRole(id){
    //--------- For notification -----
    const Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000
    });

    var token = $('meta[name="csrf-token"]').attr('content');

    $.ajax({
        type: 'POST',
        url: '/deleterole',
        data: {'id':id,'_token':token},
        dataType: 'json',
        success: function (data) {
            if(data['status'] == 'success'){
            jQuery('.'+id).remove();
            Toast.fire({
                icon: 'success',
                title: ' {{ __('roles.rhbds') }}'
            });         
            }
            else if(data['status'] == 'exist'){
            Toast.fire({
                icon: 'error',
                title: ' {{ __('roles.rcnbdbiu') }}'
            });
            }
        },  
        error: function (data) {
            Toast.fire({
                icon: 'error',
                title: ' {{ __('roles.eotdr') }}'
            });
        }
    }); 
  }
</script>
@stop