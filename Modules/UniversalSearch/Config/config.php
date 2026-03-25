<?php

declare(strict_types=1);

return [
    /*
    |--------------------------------------------------------------------------
    | Universal Search - Route Labels
    |--------------------------------------------------------------------------
    |
    | Optional human labels per route name to show in results.
    | Example:
    | 'labels' => [
    |   'admin.dashboard' => ['title' => 'Dashboard', 'group' => 'Admin'],
    | ],
    */
    'labels' => [],

    /*
    |--------------------------------------------------------------------------
    | Excludes
    |--------------------------------------------------------------------------
    */
    'exclude_name_prefixes' => [
        'ignition.',
        'debugbar.',
        'telescope.',
        'sanctum.',
        'universalsearch.',
    ],
    'exclude_uri_prefixes' => [
        '_ignition',
        '_debugbar',
        'api',
    ],

    /*
    |--------------------------------------------------------------------------
    | Limits
    |--------------------------------------------------------------------------
    */
    'min_query_length' => 2,
    'max_results' => 15,
];

