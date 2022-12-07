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
            @if($page)
                {!! $page->content !!}
            @endif
        </div>
    </div>
</section>

@stop