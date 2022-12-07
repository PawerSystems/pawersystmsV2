<option value="">-- {{ __('profile.choose_gender') }} --</option>
<option value="man" {{ $selected == 'man' ? 'selected' : '' }}>{{ __('profile.man') }}</option>
<option value="women" {{ $selected == 'women' ? 'selected' : '' }}>{{ __('profile.women') }}</option>
<option value="other" {{ $selected == 'other' ? 'selected' : '' }}>{{ __('profile.other') }}</option>
