<tr>
<td>
<table class="footer" align="center" width="570" cellpadding="0" cellspacing="0" role="presentation">
<tr>
<td class="content-cell" align="center">
{{-- Illuminate\Mail\Markdown::parse($slot) --}}
© {{ date('Y') }}
@if(session('businessName'))
    {{ session('businessName') }}
@else
    {{ config('app.name') }}
@endif
.<br> All rights reserved.
</td>
</tr>
</table>
</td>
</tr>
