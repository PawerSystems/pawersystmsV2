@extends('layouts.web')

@section('content')

<style>
@media(max-width:768px){
    #status{
        margin: 100px 0 50px 0;
    }
}
    
</style>
<section class="container">
    <div class="col-md-12">
        <div id="status" class="col-md-12">
            @if($page != NULL && $page->content != NULL)
                {!! $page->content !!}
                <br><br>
                <form action="/booking" type="POST">
                    @csrf
                    <button class="btn btn-success btn-md">{{ __('insurance.agree') }}</button>
                </form>
            @else
                {!! __('insurance.notification') !!}
                <br><br>
                <form action="/booking" type="POST">
                    @csrf
                    <button class="btn btn-success btn-md">{{ __('insurance.agree') }}</button>
                </form>
            @endif
        </div>
    </div>
</section>

@stop