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
                    <h1>{{__('subscription.all_sub')}}</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="/">{{ __('keywords.home') }}</a></li>
                    <li class="breadcrumb-item active">{{ __('subscription.all_sub') }}</li>
                    </ol>
                </div>
                </div>
            </div><!-- /.container-fluid -->
        </section>
        <table class="table">
            <thead class="thead-dark">
                <tr>
                    <th scope="col">#</th>
                    @if (in_array(auth()->user()->role,['owner','Owner']) )
                        <th scope="col">{{ __('subscription.customer_name') }}</th>
                        <th scope="col">{{ __('subscription.customer_email') }}</th>
                        <th scope="col">{{ __('subscription.location') }}</th>
                    @endif
                    <th scope="col">{{ __('subscription.plan_name') }}</th>
                    {{-- <th scope="col">Sub Name</th> --}}
                    <th scope="col">{{ __('subscription.price') }}</th>
                    <th scope="col">{{ __('subscription.status') }}</th>
                    <th scope="col">{{ __('subscription.trial_start_at') }}</th>
                    <th scope="col">{{ __('subscription.trial_ends_at') }}</th>
                    <th scope="col">{{ __('subscription.sub_ends_at') }}</th>
                    <th scope="col">{{ __('subscription.auto_renew') }}</th>
                    <th scope="col">{{ __('subscription.invoices') }}</th>
                    @if (in_array(auth()->user()->role,['owner','Owner']) )
                        <th scope="col">{{ __('profile.action') }}</th>
                    @endif
                </tr>
            </thead>
            <tbody>
            @forelse ($subscriptions as $key => $subscription)
            @php
                $plan = App\Models\Plan::where('plan_id',$subscription->stripe_price)->first();
            @endphp
                <tr>
                    <td>{{++$key}}</td>
                    @if (in_array(auth()->user()->role,['owner','Owner']) )
                        <td>{{ $subscription->user->name }}</td>
                        <td>{{ $subscription->user->email }}</td>
                        <td>{{ $subscription->user->business_id }}</td>
                    @endif
                    <td>{{ $plan->name }}</td>
                    {{-- <td>{{ $subscription->name }}</td> --}}
                    <td>{{ $plan->price }}</td>
                    <td>{{ $subscription->stripe_status }}</td>
                    {{-- <td>{{ $subscription->quantity }}</td> --}}
                    <td>{{ $subscription->trial_ends_at ? $subscription->created_at : 'N/A' }}</td>
                    <td>{{ $subscription->trial_ends_at ?: 'N/A' }}</td>
                    <td>{{ \Carbon\Carbon::parse($subscription->asStripeSubscription()->current_period_end)->format('d M Y') }}</td>
                    <td>
                    @if (!in_array(auth()->user()->role,['owner','Owner']) && $subscription->user->id == auth()->user()->id  )
                        <input type="checkbox" class="slider" data-toggle="toggle" data-size="sm" value="{{ $subscription->name }}" {{ $subscription->ends_at != null ? '' : 'checked' }}>
                    @else
                        <span class="badge bg-{{ $subscription->ends_at != null ? 'danger' : 'success' }}">
                            {{ $subscription->ends_at != null ? 'Off' :  'On' }}
                        </span>
                    @endif
                    
                    </td>
                    <td>
                        <a href="/invoices/list/{{md5($subscription->id)}}" class="btn btn-info">
                            {{ __('subscription.view_invoices') }}
                        </a>
                    </td>
                @if (in_array(auth()->user()->role,['owner','Owner']) )
                    <td>
                        <a href="/subscription/edit/{{md5($subscription->id)}}" class="btn btn-success">
                            {{ __('keywords.edit') }}
                        </a>
                    </td>
                @endif                    
                </tr>
            @empty
            <tr>
                <td colspan="11"><h5 class="text-center">{{__('subscription.no_subscriptions_to_show')}}</h5></td>
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

        var subName = jQuery(this).val();
        if(jQuery(this).is(':checked')){
            jQuery.ajax({
                url:'/subscription/resume',
                data:{ subName },
                type: 'GET',
                success:function(response)
                {
                    if(response['status'] == 'success'){
                        Toast.fire({
                            icon: 'success',
                            title: response['data']
                        });
                    }
                },
                error:function(response)
                {
                    if(response.responseJSON){
                        alert(response.responseJSON.error);
                    }
                }
            });
        }      
        else
        {
            jQuery.ajax({
                url:'/subscription/cancel',
                data:{ subName },
                type: 'GET',
                success:function(response)
                {
                    if(response['status'] == 'success'){
                        Toast.fire({
                            icon: 'success',
                            title: response['data']
                        });
                    }
                },
                error:function(response)
                {
                    if(response.responseJSON){
                        alert(response.responseJSON.error);
                    }
                }
            });
        }
    });

</script> 
@stop