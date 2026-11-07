<?php

declare(strict_types=1);

use Ouhssini\DemoMode\Tests\TestCase;

function renderDemoModeBanner(): string
{
    // Attach the Testbench session to the current request.
    request()->setLaravelSession(app('session.store'));

    $viewPath = dirname(__DIR__, 2)
        . '/resources/views/components/banner.blade.php';

    return view()->file($viewPath)->render();
}

beforeEach(function (): void {
    config()->set('demo-mode.enabled', true);
    config()->set('demo-mode.display', 'both');
    config()->set('demo-mode.banner.title', 'TEST DEMO BANNER');
    config()->set('demo-mode.banner.message', 'TEST DEMO MESSAGE');
});

it('renders the banner in banner display mode', function (): void {
    config()->set('demo-mode.display', 'banner');

    $html = renderDemoModeBanner();

    expect($html)
        ->toContain('class="demo-mode-banner"')
        ->toContain('TEST DEMO BANNER')
        ->toContain('TEST DEMO MESSAGE');
});

it('does not render the banner in toast-only mode', function (): void {
    config()->set('demo-mode.display', 'toast');

    $html = renderDemoModeBanner();

    expect($html)
        ->not->toContain('class="demo-mode-banner"')
        ->toContain('id="demo-mode-toast"');
});

it('renders the banner in both display mode', function (): void {
    config()->set('demo-mode.display', 'both');

    $html = renderDemoModeBanner();

    expect($html)
        ->toContain('class="demo-mode-banner"')
        ->toContain('id="demo-mode-toast"')
        ->toContain('TEST DEMO BANNER');
});

it('hides the demo banner but keeps the global toast renderer when demo mode is disabled', function (): void {
    config()->set('demo-mode.enabled', false);
    config()->set('demo-mode.display', 'both');

    $html = renderDemoModeBanner();

    expect($html)
        ->not->toContain('class="demo-mode-banner"')
        ->toContain('id="demo-mode-toast"');
});

it('renders global flash toasts even when demo mode is disabled', function (): void {
    config()->set('demo-mode.enabled', false);
    config()->set('demo-mode.display', 'both');

    session()->flash('success', 'Your changes were saved.');

    $html = renderDemoModeBanner();

    expect($html)
        ->toContain('id="demo-mode-toast"')
        ->toContain('Your changes were saved.');
});

it('renders flash array notifications without requiring a custom flash convention', function (): void {
    config()->set('demo-mode.enabled', false);
    config()->set('demo-mode.display', 'toast');

    session()->flash('flash', [
        'error' => 'Unable to save your changes.',
    ]);

    $html = renderDemoModeBanner();

    expect($html)
        ->toContain('id="demo-mode-toast"')
        ->toContain('Unable to save your changes.');
});

it('does not render toast markup in banner-only display mode', function (): void {
    config()->set('demo-mode.enabled', true);
    config()->set('demo-mode.display', 'banner');

    session()->flash('success', 'Saved.');

    $html = renderDemoModeBanner();

    expect($html)
        ->toContain('class="demo-mode-banner"')
        ->not->toContain('id="demo-mode-toast"');
});
