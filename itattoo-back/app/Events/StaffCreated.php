<?php

namespace App\Events;

use App\Models\Organisation;
use App\Models\Staff;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class StaffCreated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $staff;

    public $organisation;

    /**
     * Create a new event instance.
     */
    public function __construct(Staff $staff, Organisation $organisation)
    {
        $this->staff = $staff;
        $this->organisation = $organisation;
    }
}
