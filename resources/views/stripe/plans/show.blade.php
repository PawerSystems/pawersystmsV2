@extends('layouts.backend')

@section('content')

<section class="content-wrapper">
    <div class="container">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>{{ __('subscription.subscriptions') }}</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="/">{{ __('keywords.home') }}</a></li>
                    <li class="breadcrumb-item active">{{ __('subscription.subscriptions') }}</li>
                    </ol>
                </div>
                </div>
            </div><!-- /.container-fluid -->
        </section>
        
        <div class="card-deck mb-3 text-center">
            @foreach ($plans as $plan)
                <div class="card mb-4 box-shadow">
                    <div class="card-header">
                        <h4 class="my-0 font-weight-normal">
                            {{ $plan->name }}
                            @if (in_array(auth()->user()->role,['owner','Owner']))
                                ( {{ $plan->status ? 'Active' : 'Deactive' }} )
                            @endif
                        </h4>
                    </div>
                    <div class="card-body">
                        <h1 class="card-title pricing-card-title text-center" style="width:100%">
                            {{ $plan->currency == 'dkk' ? 'Kr.' : '$' }} {{ $plan->price }} 
                            <small class="text-muted">/ 
                            {{ $plan->interval_count > 1 ? __('subscription.every').$plan->interval_count : '' }} {{ $plan->billing_method }}</small>
                        </h1>
                        <div>
                            {!! $plan->description !!}
                        </div>
                        <a href="{{ route('plans.checkout',[session('business_name'),$plan->plan_id]) }}" class="btn btn-lg btn-block btn-outline-primary">{{ __('subscription.get_plan') }}</a>
                        @if (in_array(auth()->user()->role, ['owner','Owner']))
                            <a href="{{ route('plans.edit',[session('business_name'),$plan->id]) }}" class="btn btn-lg btn-block btn-outline-danger">{{ __('subscription.edit') }}</a>
                        @endif
                    </div>
                </div>
            @endforeach
            {{-- <div class="card mb-4 box-shadow">
                <div class="card-header">
                <h4 class="my-0 font-weight-normal">Enterprise</h4>
                </div>
                <div class="card-body">
                <h1 class="card-title pricing-card-title">$29 <small class="text-muted">/ mo</small></h1>
                <ul class="list-unstyled mt-3 mb-4">
                    <li>30 users included</li>
                    <li>15 GB of storage</li>
                    <li>Phone and email support</li>
                    <li>Help center access</li>
                </ul>
                <button type="button" class="btn btn-lg btn-block btn-primary">Contact us</button>
                </div>
            </div> --}}
        </div>
    </div>
</section>

@stop

@section('scripts')

@stop