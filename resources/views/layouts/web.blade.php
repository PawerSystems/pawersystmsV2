<!-- Header section -->
<x-web-header/>

<!-- page data -->
@yield('content')

<!-- Footer -->
<x-web-footer />

<!-- page scripts -->
@yield('scripts')

<!-- Plugin JavaScript -->
<script src="{{asset('web/jquery.easing.min.js')}}"></script> 
<!-- Custom scripts for this template -->
<script src="{{asset('web/style.min.js') }}"></script>

</body>
</html>
