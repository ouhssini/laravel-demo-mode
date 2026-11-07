<?php

declare(strict_types=1);

use Ouhssini\DemoMode\Tests\TestCase;

beforeEach(function (): void {
    config()->set('demo-mode.enabled', true);
});

it('flashes the blocked-request message under the conventional error key', function (): void {
    $this->from('/dashboard')
        ->post('/_demo-mode/write')
        ->assertRedirect('/dashboard')
        ->assertSessionHas('error', 'Demo mode is enabled. Changes are not allowed.');
});

it('does not flash the legacy internal demo_mode_notice key', function (): void {
    $this->from('/dashboard')
        ->post('/_demo-mode/write')
        ->assertRedirect('/dashboard')
        ->assertSessionMissing('demo_mode_notice');
});

it('respects the configured flash type', function (): void {
    config()->set('demo-mode.response.flash_type', 'warning');

    $this->from('/dashboard')
        ->post('/_demo-mode/write')
        ->assertRedirect('/dashboard')
        ->assertSessionHas('warning', 'Demo mode is enabled. Changes are not allowed.')
        ->assertSessionMissing('error');
});

it('uses the configured response message for the flash', function (): void {
    config()->set('demo-mode.response.message', 'This demo account is read-only.');

    $this->from('/dashboard')
        ->post('/_demo-mode/write')
        ->assertSessionHas('error', 'This demo account is read-only.');
});
