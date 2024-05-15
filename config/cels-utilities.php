<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Libraries
    |--------------------------------------------------------------------------
    |
    | Here you may specify library integrations endpoint (e.g. FontAwesome).
    | By default this library queries for the latest version of each
    | supported libraries before being used.
    |
    */

    'libraries' => [
        'cache' => 86400, // Cache for 1 day
        'timeout' => 10, // HTTP timeout
        'endpoints' => [
            'fontawesome_kit' => env('FONTAWESOME_KIT'),
            'fontawesome_host_endpoint' => env('FONTAWESOME_HOST'),
        ],
    ],

];
