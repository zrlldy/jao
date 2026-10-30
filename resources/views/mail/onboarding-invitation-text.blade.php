@extends('mail.text-shell')

@section('mail-title', 'Your Onboarding Starts Here')

@section('mail-content')
We are looking forward to having you join us as {!! $jobTitle !!}.

Open your onboarding checklist to review the information and documents needed before your first day.

Open Onboarding: {!! $onboardingUrl !!}
@endsection
