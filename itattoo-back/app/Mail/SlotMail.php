<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SlotMail extends Mailable
{
    use Queueable, SerializesModels;

    public $publicSlot;
    public $type;

    public function __construct($publicSlot, $type = null)
    {
        $this->publicSlot = $publicSlot;
        $this->type = $type;
    }

    public function build()
    {
        return $this->subject('Booking Order Notification')
                    ->view('emails.slot_mail')
                    ->with([
                        'publicSlot' => $this->publicSlot,
                        'type' => $this->type,
                    ]);
    }
}

