<tr>
<td class="header">
<a href="{{ $url }}" style="display: inline-block;">
@if (trim($slot) === 'Laravel')
<img src="{{ asset('img/logo.png') }}" class="logo" alt="Acai Logo">
@else
{{ $slot }}
@endif
</a>
</td>
</tr>