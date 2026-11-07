@if (config('demo-mode.enabled', false))
    <style>
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
    </style>

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
