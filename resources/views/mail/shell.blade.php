<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="color-scheme" content="light">
    <title>@yield('mail-title')</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&amp;display=swap" rel="stylesheet">
    <style>
        @media only screen and (max-width: 600px) {
            .mail-content { padding-left: 24px !important; padding-right: 24px !important; }
            .mail-title { font-size: 28px !important; }
        }
    </style>
</head>
<body style="margin:0;padding:0;background-color:#FDFDFF;color:#292F36;font-family:Poppins,Arial,sans-serif;">
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="width:100%;border-collapse:collapse;background-color:#FDFDFF;">
    <tr>
        <td align="center" style="border-top:6px solid #800000;">
            <table role="presentation" width="520" cellpadding="0" cellspacing="0" style="width:100%;max-width:520px;border-collapse:collapse;">
                <tr>
                    <td align="center" style="padding:28px 24px 32px;">
                        <img src="{{ isset($message) ? $message->embed(public_path('images/mail-logo.png')) : asset('images/mail-logo.png') }}" width="120" alt="East Equator Express" style="display:block;width:120px;max-width:100%;height:auto;border:0;">
                    </td>
                </tr>
                <tr>
                    <td class="mail-content" style="padding:8px 42px 28px;">
                        @hasSection('mail-stage')
                            <p style="margin:0 0 12px;color:#800000;font-family:Poppins,Arial,sans-serif;font-size:11px;font-weight:700;letter-spacing:1.6px;line-height:1.5;text-transform:uppercase;">@yield('mail-stage')</p>
                        @endif
                        <h1 class="mail-title" style="margin:0 0 30px;color:#292F36;font-family:Poppins,Arial,sans-serif;font-size:34px;font-weight:700;line-height:1.2;">@yield('mail-heading')</h1>
                        @yield('mail-content')
                    </td>
                </tr>
                <tr>
                    <td align="center" style="padding:24px 24px 40px;border-top:1px solid #292F36;color:#292F36;font-family:Poppins,Arial,sans-serif;font-size:12px;line-height:1.6;">
                        <p style="margin:0 0 10px;">East Equator Express<br>Mabolo, Cebu City, Philippines 6000</p>
                        <p style="margin:0 0 10px;"><a href="https://www.facebook.com/p/East-Equator-Express-PH-61553359472898/" style="color:#800000;text-decoration:underline;">Facebook</a> &nbsp;&middot;&nbsp; <a href="https://ph.linkedin.com/company/east-equator-express" style="color:#800000;text-decoration:underline;">LinkedIn</a></p>
                        <p style="margin:0;"><a href="https://www.tridevs.org/" style="color:#800000;text-decoration:underline;">Powered by Tridevs</a></p>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
</body>
</html>
