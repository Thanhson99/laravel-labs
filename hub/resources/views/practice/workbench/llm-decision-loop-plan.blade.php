@extends('learning.layout', ['title' => 'LLM Decision Loop Plan Workbench'])

@section('content')
    <section class="hero">
        <span class="badge">Runnable workbench</span>
        <h1>Model an LLM as a decision loop, not only autocomplete.</h1>
        <p>
            This workbench connects next-token probability, attention, Markov decision
            process language, reward models, PPO, tool feedback, and careful AGI claims.
        </p>
    </section>

    <section class="section">
        <div class="grid">
            <article class="panel">
                <h2>LLM Context</h2>
                <form id="llmDecisionLoopPlanForm">
                    <label>
                        Scenario preset
                        <select id="llmDecisionLoopPreset">
                            <option value="codingAgent">Coding agent with tests</option>
                            <option value="researchAnswer">Research answer with human preference</option>
                            <option value="chatOnly">Chat-only explanation</option>
                            <option value="overstatedAgi">Overstated AGI claim</option>
                        </select>
                    </label>
                    <label style="margin-top: 12px;">
                        Model role
                        <select name="model_role">
                            <option value="coding-assistant">coding-assistant</option>
                            <option value="research-assistant">research-assistant</option>
                            <option value="tool-agent">tool-agent</option>
                            <option value="chat-assistant">chat-assistant</option>
                        </select>
                    </label>
                    <label style="margin-top: 12px;">
                        Training stage
                        <select name="training_stage">
                            <option value="rlhf">rlhf</option>
                            <option value="sft">sft</option>
                            <option value="pretraining">pretraining</option>
                        </select>
                    </label>
                    <label style="margin-top: 12px;">
                        Feedback signal
                        <select name="feedback_signal">
                            <option value="tests-runtime">tests-runtime</option>
                            <option value="human-preference">human-preference</option>
                            <option value="environment-reward">environment-reward</option>
                            <option value="none">none</option>
                        </select>
                    </label>
                    <label style="margin-top: 12px;">
                        Tool use
                        <select name="tool_use">
                            <option value="yes">yes</option>
                            <option value="no">no</option>
                        </select>
                    </label>
                    <label style="margin-top: 12px;">
                        AGI claim level
                        <select name="agi_claim">
                            <option value="conservative">conservative</option>
                            <option value="optimistic">optimistic</option>
                            <option value="overstated">overstated</option>
                        </select>
                    </label>
                    <button class="button primary" type="submit" style="margin-top: 14px;">Plan LLM decision loop</button>
                </form>
            </article>

            <article class="panel">
                <h2>Decision Loop Plan</h2>
                <p class="muted" id="llmDecisionLoopPlanStatus">Submit input to build the LLM decision-loop explanation.</p>
                <pre class="raw-json"><code id="llmDecisionLoopPlanOutput">POST /api/practice/llm-decision-loop-plan</code></pre>
            </article>

            <article class="panel">
                <h2>Files To Read</h2>
                <ul>
                    <li><code>routes/api/practice-actions.php</code></li>
                    <li><code>app/Http/Requests/Api/PlanLlmDecisionLoopRequest.php</code></li>
                    <li><code>app/Http/Controllers/Api/LlmDecisionLoopPlanController.php</code></li>
                    <li><code>app/Services/Practice/LlmDecisionLoopPlanService.php</code></li>
                    <li><code>tests/Feature/LlmDecisionLoopPlanWorkbenchTest.php</code></li>
                </ul>
            </article>
        </div>
    </section>

    <script>
        const llmDecisionLoopPresets = {
            codingAgent: {
                model_role: 'coding-assistant',
                training_stage: 'rlhf',
                feedback_signal: 'tests-runtime',
                tool_use: 'yes',
                agi_claim: 'conservative',
            },
            researchAnswer: {
                model_role: 'research-assistant',
                training_stage: 'rlhf',
                feedback_signal: 'human-preference',
                tool_use: 'yes',
                agi_claim: 'optimistic',
            },
            chatOnly: {
                model_role: 'chat-assistant',
                training_stage: 'sft',
                feedback_signal: 'none',
                tool_use: 'no',
                agi_claim: 'conservative',
            },
            overstatedAgi: {
                model_role: 'tool-agent',
                training_stage: 'rlhf',
                feedback_signal: 'environment-reward',
                tool_use: 'yes',
                agi_claim: 'overstated',
            },
        };

        const llmDecisionLoopPreset = document.querySelector('#llmDecisionLoopPreset');
        const llmDecisionLoopPlanForm = document.querySelector('#llmDecisionLoopPlanForm');
        const llmDecisionLoopPlanStatus = document.querySelector('#llmDecisionLoopPlanStatus');
        const llmDecisionLoopPlanOutput = document.querySelector('#llmDecisionLoopPlanOutput');

        function applyLlmDecisionLoopPreset(presetName) {
            const preset = llmDecisionLoopPresets[presetName];

            if (!preset) {
                return;
            }

            Object.entries(preset).forEach(([field, value]) => {
                const input = llmDecisionLoopPlanForm.elements.namedItem(field);

                if (input) {
                    input.value = value;
                }
            });
        }

        llmDecisionLoopPreset.addEventListener('change', (event) => {
            applyLlmDecisionLoopPreset(event.target.value);
        });

        applyLlmDecisionLoopPreset(llmDecisionLoopPreset.value);

        llmDecisionLoopPlanForm.addEventListener('submit', async (event) => {
            event.preventDefault();
            const payload = Object.fromEntries(new FormData(llmDecisionLoopPlanForm).entries());

            llmDecisionLoopPlanStatus.textContent = 'Running POST /api/practice/llm-decision-loop-plan...';
            llmDecisionLoopPlanOutput.textContent = JSON.stringify(payload, null, 2);

            try {
                const response = await fetch('{{ route('api.practice.llm-decision-loop-plan.store') }}', {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(payload),
                });
                const body = await response.json();

                llmDecisionLoopPlanStatus.textContent = `HTTP ${response.status}`;
                llmDecisionLoopPlanOutput.textContent = JSON.stringify(body, null, 2);
            } catch (error) {
                llmDecisionLoopPlanStatus.textContent = 'Request failed';
                llmDecisionLoopPlanOutput.textContent = error instanceof Error ? error.message : 'Unknown request error';
            }
        });
    </script>
@endsection
