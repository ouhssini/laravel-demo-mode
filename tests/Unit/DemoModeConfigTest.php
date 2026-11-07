<?php

declare(strict_types=1);

use Ouhssini\DemoMode\Tests\TestCase;


it('has demo mode disabled by default', function (): void {
    expect(config('demo-mode.enabled'))->toBeFalse();
});

it('uses both display modes by default', function (): void {
    expect(config('demo-mode.display'))->toBe('both');
});

it('supports banner display mode', function (): void {
    config()->set('demo-mode.display', 'banner');

    expect(config('demo-mode.display'))->toBe('banner');
});

it('supports toast display mode', function (): void {
    config()->set('demo-mode.display', 'toast');

    expect(config('demo-mode.display'))->toBe('toast');
});

it('supports both display mode', function (): void {
    config()->set('demo-mode.display', 'both');

    expect(config('demo-mode.display'))->toBe('both');
});

it('has a default forbidden response status', function (): void {
    expect(config('demo-mode.response.status'))->toBe(403);
});

it('has a default response message', function (): void {
    expect(config('demo-mode.response.message'))
        ->toBe('Demo mode is enabled. Changes are not allowed.');
});