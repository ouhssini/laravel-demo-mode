<?php

declare(strict_types=1);

namespace Ouhssini\DemoMode;

use Illuminate\Contracts\Http\Kernel as HttpKernel;
use Illuminate\Support\Facades\Blade;
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
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'demo-mode');

        Blade::component('demo-mode::components.banner', 'demo-mode-banner');

        $this->publishes([
            __DIR__.'/../config/demo-mode.php' => config_path('demo-mode.php'),
        ], 'demo-mode-config');

        $this->publishes([
            __DIR__.'/../resources/views' => resource_path('views/vendor/demo-mode'),
        ], 'demo-mode-views');

        // Register globally so both web and API requests are protected.
        $this->app->booted(function (): void {
            $kernel = $this->app->make(HttpKernel::class);

            if (method_exists($kernel, 'pushMiddleware')) {
                $kernel->pushMiddleware(DemoModeMiddleware::class);
            }
        });
    }
}
