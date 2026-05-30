@extends('learning.layout', ['title' => 'AI Hallucination Guard Plan Workbench'])

@section('content')
    <section class="hero">
        <span class="badge">Runnable workbench</span>
        <h1>Reduce hallucination risk before accepting AI-generated code.</h1>
        <p>
            This workbench turns AI risk into concrete guardrails: evidence sources,
            prompt contracts, verification commands, review lenses, and code controls.
        </p>
    </section>

    <section class="section">
        <div class="grid">
            <article class="panel">
                <h2>AI Risk Context</h2>
                <form id="aiHallucinationGuardPlanForm">
                    <label>
                        Scenario preset
                        <select id="aiHallucinationGuardPreset">
                            <option value="highRiskCode">High-risk code generation</option>
                            <option value="debugging">Debugging with partial evidence</option>
                            <option value="dataAnswer">Data answer without retrieval</option>
                            <option value="review">AI diff review with tests</option>
                        </select>
                    </label>
                    <label style="margin-top: 12px;">
                        AI task
                        <select name="ai_task">
                            <option value="code-generation">code-generation</option>
                            <option value="code-review">code-review</option>
                            <option value="data-answer">data-answer</option>
                            <option value="refactor">refactor</option>
                            <option value="debugging">debugging</option>
                        </select>
                    </label>
                    <label style="margin-top: 12px;">
                        Risk level
                        <select name="risk_level">
                            <option value="high">high</option>
                            <option value="medium">medium</option>
                            <option value="low">low</option>
                        </select>
                    </label>
                    <label style="margin-top: 12px;">
                        Evidence sources
                        <select name="evidence_sources">
                            <option value="none">none</option>
                            <option value="partial">partial</option>
                            <option value="repo-context">repo-context</option>
                            <option value="tests-docs">tests-docs</option>
                        </select>
                    </label>
                    <label style="margin-top: 12px;">
                        Runtime checks
                        <select name="runtime_checks">
                            <option value="yes">yes</option>
                            <option value="no">no</option>
                        </select>
                    </label>
                    <label style="margin-top: 12px;">
                        Human review
                        <select name="human_review">
                            <option value="yes">yes</option>
                            <option value="no">no</option>
                        </select>
                    </label>
                    <button class="button primary" type="submit" style="margin-top: 14px;">Plan hallucination guardrails</button>
                </form>
            </article>

            <article class="panel">
                <h2>Guardrail Plan</h2>
                <p class="muted" id="aiHallucinationGuardPlanStatus">Submit input to build the hallucination guard plan.</p>
                <pre class="raw-json"><code id="aiHallucinationGuardPlanOutput">POST /api/practice/ai-hallucination-guard-plan</code></pre>
            </article>

            <article class="panel">
                <h2>Files To Read</h2>
                <ul>
                    <li><code>routes/api/practice-actions.php</code></li>
                    <li><code>app/Http/Requests/Api/PlanAiHallucinationGuardRequest.php</code></li>
                    <li><code>app/Http/Controllers/Api/AiHallucinationGuardPlanController.php</code></li>
                    <li><code>app/Services/Practice/AiHallucinationGuardPlanService.php</code></li>
                    <li><code>tests/Feature/AiHallucinationGuardPlanWorkbenchTest.php</code></li>
                </ul>
            </article>
        </div>
    </section>

    <script>
        const aiHallucinationGuardPresets = {
            highRiskCode: {
                ai_task: 'code-generation',
                risk_level: 'high',
                evidence_sources: 'partial',
                runtime_checks: 'yes',
                human_review: 'yes',
            },
            debugging: {
                ai_task: 'debugging',
                risk_level: 'medium',
                evidence_sources: 'partial',
                runtime_checks: 'yes',
                human_review: 'yes',
            },
            dataAnswer: {
                ai_task: 'data-answer',
                risk_level: 'high',
                evidence_sources: 'none',
                runtime_checks: 'no',
                human_review: 'yes',
            },
            review: {
                ai_task: 'code-review',
                risk_level: 'medium',
                evidence_sources: 'tests-docs',
                runtime_checks: 'yes',
                human_review: 'yes',
            },
        };

        const aiHallucinationGuardPreset = document.querySelector('#aiHallucinationGuardPreset');
        const aiHallucinationGuardPlanForm = document.querySelector('#aiHallucinationGuardPlanForm');
        const aiHallucinationGuardPlanStatus = document.querySelector('#aiHallucinationGuardPlanStatus');
        const aiHallucinationGuardPlanOutput = document.querySelector('#aiHallucinationGuardPlanOutput');

        function applyAiHallucinationGuardPreset(presetName) {
            const preset = aiHallucinationGuardPresets[presetName];

            if (!preset) {
                return;
            }

            Object.entries(preset).forEach(([field, value]) => {
                const input = aiHallucinationGuardPlanForm.elements.namedItem(field);

                if (input) {
                    input.value = value;
                }
            });
        }

        aiHallucinationGuardPreset.addEventListener('change', (event) => {
            applyAiHallucinationGuardPreset(event.target.value);
        });

        applyAiHallucinationGuardPreset(aiHallucinationGuardPreset.value);

        aiHallucinationGuardPlanForm.addEventListener('submit', async (event) => {
            event.preventDefault();
            const payload = Object.fromEntries(new FormData(aiHallucinationGuardPlanForm).entries());

            aiHallucinationGuardPlanStatus.textContent = 'Running POST /api/practice/ai-hallucination-guard-plan...';
            aiHallucinationGuardPlanOutput.textContent = JSON.stringify(payload, null, 2);

            try {
                const response = await fetch('{{ route('api.practice.ai-hallucination-guard-plan.store') }}', {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(payload),
                });
                const body = await response.json();

                aiHallucinationGuardPlanStatus.textContent = `HTTP ${response.status}`;
                aiHallucinationGuardPlanOutput.textContent = JSON.stringify(body, null, 2);
            } catch (error) {
                aiHallucinationGuardPlanStatus.textContent = 'Request failed';
                aiHallucinationGuardPlanOutput.textContent = error instanceof Error ? error.message : 'Unknown request error';
            }
        });
    </script>
@endsection
