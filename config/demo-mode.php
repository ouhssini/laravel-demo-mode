
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
    | Excluded Paths
    |--------------------------------------------------------------------------
    |
    | Paths listed here are completely hidden from demo users. Any request —
    | regardless of HTTP method — matching one of these patterns will be
    | redirected to a "page not available" screen with a back button.
    |
    | Patterns use Laravel's $request->is() wildcard syntax:
    |   'settings'    — matches /settings exactly
    |   'settings/*'  — matches /settings/anything
    |   'account'     — matches /account exactly
    |
    */

    'excluded_paths' => [
        // 'settings',
        // 'settings/*',
        // 'account',
    ],

    /*
    |--------------------------------------------------------------------------
    | Excluded Path Response
    |--------------------------------------------------------------------------
    */

    'excluded_response' => [
        'title'   => env('DEMO_MODE_EXCLUDED_TITLE', 'Page Not Available'),
        'message' => env(
            'DEMO_MODE_EXCLUDED_MESSAGE',
            'This page is not available in the demo.'
        ),
    ],

    /*
    |--------------------------------------------------------------------------
    | Blocked Request Response
    |--------------------------------------------------------------------------
    */

    'response' => [
        'status' => 403,

        'message' => 'Demo mode is enabled. Changes are not allowed.',

        /*
        |--------------------------------------------------------------------------
        | Blocked-Request Flash Type
        |--------------------------------------------------------------------------
        |
        | The Laravel session flash key used when a browser request is blocked.
        | This is a conventional key (e.g. "error", "warning", "notice", "info")
        | so applications can display the notification through their existing
        | flash-sharing implementation without a custom prop. Defaults to "error".
        |
        */

        'flash_type' => env('DEMO_MODE_FLASH_TYPE', 'error'),
    ],
];
