@extends('layouts.backend')

@section('content')
@php $cpr = $dateFormat = $timeFormat = ''; @endphp
    @foreach($settings as $setting)

        @if($setting->key == 'cpr_emp_fields')
            @if($setting->value == 'true')
                @php $cpr = 1; @endphp
            @else
                @php $cpr = 0; @endphp
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
            <h1>{{ __('users.users_list') }} <span style="font-size:16px;">( {{ $totalAdmins }} )</span></h1> 
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
      <!-- Default box -->
        <div class="card card-solid">
            <div class="card-body pb-0">
                <div class="row d-flex align-items-stretch">
                @forelse( $admins as $admin )
                    {{-- @if ( $admin-> == 'Super Admin')
                        @continue
                    @endif --}}
                    @php
                        if($admin->is_therapist){
                            $is_therapist_color = 'blue';
                            $is_therapist = __('users.therapist');
                        }
                        else{
                            $is_therapist_color = 'red';
                            $is_therapist = __('users.not_therapist'); 
                        } 
                    @endphp
                    <div class="col-12 col-sm-6 col-md-4 d-flex align-items-stretch {{md5($admin->id)}}">
                        <div class="card bg-light">
                            <div class="card-header text-muted border-bottom-0">
                            @if(is_numeric($admin->role))
                                @php
                                    $role = App\Models\Role::find($admin->role);
                                @endphp
                            @else
                                @php $role = ''; @endphp
                            @endif

                            @if($role)    
                                {{ $role->title }} (<i style="color:{{$is_therapist_color}}">{{ $is_therapist }}</i>)
                            @else
                                {{ $admin->role }} (<i style="color:{{$is_therapist_color}}">{{ $is_therapist }}</i>)
                            @endif
                            
                            </div>
                            <div class="card-body pt-0">
                                <div class="row">
                                    <div class="col-7">
                                        <h2 class="lead"><b>{{ $admin->name }}</b></h2>
                                        <p class="text-muted text-sm"><b>{{ __('users.last_update') }}: </b><br> {{ \Carbon\Carbon::parse($admin->updated_at)->format($dateFormat.($timeFormat == 12 ? ' h:i:s a' : ' H:i:s' )) }} </p>
                                        <ul class="ml-4 mb-0 fa-ul text-muted">
                                            <li class="small"><span class="fa-li"><i class="fas fa-lg fa-envelope"></i></span> {{ __('keywords.email') }}: <br>{{ $admin->email }}</li>
                                            <li class="small"><span class="fa-li"><i class="fas fa-lg fa-phone"></i></span> {{ __('keywords.number') }}: <br>{{ $admin->number ? $admin->number  : 'N/A'}}</li>
                                            @if($cpr)
                                            <li class="small"><span class="fa-li"><i class="fas fa-lg fa-key"></i></span> {{ __('users.cpr') }}: <br>{{ $admin->cprnr ? $admin->cprnr  : '-'}}</li>
                                            <!-- <li class="small"><span class="fa-li"><i class="fas fa-lg fa-key"></i></span> {{ __('users.med') }}: <br>{{ $admin->mednr ? $admin->mednr  : '-'}}</li> -->
                                            @endif
                                        </ul>
                                    </div>
                                    <div class="col-5 text-center">
                                        @php
                                            if($admin->profile_photo_path){
                                                $img = $admin->profile_photo_path;
                                            }
                                            else{
                                                if($admin->gender == 'women'){ $img = 'avatar2.png'; }
                                                else{ $img =  'avatar5.png'; }
                                            }
                                        @endphp
                                        <img src="/images/{{ $img }}" alt="" class="img-circle img-fluid" style ='width:115px; height:115px;' >
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer">
                                <div class="text-right">
                                    <a href="{{ Route('showUser',array(session('business_name'),md5($admin->id))) }}" class="btn btn-sm bg-success mt-1">
                                    <i class="fas fa-eye"></i> {{ __('keywords.view') }}
                                    </a>
                                @can('Users Change Password')    
                                    <a href="{{ Route('changepassadmin',array(session('business_name'),md5($admin->id))) }}" class="btn btn-sm btn-info mt-1">
                                    <i class="fas fa-lock"></i> {{ __('users.change_pass') }}
                                    </a>
                                @endcan
                            @if($admin->id != Auth::user()->id) 
                                @can('Users Edit')
                                    <a href="{{ Route('editadmin',array(session('business_name'),md5($admin->id))) }}" class="btn btn-sm bg-teal mt-1">
                                    <i class="fas fa-edit"></i> {{ __('keywords.edit') }}
                                    </a>
                                @endcan      
                                @can('Users Delete')  
                                    <a href="javascript:;" onclick="deleteUser('{{md5($admin->id)}}')" class="btn btn-sm btn-danger mt-1">
                                    <i class="fas fa-trash"></i> {{ __('keywords.delete') }}
                                    </a>
                                    @if($admin->is_active == 1)
                                        <a href="javascript:;" onclick="disableUser('{{md5($admin->id)}}')" class="btn btn-sm btn-warning mt-1">
                                            <i class="fas fa-times-circle"></i> {{ __('keywords.disable') }}
                                        </a>
                                    @else  
                                        <a href="javascript:;" onclick="enableUser('{{md5($admin->id)}}')" class="btn btn-sm btn-info mt-1">
                                            <i class="fas fa-check"></i> {{ __('keywords.enable') }}
                                        </a>  
                                    @endif
                                @endcan 
                            @endif       
                                </div>
                            </div>
                        </div>    
                    </div>
                    @empty
                        <p>{{ __('keywords.no_record_found') }}</p>
                    @endforelse    

                </div>
            </div>
            <div class="card-footer">
            <nav aria-label="Contacts Page Navigation">
                <ul class="pagination justify-content-center m-0">
                {{ $admins->links() }} 
                </ul>
            </nav>
            </div> 
        </div>
    </section>   
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

@endcan
</script>
@stop