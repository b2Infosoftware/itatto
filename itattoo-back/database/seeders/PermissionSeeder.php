<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $all = config('permissions.entities');

        // seeding platform entitites
        foreach ($all as $entity => $actions) {
            foreach ($actions as $action) {
                Permission::create([
                    'entity' => $entity,
                    'action' => $action,
                ]);
            }
        }
    }
}
