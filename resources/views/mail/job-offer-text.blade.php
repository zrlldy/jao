@extends('mail.text-shell')

@section('mail-title', 'Job Offer')

@section('mail-content')
We are pleased to offer you the {!! $jobTitle !!} role at East Equator Express.

Please review the full offer and its terms before responding.

Review Offer: {!! $offerUrl !!}
@endsection
