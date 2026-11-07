<?php

return [
    /* Enable demo mode using DEMO_MODE=true in your .env file. */
    'enabled' => env('DEMO_MODE', false),

    'banner' => [
        'title' => env('DEMO_MODE_BANNER_TITLE', 'Demo Environment'),
        'message' => env(
            'DEMO_MODE_BANNER_MESSAGE',
            'This is a demo version. Changes are disabled.'
        ),
    ],

    /*
     * Safe HTTP methods are always allowed. These paths are also allowed
     * to use modifying methods. Use Laravel request path patterns.
     * Examples: login, logout, webhook/*
     */
    'allowed_paths' => [
        'login',
        'logout',
    ],

    /* Route names are checked when Laravel has already resolved the route. */
    'allowed_routes' => [
        'login',
        'logout',
    ],

    'response' => [
        'status' => 403,
        'message' => 'Demo mode is enabled. Changes are not allowed.',
    ],
];
