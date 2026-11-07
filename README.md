# Laravel Demo Mode

[![Latest Version on Packagist](https://img.shields.io/packagist/v/ouhssini/laravel-demo-mode.svg?style=flat-square)](https://packagist.org/packages/ouhssini/laravel-demo-mode)
[![Total Downloads](https://img.shields.io/packagist/dt/ouhssini/laravel-demo-mode.svg?style=flat-square)](https://packagist.org/packages/ouhssini/laravel-demo-mode)
[![License](https://img.shields.io/packagist/l/ouhssini/laravel-demo-mode.svg?style=flat-square)](LICENSE)

A lightweight Laravel package that enables a read-only demo environment. It blocks modifying HTTP requests and provides optional demo notifications through a Blade banner and toast messages. It also shares demo information with Inertia when available.

- **Packagist:** https://packagist.org/packages/ouhssini/laravel-demo-mode
- **GitHub:** https://github.com/ouhssini/laravel-demo-mode

## Features

- Read-only demo mode for Laravel applications.
- Blocks modifying HTTP methods while allowing safe methods.
- Configurable allowed paths and named routes.
- Configurable JSON response status and message.
- Browser and Inertia redirects with flash notifications.
- Three notification display modes: banner, toast, or both.
- Optional Inertia shared props.
- Self-contained Blade component with its own CSS and JavaScript.
- Laravel package auto-discovery.

## Requirements

- PHP 8.2 or later for Laravel 11 and 12.
- PHP 8.3 or later for Laravel 13.
- Laravel 11, 12, or 13.
- Inertia.js is optional. It is only needed if you want to consume the `demoMode` shared page prop and use Inertia-related toast notifications.

## Installation

Install the package from Packagist:

```bash
composer require ouhssini/laravel-demo-mode
```

Laravel package auto-discovery registers the service provider. The package automatically registers its middleware in Laravel's `web` middleware group and registers the `x-demo-mode-banner` Blade component.

No manual service-provider registration or local path repository is required.

## Enable demo mode

Add these variables to your application's `.env` file:

```env
DEMO_MODE=true
DEMO_MODE_DISPLAY=both
DEMO_MODE_BANNER_TITLE="DEMO ENVIRONMENT"
DEMO_MODE_BANNER_MESSAGE="Read-only access — modifications are disabled."
```

Demo mode is disabled by default.

To disable it:

```env
DEMO_MODE=false
```

After changing environment or configuration values, clear the cached configuration:

```bash
php artisan optimize:clear
```

## Notification display modes

Use `DEMO_MODE_DISPLAY` to choose how demo notifications appear:

```env
DEMO_MODE_DISPLAY=both
```

| Mode | Description |
|---|---|
| `banner` | Displays the persistent demo banner. |
| `toast` | Displays temporary toast notifications when a modifying request is blocked. |
| `both` | Displays the banner and toast notifications. |

The default mode is `both`.

### Examples

**Banner only**

```env
DEMO_MODE_DISPLAY=banner
```

**Toast only**

```env
DEMO_MODE_DISPLAY=toast
```

**Banner and toast**

```env
DEMO_MODE_DISPLAY=both
```

The banner communicates that the application is running in demo mode. Toast notifications provide immediate feedback when a user attempts an action that is blocked.

## Add the Blade component

The notification component is opt-in. Add it once to your main Blade root view, usually `resources/views/app.blade.php`, inside the `<body>` element and before the Inertia root element:

```blade
<body class="font-sans antialiased">
    <x-demo-mode-banner />

    <x-inertia::app />
</body>
```

If your application uses a different Inertia root element, keep your existing root element and place `<x-demo-mode-banner />` before it.

The component:

- Renders nothing when demo mode is disabled.
- Displays the persistent banner according to `DEMO_MODE_DISPLAY`.
- Displays toast notifications according to `DEMO_MODE_DISPLAY`.
- Uses the configured banner title and message.
- Supports applications that apply dark mode through `html.dark`.
- Includes its own styles and JavaScript.
- Does not require React, Sonner, Tailwind CSS scanning, or additional frontend dependencies.

The banner uses sticky positioning, so it remains in the document flow and does not need a separate spacer element.

### Customize the Blade view (optional)

Publish the package views:

```bash
php artisan vendor:publish --tag=demo-mode-views
```

The view will be published to:

```text
resources/views/vendor/demo-mode/components/banner.blade.php
```

Laravel will use the published view, allowing you to customize its markup and styling without editing files inside `vendor/`.

## Publish configuration (optional)

The package works with its default configuration. Publish the configuration file if you want to customize display modes, allowed paths, allowed route names, or blocked-request responses:

```bash
php artisan vendor:publish --tag=demo-mode-config
```

This creates:

```text
config/demo-mode.php
```

### Configuration options

| Key | Description |
|---|---|
| `enabled` | Enables or disables demo mode. Defaults to the `DEMO_MODE` environment variable, which defaults to `false`. |
| `display` | Notification display mode: `banner`, `toast`, or `both`. Defaults to `both`. |
| `banner.title` | Banner heading. |
| `banner.message` | Banner description. |
| `allowed_paths` | Request path patterns exempt from write blocking. |
| `allowed_routes` | Route names exempt from write blocking when a route is available to the middleware. |
| `response.status` | HTTP status returned for blocked JSON requests. Defaults to `403`. |
| `response.message` | Message returned for blocked requests. |

### Allow specific paths

You can configure paths that should bypass demo-mode write protection in `config/demo-mode.php`:

```php
'allowed_paths' => [
    'login',
    'logout',
    'webhooks/provider',
],
```

Path patterns use Laravel's request path matching. For example, `webhooks/*` can allow a group of matching paths.

**Use exceptions carefully:** modifying requests matching an allowed path will pass through the demo-mode protection.

You can also allow specific named routes:

```php
'allowed_routes' => [
    'demo.webhook',
    'demo.logout',
],
```

Route names are checked when Laravel has resolved the route. Path exceptions are more reliable when middleware runs before route resolution.

## Protection behavior

When demo mode is enabled:

- `GET`, `HEAD`, and `OPTIONS` requests are allowed.
- Other HTTP methods, including `POST`, `PUT`, `PATCH`, and `DELETE`, are blocked by default.
- Requests matching configured allowed paths are allowed.
- Configured allowed route names are checked when a route is available.
- Browser and Inertia requests are redirected back with a flash notification.
- API and JSON requests receive a JSON error response.
- The middleware is automatically registered in the `web` middleware group.

Example JSON response for a blocked request:

```json
{
    "message": "Demo mode is enabled. Changes are not allowed.",
    "demo_mode": true
}
```

The default JSON response status is `403 Forbidden`.

### API route protection

The package automatically registers its middleware in the `web` middleware group. API routes are not automatically covered by that registration.

To protect API routes too, register `Ouhssini\DemoMode\Http\Middleware\DemoModeMiddleware` in your API middleware group or attach it to the relevant API routes.

API and JSON requests receive the configured JSON error response rather than a browser redirect.

## Inertia shared props

If Inertia is installed, the middleware shares a `demoMode` prop while demo mode is enabled:

```ts
{
    demoMode: {
        enabled: true,
        display: 'both',
        title: 'DEMO ENVIRONMENT',
        message: 'Read-only access — modifications are disabled.',
        notice: null,
    }
}
```

The `display` value reflects the configured notification mode.

The `notice` property contains the flash notification for a blocked action when toast notifications are enabled. It is `null` when there is no notification to display.

You can use `demoMode.enabled` in your React components to hide or disable create, edit, and delete controls for a clearer read-only user experience.

Frontend controls are only a usability improvement. The server-side middleware is the enforcement layer and must remain enabled.

## Important limitations

This package blocks modifying HTTP requests handled by its middleware. It does not prevent writes performed outside the HTTP request lifecycle, including:

- Queued jobs and background workers.
- Scheduled tasks.
- Artisan commands.
- Direct database access.
- Internal service calls.

Protect those execution paths separately if they can write to your demo database.

The package does not provide database snapshot/reset isolation, tenant isolation, or automatic data restoration.

## Testing

The package uses Pest and Orchestra Testbench for automated tests.

Install the development dependencies:

```bash
composer install
```

Run the test suite:

```bash
vendor/bin/pest
```

## Changelog

See [CHANGELOG.md](CHANGELOG.md) for details about changes in each release.

## Contributing

Contributions, issues, and feature requests are welcome.

Please check the existing issues before opening a new one:

https://github.com/ouhssini/laravel-demo-mode/issues

## License

This package is open-sourced software licensed under the [MIT license](LICENSE).
