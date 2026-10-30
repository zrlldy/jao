@extends('mail.text-shell')

@section('mail-title', 'Interview Cancelled')

@section('mail-content')
The scheduled interview for {!! $jobTitle !!} at East Equator Express will no longer take place.

There is no need to attend at the previously arranged time. Our hiring team will contact you if another interview is arranged.

You can reply to this email if you have a question about the change.
@endsection
