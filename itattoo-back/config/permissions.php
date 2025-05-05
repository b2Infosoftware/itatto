<?php

$crudActions = [
    'view',
    'create',
    'edit',
    'delete',
];

/**
 *  This is a list with all available permissions for entities.
 *  On top of this there are also the role names that we consider as permissions.
 *
 *  Those are going to be dynamic since we have a CRUD for roles.
 */
return [
    'entities' => [
        'dashboard' => ['view'],
        'appointments' => array_merge($crudActions, ['manage others']),
        'customers' => array_merge($crudActions, ['manage photos', 'manage videos', 'view stats']),
        'staff' => $crudActions,
        'services' => $crudActions,
        'campaigns' => $crudActions,
        'settings' => [
            'view',
            'edit',
        ],
        'locations' => $crudActions,
        'calendar-settings' => ['view', 'edit'],
        'roles' => $crudActions,
        'notifications' => ['view', 'edit'],
        'consent-forms' => $crudActions,
        'logs' => ['view'],
    ],
];
