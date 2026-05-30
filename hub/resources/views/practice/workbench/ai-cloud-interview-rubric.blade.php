@extends('learning.layout', ['title' => 'AI Cloud Interview Rubric Workbench'])

@section('content')
    <section class="hero">
        <span class="badge">Runnable workbench</span>
        <h1>Score whether a Cloud Engineer really uses AI at work.</h1>
        <p>
            This workbench turns an interview answer into a practical rubric across concrete tasks,
            prompt workflow, IaC review, failure stories, verification, AWS Documentation conflicts,
            and team enablement.
        </p>
    </section>

    <section class="section">
        <div class="grid">
            <article class="panel">
                <h2>Candidate Signals</h2>
                <form id="aiCloudInterviewRubricForm">
                    <label>
                        Scenario preset
                        <select id="aiCloudInterviewRubricPreset">
                            <option value="strongSenior">Strong senior user</option>
                            <option value="surfaceUser">Surface-level user</option>
                            <option value="midManual">Mid-level manual verifier</option>
                        </select>
                    </label>
                    <label style="margin-top: 12px;">
                        Candidate level
                        <select name="candidate_level">
                            <option value="senior">senior</option>
                            <option value="mid">mid</option>
                            <option value="junior">junior</option>
                        </select>
                    </label>
                    <label style="margin-top: 12px;">
                        Recent AI task
                        <select name="recent_task">
                            <option value="specific">specific</option>
                            <option value="vague">vague</option>
                            <option value="none">none</option>
                        </select>
                    </label>
                    <label style="margin-top: 12px;">
                        Prompt workflow
                        <select name="prompt_workflow">
                            <option value="structured">structured</option>
                            <option value="basic">basic</option>
                            <option value="none">none</option>
                        </select>
                    </label>
                    <label style="margin-top: 12px;">
                        IaC review
                        <select name="iac_review">
                            <option value="production">production</option>
                            <option value="basic">basic</option>
                            <option value="none">none</option>
                        </select>
                    </label>
                    <label style="margin-top: 12px;">
                        AI failure story
                        <select name="failure_story">
                            <option value="concrete">concrete</option>
                            <option value="generic">generic</option>
                            <option value="none">none</option>
                        </select>
                    </label>
                    <label style="margin-top: 12px;">
                        Verification workflow
                        <select name="verification_workflow">
                            <option value="systematic">systematic</option>
                            <option value="manual">manual</option>
                            <option value="none">none</option>
                        </select>
                    </label>
                    <label style="margin-top: 12px;">
                        AI vs AWS docs
                        <select name="docs_conflict">
                            <option value="source-of-truth">source-of-truth</option>
                            <option value="check-docs">check-docs</option>
                            <option value="trusts-ai">trusts-ai</option>
                        </select>
                    </label>
                    <label style="margin-top: 12px;">
                        Team enablement
                        <select name="team_enablement">
                            <option value="team-standard">team-standard</option>
                            <option value="prompt-file">prompt-file</option>
                            <option value="none">none</option>
                        </select>
                    </label>
                    <button class="button primary" type="submit" style="margin-top: 14px;">Score interview answer</button>
                </form>
            </article>

            <article class="panel">
                <h2>Rubric Output</h2>
                <p class="muted" id="aiCloudInterviewRubricStatus">Submit input to score the candidate signals.</p>
                <div id="aiCloudInterviewRubricSummary" style="display: grid; gap: 12px; margin: 14px 0;">
                    <div>
                        <strong>Score</strong>
                        <p class="muted" id="aiCloudInterviewRubricScore">No score yet.</p>
                    </div>
                    <div>
                        <strong>Hiring signal</strong>
                        <p class="muted" id="aiCloudInterviewRubricHiringSignal">Submit the rubric to generate a hiring signal.</p>
                    </div>
                    <div>
                        <strong>Score breakdown</strong>
                        <ul id="aiCloudInterviewRubricDimensions">
                            <li>No dimensions scored yet.</li>
                        </ul>
                    </div>
                    <div>
                        <strong>Red flags</strong>
                        <ul id="aiCloudInterviewRubricRedFlags">
                            <li>No red flags scored yet.</li>
                        </ul>
                    </div>
                    <div>
                        <strong>Green flags</strong>
                        <ul id="aiCloudInterviewRubricGreenFlags">
                            <li>No green flags scored yet.</li>
                        </ul>
                    </div>
                    <div>
                        <strong>Follow-up questions</strong>
                        <ul id="aiCloudInterviewRubricFollowUps">
                            <li>No follow-up questions generated yet.</li>
                        </ul>
                    </div>
                    <div>
                        <strong>Next interview actions</strong>
                        <ul id="aiCloudInterviewRubricNextActions">
                            <li>No targeted actions generated yet.</li>
                        </ul>
                    </div>
                    <div>
                        <strong>Tiered question sequence</strong>
                        <ul id="aiCloudInterviewRubricQuestionSequence">
                            <li>No question sequence generated yet.</li>
                        </ul>
                    </div>
                    <div>
                        <strong>Hands-on challenge</strong>
                        <p class="muted" id="aiCloudInterviewRubricChallenge">No hands-on challenge generated yet.</p>
                    </div>
                    <div>
                        <strong>Debrief decision</strong>
                        <p class="muted" id="aiCloudInterviewRubricDebrief">No debrief generated yet.</p>
                    </div>
                    <div>
                        <strong>Debrief markdown</strong>
                        <p class="muted" id="aiCloudInterviewRubricCopyStatus">Generate a debrief before copying.</p>
                        <button class="button" id="aiCloudInterviewRubricCopy" type="button" style="margin: 8px 0;">Copy debrief markdown</button>
                        <pre class="raw-json"><code id="aiCloudInterviewRubricMarkdown">Submit the rubric to generate a paste-ready interview note.</code></pre>
                    </div>
                </div>
                <pre class="raw-json"><code id="aiCloudInterviewRubricOutput">POST /api/practice/ai-cloud-interview-rubric</code></pre>
            </article>

            <article class="panel">
                <h2>Files To Read</h2>
                <ul>
                    <li><code>routes/api/practice-actions.php</code></li>
                    <li><code>app/Http/Requests/Api/ScoreAiCloudInterviewRubricRequest.php</code></li>
                    <li><code>app/Http/Controllers/Api/AiCloudInterviewRubricController.php</code></li>
                    <li><code>app/Services/Practice/AiCloudInterviewRubricService.php</code></li>
                    <li><code>tests/Feature/AiCloudInterviewRubricWorkbenchTest.php</code></li>
                </ul>
            </article>
        </div>
    </section>

    <script>
        const aiCloudInterviewRubricPresets = {
            strongSenior: {
                candidate_level: 'senior',
                recent_task: 'specific',
                prompt_workflow: 'structured',
                iac_review: 'production',
                failure_story: 'concrete',
                verification_workflow: 'systematic',
                docs_conflict: 'source-of-truth',
                team_enablement: 'team-standard',
            },
            surfaceUser: {
                candidate_level: 'mid',
                recent_task: 'vague',
                prompt_workflow: 'basic',
                iac_review: 'none',
                failure_story: 'none',
                verification_workflow: 'none',
                docs_conflict: 'trusts-ai',
                team_enablement: 'none',
            },
            midManual: {
                candidate_level: 'mid',
                recent_task: 'specific',
                prompt_workflow: 'basic',
                iac_review: 'basic',
                failure_story: 'generic',
                verification_workflow: 'manual',
                docs_conflict: 'check-docs',
                team_enablement: 'prompt-file',
            },
        };

        const aiCloudInterviewRubricPreset = document.querySelector('#aiCloudInterviewRubricPreset');
        const aiCloudInterviewRubricForm = document.querySelector('#aiCloudInterviewRubricForm');
        const aiCloudInterviewRubricStatus = document.querySelector('#aiCloudInterviewRubricStatus');
        const aiCloudInterviewRubricOutput = document.querySelector('#aiCloudInterviewRubricOutput');
        const aiCloudInterviewRubricScore = document.querySelector('#aiCloudInterviewRubricScore');
        const aiCloudInterviewRubricHiringSignal = document.querySelector('#aiCloudInterviewRubricHiringSignal');
        const aiCloudInterviewRubricDimensions = document.querySelector('#aiCloudInterviewRubricDimensions');
        const aiCloudInterviewRubricRedFlags = document.querySelector('#aiCloudInterviewRubricRedFlags');
        const aiCloudInterviewRubricGreenFlags = document.querySelector('#aiCloudInterviewRubricGreenFlags');
        const aiCloudInterviewRubricFollowUps = document.querySelector('#aiCloudInterviewRubricFollowUps');
        const aiCloudInterviewRubricNextActions = document.querySelector('#aiCloudInterviewRubricNextActions');
        const aiCloudInterviewRubricQuestionSequence = document.querySelector('#aiCloudInterviewRubricQuestionSequence');
        const aiCloudInterviewRubricChallenge = document.querySelector('#aiCloudInterviewRubricChallenge');
        const aiCloudInterviewRubricDebrief = document.querySelector('#aiCloudInterviewRubricDebrief');
        const aiCloudInterviewRubricMarkdown = document.querySelector('#aiCloudInterviewRubricMarkdown');
        const aiCloudInterviewRubricCopy = document.querySelector('#aiCloudInterviewRubricCopy');
        const aiCloudInterviewRubricCopyStatus = document.querySelector('#aiCloudInterviewRubricCopyStatus');
        let aiCloudInterviewRubricMarkdownValue = '';

        function applyAiCloudInterviewRubricPreset(presetName) {
            const preset = aiCloudInterviewRubricPresets[presetName];

            if (!preset) {
                return;
            }

            Object.entries(preset).forEach(([field, value]) => {
                const input = aiCloudInterviewRubricForm.elements.namedItem(field);

                if (input) {
                    input.value = value;
                }
            });
        }

        aiCloudInterviewRubricPreset.addEventListener('change', (event) => {
            applyAiCloudInterviewRubricPreset(event.target.value);
        });

        function renderAiCloudInterviewRubricList(node, items, emptyText) {
            node.replaceChildren();

            const values = Array.isArray(items) && items.length > 0 ? items : [emptyText];

            values.forEach((item) => {
                const listItem = document.createElement('li');
                listItem.textContent = item;
                node.appendChild(listItem);
            });
        }

        function renderAiCloudInterviewRubricSummary(result) {
            aiCloudInterviewRubricScore.textContent = `${result.summary.score}/100 - ${result.summary.verdict}`;
            aiCloudInterviewRubricHiringSignal.textContent = result.summary.hiring_signal;
            renderAiCloudInterviewRubricList(
                aiCloudInterviewRubricDimensions,
                result.dimensions?.map((item) => `${item.dimension}: ${item.points}/${item.max} - ${item.reason}`),
                'No dimensions scored.'
            );
            renderAiCloudInterviewRubricList(aiCloudInterviewRubricRedFlags, result.red_flags, 'No red flags found for this signal set.');
            renderAiCloudInterviewRubricList(aiCloudInterviewRubricGreenFlags, result.green_flags, 'No green flags found for this signal set.');
            renderAiCloudInterviewRubricList(aiCloudInterviewRubricFollowUps, result.follow_up_questions, 'No follow-up questions generated.');
            renderAiCloudInterviewRubricList(
                aiCloudInterviewRubricNextActions,
                result.improvement_plan?.map((item) => `${item.dimension}: ${item.action}`),
                'No targeted actions needed for this signal set.'
            );
            renderAiCloudInterviewRubricList(
                aiCloudInterviewRubricQuestionSequence,
                result.question_sequence?.map((item) => `${item.tier}: ${item.questions[0]}`),
                'No question sequence generated.'
            );
            aiCloudInterviewRubricChallenge.textContent = `${result.hands_on_challenge.title}: ${result.hands_on_challenge.candidate_task}`;
            aiCloudInterviewRubricDebrief.textContent = `${result.debrief_template.decision}: ${result.debrief_template.recommended_next_step}`;
            aiCloudInterviewRubricMarkdownValue = result.debrief_markdown;
            aiCloudInterviewRubricMarkdown.textContent = result.debrief_markdown;
            aiCloudInterviewRubricCopyStatus.textContent = 'Debrief markdown is ready to copy.';
        }

        async function copyAiCloudInterviewRubricMarkdown() {
            if (!aiCloudInterviewRubricMarkdownValue) {
                aiCloudInterviewRubricCopyStatus.textContent = 'Generate a debrief before copying.';
                return;
            }

            try {
                if (navigator.clipboard?.writeText) {
                    await navigator.clipboard.writeText(aiCloudInterviewRubricMarkdownValue);
                } else {
                    const fallback = document.createElement('textarea');
                    fallback.value = aiCloudInterviewRubricMarkdownValue;
                    document.body.appendChild(fallback);
                    fallback.select();
                    document.execCommand('copy');
                    fallback.remove();
                }

                aiCloudInterviewRubricCopyStatus.textContent = 'Debrief markdown copied.';
            } catch (error) {
                aiCloudInterviewRubricCopyStatus.textContent = 'Copy failed. Select the markdown manually.';
            }
        }

        aiCloudInterviewRubricCopy.addEventListener('click', copyAiCloudInterviewRubricMarkdown);

        aiCloudInterviewRubricForm.addEventListener('submit', async (event) => {
            event.preventDefault();
            aiCloudInterviewRubricStatus.textContent = 'Scoring interview answer...';

            const payload = Object.fromEntries(new FormData(aiCloudInterviewRubricForm).entries());
            const response = await fetch('/api/practice/ai-cloud-interview-rubric', {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content ?? '',
                },
                body: JSON.stringify(payload),
            });

            const data = await response.json();
            aiCloudInterviewRubricStatus.textContent = response.ok
                ? `Verdict: ${data.data.summary.verdict} (${data.data.summary.score}/100)`
                : 'Validation failed. Check the selected values.';

            if (response.ok) {
                renderAiCloudInterviewRubricSummary(data.data);
            }

            aiCloudInterviewRubricOutput.textContent = JSON.stringify(data, null, 2);
        });

        applyAiCloudInterviewRubricPreset('strongSenior');
    </script>
@endsection
