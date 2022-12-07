<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
@php
    $business = App\Models\Business::select()->where('business_name',session('business_name'))->first();
@endphp
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $business->brand_name != '' ? $business->brand_name : $business->business_name }}</title>

    <!-- Bootstrap core CSS -->
    <link rel="stylesheet" href="{{asset('dist/css/adminlte.min.css')}}">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate"/>
    <link rel="icon" href="/images/{{ $business->logo ?: 'ps_logo.jpg' }}" type="image/icon type">

    <meta name="theme-color" content="#ffffff"> 
    <!-- Custom styles for this template -->
    <link href="{{asset('web/style.css')}}" rel="stylesheet">
    <link href="{{asset('web/style.min.css')}}" rel="stylesheet">
    <!-- Custom fonts for this template -->
    <link rel="stylesheet" href="{{asset('plugins/fontawesome-free/css/all.min.css')}}">
    <link href="https://fonts.googleapis.com/css?family=Montserrat:400,700" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Kaushan+Script" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Droid+Serif:400,700,400italic,700italic" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Roboto+Slab:400,100,300,700" rel="stylesheet" type="text/css">
    <!-- jQuery -->
    <script src="{{asset('plugins/jquery/jquery.min.js')}}"></script>
    <!-- Bootstrap -->
    <script src="{{asset('plugins/bootstrap/js/bootstrap.bundle.min.js')}}"></script>
    <script src="{{asset('plugins/jquery-validation/jquery.validate.min.js') }}"></script>
    <!-- flag-icon-css -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/flag-icon-css/3.3.0/css/flag-icon.min.css">

    <style>
        ::placeholder {
        color: black !important;
        opacity: 0.8 !important;
        }
        :-ms-input-placeholder {
        color: black !important;
        }
        ::-ms-input-placeholder {
        color: black !important;
        }

        @media (min-width: 992px){
            .navbar-expand-lg .navbar-nav .dropdown-menu.web {
                position: initial !important;
            }
        }
        @media screen and (max-width: 639px){
            .antialiased{ margin-top:12vh; }
        }
        
    </style>
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
</head>
<header>
<nav class="navbar navbar-expand-lg navbar-dark fixed-top" id="mainNav">
        <div class="container-fluid">
            <a class="navbar-brand js-scroll-trigger" href="/">
                <img width="70" height="70" src="/images/{{ $business->logo ?: 'ps_logo.jpg' }}">
            </a>

            <button class="navbar-toggler navbar-toggler-right" type="button" data-toggle="collapse" data-target="#navbarResponsive" aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation">
                {{ __('keywords.menu') }}
                <i class="fas fa-bars"></i>
            </button>
            <div class="collapse navbar-collapse" id="navbarResponsive">
                <ul class="navbar-nav text-uppercase ml-auto">
                    <x-web-menu/>
                @if(!auth::check())
                    <li class="nav-item">
                        <a class="nav-link" href="/login">{{ __('web.login') }}</a>
                    </li> 
                @endif
                    @foreach ( Config::get('languages') as $key => $val )
                    <li class="nav-item">
                        <a href="{{ Route('lang',[session('business_name'), $key]) }}" class="nav-link">
                        <i class="flag-icon flag-icon-{{ $val['flag-icon'] }}"></i>
                        </a>
                    </li>    
                    @endforeach                        
                </ul>
            </div>
        </div>
    </nav>
</header>

<x-guest-layout>
    <x-jet-authentication-card>
        <x-slot name="logo">
        
        </x-slot>

        <x-jet-validation-errors class="mb-4" />

        @if (session('status'))
            <div class="mb-4 font-medium text-sm text-green-600">
                {{ session('status') }}
            </div>
        @endif

        <form method="POST" action="{{ route('logged_in',Session('business_name')) }}">
            @csrf

            <div>
                <x-jet-label for="email" value="{{ __('auth.email') }}" />
                <x-jet-input id="email" class="block mt-1 w-full" type="text" name="email" :value="old('email')" required autofocus />
            </div>

            <div class="mt-4">
                <x-jet-label for="password" value="{{ __('auth.password') }}" />
                <x-jet-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="current-password" />
            </div>

            <div class="block mt-4">
                <label for="remember_me" class="flex items-center">
                    <input id="remember_me" type="checkbox" class="form-checkbox" name="remember">
                    <span class="ml-2 text-sm text-gray-600">{{ __('auth.rememberme') }}</span>
                </label>
            </div>

            <div class="flex items-center justify-end mt-4">
                @if (Route::has('password.request'))
                    <a class="underline text-sm text-gray-600 hover:text-gray-900" href="{{ route('password.request') }}">
                        {{ __('auth.forgot-your-password') }}
                    </a>
                @endif

                <x-jet-button class="ml-4">
                    {{ __('auth.login') }}
                </x-jet-button>
            </div>
        </form>
    </x-jet-authentication-card>
</x-guest-layout>

<link href="{{asset('web/style.min.css')}}" rel="stylesheet">
  <style>
      .container-fluid > .row{ margin:0; }
  </style>
  <footer class="footer">
      <div class="container">
          <div class="row align-items-center">
              <div class="col-md-12">   
                  <span class="copyright">{{ __('web.question_please_contact') }} <a href="mailto:{{ $business->user->email }}">{{ $business->brand_name?: $business->business_name}}</a></span>
              </div> 
              <div class="col-md-12">
                  <ul class="list-inline quicklinks">
                      <li class="list-inline-item">
                      <a href="{{ Route('gdpr',session('business_name')) }}">GDPR</a>
                      </li>
                      <!-- <li class="list-inline-item">
                      <a href="{{ Route('resendemail',session('business_name')) }}"> {{ __('web.resendEmail') }}</a>
                      </li> -->
                  </ul>
                  <span class="copyright">{{ __('keywords.copyright') }} &copy; {{ config('app.domain') }}</span>
  
              </div>
          </div>
      </div>
  </footer>
