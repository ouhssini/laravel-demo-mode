<?php

declare(strict_types=1);

namespace Ouhssini\DemoMode;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Ouhssini\DemoMode\Http\Middleware\DemoModeMiddleware;

class DemoModeServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/demo-mode.php',
            'demo-mode'
        );
    }

    public function boot(): void
    {
        $this->loadViewsFrom(
            __DIR__.'/../resources/views',
            'demo-mode'
        );

        Blade::component(
            'demo-mode::components.banner',
            'demo-mode-banner'
        );

        // Internal route that renders the restricted-page view.
        // Excluded as a path itself so it is never blocked by its own check.
        Route::get('/_demo-mode/restricted', function () {
            return response()->view('demo-mode::restricted', [], 403);
        })->name('demo-mode.restricted')
          ->middleware('web');

        $this->publishes([
            __DIR__.'/../config/demo-mode.php' => config_path('demo-mode.php'),
        ], 'demo-mode-config');

        $this->publishes([
            __DIR__.'/../resources/views' => resource_path('views/vendor/demo-mode'),
        ], 'demo-mode-views');

        // Register middleware in the web group.
        // Use the kernel's appendMiddlewareToGroup when available (Laravel 10
        // and apps that kept the Http Kernel), otherwise fall back to the
        // Router's pushMiddlewareToGroup (Laravel 11+, slim app structure).
        $this->app->booted(function (): void {
            if ($this->app->bound(\Illuminate\Contracts\Http\Kernel::class)) {
                $kernel = $this->app->make(\Illuminate\Contracts\Http\Kernel::class);

                if (method_exists($kernel, 'appendMiddlewareToGroup')) {
                    $kernel->appendMiddlewareToGroup('web', DemoModeMiddleware::class);

                    return;
                }
            }

            $this->app['router']->pushMiddlewareToGroup('web', DemoModeMiddleware::class);
        });
    }
}
