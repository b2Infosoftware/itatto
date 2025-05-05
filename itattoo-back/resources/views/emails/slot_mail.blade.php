@extends('emails.master')

@section('content')
    @if ($type == 'approve')
        <p>Dear <strong>{{ $publicSlot->first_name }} {{ $publicSlot->last_name }}</strong>,</p>
        <p>We are pleased to inform you that your booking request has been approved. The schedule is as follows:</p>
        <p><strong>Date:</strong> {{ \Carbon\Carbon::parse($publicSlot->date)->format('l, F j, Y') }}</p>
        <p><strong>Start Time:</strong> {{ \Carbon\Carbon::parse($publicSlot->start_time)->format('g:i A') }}</p>
        <p><strong>End Time:</strong> {{ \Carbon\Carbon::parse($publicSlot->end_time)->format('g:i A') }}</p>
        <p>Thank You!</p>
    @elseif ($type == 'rejected')
        <p>Dear <strong>{{ $publicSlot->first_name }} {{ $publicSlot->last_name }}</strong>,</p>
        <p>We regret to inform you that your booking has been rejected.</p>
        <p>Thank You!</p>
    @elseif ($type == 'reschedule')
        <p>Dear <strong>{{ $publicSlot->first_name }} {{ $publicSlot->last_name }}</strong>,</p>
        <p>Your booking has been rescheduled. Please check the updated time.</p>
        <p><strong>Date:</strong> {{ \Carbon\Carbon::parse($publicSlot->date)->format('l, F j, Y') }}</p>
        <p><strong>Start Time:</strong> {{ \Carbon\Carbon::parse($publicSlot->start_time)->format('g:i A') }}</p>
        <p><strong>End Time:</strong> {{ \Carbon\Carbon::parse($publicSlot->end_time)->format('g:i A') }}</p>
        <p><a href="{{ config('app.client') . '/cancel-appointment/' . $publicSlot->id }}">Link For Cancellation</a></p>
        <p>Thank You!</p>
    @else
        <p>Dear <strong>{{ $publicSlot->first_name }} {{ $publicSlot->last_name }}</strong>,</p>
        <p>We have received your booking request.</p>
        <p>Our team will review it and get back to you shortly. Thank you for choosing our service.</p>
        <p>Thank you!</p>
    @endif
@endsection
