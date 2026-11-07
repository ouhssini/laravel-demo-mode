# Laravel Demo Mode

A lightweight Laravel package that enables a read-only demo environment, provides a reusable Blade banner component, shares optional demo information with Inertia, and blocks modifying HTTP requests.

## Requirements

- PHP 8.2+
- Laravel 11, 12, or 13
- Optional: Inertia.js (only needed for the `demoMode` shared page prop)

## Installation

### Install from a local folder

Place the package in your Laravel project, for example:

```text
packages/ouhssini/laravel-demo-mode
```

Add the local path repository to your application's root `composer.json`:

```json
{
    "repositories": [
        {
            "type": "path",
            "url": "packages/ouhssini/laravel-demo-mode",
            "options": {
                "symlink": true
            }
        }
    ]
}
```

Install the local development version:

```bash
composer require "ouhssini/laravel-demo-mode:@dev"
```

The package service provider is registered through Laravel package auto-discovery. It registers the global middleware and the Blade component automatically.

### Publish configuration (optional)

The package works with its default configuration. Publish it if you want to customize allowed paths or response behavior:

```bash
php artisan vendor:publish --tag=demo-mode-config
```

Enable demo mode in `.env`:

```env
DEMO_MODE=true
DEMO_MODE_BANNER_TITLE="DEMO ENVIRONMENT"
DEMO_MODE_BANNER_MESSAGE="Read-only access — modifications are disabled."
```

Clear cached configuration after changing environment/configuration values:

```bash
php artisan optimize:clear
```

Disable demo mode with `DEMO_MODE=false`.

## Add the banner to Blade

Add the component once in your main `resources/views/app.blade.php`, inside the `<body>` and before the Inertia root element:

```blade
<body class="font-sans antialiased">
    <x-demo-mode-banner />

    <x-inertia::app />
</body>
```

The component:

- Renders only when `DEMO_MODE=true`.
- Is fixed to the top of the viewport with a very high stacking order.
- Adds its own spacer so page content is not hidden underneath it.
- Uses the configured title and message.
- Supports applications that use `html.dark` for dark mode.
- Does not require React, JavaScript, Tailwind scanning, or extra frontend dependencies.

The component is intentionally opt-in: add `<x-demo-mode-banner />` to the application's root Blade view wherever you want the banner to appear.

### Customize the banner view (optional)

Publish the component view:

```bash
php artisan vendor:publish --tag=demo-mode-views
```

The published view will be placed at:

```text
resources/views/vendor/demo-mode/components/banner.blade.php
```

Laravel will use the published view instead of the package view, so you can customize its markup and styling without editing `vendor/` files.

## Protection behavior

When demo mode is enabled:

- `GET`, `HEAD`, and `OPTIONS` requests are allowed.
- Other HTTP methods, including `POST`, `PUT`, `PATCH`, and `DELETE`, are blocked by default.
- Requests matching configured allowed paths or route names are allowed.
- JSON requests receive a JSON error response; browser requests receive the configured HTTP error.
- The middleware runs globally and therefore covers web and API HTTP requests.

Example blocked JSON response:

```json
{
    "message": "Demo mode is enabled. Changes are not allowed.",
    "demo_mode": true
}
```

## Configuration

`config/demo-mode.php`:

| Key | Purpose |
| --- | --- |
| `enabled` | Enables/disables demo mode using `DEMO_MODE`. |
| `banner.title` | Banner heading. |
| `banner.message` | Banner description. |
| `allowed_paths` | Request path patterns exempt from write blocking. |
| `allowed_routes` | Route names exempt from write blocking when the route is resolved. |
| `response.status` | HTTP status returned for blocked requests. |
| `response.message` | Error message returned for blocked requests. |

### Allow another path

```php
'allowed_paths' => [
    'login',
    'logout',
    'webhooks/provider',
],
```

Use exceptions carefully: any modifying request matching an allowed path will pass through. For global middleware, path exceptions are the reliable option because route names may not yet be available when global middleware executes.

## Inertia shared props

If Inertia is installed, the middleware also shares this prop while demo mode is enabled:

```ts
{
    demoMode: {
        enabled: true,
        title: 'DEMO ENVIRONMENT',
        message: 'Read-only access — modifications are disabled.',
    }
}
```

The Blade banner does not depend on this prop. You can use `demoMode.enabled` separately to disable or hide frontend mutation controls for a better read-only user experience. Server-side middleware remains the enforcement layer.

## Important limitations

This package blocks modifying HTTP requests. It does not prevent writes performed outside HTTP requests, such as queued jobs, scheduled tasks, Artisan commands, direct database access, or internal service calls. Protect those separately if they can run against your demo database.

It does not provide database snapshot/reset isolation, tenant isolation, or automatic data restoration.

## License

MIT
