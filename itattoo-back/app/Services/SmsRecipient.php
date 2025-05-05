<?php

namespace App\Services;

class SmsRecipient
{
    public $destinatari_destination_addr;

    public $recipients_delivery_states_id;

    public function __construct($destinatari_destination_addr, $destinatari_delivery_states_id = 1)
    {
        $this->destinatari_destination_addr = $destinatari_destination_addr;
        $this->recipients_delivery_states_id = $destinatari_delivery_states_id;
    }
}
