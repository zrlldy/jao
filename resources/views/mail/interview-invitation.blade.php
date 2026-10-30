@extends('mail.shell')

@section('mail-title', 'Interview Invitation')
@section('mail-stage', 'Interview')
@section('mail-heading', "Let's meet.")

@section('mail-content')
    <p style="margin:0 0 22px;color:#292F36;font-family:Poppins,Arial,sans-serif;font-size:16px;line-height:1.6;">We would like to invite you to interview for <strong>{{ $jobTitle }}</strong> at East Equator Express.</p>
    @include('mail.interview-details')
    <p style="margin:0;color:#292F36;font-family:Poppins,Arial,sans-serif;font-size:16px;line-height:1.6;">Please reply to this email if you cannot attend or need an accommodation.</p>
@endsection
