<?php

namespace App\Observers;

use App\Models\Role;
use App\Models\Staff;
use App\Models\Service;
use App\Models\Campaign;
use App\Models\Category;
use App\Models\Location;
use App\Models\ConsentForm;
use App\Models\Organisation;
use App\Models\CalendarSettings;
use App\Models\NotificationSettings;
use App\Models\NotificationTemplate;

class OrganisationObserver
{
    /**
     * Handle the Organisation "created" event.
     */
    public function created(Organisation $organisation): void
    {
        CalendarSettings::create(['organisation_id' => $organisation->id]);
        NotificationSettings::create([
            'organisation_id' => $organisation->id,
            'customer_events' => ['created', 'edited', 'canceled', 'remind', 'after'],
            'staff_events' => [],
        ]);

        $location = Location::create([
            'name' => $organisation->name . ' Main Studio',
            'organisation_id' => $organisation->id,
        ]);

        $organisation->staff()->attach($organisation->owner_id, ['confirmed_at' => now()]);
        $location->staff()->attach($organisation->owner_id);

        Staff::whereId($organisation->owner_id)->update([
            'default_organisation_id' => $organisation->id,
            'default_location_id' => $location->id,
        ]);

        foreach (config('prePopulate.categories') as $item) {
            $category = Category::create([
                'name' => $item['name'],
                'organisation_id' => $organisation->id,
            ]);
            foreach ($item['services'] as $key=> $service) {
                Service::create([
                    'name' => $service['name'],
                    'color' => $service['color'],
                    'duration' => $service['duration'],
                    'price' => 0,
                    'position' => $key,
                    'organisation_id' => $organisation->id,
                    'category_id' => $category->id,
                ]);
            }
            $consentForm = config('prePopulate.consentForm.tattoo');
            $consentForm['organisation_id'] = $organisation->id;
            $consentForm['category_id'] = $category->id;
            ConsentForm::create($consentForm);
        }

        $organisation->owner->generateAvailability();

        // seeding some pre-defined roles and assign admin to owner
        Role::generateOrganisationRoles($organisation);
        $admin = Role::where('organisation_id', $organisation->id)->where('name', 'admin')->first();
        $organisation->owner->update(['role_id' => $admin->id]);

        // Templates for reminders and other stuff
        foreach (config('prePopulate.notificationsContent') as $item) {
            NotificationTemplate::create([
                'organisation_id' => $organisation->id,
                'name' => $item['name'],
                'channel' => $item['channel'],
                'type' => $item['slug'],
                'entity' => $item['entity'],
                'subject' => $item['subject'] ?? null,
                'message' => $item['message'],
            ]);
        }

        // Birthday Campaingn
        Campaign::create([
            'organisation_id' =>$organisation->id,
            'name' => 'Birthday Message',
            'scheduled_date' => null,
            'scheduled_time' => '10:00',
            'timezone' => 'UTC',
            'delivered_on' => false,
            'is_active' => true,
            'is_birthday' => true,
            'filters' => [],
            'type' => 'email',
            'email_subject' => 'Happy Birthday',
            'message' => '<p>Happy Birthday! 🎉</p>
                <p>Wishing you a day filled with joy, love, and all your favorite things. Thank you for being a valued customer. Enjoy your special day!</p>
                <p>Best wishes!</p>',
        ]);
    }
}
