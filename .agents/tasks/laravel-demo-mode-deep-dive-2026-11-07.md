# Deep-Dive Report: `ouhssini/laravel-demo-mode`

**Investigation date:** 2026-11-07
**Method:** Read-only review of source, config, tests, lock file; ran the Pest suite to confirm runtime behavior.

## Summary first

The package's **core is real, clean, and working**: a read-only demo-mode middleware that blocks mutating HTTP methods, with allowed paths/named-routes exceptions, configurable JSON/redirect responses, a global Blade toast renderer, a persistent banner, and optional Inertia shared props. The Pest suite **passes 35 tests / 62 assertions** against Laravel 13.40.0 on PHP 8.4, with 2 Inertia tests **skipped**.

The honest assessment: this is a **well-built pre-release package, not yet production-ready for distribution**. Three things keep it out of "ship it" territory:

1. **Inertia integration — the headline differentiator — has zero runtime test coverage.** Both Inertia tests self-skip because `inertiajs/inertia-laravel` is not in `require-dev`, so `shareInertiaProps()` (`DemoModeMiddleware.php:42`) and the `demoMode`/`demoModeNotifications` props have never actually executed.
2. **No `LICENSE` file** at the repo root, despite the README badges and footer linking to one and claiming MIT. The `.gitignore` even whitelists `!.env.example` but no `.env.example` ships either.
3. **The advertised support matrix is unverified.** `composer.json` advertises Laravel 11/12/13 and PHP 8.2, but the locked dev environment pins Laravel 13 + PHP 8.3 (orchestra/testbench v11.3 requires `php ^8.3`, `laravel/framework ^13.32`). Only Laravel 13 is ever exercised, and the suite cannot run on PHP 8.2.

---

## 1. Overview

`ouhssini/laravel-demo-mode` is a lightweight Laravel package that puts an application into a read-only demo state: it blocks mutating HTTP methods (`POST`/`PUT`/`PATCH`/`DELETE`), lets safe methods and configured exceptions through, and surfaces a persistent Blade banner plus toast notifications. It optionally shares demo-mode state and normalized flash notifications with Inertia.js during client-side navigation. It is auto-discovered (provider + middleware-in-`web` + Blade component alias) and ships configurable display modes (`banner`/`toast`/`both`), blocked-request response status/message, and allowed paths/named-routes.

- `composer.json`: `php ^8.2`, `illuminate/contracts|http|support ^11.0 || ^12.0 || ^13.0`; dev: `pestphp/pest ^5.3`, `orchestra/testbench ^11.3`.
- `CHANGELOG.md`: entirely `[Unreleased]` — **no stable release has been published.**

## 2. Architecture

Four source files, one config, one Blade view. Clean separation:

```
config/demo-mode.php                          # all knobs (enabled, display, banner, allowed_*, response.*)
src/DemoModeServiceProvider.php               # discovery: config merge, views, Blade alias, web-middleware append
src/Http/Middleware/DemoModeMiddleware.php     # enforcement + Inertia sharing + response shaping
src/Support/FlashNotificationResolver.php     # normalizes many flash conventions into one toast shape
resources/views/components/banner.blade.php    # banner + toast markup + inline CSS/JS, self-contained
```

Request flow (`DemoModeMiddleware.php:17`):

1. `handle()` first calls `shareInertiaProps($request)` on **every** request — this is deliberate so the Blade component can act as a global toast renderer even when demo mode is off (`README` documents this).
2. If demo mode is disabled → `$next($request)`.
3. If the method is in `SAFE_METHODS = ['GET','HEAD','OPTIONS']` (`DemoModeMiddleware.php:15,27`) → pass.
4. If `isAllowedRequest()` matches an `allowed_paths` pattern (`$request->is($pattern)`) or an `allowed_routes` route name → pass (`DemoModeMiddleware.php:108`).
5. Otherwise `handleBlockedRequest()`: JSON for `expectsJson()`/`wantsJson()`/`api/*` (`:86-88`); `redirect()->back(303)->with('demo_mode_notice', …)` for browsers (`:98-99`).

Registration (`DemoModeServiceProvider.php`): `register()` merges config (`:20`); `boot()` loads views under the `demo-mode` namespace (`:24`), registers the `x-demo-mode-banner` alias (`Blade::component('demo-mode::components.banner', 'demo-mode-banner')`, `:29`), declares the two publish tags (`demo-mode-config`, `demo-mode-views`), and appends the middleware to the `web` group inside `$app->booted()` with a `method_exists` guard (`:45-47`) — so it's safe across kernel implementations.

Flash normalization (`FlashNotificationResolver.php`): reads direct keys (`success`/`error`/`notice`/`warning`/`info`, `:14`), typed `flash` arrays, structured `flash` arrays with `type`/`message`, and the internal `demo_mode_notice`, then dedupes by `type|title|message` (`:60-73`).

Blade view (`banner.blade.php`): computes `$showBanner`/`$showToast` from `display` (`:2-6`), renders banner only when `enabled && display∈{banner,both}`, renders toast whenever `display∈{toast,both}` — including when demo mode is off. Toast JS (`:330-410`) queues notifications, auto-dismisses at 4000ms, listens for Inertia's `inertia:navigate` and reads `event.detail?.page?.props?.demoModeNotifications` (`:405-406`).

## 3. What's Implemented (feature-by-feature, with evidence)

Each item is backed by a passing test unless noted.

**Implemented and working (tests pass):**

- **Safe-method pass-through + mutating-method blocking** — GET allowed; POST/PUT/PATCH/DELETE redirected. `DemoModeMiddlewareTest.php` (6 tests pass). `SAFE_METHODS` at `DemoModeMiddleware.php:15`.
- **Allowed paths with wildcards** — `$request->is($pattern)`. `AllowedRequestsTest.php` covers exact + `_demo-mode/*` wildcard (4 tests pass).
- **Allowed named routes** — `$request->route()?->getName()` checked only when a route is resolved. `AllowedRequestsTest.php` (pass).
- **JSON error response** — 403 + configurable `status`/`message`, `{ message, demo_mode:true }` shape. `DemoModeResponseTest.php` (4 tests pass), `handleBlockedRequest()` at `:75`.
- **Browser/Inertia redirect** — `back(303)` with `demo_mode_notice` flash. `DemoModeResponseTest.php` asserts exact session payload.
- **Display modes** — `banner`/`toast`/`both` gate the banner and toast markup independently. `DemoModeDisplayTest.php` (7 tests pass); `$showBanner`/`$showToast` at `banner.blade.php:5-6`.
- **Global toast renderer when demo mode is off** — toast markup still renders when `display` includes `toast` even if `enabled=false`. `DemoModeDisplayTest.php` "hides the demo banner but keeps the global toast renderer" + "renders global flash toasts even when demo mode is disabled" (pass).
- **Flash normalization** — direct keys, typed `flash` arrays, structured `flash` arrays, `demo_mode_notice`, empty/unsupported ignored, dedup. `FlashNotificationResolverTest.php` (6 tests pass).
- **Config defaults + set/get** — `enabled=false`, `display=both`, `status=403`, message string. `DemoModeConfigTest.php` (7 tests pass).
- **Auto-discovery** — provider in `composer.json` `extra.laravel.providers`; middleware appended to `web`; `x-demo-mode-banner` alias. Implicitly proven: feature tests run routes under `Route::middleware('web')` (`tests/TestCase.php:31`) and writes are blocked — i.e., the auto-registration actually works.
- **Publishable assets** — `demo-mode-config` and `demo-mode-views` tags (`DemoModeServiceProvider.php:34-41`), matching the README.
- **Self-contained Blade** — inline CSS/JS, dark-mode (`html.dark`), reduced-motion/reduced-transparency, ARIA (`role="status"`, `aria-live`, close button label). No frontend deps.

**Implemented but fragile/incomplete:**

- **Inertia shared props** — code is present and plausible (`shareInertiaProps()` at `:42-68`, `demoMode` + `demoModeNotifications` shares), but **never executed in CI** because `inertiajs/inertia-laravel` is absent from `require-dev`. See §4/§5.
- **Config validation** — none. An invalid `DEMO_MODE_DISPLAY` silently renders nothing (neither `$showBanner` nor `$showToast` is true). A non-numeric `response.status` is `(int)`-coerced (e.g., `"foo"` → `0`, an invalid HTTP status). No guard, no error.
- **`api/*` JSON detection** is hardcoded (`DemoModeMiddleware.php:88`); apps using a different API prefix and not sending JSON headers won't get the JSON branch.

**Not implemented at all:**

- No `LICENSE` file (see §5, Critical).
- No CI workflows (no `.github/`).
- No `.env.example` (the `.gitignore` whitelists one via `!.env.example`, but none ships).
- No opt-out/config toggle for middleware auto-registration or the target group; it always appends to `web`.
- No `suggest` entry in `composer.json` for `inertiajs/inertia-laravel`.

## 4. Test Coverage

**Suite run (read-only):** `vendor\bin\pest` on PHP 8.4.27 → **35 passed, 2 skipped, 62 assertions, 1.57s**.

| File | Status | What it covers |
|---|---|---|
| `Unit/DemoModeConfigTest.php` | 7 pass | config defaults + set/get (mostly tautological) |
| `Unit/FlashNotificationResolverTest.php` | 6 pass | all flash conventions, dedup indirectly, empty rejection |
| `Unit/ExampleTest.php` | 1 pass | `expect(true)->toBeTrue()` — **default scaffold, should be removed** |
| `Feature/DemoModeMiddlewareTest.php` | 6 pass | safe vs mutating methods, disabled passthrough |
| `Feature/AllowedRequestsTest.php` | 4 pass | allowed path, wildcard, allowed route, deny default |
| `Feature/DemoModeResponseTest.php` | 4 pass | redirect + flash, JSON 403, custom status, custom message |
| `Feature/DemoModeDisplayTest.php` | 7 pass | banner/toast/both gating, toast-renderer-when-off, flash-array |
| `Feature/InertiaIntegrationTest.php` | **2 skipped** | would assert `Inertia::share('demoModeNotifications'|'demoMode', Closure)` — **always skipped, Inertia not installed** |

**Covered well:** middleware method gating, allow-lists, JSON vs redirect response shaping, display-mode gating, flash normalization.

**Not covered / weak:**

- **Inertia integration is entirely unverified at runtime** — the package's main differentiator. The two tests are `markTestSkipped` whenever `class_exists(Inertia::class)` is false, and it is always false here (not in `require-dev`). The actual prop contents, the `notice` legacy value, and the `inertia:navigate` listener path (`banner.blade.php:405-406`) are never exercised by PHP. (The toast JS itself is frontend and has no test at all.)
- **Safe-method coverage is GET-only** — no explicit `HEAD` or `OPTIONS` test.
- **`api/*` branch** isn't isolated from `expectsJson()` (no test distinguishes them).
- **No test asserts the `x-demo-mode-banner` component alias** resolves — `DemoModeDisplayTest` renders the Blade file via `view()->file($viewPath)` (`DemoModeDisplayTest.php:7-13`), bypassing the registered alias. So `Blade::component(...)` (`DemoModeServiceProvider.php:29`) is not directly verified.
- **No test for invalid `display`** (e.g., `DEMO_MODE_DISPLAY=bogus`) or invalid `response.status`.
- **No test for the `info` type**, nor a direct dedup test, nor a multi-notification queue test.
- **No test for publishable tags** (low priority).
- `tests/Unit/ExampleTest.php` is leftover boilerplate; every test file also carries an unused `use Ouhssini\DemoMode\Tests\TestCase;` (Pest.php already does `uses(TestCase::class)`).

## 5. What Needs Improvement

### Critical
- **None security-critical found.** The write-blocking logic is sound, allowed-path matching uses Laravel's `$request->is()` (no injection), and `@json($notifications)` (`banner.blade.php:402`) is **safe against `</script>` breakout** — confirmed: `Illuminate\Support\Js` encodes with `JSON_HEX_TAG|JSON_HEX_APOS|JSON_HEX_AMP|JSON_HEX_QUOT` (`vendor/.../Support/Js.php:26-30`), so `<` becomes `\u003C`. Flash data injected into the toast script is not an XSS vector.

### Important
1. **Inertia feature is untested at runtime.** Add `inertiajs/inertia-laravel` to `require-dev` (or a dedicated CI matrix job) so the two `InertiaIntegrationTest` cases actually run and assert the `demoMode`/`demoModeNotifications` prop shapes. Right now the package's headline capability ships blind. Highest-leverage fix before release.
2. **Missing `LICENSE` file.** README badges and the footer link to `LICENSE` and assert MIT, but no such file exists at the repo root (verified by directory listing). Users cannot legally rely on the stated license. Add the MIT `LICENSE`.
3. **Advertised version matrix is unverified.** `composer.json` says Laravel 11/12/13 + PHP 8.2, but the lock pins `laravel/framework v13.40.0` and `orchestra/testbench v11.3.0` (which requires `php ^8.3`, `laravel/framework ^13.32`). Consequences: (a) only Laravel 13 is ever tested; (b) contributors on PHP 8.2 cannot `composer install` dev deps. Add a CI matrix across the supported Laravel/PHP pairs, or narrow the advertised matrix to what's actually tested.
4. **No config validation.** Invalid `display` silently no-ops; invalid `response.status` coerces to `0`. Validate `display ∈ {banner,toast,both}` and `status` is a valid HTTP code in `boot()` or a dedicated validator, failing loudly on misconfiguration.
5. **Remove `tests/Unit/ExampleTest.php`** and the unused `use …TestCase;` imports across test files.

### Nice-to-have
- Ship a `.env.example` (`.gitignore` already expects it) documenting `DEMO_MODE`, `DEMO_MODE_DISPLAY`, banner title/message.
- Add `inertiajs/inertia-laravel` to `composer.json` `suggest`.
- Make the JSON-detection prefix (`api/*`) and the toast auto-dismiss (4000ms) configurable; the prefix is the only genuinely surprising hardcode.
- Add a config flag to disable/retarget middleware auto-registration (some apps may not want it in `web`).
- Align default casing: Blade's fallback is `'DEMO ENVIRONMENT'` (`banner.blade.php:267`) while the config default is `'Demo Environment'` (`config/demo-mode.php:33`). Harmless (config wins) but inconsistent.
- `phpunit.xml` still references `<directory>app</directory>` under `<source>` — a leftover scaffold (no `app/` exists here); irrelevant unless coverage is enabled, but worth cleaning up.
- Add explicit `HEAD`/`OPTIONS`, invalid-`display`, invalid-`status`, `info`-type, and dedup tests.

## 6. Gaps Between README and Code

- **README ↔ LICENSE:** README badges + footer assert MIT and link to `LICENSE`; the file is absent. **Real gap.**
- **README ↔ version support:** README states "PHP 8.2 for Laravel 11/12, PHP 8.3 for Laravel 13, Laravel 11/12/13". Code declares it, but the locked/tested environment is Laravel 13 only and dev tooling needs PHP 8.3+. **Partial gap** (claims exceed verification).
- **README ↔ Inertia optionality:** README correctly says Inertia is optional and that the package "shares a `demoMode` prop … and a separate `demoModeNotifications` prop." Code matches. But README doesn't disclose that the package's **own test suite skips all Inertia tests** — a transparency gap, not a code gap.
- **README config table:** every documented key exists in `config/demo-mode.php` with matching defaults. **No gap.**
- **README "auto-registers middleware in `web` + `x-demo-mode-banner`":** both true (`DemoModeServiceProvider.php:29,45-47`). **No gap.**
- **README `DEMO_MODE_BANNER_TITLE="DEMO ENVIRONMENT"` example** vs config default `'Demo Environment'`: cosmetic inconsistency only.

## 7. Summary verdict

**Functional, well-structured core — pre-release quality, not yet production-ready for distribution.** The read-only enforcement, response shaping, display modes, flash normalization, and Blade rendering are genuinely implemented and proven by 35 passing tests; the code is typed (`declare(strict_types=1)` everywhere), cleanly separated, auto-discovered, and XSS-safe on the toast injection path. But it is not shippable as-is: the **Inertia integration is the differentiator and is completely untested at runtime**, the **`LICENSE` file is missing**, and the **advertised Laravel 11/12 + PHP 8.2 support is unverified** (only Laravel 13/PHP 8.3 is exercised, and dev install requires PHP 8.3). Close those three and add a multi-version CI matrix, and this is a credible, small, focused package ready for a `v0.1.0` tag.
