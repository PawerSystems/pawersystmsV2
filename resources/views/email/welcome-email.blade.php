@component('mail::message')

{!! $message !!}
<p>
@if($businessName)
    {{ $businessName }}
@else
    {{ config('app.name') }}
@endif
</p>
@endcomponent
