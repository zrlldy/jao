@props(['mailMessage' => null])
<tr>
    <td class="header" align="center">
        <table class="header-inner" width="520" cellpadding="0" cellspacing="0" role="presentation">
            <tr>
                <td align="center">
                    <img src="{{ $mailMessage ? $mailMessage->embed(public_path('images/mail-logo.png')) : asset('images/mail-logo.png') }}" width="120" alt="East Equator Express">
                </td>
            </tr>
        </table>
    </td>
</tr>
