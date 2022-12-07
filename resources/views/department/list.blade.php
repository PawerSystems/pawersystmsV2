@extends('layouts.backend')

@section('content')
  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>{{ __('department.department_list') }}</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="{{ Route('dashboard',session('business_name')) }}">{{ __('keywords.home') }}</a></li>
              <li class="breadcrumb-item active">{{ __('department.department_list') }}</li>
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
                <h3 class="card-title">{{ __('department.department_list') }}</h3>
                @can('Department Create') 
                <button class="btn btn-success btn-sm float-right" data-toggle="modal" data-target="#modal-default">{{ __('department.add_new') }}</button>
                @endcan
              </div>
              <!-- /.card-header -->
              <div class="card-body">
                <table id="datatable" class="table table-bordered table-striped">
                  <thead>
                  <tr>
                    <th>{{ __('keywords.name') }}</th>
                    <th>{{ __('department.status') }}</th>
                    <th>{{ __('department.action') }}</th>
                  </tr>
                  </thead>
                  <tbody>
                  @foreach($departments as $key => $value)
                        <tr>
                            <td>
                              <p style="display:none;">{{ $value->name }}</p>
                                <input type="hidden" name="id" value="{{ $value->id }}" >
                                <input type="text" class="form-control" name="name" value="{{ $value->name }}">
                            </td>
                            <td class='text-center'>
                                <span class="badge bg-{{ $value->is_active ? 'success' : 'danger' }}" onclick="statusChange(this)" style="cursor: pointer;">
                                    {{ $value->is_active ?  __('department.active') :__('department.deactive') }}
                                </span>
                            </td>
                            <td class='text-center'>
                              @can('Department Edit') 
                                <button class="btn btn-info btn-sm" onclick="updateMethod(this)">
                                    <i class="nav-icon fas fa-edit"></i>
                                    {{ __('keywords.update') }}
                                </button>
                              @endcan  
                              @can('Department Delete')   
                                <button class="btn btn-danger btn-sm" onclick="deleteMethod(this)">
                                    <i class="nav-icon fas fa-trash"></i>
                                    {{ __('keywords.delete') }}
                                </button>
                              @endcan                                
                            </td>
                        </tr>
                    @endforeach
                  </tbody>
                  <tfoot>
                  <tr>
                    <th>{{ __('keywords.name') }}</th>
                    <th>{{ __('department.status') }}</th>
                    <th>{{ __('department.action') }}</th>
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

@can('Department Create') 
<div class="modal fade" id="modal-default">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">{{ __('department.create_department') }}</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form action="{{ Route('addDepartment',session('business_name')) }}" method="post" id="saveMethod">
          @csrf
          <div class="input-group mb-3">
              <input type="text" name="name" value="{{ old('name') }}" class="form-control" placeholder="{{ __('department.department_name') }}">
              <div class="input-group-append">
                  <div class="input-group-text">
                      <i class="fas fa-building"></i>
                  </div>
              </div>
          @if ($errors->has('name'))
              <span class="text-danger">{{ $errors->first('name') }}</span>
          @endif
          </div>
        </div>
        <div class="modal-footer justify-content-between">
          <button type="button" class="btn btn-default" data-dismiss="modal">{{ __('keywords.close') }}</button>
          <button type="submit" class="btn btn-primary">{{ __('keywords.save') }}</button>
        </div>
      </form>
    </div>
    <!-- /.modal-content -->
  </div>
  <!-- /.modal-dialog -->
</div>
<!-- /.modal -->
@endcan
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
  $('#saveMethod').validate({
    rules: {
        name: {
            required: true
        }
    },
    messages: {
        name: {
        required: "{{ __('department.please_enter_department_name') }}"
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


<script>
@can('Department Edit') 
function updateMethod(obj){
    const Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000
    });

    var name = jQuery(obj).closest('tr').find('input[name=name]').val();
    var id = jQuery(obj).closest('tr').find('input[name=id]').val();
    var token = $('meta[name="csrf-token"]').attr('content');
    $.ajax({
      type: 'POST',
      url: '/updateDepartmentAjax',
      data: { 'id':id,'name':name,'_token':token },
      dataType: 'json',
      success: function (data) {
        if(data == 'success'){
          Toast.fire({
              icon: 'success',
              title: ' {{ __('department.dhbus') }}'
          });
        }
        else if(data == 'exist'){
            Toast.fire({
              icon: 'error',
              title: ' {{ __('department.department_already_exist') }}'
          });
        }
        else{
          Toast.fire({
              icon: 'error',
              title: ' {{ __('department.tiauetud') }}'
          })
        }
      },
      error: function (data) {
        Toast.fire({
              icon: 'error',
              title: ' {{ __('department.pcydatta') }}'
          })
      }
    });
}
@endcan
@can('Department Delete') 
function deleteMethod(obj){
  const Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000
    });

    var id = jQuery(obj).closest('tr').find('input[name=id]').val();
    var token = $('meta[name="csrf-token"]').attr('content');

    $.ajax({
      type: 'POST',
      url: '/deleteDepartmentAjax',
      data: { 'id':id,'_token':token },
      dataType: 'json',
      success: function (data) {
        if(data == 'success'){
          Toast.fire({
              icon: 'success',
              title: ' {{ __('department.dhbds') }}'
          });
          jQuery(obj).closest('tr').remove();
        }
        else if(data == 'exist'){
            Toast.fire({
              icon: 'error',
              title: ' {{ __('department.diiusycndi') }}'
          });
        }
        else{
          Toast.fire({
              icon: 'error',
              title: ' {{ __('department.tiauetud') }}'
          })
        }
      },
      error: function (data) {
        Toast.fire({
              icon: 'error',
              title: ' {{ __('department.pcydatta') }}'
          })
      }
    });
}
@endcan
@can('Department Edit') 
function statusChange(obj){
    const Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000
    });

    var id = jQuery(obj).closest('tr').find('input[name=id]').val();
    var token = $('meta[name="csrf-token"]').attr('content');

    $.ajax({
      type: 'POST',
      url: '/updateDepStatusAjax',
      data: { 'id':id,'_token':token },
      dataType: 'json',
      success: function (data) {
        if(data == 'success'){
          Toast.fire({
              icon: 'success',
              title: ' {{ __('department.dhbus') }}'
          });

          if( jQuery(obj).hasClass('bg-danger') ){
            jQuery(obj).removeClass('bg-danger').addClass('bg-success');
            jQuery(obj).html('Active');
          }
          else{
            jQuery(obj).removeClass('bg-success').addClass('bg-danger');
            jQuery(obj).html('Deactive');
          }
        }
        else{
          Toast.fire({
              icon: 'error',
              title: ' {{ __('department.tiauetud') }}'
          })
        }
      },
      error: function (data) {
        Toast.fire({
              icon: 'error',
              title: '{{ __('department.pcydatta') }}'
          })
      }
    });

}
@endcan
</script>

@stop