@extends('learning.layout', ['title' => 'HTTP Request Flow Workbench'])

@section('content')
    <section class="hero">
        <span class="badge">Runnable workbench</span>
        <h1>Trace a Laravel request through real app layers.</h1>
        <p>
            This workbench turns request-flow theory into code you can read:
            route file, Form Request, controller, service, JSON response, and feature test.
        </p>
    </section>

    <section class="section">
        <div class="grid">
            <article class="panel">
                <h2>Trace Request</h2>
                <form id="httpFlowForm">
                    <label>
                        Method
                        <select name="method">
                            <option value="GET">GET</option>
                            <option value="POST" selected>POST</option>
                            <option value="PUT">PUT</option>
                            <option value="PATCH">PATCH</option>
                            <option value="DELETE">DELETE</option>
                        </select>
                    </label>

                    <label style="margin-top: 12px;">
                        Path
                        <input name="path" value="/api/practice/topics" autocomplete="off">
                    </label>

                    <label style="margin-top: 12px;">
                        Accept
                        <select name="accept">
                            <option value="application/json" selected>application/json</option>
                            <option value="text/html">text/html</option>
                        </select>
                    </label>

                    <button class="button primary" type="submit" style="margin-top: 14px;">Trace request</button>
                </form>
            </article>

            <article class="panel">
                <h2>Trace Output</h2>
                <p class="muted" id="httpFlowStatus">Submit the form to trace the request.</p>
                <pre class="raw-json"><code id="httpFlowOutput">POST /api/practice/http-request-flow</code></pre>
            </article>

            <article class="panel">
                <h2>Files To Read</h2>
                <ul>
                    <li><code>routes/web/workbench.php</code></li>
                    <li><code>routes/api/practice-actions.php</code></li>
                    <li><code>app/Http/Requests/Api/TraceHttpRequestFlowRequest.php</code></li>
                    <li><code>app/Http/Controllers/Api/HttpRequestFlowTraceController.php</code></li>
                    <li><code>app/Services/Practice/HttpRequestFlowTracerService.php</code></li>
                    <li><code>tests/Feature/HttpRequestFlowWorkbenchTest.php</code></li>
                </ul>
            </article>
        </div>
    </section>

    <section class="section">
        <h2>What To Change Next</h2>
        <div class="list">
            <article class="item">
                <div class="meta">
                    <span class="badge">Route</span>
                </div>
                <p>Add one route comment, run <code>php artisan route:list --path=workbench</code>, then confirm the route name stays stable.</p>
            </article>
            <article class="item">
                <div class="meta">
                    <span class="badge">Request</span>
                </div>
                <p>Change the path validation rule, submit invalid input, and inspect the 422 error shape.</p>
            </article>
            <article class="item">
                <div class="meta">
                    <span class="badge">Service</span>
                </div>
                <p>Add one trace step in the service and update the feature test to prove the output changed.</p>
            </article>
        </div>
    </section>

    <script>
        const httpFlowForm = document.querySelector('#httpFlowForm');
        const httpFlowStatus = document.querySelector('#httpFlowStatus');
        const httpFlowOutput = document.querySelector('#httpFlowOutput');

        httpFlowForm.addEventListener('submit', async (event) => {
            event.preventDefault();

            const payload = Object.fromEntries(new FormData(httpFlowForm).entries());

            httpFlowStatus.textContent = 'Running POST /api/practice/http-request-flow...';
            httpFlowOutput.textContent = JSON.stringify(payload, null, 2);

            try {
                const response = await fetch('{{ route('api.practice.http-request-flow.store') }}', {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(payload),
                });
                const body = await response.json();

                httpFlowStatus.textContent = `HTTP ${response.status}`;
                httpFlowOutput.textContent = JSON.stringify(body, null, 2);
            } catch (error) {
                httpFlowStatus.textContent = 'Request failed';
                httpFlowOutput.textContent = error instanceof Error ? error.message : 'Unknown request error';
            }
        });
    </script>
@endsection
