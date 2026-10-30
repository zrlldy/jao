@extends('mail.shell')

@section('mail-title', 'Your Onboarding Starts Here')
@section('mail-stage', 'Onboarding')
@section('mail-heading', 'Welcome to East Equator Express.')

@section('mail-content')
    <p style="margin:0 0 22px;color:#292F36;font-family:Poppins,Arial,sans-serif;font-size:16px;line-height:1.6;">We are looking forward to having you join us as <strong>{{ $jobTitle }}</strong>.</p>
    <p style="margin:0 0 28px;color:#292F36;font-family:Poppins,Arial,sans-serif;font-size:16px;line-height:1.6;">Open your onboarding checklist to review the information and documents needed before your first day.</p>
    @include('mail.action', ['url' => $onboardingUrl, 'label' => 'Open Onboarding'])
@endsection
