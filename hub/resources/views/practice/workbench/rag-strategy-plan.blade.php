@extends('learning.layout', ['title' => 'RAG Strategy Plan Workbench'])

@section('content')
    <section class="hero">
        <span class="badge">Runnable workbench</span>
        <h1>Choose between RAG, Long Context, CAG, and hybrid chatbot context.</h1>
        <p>
            This workbench turns retrieval shape, relationship needs, tool use, freshness,
            risk, answer style, and context strategy into a concrete AI chatbot architecture plan.
        </p>
    </section>

    <section class="section">
        <div class="grid">
            <article class="panel">
                <h2>RAG Context</h2>
                <form id="ragStrategyPlanForm">
                    <label>
                        Scenario preset
                        <select id="ragStrategyPreset">
                            <option value="classicDocs">Classic document Q&A</option>
                            <option value="graphIncidents">Graph incident knowledge</option>
                            <option value="agentOps">Agentic operations assistant</option>
                            <option value="highRiskPolicy">High-risk policy answers</option>
                            <option value="longContextPack">Long context document pack</option>
                            <option value="cagFaq">CAG stable FAQ chatbot</option>
                            <option value="hybridSupport">Hybrid support chatbot</option>
                        </select>
                    </label>
                    <label style="margin-top: 12px;">
                        Context strategy
                        <select name="context_strategy">
                            <option value="auto">auto</option>
                            <option value="rag">rag</option>
                            <option value="long-context">long-context</option>
                            <option value="cag">cag</option>
                            <option value="hybrid">hybrid</option>
                        </select>
                    </label>
                    <label style="margin-top: 12px;">
                        Knowledge shape
                        <select name="knowledge_shape">
                            <option value="documents">documents</option>
                            <option value="entities">entities</option>
                            <option value="workflows">workflows</option>
                        </select>
                    </label>
                    <label style="margin-top: 12px;">
                        Relationship need
                        <select name="relationship_need">
                            <option value="low">low</option>
                            <option value="medium">medium</option>
                            <option value="high">high</option>
                        </select>
                    </label>
                    <label style="margin-top: 12px;">
                        Tool use
                        <select name="tool_use">
                            <option value="none">none</option>
                            <option value="retrieval-tools">retrieval-tools</option>
                            <option value="multi-step-agent">multi-step-agent</option>
                        </select>
                    </label>
                    <label style="margin-top: 12px;">
                        Freshness
                        <select name="freshness">
                            <option value="static">static</option>
                            <option value="periodic">periodic</option>
                            <option value="real-time">real-time</option>
                        </select>
                    </label>
                    <label style="margin-top: 12px;">
                        Risk level
                        <select name="risk_level">
                            <option value="low">low</option>
                            <option value="medium">medium</option>
                            <option value="high">high</option>
                        </select>
                    </label>
                    <label style="margin-top: 12px;">
                        Answer style
                        <select name="answer_style">
                            <option value="summary">summary</option>
                            <option value="citations">citations</option>
                            <option value="actions">actions</option>
                        </select>
                    </label>
                    <button class="button primary" type="submit" style="margin-top: 14px;">Plan RAG strategy</button>
                </form>
            </article>

            <article class="panel">
                <h2>Strategy Summary</h2>
                <p class="muted" id="ragStrategyPlanStatus">Submit input to choose a RAG pattern.</p>
                <div id="ragStrategySummary" class="muted">No plan yet.</div>
                <div id="ragStrategyOperationalPlan" class="muted" style="margin-top: 14px;"></div>
                <pre class="raw-json"><code id="ragStrategyPlanOutput">POST /api/practice/rag-strategy-plan</code></pre>
            </article>

            <article class="panel">
                <h2>Files To Read</h2>
                <ul>
                    <li><code>routes/api/practice-actions.php</code></li>
                    <li><code>app/Http/Requests/Api/PlanRagStrategyRequest.php</code></li>
                    <li><code>app/Http/Controllers/Api/RagStrategyPlanController.php</code></li>
                    <li><code>app/Services/Practice/RagStrategyPlanService.php</code></li>
                    <li><code>tests/Feature/RagStrategyPlanWorkbenchTest.php</code></li>
                </ul>
            </article>
        </div>
    </section>

    <script>
        const ragStrategyPresets = {
            classicDocs: {
                knowledge_shape: 'documents',
                relationship_need: 'low',
                tool_use: 'none',
                freshness: 'periodic',
                risk_level: 'medium',
                answer_style: 'citations',
                context_strategy: 'rag',
            },
            graphIncidents: {
                knowledge_shape: 'entities',
                relationship_need: 'high',
                tool_use: 'retrieval-tools',
                freshness: 'periodic',
                risk_level: 'high',
                answer_style: 'citations',
                context_strategy: 'rag',
            },
            agentOps: {
                knowledge_shape: 'workflows',
                relationship_need: 'medium',
                tool_use: 'multi-step-agent',
                freshness: 'real-time',
                risk_level: 'high',
                answer_style: 'actions',
                context_strategy: 'hybrid',
            },
            highRiskPolicy: {
                knowledge_shape: 'documents',
                relationship_need: 'medium',
                tool_use: 'none',
                freshness: 'static',
                risk_level: 'high',
                answer_style: 'citations',
                context_strategy: 'rag',
            },
            longContextPack: {
                knowledge_shape: 'documents',
                relationship_need: 'medium',
                tool_use: 'none',
                freshness: 'static',
                risk_level: 'medium',
                answer_style: 'summary',
                context_strategy: 'long-context',
            },
            cagFaq: {
                knowledge_shape: 'documents',
                relationship_need: 'low',
                tool_use: 'none',
                freshness: 'static',
                risk_level: 'low',
                answer_style: 'summary',
                context_strategy: 'cag',
            },
            hybridSupport: {
                knowledge_shape: 'documents',
                relationship_need: 'medium',
                tool_use: 'retrieval-tools',
                freshness: 'periodic',
                risk_level: 'high',
                answer_style: 'citations',
                context_strategy: 'hybrid',
            },
        };

        const ragStrategyPreset = document.querySelector('#ragStrategyPreset');
        const ragStrategyPlanForm = document.querySelector('#ragStrategyPlanForm');
        const ragStrategyPlanStatus = document.querySelector('#ragStrategyPlanStatus');
        const ragStrategyPlanOutput = document.querySelector('#ragStrategyPlanOutput');
        const ragStrategySummary = document.querySelector('#ragStrategySummary');
        const ragStrategyOperationalPlan = document.querySelector('#ragStrategyOperationalPlan');

        function escapeHtml(value) {
            const div = document.createElement('div');
            div.textContent = value;
            return div.innerHTML;
        }

        function applyRagStrategyPreset(presetName) {
            const preset = ragStrategyPresets[presetName];

            if (!preset) {
                return;
            }

            Object.entries(preset).forEach(([field, value]) => {
                const input = ragStrategyPlanForm.elements.namedItem(field);

                if (input) {
                    input.value = value;
                }
            });
        }

        function renderRagStrategySummary(data) {
            const summary = data.summary || {};
            const scores = data.score_breakdown || {};
            const contextStrategy = data.context_strategy_plan || {};
            const rollout = Array.isArray(data.rollout_plan) ? data.rollout_plan : [];
            const observability = Array.isArray(data.observability_plan) ? data.observability_plan : [];
            const answerContract = data.answer_contract || {};
            const costControls = Array.isArray(data.cost_controls) ? data.cost_controls : [];
            const readiness = data.readiness_score || {};
            const runbook = Array.isArray(data.failure_runbook) ? data.failure_runbook : [];
            const benchmarks = Array.isArray(data.benchmark_plan) ? data.benchmark_plan : [];
            const owners = Array.isArray(data.ownership_matrix) ? data.ownership_matrix : [];
            const fixtures = Array.isArray(data.test_fixture_plan) ? data.test_fixture_plan : [];
            const backlog = Array.isArray(data.implementation_backlog) ? data.implementation_backlog : [];
            const threats = Array.isArray(data.threat_model) ? data.threat_model : [];
            const sloPolicy = data.slo_policy || {};
            const releases = Array.isArray(data.release_checklist) ? data.release_checklist : [];
            const evidencePacket = Array.isArray(data.evidence_packet) ? data.evidence_packet : [];
            const blueprint = Array.isArray(data.laravel_integration_blueprint) ? data.laravel_integration_blueprint : [];
            const ciGates = Array.isArray(data.ci_quality_gates) ? data.ci_quality_gates : [];
            const openApi = data.openapi_contract || {};
            const injectionTests = Array.isArray(data.prompt_injection_tests) ? data.prompt_injection_tests : [];
            const capacityPlan = Array.isArray(data.capacity_plan) ? data.capacity_plan : [];

            ragStrategySummary.innerHTML = `
                <p><strong>${escapeHtml(summary.recommendation || 'unknown')}</strong></p>
                <p>${escapeHtml(summary.reason || '')}</p>
                <p>Context strategy: <strong>${escapeHtml(contextStrategy.recommendation || 'unknown')}</strong></p>
                <p>${escapeHtml(contextStrategy.reason || '')}</p>
                <p>Scores: classic ${Number(scores.classic_rag || 0)}, graph ${Number(scores.graph_rag || 0)}, agentic ${Number(scores.agentic_rag || 0)}</p>
                <p>Readiness: ${Number(readiness.score || 0)} / 100 (${escapeHtml(readiness.status || 'unknown')})</p>
            `;
            ragStrategyOperationalPlan.innerHTML = `
                <p><strong>Context routing</strong></p>
                <ul>${(Array.isArray(contextStrategy.routing_sequence) ? contextStrategy.routing_sequence : []).map((item) => `<li>${escapeHtml(item.step || '')}: ${escapeHtml(item.decision || '')}</li>`).join('')}</ul>
                <p><strong>Context guardrails</strong></p>
                <ul>${(Array.isArray(contextStrategy.guardrails) ? contextStrategy.guardrails : []).map((item) => `<li>${escapeHtml(item)}</li>`).join('')}</ul>
                <p><strong>Rollout gates</strong></p>
                <ul>${rollout.slice(0, 3).map((item) => `<li>${escapeHtml(item.stage || '')}: ${escapeHtml(item.exit_gate || '')}</li>`).join('')}</ul>
                <p><strong>Answer contract</strong></p>
                <ul>${Object.entries(answerContract).slice(0, 4).map(([field, rule]) => `<li>${escapeHtml(field)}: ${escapeHtml(rule)}</li>`).join('')}</ul>
                <p><strong>Signals</strong></p>
                <ul>${observability.slice(0, 4).map((item) => `<li>${escapeHtml(item.signal || '')}</li>`).join('')}</ul>
                <p><strong>Cost controls</strong></p>
                <ul>${costControls.slice(0, 3).map((item) => `<li>${escapeHtml(item)}</li>`).join('')}</ul>
                <p><strong>Benchmark plan</strong></p>
                <ul>${benchmarks.slice(0, 3).map((item) => `<li>${escapeHtml(item.name || '')}: ${escapeHtml(item.target || '')}</li>`).join('')}</ul>
                <p><strong>Test fixtures</strong></p>
                <ul>${fixtures.slice(0, 3).map((item) => `<li>${escapeHtml(item.name || '')}: ${escapeHtml(item.proves || '')}</li>`).join('')}</ul>
                <p><strong>Implementation backlog</strong></p>
                <ul>${backlog.slice(0, 3).map((item) => `<li>${escapeHtml(item.milestone || '')}</li>`).join('')}</ul>
                <p><strong>Owners</strong></p>
                <ul>${owners.slice(0, 4).map((item) => `<li>${escapeHtml(item.role || '')}: ${escapeHtml(item.owns || '')}</li>`).join('')}</ul>
                <p><strong>SLO policy</strong></p>
                <ul><li>Availability: ${escapeHtml(sloPolicy.availability || '')}</li><li>Latency: ${escapeHtml(sloPolicy.latency || '')}</li></ul>
                <p><strong>Release checklist</strong></p>
                <ul>${releases.slice(0, 4).map((item) => `<li>${escapeHtml(item.item || '')}: ${escapeHtml(item.evidence || '')}</li>`).join('')}</ul>
                <p><strong>Evidence packet</strong></p>
                <ul>${evidencePacket.slice(0, 3).map((item) => `<li>${escapeHtml(item)}</li>`).join('')}</ul>
                <p><strong>Laravel blueprint</strong></p>
                <ul>${blueprint.slice(0, 4).map((item) => `<li>${escapeHtml(item.file || '')}: ${escapeHtml(item.responsibility || '')}</li>`).join('')}</ul>
                <p><strong>API contract</strong></p>
                <ul><li>${escapeHtml(openApi.method || '')} ${escapeHtml(openApi.path || '')}</li></ul>
                <p><strong>CI gates</strong></p>
                <ul>${ciGates.slice(0, 4).map((item) => `<li>${escapeHtml(item.name || '')}: ${escapeHtml(item.command || '')}</li>`).join('')}</ul>
                <p><strong>Injection tests</strong></p>
                <ul>${injectionTests.slice(0, 3).map((item) => `<li>${escapeHtml(item.name || '')}: ${escapeHtml(item.expected_behavior || '')}</li>`).join('')}</ul>
                <p><strong>Capacity plan</strong></p>
                <ul>${capacityPlan.slice(0, 3).map((item) => `<li>${escapeHtml(item.resource || '')}: ${escapeHtml(item.control || '')}</li>`).join('')}</ul>
                <p><strong>Threat model</strong></p>
                <ul>${threats.slice(0, 3).map((item) => `<li>${escapeHtml(item.threat || '')}: ${escapeHtml(item.mitigation || '')}</li>`).join('')}</ul>
                <p><strong>Failure runbook</strong></p>
                <ul>${runbook.slice(0, 3).map((item) => `<li>${escapeHtml(item.symptom || '')}: ${escapeHtml(item.immediate_action || '')}</li>`).join('')}</ul>
            `;
        }

        ragStrategyPreset.addEventListener('change', (event) => {
            applyRagStrategyPreset(event.target.value);
        });

        applyRagStrategyPreset(ragStrategyPreset.value);

        ragStrategyPlanForm.addEventListener('submit', async (event) => {
            event.preventDefault();
            const payload = Object.fromEntries(new FormData(ragStrategyPlanForm).entries());

            ragStrategyPlanStatus.textContent = 'Running POST /api/practice/rag-strategy-plan...';
            ragStrategyPlanOutput.textContent = JSON.stringify(payload, null, 2);

            try {
                const response = await fetch('{{ route('api.practice.rag-strategy-plan.store') }}', {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(payload),
                });
                const body = await response.json();

                ragStrategyPlanStatus.textContent = `HTTP ${response.status}`;
                ragStrategyPlanOutput.textContent = JSON.stringify(body, null, 2);

                if (body.data) {
                    renderRagStrategySummary(body.data);
                }
            } catch (error) {
                ragStrategyPlanStatus.textContent = 'Request failed';
                ragStrategyPlanOutput.textContent = error instanceof Error ? error.message : 'Unknown request error';
            }
        });
    </script>
@endsection
