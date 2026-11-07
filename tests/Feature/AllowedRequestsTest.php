<?php

declare(strict_types=1);

use Ouhssini\DemoMode\Tests\TestCase;


beforeEach(function (): void {
    config()->set('demo-mode.enabled', true);
});

it('allows requests matching an allowed path', function (): void {
    config()->set('demo-mode.allowed_paths', [
        '_demo-mode/allowed-path',
    ]);

    $this->post('/_demo-mode/allowed-path')
        ->assertOk()
        ->assertSee('allowed');
});

it('supports wildcard allowed paths', function (): void {
    config()->set('demo-mode.allowed_paths', [
        '_demo-mode/*',
    ]);

    $this->post('/_demo-mode/write')
        ->assertOk()
        ->assertSee('written');
});

it('allows requests matching an allowed route name', function (): void {
    config()->set('demo-mode.allowed_routes', [
        'demo-mode.test.allowed-route',
    ]);

    $this->post('/_demo-mode/allowed-route')
        ->assertOk()
        ->assertSee('allowed');
});

it('blocks requests that are not whitelisted', function (): void {
    config()->set('demo-mode.allowed_paths', []);
    config()->set('demo-mode.allowed_routes', []);

    $this->from('/previous-page')
        ->post('/_demo-mode/write')
        ->assertRedirect('/previous-page')
        ->assertSessionHas('error');
});