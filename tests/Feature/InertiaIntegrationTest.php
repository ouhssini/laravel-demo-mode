<?php

declare(strict_types=1);

use Illuminate\Http\Request;
use Inertia\Inertia;
use Ouhssini\DemoMode\Http\Middleware\DemoModeMiddleware;

it('shares demo mode and normalized notification props with Inertia', function (): void {
    if (! class_exists(Inertia::class)) {
        $this->markTestSkipped(
            'Inertia is not installed. Install inertiajs/inertia-laravel to run this test.'
        );
    }

    config()->set('demo-mode.enabled', true);
    config()->set('demo-mode.display', 'both');

    Inertia::shouldReceive('share')
        ->once()
        ->with('demoModeNotifications', Mockery::type(Closure::class));

    Inertia::shouldReceive('share')
        ->once()
        ->with('demoMode', Mockery::type(Closure::class));

    $request = Request::create('/dashboard', 'GET');
    $middleware = app(DemoModeMiddleware::class);

    $response = $middleware->handle(
        $request,
        fn ($request) => response('OK')
    );

    expect($response->getContent())->toBe('OK');
});

it('shares global notifications even when demo mode is disabled', function (): void {
    if (! class_exists(Inertia::class)) {
        $this->markTestSkipped(
            'Inertia is not installed. Install inertiajs/inertia-laravel to run this test.'
        );
    }

    config()->set('demo-mode.enabled', false);

    Inertia::shouldReceive('share')
        ->once()
        ->with('demoModeNotifications', Mockery::type(Closure::class));

    Inertia::shouldReceive('share')
        ->never()
        ->with('demoMode', Mockery::type(Closure::class));

    $request = Request::create('/dashboard', 'GET');
    $middleware = app(DemoModeMiddleware::class);

    $response = $middleware->handle(
        $request,
        fn ($request) => response('OK')
    );

    expect($response->getContent())->toBe('OK');
});
