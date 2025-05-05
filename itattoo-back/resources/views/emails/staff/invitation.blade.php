@extends('emails.master')

@section('content')
    <h2 style="margin-top: 0px;">
        Invitation to Join
    </h2>
    <div style="color: #636363; font-size: 16px;">
        <p>
            Hey {{ $staff->first_name }}!
        </p>
        <p>
            Your account has been added to organisation {{ $organisation->name }}.
        </p>
        <p>
            To join your community, simply click on the button below:
        </p>
    </div>

    @include('emails.partials.cta', ['link' => $client_url, 'button_text' => 'Accept Invitation'])
@endsection

@section('extrafooter')
    <p>
        If you're having troubles clicking the "Accept Invitation" button, copy and paste the URL below into your browser:
    </p>
    <a href="{{ $client_url }}">{{ $client_url }} </a>
@endsection