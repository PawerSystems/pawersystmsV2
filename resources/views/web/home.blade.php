@extends('layouts.web')

@section('content')

@if($offerSection->value == 'true' && $offerData != '')
    <style>
        #offerDiv{ padding:10px 20px; }
        /* @media only screen and (min-width:768px){
            #carouselExampleIndicators,#offerDiv { width:50%; float:left; margin-top:100px; }
        } */
    </style>
@endif
<!-- Header -->
<div id="carouselExampleIndicators" class="carousel slide" data-ride="carousel">
    <ol class="carousel-indicators">
        <li data-target="#carouselExampleIndicators" data-slide-to="0" class="active"></li>
    </ol>
    <div class="carousel-inner">
        <div class="carousel-item active" style="background-image:url('/images/{{ $business->banner ?: 'bg-img1.jpg' }}')">
            {{-- <img class="d-block w-100" src="/images/{{ $business->banner ?: 'bg-img1.jpg' }}" alt="first-slide" /> --}}
            <div class="carousel-caption d-none d-md-block">
                <h5 class="hm-intro-h5"></h5>
                <p class="hm-intro-p">{{ __('web.welcome') }}</p>
        
            @if ($bookingPage->is_active)
                <a class="btn btn-primary btn-xl text-uppercase js-scroll-trigger home-banner-btn" href="/booking">
                    {{ __('web.book_now') }}
                </a>
            @endif

            @if ($eventPage->is_active)
                @if (in_array($business->business_name,['iffinternal','b73banko']))
                    <a class="btn btn-primary btn-xl text-uppercase js-scroll-trigger home-banner-btn {{$business->business_name == 'b73banko' ? 'events-button' : ''}}" href="/events">{{ __('keywords.book_breakfast') }}</a>
                @else
                    <a class="btn btn-primary btn-xl text-uppercase js-scroll-trigger home-banner-btn" href="/events">{{ __('web.book_event') }}</a>
                @endif
            @endif
                
                
            </div>
        </div>
    </div>
</div>

<section class="page-section" id="about" >
    <div class="@if($offerSection->value == 'true' && $offerData != '')  @else container @endif">
      <div class="@if($offerSection->value == 'true' && $offerData != '')  @else row @endif">
        <div class="@if($offerSection->value == 'true' && $offerData != '') col-lg-6 col-md-6 col-sm-12 col-xs-12 @else col-lg-12 col-md-12 col-sm-12 col-xs-12 @endif text-center">
        {{-- @if( $page )
            @if($page->content)
                {!! $page->content !!}
            @else    
                {!! $content !!}
            @endif
        @else    
            {!! $content !!}  
        @endif     --}}
        @if($page)
            {!! $page->content !!}
        @endif
        </div>
    @if($offerSection->value == 'true' && $offerData != '')
        <div id="offerDiv" class="col-lg-6 col-md-6 col-sm-12 col-xs-12 text-center">
            {{-- $offerData->price --}}
            @if($offerTitle)
                <h3>{{ $offerTitle->value }}</h3>
            @endif
            <a href="{{ $offerUrl->value }}" target="_blank">
                <img src="{{ $offerData->image }}" class="img-responsive mb-4" alt="Product Image" width="50%">
                <h5>{!! $offerData->title !!}</h5>
                <div>{!! $offerData->description !!}</div>
            </a>
        </div>
    @endif
    </div>
</section>

@stop

@section('scripts')
    <script>
        if( jQuery('#mainNav .nav-link.EVENTS').length > 0 ){
            jQuery('.events-button').text(jQuery('#mainNav .nav-link.EVENTS').text());
        }
    </script>
@stop