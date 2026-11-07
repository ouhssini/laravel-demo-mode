<?php

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

        $this->shareInertiaProps();

        if (in_array($request->method(), self::SAFE_METHODS, true)) {
            return $next($request);
        }

        if ($this->isAllowedRequest($request)) {
            return $next($request);
        }

        $message = (string) config(
            'demo-mode.response.message',
            'Demo mode is enabled. Changes are not allowed.'
        );
        $status = (int) config('demo-mode.response.status', 403);

        if ($request->expectsJson() || $request->wantsJson()) {
            return response()->json([
                'message' => $message,
                'demo_mode' => true,
            ], $status);
        }

        abort($status, $message);
    }

    private function shareInertiaProps(): void
    {
        if (! class_exists(\Inertia\Inertia::class)) {
            return;
        }

        \Inertia\Inertia::share('demoMode', [
            'enabled' => true,
            'title' => config('demo-mode.banner.title'),
            'message' => config('demo-mode.banner.message'),
        ]);
    }

    private function isAllowedRequest(Request $request): bool
    {
        foreach (config('demo-mode.allowed_paths', []) as $pattern) {
            if ($request->is($pattern)) {
                return true;
            }
        }

        $routeName = $request->route()?->getName();

        return $routeName !== null
            && in_array($routeName, config('demo-mode.allowed_routes', []), true);
    }
}
