@extends('layouts.web')

@section('content')
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