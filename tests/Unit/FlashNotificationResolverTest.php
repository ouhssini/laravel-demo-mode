<?php

declare(strict_types=1);

use Illuminate\Session\Store;
use Ouhssini\DemoMode\Support\FlashNotificationResolver;

function resolveFlashNotifications(array $values): array
{
    $session = new Store('demo-mode-test', new Illuminate\Session\ArraySessionHandler(120));

    foreach ($values as $key => $value) {
        $session->flash($key, $value);
    }

    return app(FlashNotificationResolver::class)->resolve($session);
}

it('resolves direct success and error flash keys', function (): void {
    $notifications = resolveFlashNotifications([
        'success' => 'Saved successfully.',
        'error' => 'Something went wrong.',
    ]);

    expect($notifications)->toBe([
        ['type' => 'success', 'title' => 'Success', 'message' => 'Saved successfully.'],
        ['type' => 'error', 'title' => 'Error', 'message' => 'Something went wrong.'],
    ]);
});

it('resolves direct notice and warning flash keys', function (): void {
    $notifications = resolveFlashNotifications([
        'notice' => 'Read-only mode is enabled.',
        'warning' => 'Please review this action.',
    ]);

    expect($notifications)->toHaveCount(2)
        ->and($notifications[0]['type'])->toBe('notice')
        ->and($notifications[1]['type'])->toBe('warning');
});

it('resolves flash arrays keyed by notification type', function (): void {
    $notifications = resolveFlashNotifications([
        'flash' => [
            'error' => 'Payment failed.',
        ],
    ]);

    expect($notifications)->toBe([
        ['type' => 'error', 'title' => 'Error', 'message' => 'Payment failed.'],
    ]);
});

it('resolves structured flash notification arrays', function (): void {
    $notifications = resolveFlashNotifications([
        'flash' => [
            'type' => 'success',
            'title' => 'Order saved',
            'message' => 'Your order was saved.',
        ],
    ]);

    expect($notifications)->toBe([
        ['type' => 'success', 'title' => 'Order saved', 'message' => 'Your order was saved.'],
    ]);
});

it('resolves the package demo mode notice', function (): void {
    $notifications = resolveFlashNotifications([
        'demo_mode_notice' => [
            'title' => 'Demo Mode',
            'message' => 'Changes are not allowed.',
        ],
    ]);

    expect($notifications)->toBe([
        ['type' => 'notice', 'title' => 'Demo Mode', 'message' => 'Changes are not allowed.'],
    ]);
});

it('ignores empty or unsupported flash values', function (): void {
    $notifications = resolveFlashNotifications([
        'success' => '',
        'error' => ['unexpected' => 'shape'],
    ]);

    expect($notifications)->toBe([]);
});
