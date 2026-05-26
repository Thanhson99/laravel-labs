@extends('learning.layout', ['title' => 'Cache Strategy Plan Workbench'])

@section('content')
    <section class="hero">
        <span class="badge">Runnable workbench</span>
        <h1>Plan cache keys, TTL, and invalidation before caching.</h1>
        <p>
            This workbench helps you decide what to cache, how long it may stay stale,
            and which write path owns invalidation.
        </p>
    </section>

    <section class="section">
        <div class="grid">
            <article class="panel">
                <h2>Cache Input</h2>
                <form id="cacheStrategyPlanForm">
                    <label>
                        Resource name
                        <input name="resource_name" value="Practice dashboard" autocomplete="off">
                    </label>
                    <label style="margin-top: 12px;">
                        Scope
                        <input name="scope" value="user 42" autocomplete="off">
                    </label>
                    <label style="margin-top: 12px;">
                        TTL minutes
                        <input name="ttl_minutes" type="number" min="1" max="1440" value="15">
                    </label>
                    <label style="margin-top: 12px;">
                        Invalidation trigger
                        <input name="invalidation_trigger" value="Clear when practice progress is updated" autocomplete="off">
                    </label>
                    <button class="button primary" type="submit" style="margin-top: 14px;">Plan cache strategy</button>
                </form>
            </article>

            <article class="panel">
                <h2>Cache Plan</h2>
                <p class="muted" id="cacheStrategyPlanStatus">Submit input to build the cache strategy.</p>
                <pre class="raw-json"><code id="cacheStrategyPlanOutput">POST /api/practice/cache-strategy-plan</code></pre>
            </article>

            <article class="panel">
                <h2>Files To Read</h2>
                <ul>
                    <li><code>routes/api/practice-actions.php</code></li>
                    <li><code>app/Http/Requests/Api/PlanCacheStrategyRequest.php</code></li>
                    <li><code>app/Http/Controllers/Api/CacheStrategyPlanController.php</code></li>
                    <li><code>app/Services/Practice/CacheStrategyPlanService.php</code></li>
                    <li><code>config/cache.php</code></li>
                    <li><code>tests/Feature/CacheStrategyPlanWorkbenchTest.php</code></li>
                </ul>
            </article>
        </div>
    </section>

    <script>
        const cacheStrategyPlanForm = document.querySelector('#cacheStrategyPlanForm');
        const cacheStrategyPlanStatus = document.querySelector('#cacheStrategyPlanStatus');
        const cacheStrategyPlanOutput = document.querySelector('#cacheStrategyPlanOutput');

        cacheStrategyPlanForm.addEventListener('submit', async (event) => {
            event.preventDefault();
            const formData = new FormData(cacheStrategyPlanForm);
            const payload = {
                resource_name: String(formData.get('resource_name') || ''),
                scope: String(formData.get('scope') || ''),
                ttl_minutes: Number(formData.get('ttl_minutes') || 1),
                invalidation_trigger: String(formData.get('invalidation_trigger') || ''),
            };

            cacheStrategyPlanStatus.textContent = 'Running POST /api/practice/cache-strategy-plan...';
            cacheStrategyPlanOutput.textContent = JSON.stringify(payload, null, 2);

            try {
                const response = await fetch('{{ route('api.practice.cache-strategy-plan.store') }}', {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(payload),
                });
                const body = await response.json();

                cacheStrategyPlanStatus.textContent = `HTTP ${response.status}`;
                cacheStrategyPlanOutput.textContent = JSON.stringify(body, null, 2);
            } catch (error) {
                cacheStrategyPlanStatus.textContent = 'Request failed';
                cacheStrategyPlanOutput.textContent = error instanceof Error ? error.message : 'Unknown request error';
            }
        });
    </script>
@endsection
