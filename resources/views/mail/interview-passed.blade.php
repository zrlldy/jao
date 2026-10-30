@extends('mail.shell')

@section('mail-title', 'Interview Update')
@section('mail-stage', 'Interview update')
@section('mail-heading', 'You passed the interview.')

@section('mail-content')
    <p style="margin:0 0 22px;color:#292F36;font-family:Poppins,Arial,sans-serif;font-size:16px;line-height:1.6;">Thank you for meeting with us about <strong>{{ $jobTitle }}</strong>.</p>
    <p style="margin:0 0 28px;color:#292F36;font-family:Poppins,Arial,sans-serif;font-size:16px;line-height:1.6;">You passed the interview stage. Our hiring team will contact you about the next step.</p>
    <p style="margin:0;padding:18px 0 0;border-top:1px solid #292F36;color:#292F36;font-family:Poppins,Arial,sans-serif;font-size:13px;line-height:1.6;">This update is about the interview stage; any offer will be communicated separately.</p>
@endsection
