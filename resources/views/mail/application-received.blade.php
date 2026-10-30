@extends('mail.shell')

@section('mail-title', 'Application Received')
@section('mail-stage', 'Application update')
@section('mail-heading', 'We received your application.')

@section('mail-content')
    <p style="margin:0 0 22px;color:#292F36;font-family:Poppins,Arial,sans-serif;font-size:16px;line-height:1.6;">Thank you for applying for <strong>{{ $jobTitle }}</strong> at East Equator Express.</p>
    <p style="margin:0 0 28px;color:#292F36;font-family:Poppins,Arial,sans-serif;font-size:16px;line-height:1.6;">Our hiring team will review your application. We will email you if there is an update.</p>
    <p style="margin:0;padding:18px 0 0;border-top:1px solid #292F36;color:#292F36;font-family:Poppins,Arial,sans-serif;font-size:13px;line-height:1.6;">You do not need to submit another application for this role.</p>
@endsection
