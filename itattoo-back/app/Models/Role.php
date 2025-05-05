<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $guarded = ['id'];

    public function permissions()
    {
        return $this->belongsToMany(Permission::class);
    }

    public function staff()
    {
        return $this->hasMany(Staff::class);
    }

    public static function generateOrganisationRoles(Organisation $organisation)
    {
        $admin = self::create([
            'organisation_id' => $organisation->id,
            'name' => 'Admin',
            'editable' => false,
            'slug' => 'admin',
        ]);
        $manager = self::create([
            'organisation_id' => $organisation->id,
            'name' => 'Manager',
            'slug' => 'manager',
        ]);
        $staff = self::create([
            'organisation_id' => $organisation->id,
            'name' => 'Staff',
            'slug' => 'staff',
        ]);

        // assign permissions;
        $admin->permissions()->sync(Permission::pluck('id')->all());

        $managerPermissions = Permission::whereIn('entity', ['appointments', 'staff', 'customers'])
        ->orWhere(function ($q) {
            $q->whereEntity('services')->whereIn('action', ['view']);
        })->orWhere(function ($q) {
            $q->whereEntity('locations')->whereIn('action', ['view']);
        })->orWhere(function ($q) {
            $q->whereEntity('calendar-settings')->whereIn('action', ['view']);
        })->orWhere(function ($q) {
            $q->whereEntity('roles')->whereIn('action', ['view']);
        })->orWhere(function ($q) {
            $q->whereEntity('consent-forms')->whereIn('action', ['view']);
        })->pluck('id')->all();
        $manager->permissions()->sync($managerPermissions);

        $staffPermissions = Permission::where(function ($q) {
            $q->whereEntity('customers')->whereNotIn('action', ['delete']);
        })->orWhere(function ($q) {
            $q->whereEntity('appointments')->whereNotIn('action', ['manage others']);
        })->orWhere(function ($q) {
            $q->whereEntity('staff')->whereIn('action', ['view', 'edit']);
        })->orWhere(function ($q) {
            $q->whereEntity('services')->whereIn('action', ['view']);
        })->orWhere(function ($q) {
            $q->whereEntity('locations')->whereIn('action', ['view']);
        })->orWhere(function ($q) {
            $q->whereEntity('calendar-settings')->whereIn('action', ['view']);
        })->orWhere(function ($q) {
            $q->whereEntity('roles')->whereIn('action', ['view']);
        })->orWhere(function ($q) {
            $q->whereEntity('consent-forms')->whereIn('action', ['view']);
        })->pluck('id')->all();

        $staff->permissions()->attach($staffPermissions);
    }
}
