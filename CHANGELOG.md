# Changelog

All notable changes to `ouhssini/laravel-demo-mode` are documented in this file.

The format follows [Keep a Changelog](https://keepachangelog.com/en/1.1.0/), and this project aims to follow [Semantic Versioning](https://semver.org/).

## [Unreleased]

### Added

- Configurable notification display modes through `DEMO_MODE_DISPLAY`.
- Support for `banner`, `toast`, and `both` display modes.
- Toast notifications when modifying requests are blocked.
- Persistent Blade banner for demo-mode status.
- Optional Inertia shared props containing demo-mode information.
- Flash notification support for blocked browser and Inertia requests.
- Configurable banner title and message.
- Configurable blocked-request response status and message.
- Configurable allowed request paths and named routes.
- JSON error responses for API and JSON requests.
- Automated test foundation using Pest and Orchestra Testbench.
- Documentation for installation, configuration, notification modes, and API protection.

### Changed

- Browser and Inertia requests receive a redirect with a flash notification instead of a full-page error response.
- Demo notifications are controlled through a single display configuration.
- Middleware registration is scoped to Laravel's `web` middleware group.

### Security

- Modifying HTTP requests are blocked while demo mode is enabled, except for explicitly allowed paths and named routes.
- Safe HTTP methods (`GET`, `HEAD`, and `OPTIONS`) remain accessible.

## Version history

No stable release history has been documented yet. Release entries will be added here when versions are published.

[Unreleased]: https://github.com/ouhssini/laravel-demo-mode/compare/HEAD...main
