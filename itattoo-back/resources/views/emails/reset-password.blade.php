@extends('emails.master')

@section('content')
    <h2 style="margin-top: 0px;">
        Account Information
    </h2>
    <div style="color: #636363; font-size: 14px;">
        <p>
            Hey {{ $user->first_name }}!
        </p>
        <p>
            You (or someone else) requested a password change for your {{ config('app.name') }} account.
        </p>
        <p>
            If you did not initiate this change no futher action is needed. Otherwise, please click on the button below to
            create a new password for your account.
        </p>
    </div>

    @include('emails.partials.cta', ['link' => $url, 'button_text' => 'Reset your password'])
@endsection


@section('extrafooter')
    <p>
        If you're having troubles clicking the "Reset your password" button, copy and paste the URL below into your browser:
    </p>
    <a href="{{ $url }}">{{ $url }}</a>
@endsection
