@extends('learning.layout', ['title' => 'Load Balancer Plan Workbench'])

@section('content')
    <section class="hero">
        <span class="badge">Runnable workbench</span>
        <h1>Choose a load-balancer algorithm for real traffic.</h1>
        <p>
            This workbench turns system-design theory into a practical plan with four common algorithms,
            operational risk assessment, expected capacity share, Nginx-style upstream config, config review checks, observability metrics, incident steps, traffic simulations, and an interview rubric.
        </p>
    </section>

    <section class="section">
        <div class="grid">
            <article class="panel">
                <h2>Balancer Input</h2>
                <label>
                    Scenario preset
                    <select id="loadBalancerPreset">
                        <option value="api" selected>Public API</option>
                        <option value="reports">Report exports</option>
                        <option value="legacy">Legacy session app</option>
                    </select>
                </label>

                <form id="loadBalancerPlanForm">
                    <label style="margin-top: 12px;">
                        Service name
                        <input name="service_name" value="Checkout API" autocomplete="off">
                    </label>

                    <label style="margin-top: 12px;">
                        Traffic pattern
                        <select name="traffic_pattern">
                            <option value="even" selected>even traffic</option>
                            <option value="heterogeneous">different upstream capacity</option>
                            <option value="bursty">bursty or long-running requests</option>
                            <option value="session-affinity">session affinity required</option>
                        </select>
                    </label>

                    <label style="margin-top: 12px;">
                        Algorithm
                        <select name="algorithm">
                            <option value="auto" selected>auto</option>
                            <option value="round_robin">round_robin</option>
                            <option value="weighted_round_robin">weighted_round_robin</option>
                            <option value="least_connections">least_connections</option>
                            <option value="ip_hash">ip_hash</option>
                        </select>
                    </label>

                    <label style="margin-top: 12px;">
                        Upstream count
                        <input name="upstream_count" type="number" min="2" max="8" value="3">
                    </label>

                    <label style="margin-top: 12px;">
                        <input type="checkbox" name="has_sticky_sessions" value="1">
                        Sticky sessions required
                    </label>

                    <label style="margin-top: 12px;">
                        Health check path
                        <input name="health_check_path" value="/up" autocomplete="off">
                    </label>

                    <button class="button primary" type="submit" style="margin-top: 14px;">Plan load balancing</button>
                </form>
            </article>

            <article class="panel">
                <h2>Load Balancer Plan</h2>
                <p class="muted" id="loadBalancerPlanStatus">Submit input to compare four algorithms and generate risk, capacity share, config review checks, metrics, incident steps, simulations, and a structured interview answer.</p>
                <pre class="raw-json"><code id="loadBalancerPlanOutput">POST /api/practice/load-balancer-plan</code></pre>
            </article>

            <article class="panel">
                <h2>Files To Read</h2>
                <ul>
                    <li><code>data/interview/devops.en.json</code></li>
                    <li><code>data/interview/devops.vi.json</code></li>
                    <li><code>routes/api/practice-actions.php</code></li>
                    <li><code>app/Http/Requests/Api/PlanLoadBalancerRequest.php</code></li>
                    <li><code>app/Services/Practice/LoadBalancerPlanService.php</code></li>
                    <li><code>tests/Feature/LoadBalancerPlanWorkbenchTest.php</code></li>
                </ul>
            </article>
        </div>
    </section>

    <script>
        const loadBalancerPlanForm = document.querySelector('#loadBalancerPlanForm');
        const loadBalancerPreset = document.querySelector('#loadBalancerPreset');
        const loadBalancerPlanStatus = document.querySelector('#loadBalancerPlanStatus');
        const loadBalancerPlanOutput = document.querySelector('#loadBalancerPlanOutput');
        const loadBalancerPresets = {
            api: {
                service_name: 'Checkout API',
                traffic_pattern: 'even',
                algorithm: 'auto',
                upstream_count: 3,
                has_sticky_sessions: false,
                health_check_path: '/up',
            },
            reports: {
                service_name: 'Report Export Worker',
                traffic_pattern: 'bursty',
                algorithm: 'auto',
                upstream_count: 4,
                has_sticky_sessions: false,
                health_check_path: '/health',
            },
            legacy: {
                service_name: 'Legacy Session App',
                traffic_pattern: 'session-affinity',
                algorithm: 'auto',
                upstream_count: 3,
                has_sticky_sessions: true,
                health_check_path: '/up',
            },
        };

        function applyLoadBalancerPreset(name) {
            const preset = loadBalancerPresets[name] || loadBalancerPresets.api;

            Object.entries(preset).forEach(([field, value]) => {
                const input = loadBalancerPlanForm.elements[field];

                if (!input) {
                    return;
                }

                if (input.type === 'checkbox') {
                    input.checked = Boolean(value);
                    return;
                }

                input.value = value;
            });
        }

        loadBalancerPreset.addEventListener('change', (event) => {
            applyLoadBalancerPreset(event.target.value);
        });

        loadBalancerPlanForm.addEventListener('submit', async (event) => {
            event.preventDefault();

            const formData = new FormData(loadBalancerPlanForm);
            const payload = {
                service_name: String(formData.get('service_name') || ''),
                traffic_pattern: String(formData.get('traffic_pattern') || 'even'),
                algorithm: String(formData.get('algorithm') || 'auto'),
                upstream_count: Number(formData.get('upstream_count') || 2),
                has_sticky_sessions: formData.has('has_sticky_sessions'),
                health_check_path: String(formData.get('health_check_path') || '/up'),
            };

            loadBalancerPlanStatus.textContent = 'Running POST /api/practice/load-balancer-plan...';
            loadBalancerPlanOutput.textContent = JSON.stringify(payload, null, 2);

            try {
                const response = await fetch('{{ route('api.practice.load-balancer-plan.store') }}', {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(payload),
                });
                const body = await response.json();

                loadBalancerPlanStatus.textContent = `HTTP ${response.status}`;
                loadBalancerPlanOutput.textContent = JSON.stringify(body, null, 2);
            } catch (error) {
                loadBalancerPlanStatus.textContent = 'Request failed';
                loadBalancerPlanOutput.textContent = error instanceof Error ? error.message : 'Unknown request error';
            }
        });
    </script>
@endsection
