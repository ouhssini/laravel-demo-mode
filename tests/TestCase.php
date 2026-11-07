<?php

declare(strict_types=1);

namespace Ouhssini\DemoMode\Tests;

use Illuminate\Support\Facades\Route;
use Ouhssini\DemoMode\DemoModeServiceProvider;
use Orchestra\Testbench\TestCase as OrchestraTestCase;

abstract class TestCase extends OrchestraTestCase
{
    protected function getPackageProviders($app): array
    {
        return [
            DemoModeServiceProvider::class,
        ];
    }

    protected function setUp(): void
    {
        parent::setUp();

        config()->set(
            'app.key',
            'base64:'.base64_encode(str_repeat('a', 32))
        );

        config()->set('app.cipher', 'AES-256-CBC');

        config()->set('demo-mode.enabled', false);
        config()->set('demo-mode.display', 'both');

        config()->set('demo-mode.banner.title', 'DEMO ENVIRONMENT');
        config()->set(
            'demo-mode.banner.message',
            'Read-only access — modifications are disabled.'
        );

        config()->set('demo-mode.response.status', 403);
        config()->set(
            'demo-mode.response.message',
            'Demo mode is enabled. Changes are not allowed.'
        );

        config()->set('demo-mode.allowed_paths', []);
        config()->set('demo-mode.allowed_routes', []);

        Route::middleware(['web', \Ouhssini\DemoMode\Http\Middleware\DemoModeMiddleware::class])->group(function (): void {
            Route::get('/_demo-mode/read', fn () => response('read'))
                ->name('demo-mode.test.read');

            Route::match(
                ['POST', 'PUT', 'PATCH', 'DELETE'],
                '/_demo-mode/write',
                fn () => response('written')
            )->name('demo-mode.test.write');

            Route::post(
                '/_demo-mode/allowed-path',
                fn () => response('allowed')
            )->name('demo-mode.test.allowed-path');

            Route::post(
                '/_demo-mode/allowed-route',
                fn () => response('allowed')
            )->name('demo-mode.test.allowed-route');
        });
    }
}