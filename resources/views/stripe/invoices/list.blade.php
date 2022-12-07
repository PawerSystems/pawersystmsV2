@extends('layouts.backend')

@section('content')

<section class="content-wrapper">
    <div class="container">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container">
                <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>{{__('subscription.all_invoices')}}</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="/">{{ __('keywords.home') }}</a></li>
                    <li class="breadcrumb-item active">{{ __('subscription.all_invoices') }}</li>
                    </ol>
                </div>
                </div>
            </div><!-- /.container-fluid -->
        </section>
        <table class="table">
            <thead class="thead-dark">
                <tr>
                    <th scope="col">#</th>
                    <th scope="col" class="text-center">{{ __('subscription.date') }}</th>
                    <th scope="col" class="text-center">{{ __('subscription.invoices') }}</th>
                </tr>
            </thead>
            <tbody>
            @forelse ($invoices as $key => $invoice)
                <tr>
                    <td >{{++$key}}</td>
                    <td class="text-center">{{$invoice->date()->toFormattedDateString()}}</td>
                    <td class="text-center">
                        <a class="btn btn-success" href="/invoice/{{ $invoice->id }}">
                            {{ __('subscription.download') }}
                        </a>
                    </td>
                </tr>
            @empty
            <tr>
                <td colspan="3"><h5 class="text-center">{{__('subscription.no_subscriptions_to_show')}}</h5></td>
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
@stop