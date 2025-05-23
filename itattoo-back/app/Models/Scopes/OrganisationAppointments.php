<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Database\Eloquent\Builder;

class OrganisationAppointments implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     */
    public function apply(Builder $builder, Model $model): void
    {
        $builder->whereHas('location', function ($query) {
            return $query->whereOrganisationId(auth()->user()->default_organisation_id)->whereLocationId(auth()->user()->default_location_id);
        });
    }
}
