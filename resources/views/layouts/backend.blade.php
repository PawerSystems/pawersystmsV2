@if(session('navFixed') == 'off')
@php $sidebar = 'sidebar-collapse';  @endphp
@else
@php $sidebar = '';  @endphp
@endif

@if(auth()->user()->role != 'Customer')
@php $class = $sidebar.' hold-transition sidebar-mini  layout-fixed';  @endphp
@else
@php $class = 'hold-transition layout-top-nav';  @endphp
@endif
{{-- @php $class = $sidebar.' hold-transition sidebar-mini  layout-fixed';  @endphp --}}

<x-header class=" {{ $class }}" wrapper="wrapper"/>
<!-- Navbar -->
<x-navbar type=""/>
<!-- /.navbar -->

<!-- Main Sidebar Container -->
@if(auth()->user()->role != 'Customer')
<x-leftNavBar/>
@endif
<!-- Content Wrapper. Contains page content -->
@yield('content')

<!-- /.content-wrapper -->

<!-- Main Footer -->
<x-footer />
@yield('scripts')
