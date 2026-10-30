@extends('mail.text-shell')

@section('mail-title', 'Interview Invitation')

@section('mail-content')
We would like to invite you to interview for {!! $jobTitle !!} at East Equator Express.

Date: {!! $interviewDate !!}
Time: {!! $interviewTime !!}
Location: {!! $interviewLocation !!}

Please reply to this email if you cannot attend or need an accommodation.
@endsection
