<?php

declare(strict_types=1);

namespace Ouhssini\DemoMode;

use Illuminate\Contracts\Http\Kernel as HttpKernel;
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

        $this->app->booted(function (): void {
            $kernel = $this->app->make(HttpKernel::class);

            if (method_exists($kernel, 'appendMiddlewareToGroup')) {
                $kernel->appendMiddlewareToGroup(
                    'web',
                    DemoModeMiddleware::class
                );
            }
        });
    }
}
