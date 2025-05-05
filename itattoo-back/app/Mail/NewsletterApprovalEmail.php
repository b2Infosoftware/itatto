<?php

namespace App\Mail;

use App\Models\Customer;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Support\Facades\URL;
use Illuminate\Queue\SerializesModels;

class NewsletterApprovalEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $approveUrl;

    public $rejectUrl;

    public $customer;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Customer $customer)
    {
        $approveUrl = URL::signedRoute('newsletter.approve', ['customer' => $customer->id]);
        $rejectUrl = URL::signedRoute('newsletter.reject', ['customer' => $customer->id]);
        $this->approveUrl = config('app.client') . '/newsletter-accept' . explode('newsletter/accept', $approveUrl)[1];
        $this->rejectUrl = config('app.client') . '/newsletter-reject' . explode('newsletter/reject', $rejectUrl)[1];
        $this->customer = $customer;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from($address = config('mail.from.address'), $name = config('mail.from.name'))
            ->subject('Newsletter approval request')
            ->view('emails.newsletter-approval');
    }
}
