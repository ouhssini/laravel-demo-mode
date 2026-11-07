<?php

declare(strict_types=1);

namespace Ouhssini\DemoMode\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Ouhssini\DemoMode\Support\FlashNotificationResolver;
use Symfony\Component\HttpFoundation\Response;

class DemoModeMiddleware
{
    private const SAFE_METHODS = ['GET', 'HEAD', 'OPTIONS'];

    public function handle(Request $request, Closure $next): Response
    {
        // Share normal application flash messages even when demo mode is off.
        // This allows the Blade component to act as a global toast renderer.
        $this->shareInertiaProps($request);

        if (! config('demo-mode.enabled', false)) {
            return $next($request);
        }

        if (in_array($request->method(), self::SAFE_METHODS, true)) {
            return $next($request);
        }

        if ($this->isAllowedRequest($request)) {
            return $next($request);
        }

        return $this->handleBlockedRequest($request);
    }

    /**
     * Share normalized flash notifications with Inertia without replacing the
     * application's own `flash` prop or requiring a specific flash convention.
     */
    private function shareInertiaProps(Request $request): void
    {
        if (! class_exists(Inertia::class)) {
            return;
        }

        Inertia::share('demoModeNotifications', function () use ($request): array {
            if (! $request->hasSession()) {
                return [];
            }

            return app(FlashNotificationResolver::class)
                ->resolve($request->session());
        });

        if (! config('demo-mode.enabled', false)) {
            return;
        }

        Inertia::share('demoMode', fn () => [
            'enabled' => true,
            'display' => config('demo-mode.display', 'both'),
            'title' => config('demo-mode.banner.title'),
            'message' => config('demo-mode.banner.message'),
            'notice' => $request->hasSession()
                ? $request->session()->get('demo_mode_notice')
                : null,
        ]);
    }

    /**
     * Handle blocked modification requests.
     */
    private function handleBlockedRequest(Request $request): Response
    {
        $message = (string) config(
            'demo-mode.response.message',
            'Demo mode is enabled. Changes are not allowed.'
        );

        $status = (int) config('demo-mode.response.status', 403);

        // API and JSON requests receive a proper HTTP error.
        if (
            $request->expectsJson()
            || $request->wantsJson()
            || $request->is('api/*')
        ) {
            return response()->json([
                'message' => $message,
                'demo_mode' => true,
            ], $status);
        }

        // Browser and Inertia requests return to the previous page.
        return redirect()
            ->back(303)
            ->with('demo_mode_notice', [
                'title' => 'Demo Mode',
                'message' => $message,
            ]);
    }

    /**
     * Check whether the request is explicitly allowed.
     */
    private function isAllowedRequest(Request $request): bool
    {
        foreach (config('demo-mode.allowed_paths', []) as $pattern) {
            if ($request->is($pattern)) {
                return true;
            }
        }

        $routeName = $request->route()?->getName();

        return $routeName !== null
            && in_array(
                $routeName,
                config('demo-mode.allowed_routes', []),
                true
            );
    }
}
