<?php

namespace App\Traits;

trait HasPermissions
{
    /**
     * Checks whether a user has permission to perform an action on a given entity.
     *
     * @param [string] $action
     * @param [string] $entity
     * @return bool
     */
    public function hasAccessTo($action, $entity) : bool
    {
        if ($this->isSuperAdmin()) {
            return true;
        }

        if(!$this->defaultOrganisation?->activeSubscription || $this->defaultOrganisation->suspended_at){
            return in_array($action, ['view']) && in_array($entity,['locations','staff','services','appointments']);
            // return false;
        }

        return $this->permissions()
                    ->whereAction($action)
                    ->whereEntity($entity)
                    ->exists();
    }
}
