@extends("layouts.backend")

@section('content')

<div class="content-wrapper"> 

    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>{{ __('roles.edit_role') }}</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="{{ Route('dashboard',session('business_name')) }}">{{ __('keywords.home') }}</a></li>
              <li class="breadcrumb-item active">{{ __('roles.edit_role') }}</li>
            </ol>
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>

    <div class="row"> 
        <div class="col-md-3"></div>  
        <div class="col-md-6 col-sm-12">
        <form action="{{ Route('updateRole',session('business_name')) }}" method="post" id="saveBusiness">
                    @csrf
                    <input type="hidden" name="id" value="{{ md5($role->id) }}">
            <div class="card card-info">
                    <div class="card-header">
                        <h3 class="card-title">{{ __('roles.edit_role') }}</h3>
                    </div>
                    
                    <div class="card-body">
                        <!-- Title -->
                        <div class="form-group">
                            <label>{{ __('roles.title') }}:</label>
                            <div class="input-group">
                                <div class="input-group-append">
                                    <div class="input-group-text"><i class="fa fa-heading"></i></div>
                                </div>
                                <input type="text" name="title" value="{{ $role->title }}" class="form-control float-right" id="title">
                            </div>
                        </div>
                        <!-- Permissions-->
                        <div class="input-group mb-3">
                            <label style="width: 100%;">{{ __('roles.permissions') }}:</label>
                            <div class="input-group-append">
                                <div class="input-group-text">
                                    <span class="fas fa-shield-alt"></span>
                                </div>
                            </div>
                            <select class="form-control select2bs4" name="permissions[]"  id="permissions" multiple require>
                                @foreach($permissions as $permission)
                                @php
                                    $check = '';
                                @endphp
                                    @if(in_array($permission->id, $role->permissions->pluck('id')->toArray()))
                                        @php
                                            $check = 'selected';
                                        @endphp
                                    @endif
                                    <option value="{{ $permission->id }}" {{ $check }}>
                                    {{$permission->title}}</option>
                                @endforeach
                            </select> 
                        </div>
                        
                        @if ($errors->has('permissions'))
                                <span class="text-danger">{{ $errors->first('permissions') }}</span>
                            @endif
                    </div>
                    <div class="card-footer">
                    <button type="submit" class="btn btn-info">{{ __('keywords.save') }}</button>
                    </div>
                </form>
            </div>
            <!-- /.card -->
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
  $('#saveBusiness').validate({
    rules: {
        title: {
            required: true
        },
        permissions: {
            required: true
        },
    },
    messages: {
        title: {
            required: "{{ __('keywords.please_enter_title_of_role') }}"
        },
        permissions: {
            required: "{{ __('roles.please_select_at_least_1_permission') }}"
        }
    },
    errorElement: 'span',
    errorPlacement: function (error, element) {
      error.addClass('invalid-feedback');
      element.closest('.input-group').append(error);
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


<!-- Page script -->
<script>
  $(function () {
  
    //Initialize Select2 Elements
    $('.select2bs4').select2({
      theme: 'bootstrap4'
    });
 
  })
</script>
@stop