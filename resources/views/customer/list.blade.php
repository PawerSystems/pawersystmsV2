@extends('layouts.backend')

@section('content')
@php $cpr = $dateFormat = $timeFormat = $mdr = ''; @endphp
    @foreach($settings as $setting)

        @if($setting->key == 'cpr_emp_fields')
            @if($setting->value == 'true')
                @php $cpr = 1; @endphp
            @else
                @php $cpr = 0; @endphp
            @endif
        
        @elseif($setting->key == 'mdr_field')
            @if($setting->value == 'true')
                @php $mdr = 1; @endphp
            @else
                @php $mdr = 0; @endphp
            @endif    

        @elseif($setting->key == 'date_format')
            @php $dateFormat = $setting->value; @endphp 
            
        @elseif($setting->key == 'time_format')
            @php $timeFormat = $setting->value; @endphp 

        @endif

    @endforeach
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>{{ __('customer.customers_list') }}</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="/">{{ __('keywords.home') }}</a></li>
              <li class="breadcrumb-item active">{{ __('customer.customers_list') }}</li>
            </ol>
          </div>
          <div class="col-md-12">
            @if(Route::current()->getName() == 'disablecustomerlist')
                <a class="float-right btn btn-info btn-ms" href="{{ Route('customerlist',session('business_name')) }}">{{ __('users.active_users') }}</a>
            @else
                <a class="float-right btn btn-danger btn-ms" href="{{ Route('disablecustomerlist',session('business_name')) }}">{{ __('users.disabled_users') }}</a>
            @endif
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
              <h3 class="card-title">{{ __('customer.customers_list') }}</h3>
              </div>
              <!-- /.card-header -->
              <div class="card-body">
                <table id="datatable" class="table table-bordered table-striped">
                  <thead>
                  <tr>
                    <th>{{ __('keywords.name') }}</th>
                    <th>{{ __('keywords.email') }}</th>
                    <th>{{ __('keywords.number') }}</th>
                    @if($cpr)
                        <th>{{ __('customer.cpnr') }}</th>
                    @endif
                    @if($mdr)
                        <th>{{ __('customer.mednr') }}</th>
                    @endif 
                    <th>{{ __('customer.last_update') }}</th>                 
                    <th>{{ __('profile.action') }}</th>
                  </tr>
                  </thead>
                  <tbody>
                  @forelse( $customers as $customer )
                        <tr class="{{ md5($customer->id) }}">
                            <td>{{ $customer->name }}</td>
                            <td>{{ $customer->email }}</td>
                            <td>{{ $customer->number }}</td>
                            @if($cpr)
                                <td>{{ $customer->cprnr }}</td>
                            @endif
                            @if($mdr)    
                                <td>{{ $customer->mednr }}</td>
                            @endif
                            <td><span class="v-none">{{ $customer->updated_at }}</span> {{ \Carbon\Carbon::parse($customer->updated_at)->format($dateFormat.($timeFormat == 12 ? ' h:i:s a' : ' H:i:s' )) }}</td>
                            <td>
                            @can('Customer Edit')  
                                    <a href="{{ Route('editcustomer',array(session('business_name'),md5($customer->id))) }}" class="btn btn-sm bg-teal">
                                    <i class="fas fa-edit"></i> {{ __('keywords.edit') }}
                                    </a>
                                    <a href="{{ Route('changepasscustomer',array(session('business_name'),md5($customer->id))) }}" class="btn btn-sm btn-info">
                                    <i class="fas fa-lock"></i> {{ __('customer.change_pass') }}
                                    </a>
                                @endcan
                                @can('Customer Delete')      
                                    <a href="javascript:;" onclick="deleteUser('{{md5($customer->id)}}')" class="btn btn-sm btn-danger">
                                    <i class="fas fa-trash"></i>  {{ __('keywords.delete') }}
                                    </a>
                                    @if($customer->is_active == 1)
                                        <a href="javascript:;" onclick="disableUser('{{md5($customer->id)}}')" class="btn btn-sm btn-warning">
                                            <i class="fas fa-times-circle"></i> {{ __('keywords.disable') }}
                                        </a>
                                    @else  
                                        <a href="javascript:;" onclick="enableUser('{{md5($customer->id)}}')" class="btn btn-sm btn-info">
                                            <i class="fas fa-check"></i> {{ __('keywords.enable') }}
                                        </a>  
                                    @endif
                                @endcan  
                            </td>
                        </tr>
                    @empty
                        <p>{{ __('keywords.no_record_found') }}</p>
                    @endforelse
                  </tbody>
                  <tfoot>
                  <tr>
                    <th>{{ __('keywords.name') }}</th>
                    <th>{{ __('keywords.email') }}</th>
                    <th>{{ __('keywords.number') }}</th>
                    @if($cpr)
                        <th>{{ __('customer.cpnr') }}</th>
                    @endif
                    @if($mdr)
                        <th>{{ __('customer.mednr') }}</th>
                    @endif               
                    <th>{{ __('customer.last_update') }}</th>                      
                    <th>{{ __('profile.action') }}</th>
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

<script>

    function deleteUser(id){

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
            url: '/deletecustomer',
            data: {'id':id,'_token':token},
            dataType: 'json',
            success: function (data) {
                if(data['status'] == 'success'){
                  jQuery('.'+id).remove();
                  Toast.fire({
                      icon: 'success',
                      title: ' {{ __('customer.chbdfs') }}'
                  });         
                }
                else if(data['status'] == 'exist'){
                  Toast.fire({
                      icon: 'error',
                      title: ' {{ __('customer.cbeissycndc') }}'
                  });
                }
            },  
            error: function (data) {
                Toast.fire({
                    icon: 'error',
                    title: ' {{ __('event.eotdb') }}'
                });
            }
        }); 
    }

    
function disableUser(id){

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
      url: '/disableuser',
      data: {'id':id,'_token':token},
      dataType: 'json',
      success: function (data) {
          if(data['status'] == 'success'){
              jQuery('.'+id).remove();
              Toast.fire({
                  icon: 'success',
                  title: ' {{ __('users.user_has_been_disable') }}'
              });         
          }
      },  
      error: function (data) {
          Toast.fire({
              icon: 'error',
              title: ' {{ __('users.uetdu') }}'
          });
      }
  }); 
}

function enableUser(id){

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
      url: '/enableuser',
      data: {'id':id,'_token':token},
      dataType: 'json',
      success: function (data) {
          if(data['status'] == 'success'){
              jQuery('.'+id).remove();
              Toast.fire({
                  icon: 'success',
                  title: ' {{ __('users.user_has_been_enabled') }}'
              });         
          }
      },  
      error: function (data) {
          Toast.fire({
              icon: 'error',
              title: ' {{ __('users.uetau') }}'
          });
      }
  }); 
}


</script>
@stop