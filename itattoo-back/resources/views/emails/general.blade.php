@extends('emails.master')

@section('content')
{!! str_replace(config('customVariables.mail'),
    [
        $appointment->staff?->fullName,
        $appointment->organisation?->name,
        $appointment->service?->name,
        \Carbon\Carbon::parse($appointment->date)->format('D, d M Y'),
        $appointment->start_time,
        $appointment->end_time,
        $appointment->customer?->fullName,
        $appointment->duration,
        $appointment->customer?->vip?->label,
    ], 
    $notificationTemplate->message )!!}


    @if (is_null($appointment->status))
        @include('emails.partials.cta', ['link' => $appointment->generateCancelUrl(), 'button_text' => trans('general.cancel')])
    @endif
@endsection