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
            <h1>{{ __('customer.customers_list') }}</h1>
            <h6>{{ __('customer.total_customers') }} - <b>{{$totalCustomers}}</b></h6>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="/">{{ __('keywords.home') }}</a></li>
              <li class="breadcrumb-item active">{{ __('customer.customers_list') }}</li>
            </ol>
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
                @forelse( $customers as $customer )
                    <div class="col-12 col-sm-6 col-md-4 d-flex align-items-stretch">
                        <div class="card bg-light">
                            <div class="card-header text-muted border-bottom-0">
                                {{ $customer->role }}
                            </div>
                            <div class="card-body pt-0">
                                <div class="row">
                                    <div class="col-7">
                                        <h2 class="lead"><b>{{ $customer->name }}</b></h2>
                                        <p class="text-muted text-sm"><b>{{ __('customer.last_update') }}: </b><br> {{ \Carbon\Carbon::parse($customer->updated_at)->format($dateFormat.($timeFormat == 12 ? ' h:i:s a' : ' H:i:s' )) }} </p>
                                        <ul class="ml-4 mb-0 fa-ul text-muted">
                                            <li class="small"><span class="fa-li"><i class="fas fa-lg fa-envelope"></i></span> {{ __('keywords.email') }}: <br>{{ $customer->email }}</li>
                                            <li class="small"><span class="fa-li"><i class="fas fa-lg fa-phone"></i></span> {{ __('keywords.number') }}: <br>{{ $customer->number ? $customer->number  : 'N/A'}}</li>
                                            @if($cpr)
                                            <li class="small"><span class="fa-li"><i class="fas fa-lg fa-key"></i></span> {{ __('customer.cpnr') }}: <br>{{ $customer->cprnr ? $customer->cprnr  : '-'}}</li>
                                            <!-- <li class="small"><span class="fa-li"><i class="fas fa-lg fa-key"></i></span> {{ __('customer.mednr') }}: <br>{{ $customer->mednr ? $customer->mednr  : '-'}}</li> -->
                                            @endif
                                        </ul>
                                    </div>
                                    <div class="col-5 text-center">
                                        <img src="/images/{{ $customer->profile_photo_path ? $customer->profile_photo_path : 'avatar5.png' }}" alt="" class="img-circle img-fluid" width="250px">
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer">
                                <div class="text-right">
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
                                @endcan    
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
                {{ $customers->links() }} 
                </ul>
            </nav>
            </div> 
        </div>
    </section>   
</div>               
@stop

@section('scripts')
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

</script>
@stop