<option value="">--{{ __('department.select_department') }}--</option>
@foreach($departments as $dep)
    <option value="{{ $dep->id }}">{{ $dep->name }} </option>
@endforeach
