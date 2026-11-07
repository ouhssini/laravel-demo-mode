
<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Demo Mode
    |--------------------------------------------------------------------------
    */

    'enabled' => env('DEMO_MODE', false),

    /*
    |--------------------------------------------------------------------------
    | Notification Display
    |--------------------------------------------------------------------------
    |
    | Available modes:
    | - banner: Show the persistent top banner.
    | - toast: Show only temporary notifications.
    | - both: Show the banner and temporary notifications.
    |
    */

    'display' => env('DEMO_MODE_DISPLAY', 'both'),

    /*
    |--------------------------------------------------------------------------
    | Banner
    |--------------------------------------------------------------------------
    */

    'banner' => [
        'title' => env('DEMO_MODE_BANNER_TITLE', 'Demo Environment'),

        'message' => env(
            'DEMO_MODE_BANNER_MESSAGE',
            'This is a demo version. Changes are disabled.'
        ),
    ],

    /*
    |--------------------------------------------------------------------------
    | Allowed Paths
    |--------------------------------------------------------------------------
    */

    'allowed_paths' => [
        'login',
        'logout',
    ],

    /*
    |--------------------------------------------------------------------------
    | Allowed Routes
    |--------------------------------------------------------------------------
    */

    'allowed_routes' => [
        'login',
        'logout',
    ],

    /*
    |--------------------------------------------------------------------------
    | Blocked Request Response
    |--------------------------------------------------------------------------
    */

    'response' => [
        'status' => 403,

        'message' => 'Demo mode is enabled. Changes are not allowed.',
    ],
];
