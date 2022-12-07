@php
    $needToReplace = ["https://","http://"];
    $replaceWith = ["",""];
@endphp
@foreach($menu as $key => $value)
    @if(!empty(session('locale')))
        @if( $value->language != session('locale') )
            @continue
        @endif
    @else
        @if( $value->language != 'en' )
            @continue
        @endif
    @endif
    @if( $value->page != 'NOTIFICATION' )
        @php
            $linkCheck = preg_match('/Link-[0-9]/', $value->page);
        @endphp

        @if( $linkCheck != 1  )
            <li class="nav-item">
                <a class="nav-link {{$value->page}}" href="/{{ strtolower(str_replace(' ','',$value->page)) }}">{{ ($value->title ?: $value->page ) }}</a>
            </li> 
        @else  
            <li class="nav-item">
                <a class="nav-link" target="__blank" href="//{{ str_replace($needToReplace,$replaceWith,$value->content) }}">{{ $value->title }}</a>
            </li> 
        @endif
    @endif    
@endforeach