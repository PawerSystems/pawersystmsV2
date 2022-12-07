<option value="">-- {{ __('profile.choose_year') }} --</option>
@for($i = 1900; $i <= date('Y'); $i++ )
    <option value="{{$i}}" {{ $selected == $i ? 'selected' : '' }}>{{$i}}</option>
@endfor