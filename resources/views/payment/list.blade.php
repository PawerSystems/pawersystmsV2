@extends('layouts.backend')

@section('content')
  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>{{ __('payment.payment_method') }}</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="{{ Route('dashboard',session('business_name')) }}">{{ __('keywords.home') }}</a></li>
              <li class="breadcrumb-item active">{{ __('payment.payment_method') }}</li>
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
                <h3 class="card-title">{{ __('payment.method_list') }} </h3>
                @can('Payment Method Create')
                <button class="btn btn-success btn-sm float-right" data-toggle="modal" data-target="#modal-default">{{ __('payment.add_new') }}</button>
                @endcan
              </div>
              <!-- /.card-header -->
              <div class="card-body">
                <table id="datatable" class="table table-bordered table-striped">
                  <thead>
                  <tr>
                    <th>{{ __('payment.title') }}</th>
                    <th>{{ __('payment.status') }}</th>
                    <th>{{ __('payment.action') }}</th>
                  </tr>
                  </thead>
                  <tbody>
                  @foreach($methods as $key => $value)
                        <tr>
                            <td>
                              <p style="display:none;">{{ $value->title }}</p>
                                <input type="hidden" name="id" value="{{ $value->id }}" >
                                <input type="text" class="form-control" name="title" value="{{ $value->title }}">
                            </td>
                            <td class='text-center'>
                                <span class="badge bg-{{ $value->is_active ? 'success' : 'danger' }}" onclick="statusChange(this)" style="cursor: pointer;">
                                    {{ $value->is_active ? __('payment.active') :  __('payment.deactive') }}
                                </span>
                            </td>
                            <td class='text-center'>
                              @can('Payment Method Edit')
                                <button class="btn btn-info btn-sm" onclick="updateMethod(this)">
                                    <i class="nav-icon fas fa-edit"></i>
                                    {{ __('keywords.update') }}
                                </button>
                              @endcan  
                              @can('Payment Method Delete')  
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
                    <th>{{ __('payment.title') }}</th>
                    <th>{{ __('payment.status') }}</th>
                    <th>{{ __('payment.action') }}</th>
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


@can('Payment Method Create')
<div class="modal fade" id="modal-default">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">{{ __('payment.add_new_payment_method') }}</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form action="{{ Route('addMethod',session('business_name')) }}" method="post" id="saveMethod">
            @csrf
            <div class="input-group mb-3">
                <input type="text" name="title" value="{{ old('title') }}" class="form-control" placeholder="{{ __('payment.title') }}">
                <div class="input-group-append">
                    <div class="input-group-text">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                </div>
            @if ($errors->has('title'))
                <span class="text-danger">{{ $errors->first('title') }}</span>
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
        title: {
            required: true
        }
    },
    messages: {
        title: {
        required: "{{ __('payment.please_enter_method_title') }}."
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

@can('Payment Method Delete')
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
      url: '/deleteMethodAjax',
      data: { 'id':id,'_token':token },
      dataType: 'json',
      success: function (data) {
        if(data == 'success'){
          Toast.fire({
              icon: 'success',
              title: ' {{ __('payment.data_has_been_deletet_s') }}!!'
          });
          jQuery(obj).closest('tr').remove();
        }
        else if(data == 'exist'){
            Toast.fire({
              icon: 'error',
              title: ' {{ __('payment.pmiiusycndi') }}!!'
          });
        }
        else{
          Toast.fire({
              icon: 'error',
              title: ' {{ __('payment.tiauetud') }}.'
          })
        }
      },
      error: function (data) {
        Toast.fire({
              icon: 'error',
              title: '{{ __('payment.pcydatta') }}.'
          })
      }
    });
}
@endcan
@can('Payment Method Edit')
function updateMethod(obj){
    const Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000
    });

    var title = jQuery(obj).closest('tr').find('input[name=title]').val();
    var id = jQuery(obj).closest('tr').find('input[name=id]').val();
    var token = $('meta[name="csrf-token"]').attr('content');
    $.ajax({
      type: 'POST',
      url: '/updateMethodAjax',
      data: { 'id':id,'title':title,'_token':token },
      dataType: 'json',
      success: function (data) {
        if(data == 'success'){
          Toast.fire({
              icon: 'success',
              title: ' {{ __('payment.dhbus') }}!!'
          });
        }
        else if(data == 'exist'){
            Toast.fire({
              icon: 'error',
              title: ' {{ __('payment.payment_method_already_exist') }}!!'
          });
        }
        else{
          Toast.fire({
              icon: 'error',
              title: ' {{ __('payment.tiauetud') }}.'
          })
        }
      },
      error: function (data) {
        Toast.fire({
              icon: 'error',
              title: '{{ __('payment.pcydatta') }}.'
          })
      }
    });
}


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
      url: '/updateStatusAjax',
      data: { 'id':id,'_token':token },
      dataType: 'json',
      success: function (data) {
        if(data == 'success'){
          Toast.fire({
              icon: 'success',
              title: ' {{ __('payment.dhbus') }}!!'
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
              title: ' {{ __('payment.tiauetud') }}.'
          })
        }
      },
      error: function (data) {
        Toast.fire({
              icon: 'error',
              title: ' {{ __('payment.pcydatta') }}.'
          })
      }
    });

}
@endcan
</script>

@stop