@extends('layouts.backend')

@section('content')
<style>
    .StripeElement {
        background-color: white;
        padding: 8px 12px;
        border-radius: 4px;
        border: 1px solid transparent;
        box-shadow: 0 1px 3px 0 #e6ebf1;
        -webkit-transition: box-shadow 150ms ease;
        transition: box-shadow 150ms ease;
    }
    .StripeElement--focus {
        box-shadow: 0 1px 3px 0 #cfd7df;
    }
    .StripeElement--invalid {
        border-color: #fa755a;
    }
    .StripeElement--webkit-autofill {
        background-color: #fefde5 !important;
    }
</style>

<section class="content-wrapper">
    <div class="container-fluid">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>{{__('subscription.requests')}}</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="/">{{ __('keywords.home') }}</a></li>
                    <li class="breadcrumb-item active">{{ __('subscription.requests') }}</li>
                    </ol>
                </div>
                </div>
            </div><!-- /.container-fluid -->
        </section>
        <table class="table">
            <thead class="thead-dark">
                <tr>
                    <th scope="col">{{ __('subscription.request').' '.__('subscription.id') }}</th>
                    <th scope="col">{{ __('subscription.cname') }}</th>
                    <th scope="col">{{ __('subscription.cemail') }}</th>
                    <th scope="col">{{ __('subscription.cnumber') }}</th>
                    <th scope="col">{{ __('subscription.business_name') }}</th>
                    <th scope="col">{{ __('subscription.plan') }}</th>
                    <th scope="col">{{ __('subscription.status') }}</th>
                    <th scope="col">{{ __('subscription.request_time') }}</th>
                    <th scope="col">{{ __('profile.action') }}</th>
                </tr>
            </thead>
            <tbody>
            @forelse ($requests as $request)
            @php
                $plan = App\Models\Plan::find($request->plan);
            @endphp
                <tr>
                    <td>{{ $request->id }}</td>
                    <td>{{ $request->name }}</td>
                    <td>{{ $request->email }}</td>
                    <td>{{ $request->number }}</td>
                    <td>{{ $request->business_name }}</td>
                    <td>{{ $plan->name }}</td>
                    <td>
                        <span class="badge bg-{{ $request->status ? 'success' : 'danger' }}">
                            {{ $request->status ? 'Open' : 'Closed' }}
                        </span>
                    </td>
                    <td>{{ $request->created_at }}</td>
                    <td>
                        <input type="checkbox" class="slider" data-toggle="toggle" data-size="sm" value="{{ $request->id }}" {{ $request->status ? '' : 'checked' }}>
                    </td>
                </tr>
            @empty
            <tr>
                <td colspan="8"><h5 class="text-center">{{__('subscription.no_request_to_show')}}</h5></td>
            </tr>
            @endforelse
            </tbody>
          </table>
    </div>
</section>

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

    jQuery('.slider').click(function(){

        const Toast = Swal.mixin({
		  toast: true,
		  position: 'top-end',
		  showConfirmButton: false,
		  timer: 3000
		});

        var id = jQuery(this).val();
        if(jQuery(this).is(':checked')){
            var status = 0;
        }else{
            var status = 1;
        }
        jQuery.ajax({
            url:'/request/status',
            data:{ status:status, id:id},
            type: 'GET',
            success:function(response)
            {
                if(response['status'] == 'success'){
                    Toast.fire({
                        icon: 'success',
                        title: response['data']
                    });

                    if(status){
                        jQuery('.badge').removeClass('bg-danger').addClass('bg-success').text('Open');
                    }else{
                        jQuery('.badge').removeClass('bg-success').addClass('bg-danger').text('Closed');
                    }
                }
            },
            error:function(response)
            {
                if(response.responseJSON){
                    alert(response.responseJSON.error);
                }
            }
        });    
       
    });

</script> 
@stop