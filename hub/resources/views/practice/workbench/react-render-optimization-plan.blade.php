@extends('learning.layout', ['title' => 'React Render Optimization Workbench'])

@section('content')
    <section class="hero">
        <span class="badge">Runnable workbench</span>
        <h1>Optimize React re-renders with measurement, not superstition.</h1>
        <p>
            Practice when to use React.memo, useMemo, and useCallback, when to move state, and when large lists need virtualization instead.
        </p>
    </section>

    <section class="section">
        <div class="grid">
            <article class="panel">
                <h2>Render Input</h2>
                <form id="reactRenderForm">
                    <label>Scenario preset
                        <select id="reactRenderPreset" style="margin-top: 8px;">
                            <option value="prop-churn" selected>Prop churn in search panel</option>
                            <option value="large-list">Large table with slow commits</option>
                            <option value="context-update">Context update touches too much UI</option>
                            <option value="expensive-calculation">Expensive derived calculation</option>
                        </select>
                    </label>
                    <label>Component name <input name="component_name" value="Customer Search Panel"></label>
                    <label style="margin-top: 12px;">Component type
                        <select name="component_type">
                            <option value="search-panel" selected>search-panel</option>
                            <option value="table">table</option>
                            <option value="form">form</option>
                            <option value="dashboard">dashboard</option>
                            <option value="list-item">list-item</option>
                        </select>
                    </label>
                    <label style="margin-top: 12px;">Render issue
                        <select name="render_issue">
                            <option value="prop-churn" selected>prop-churn</option>
                            <option value="large-list">large-list</option>
                            <option value="expensive-calculation">expensive-calculation</option>
                            <option value="context-update">context-update</option>
                            <option value="unknown">unknown</option>
                        </select>
                    </label>
                    <label style="margin-top: 12px;">State shape
                        <select name="state_shape">
                            <option value="local-state">local-state</option>
                            <option value="parent-state" selected>parent-state</option>
                            <option value="context-state">context-state</option>
                            <option value="global-state">global-state</option>
                        </select>
                    </label>
                    <label style="margin-top: 12px;">List size <input name="list_size" type="number" min="1" max="10000" value="250"></label>
                    <label style="margin-top: 12px;">Profiler signal
                        <select name="profiler_signal">
                            <option value="prop-churn" selected>prop-churn</option>
                            <option value="slow-commit">slow-commit</option>
                            <option value="many-children">many-children</option>
                            <option value="not-measured">not-measured</option>
                        </select>
                    </label>
                    <button class="button primary" type="submit" style="margin-top: 14px;">Plan optimization</button>
                    <button class="button" id="copyReactRenderPacket" type="button" style="margin-top: 14px;">Copy review packet</button>
                </form>
            </article>

            <article class="panel">
                <h2>Optimization Plan</h2>
                <p class="muted" id="reactRenderStatus">Submit input to generate a React render optimization plan.</p>
                <pre class="raw-json"><code id="reactRenderOutput">POST /api/practice/react-render-optimization-plan</code></pre>
            </article>

            <article class="panel">
                <h2>Summary</h2>
                <div id="reactRenderSummary" class="list">
                    <p class="muted">Run the planner to see memoization decisions, anti-patterns, profiler checks, and code examples.</p>
                </div>
            </article>
        </div>
    </section>

    <script>
        const reactRenderForm = document.querySelector('#reactRenderForm');
        const reactRenderStatus = document.querySelector('#reactRenderStatus');
        const reactRenderOutput = document.querySelector('#reactRenderOutput');
        const reactRenderSummary = document.querySelector('#reactRenderSummary');
        const reactRenderPreset = document.querySelector('#reactRenderPreset');
        const copyReactRenderPacket = document.querySelector('#copyReactRenderPacket');
        let lastReactRenderPacket = '';

        const reactRenderPresets = {
            'prop-churn': {
                component_name: 'Customer Search Panel',
                component_type: 'search-panel',
                render_issue: 'prop-churn',
                state_shape: 'parent-state',
                list_size: 250,
                profiler_signal: 'prop-churn',
            },
            'large-list': {
                component_name: 'Audit Table',
                component_type: 'table',
                render_issue: 'large-list',
                state_shape: 'global-state',
                list_size: 2000,
                profiler_signal: 'slow-commit',
            },
            'context-update': {
                component_name: 'Notification Dashboard',
                component_type: 'dashboard',
                render_issue: 'context-update',
                state_shape: 'context-state',
                list_size: 80,
                profiler_signal: 'many-children',
            },
            'expensive-calculation': {
                component_name: 'Pricing Form',
                component_type: 'form',
                render_issue: 'expensive-calculation',
                state_shape: 'local-state',
                list_size: 40,
                profiler_signal: 'slow-commit',
            },
        };

        function escapeReactRenderHtml(value) {
            return String(value ?? '')
                .replaceAll('&', '&amp;')
                .replaceAll('<', '&lt;')
                .replaceAll('>', '&gt;')
                .replaceAll('"', '&quot;')
                .replaceAll("'", '&#039;');
        }

        function renderReactRenderSummary(data) {
            lastReactRenderPacket = String(data.review_packet_markdown || '');
            const plan = data.optimization_plan.map((item) => `<li><strong>${escapeReactRenderHtml(item.tool)}</strong>: ${escapeReactRenderHtml(item.when_to_use)}</li>`).join('');
            const matrix = data.tool_decision_matrix.map((item) => `<li><strong>${escapeReactRenderHtml(item.tool)}</strong> (${escapeReactRenderHtml(item.fit)} ${escapeReactRenderHtml(item.score)}/100): ${escapeReactRenderHtml(item.use_when)} <span class="muted">Avoid: ${escapeReactRenderHtml(item.avoid_when)}</span></li>`).join('');
            const checks = data.profiler_checklist.map((item) => `<li>${escapeReactRenderHtml(item)}</li>`).join('');
            const anti = data.anti_patterns.map((item) => `<li>${escapeReactRenderHtml(item)}</li>`).join('');
            const steps = data.implementation_steps.map((item) => `<li><strong>${escapeReactRenderHtml(item.step)}</strong>: ${escapeReactRenderHtml(item.action)} <span class="muted">Verify: ${escapeReactRenderHtml(item.verify)}</span></li>`).join('');
            const before = data.measurement_template.before.map((item) => `<li>${escapeReactRenderHtml(item)}</li>`).join('');
            const after = data.measurement_template.after.map((item) => `<li>${escapeReactRenderHtml(item)}</li>`).join('');
            const dependencies = data.dependency_review.map((item) => `<li><strong>${escapeReactRenderHtml(item.target)}</strong>: ${escapeReactRenderHtml(item.check)} <span class="muted">Risk: ${escapeReactRenderHtml(item.failure_mode)}</span></li>`).join('');
            const regressions = data.regression_checks.map((item) => `<li><strong>${escapeReactRenderHtml(item.name)}</strong>: ${escapeReactRenderHtml(item.check)} <span class="muted">${escapeReactRenderHtml(item.purpose)}</span></li>`).join('');

            reactRenderSummary.innerHTML = `
                <div class="item">
                    <div class="meta">
                        <span class="badge pending">${escapeReactRenderHtml(data.risk_level)} risk</span>
                        <span class="badge done">${escapeReactRenderHtml(data.readiness_score.label)} ${escapeReactRenderHtml(data.readiness_score.score)}/100</span>
                        <span class="badge">${escapeReactRenderHtml(data.component)}</span>
                    </div>
                    <h3>${escapeReactRenderHtml(data.recommendation)}</h3>
                    <p>${escapeReactRenderHtml(data.interview_answer)}</p>
                </div>
                <div class="item"><h3>Tool Decision Matrix</h3><ul>${matrix}</ul></div>
                <div class="item"><h3>Optimization Plan</h3><ul>${plan}</ul></div>
                <div class="item"><h3>Readiness Score</h3><p><strong>${escapeReactRenderHtml(data.readiness_score.label)} ${escapeReactRenderHtml(data.readiness_score.score)}/100</strong></p><pre class="raw-json"><code>${escapeReactRenderHtml(JSON.stringify(data.readiness_score, null, 2))}</code></pre></div>
                <div class="item"><h3>Implementation Steps</h3><ul>${steps}</ul></div>
                <div class="item"><h3>Profiler Checklist</h3><ul>${checks}</ul></div>
                <div class="item"><h3>Measurement Template</h3><p><strong>Before</strong></p><ul>${before}</ul><p><strong>After</strong></p><ul>${after}</ul><p class="muted">${escapeReactRenderHtml(data.measurement_template.pass_condition)}</p></div>
                <div class="item"><h3>Dependency Review</h3><ul>${dependencies}</ul></div>
                <div class="item"><h3>Regression Checks</h3><ul>${regressions}</ul></div>
                <div class="item"><h3>Anti-patterns</h3><ul>${anti}</ul></div>
                <div class="item"><h3>Pull Request Note</h3><pre class="raw-json"><code>${escapeReactRenderHtml(data.pull_request_note)}</code></pre></div>
                <div class="item"><h3>Review Packet Markdown</h3><pre class="raw-json"><code>${escapeReactRenderHtml(data.review_packet_markdown)}</code></pre></div>
                <div class="item"><h3>Code Examples</h3><pre class="raw-json"><code>${escapeReactRenderHtml(JSON.stringify(data.code_examples, null, 2))}</code></pre></div>
            `;
        }

        function applyReactRenderPreset(name) {
            const preset = reactRenderPresets[name] || reactRenderPresets['prop-churn'];

            for (const [key, value] of Object.entries(preset)) {
                const field = reactRenderForm.elements.namedItem(key);

                if (field) {
                    field.value = String(value);
                }
            }
        }

        reactRenderPreset.addEventListener('change', (event) => {
            applyReactRenderPreset(event.target.value);
        });

        copyReactRenderPacket.addEventListener('click', async () => {
            if (!lastReactRenderPacket) {
                reactRenderStatus.textContent = 'Run the planner before copying a review packet.';

                return;
            }

            try {
                await navigator.clipboard.writeText(lastReactRenderPacket);
                copyReactRenderPacket.textContent = 'Copied review packet';
            } catch (error) {
                reactRenderStatus.textContent = 'Copy failed';
            } finally {
                setTimeout(() => {
                    copyReactRenderPacket.textContent = 'Copy review packet';
                }, 1400);
            }
        });

        reactRenderForm.addEventListener('submit', async (event) => {
            event.preventDefault();

            const formData = new FormData(reactRenderForm);
            const payload = {
                component_name: String(formData.get('component_name') || ''),
                component_type: String(formData.get('component_type') || 'search-panel'),
                render_issue: String(formData.get('render_issue') || 'prop-churn'),
                state_shape: String(formData.get('state_shape') || 'parent-state'),
                list_size: Number(formData.get('list_size') || 1),
                profiler_signal: String(formData.get('profiler_signal') || 'prop-churn'),
            };

            reactRenderStatus.textContent = 'Running POST /api/practice/react-render-optimization-plan...';
            reactRenderOutput.textContent = JSON.stringify(payload, null, 2);

            try {
                const response = await fetch('{{ route('api.practice.react-render-optimization-plan.store') }}', {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(payload),
                });
                const body = await response.json();

                reactRenderStatus.textContent = `HTTP ${response.status}`;
                reactRenderOutput.textContent = JSON.stringify(body, null, 2);

                if (body.data) {
                    renderReactRenderSummary(body.data);
                }
            } catch (error) {
                reactRenderStatus.textContent = 'Request failed';
                reactRenderOutput.textContent = error instanceof Error ? error.message : 'Unknown request error';
                reactRenderSummary.innerHTML = '<p class="muted">No summary available.</p>';
            }
        });
    </script>
@endsection
