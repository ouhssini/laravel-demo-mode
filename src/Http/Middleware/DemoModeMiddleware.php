<?php

declare(strict_types=1);

namespace Ouhssini\DemoMode\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class DemoModeMiddleware
{
    private const SAFE_METHODS = ['GET', 'HEAD', 'OPTIONS'];

    public function handle(Request $request, Closure $next): Response
    {
        if (! config('demo-mode.enabled', false)) {
            return $next($request);
        }

        // Always allow the package's own restricted page through so it can render.
        if ($request->routeIs('demo-mode.restricted')) {
            return $next($request);
        }

        // Excluded paths are hidden from demo users entirely — any HTTP method,
        // including safe ones, is redirected to the restricted page.
        if ($this->isExcludedPath($request)) {
            return $this->handleExcludedPath($request);
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
     * Redirect excluded-path requests to the restricted page.
     */
    private function handleExcludedPath(Request $request): Response
    {
        // JSON / API callers get a structured error instead of a redirect.
        if (
            $request->expectsJson()
            || $request->wantsJson()
            || $request->is('api/*')
        ) {
            $status = (int) config('demo-mode.response.status', 403);

            return response()->json([
                'message' => (string) config(
                    'demo-mode.excluded_response.message',
                    'This page is not available in the demo.'
                ),
                'demo_mode' => true,
            ], $status);
        }

        return redirect()->route('demo-mode.restricted');
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

        // Browser and Inertia requests return to the previous page with a
        // conventional Laravel session flash message. Applications can display
        // the notification through their existing flash-sharing implementation
        // without the package injecting a custom prop.
        $flashType = (string) config('demo-mode.response.flash_type', 'error');

        return redirect()
            ->back(303)
            ->with($flashType, $message);
    }

    /**
     * Check whether the request path is excluded from demo access.
     */
    private function isExcludedPath(Request $request): bool
    {
        foreach (config('demo-mode.excluded_paths', []) as $pattern) {
            if ($request->is($pattern)) {
                return true;
            }
        }

        return false;
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
