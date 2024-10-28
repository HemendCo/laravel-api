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
        'trackers_table' => env('API_DB_QUEUE_TRACKERS_TABLE', 'system_jobs_trackers'),
        'event_manager' => \Hemend\Api\Foundation\EventManagers\DefaultEventManager::class,
        'model' => \Hemend\Api\Models\JobsTrackers::class,
        'db_connection' => null
    ],
];