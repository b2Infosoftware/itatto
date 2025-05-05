<?php

namespace App\Mail;

use App\Models\Campaign;
use App\Models\Customer;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Contracts\Queue\ShouldQueue;

class CampaignEmail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $campaign;

    public $customerName;

    public $companyName;

    /**
     * Create a new message instance.
     */
    public function __construct(Campaign $campaign, Customer $customer)
    {
        $this->campaign = $campaign;
        $this->companyName = $campaign->organisation->name;
        $this->customerName = $customer->first_name;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->campaign->email_subject,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.campaign',
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
