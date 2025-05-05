<?php

namespace App\Providers;

use App\Models\Staff;
use App\Models\Appointment;
use App\Events\StaffCreated;
use App\Models\Organisation;
use App\Models\Subscription;
use App\Events\StaffRegistered;
use App\Observers\StaffObserver;
use App\Listeners\SendInvitation;
use App\Events\InvitationAccepted;
use App\Listeners\SendWelcomeEmail;
use App\Events\StripeWebhookReceived;
use Illuminate\Support\Facades\Event;
use App\Listeners\HandleStripeWebhook;
use App\Observers\AppointmentObserver;
use Illuminate\Auth\Events\Registered;
use App\Observers\OrganisationObserver;
use App\Observers\SubscriptionObserver;
use App\Listeners\SendStaffWelcomeEmail;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        StaffRegistered::class => [
            SendStaffWelcomeEmail::class,
        ],
        StaffCreated::class => [
            SendInvitation::class,
        ],
        // Registered::class => [
        //     SendEmailVerificationNotification::class,
        // ],
        InvitationAccepted::class => [
            SendWelcomeEmail::class,
        ],
        StripeWebhookReceived::class => [
            HandleStripeWebhook::class,
        ],
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        Organisation::observe(OrganisationObserver::class);
        Staff::observe(StaffObserver::class);
        Appointment::observe(AppointmentObserver::class);
        Subscription::observe(SubscriptionObserver::class);
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
