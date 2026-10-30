@extends('mail.text-shell')

@section('mail-title', 'Interview Rescheduled')

@section('mail-content')
The interview for {!! $jobTitle !!} has been rescheduled. Please use the updated details below.

Date: {!! $interviewDate !!}
Time: {!! $interviewTime !!}
Location: {!! $interviewLocation !!}

Please reply to this email if the new schedule does not work for you.
@endsection
