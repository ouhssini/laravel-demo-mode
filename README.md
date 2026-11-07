# Laravel Demo Mode

[![Latest Version on Packagist](https://img.shields.io/packagist/v/ouhssini/laravel-demo-mode.svg?style=flat-square)](https://packagist.org/packages/ouhssini/laravel-demo-mode)
[![Total Downloads](https://img.shields.io/packagist/dt/ouhssini/laravel-demo-mode.svg?style=flat-square)](https://packagist.org/packages/ouhssini/laravel-demo-mode)
[![License](https://img.shields.io/packagist/l/ouhssini/laravel-demo-mode.svg?style=flat-square)](LICENSE)

A lightweight Laravel package that enables a read-only demo environment, provides an optional Blade banner component, shares demo information with Inertia when available, and blocks modifying HTTP requests.

- **Packagist:** <https://packagist.org/packages/ouhssini/laravel-demo-mode>
- **GitHub:** <https://github.com/ouhssini/laravel-demo-mode>

## Requirements

- PHP 8.2 or later for Laravel 11 and 12.
- PHP 8.3 or later for Laravel 13.
- Laravel 11, 12, or 13.
- Inertia.js is optional. It is only needed if you want to consume the `demoMode` shared page prop.

## Installation

Install the stable package directly from Packagist:

```bash
composer require ouhssini/laravel-demo-mode
```

Laravel package auto-discovery registers the service provider. The package automatically registers its global middleware and the `<x-demo-mode-banner />` Blade component.

No manual service-provider registration or local path repository is required.

## Enable demo mode

Add the following to your application's `.env` file:

```env
DEMO_MODE=true
DEMO_MODE_BANNER_TITLE="DEMO ENVIRONMENT"
DEMO_MODE_BANNER_MESSAGE="Read-only access — modifications are disabled."
```

Demo mode is disabled by default. To disable it, set:

```env
DEMO_MODE=false
```

After changing environment or configuration values, clear cached configuration:

```bash
php artisan optimize:clear
```

## Add the banner to Blade

The banner is opt-in. Add the component once to your main Blade root view, usually `resources/views/app.blade.php`, inside the `<body>` and before the Inertia root element:

```blade
<body class="font-sans antialiased">
    <x-demo-mode-banner />

    <x-inertia::app />
</body>
```

If your application uses a different Inertia root element, keep your existing root element and place `<x-demo-mode-banner />` before it.

The component:

- Renders nothing when `DEMO_MODE` is disabled.
- Displays a responsive banner at the top of the page when demo mode is enabled.
- Uses the configured title and message.
- Supports applications that apply dark mode through `html.dark`.
- Includes its own styles and does not require React, JavaScript, Tailwind scanning, or extra frontend dependencies.

The component uses sticky positioning, so it remains in the document flow and does not need a separate spacer element.

### Customize the banner view (optional)

Publish the package's Blade views:

```bash
php artisan vendor:publish --tag=demo-mode-views
```

The banner view will be published to:

```text
resources/views/vendor/demo-mode/components/banner.blade.php
```

Laravel will use the published view, allowing you to customize its markup and styling without editing files in `vendor/`.

## Publish configuration (optional)

The package works with its default configuration. Publish the configuration file if you want to customize allowed paths, allowed route names, or blocked-request responses:

```bash
php artisan vendor:publish --tag=demo-mode-config
```

This creates:

```text
config/demo-mode.php
```

### Configuration options

| Key | Description |
| --- | --- |
| `enabled` | Enables or disables demo mode. Defaults to the `DEMO_MODE` environment variable, which defaults to `false`. |
| `banner.title` | Banner heading. |
| `banner.message` | Banner description. |
| `allowed_paths` | Request path patterns exempt from write blocking. |
| `allowed_routes` | Route names exempt from write blocking when a route is available to the middleware. |
| `response.status` | HTTP status returned for blocked requests. Defaults to `403`. |
| `response.message` | Error message returned for blocked requests. |

### Allow specific paths

By default, the package allows the `login` and `logout` paths so users can authenticate and leave the demo environment.

You can add paths in `config/demo-mode.php`:

```php
'allowed_paths' => [
    'login',
    'logout',
    'webhooks/provider',
],
```

Path patterns use Laravel's request path matching. For example, `webhooks/*` can allow a group of matching paths.

**Use exceptions carefully:** modifying requests matching an allowed path will pass through the demo-mode protection. Since the package middleware is registered globally, path exceptions are the recommended and most reliable way to allow requests. A route name may not yet be available when global middleware runs.

## Protection behavior

When demo mode is enabled:

- `GET`, `HEAD`, and `OPTIONS` requests are allowed.
- Other HTTP methods, including `POST`, `PUT`, `PATCH`, and `DELETE`, are blocked by default.
- Requests matching configured allowed paths are allowed.
- Configured allowed route names are checked when a route name is available.
- Requests expecting JSON receive a JSON error response.
- Other requests receive the configured HTTP error response.
- The middleware is registered globally, so it protects HTTP requests across the application's web and API routes.

Example JSON response for a blocked request:

```json
{
    "message": "Demo mode is enabled. Changes are not allowed.",
    "demo_mode": true
}
```

The default response status is `403 Forbidden`.

## Inertia shared props

If Inertia is installed, the middleware shares a `demoMode` prop while demo mode is enabled:

```ts
{
    demoMode: {
        enabled: true,
        title: 'DEMO ENVIRONMENT',
        message: 'Read-only access — modifications are disabled.',
    }
}
```

The Blade banner does not depend on this prop. You can use `demoMode.enabled` in your React components to hide or disable create, edit, and delete controls for a clearer read-only user experience.

Frontend controls are only a usability improvement. The server-side middleware is the enforcement layer and must remain enabled.

## Important limitations

This package blocks modifying HTTP requests. It does not prevent writes performed outside the HTTP request lifecycle, including:

- Queued jobs and background workers.
- Scheduled tasks.
- Artisan commands.
- Direct database access.
- Internal service calls.

Protect those execution paths separately if they can write to your demo database.

The package does not provide database snapshot/reset isolation, tenant isolation, or automatic data restoration.

## License

This package is open-sourced software licensed under the [MIT license](LICENSE).
