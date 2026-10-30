@extends('mail.shell')

@section('mail-title', 'Application Update')
@section('mail-stage', 'Application update')
@section('mail-heading', 'An update on your application.')

@section('mail-content')
    <p style="margin:0 0 22px;color:#292F36;font-family:Poppins,Arial,sans-serif;font-size:16px;line-height:1.6;">Thank you for your interest in <strong>{{ $jobTitle }}</strong> at East Equator Express and for the time you spent with us.</p>
    <p style="margin:0 0 28px;color:#292F36;font-family:Poppins,Arial,sans-serif;font-size:16px;line-height:1.6;">We will not be moving forward with your application for this role.</p>
    <p style="margin:0;padding:18px 0 0;border-top:1px solid #292F36;color:#292F36;font-family:Poppins,Arial,sans-serif;font-size:13px;line-height:1.6;">We appreciate your interest in East Equator Express and wish you well in your search.</p>
@endsection
