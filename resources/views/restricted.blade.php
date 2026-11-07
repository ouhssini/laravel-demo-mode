<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>{{ config('demo-mode.excluded_response.title', 'Page Not Available') }}</title>
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            min-height: 100dvh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
            background: #f9fafb;
            font-family: ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont,
                         'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            color: #111827;
        }

        .card {
            width: 100%;
            max-width: 420px;
            background: #ffffff;
            border: 1px solid #e5e7eb;
            border-radius: 16px;
            padding: 40px 36px;
            text-align: center;
            box-shadow: 0 4px 24px rgba(0, 0, 0, .07);
        }

        .icon-wrap {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 64px;
            height: 64px;
            border-radius: 50%;
            background: #fef3c7;
            margin-bottom: 24px;
        }

        .icon-wrap svg {
            width: 32px;
            height: 32px;
            color: #d97706;
        }

        h1 {
            font-size: 20px;
            font-weight: 700;
            line-height: 1.3;
            color: #111827;
            margin-bottom: 10px;
        }

        p {
            font-size: 14px;
            line-height: 1.6;
            color: #6b7280;
            margin-bottom: 28px;
        }

        .back-btn {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            padding: 10px 22px;
            border-radius: 8px;
            background: #111827;
            color: #ffffff;
            font-size: 14px;
            font-weight: 600;
            text-decoration: none;
            transition: background .15s ease;
        }

        .back-btn:hover { background: #374151; }

        .back-btn svg {
            width: 16px;
            height: 16px;
            flex-shrink: 0;
        }

        .demo-badge {
            display: inline-block;
            margin-top: 28px;
            padding: 4px 10px;
            border-radius: 99px;
            background: #fef3c7;
            color: #92400e;
            font-size: 11px;
            font-weight: 600;
            letter-spacing: .04em;
        }

        /* Dark mode */
        @media (prefers-color-scheme: dark) {
            body { background: #0f172a; color: #f1f5f9; }
            .card { background: #1e293b; border-color: #334155; }
            h1 { color: #f1f5f9; }
            p { color: #94a3b8; }
            .back-btn { background: #f1f5f9; color: #0f172a; }
            .back-btn:hover { background: #e2e8f0; }
        }

        /* html.dark class override (Tailwind / app-controlled) */
        html.dark body { background: #0f172a; color: #f1f5f9; }
        html.dark .card { background: #1e293b; border-color: #334155; }
        html.dark h1 { color: #f1f5f9; }
        html.dark p { color: #94a3b8; }
        html.dark .back-btn { background: #f1f5f9; color: #0f172a; }
        html.dark .back-btn:hover { background: #e2e8f0; }
    </style>
</head>
<body>
    <div class="card">
        <div class="icon-wrap">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                 stroke="currentColor" stroke-width="1.8"
                 stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <rect width="18" height="11" x="3" y="11" rx="2" ry="2"/>
                <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
            </svg>
        </div>

        <h1>{{ config('demo-mode.excluded_response.title', 'Page Not Available') }}</h1>

        <p>{{ config('demo-mode.excluded_response.message', 'This page is not available in the demo.') }}</p>

        <a href="{{ url()->previous(url('/')) }}" class="back-btn">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                 stroke="currentColor" stroke-width="2"
                 stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <path d="M19 12H5"/>
                <path d="m12 19-7-7 7-7"/>
            </svg>
            Go back
        </a>

        <div>
            <span class="demo-badge">Demo Mode</span>
        </div>
    </div>
</body>
</html>
