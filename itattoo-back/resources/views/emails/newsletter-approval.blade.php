@extends('emails.master')

@section('content')
    <h2 style="margin-top: 0px;">
        Newsletter approval confirmation
    </h2>
    <div style="color: #636363; font-size: 14px;">
        <p>
            Hey {{ $customer->first_name }}!
        </p>
        <p>
            We're sending you this email to get your confirmation that you're ok with receiving newsletter from {{$customer->organisation->name}}.
        </p>        
    </div>

    @include('emails.partials.cta', ['link' => $approveUrl, 'button_text' => 'Send me newsletters'])
@endsection


@section('extrafooter')
    <p>
        If you're not ok with receiving marketing email from us please click the link below:
    </p>
    <a href="{{ $rejectUrl }}">{{ $rejectUrl }}</a>
@endsection
