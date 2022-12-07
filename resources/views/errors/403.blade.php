@extends('layouts.web')

@section('content')
{{-- <x-header class="hold-transition lockscreen" wrapper="lockscreen-wrapper"/> --}}
  <!-- Navbar -->

  <style>
    body { 
        min-height: 100vh;
        display: flex;
        flex-direction: column;
    }
    .container-main {  margin-top: 25vh !important; margin: 0 auto; max-width: 500px; }
    footer{ margin-top:auto; }
    </style>

    <div class="container-main">
        <div class="lockscreen-logo">
            {{ __('error.hi') }} <b>{{ Auth::user()->name }}</b>,
                {{ __('error.not_allow_this_page') }}
        </div>

        @if(Session::get('warning'))
            <div class="alert alert-danger">
                {{ Session::get('warning') }}
            </div>
        @endif

        <a href="/" class="btn btn-info btn-block">{{ __('error.take_me_home') }} </a>
        <a href="/login" class="btn btn-primary btn-block">
            {{ __('error.take_me_login_page') }} 
        </a>
    </div>
@stop