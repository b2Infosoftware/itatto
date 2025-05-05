@extends('emails.master')

@section('content')
{!! str_replace(config('customVariables.newsletter'),
    [
        $companyName,
        $customerName,
    ], 
    $campaign->message )
!!}

@endsection