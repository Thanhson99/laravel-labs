<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ $title ?? 'Laravel Labs Hub' }}</title>
        <style>
            :root {
                color-scheme: light;
                --bg: #f8fafc;
                --panel: #ffffff;
                --ink: #172033;
                --muted: #627084;
                --line: #d8e0ea;
                --brand: #d93f2f;
                --brand-soft: #fff1ee;
                --green: #13795b;
                --amber: #9a6400;
                --blue: #2563eb;
            }

            * { box-sizing: border-box; }

            body {
                margin: 0;
                background: var(--bg);
                color: var(--ink);
                font-family: ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
                line-height: 1.6;
            }

            a { color: inherit; }

            .shell {
                width: min(1180px, calc(100% - 32px));
                margin: 0 auto;
                padding: 28px 0 56px;
            }

            .topbar {
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 16px;
                margin-bottom: 28px;
            }

            .brand {
                display: flex;
                flex-direction: column;
                gap: 2px;
                text-decoration: none;
            }

            .brand strong {
                font-size: 20px;
                letter-spacing: 0;
            }

            .brand span, .muted {
                color: var(--muted);
                font-size: 14px;
            }

            .nav {
                display: flex;
                gap: 10px;
                flex-wrap: wrap;
            }

            .button {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                min-height: 38px;
                padding: 8px 12px;
                border: 1px solid var(--line);
                border-radius: 8px;
                background: var(--panel);
                color: var(--ink);
                text-decoration: none;
                font-weight: 650;
            }

            .button.primary {
                background: var(--brand);
                border-color: var(--brand);
                color: #ffffff;
            }

            .hero, .panel {
                background: var(--panel);
                border: 1px solid var(--line);
                border-radius: 8px;
            }

            .hero {
                padding: 28px;
                margin-bottom: 20px;
            }

            h1, h2, h3 {
                line-height: 1.2;
                margin: 0;
                letter-spacing: 0;
            }

            h1 { font-size: clamp(32px, 5vw, 54px); max-width: 920px; }
            h2 { font-size: 24px; margin-bottom: 12px; }
            h3 { font-size: 18px; }

            .hero p {
                max-width: 780px;
                color: var(--muted);
                font-size: 18px;
                margin: 16px 0 0;
            }

            .grid {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
                gap: 14px;
            }

            .panel {
                padding: 18px;
            }

            .stat {
                display: flex;
                flex-direction: column;
                gap: 6px;
            }

            .stat strong {
                font-size: 30px;
                line-height: 1;
            }

            .section {
                margin-top: 22px;
            }

            .badge {
                display: inline-flex;
                align-items: center;
                width: fit-content;
                padding: 3px 8px;
                border-radius: 999px;
                background: #eef4ff;
                color: var(--blue);
                font-size: 12px;
                font-weight: 700;
            }

            .badge.done {
                background: #eaf8f2;
                color: var(--green);
            }

            .badge.pending {
                background: #fff6df;
                color: var(--amber);
            }

            .list {
                display: grid;
                gap: 12px;
            }

            .item {
                background: var(--panel);
                border: 1px solid var(--line);
                border-radius: 8px;
                padding: 16px;
            }

            .meta {
                display: flex;
                align-items: center;
                flex-wrap: wrap;
                gap: 8px;
                margin-bottom: 8px;
            }

            .filters {
                display: grid;
                grid-template-columns: minmax(220px, 1fr) repeat(3, minmax(120px, 160px)) auto;
                gap: 10px;
                align-items: end;
                margin-bottom: 16px;
            }

            label {
                display: grid;
                gap: 5px;
                color: var(--muted);
                font-size: 13px;
                font-weight: 650;
            }

            input, select {
                width: 100%;
                min-height: 40px;
                border: 1px solid var(--line);
                border-radius: 8px;
                background: var(--panel);
                color: var(--ink);
                padding: 8px 10px;
                font: inherit;
            }

            pre {
                overflow: auto;
                padding: 12px;
                border-radius: 8px;
                background: #101827;
                color: #f7fafc;
                font-size: 13px;
            }

            .raw-json {
                max-height: 520px;
            }

            @media (max-width: 760px) {
                .topbar, .filters {
                    grid-template-columns: 1fr;
                    align-items: stretch;
                }

                .topbar {
                    display: grid;
                }

                .hero {
                    padding: 20px;
                }
            }
        </style>
    </head>
    <body>
        <div class="shell">
            <header class="topbar">
                <a class="brand" href="{{ route('learning.dashboard') }}">
                    <strong>Laravel Practice Hub</strong>
                    <span>Code-first workspace for learning Laravel by doing</span>
                </a>
                <nav class="nav" aria-label="Primary navigation">
                    <a class="button" href="{{ route('learning.dashboard') }}">Dashboard</a>
                    <a class="button" href="{{ route('practice.index') }}">Practice</a>
                    <a class="button" href="{{ route('learning.labs') }}">Labs</a>
                    <a class="button" href="{{ route('learning.quiz') }}">Quiz</a>
                    <a class="button" href="{{ route('learning.study-plan') }}">Study plan</a>
                    <a class="button" href="{{ route('learning.questions') }}">Reference</a>
                    <a class="button" href="../index.html">Static portal</a>
                </nav>
            </header>

            @yield('content')
        </div>
    </body>
</html>
