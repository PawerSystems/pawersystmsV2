@extends('layouts.web')

@section('content')
<style>
    @media(max-width:768px){
        #contact{ margin-top: 18vh; }
    }
</style>
<section class="container">
    <div class="col-md-12">
        <div id="contact" class="col-md-12 card">

            <!-- Showing error or success messages -->
            @if(Session::get('success'))
                <div class="alert alert-success alert-dismissible mt-3">
                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                    {{ __('web.form_save_successfully') }}
                </div>
            @elseif( Session::get('error') )
                <div class="alert alert-danger alert-dismissible mt-3">
                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                    {{ __('web.error_to_send_form') }}
                </div>
            @endif

            <form name="contactForm" id="contactForm" type="POST" action="{{ Route('contactSave',session('business_name')) }}">
                @csrf

                <h3 class="text-center mt-4">{{ __('keywords.contact_us') }}</h3>

                <div class="form-group">
                    <label for="subject">{{ __('keywords.subject') }}</label>
                    <input type="text" class="form-control required" value="{{ $msg ?: old('subject') }}" name="subject" id="subject" placeholder="{{ __('keywords.subject') }}" required>
                    @if ($errors->has('subject'))
                        <span class="text-danger">{{ $errors->first('subject') }}</span>
                    @endif
                </div>

                <div class="form-group">
                    <label for="name">{{ __('keywords.name') }}</label>
                    <input type="text" class="form-control required" value="{{ Auth::user() ? Auth::user()->name : old('name') }}" name="name" id="name" placeholder="{{ __('keywords.name') }}" required>
                    @if ($errors->has('name'))
                        <span class="text-danger">{{ $errors->first('name') }}</span>
                    @endif
                </div>

                <div class="form-group">
                    <label for="email">{{ __('keywords.email') }}</label>
                    <input type="email" class="form-control required" value="{{ Auth::user() ? Auth::user()->email : old('email') }}" name="email" id="email" placeholder="{{ __('keywords.email') }}" required>
                    @if ($errors->has('email'))
                        <span class="text-danger">{{ $errors->first('email') }}</span>
                    @endif
                </div>

                <div class="form-group">
                    <label for="email">{{ __('keywords.number') }}</label>
                    <input type="number" class="form-control required" value="{{ Auth::user() ? Auth::user()->number : old('number') }}" name="number" id="number" placeholder="{{ __('keywords.number') }}">
                    @if ($errors->has('number'))
                        <span class="text-danger">{{ $errors->first('number') }}</span>
                    @endif
                </div>

                <div class="form-group">
                    <label for="description">{{ __('keywords.description') }}</label>
                    <textarea class="form-control" name="description" id="description" cols="30" rows="10">{{ old('description') }}</textarea>
                    @if ($errors->has('description'))
                    <span class="text-danger">{{ $errors->first('description') }}</span>
                @endif
                </div>

                <button class="btn btn-info btn-block mb-4">{{ __('keywords.send') }}</button>

            </form>    
        </div>
    </div>
</section>

@stop

