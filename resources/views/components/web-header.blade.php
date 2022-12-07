@php $class = 'fixed-top'; @endphp

@php $clipT = $clipE = $googleCode = ''; @endphp
@foreach($settings as $setting)
    @if($setting->key == 'clipboard_treatment')
        @if($setting->value == 'true')
            @php $clipT = 1; @endphp
        @else
            @php $clipT = 0 @endphp
        @endif
    
    @elseif($setting->key == 'clipboard_event')
        @if($setting->value == 'true')
            @php $clipE = 1; @endphp
        @else
            @php $clipE = 0 @endphp
        @endif    

    @elseif($setting->key == 'google_analytics_code')
        @php $googleCode  = $setting->value; @endphp  


  @endif     
@endforeach


<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $business->brand_name ?: $business->business_name }}</title>

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
    <link rel="stylesheet" href="{{asset('plugins/flag-icon-css/css/flag-icon.min.css') }}">
    <link rel="stylesheet" href="{{asset('plugins/jquery-ui/jquery-ui.css') }}">
    <!-- InputMask -->
    <script src="{{asset('plugins/jquery-ui/jquery-ui.js') }}"></script>

    
    <style>
        section#bod1 {
            margin: 30px 0 0 0 !important;
        }
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

        .js-cookie-consent.cookie-consent{
            text-align: center;
            padding: 15px 0;
            color: white;
            background-color: black;
            font-weight: bold;
            position: fixed;
            right: 0;
            bottom: 0;
            left: 0;
            z-index: 1030;
        }
        .cookie-consent__message{
             margin: 0 20px 0 0;
        }
        .js-cookie-consent-agree.cookie-consent__agree{
            background: white;
            color: black;
            border: none;
            padding: 15px;
            font-weight: bolder;
            border-radius: 5px;
        }
        .web-logo{ width: 120px; height:70px; }
    </style>

{!! $googleCode  !!}
</head>
<header>

@foreach($menu as $key => $value)
    @if(!empty(session('locale')))
        @if( $value->language != session('locale') )
            @continue
        @endif
    @else
        @if( $value->language != config('app.locale') )
            @continue
        @endif 
    @endif
    @if( $value->page  == 'NOTIFICATION' )
    
        @php $class = ''; @endphp
            
        <!-- Navigation -->
        <style>
            .alert {
                margin-bottom: 0px;
                background-color: #ff3300;
                border-color: #ff3300;
                color: white;
            }
            .carousel {
                margin-top: 0px;
            }

        </style>
        <div class="alert alert-dismissible">
            <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
            {!! $value->content  !!}
        </div>
            <!-- add this class back to nav when remove notification ( fixed-top ) -->
    @endif
@endforeach  

    <nav class="navbar navbar-expand-lg navbar-dark {{ $class }}" id="mainNav">
        <div class="container-fluid">
            <a class="navbar-brand js-scroll-trigger" href="/">
                <img class="web-logo" src="/images/{{ $business->logo ?: 'ps_logo.jpg' }}">
            </a>

            <button class="navbar-toggler navbar-toggler-right" type="button" data-toggle="collapse" data-target="#navbarResponsive" aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation">
                Menu
                <i class="fas fa-bars"></i>
            </button>
            <div class="collapse navbar-collapse" id="navbarResponsive">
                <ul class="navbar-nav text-uppercase ml-auto">
                    @if(Auth::user())                    
                    <li class="nav-item dropdown">
                        <a id="dropdownSubMenu1" href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" class="nav-link dropdown-toggle">{{ __('leftnav.my_bookings') }}</a>
                        <ul aria-labelledby="dropdownSubMenu1" class="dropdown-menu border-0 shadow">
                        @if($treatmentBooking > 0)
                        <li>
                            <a class="dropdown-item" href="{{ Route('MyTreatmentBookings',Session('business_name')) }}" class="nav-link">
                            {{ __('leftnav.treatment') }}
                            </a>
                        </li>
                        @endif
                        @if($eventBooking > 0)  
                        <li>
                            <a class="dropdown-item" href="{{ Route('myEventBookings',Session('business_name')) }}" class="nav-link">
                            {{ __('leftnav.events') }}
                            </a>
                        </li>
                        @endif
                        </ul>
                    </li> 
                    @if($clipE || $clipT )
                    <li class="nav-item">
                        <a href="{{ Route('myCards',Session('business_name')) }}" class="nav-link">
                        {{ __('leftnav.my_cards') }}
                        </a>
                    </li>
                    @endif
                    @endif
                    <x-web-menu/>
                    <li class="nav-item">
                    @if(!Auth::user())
                        <a class="nav-link" href="{{ Route('login') }}">{{ __('web.login') }}</a>
                    @else
                    <li class="nav-item">
                        <a href="{{ route('profile', session('business_name')) }}" class="nav-link">
                            {{ __('leftnav.profile') }}
                        </a>
                    </li>
                    <li class="nav-item">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <a class="nav-link" href="{{ route('logout') }}"
                            onclick="event.preventDefault();
                            this.closest('form').submit();">
                                {{ __('leftnav.logout') }}
                            </a>
                        </form>
                    </li>  
                        
                    @endif
                    </li>  

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