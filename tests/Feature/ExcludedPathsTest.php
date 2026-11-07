<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Ouhssini\DemoMode\Tests\TestCase;

beforeEach(function (): void {
    config()->set('demo-mode.enabled', true);
    config()->set('demo-mode.excluded_paths', []);
});

it('does not redirect when excluded_paths is empty', function (): void {
    $this->get('/_demo-mode/read')
        ->assertOk();
});

it('redirects GET requests to the restricted page for excluded paths', function (): void {
    config()->set('demo-mode.excluded_paths', ['settings']);

    Route::middleware('web')->get('/settings', fn () => response('settings page'));

    $this->get('/settings')
        ->assertRedirect('/_demo-mode/restricted');
});

it('redirects POST requests to the restricted page for excluded paths', function (): void {
    config()->set('demo-mode.excluded_paths', ['settings']);

    Route::middleware('web')->match(['GET', 'POST'], '/settings', fn () => response('settings page'));

    $this->from('/dashboard')
        ->post('/settings')
        ->assertRedirect('/_demo-mode/restricted');
});

it('supports wildcard excluded path patterns', function (): void {
    config()->set('demo-mode.excluded_paths', ['account/*']);

    Route::middleware('web')->get('/account/profile', fn () => response('profile'));

    $this->get('/account/profile')
        ->assertRedirect('/_demo-mode/restricted');
});

it('does not redirect a path that is not excluded', function (): void {
    config()->set('demo-mode.excluded_paths', ['settings']);

    $this->get('/_demo-mode/read')
        ->assertOk();
});

it('renders the restricted page with the configured title and message', function (): void {
    config()->set('demo-mode.excluded_response.title', 'Not Available');
    config()->set('demo-mode.excluded_response.message', 'This section is hidden in the demo.');

    $this->get('/_demo-mode/restricted')
        ->assertStatus(403)
        ->assertSee('Not Available')
        ->assertSee('This section is hidden in the demo.')
        ->assertSee('Go back');
});

it('returns JSON for excluded API requests', function (): void {
    config()->set('demo-mode.excluded_paths', ['api/settings']);
    config()->set('demo-mode.excluded_response.message', 'This page is not available in the demo.');

    Route::middleware('web')->get('/api/settings', fn () => response()->json([]));

    $this->getJson('/api/settings')
        ->assertForbidden()
        ->assertJsonPath('demo_mode', true)
        ->assertJsonPath('message', 'This page is not available in the demo.');
});

it('excluded paths take priority over allowed paths', function (): void {
    config()->set('demo-mode.excluded_paths', ['settings']);
    config()->set('demo-mode.allowed_paths', ['settings']);

    Route::middleware('web')->get('/settings', fn () => response('settings'));

    $this->get('/settings')
        ->assertRedirect('/_demo-mode/restricted');
});

it('does not restrict excluded paths when demo mode is disabled', function (): void {
    config()->set('demo-mode.enabled', false);
    config()->set('demo-mode.excluded_paths', ['settings']);

    Route::middleware('web')->get('/settings', fn () => response('settings'));

    $this->get('/settings')
        ->assertOk()
        ->assertSee('settings');
});
