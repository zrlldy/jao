@extends('mail.text-shell')

@section('mail-title', 'Application Received')

@section('mail-content')
Thank you for applying for {!! $jobTitle !!} at East Equator Express.

Our hiring team will review your application. We will email you if there is an update.

You do not need to submit another application for this role.
@endsection
