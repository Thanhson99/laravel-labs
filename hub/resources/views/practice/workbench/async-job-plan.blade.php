@extends('learning.layout', ['title' => 'Async Job Plan Workbench'])

@section('content')
    <section class="hero">
        <span class="badge">Runnable workbench</span>
        <h1>Plan a queue job before running a worker.</h1>
        <p>
            This workbench turns async concepts into readable Laravel code: job name,
            payload key, idempotency key, retry policy, and queue commands.
        </p>
    </section>

    <section class="section">
        <div class="grid">
            <article class="panel">
                <h2>Job Input</h2>
                <form id="asyncJobPlanForm">
                    <label>
                        Job name
                        <input name="job_name" value="Sync External Order" autocomplete="off">
                    </label>

                    <label style="margin-top: 12px;">
                        Payload key
                        <input name="payload_key" value="order 123" autocomplete="off">
                    </label>

                    <label style="margin-top: 12px;">
                        Attempts
                        <input name="attempts" type="number" min="1" max="10" value="3">
                    </label>

                    <label style="margin-top: 12px;">
                        Backoff seconds
                        <input name="backoff_seconds" type="number" min="1" max="3600" value="60">
                    </label>

                    <button class="button primary" type="submit" style="margin-top: 14px;">Plan async job</button>
                </form>
            </article>

            <article class="panel">
                <h2>Job Plan</h2>
                <p class="muted" id="asyncJobPlanStatus">Submit input to build the async plan.</p>
                <pre class="raw-json"><code id="asyncJobPlanOutput">POST /api/practice/async-job-plan</code></pre>
            </article>

            <article class="panel">
                <h2>Files To Read</h2>
                <ul>
                    <li><code>routes/api/practice-actions.php</code></li>
                    <li><code>app/Http/Requests/Api/PlanAsyncJobRequest.php</code></li>
                    <li><code>app/Http/Controllers/Api/AsyncJobPlanController.php</code></li>
                    <li><code>app/Services/Practice/AsyncJobPlanService.php</code></li>
                    <li><code>tests/Feature/AsyncJobPlanWorkbenchTest.php</code></li>
                </ul>
            </article>
        </div>
    </section>

    <section class="section">
        <h2>Practice Variations</h2>
        <div class="list">
            <article class="item">
                <div class="meta">
                    <span class="badge">Idempotency</span>
                </div>
                <p>Change the payload key and inspect how the idempotency key changes.</p>
            </article>
            <article class="item">
                <div class="meta">
                    <span class="badge">Retry</span>
                </div>
                <p>Change attempts and backoff to understand how retry policy should be explicit before using a queue worker.</p>
            </article>
            <article class="item">
                <div class="meta">
                    <span class="badge">Job class</span>
                </div>
                <p>Use the generated <code>php artisan make:job</code> command as the next step after reading this service.</p>
            </article>
        </div>
    </section>

    <script>
        const asyncJobPlanForm = document.querySelector('#asyncJobPlanForm');
        const asyncJobPlanStatus = document.querySelector('#asyncJobPlanStatus');
        const asyncJobPlanOutput = document.querySelector('#asyncJobPlanOutput');

        asyncJobPlanForm.addEventListener('submit', async (event) => {
            event.preventDefault();

            const formData = new FormData(asyncJobPlanForm);
            const payload = {
                job_name: String(formData.get('job_name') || ''),
                payload_key: String(formData.get('payload_key') || ''),
                attempts: Number(formData.get('attempts') || 1),
                backoff_seconds: Number(formData.get('backoff_seconds') || 1),
            };

            asyncJobPlanStatus.textContent = 'Running POST /api/practice/async-job-plan...';
            asyncJobPlanOutput.textContent = JSON.stringify(payload, null, 2);

            try {
                const response = await fetch('{{ route('api.practice.async-job-plan.store') }}', {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(payload),
                });
                const body = await response.json();

                asyncJobPlanStatus.textContent = `HTTP ${response.status}`;
                asyncJobPlanOutput.textContent = JSON.stringify(body, null, 2);
            } catch (error) {
                asyncJobPlanStatus.textContent = 'Request failed';
                asyncJobPlanOutput.textContent = error instanceof Error ? error.message : 'Unknown request error';
            }
        });
    </script>
@endsection
