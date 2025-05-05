<?php

namespace App\Mail;

use App\Models\Staff;
use App\Models\Customer;
use App\Models\Appointment;
use App\Models\Organisation;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use App\Models\NotificationTemplate;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Contracts\Queue\ShouldQueue;

class CustomEmailTemplate extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $notificationTemplate;

    public $appointment;

    public $staff;

    public $customer;

    public $organisation;

    /**
     * Create a new message instance.
     */
    public function __construct(NotificationTemplate $notificationTemplate, ?Appointment $appointment)
    {
        $this->notificationTemplate = $notificationTemplate;
        $this->appointment = $appointment;
        // $this->staff = $staff;
        // $this->customer = $customer;
        // $this->organisation = $organisation;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->notificationTemplate->subject,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.general',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
