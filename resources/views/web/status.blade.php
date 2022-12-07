@extends('layouts.web')

@section('content')

<section class="page-section" id="bod1">
    <div class="container">
        <div class="row">
            <div class="col-lg-12 text-center">
                <h2 class="section-headingbooking text-uppercase">
                    <font style="vertical-align: inherit;"><font style="vertical-align: inherit;">Status</font></font>
                </h2>
            </div>
        </div>

        <div class="col-lg-12 text-center">
            <h3 class="section-subheadingbooking text-muted">
                <font style="vertical-align: inherit;">
                    <font style="vertical-align: inherit;">{{ __('web.status_desc') }}</font>
                </font>
            </h3>
            {{-- <h3 class="section-subheadingbooking text-muted">
                <font style="vertical-align: inherit;"><font style="vertical-align: inherit;"> If you have deleted the email, click here: </font></font>
                <a href="{{ route('resendemail',session('business_name')) }}">
                    <font style="vertical-align: inherit;"><font style="vertical-align: inherit;">Resend email</font></font>
                </a>
            </h3> --}}
        </div>

        <div class="tbl-header" style="padding-right: 0px;">
            <table cellpadding="0" cellspacing="0" border="0">
                <thead>
                    <tr>
                        <th class="th-fir">
                            <font style="vertical-align: inherit;"><font style="vertical-align: inherit;">{{ __('web.date/time') }}</font></font>
                        </th>
                        <th class="th-sec">
                            <font style="vertical-align: inherit;"><font style="vertical-align: inherit;">{{ __('event.event') }}</font></font>
                        </th>
                        <th class="th-trd">
                            <font style="vertical-align: inherit;"><font style="vertical-align: inherit;">{{ __('web.booking') }}</font></font>
                        </th>
                    </tr>
                </thead>
            </table>
        </div>
        <div class="tbl-content">
            <table cellpadding="0" cellspacing="0" border="0">
                <tbody>
                    @foreach ($events as $event)
                        <tr>
                            <td class="th-fir">
                                
                                <font style="vertical-align: inherit;"><font style="vertical-align: inherit;"> {{ \Carbon\Carbon::parse($event->date)->format($dateFormat->value).' '.$event->time.' ('.$event->duration.' min)'}}</font></font>
                            </td>
                            <td class="th-sec">
                                <font style="vertical-align: inherit;"><font style="vertical-align: inherit;">{{ $event->name }}</font></font>
                            </td>
                            <td class="th-trd">
                                <font style="vertical-align: inherit;"><font style="vertical-align: inherit;">    
                                    {{ $event->eventBookedSlots->count() }} 
                                    {{ __('keywords.out_of') }} 
                                    {{ $event->slots }} 
                                    @if($event->eventGuestSlots->count() > 0 )
                                        ({{ $event->eventGuestSlots->count() }} {{ __('event.guest') }}) 
                                    @endif
                                    @if($event->eventWaitingSlots->count() > 0 )
                                        ({{ $event->eventWaitingSlots->count() }} {{ __('event.waiting') }}) 
                                    @endif
                                </font></font>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</section>

@stop