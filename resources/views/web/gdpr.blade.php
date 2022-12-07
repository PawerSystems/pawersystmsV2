@extends('layouts.web')

@section('content')

<style>
    #book{
        margin-top:100px;
    }
</style>
<section>
    <div id="book">           
        @if($page)
        {!! $page->content !!}
        @endif
    </div>
</section>
@stop