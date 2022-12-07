{{-- @foreach($range as $r)
    @if( $selected == $r || '[&quot;'.$r.'&quot;]' == $selected  )
        <option class="badge bg-danger" value="{{ $r }}" selected>{{ $r }}</option>
    @elseif( !in_array($r, $bookingDetails) )
        <option value="{{ $r }}">{{ $r }}</option>  
    @else
        <option class="badge bg-danger" value="{{ $r }}" disabled="disabled">{{ $r }} ({{ __('treatment.booked') }}) </option>  
    @endif
@endforeach --}}

@foreach($range as $r)
    @if( $selected == $r || '[&quot;'.$r.'&quot;]' == $selected  )
        <option class="badge bg-danger" value="{{ $r }}" selected>{{ $r }}</option>
    @elseif( !in_array($r, $bookingDetails) )
        <option value="{{ $r }}">{{ $r }}</option>  
    @else
        <option class="badge bg-danger" value="{{ $r }}">{{ $r }} ({{ __('treatment.booked') }}) </option>  
    @endif
@endforeach