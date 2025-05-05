@extends('emails.master')

@section('content')
    <h2 style="margin-top: 0px;">
        Get Started
    </h2>
    <div style="color: #636363; font-size: 16px;">
        <p>
            Hey {{ $staff->first_name }}!
        </p>
        <p>
            Your account has been created. Below are your system generated credentials.
        </p>
        <p style="font-weight: bold">
            Please change the password immediately after login.
        </p>
        <p>
            Your temporary password is: <span style="font-weight: bold">{{ $password }}</span>
        </p>

        @include('emails.partials.cta', ['link' => $client_url, 'button_text' => 'Login to your account'])
    </div>

@endsection

