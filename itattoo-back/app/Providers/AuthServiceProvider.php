<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;

use App\Models\Role;
use App\Models\Staff;
use App\Models\Service;
use App\Models\TimeOff;
use App\Models\Category;
use App\Models\Customer;
use App\Models\Location;
use App\Models\Appointment;
use App\Models\ConsentForm;
use App\Models\Availability;
use App\Policies\V1\RolePolicy;
use App\Models\CalendarSettings;
use App\Policies\V1\StaffPolicy;
use App\Policies\V1\ServicePolicy;
use App\Policies\V1\TimeOffPolicy;
use App\Policies\V1\CategoryPolicy;
use App\Policies\V1\CustomerPolicy;
use App\Policies\V1\LocationPolicy;
use Illuminate\Support\Facades\URL;
use App\Models\NotificationTemplate;
use App\Models\Sms;
use App\Policies\V1\AppointmentPolicy;
use App\Policies\V1\ConsentFormPolicy;
use App\Policies\V1\AvailabilityPolicy;
use App\Policies\V1\CalendarSettingsPolicy;
use Illuminate\Auth\Notifications\VerifyEmail;
use App\Policies\V1\NotificationTemplatePolicy;
use App\Policies\V1\SmsPolicy;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Role::class => RolePolicy::class,
        Staff::class => StaffPolicy::class,
        TimeOff::class => TimeOffPolicy::class,
        Service::class => ServicePolicy::class,
        Customer::class => CustomerPolicy::class,
        Location::class => LocationPolicy::class,
        Category::class => CategoryPolicy::class,
        ConsentForm::class => ConsentFormPolicy::class,
        Appointment::class => AppointmentPolicy::class,
        Availability::class => AvailabilityPolicy::class,
        NotificationTemplate::class => NotificationTemplatePolicy::class,
        CalendarSettings::class => CalendarSettingsPolicy::class,
        Sms::class => SmsPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        VerifyEmail::createUrlUsing(function ($notifiable) {
            $frontendUrl = config('app.client') . '/verify-email?';

            $key = ($notifiable instanceof Staff) ? 'staff_id' : 'customer_id';
            $verifyUrl = URL::temporarySignedRoute(
                'verification.verify',
                now()->addMinutes(config('auth.verification.expire', 60)),
                [
                    $key => $notifiable->getKey(),
                    'hash' => sha1($notifiable->getEmailForVerification()),
                ]
            );

            return $frontendUrl . explode('?', $verifyUrl)[1];
        });

        VerifyEmail::toMailUsing(function (object $notifiable, string $url) {
            $subject = 'New ' . config('app.name') . ' account email verification';

            return (new MailMessage)
            ->subject($subject)
            ->markdown('emails.staff.welcome', ['client_url' => $url, 'staff' => $notifiable]);
        });
    }
}
