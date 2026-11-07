<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Ouhssini\DemoMode\Http\Middleware\DemoModeMiddleware;
use Ouhssini\DemoMode\Tests\TestCase;


beforeEach(function (): void {
    config()->set('demo-mode.enabled', true);

    Route::middleware(DemoModeMiddleware::class)
        ->post('/_demo-mode/api-write', fn () => response()->json([
            'success' => true,
        ]));
});

it('redirects browser requests back with a flash notice', function (): void {
    $this->from('/dashboard')
        ->post('/_demo-mode/write')
        ->assertRedirect('/dashboard')
        ->assertSessionHas('demo_mode_notice', [
            'title' => 'Demo Mode',
            'message' => 'Demo mode is enabled. Changes are not allowed.',
        ]);
});

it('returns JSON for JSON requests', function (): void {
    $this->postJson('/_demo-mode/api-write')
        ->assertForbidden()
        ->assertExactJson([
            'message' => 'Demo mode is enabled. Changes are not allowed.',
            'demo_mode' => true,
        ]);
});

it('uses the configured JSON response status', function (): void {
    config()->set('demo-mode.response.status', 423);

    $this->postJson('/_demo-mode/api-write')
        ->assertStatus(423);
});

it('uses the configured response message', function (): void {
    config()->set(
        'demo-mode.response.message',
        'This demo account is read-only.'
    );

    $this->postJson('/_demo-mode/api-write')
        ->assertForbidden()
        ->assertJsonPath(
            'message',
            'This demo account is read-only.'
        );
});