@extends('layouts.backend')

@section('content')

<section class="content-wrapper">
    <div class="container">

          <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>{{ __('subscription.create_plan') }}</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="/">{{ __('keywords.home') }}</a></li>
                    <li class="breadcrumb-item active">{{ __('subscription.create_plan') }}</li>
                    </ol>
                </div>
                </div>
            </div><!-- /.container-fluid -->
        </section>

        <form action="{{ route('plans.save',session('business_name')) }}" method="POST" id="plan-form">
            @csrf
            <div class="form-group">
              <label for="planName">{{ __('subscription.plan_name') }}</label>
              <input type="text" name="name" class="form-control" id="planName" placeholder="{{ __('subscription.plan_name') }}">
                @if ($errors->has('name'))
                  <span class="text-danger">{{ $errors->first('name') }}</span>
                @endif
            </div>

            <div class="form-group">
                <label for="planAmount">{{ __('subscription.amount') }}</label>
                <input type="number" name="amount" class="form-control" id="planAmount" placeholder="{{ __('subscription.amount') }}">
                @if ($errors->has('amount'))
                    <span class="text-danger">{{ $errors->first('amount') }}</span>
                @endif
            </div>

            {{-- <div class="form-group">
                <label for="planCurency">{{ __('subscription.currency') }}</label>
                <input type="text" name="currency" class="form-control" id="planCurency" placeholder="{{ __('subscription.currency') }}">
                @if ($errors->has('currency'))
                    <span class="text-danger">{{ $errors->first('currency') }}</span>
                @endif
            </div> --}}

            <div class="form-group">
                <label for="planIntervalCount">{{ __('subscription.interval_count') }}</label>
                <input type="number" name="interval_count" class="form-control" id="planIntervalCount" placeholder="{{ __('subscription.interval_count') }}">
                @if ($errors->has('interval_count'))
                    <span class="text-danger">{{ $errors->first('interval_count') }}</span>
                @endif
            </div>
           
            <div class="form-group">
              <label for="planPeriod">{{ __('subscription.billing_period') }}</label>
              <select class="form-control" id="planPeriod" name="period">
                <option value="week">{{ __('subscription.weekly') }}</option>
                <option value="month">{{ __('subscription.monthly') }}</option>
                <option value="year">{{ __('subscription.yearly') }}</option>
              </select>
                @if ($errors->has('period'))
                    <span class="text-danger">{{ $errors->first('period') }}</span>
                @endif
            </div>

            <div class="form-group">
                <label for="planIntervalCount">{{ __('subscription.trial_days') }}</label>
                <input type="number" name="trialDays" min="0" value="90" class="form-control" id="planIntervalCount" placeholder="{{ __('subscription.trial_days') }}">
                @if ($errors->has('trialDays'))
                    <span class="text-danger">{{ $errors->first('trialDays') }}</span>
                @endif
            </div>

            <div class="form-group">
                <label for="description">{{ __('subscription.description') }}</label>
                <textarea class="textarea" name="description" placeholder="{{ __('web.place_content_here') }}"></textarea>                
                @if ($errors->has('description'))
                    <span class="text-danger">{{ $errors->first('description') }}</span>
                @endif
            </div>
            

            <button type="submit" class="btn btn-success btn-block">{{ __('keywords.save') }}</button>
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

<!-- Summernote -->
<script src="{{asset('plugins/summernote/summernote-bs4.min.js')}}"></script>
<script>
  $(function () {
    // Summernote
    $('.textarea').summernote({
    height: 300,
    toolbar: [
        [ 'style', [ 'style' ] ],
        [ 'font', [ 'bold', 'italic', 'underline', 'strikethrough', 'superscript', 'subscript', 'clear'] ],
        [ 'fontname', [ 'fontname' ] ],
        [ 'fontsize', [ 'fontsize' ] ],
        [ 'color', [ 'color' ] ],
        [ 'para', [ 'ol', 'ul', 'paragraph', 'height' ] ],
        [ 'table', [ 'table' ] ],
        [ 'insert', [ 'link'] ],
        [ 'view', [ 'undo', 'redo', 'fullscreen', 'codeview', 'help' ] ]
    ]
})
  })
</script>
@stop