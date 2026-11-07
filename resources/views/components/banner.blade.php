@php
    $display = config('demo-mode.display', 'both');

    $showBanner = in_array($display, ['banner', 'both'], true);
    $showToast = in_array($display, ['toast', 'both'], true);
@endphp

@if (config('demo-mode.enabled', false) && ($showBanner || $showToast))

<style>
    /* Demo mode banner */

    .demo-mode-banner {
        position: sticky;
        top: 0;
        z-index: 2147483647;
        display: flex;
        min-height: 44px;
        align-items: center;
        justify-content: center;
        gap: 10px;
        padding: 10px 16px;
        border-bottom: 1px solid #fcd34d;
        background: rgba(255, 251, 235, .97);
        color: #78350f;
        text-align: center;
        box-shadow: 0 1px 3px rgba(15, 23, 42, .08);
        -webkit-backdrop-filter: blur(12px);
        backdrop-filter: blur(12px);
    }

    .demo-mode-banner__content {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: center;
        gap: 4px 9px;
        font-family: inherit;
        font-size: 12px;
        line-height: 1.5;
    }

    .demo-mode-banner__title {
        font-weight: 700;
        letter-spacing: .045em;
    }

    .demo-mode-banner__message {
        font-weight: 500;
        color: #92400e;
    }

    .demo-mode-banner__separator {
        color: #d97706;
    }

    .demo-mode-banner__icon {
        width: 16px;
        height: 16px;
        flex: 0 0 auto;
        color: #d97706;
    }

    html.dark .demo-mode-banner {
        border-bottom-color: rgba(146, 64, 14, .65);
        background: rgba(69, 26, 3, .97);
        color: #fef3c7;
    }

    html.dark .demo-mode-banner__message {
        color: #fde68a;
    }

    html.dark .demo-mode-banner__separator,
    html.dark .demo-mode-banner__icon {
        color: #fbbf24;
    }

    @media (max-width: 520px) {
        .demo-mode-banner {
            gap: 7px;
            padding: 9px 12px;
        }

        .demo-mode-banner__content {
            column-gap: 6px;
            font-size: 11px;
        }
    }

    @media (prefers-reduced-transparency: reduce) {
        .demo-mode-banner {
            -webkit-backdrop-filter: none;
            backdrop-filter: none;
        }
    }

    /* Demo mode toast */

    .demo-mode-toast {
        position: fixed;
        right: 24px;
        bottom: 24px;
        z-index: 2147483647;
        display: flex;
        align-items: flex-start;
        gap: 12px;
        width: calc(100% - 32px);
        max-width: 360px;
        padding: 15px;
        border: 1px solid #fcd34d;
        border-radius: 12px;
        background: #ffffff;
        color: #1f2937;
        box-shadow:
            0 10px 25px rgba(0, 0, 0, .10),
            0 2px 6px rgba(0, 0, 0, .05);
        opacity: 0;
        visibility: hidden;
        transform: translateY(12px);
        transition:
            opacity .2s ease,
            transform .2s ease,
            visibility .2s ease;
        font-family: inherit;
    }

    .demo-mode-toast.is-visible {
        opacity: 1;
        visibility: visible;
        transform: translateY(0);
    }

    .demo-mode-toast__icon {
        display: flex;
        width: 36px;
        height: 36px;
        flex: 0 0 auto;
        align-items: center;
        justify-content: center;
        border-radius: 9px;
        background: #fef3c7;
        color: #b45309;
    }

    .demo-mode-toast__icon svg {
        width: 19px;
        height: 19px;
    }

    .demo-mode-toast__content {
        min-width: 0;
        flex: 1;
    }

    .demo-mode-toast__title {
        margin: 0;
        color: #111827;
        font-size: 13px;
        font-weight: 700;
        line-height: 1.5;
    }

    .demo-mode-toast__message {
        margin: 4px 0 0;
        color: #6b7280;
        font-size: 12px;
        font-weight: 500;
        line-height: 1.5;
        overflow-wrap: anywhere;
    }

    .demo-mode-toast__close {
        display: flex;
        width: 24px;
        height: 24px;
        flex: 0 0 auto;
        align-items: center;
        justify-content: center;
        padding: 0;
        border: 0;
        border-radius: 6px;
        background: transparent;
        color: #9ca3af;
        cursor: pointer;
        font-size: 20px;
        line-height: 1;
    }

    .demo-mode-toast__close:hover {
        background: #f3f4f6;
        color: #374151;
    }

    html.dark .demo-mode-toast {
        border-color: #78350f;
        background: #1f2937;
        color: #f9fafb;
        box-shadow: 0 10px 25px rgba(0, 0, 0, .35);
    }

    html.dark .demo-mode-toast__icon {
        background: rgba(146, 64, 14, .3);
        color: #fbbf24;
    }

    html.dark .demo-mode-toast__title {
        color: #f9fafb;
    }

    html.dark .demo-mode-toast__message {
        color: #9ca3af;
    }

    html.dark .demo-mode-toast__close:hover {
        background: #374151;
        color: #ffffff;
    }

    @media (max-width: 520px) {
        .demo-mode-toast {
            right: 16px;
            bottom: 16px;
        }
    }

    @media (prefers-reduced-motion: reduce) {
        .demo-mode-toast {
            transition: none;
        }
    }
</style>

@if ($showBanner)
    <div class="demo-mode-banner" role="status" aria-live="polite">
        <svg
            class="demo-mode-banner__icon"
            xmlns="http://www.w3.org/2000/svg"
            viewBox="0 0 24 24"
            fill="none"
            stroke="currentColor"
            stroke-width="1.8"
            stroke-linecap="round"
            stroke-linejoin="round"
            aria-hidden="true"
        >
            <path d="M12 3 2.8 19a1.7 1.7 0 0 0 1.5 2.5h15.4a1.7 1.7 0 0 0 1.5-2.5L12 3Z" />
            <path d="M12 9v4" />
            <path d="M12 17h.01" />
        </svg>

        <div class="demo-mode-banner__content">
            <strong class="demo-mode-banner__title">
                {{ config('demo-mode.banner.title', 'DEMO ENVIRONMENT') }}
            </strong>

            <span class="demo-mode-banner__separator" aria-hidden="true">•</span>

            <span class="demo-mode-banner__message">
                {{ config('demo-mode.banner.message', 'Read-only access — modifications are disabled.') }}
            </span>
        </div>
    </div>
@endif

@if ($showToast)
    <div
        id="demo-mode-toast"
        class="demo-mode-toast"
        role="status"
        aria-live="polite"
        aria-atomic="true"
        aria-hidden="true"
    >
        <div class="demo-mode-toast__icon">
            <svg
                xmlns="http://www.w3.org/2000/svg"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="1.8"
                stroke-linecap="round"
                stroke-linejoin="round"
                aria-hidden="true"
            >
                <path d="M12 3 2.8 19a1.7 1.7 0 0 0 1.5 2.5h15.4a1.7 1.7 0 0 0 1.5-2.5L12 3Z" />
                <path d="M12 9v4" />
                <path d="M12 17h.01" />
            </svg>
        </div>

        <div class="demo-mode-toast__content">
            <p id="demo-mode-toast-title" class="demo-mode-toast__title">
                Demo Mode
            </p>

            <p id="demo-mode-toast-message" class="demo-mode-toast__message"></p>
        </div>

        <button
            type="button"
            id="demo-mode-toast-close"
            class="demo-mode-toast__close"
            aria-label="Close notification"
        >
            &times;
        </button>
    </div>

    <script>
        (() => {
            const toast = document.getElementById('demo-mode-toast');

            if (!toast || toast.dataset.initialized === 'true') {
                return;
            }

            toast.dataset.initialized = 'true';

            const titleElement = document.getElementById('demo-mode-toast-title');
            const messageElement = document.getElementById('demo-mode-toast-message');
            const closeButton = document.getElementById('demo-mode-toast-close');

            let dismissTimeout = null;

            function hideToast() {
                clearTimeout(dismissTimeout);

                toast.classList.remove('is-visible');
                toast.setAttribute('aria-hidden', 'true');
            }

            function showToast(notice) {
                if (!notice) {
                    return;
                }

                const title = typeof notice === 'string'
                    ? 'Demo Mode'
                    : notice.title || 'Demo Mode';

                const message = typeof notice === 'string'
                    ? notice
                    : notice.message;

                if (!message) {
                    return;
                }

                clearTimeout(dismissTimeout);

                titleElement.textContent = title;
                messageElement.textContent = message;

                toast.setAttribute('aria-hidden', 'false');
                toast.classList.add('is-visible');

                dismissTimeout = setTimeout(hideToast, 4000);
            }

            closeButton.addEventListener('click', hideToast);

            // Traditional browser form submissions.
            const initialNotice = @json(session('demo_mode_notice'));

            if (initialNotice) {
                showToast(initialNotice);
            }

            // Inertia navigation events.
            document.addEventListener('inertia:navigate', (event) => {
                const notice = event.detail?.page?.props?.demoMode?.notice;

                if (notice) {
                    showToast(notice);
                }
            });
        })();
    </script>
@endif

@endif
