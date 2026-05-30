@extends('learning.layout', ['title' => 'AI Agent Memory Plan Workbench'])

@section('content')
    <section class="hero">
        <span class="badge">Runnable workbench</span>
        <h1>Design AI agent memory as governed contracts.</h1>
        <p>
            This workbench separates working, episodic, semantic, and procedural
            memory so a developer-style agent can reuse context without trusting
            stale, private, or low-confidence facts.
        </p>
    </section>

    <section class="section">
        <div class="grid">
            <article class="panel">
                <h2>Memory Context</h2>
                <form id="aiAgentMemoryPlanForm">
                    <label>
                        Scenario preset
                        <select id="aiAgentMemoryPreset">
                            <option value="developerStrict">Developer agent, strict memory</option>
                            <option value="supportBalanced">Support agent, rolling window</option>
                            <option value="researchRefresh">Research agent, refresh before use</option>
                        </select>
                    </label>
                    <label style="margin-top: 12px;">
                        Agent profile
                        <select name="agent_profile">
                            <option value="developer-agent">developer-agent</option>
                            <option value="support-agent">support-agent</option>
                            <option value="research-agent">research-agent</option>
                        </select>
                    </label>
                    <label style="margin-top: 12px;">
                        Storage scope
                        <select name="storage_scope">
                            <option value="project-scoped">project-scoped</option>
                            <option value="session-only">session-only</option>
                            <option value="user-scoped">user-scoped</option>
                        </select>
                    </label>
                    <label style="margin-top: 12px;">
                        Retention policy
                        <select name="retention_policy">
                            <option value="reviewed-durable">reviewed-durable</option>
                            <option value="short-lived">short-lived</option>
                            <option value="rolling-window">rolling-window</option>
                        </select>
                    </label>
                    <label style="margin-top: 12px;">
                        Privacy mode
                        <select name="privacy_mode">
                            <option value="strict">strict</option>
                            <option value="balanced">balanced</option>
                        </select>
                    </label>
                    <label style="margin-top: 12px;">
                        Staleness policy
                        <select name="staleness_policy">
                            <option value="block-stale">block-stale</option>
                            <option value="warn-on-stale">warn-on-stale</option>
                            <option value="refresh-before-use">refresh-before-use</option>
                        </select>
                    </label>
                    <button class="button primary" type="submit" style="margin-top: 14px;">Plan agent memory</button>
                </form>
            </article>

            <article class="panel">
                <h2>Memory Plan</h2>
                <p class="muted" id="aiAgentMemoryPlanStatus">Submit input to build the AI agent memory contract.</p>
                <pre class="raw-json"><code id="aiAgentMemoryPlanOutput">POST /api/practice/ai-agent-memory-plan</code></pre>
            </article>

            <article class="panel">
                <h2>Files To Read</h2>
                <ul>
                    <li><code>routes/api/practice-actions.php</code></li>
                    <li><code>app/Http/Requests/Api/PlanAiAgentMemoryRequest.php</code></li>
                    <li><code>app/Http/Controllers/Api/AiAgentMemoryPlanController.php</code></li>
                    <li><code>app/Services/Practice/AiAgentMemoryPlanService.php</code></li>
                    <li><code>tests/Feature/AiAgentMemoryPlanWorkbenchTest.php</code></li>
                </ul>
            </article>
        </div>
    </section>

    <script>
        const aiAgentMemoryPresets = {
            developerStrict: {
                agent_profile: 'developer-agent',
                storage_scope: 'project-scoped',
                retention_policy: 'reviewed-durable',
                privacy_mode: 'strict',
                staleness_policy: 'block-stale',
            },
            supportBalanced: {
                agent_profile: 'support-agent',
                storage_scope: 'user-scoped',
                retention_policy: 'rolling-window',
                privacy_mode: 'balanced',
                staleness_policy: 'warn-on-stale',
            },
            researchRefresh: {
                agent_profile: 'research-agent',
                storage_scope: 'session-only',
                retention_policy: 'short-lived',
                privacy_mode: 'strict',
                staleness_policy: 'refresh-before-use',
            },
        };

        const aiAgentMemoryPreset = document.querySelector('#aiAgentMemoryPreset');
        const aiAgentMemoryPlanForm = document.querySelector('#aiAgentMemoryPlanForm');
        const aiAgentMemoryPlanStatus = document.querySelector('#aiAgentMemoryPlanStatus');
        const aiAgentMemoryPlanOutput = document.querySelector('#aiAgentMemoryPlanOutput');

        function applyAiAgentMemoryPreset(presetName) {
            const preset = aiAgentMemoryPresets[presetName];

            if (!preset) {
                return;
            }

            Object.entries(preset).forEach(([field, value]) => {
                const input = aiAgentMemoryPlanForm.elements.namedItem(field);

                if (input) {
                    input.value = value;
                }
            });
        }

        aiAgentMemoryPreset.addEventListener('change', (event) => {
            applyAiAgentMemoryPreset(event.target.value);
        });

        applyAiAgentMemoryPreset(aiAgentMemoryPreset.value);

        aiAgentMemoryPlanForm.addEventListener('submit', async (event) => {
            event.preventDefault();
            const payload = Object.fromEntries(new FormData(aiAgentMemoryPlanForm).entries());

            aiAgentMemoryPlanStatus.textContent = 'Running POST /api/practice/ai-agent-memory-plan...';
            aiAgentMemoryPlanOutput.textContent = JSON.stringify(payload, null, 2);

            try {
                const response = await fetch('{{ route('api.practice.ai-agent-memory-plan.store') }}', {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(payload),
                });
                const body = await response.json();

                aiAgentMemoryPlanStatus.textContent = `HTTP ${response.status}`;
                aiAgentMemoryPlanOutput.textContent = JSON.stringify(body, null, 2);
            } catch (error) {
                aiAgentMemoryPlanStatus.textContent = 'Request failed';
                aiAgentMemoryPlanOutput.textContent = error instanceof Error ? error.message : 'Unknown request error';
            }
        });
    </script>
@endsection
