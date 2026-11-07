<?php

declare(strict_types=1);

use Ouhssini\DemoMode\Tests\TestCase;


beforeEach(function (): void {
    config()->set('demo-mode.enabled', true);
});

it('allows modification requests when demo mode is disabled', function (): void {
    config()->set('demo-mode.enabled', false);

    $this->post('/_demo-mode/write')
        ->assertOk()
        ->assertSee('written');
});

it('allows GET requests in demo mode', function (): void {
    $this->get('/_demo-mode/read')
        ->assertOk()
        ->assertSee('read');
});

it('blocks POST requests in demo mode', function (): void {
    $this->from('/previous-page')
        ->post('/_demo-mode/write')
        ->assertRedirect('/previous-page')
        ->assertSessionHas('demo_mode_notice');
});

it('blocks PUT requests in demo mode', function (): void {
    $this->from('/previous-page')
        ->put('/_demo-mode/write')
        ->assertRedirect('/previous-page');
});

it('blocks PATCH requests in demo mode', function (): void {
    $this->from('/previous-page')
        ->patch('/_demo-mode/write')
        ->assertRedirect('/previous-page');
});

it('blocks DELETE requests in demo mode', function (): void {
    $this->from('/previous-page')
        ->delete('/_demo-mode/write')
        ->assertRedirect('/previous-page');
});