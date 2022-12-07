@if (session()->has('newurl'))
  <script src="{{asset('plugins/jquery/jquery.min.js')}}"></script>
  <script>
    jQuery(function(){
      window.open('{{ session('newurl') }}', "_blank");
    });
  </script>
@endif

@php $clipT = $clipE = ''; @endphp
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
  @endif     
@endforeach

@if(auth()->user()->role == 'Customer')
  @php $class = 'navbar navbar-expand-lg navbar-dark fixed-top'; @endphp
@else
  @php $class = 'main-header navbar navbar-expand-md navbar-white navbar-light'; @endphp
@endif
<style>
@media screen and (max-width:512px){
  .navbar-expand .navbar-nav .nav-link {
      padding-right: 10px;
      padding-left: 10px;
  }
  .sm-mb-3{
    margin:0 0 10px 0;
  }
}
.menu-bar{ flex-wrap: wrap; }

</style>
@if(auth()->user()->role == 'Customer')
  <style>
    nav .navbar-toggler {
      font-size: 12px;
      right: 0;
      padding: 13px;
      text-transform: uppercase;
      color: #fff;
      border: 0;
      background-color: #99b45f !important;
      font-family: 'Montserrat','-apple-system','BlinkMacSystemFont','Segoe UI','Roboto','Helvetica Neue',Arial,sans-serif,'Apple Color Emoji','Segoe UI Emoji','Segoe UI Symbol','Noto Color Emoji';
    }
    .navbar-dark .navbar-nav .nav-link
    { 
      text-transform: uppercase; 
      color:White; 
      letter-spacing: 1px !important; 
      font-weight: 350 !important; 
      font-family: 'Montserrat','-apple-system','BlinkMacSystemFont','Segoe UI','Roboto','Helvetica Neue',Arial,sans-serif,'Apple Color Emoji','Segoe UI Emoji','Segoe UI Symbol','Noto Color Emoji';

    }
    .content-header{ margin-bottom: 50px; }
    .navbar-dark .navbar-nav .nav-link:hover{ color:black }
    #mainNav{
      background-color: #cdd6d1 !important;
    }
    .nav-item .nav-link { color: #424242 !important; }
    
  </style>
@endif
<nav class="{{ $class  }}" id="mainNav">
    <!-- Left navbar links -->
    @if(auth()->user()->role != 'Customer')
      <a onclick="checkpushmenu(this)" class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a> 

      @if( auth()->user()->role == 'Owner')
        <form method="POST" action="/change-user" class="form-inline  sm-mb-3" name="userSwitchForm" id="userSwitchForm">
          @csrf
          <label for="domain">{{ __('keywords.select_location') }}</label>&nbsp;
          <input type="hidden" name="superadmin" id="superadmin" value="">
          <div class="input-group">
            <select name="domain" id="domain" class="form-control">
              @foreach($businesses as $bus)
                @if($bus->id != auth()->user()->business_id)
                  <option value="{{ $bus->business_name }}" data-admin="{{ md5($bus->superAdmin ? $bus->superAdmin->id : $bus->superAdminRole->activeUserFromRole->id ) }}">{{ $bus->brand_name ?: $bus->business_name }} ({{ $bus->business_name.'.'.config('app.domain') }})</option>
                @endif  
              @endforeach
            </select>
            <div class="input-group-append">
              <button type="submit" class="btn btn-outline-secondary" type="button">{{ __('web.switch') }}</button>
            </div>
          </div>
        </form>
      @endif
      
    @else
    <div class="container-fluid">
    <a class="navbar-brand js-scroll-trigger" href="/">
      <img width="70" height="70" src="/images/{{ $business->logo ?: 'ps_logo.jpg' }}">
    </a>
    @endif


    <button class="navbar-toggler navbar-toggler-right" type="button" data-toggle="collapse" data-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
      @if(auth()->user()->role == 'Customer'){{ __('web.menu') }} @endif
      <i class="fas fa-bars"></i>
    </button>
      
    <div class="collapse navbar-collapse order-3" id="navbarCollapse">
      <ul class="order-1 order-md-3 navbar-nav  ml-auto menu-bar">
        @if(auth()->user()->role == 'Customer')
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
      @if(auth()->user()->role != 'Customer')
        <!-- Language Dropdown Menu -->
        <li class="nav-item dropdown">
        <a class="nav-link" data-toggle="dropdown" href="javascript:;">
          @foreach ( Config::get('languages') as $key => $val )
            @if(Lang::locale() == $key)
              <i class="flag-icon flag-icon-{{ $val['flag-icon'] }}"></i>
            @endif
          @endforeach
        </a>
          
        <div class="dropdown-menu dropdown-menu-right p-0">
        @foreach ( Config::get('languages') as $key => $val )
          <a href="{{ Route('lang',[session('business_name'), $key]) }}" class="dropdown-item @if(Lang::locale() == $key) active @endif">
            <i class="flag-icon flag-icon-{{ $val['flag-icon'] }} mr-2"></i> {{ $val['display'] }}
          </a>
        @endforeach
        </div>
        
        </li>
      @else
          @foreach ( Config::get('languages') as $key => $val )
            <li class="nav-item">
              <a href="{{ Route('lang',[session('business_name'), $key]) }}" class="nav-link">
              <i class="flag-icon flag-icon-{{ $val['flag-icon'] }}"></i>
              </a>
            </li>            
          @endforeach
      @endif
      </ul>
    </div> 
    @if(auth()->user()->role == 'Customer')
    </div>
    @endif
  </nav>
  <div class="alert alert-danger main-header">
    <strong>Danger!</strong> You should <a href="#" class="alert-link">read this message</a>.
  </div>
