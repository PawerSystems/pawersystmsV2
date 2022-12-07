<footer class="footer">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-12">   
                <span class="copyright">{{ __('web.question_please_contact') }} <a href="mailto:{{ $email }}">{{ $business}}</a></span>
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
@include('cookieConsent::index')
  
