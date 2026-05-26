@extends('learning.layout', ['title' => 'Security Escape Preview Workbench'])

@section('content')
    <section class="hero">
        <span class="badge">Runnable workbench</span>
        <h1>Practice Blade escaping with unsafe-looking content.</h1>
        <p>
            This workbench calls a real API that validates user input, escapes it, detects risky patterns,
            and explains which Blade output rule to use.
        </p>
    </section>

    <section class="section">
        <div class="grid">
            <article class="panel">
                <h2>Unsafe Input</h2>
                <form id="escapePreviewForm">
                    <label>
                        Title
                        <input name="title" value="Profile bio preview" autocomplete="off">
                    </label>

                    <label style="margin-top: 12px;">
                        Body
                        <textarea name="body" rows="8" style="width:100%; border:1px solid var(--line); border-radius:8px; padding:10px; font:inherit;">Hello <script>alert('xss')</script> Laravel</textarea>
                    </label>

                    <button class="button primary" type="submit" style="margin-top: 14px;">Preview escaped output</button>
                </form>
            </article>

            <article class="panel">
                <h2>Escaped Preview</h2>
                <p class="muted" id="escapePreviewStatus">Submit the form to inspect escaped output.</p>
                <pre class="raw-json"><code id="escapePreviewOutput">POST /api/practice/security-escape-preview</code></pre>
            </article>

            <article class="panel">
                <h2>Files To Read</h2>
                <ul>
                    <li><code>routes/api/practice-actions.php</code></li>
                    <li><code>app/Http/Requests/Api/PreviewEscapedContentRequest.php</code></li>
                    <li><code>app/Http/Controllers/Api/SecurityEscapePreviewController.php</code></li>
                    <li><code>app/Services/Practice/SecurityEscapePreviewService.php</code></li>
                    <li><code>resources/views/practice/workbench/security-escape-preview.blade.php</code></li>
                    <li><code>tests/Feature/SecurityEscapePreviewWorkbenchTest.php</code></li>
                </ul>
            </article>
        </div>
    </section>

    <section class="section">
        <h2>Practice Variations</h2>
        <div class="list">
            <article class="item">
                <div class="meta">
                    <span class="badge">Script tag</span>
                </div>
                <p>Submit content with <code>&lt;script&gt;</code> and confirm the API reports a <code>script-tag</code> risk flag.</p>
            </article>
            <article class="item">
                <div class="meta">
                    <span class="badge">Inline event</span>
                </div>
                <p>Try an image tag with <code>onerror=</code> and read how the service detects inline event handlers.</p>
            </article>
            <article class="item">
                <div class="meta">
                    <span class="badge">Blade rule</span>
                </div>
                <p>Read the view and compare escaped <code>@{{ $value }}</code> output with unsafe raw HTML output.</p>
            </article>
        </div>
    </section>

    <script>
        const escapePreviewForm = document.querySelector('#escapePreviewForm');
        const escapePreviewStatus = document.querySelector('#escapePreviewStatus');
        const escapePreviewOutput = document.querySelector('#escapePreviewOutput');

        escapePreviewForm.addEventListener('submit', async (event) => {
            event.preventDefault();

            const payload = Object.fromEntries(new FormData(escapePreviewForm).entries());

            escapePreviewStatus.textContent = 'Running POST /api/practice/security-escape-preview...';
            escapePreviewOutput.textContent = JSON.stringify(payload, null, 2);

            try {
                const response = await fetch('{{ route('api.practice.security-escape-preview.store') }}', {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(payload),
                });
                const body = await response.json();

                escapePreviewStatus.textContent = `HTTP ${response.status}`;
                escapePreviewOutput.textContent = JSON.stringify(body, null, 2);
            } catch (error) {
                escapePreviewStatus.textContent = 'Request failed';
                escapePreviewOutput.textContent = error instanceof Error ? error.message : 'Unknown request error';
            }
        });
    </script>
@endsection
