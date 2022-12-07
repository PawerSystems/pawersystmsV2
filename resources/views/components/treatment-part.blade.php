<option value="">-- {{ __('treatment.choose_treatment') }} --</option>
@foreach($parts as $part)
    @if ( App\Models\TreatmentPartTranslation::where('treatment_part_id',$part->id)->where('key',session('locale'))->count() > 0)
        @php  $translation = App\Models\TreatmentPartTranslation::where('treatment_part_id',$part->id)->where('key',session('locale'))->first(); @endphp

        <option value="{{ $part->id }}" {{ $selected == $part->id ? 'selected' : '' }}>
            {{ $translation->value }}
        </option>
    @else
        <option value="{{ $part->id }}" {{ $selected == $part->id ? 'selected' : '' }}>{{ $part->title }}</option>
    @endif
@endforeach
