@extends('emails.master')

@section('content')
    <h2 style="margin-top: 0px;">
        Account Verification
    </h2>
    <div style="color: #636363; font-size: 16px;">
        <p>
            Hey {{ $staff->first_name }}!
        </p>
        <p>
            It's great to see you've joined {{ config('app.name') }}! Happy to have you on board!
        </p>
        <p>
            To continue using your new {{ config('app.name') }} account please verify your email address below:
        </p>
    </div>

    @include('emails.partials.cta', ['link' => $client_url, 'button_text' => 'Verify account'])
@endsection

@section('extrafooter')
    <p>
        If you're having troubles clicking the "Verify account" button, copy and paste the URL below into your browser:
    </p>
    <a href="{{ $client_url }}">{{ $client_url }} </a>
@endsection