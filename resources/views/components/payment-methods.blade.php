<option value="">-- {{ __('payment.choose_payment') }} --</option>
@foreach($methods as $method)
    <option value="{{ $method->id }}" {{ $selected == $method->id ? 'selected' : '' }}>{{ $method->title }}</option>
@endforeach
