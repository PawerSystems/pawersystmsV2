@if( $treatments )
    @foreach($treatments as $treatment)
        @if($treatment->is_insurance == 1)
            @if($CPRForInsurance != Null && $CPRForInsurance->value == 'true') 
                <option value="{{ $treatment->id }}" data-insurance="{{ $treatment->is_insurance }}">{{ $treatment->treatment_name }} ({{ $treatment->time_shown ?: $treatment->inter }} min)</option> 
            @endif
        @else
            <option value="{{ $treatment->id }}" data-insurance="{{ $treatment->is_insurance }}">{{ $treatment->treatment_name }} ({{ $treatment->time_shown ?: $treatment->inter }} min)</option>    
        @endif
    @endforeach
@endif    
