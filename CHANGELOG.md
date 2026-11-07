# Changelog

All notable changes to `ouhssini/laravel-demo-mode` are documented in this file.

The format follows [Keep a Changelog](https://keepachangelog.com/en/1.1.0/), and this project aims to follow [Semantic Versioning](https://semver.org/).

## [Unreleased]

### Added

- Configurable notification display modes through `DEMO_MODE_DISPLAY`.
- Support for `banner`, `toast`, and `both` display modes.
- Toast notifications when modifying requests are blocked.
- A global Blade toast renderer that automatically detects common Laravel flash notifications.
- Support for `success`, `error`, `notice`, `warning`, and `info` flash types.
- Support for direct flash keys, typed `flash` arrays, and structured flash notification arrays.
- A configurable `response.flash_type` (and `DEMO_MODE_FLASH_TYPE` env variable) for the conventional Laravel session flash key used when a browser request is blocked.
- Persistent Blade banner for demo-mode status.
- Flash notification support for blocked browser and Inertia requests using conventional session keys.
- Configurable banner title and message.
- Configurable blocked-request response status and message.
- Configurable allowed request paths and named routes.
- JSON error responses for API and JSON requests.
- Automated test foundation using Pest and Orchestra Testbench.
- Documentation for installation, configuration, notification modes, and API protection.

### Changed

- Browser and Inertia requests receive a redirect with a flash notification instead of a full-page error response.
- Demo notifications are controlled through a single display configuration.
- The existing Blade component also renders application flash notifications when toast display is enabled, even if demo mode itself is disabled.
- Blocked browser requests now flash a conventional session message (default `error`, configurable via `response.flash_type`) instead of an internal `demo_mode_notice` key.
- The standalone Blade toast resolves multiple common Laravel flash conventions and deduplicates notifications surfaced through more than one convention.
- Middleware registration is scoped to Laravel's `web` middleware group.

### Removed

- Custom `demoModeNotifications` and `demoMode` Inertia shared props. The package no longer injects Inertia props, reads a custom prop during client-side navigation, or requires changes to the application's `HandleInertiaRequests` middleware.
- The internal `demo_mode_notice` session flash key. Blocked requests now use a conventional, configurable flash key that existing notification implementations can consume directly.

### Security

- Modifying HTTP requests are blocked while demo mode is enabled, except for explicitly allowed paths and named routes.
- Safe HTTP methods (`GET`, `HEAD`, and `OPTIONS`) remain accessible.

## Version history

No stable release history has been documented yet. Release entries will be added here when versions are published.

[Unreleased]: https://github.com/ouhssini/laravel-demo-mode/compare/HEAD...main
