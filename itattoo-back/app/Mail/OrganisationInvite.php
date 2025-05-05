<?php

namespace App\Mail;

use App\Models\Staff;
use App\Models\Organisation;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Support\Facades\URL;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Contracts\Queue\ShouldQueue;

class OrganisationInvite extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $staff;

    public $client_url;

    public $organisation;

    /**
     * Create a new message instance.
     */
    public function __construct(Staff $staff, Organisation $organisation)
    {
        $this->staff = $staff;
        $this->organisation = $organisation;
        $verificationUrl = URL::temporarySignedRoute(
            'accept.invitation',
            now()->addMinutes(60),
            [
                'id' => $staff->id,
                'organisation_id' => $organisation->id,
                'hash' => sha1($staff->email),
            ]
        );
        $this->client_url = config('app.client') . '/accept-invitation?' . explode('?', $verificationUrl)[1];
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'New Member',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.staff.invitation',
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
