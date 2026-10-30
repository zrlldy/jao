@extends('mail.text-shell')

@section('mail-title', 'Interview Update')

@section('mail-content')
Thank you for meeting with us about {!! $jobTitle !!}.

You passed the interview stage. Our hiring team will contact you about the next step.

This update is about the interview stage; any offer will be communicated separately.
@endsection
