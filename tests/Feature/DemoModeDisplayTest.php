<?php

declare(strict_types=1);

use Ouhssini\DemoMode\Tests\TestCase;


function renderDemoModeBanner(): string
{
    $viewPath = dirname(__DIR__, 2)
        . '/resources/views/components/banner.blade.php';

    return view()->file($viewPath)->render();
}

beforeEach(function (): void {
    config()->set('demo-mode.enabled', true);
    config()->set('demo-mode.banner.title', 'TEST DEMO BANNER');
    config()->set('demo-mode.banner.message', 'TEST DEMO MESSAGE');
});

it('renders the banner in banner display mode', function (): void {
    config()->set('demo-mode.display', 'banner');

    $html = renderDemoModeBanner();

    expect($html)->toContain('TEST DEMO BANNER')
        ->toContain('TEST DEMO MESSAGE');
});

it('does not render the banner in toast-only mode', function (): void {
    config()->set('demo-mode.display', 'toast');

    $html = renderDemoModeBanner();

    expect($html)->not->toContain('TEST DEMO BANNER');
});

it('renders the banner in both display mode', function (): void {
    config()->set('demo-mode.display', 'both');

    $html = renderDemoModeBanner();

    expect($html)->toContain('TEST DEMO BANNER')
        ->toContain('TEST DEMO MESSAGE');
});

it('renders nothing when demo mode is disabled', function (): void {
    config()->set('demo-mode.enabled', false);

    $html = renderDemoModeBanner();

    expect(trim($html))->toBeEmpty();
});