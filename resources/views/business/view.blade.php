@extends("layouts.backend")

@section('content')
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>{{ __('business.business_detail') }}</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="{{ Route('dashboard',session('business_name')) }}">{{ __('keywords.home') }}</a></li>
              <li class="breadcrumb-item active">{{ __('business.business_detail') }}</li>
            </ol>
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>
   <!-- Main content -->
   <section class="content">   
        <!-- Default box -->
        <div class="card card-solid">

            <div class="row card-body pb-0">
                <div class="col-12">
                    <h3 class="text-primary"><i class="fas fa-paint-brush"></i> {{ __('business.business_location') }}: {{ $business->business_name }} </h3>
                </div>
                <div class="col-md-12 col-sm-12">
                    <h5 class="mt-5 text-muted">{{ __('business.accesses') }}</h5>
                    @foreach($business->permissions as $permission)
                        <span class="badge bg-info badge-pill" style="font-size:15px;">{{ $permission->title }}</span>
                    @endforeach
                </div>
            </div>
            <div class="col-12 mt-5">
                <h3 class="text-primary"><i class="fas fa-paint-brush"></i> {{ __('business.users') }}</h3>
            </div>
            <div class="card-body pb-0">
                <div class="row d-flex align-items-stretch">
                @foreach($users as $user)
                    <div class="col-12 col-sm-6 col-md-4 d-flex align-items-stretch">
                        <div class="card bg-light">
                        <div class="card-header text-muted border-bottom-0">
                        @if(is_numeric($user->role))
                            @php
                                $role = App\Models\Role::find($user->role);
                            @endphp
                        @else
                            @php $role = ''; @endphp
                        @endif

                        @if($role)    
                            {{ $role->title }}
                        @else
                            {{ $user->role }}
                        @endif    
                        </div>
                        <div class="card-body pt-0">
                            <div class="row">
                            <div class="col-7">
                                <h2 class="lead"><b>{{ $user->name }}</b></h2>
                                <ul class="ml-4 mb-0 fa-ul text-muted">
                                    <li class="small">
                                        <span class="fa-li"><i class="far fa-envelope"></i></span>        
                                        {{ __('keywords.email') }}: {{ $user->email }}
                                    </li>
                                </ul>
                                <ul class="ml-4 mb-0 fa-ul text-muted">
                                    <li class="small">
                                        <span class="fa-li"><i class="fas fa-phone"></i></span> 
                                        {{ __('keywords.number') }}: {{ $user->number }}
                                    </li>
                                </ul>
                            </div>
                            <div class="col-5 text-center">
                                <img src="/images/{{ ($user->profile_photo_path ? $user->profile_photo_path : 'avatar5.png' ) }}" alt="" class="img-circle img-fluid">
                            </div>
                            </div>
                        </div>
                         <div class="card-footer">
                            <div class="text-right">
                                @can('Users Change Password')    
                                    <a href="{{ Route('changepassadmin',array(session('business_name'),md5($user->id))) }}" class="btn btn-sm btn-info">
                                    <i class="fas fa-lock"></i> {{ __('users.change_pass') }}
                                    </a>
                                @endcan  
                            </div>
                        </div>
                        </div>
                    </div>   
                    @endforeach    
                
                </div>
            </div>
            <!-- /.card-body -->
        </div>
        <!-- /.card -->
    </section>
        <!-- /.content -->
</div>
@stop