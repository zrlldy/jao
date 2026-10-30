@extends('mail.shell')

@section('mail-title', 'Interview Rescheduled')
@section('mail-stage', 'Schedule change')
@section('mail-heading', 'Your interview has a new time.')

@section('mail-content')
    <p style="margin:0 0 22px;color:#292F36;font-family:Poppins,Arial,sans-serif;font-size:16px;line-height:1.6;">The interview for <strong>{{ $jobTitle }}</strong> has been rescheduled. Please use the updated details below.</p>
    @include('mail.interview-details')
    <p style="margin:0;color:#292F36;font-family:Poppins,Arial,sans-serif;font-size:16px;line-height:1.6;">Please reply to this email if the new schedule does not work for you.</p>
@endsection
