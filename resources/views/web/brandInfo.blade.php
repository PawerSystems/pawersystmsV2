@extends("layouts.backend")

@section('content')


<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>{{ __('web.brand_detail') }}</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="/">{{ __('keywords.home') }}</a></li>
              <li class="breadcrumb-item active">{{ __('web.brand_detail') }}</li>
            </ol>
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>

    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        <div class="row">
          <div class="col-md-3">

            <!-- Profile Image -->
            <div class="card card-primary card-outline">
              <div class="card-body box-profile">
                <div class="text-center">
                  <img class="img-fluid web-logo"
                       src="images/{{ $brand->logo ?: 'ps_logo.jpg' }}"
                       alt="{{ __('web.logo') }}">
                </div>

                <h3 class="profile-username text-center">{{ __('web.logo') }}</h3>

                <ul class="list-group list-group-unbordered mb-3">
                  <li class="list-group-item">
                    <b>{{ __('web.url') }}</b> <a class="float-right">{{ session('business_name') }}.{{ \Config::get('app.domain') }}</a>
                  </li>
                  <li class="list-group-item">
                    <b>{{ __('web.brand_name') }}</b> <a class="float-right">{{ $brand->brand_name }}</a>
                  </li>
                </ul>
              </div>
              <!-- /.card-body -->
            </div>
            <!-- /.card -->

          </div>
          <!-- /.col -->
          <div class="col-md-9">
          @if(Session::get('status'))
          <div class="alert alert-warning alert-dismissible">
            <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
              {{ Session::get('status') }}
          </div>
          @endif
            <div class="card card-primary card-outline">
              {{-- <div class="card-header p-2">
                <ul class="nav nav-pills">
                  <li class="nav-item"><a class="nav-link active" href="#details" data-toggle="tab">{{ __('web.update_brand_details') }}</a></li>
                </ul>
              </div> --}}
              <div class="card-body">
                <div class="tab-content">
               
                  <div class="active tab-pane" id="details">
                    <form id="updateProfile" class="form-horizontal" method="POST" action="{{ route('updateBrand',Session('business_name')) }}" enctype="multipart/form-data">
                    @csrf
                      <div class="form-group row">
                        <label for="inputName" class="col-sm-2 col-form-label">{{ __('web.brand_name') }}</label>
                        <div class="col-sm-10">
                          <input type="text" value="{{ $brand->brand_name }}" name="name" class="form-control" id="inputName" placeholder="{{ __('web.brand_name') }}">
                          @if ($errors->has('name'))
                              <span class="text-danger">{{ $errors->first('name') }}</span>
                          @endif
                        </div>
                      </div>
                      <div class="form-group row">
                        <label for="inputName" class="col-sm-2 col-form-label">{{ __('web.business_email') }}</label>
                        <div class="col-sm-10">
                          <input type="email" value="{{ $brand->business_email }}" name="email" class="form-control" id="inputName" placeholder="{{ __('web.business_email') }}">
                          @if ($errors->has('email'))
                              <span class="text-danger">{{ $errors->first('email') }}</span>
                          @endif
                        </div>
                      </div>
                      <div class="form-group row">
                        <label for="customFile"class="col-sm-2 col-form-label">{{ __('web.logo') }}</label>
                        <div class="input-group col-sm-10 pull-right">
                          <div class="custom-file">
                            <input type="file" class="custom-file-input" id="customFile" name="image">
                            <label class="custom-file-label" for="customFile">{{ __('web.choose_file') }}</label>
                          </div>
                        </div>
                        <div class="input-group offset-sm-2 col-sm-10 pull-right">
                          <i>( {{ __('web.logo_size') }} )</i>
                        </div>
                      </div>

                      <div class="form-group row">
                        <label for="customFile"class="col-sm-2 col-form-label">{{ __('web.home_page_banner') }}</label>
                        <div class="input-group col-sm-10 pull-right">
                          <div class="custom-file">
                            <input type="file" class="custom-file-input" id="customFile" name="banner">
                            <label class="custom-file-label" for="customFile">{{ __('web.choose_file') }}</label>
                          </div>
                        </div>
                      </div>
                      @can('Brand Details Edit')
                      <div class="form-group row">
                        <div class="col-sm-12">
                          <button type="submit" class="btn btn-info float-right">{{ __('keywords.submit') }}</button>
                        </div>
                      </div>
                      @endcan
                    </form>
                  </div>
                <!-- /.tab-content -->
              </div><!-- /.card-body -->
            </div>
            <!-- /.nav-tabs-custom -->
          </div>
          <!-- /.col -->
        </div>
        <!-- /.row -->
      </div><!-- /.container-fluid -->
    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->



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