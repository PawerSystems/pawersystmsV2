@extends("layouts.backend")

@section('content')


<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>{{ __('profile.instructor') }}</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="/">{{ __('keywords.home') }}</a></li>
              <li class="breadcrumb-item active">{{ __('profile.instructor') }}</li>
            </ol>
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>

    <section class="row">
    <div class="col-md-3"></div>
        <div class="col-md-6">
            <div class="card card-widget widget-user">
                <!-- Add the bg color to the header using any of the bg-* classes -->
                <div class="widget-user-header bg-info">
                
                </div>
                <div class="widget-user-image">
                @php
                    if($user->profile_photo_path){
                        $img = $user->profile_photo_path;
                    }
                    else{
                        if($user->gender == 'women'){ $img = 'avatar2.png'; }
                        else{ $img =  'avatar5.png'; }
                    }
                @endphp
                <img class="img-circle elevation-2" src="/images/{{ $img }}" alt="User Avatar">
                </div>
                <div class="card-footer">
                <div class="row">
                    <div class="col-sm-6 border-right">
                      <div class="description-block">
                          <h5 class="description-header">
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
                          </h5>
                          <span class="description-text">{{ __('keywords.designation') }}</span>
                      </div>
                      <!-- /.description-block -->
                    </div>
                    <!-- /.col -->
                    <div class="col-sm-6">
                      <div class="description-block">
                          <h5 class="description-header">{{ $user->name }}</h5>
                          <span class="description-text">{{ __('keywords.name') }}</span>
                      </div>
                    </div>
                    <!-- /.col -->
                    <div class="col-sm-12 text-center mt-3">{{ $user->free_txt }}</div>
                    
                </div>
                <!-- /.row -->
                </div>
            </div>
        </div>
    <section>  
</div>  
@stop