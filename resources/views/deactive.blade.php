<x-header class="hold-transition lockscreen" wrapper="lockscreen-wrapper"/>
  <!-- Navbar -->

  <div class="lockscreen-logo">
    Hi <b>{{ Auth::user()->name }}</b>,
  </div>

@if(Session::get('warning'))
    <div class="alert alert-danger">
        {{ Session::get('warning') }}
    </div>
@endif

<form method="POST" action="{{ route('logout') }}">
    @csrf
    <a class="btn btn-block btn-primary btn-lg" href="{{ route('logout') }}"
        onclick="event.preventDefault();
        this.closest('form').submit();">
        {{ __('Logout') }}
    </a>
</form>


<!-- Create Post Form -->