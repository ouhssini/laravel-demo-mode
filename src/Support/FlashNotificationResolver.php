<?php

declare(strict_types=1);

namespace Ouhssini\DemoMode\Support;

use Illuminate\Contracts\Session\Session;

/**
 * Normalizes common Laravel flash-message conventions for the package toast UI.
 */
class FlashNotificationResolver
{
    private const TYPES = ['success', 'error', 'notice', 'warning', 'info'];

    /**
     * @return array<int, array{type: string, title: string, message: string}>
     */
    public function resolve(Session $session): array
    {
        $notifications = [];

        // Common convention: ->with('success', '...'), ->with('error', '...'), etc.
        foreach (self::TYPES as $type) {
            $value = $session->get($type);

            if ($value !== null) {
                $this->append($notifications, $value, $type);
            }
        }

        // Common convention: ->with('flash', ['error' => '...', 'success' => '...']).
        $flash = $session->get('flash');

        if (is_array($flash)) {
            $typedEntries = false;

            foreach (self::TYPES as $type) {
                if (array_key_exists($type, $flash)) {
                    $typedEntries = true;
                    $this->append($notifications, $flash[$type], $type);
                }
            }

            // Also support ->with('flash', ['type' => 'success', 'message' => '...']).
            if (! $typedEntries && isset($flash['message'])) {
                $type = is_string($flash['type'] ?? null)
                    && in_array($flash['type'], self::TYPES, true)
                        ? $flash['type']
                        : 'notice';

                $this->append($notifications, $flash, $type);
            }
        }

        // Internal notification generated when demo-mode blocks a request.
        $demoNotice = $session->get('demo_mode_notice');

        if ($demoNotice !== null) {
            $this->append($notifications, $demoNotice, 'notice', 'Demo Mode');
        }

        // Avoid duplicate messages when an application exposes the same flash
        // through more than one convention.
        $unique = [];

        foreach ($notifications as $notification) {
            $key = implode('|', [
                $notification['type'],
                $notification['title'],
                $notification['message'],
            ]);

            $unique[$key] = $notification;
        }

        return array_values($unique);
    }

    /**
     * @param array<int, array{type: string, title: string, message: string}> $notifications
     */
    private function append(
        array &$notifications,
        mixed $value,
        string $type,
        ?string $defaultTitle = null
    ): void {
        if (is_string($value) || is_numeric($value)) {
            $message = trim((string) $value);
            $title = $defaultTitle ?? ucfirst($type);
        } elseif (is_array($value)) {
            $message = $value['message'] ?? null;
            $title = $value['title'] ?? ($defaultTitle ?? ucfirst($type));

            if (! is_string($message) && ! is_numeric($message)) {
                return;
            }

            $message = trim((string) $message);
            $title = is_string($title) ? trim($title) : ucfirst($type);
        } else {
            return;
        }

        if ($message === '') {
            return;
        }

        $notifications[] = [
            'type' => $type,
            'title' => $title !== '' ? $title : ucfirst($type),
            'message' => $message,
        ];
    }
}
