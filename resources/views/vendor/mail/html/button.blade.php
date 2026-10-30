@props([
    'url',
    'color' => 'primary',
    'align' => 'center',
])
<table class="action" width="100%" cellpadding="0" cellspacing="0" role="presentation">
<tr>
<td class="action-cell" align="{{ $align }}" bgcolor="#FDFDFF">
<table border="0" cellpadding="0" cellspacing="0" role="presentation">
<tr>
<td>
<a href="{{ $url }}" class="button button-{{ $color }}" target="_blank" rel="noopener">{!! $slot !!}</a>
</td>
</tr>
</table>
</td>
</tr>
</table>
