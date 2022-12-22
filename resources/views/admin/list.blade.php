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
            @if(Route::current()->getName() == 'disableAdminlist')
                <h1>{{ __('users.disabled_users') }}</h1>
            @else
                <h1>{{ __('users.active_users') }}</h1>
            @endif
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="/">{{ __('keywords.home') }}</a></li>
              <li class="breadcrumb-item active">{{ __('users.users_list') }}</li>
            </ol>
          </div>
          <div class="col-md-12">
            @if(Route::current()->getName() == 'disableAdminlist')
                <a class="float-right btn btn-info btn-ms" href="{{ Route('adminlist',session('business_name')) }}">{{ __('users.active_users') }}</a>
            @else
                <a class="float-right btn btn-danger btn-ms" href="{{ Route('disableAdminlist',session('business_name')) }}">{{ __('users.disabled_users') }}</a>
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
                @if(Route::current()->getName() == 'disableAdminlist')
                    <h3 class="card-title">{{ __('users.disabled_users') }}</h3>
                @else
                    <h3 class="card-title">{{ __('users.active_users') }}</h3>
                @endif
              
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
                        <th>{{ __('users.cpr') }}</th>
                    @endif
                    @if($mdr)
                        <th>{{ __('users.med') }}</th>
                    @endif 
                    <th>{{ __('users.role') }}</th>
                    @can('Users Edit') 
                        <th>{{ __('users.is_therapist') }}</th>
                    @endcan
                    <th>{{ __('profile.action') }}</th>
                  </tr>
                  </thead>
                  <tbody>
                  @forelse( $admins as $admin )
                        <tr class="{{ md5($admin->id) }}">
                            <td>{{ $admin->name }}</td>
                            <td>{{ $admin->email }}</td>
                            <td>{{ $admin->number }}</td>
                            @if($cpr)
                                <td>{{ $admin->cprnr }}</td>
                            @endif
                            @if($mdr)    
                                <td>{{ $admin->mednr }}</td>
                            @endif
                            <td>
                                @if(is_numeric($admin->role))
                                @php
                                    $role = App\Models\Role::find($admin->role);
                                @endphp
                                @else
                                    @php $role = ''; @endphp
                                @endif

                                @if($role)    
                                    {{ $role->title }}
                                @else
                                    {{ $admin->role }}
                                @endif
                            </td>
                            @can('Users Edit') 
                            <td> <input type="checkbox" class="form-control" name="is_therapist" id="is_therapist" {{ $admin->is_therapist ? 'Checked' : ''  }} data-id="{{ $admin->id }}" onchange="updateTherapist(this)"></td>
                            @endcan
                            <td>
                                <a href="{{ Route('showUser',array(session('business_name'),md5($admin->id))) }}" class="btn btn-sm bg-success">
                                    <i class="fas fa-eye"></i> {{ __('keywords.view') }}
                                </a>
                                @can('Users Edit')  
                                    <a href="{{ Route('editadmin',array(session('business_name'),md5($admin->id))) }}" class="btn btn-sm bg-teal">
                                    <i class="fas fa-edit"></i> {{ __('keywords.edit') }}
                                    </a>
                                    <a href="{{ Route('changepassadmin',array(session('business_name'),md5($admin->id))) }}" class="btn btn-sm btn-info">
                                    <i class="fas fa-lock"></i> {{ __('users.change_pass') }}
                                    </a>
                                @endcan
                                @can('Users Delete')      
                                    <a href="javascript:;" onclick="deleteUser('{{md5($admin->id)}}')" class="btn btn-sm btn-danger">
                                    <i class="fas fa-trash"></i>  {{ __('keywords.delete') }}
                                    </a>
                                    @if($admin->is_active == 1)
                                        <a href="javascript:;" onclick="disableUser('{{md5($admin->id)}}')" class="btn btn-sm btn-warning">
                                            <i class="fas fa-times-circle"></i> {{ __('keywords.disable') }}
                                        </a>
                                    @else  
                                        <a href="javascript:;" onclick="enableUser('{{md5($admin->id)}}')" class="btn btn-sm btn-info">
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
@can('Users Delete')

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
        url: '/deleteadmin',
        data: {'id':id,'_token':token},
        dataType: 'json',
        success: function (data) {
            if(data['status'] == 'success'){
            jQuery('.'+id).remove();
            Toast.fire({
                icon: 'success',
                title: ' {{ __('keywords.ahbdfs') }}'
            });         
            }
            else if(data['status'] == 'exist'){
            Toast.fire({
                icon: 'error',
                title: ' {{ __('keywords.abeissycndc') }}'
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
@endcan
@can('Users Edit')
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
            }else if(data['status'] == 'exist'){
                Toast.fire({
                    icon: 'error',
                    title: ' {{ __('keywords.abeissycndic') }}'
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

function updateTherapist(obj){
     //--------- For notification -----
     const Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000
    });

    var token = $('meta[name="csrf-token"]').attr('content');
    var id = jQuery(obj).attr('data-id');
    var check = jQuery(obj).is(":checked");

    $.ajax({
        type: 'POST',
        url: '/updateTharapist',
        data: {'id':id,'check':check,'_token':token},
        dataType: 'json',
        success: function (data) {
            if(data['status'] == 'success'){
                Toast.fire({
                    icon: 'success',
                    title: ' {{ __('users.uhbu') }}'
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

@endcan
</script>
@stop