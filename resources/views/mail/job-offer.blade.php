@extends('mail.shell')

@section('mail-title', 'Job Offer')
@section('mail-stage', 'Offer')
@section('mail-heading', 'An offer from our team.')

@section('mail-content')
    <p style="margin:0 0 22px;color:#292F36;font-family:Poppins,Arial,sans-serif;font-size:16px;line-height:1.6;">We are pleased to offer you the <strong>{{ $jobTitle }}</strong> role at East Equator Express.</p>
    <p style="margin:0 0 28px;color:#292F36;font-family:Poppins,Arial,sans-serif;font-size:16px;line-height:1.6;">Please review the full offer and its terms before responding.</p>
    @include('mail.action', ['url' => $offerUrl, 'label' => 'Review Offer'])
@endsection
    