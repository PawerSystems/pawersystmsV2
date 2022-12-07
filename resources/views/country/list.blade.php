@extends('layouts.backend')

@section('content')
<style>
.passed{ color:red; }
</style>
  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>{{ __('country.countries_list') }}</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="{{ Route('dashboard',session('business_name')) }}">{{ __('keywords.home') }}</a></li>
              <li class="breadcrumb-item active">{{ __('country.countries_list') }}</li>
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
                <span>
                    <h3 class="card-title">{{ __('country.countries_list') }}</h3>
                    <a href="{{ Route('creatCountry',session('business_name')) }}" class="btn btn-info float-right">{{ __('country.add_new') }}</a>
                </span>
              </div>
              <!-- /.card-header -->
              <div class="card-body">
                <table id="datatable" class="table table-bordered table-striped">
                  <thead>
                  <tr>
                    <th>{{ __('country.country_name') }}</th>
                    <th>{{ __('country.country_code') }}</th>
                    <th>{{ __('country.action') }}</th>
                  </tr>
                  </thead>
                  <tbody>
                    @foreach($countries as $key => $value)
                        <tr>
                            <td>{{ $value->name }}</td>
                            <td>{{ $value->code }}</td>
                            <td class='text-center'>
                                <a class="btn btn-info btn-sm mt-1" href="{{ Route('CountryEdit',array(session('business_name'),md5($value->id))) }}">
                                    <i class="nav-icon fas fa-edit"></i>
                                    {{ __('keywords.edit') }}
                                </a>
                                <a class="btn btn-danger btn-sm mt-1" href="{{ Route('CountryDelete',array(session('business_name'),md5($value->id))) }}">
                                    <i class="nav-icon fas fa-trash"></i>
                                    {{ __('keywords.delete') }}
                                </a>
                            </td>
                        </tr>
                    @endforeach
                  </tbody>
                  <tfoot>
                  <tr>
                    <th>{{ __('country.country_name') }}</th>
                    <th>{{ __('country.country_code') }}</th>
                    <th>{{ __('country.action') }}</th>
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