@extends('learning.layout', ['title' => 'Rate Limit Plan Workbench'])

@section('content')
    <section class="hero">
        <span class="badge">Runnable workbench</span>
        <h1>Plan API throttling before a route becomes expensive or sensitive.</h1>
        <p>
            This workbench turns Laravel rate limiting into a concrete plan: identity key,
            named middleware, retry window, failure response, and tests.
        </p>
    </section>

    <section class="section">
        <div class="grid">
            <article class="panel">
                <h2>Rate Limit Input</h2>
                <form id="rateLimitPlanForm">
                    <label>
                        Endpoint name
                        <input name="endpoint_name" value="Password reset request" autocomplete="off">
                    </label>
                    <label style="margin-top: 12px;">
                        Actor type
                        <select name="actor_type">
                            <option value="ip">ip</option>
                            <option value="user" selected>user</option>
                            <option value="team">team</option>
                            <option value="api-token">api-token</option>
                        </select>
                    </label>
                    <label style="margin-top: 12px;">
                        Max attempts
                        <input name="max_attempts" type="number" min="1" max="1000" value="5">
                    </label>
                    <label style="margin-top: 12px;">
                        Decay minutes
                        <input name="decay_minutes" type="number" min="1" max="1440" value="10">
                    </label>
                    <label style="margin-top: 12px;">
                        Sensitivity
                        <select name="sensitivity">
                            <option value="normal">normal</option>
                            <option value="sensitive" selected>sensitive</option>
                            <option value="bulk">bulk</option>
                        </select>
                    </label>
                    <button class="button primary" type="submit" style="margin-top: 14px;">Plan rate limit</button>
                </form>
            </article>

            <article class="panel">
                <h2>Throttle Plan</h2>
                <p class="muted" id="rateLimitPlanStatus">Submit input to build the throttling plan.</p>
                <pre class="raw-json"><code id="rateLimitPlanOutput">POST /api/practice/rate-limit-plan</code></pre>
            </article>

            <article class="panel">
                <h2>Files To Read</h2>
                <ul>
                    <li><code>routes/api/practice-actions.php</code></li>
                    <li><code>app/Http/Requests/Api/PlanRateLimitRequest.php</code></li>
                    <li><code>app/Http/Controllers/Api/RateLimitPlanController.php</code></li>
                    <li><code>app/Services/Practice/RateLimitPlanService.php</code></li>
                    <li><code>bootstrap/app.php</code></li>
                    <li><code>tests/Feature/RateLimitPlanWorkbenchTest.php</code></li>
                </ul>
            </article>
        </div>
    </section>

    <script>
        const rateLimitPlanForm = document.querySelector('#rateLimitPlanForm');
        const rateLimitPlanStatus = document.querySelector('#rateLimitPlanStatus');
        const rateLimitPlanOutput = document.querySelector('#rateLimitPlanOutput');

        rateLimitPlanForm.addEventListener('submit', async (event) => {
            event.preventDefault();
            const formData = new FormData(rateLimitPlanForm);
            const payload = {
                endpoint_name: String(formData.get('endpoint_name') || ''),
                actor_type: String(formData.get('actor_type') || 'user'),
                max_attempts: Number(formData.get('max_attempts') || 1),
                decay_minutes: Number(formData.get('decay_minutes') || 1),
                sensitivity: String(formData.get('sensitivity') || 'sensitive'),
            };

            rateLimitPlanStatus.textContent = 'Running POST /api/practice/rate-limit-plan...';
            rateLimitPlanOutput.textContent = JSON.stringify(payload, null, 2);

            try {
                const response = await fetch('{{ route('api.practice.rate-limit-plan.store') }}', {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(payload),
                });
                const body = await response.json();

                rateLimitPlanStatus.textContent = `HTTP ${response.status}`;
                rateLimitPlanOutput.textContent = JSON.stringify(body, null, 2);
            } catch (error) {
                rateLimitPlanStatus.textContent = 'Request failed';
                rateLimitPlanOutput.textContent = error instanceof Error ? error.message : 'Unknown request error';
            }
        });
    </script>
@endsection
