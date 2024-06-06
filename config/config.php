<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Routes
    |--------------------------------------------------------------------------
    |
    | Supported: null, '*', ['storage_link']
    |
    | URL [storage_link]: /api/storage-link
    |
    */
    'routes' => null,

    'paginate' => [
        'limit' => [
            'min' => 15,
            'max' => 100,
        ]
    ],

    'job' => [
        'event_manager' => \Hemend\Api\Foundation\EventManagers\DefaultEventManager::class,
        'model' => \Hemend\Api\Models\JobsTrackers::class,
        'db_connection' => null
    ],
];