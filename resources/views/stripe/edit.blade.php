@extends('layouts.backend')

@section('content')

<section class="content-wrapper">
    <div class="container">

          <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>{{ __('subscription.edit_subscription') }}</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="/">{{ __('keywords.home') }}</a></li>
                    <li class="breadcrumb-item active">{{ __('subscription.edit_subscription') }}</li>
                    </ol>
                </div>
                </div>
            </div><!-- /.container-fluid -->
        </section>

        @php
            $plan = App\Models\Plan::where('plan_id',$subscription->stripe_price)->first();
        @endphp

        <form action="{{ route('subscription.update',session('business_name')) }}" method="POST" id="subscription-form">
            @csrf
            <input type="hidden" name="subId" value="{{ $subscription->id }}">
            
            <div class="form-group">
                <label for="location">{{ __('subscription.location') }}</label>
                <input type="text" class="form-control" value="{{$subscription->user->business_id}}" disabled>
              </div>
            
            <div class="form-group">
              <label for="subName">{{ __('subscription.plan_name') }}</label>
              <input type="text" class="form-control" value="{{$subscription->name}}" disabled>
            </div>

            <div class="form-group">
                <label for="planAmount">{{ __('subscription.amount') }}</label>
                <input type="number" class="form-control" value="{{ $plan->price }}" disabled>
            </div>
           
            <div class="form-group">
              <label for="planPeriod">{{ __('subscription.billing_period') }}</label>
              <select class="form-control" id="planPeriod" name="period" disabled>
                <option value="week" {{ $plan->billing_method == 'week' ? 'selected' : '' }}>{{ __('subscription.weekly') }}</option>
                <option value="month" {{ $plan->billing_method == 'month' ? 'selected' : '' }}>{{ __('subscription.monthly') }}</option>
                <option value="year" {{ $plan->billing_method == 'year' ? 'selected' : '' }}>{{ __('subscription.yearly') }}</option>
              </select>
                @if ($errors->has('period'))
                    <span class="text-danger">{{ $errors->first('period') }}</span>
                @endif
            </div>

            <div class="form-group">
                <label>{{ __('subscription.trial_ends_at') }}</label>
                <input type="text" value="{{ $subscription->trial_ends_at ?: '---' }}" class="form-control" disabled>
            </div>

            <div class="form-group">
                <label for="trial_days">{{ __('subscription.add_trial_days') }}</label>
                <input type="number" name="trial_days" class="form-control" id="trial_days" >
                @if ($errors->has('trial_days'))
                  <span class="text-danger">{{ $errors->first('trial_days') }}</span>
                @endif
            </div>
            
            <button type="submit" class="btn btn-success btn-block">{{ __('keywords.update') }}</button>
        </form><br><br>
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
@stop