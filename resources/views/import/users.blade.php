@extends("layouts.backend")

@section('content')

<div class="content-wrapper">
    
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>{{ __('import.users_data') }}</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="/">{{ __('keywords.home') }}</a></li>
              <li class="breadcrumb-item active">{{ __('import.users_data') }}</li>
            </ol>
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>

    <div class="col-md-4" style="margin:auto; padding-top:30px;">
        <div class="card">
            <div class="card-body register-card-body">
                <p class="login-box-msg">{{ __('import.import_user_data') }}</p>

                <form action="{{ Route('import-users-data',session('business_name')) }}" method="post" id="importFormData" enctype="multipart/form-data">                    
                    @csrf

                    <div class="form-group row">
                      <div class="col-sm-12">
                        <select class="form-control" name="business_id">
                          <option value="">{{ __('import.select_business') }}</option>
                          @foreach ($businesses as $business )
                            <option value="{{ $business->id }}">{{ $business->business_name }}</option>
                          @endforeach
                        </select>
                      </div>
                  </div>
                  @if ($errors->has('business_id'))
                      <span class="text-danger">{{ $errors->first('business_id') }}</span>
                  @endif


                    <div class="form-group row">
                        <div class="input-group col-sm-12 pull-right">
                          <div class="custom-file">
                            <input type="file" class="custom-file-input" name="customers_details">
                            <label class="custom-file-label">{{ __('import.users_details_file') }}</label>
                          </div>
                          <span class="text-info pull-right"><a href="/sample/user_sample.csv" download><i>&nbsp;&nbsp; {{__('import.sample_file')}} </i></a></span>
                        </div>
                    </div>
                    @if ($errors->has('customers_details'))
                        <span class="text-danger">{{ $errors->first('customers_details') }}</span>
                    @endif

                   
                    <div class="col-4">
                        <button type="submit" class="btn btn-primary btn-block">{{ __('import.import') }}</button>
                    </div>
                    <!-- /.col -->
                 
                </form>
            </div>
        </div> 
    </div>
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


<script type="text/javascript">
$(document).ready(function () {
  $.validator.setDefaults({
    submitHandler: function () {
        return true;
    }
  });
  $('#importFormData').validate({
    rules: {
        business_id: {
            required: true,
        },
        customers_details: {
            required: true,
            extension: "csv"
        }
    },
    messages: {
        business_id: {
            required: "{{ __('import.select_one_business') }}",
        },
        customers_details: {
            required: "{{ __('import.please_uplaod_users_details_file') }}",
            extension: "{{ __('import.only_excel_type_files_can_upload') }}",
        }
    },
    errorElement: 'span',
    errorPlacement: function (error, element) {
      error.addClass('invalid-feedback');
      element.closest('.form-group').append(error);
    },
    highlight: function (element, errorClass, validClass) {
      $(element).addClass('is-invalid');
    },
    unhighlight: function (element, errorClass, validClass) {
      $(element).removeClass('is-invalid');
    }
  });
});
</script>
@stop