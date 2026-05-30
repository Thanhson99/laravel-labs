@extends('learning.layout', ['title' => 'Reverse Proxy Failure Plan Workbench'])

@section('content')
    <section class="hero">
        <span class="badge">Runnable workbench</span>
        <h1>Plan reverse-proxy failure modes before origins disappear.</h1>
        <p>
            This workbench turns edge outage lessons into an operational plan: request path mapping, config validation, blast-radius controls,
            staged rollout, fail-small behavior, observability, incident triage, and an interview answer.
        </p>
    </section>

    <section class="section">
        <div class="grid">
            <article class="panel">
                <h2>Proxy Failure Input</h2>
                <label>
                    Scenario preset
                    <select id="reverseProxyPreset">
                        <option value="edge" selected>Edge feature file</option>
                        <option value="waf">WAF rule rollout</option>
                        <option value="nginx">Internal Nginx deploy</option>
                    </select>
                </label>

                <form id="reverseProxyPlanForm">
                    <label style="margin-top: 12px;">
                        Service name
                        <input name="service_name" value="Public Checkout Edge" autocomplete="off">
                    </label>

                    <label style="margin-top: 12px;">
                        Proxy layer
                        <select name="proxy_layer">
                            <option value="edge-cdn" selected>edge-cdn</option>
                            <option value="regional-proxy">regional-proxy</option>
                            <option value="internal-nginx">internal-nginx</option>
                        </select>
                    </label>

                    <label style="margin-top: 12px;">
                        Change type
                        <select name="change_type">
                            <option value="feature-file" selected>feature-file</option>
                            <option value="config-file">config-file</option>
                            <option value="routing-rule">routing-rule</option>
                            <option value="waf-rule">waf-rule</option>
                            <option value="deploy">deploy</option>
                        </select>
                    </label>

                    <label style="margin-top: 12px;">
                        Origin count
                        <input name="origin_count" type="number" min="1" max="500" value="120">
                    </label>

                    <label style="margin-top: 12px;">
                        Rollout strategy
                        <select name="rollout_strategy">
                            <option value="global" selected>global</option>
                            <option value="staged">staged</option>
                            <option value="canary">canary</option>
                        </select>
                    </label>

                    <label style="margin-top: 12px;">
                        <input type="checkbox" name="has_health_gate" value="1">
                        Automated health gate
                    </label>

                    <label style="margin-top: 12px;">
                        Failure behavior
                        <select name="fail_behavior">
                            <option value="fail_closed" selected>fail_closed</option>
                            <option value="fail_open">fail_open</option>
                            <option value="serve_stale">serve_stale</option>
                            <option value="bypass_optional_module">bypass_optional_module</option>
                        </select>
                    </label>

                    <label style="margin-top: 12px;">
                        Observed failure
                        <select name="observed_failure">
                            <option value="http_500" selected>http_500</option>
                            <option value="timeouts">timeouts</option>
                            <option value="tls_loop">tls_loop</option>
                            <option value="bad_routing">bad_routing</option>
                        </select>
                    </label>

                    <button class="button primary" type="submit" style="margin-top: 14px;">Plan proxy failure</button>
                </form>
            </article>

            <article class="panel">
                <h2>Failure Plan</h2>
                <p class="muted" id="reverseProxyPlanStatus">Submit input to generate blast-radius controls, rollout gates, observability, incident triage, and an interview answer.</p>
                <pre class="raw-json"><code id="reverseProxyPlanOutput">POST /api/practice/reverse-proxy-failure-plan</code></pre>
            </article>

            <article class="panel">
                <h2>Executive Summary</h2>
                <div id="reverseProxyPlanSummary" class="list">
                    <p class="muted">Run the planner to see risk, first action, verification focus, and why origin health is not enough.</p>
                </div>
            </article>

            <article class="panel">
                <h2>Incident Memo</h2>
                <button class="button" id="copyReverseProxyMemo" type="button">Copy memo</button>
                <pre class="raw-json" style="margin-top: 12px;"><code id="reverseProxyPlanMemo">Run the planner to generate a markdown incident memo.</code></pre>
            </article>

            <article class="panel">
                <h2>Files To Read</h2>
                <ul>
                    <li><code>data/interview/devops.en.json</code></li>
                    <li><code>data/interview/devops.vi.json</code></li>
                    <li><code>routes/api/practice-actions.php</code></li>
                    <li><code>app/Http/Requests/Api/PlanReverseProxyFailureRequest.php</code></li>
                    <li><code>app/Services/Practice/ReverseProxyFailurePlanService.php</code></li>
                    <li><code>tests/Feature/ReverseProxyFailurePlanWorkbenchTest.php</code></li>
                </ul>
            </article>
        </div>
    </section>

    <script>
        const reverseProxyPlanForm = document.querySelector('#reverseProxyPlanForm');
        const reverseProxyPreset = document.querySelector('#reverseProxyPreset');
        const reverseProxyPlanStatus = document.querySelector('#reverseProxyPlanStatus');
        const reverseProxyPlanOutput = document.querySelector('#reverseProxyPlanOutput');
        const reverseProxyPlanSummary = document.querySelector('#reverseProxyPlanSummary');
        const reverseProxyPlanMemo = document.querySelector('#reverseProxyPlanMemo');
        const copyReverseProxyMemo = document.querySelector('#copyReverseProxyMemo');
        const reverseProxyPresets = {
            edge: {
                service_name: 'Public Checkout Edge',
                proxy_layer: 'edge-cdn',
                change_type: 'feature-file',
                origin_count: 120,
                rollout_strategy: 'global',
                has_health_gate: false,
                fail_behavior: 'fail_closed',
                observed_failure: 'http_500',
            },
            waf: {
                service_name: 'Account Security Edge',
                proxy_layer: 'edge-cdn',
                change_type: 'waf-rule',
                origin_count: 40,
                rollout_strategy: 'canary',
                has_health_gate: true,
                fail_behavior: 'fail_closed',
                observed_failure: 'bad_routing',
            },
            nginx: {
                service_name: 'Internal Admin Proxy',
                proxy_layer: 'internal-nginx',
                change_type: 'deploy',
                origin_count: 4,
                rollout_strategy: 'staged',
                has_health_gate: true,
                fail_behavior: 'bypass_optional_module',
                observed_failure: 'timeouts',
            },
        };

        function applyReverseProxyPreset(name) {
            const preset = reverseProxyPresets[name] || reverseProxyPresets.edge;

            Object.entries(preset).forEach(([field, value]) => {
                const input = reverseProxyPlanForm.elements[field];

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

        reverseProxyPreset.addEventListener('change', (event) => {
            applyReverseProxyPreset(event.target.value);
        });

        applyReverseProxyPreset(reverseProxyPreset.value);

        function escapeReverseProxyHtml(value) {
            return String(value ?? '')
                .replaceAll('&', '&amp;')
                .replaceAll('<', '&lt;')
                .replaceAll('>', '&gt;')
                .replaceAll('"', '&quot;')
                .replaceAll("'", '&#039;');
        }

        function renderReverseProxySummary(data) {
            const summary = data.executive_summary;
            const readiness = data.readiness_score;
            const riskSignals = data.risk_signals
                .slice(0, 3)
                .map((signal) => `<li>${escapeReverseProxyHtml(signal)}</li>`)
                .join('');
            const controls = data.blast_radius_controls
                .slice(0, 3)
                .map((control) => `<li>${escapeReverseProxyHtml(control)}</li>`)
                .join('');
            const decisions = data.decision_tree
                .slice(0, 3)
                .map((decision) => `<li><strong>If</strong> ${escapeReverseProxyHtml(decision.if)}, <strong>then</strong> ${escapeReverseProxyHtml(decision.then)}.</li>`)
                .join('');
            const scenarioChecks = data.scenario_playbook.validation_focus
                .slice(0, 3)
                .map((check) => `<li>${escapeReverseProxyHtml(check)}</li>`)
                .join('');
            const simulationSteps = data.simulation_plan
                .slice(0, 3)
                .map((step) => `<li><strong>${escapeReverseProxyHtml(step.step)}</strong>: ${escapeReverseProxyHtml(step.expected_signal)}</li>`)
                .join('');
            const timeline = data.incident_timeline
                .slice(0, 3)
                .map((stage) => `<li><strong>${escapeReverseProxyHtml(stage.window)}</strong>: ${escapeReverseProxyHtml(stage.goal)}</li>`)
                .join('');
            const evidence = data.evidence_pack
                .slice(0, 2)
                .map((item) => `<li><strong>${escapeReverseProxyHtml(item.owner)}</strong>: ${escapeReverseProxyHtml(item.artifact)}</li>`)
                .join('');

            reverseProxyPlanSummary.innerHTML = `
                <div class="item">
                    <div class="meta">
                        <span class="badge pending">${escapeReverseProxyHtml(data.risk_level)} risk</span>
                        <span class="badge done">${escapeReverseProxyHtml(readiness.label)} ${escapeReverseProxyHtml(readiness.score)}/100</span>
                        <span class="badge">${escapeReverseProxyHtml(data.service)}</span>
                    </div>
                    <h3>${escapeReverseProxyHtml(summary.headline)}</h3>
                    <p class="muted">${escapeReverseProxyHtml(summary.why_it_matters)}</p>
                </div>
                <div class="item">
                    <h3>First Action</h3>
                    <p>${escapeReverseProxyHtml(summary.first_action)}</p>
                    <p class="muted">${escapeReverseProxyHtml(readiness.next_actions[0] ?? summary.verification_focus)}</p>
                </div>
                <div class="item">
                    <h3>Risk Signals</h3>
                    <ul>${riskSignals}</ul>
                </div>
                <div class="item">
                    <h3>Blast-Radius Controls</h3>
                    <ul>${controls}</ul>
                </div>
                <div class="item">
                    <h3>Decision Tree</h3>
                    <ul>${decisions}</ul>
                </div>
                <div class="item">
                    <h3>Scenario Playbook</h3>
                    <p><strong>${escapeReverseProxyHtml(data.scenario_playbook.change_type)}</strong>: ${escapeReverseProxyHtml(data.scenario_playbook.rollback_trigger)}</p>
                    <ul>${scenarioChecks}</ul>
                </div>
                <div class="item">
                    <h3>Simulation Plan</h3>
                    <ul>${simulationSteps}</ul>
                    <p class="muted">${escapeReverseProxyHtml(data.chaos_drill.objective)}</p>
                </div>
                <div class="item">
                    <h3>Health Gate</h3>
                    <p><strong>${escapeReverseProxyHtml(data.health_gate_policy.mode)}</strong>: ${escapeReverseProxyHtml(data.health_gate_policy.stop_conditions[0])}</p>
                    <p class="muted">${escapeReverseProxyHtml(data.alert_rules[0].action)}</p>
                </div>
                <div class="item">
                    <h3>Ownership</h3>
                    <p><strong>${escapeReverseProxyHtml(data.ownership_matrix[0].owner)}</strong>: ${escapeReverseProxyHtml(data.ownership_matrix[0].responsibility)}</p>
                    <p class="muted">${escapeReverseProxyHtml(data.postmortem_actions[0].action)}</p>
                </div>
                <div class="item">
                    <h3>Incident Timeline</h3>
                    <ul>${timeline}</ul>
                </div>
                <div class="item">
                    <h3>Evidence Pack</h3>
                    <ul>${evidence}</ul>
                </div>
            `;
        }

        reverseProxyPlanForm.addEventListener('submit', async (event) => {
            event.preventDefault();

            const formData = new FormData(reverseProxyPlanForm);
            const payload = {
                service_name: String(formData.get('service_name') || ''),
                proxy_layer: String(formData.get('proxy_layer') || 'edge-cdn'),
                change_type: String(formData.get('change_type') || 'feature-file'),
                origin_count: Number(formData.get('origin_count') || 1),
                rollout_strategy: String(formData.get('rollout_strategy') || 'global'),
                has_health_gate: formData.has('has_health_gate'),
                fail_behavior: String(formData.get('fail_behavior') || 'fail_closed'),
                observed_failure: String(formData.get('observed_failure') || 'http_500'),
            };

            reverseProxyPlanStatus.textContent = 'Running POST /api/practice/reverse-proxy-failure-plan...';
            reverseProxyPlanOutput.textContent = JSON.stringify(payload, null, 2);

            try {
                const response = await fetch('{{ route('api.practice.reverse-proxy-failure-plan.store') }}', {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(payload),
                });
                const body = await response.json();

                reverseProxyPlanStatus.textContent = `HTTP ${response.status}`;
                reverseProxyPlanOutput.textContent = JSON.stringify(body, null, 2);
                reverseProxyPlanMemo.textContent = body.data?.incident_memo_markdown ?? 'No memo returned.';

                if (body.data) {
                    renderReverseProxySummary(body.data);
                }
            } catch (error) {
                reverseProxyPlanStatus.textContent = 'Request failed';
                reverseProxyPlanOutput.textContent = error instanceof Error ? error.message : 'Unknown request error';
                reverseProxyPlanMemo.textContent = 'No memo available.';
                reverseProxyPlanSummary.innerHTML = '<p class="muted">No summary available.</p>';
            }
        });

        copyReverseProxyMemo.addEventListener('click', async () => {
            try {
                await navigator.clipboard.writeText(reverseProxyPlanMemo.textContent);
                copyReverseProxyMemo.textContent = 'Copied memo';
            } catch (error) {
                copyReverseProxyMemo.textContent = 'Copy failed';
            }

            window.setTimeout(() => {
                copyReverseProxyMemo.textContent = 'Copy memo';
            }, 1600);
        });
    </script>
@endsection
