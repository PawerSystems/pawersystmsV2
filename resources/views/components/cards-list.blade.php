@if($cards->count() > 0)
    <option value="">-- {{ __('card.select_card') }} --</option>
    @foreach($cards as $c)
        <option value="{{ $c->id }}" {{ date($c->expiry_date) < date('Y-m-d') ? 'disabled' : '' }} {{ $card == $c->id ? 'selected' : '' }}>{{ $c->name }} {{ date($c->expiry_date) < date('Y-m-d') ? '(Expired)' : '' }} </option>
    @endforeach
@else
<option value="">{{ __('card.no_cards') }}</option>
@endif