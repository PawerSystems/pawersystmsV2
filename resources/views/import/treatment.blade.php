@extends("layouts.backend")

@section('content')

<div class="content-wrapper">
    
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>{{ __('import.treatment_data') }}</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="/">{{ __('keywords.home') }}</a></li>
              <li class="breadcrumb-item active">{{ __('import.treatment_data') }}</li>
            </ol>
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>

    <div class="col-md-4" style="margin:auto; padding-top:30px;">
        <div class="card">
            <div class="card-body register-card-body">
                <p class="login-box-msg">{{ __('import.import_treatment_data') }}</p>

                <form action="{{ Route('import-treatment-data',session('business_name')) }}" method="post" id="importFormData" enctype="multipart/form-data">                    
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
                            <input type="file" class="custom-file-input" name="treatments">
                            <label class="custom-file-label">{{ __('import.treatment_file') }}</label>
                          </div>
                          <span class="text-info pull-right"><a href="/sample/treatment_sample.csv" download><i>&nbsp;&nbsp; {{__('import.sample_file')}} </i></a></span>
                        </div>
                    </div>
                    @if ($errors->has('treatments'))
                        <span class="text-danger">{{ $errors->first('treatments') }}</span>
                    @endif

                    <div class="form-group row">
                        <div class="input-group col-sm-12 pull-right">
                          <div class="custom-file">
                            <input type="file" class="custom-file-input" name="dates">
                            <label class="custom-file-label">{{ __('import.dates_file') }}</label>
                          </div>
                          <span class="text-info pull-right"><a href="/sample/date_sample.csv" download><i>&nbsp;&nbsp; {{__('import.sample_file')}} </i></a></span>
                        </div>
                    </div>
                    @if ($errors->has('dates'))
                        <span class="text-danger">{{ $errors->first('dates') }}</span>
                    @endif

                    <div class="form-group row">
                        <div class="input-group col-sm-12 pull-right">
                          <div class="custom-file">
                            <input type="file" class="custom-file-input" name="date_bookings">
                            <label class="custom-file-label">{{ __('import.date_bookings_file') }}</label>
                          </div>
                          <span class="text-info pull-right"><a href="/sample/Date_bookings.csv" download><i>&nbsp;&nbsp; {{__('import.sample_file')}} </i></a></span>
                        </div>
                    </div>
                    @if ($errors->has('date_bookings'))
                        <span class="text-danger">{{ $errors->first('date_bookings') }}</span>
                    @endif

                    <div class="form-group row">
                        <div class="input-group col-sm-12 pull-right">
                          <div class="custom-file">
                            <input type="file" class="custom-file-input" name="customers_details">
                            <label class="custom-file-label">{{ __('import.users_details_file') }}</label>
                          </div>
                          <span class="text-info pull-right"><a href="/sample/users_sample.csv" download><i>&nbsp;&nbsp; {{__('import.sample_file')}} </i></a></span>
                        </div>
                    </div>
                    @if ($errors->has('customers_details'))
                        <span class="text-danger">{{ $errors->first('customers_details') }}</span>
                    @endif

                    <div class="form-group row">
                        <div class="input-group col-sm-12 pull-right">
                          <div class="custom-file">
                            <input type="file" class="custom-file-input" name="departments">
                            <label class="custom-file-label">{{ __('import.departments_file') }}</label>
                          </div>
                          <span class="text-info pull-right"><a href="/sample/Departments.csv" download><i>&nbsp;&nbsp; {{__('import.sample_file')}} </i></a></span>
                        </div>
                    </div>
                    @if ($errors->has('departments'))
                        <span class="text-danger">{{ $errors->first('departments') }}</span>
                    @endif

                    <div class="form-group row">
                        <div class="input-group col-sm-12 pull-right">
                          <div class="custom-file">
                            <input type="file" class="custom-file-input" name="treatment_parts">
                            <label class="custom-file-label">{{ __('import.treatment_parts_file') }}</label>
                          </div>
                          <span class="text-info pull-right"><a href="/sample/TreatmntParts.csv" download><i>&nbsp;&nbsp; {{__('import.sample_file')}} </i></a></span>
                        </div>
                    </div>
                    @if ($errors->has('treatment_parts'))
                        <span class="text-danger">{{ $errors->first('treatment_parts') }}</span>
                    @endif

                    <div class="form-group row">
                        <div class="input-group col-sm-12 pull-right">
                          <div class="custom-file">
                            <input type="file" class="custom-file-input" name="payment_method">
                            <label class="custom-file-label">{{ __('import.payment_method_file') }}</label>
                          </div>
                          <span class="text-info pull-right"><a href="/sample/Payment_methods.csv" download><i>&nbsp;&nbsp; {{__('import.sample_file')}} </i></a></span>
                        </div>
                    </div>
                    @if ($errors->has('payment_method'))
                        <span class="text-danger">{{ $errors->first('payment_method') }}</span>
                    @endif

                    <div class="form-group row">
                        <div class="input-group col-sm-12 pull-right">
                          <div class="custom-file">
                            <input type="file" class="custom-file-input" name="cards_details">
                            <label class="custom-file-label">{{ __('import.cards_details_file') }}</label>
                          </div>
                          <span class="text-info pull-right"><a href="/sample/Cards.csv" download><i>&nbsp;&nbsp; {{__('import.sample_file')}} </i></a></span>
                        </div>
                    </div>
                    @if ($errors->has('cards_details'))
                        <span class="text-danger">{{ $errors->first('cards_details') }}</span>
                    @endif

                    <div class="form-group row">
                        <div class="input-group col-sm-12 pull-right">
                          <div class="custom-file">
                            <input type="file" class="custom-file-input" name="card_use_detail">
                            <label class="custom-file-label">{{ __('import.card_use_detail_file') }}</label>
                          </div>
                          <span class="text-info pull-right"><a href="/sample/ClipsUsed.csv" download><i>&nbsp;&nbsp; {{__('import.sample_file')}} </i></a></span>
                        </div>
                    </div>
                    @if ($errors->has('card_use_detail'))
                        <span class="text-danger">{{ $errors->first('card_use_detail') }}</span>
                    @endif

                    <div class="form-group row">
                      <div class="input-group col-sm-12 pull-right">
                        <div class="custom-file">
                          <input type="file" class="custom-file-input" name="journals_detail">
                          <label class="custom-file-label">{{ __('import.journals_detail_file') }}</label>
                        </div>
                        <span class="text-info pull-right"><a href="/sample/JournalsDetails.csv" download><i>&nbsp;&nbsp; {{__('import.sample_file')}} </i></a></span>
                      </div>
                    </div>
                    @if ($errors->has('journals_detail'))
                        <span class="text-danger">{{ $errors->first('journals_detail') }}</span>
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
        treatments: {
            required: true,
            extension: "csv"
        },
        dates: {
            required: true,
            extension: "csv"
        },
        date_bookings: {
            required: true,
            extension: "csv"
        },
        customers_details: {
            required: true,
            extension: "csv"
        },
        departments: {
            required: true,
            extension: "csv"
        },
        treatment_parts: {
            required: true,
            extension: "csv"
        },
        payment_method: {
            required: true,
            extension: "csv"
        },
        cards_details: {
            required: true,
            extension: "csv"
        },
        card_use_detail: {
            required: true,
            extension: "csv"
        },
        journals_detail: {
            required: true,
            extension: "csv"
        }
    },
    messages: {
        business_id: {
            required: "{{ __('import.select_one_business') }}",
        },
        treatments: {
            required: "{{ __('import.please_uplaod_treatments_file') }}",
            extension: "{{ __('import.only_excel_type_files_can_upload') }}",
        },
        dates: {
            required: "{{ __('import.please_uplaod_dates_file') }}",
            extension: "{{ __('import.only_excel_type_files_can_upload') }}",
        },
        date_bookings: {
            required: "{{ __('import.please_uplaod_date_bookings_file') }}",
            extension: "{{ __('import.only_excel_type_files_can_upload') }}",
        },
        customers_details: {
            required: "{{ __('import.please_uplaod_users_details_file') }}",
            extension: "{{ __('import.only_excel_type_files_can_upload') }}",
        },
        departments: {
            required: "{{ __('import.please_uplaod_departments_file') }}",
            extension: "{{ __('import.only_excel_type_files_can_upload') }}",
        },
        treatment_parts: {
            required: "{{ __('import.please_uplaod_treatment_parts_file') }}",
            extension: "{{ __('import.only_excel_type_files_can_upload') }}",
        },
        payment_method: {
            required: "{{ __('import.please_uplaod_payment_method_file') }}",
            extension: "{{ __('import.only_excel_type_files_can_upload') }}",
        },
        cards_details: {
            required: "{{ __('import.please_uplaod_cards_details_file') }}",
            extension: "{{ __('import.only_excel_type_files_can_upload') }}",
        },
        card_use_detail: {
            required: "{{ __('import.please_uplaod_card_use_details_file') }}",
            extension: "{{ __('import.only_excel_type_files_can_upload') }}",
        },
        journals_detail: {
            required: "{{ __('import.please_uplaod_journal_details_file') }}",
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