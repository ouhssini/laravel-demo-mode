<?php

namespace Ouhssini\DemoMode\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\Response;

class DemoModeMiddleware
{
    private const SAFE_METHODS = ['GET', 'HEAD', 'OPTIONS'];

    public function handle(Request $request, Closure $next): Response
    {
        if (! config('demo-mode.enabled', false)) {
            return $next($request);
        }

        $this->shareInertiaProps($request);

        if (in_array($request->method(), self::SAFE_METHODS, true)) {
            return $next($request);
        }

        if ($this->isAllowedRequest($request)) {
            return $next($request);
        }

        return $this->handleBlockedRequest($request);
    }

    /**
     * Share demo information and flash notifications with Inertia.
     */
    private function shareInertiaProps(Request $request): void
    {
        if (! class_exists(Inertia::class)) {
            return;
        }

       Inertia::share('demoMode', fn () => [
    'enabled' => true,
    'display' => config('demo-mode.display', 'both'),
    'title' => config('demo-mode.banner.title'),
    'message' => config('demo-mode.banner.message'),

    'notice' => in_array(
        config('demo-mode.display', 'both'),
        ['toast', 'both'],
        true
    ) && $request->hasSession()
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
