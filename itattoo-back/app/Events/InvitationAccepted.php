<?php

namespace App\Events;

use App\Models\Staff;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class InvitationAccepted
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $staff;

    public $password;
    /**
     * Create a new event instance.
     */
    public function __construct(Staff $staff, $password)
    {
        $this->staff = $staff;
        $this->password = $password;
    }
}
