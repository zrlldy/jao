@extends('mail.shell')

@section('mail-title', 'Interview Cancelled')
@section('mail-stage', 'Schedule change')
@section('mail-heading', 'Your interview is cancelled.')

@section('mail-content')
    <p style="margin:0 0 22px;color:#292F36;font-family:Poppins,Arial,sans-serif;font-size:16px;line-height:1.6;">The scheduled interview for <strong>{{ $jobTitle }}</strong> at East Equator Express will no longer take place.</p>
    <p style="margin:0 0 28px;color:#292F36;font-family:Poppins,Arial,sans-serif;font-size:16px;line-height:1.6;">There is no need to attend at the previously arranged time. Our hiring team will contact you if another interview is arranged.</p>
    <p style="margin:0;padding:18px 0 0;border-top:1px solid #292F36;color:#292F36;font-family:Poppins,Arial,sans-serif;font-size:13px;line-height:1.6;">You can reply to this email if you have a question about the change.</p>
@endsection
