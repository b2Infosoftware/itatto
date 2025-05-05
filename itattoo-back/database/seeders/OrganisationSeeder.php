<?php

namespace Database\Seeders;

use App\Models\Tag;
use App\Models\Role;
use App\Models\Staff;
use App\Models\Customer;
use App\Models\Location;
use App\Models\Permission;
use App\Models\Organisation;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class OrganisationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        DB::table('organisations')->truncate();
        Schema::enableForeignKeyConstraints();

        $superAdmin = Role::create([
            'name'=>'SuperAdmin',
            'slug'=>'super-admin',
        ]);
        $superAdmin->permissions()->sync(Permission::pluck('id')->all());
        $admin = Staff::factory()->create([
            'first_name' => 'Super',
            'last_name' => 'Admin',
            'email' => 'info@itattoo.com',
            'role_id' => $superAdmin->id,
            'email_verified_at' => now(),
        ]);

        for ($i = 0; $i < 3; $i++) {
            // create organisation owner
            $staff = Staff::factory()->create([
                'email_verified_at' => now(),
            ]);

            // create organisation
            $organisation = Organisation::factory()->create([
                'owner_id' => $staff->id,
            ]);

            // create some tags as well
            $tagNames = ['Blackwork', 'Letter', 'Piercer', 'Traditional'];
            foreach ($tagNames as $key => $tag) {
                Tag::create(['name' => $tag, 'organisation_id' => $organisation->id]);
            }

            // create customers
            Customer::factory(15)->create([
                'organisation_id' => $organisation->id,
            ]);

            // fetch serviceIds and main location which are created in observer
            $serviceIds = $organisation->services()->pluck('id')->all();
            $location = $organisation->locations()->first();

            // create 10 staff members for organisation
            for ($i = 0; $i < 10; $i++) {
                $staff = Staff::factory()->create([
                    'default_organisation_id' => $organisation->id,
                    'default_location_id' => $location->id,
                    'email_verified_at' => now(),
                ]);
                $staff->organisations()->attach(1, ['confirmed_at' => now()]);
                $staff->locations()->sync([$location->id]);
                $staff->tags()->sync([rand(1, 4)]);
                $staff->services()->sync(fake()->randomElement($serviceIds));
                $staff->generateAvailability();
            }
        }

        $admin->organisations()->attach(Organisation::pluck('id')->all());
        $admin->locations()->attach(Location::pluck('id')->all());
        $admin->generateAvailability();
        $admin->update([
            'default_organisation_id' => 1,
            'default_location_id' => 1,
        ]);
    }
}
