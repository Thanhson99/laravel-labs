@extends('learning.layout', ['title' => 'System Design Tradeoff Workbench'])

@section('content')
    <section class="hero">
        <span class="badge">Runnable workbench</span>
        <h1>Answer System Design with constraints before technology.</h1>
        <p>
            Practice turning an ambiguous prompt like a notification system for 10 million users
            into clarifying questions, explicit costs, team-fit checks, and an interview-ready
            tradeoff statement.
        </p>
    </section>

    <section class="section">
        <div class="grid">
            <article class="panel">
                <h2>Scenario</h2>
                <form id="systemDesignTradeoffForm">
                    <label>
                        Preset
                        <select id="systemDesignTradeoffPreset">
                            <option value="liveNotifications">10M real-time notifications</option>
                            <option value="simpleNotifications">Delayed notifications, small team</option>
                            <option value="payments">Payment flow correctness</option>
                            <option value="legacyScale">Legacy database scale</option>
                        </select>
                    </label>
                    <label style="margin-top: 12px;">
                        Scenario
                        <select name="scenario">
                            <option value="notification-10m">notification-10m</option>
                            <option value="payment-flow">payment-flow</option>
                            <option value="legacy-scale">legacy-scale</option>
                            <option value="startup-microservices">startup-microservices</option>
                        </select>
                    </label>
                    <label style="margin-top: 12px;">
                        Latency requirement
                        <select name="latency_requirement">
                            <option value="real-time">real-time</option>
                            <option value="near-real-time">near-real-time</option>
                            <option value="delayed">delayed</option>
                        </select>
                    </label>
                    <label style="margin-top: 12px;">
                        Failure impact
                        <select name="failure_impact">
                            <option value="high">high</option>
                            <option value="medium">medium</option>
                            <option value="low">low</option>
                        </select>
                    </label>
                    <label style="margin-top: 12px;">
                        Consistency need
                        <select name="consistency_need">
                            <option value="eventual">eventual</option>
                            <option value="strong">strong</option>
                        </select>
                    </label>
                    <label style="margin-top: 12px;">
                        Team maturity
                        <select name="team_maturity">
                            <option value="platform">platform</option>
                            <option value="medium">medium</option>
                            <option value="small">small</option>
                        </select>
                    </label>
                    <label style="margin-top: 12px;">
                        Operational capacity
                        <select name="operational_capacity">
                            <option value="high">high</option>
                            <option value="medium">medium</option>
                            <option value="low">low</option>
                        </select>
                    </label>
                    <label style="margin-top: 12px;">
                        Current constraint
                        <select name="current_constraint">
                            <option value="greenfield">greenfield</option>
                            <option value="legacy-database">legacy-database</option>
                            <option value="fast-delivery">fast-delivery</option>
                            <option value="regulated">regulated</option>
                        </select>
                    </label>
                    <label style="margin-top: 12px;">
                        Target level
                        <select name="target_level">
                            <option value="l6">L6 Staff</option>
                            <option value="l5">L5 Senior</option>
                            <option value="l7">L7 Principal</option>
                            <option value="l4">L4 Mid</option>
                        </select>
                    </label>
                    <label style="margin-top: 12px;">
                        Candidate answer
                        <textarea name="candidate_answer" rows="6" placeholder="Paste your own 60-120 second answer here for scoring."></textarea>
                    </label>
                    <button class="button primary" type="submit" style="margin-top: 14px;">Plan tradeoff answer</button>
                </form>
            </article>

            <article class="panel">
                <h2>Plan Output</h2>
                <p class="muted" id="systemDesignTradeoffStatus">Submit input to generate a tradeoff-first answer.</p>
                <pre class="raw-json"><code id="systemDesignTradeoffOutput">POST /api/practice/system-design-tradeoff-plan</code></pre>
            </article>

            <article class="panel">
                <h2>Interview Summary</h2>
                <div id="systemDesignTradeoffSummary" class="list">
                    <p class="muted">Run the planner to see recommendation, tradeoff statement, and level framing.</p>
                </div>
            </article>

            <article class="panel">
                <h2>Decision Memo</h2>
                <button class="button" id="copySystemDesignTradeoffMemo" type="button">Copy memo</button>
                <pre class="raw-json" style="margin-top: 12px;"><code id="systemDesignTradeoffMemo">Run the planner to generate a markdown memo.</code></pre>
            </article>

            <article class="panel">
                <h2>Interview Packet</h2>
                <button class="button" id="copySystemDesignTradeoffPacket" type="button">Copy packet</button>
                <pre class="raw-json" style="margin-top: 12px;"><code id="systemDesignTradeoffPacket">Run the planner to generate a rehearsal packet.</code></pre>
            </article>

            <article class="panel">
                <h2>Candidate Review</h2>
                <button class="button" id="copySystemDesignTradeoffReview" type="button">Copy review</button>
                <pre class="raw-json" style="margin-top: 12px;"><code id="systemDesignTradeoffReview">Paste an answer and run the planner to generate a review.</code></pre>
            </article>

            <article class="panel">
                <h2>Files To Read</h2>
                <ul>
                    <li><code>routes/api/practice-actions.php</code></li>
                    <li><code>routes/web/workbench.php</code></li>
                    <li><code>app/Http/Requests/Api/PlanSystemDesignTradeoffRequest.php</code></li>
                    <li><code>app/Services/Practice/SystemDesignTradeoffPlanService.php</code></li>
                    <li><code>tests/Unit/Practice/SystemDesignTradeoffPlanServiceTest.php</code></li>
                </ul>
            </article>
        </div>
    </section>

    <script>
        const systemDesignTradeoffPresets = {
            liveNotifications: {
                scenario: 'notification-10m',
                latency_requirement: 'real-time',
                failure_impact: 'high',
                consistency_need: 'eventual',
                team_maturity: 'platform',
                operational_capacity: 'high',
                current_constraint: 'greenfield',
                target_level: 'l6',
                candidate_answer: '',
            },
            simpleNotifications: {
                scenario: 'notification-10m',
                latency_requirement: 'delayed',
                failure_impact: 'low',
                consistency_need: 'eventual',
                team_maturity: 'small',
                operational_capacity: 'low',
                current_constraint: 'fast-delivery',
                target_level: 'l5',
                candidate_answer: '',
            },
            payments: {
                scenario: 'payment-flow',
                latency_requirement: 'near-real-time',
                failure_impact: 'high',
                consistency_need: 'strong',
                team_maturity: 'medium',
                operational_capacity: 'medium',
                current_constraint: 'regulated',
                target_level: 'l6',
                candidate_answer: '',
            },
            legacyScale: {
                scenario: 'legacy-scale',
                latency_requirement: 'delayed',
                failure_impact: 'medium',
                consistency_need: 'strong',
                team_maturity: 'medium',
                operational_capacity: 'low',
                current_constraint: 'legacy-database',
                target_level: 'l7',
                candidate_answer: '',
            },
        };

        const systemDesignTradeoffPreset = document.querySelector('#systemDesignTradeoffPreset');
        const systemDesignTradeoffForm = document.querySelector('#systemDesignTradeoffForm');
        const systemDesignTradeoffStatus = document.querySelector('#systemDesignTradeoffStatus');
        const systemDesignTradeoffOutput = document.querySelector('#systemDesignTradeoffOutput');
        const systemDesignTradeoffSummary = document.querySelector('#systemDesignTradeoffSummary');
        const systemDesignTradeoffMemo = document.querySelector('#systemDesignTradeoffMemo');
        const systemDesignTradeoffPacket = document.querySelector('#systemDesignTradeoffPacket');
        const systemDesignTradeoffReview = document.querySelector('#systemDesignTradeoffReview');
        const copySystemDesignTradeoffMemo = document.querySelector('#copySystemDesignTradeoffMemo');
        const copySystemDesignTradeoffPacket = document.querySelector('#copySystemDesignTradeoffPacket');
        const copySystemDesignTradeoffReview = document.querySelector('#copySystemDesignTradeoffReview');

        function applySystemDesignTradeoffPreset(presetName) {
            const preset = systemDesignTradeoffPresets[presetName];

            if (!preset) {
                return;
            }

            Object.entries(preset).forEach(([field, value]) => {
                const input = systemDesignTradeoffForm.elements.namedItem(field);

                if (input) {
                    input.value = value;
                }
            });
        }

        function escapeSystemDesignTradeoffHtml(value) {
            return String(value ?? '')
                .replaceAll('&', '&amp;')
                .replaceAll('<', '&lt;')
                .replaceAll('>', '&gt;')
                .replaceAll('"', '&quot;')
                .replaceAll("'", '&#039;');
        }

        function renderSystemDesignTradeoffSummary(data) {
            const levels = data.level_framing
                .map((item) => `<li><strong>${escapeSystemDesignTradeoffHtml(item.level)}</strong>: ${escapeSystemDesignTradeoffHtml(item.signal)}</li>`)
                .join('');
            const checks = data.three_question_check
                .map((item) => `<li><strong>${escapeSystemDesignTradeoffHtml(item.question)}</strong><br>${escapeSystemDesignTradeoffHtml(item.answer)}</li>`)
                .join('');
            const metrics = data.operating_model.metrics
                .map((metric) => `<li>${escapeSystemDesignTradeoffHtml(metric)}</li>`)
                .join('');
            const followups = data.interviewer_followups
                .map((question) => `<li>${escapeSystemDesignTradeoffHtml(question)}</li>`)
                .join('');
            const simulation = data.interviewer_simulation
                .map((item) => `<li><strong>${escapeSystemDesignTradeoffHtml(item.interviewer)}</strong><br><span class="muted">${escapeSystemDesignTradeoffHtml(item.intent)}</span><br>${escapeSystemDesignTradeoffHtml(item.strong_response)}</li>`)
                .join('');
            const rubric = data.answer_scorecard.dimensions
                .map((item) => `<li><strong>${escapeSystemDesignTradeoffHtml(item.name)} (${escapeSystemDesignTradeoffHtml(item.points)} pts)</strong><br>${escapeSystemDesignTradeoffHtml(item.evidence)}</li>`)
                .join('');
            const antiPatterns = data.anti_patterns
                .map((item) => `<li><strong>${escapeSystemDesignTradeoffHtml(item.pattern)}</strong><br>${escapeSystemDesignTradeoffHtml(item.better_move)}</li>`)
                .join('');
            const evolution = data.architecture_evolution_path
                .map((item) => `<li><strong>${escapeSystemDesignTradeoffHtml(item.phase)}</strong><br>${escapeSystemDesignTradeoffHtml(item.decision)}</li>`)
                .join('');
            const contract = Object.entries(data.answer_contract.filled_example)
                .map(([key, value]) => `<li><strong>${escapeSystemDesignTradeoffHtml(key)}</strong>: ${escapeSystemDesignTradeoffHtml(value)}</li>`)
                .join('');
            const checklist = data.review_checklist
                .map((item) => `<li><strong>${escapeSystemDesignTradeoffHtml(item.item)}</strong><br>${escapeSystemDesignTradeoffHtml(item.pass_condition)}</li>`)
                .join('');
            const calibration = data.calibration_examples
                .map((item) => `<li><strong>${escapeSystemDesignTradeoffHtml(item.level)}</strong>: ${escapeSystemDesignTradeoffHtml(item.answer)}</li>`)
                .join('');
            const drillCards = data.drill_cards
                .map((item) => `<li><strong>${escapeSystemDesignTradeoffHtml(item.front)}</strong><br>${escapeSystemDesignTradeoffHtml(item.back)}</li>`)
                .join('');
            const decisionTree = data.decision_tree
                .map((item) => `<li><strong>${escapeSystemDesignTradeoffHtml(item.question)}</strong><br>${escapeSystemDesignTradeoffHtml(item.current_answer)} - ${escapeSystemDesignTradeoffHtml(item.implication)}</li>`)
                .join('');
            const variations = data.scenario_variations
                .map((item) => `<li><strong>${escapeSystemDesignTradeoffHtml(item.change)}</strong><br>${escapeSystemDesignTradeoffHtml(item.expected_shift)}</li>`)
                .join('');
            const scoreBands = data.score_interpretation
                .map((item) => `<li><strong>${escapeSystemDesignTradeoffHtml(item.range)}</strong>: ${escapeSystemDesignTradeoffHtml(item.signal)}</li>`)
                .join('');
            const targetLevelItems = data.target_level_plan.must_add
                .map((item) => `<li>${escapeSystemDesignTradeoffHtml(item)}</li>`)
                .join('');
            const candidateMatched = data.candidate_answer_review.matched
                .map((item) => `<li>${escapeSystemDesignTradeoffHtml(item)}</li>`)
                .join('');
            const candidateMissing = data.candidate_answer_review.missing
                .map((item) => `<li>${escapeSystemDesignTradeoffHtml(item)}</li>`)
                .join('');
            const candidateEvidence = data.candidate_answer_review.evidence_spans
                .map((item) => `<li><strong>${escapeSystemDesignTradeoffHtml(item.dimension)}</strong><br>${escapeSystemDesignTradeoffHtml(item.evidence)}</li>`)
                .join('');
            const candidateRewriteOutline = data.candidate_answer_review.rewrite_outline
                .map((item) => `<li>${escapeSystemDesignTradeoffHtml(item)}</li>`)
                .join('');
            const candidateGapDrills = data.candidate_answer_review.gap_drills
                .map((item) => `<li><strong>${escapeSystemDesignTradeoffHtml(item.gap)}</strong><br>${escapeSystemDesignTradeoffHtml(item.drill)}</li>`)
                .join('');

            systemDesignTradeoffSummary.innerHTML = `
                <div class="item">
                    <div class="meta">
                        <span class="badge done">${escapeSystemDesignTradeoffHtml(data.recommendation.style)}</span>
                    </div>
                    <h3>${escapeSystemDesignTradeoffHtml(data.recommendation.label)}</h3>
                    <p class="muted">${escapeSystemDesignTradeoffHtml(data.recommendation.reason)}</p>
                </div>
                <div class="item">
                    <h3>Tradeoff statement</h3>
                    <p>${escapeSystemDesignTradeoffHtml(data.tradeoff_statement)}</p>
                </div>
                <div class="item">
                    <h3>Three senior questions</h3>
                    <ul>${checks}</ul>
                </div>
                <div class="item">
                    <h3>Level framing</h3>
                    <ul>${levels}</ul>
                </div>
                <div class="item">
                    <h3>Operating model</h3>
                    <p class="muted">${escapeSystemDesignTradeoffHtml(data.operating_model.owner)}</p>
                    <ul>${metrics}</ul>
                </div>
                <div class="item">
                    <h3>Interviewer follow-ups</h3>
                    <ul>${followups}</ul>
                </div>
                <div class="item">
                    <h3>Interviewer simulation</h3>
                    <ul>${simulation}</ul>
                </div>
                <div class="item">
                    <h3>Answer scorecard</h3>
                    <p class="muted">Passing score: ${escapeSystemDesignTradeoffHtml(data.answer_scorecard.passing_score)} / ${escapeSystemDesignTradeoffHtml(data.answer_scorecard.max_score)}</p>
                    <ul>${rubric}</ul>
                </div>
                <div class="item">
                    <h3>Anti-patterns</h3>
                    <ul>${antiPatterns}</ul>
                </div>
                <div class="item">
                    <h3>Evolution path</h3>
                    <ul>${evolution}</ul>
                </div>
                <div class="item">
                    <h3>Answer contract</h3>
                    <ul>${contract}</ul>
                </div>
                <div class="item">
                    <h3>Review checklist</h3>
                    <ul>${checklist}</ul>
                </div>
                <div class="item">
                    <h3>Calibration examples</h3>
                    <ul>${calibration}</ul>
                </div>
                <div class="item">
                    <h3>Timed answers</h3>
                    <p><strong>60s:</strong> ${escapeSystemDesignTradeoffHtml(data.one_minute_answer)}</p>
                    <p><strong>2m:</strong> ${escapeSystemDesignTradeoffHtml(data.two_minute_answer)}</p>
                </div>
                <div class="item">
                    <h3>Drill cards</h3>
                    <ul>${drillCards}</ul>
                </div>
                <div class="item">
                    <h3>Decision tree</h3>
                    <ul>${decisionTree}</ul>
                </div>
                <div class="item">
                    <h3>Scenario variations</h3>
                    <ul>${variations}</ul>
                </div>
                <div class="item">
                    <h3>Score interpretation</h3>
                    <ul>${scoreBands}</ul>
                </div>
                <div class="item">
                    <h3>Target level plan</h3>
                    <p><strong>${escapeSystemDesignTradeoffHtml(data.target_level_plan.target_level)}</strong>: ${escapeSystemDesignTradeoffHtml(data.target_level_plan.expected_signal)}</p>
                    <p class="muted">${escapeSystemDesignTradeoffHtml(data.target_level_plan.answer_opening)}</p>
                    <ul>${targetLevelItems}</ul>
                </div>
                <div class="item">
                    <h3>Candidate answer review</h3>
                    <p><strong>${escapeSystemDesignTradeoffHtml(data.candidate_answer_review.band)}</strong>: ${escapeSystemDesignTradeoffHtml(data.candidate_answer_review.score)} / ${escapeSystemDesignTradeoffHtml(data.candidate_answer_review.max_score)}</p>
                    <p class="muted">${escapeSystemDesignTradeoffHtml(data.candidate_answer_review.next_rewrite)}</p>
                    <h4>Matched</h4>
                    <ul>${candidateMatched || '<li>None yet</li>'}</ul>
                    <h4>Missing</h4>
                    <ul>${candidateMissing || '<li>None</li>'}</ul>
                    <h4>Evidence</h4>
                    <ul>${candidateEvidence || '<li>No evidence matched yet</li>'}</ul>
                    <h4>Rewrite outline</h4>
                    <ul>${candidateRewriteOutline}</ul>
                    <h4>Gap drills</h4>
                    <ul>${candidateGapDrills}</ul>
                </div>
            `;
        }

        systemDesignTradeoffPreset.addEventListener('change', (event) => {
            applySystemDesignTradeoffPreset(event.target.value);
        });

        applySystemDesignTradeoffPreset(systemDesignTradeoffPreset.value);

        systemDesignTradeoffForm.addEventListener('submit', async (event) => {
            event.preventDefault();

            const payload = Object.fromEntries(new FormData(systemDesignTradeoffForm).entries());
            systemDesignTradeoffStatus.textContent = 'Planning...';

            try {
                const response = await fetch('/api/practice/system-design-tradeoff-plan', {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(payload),
                });

                const body = await response.json();

                if (!response.ok) {
                    throw new Error(JSON.stringify(body, null, 2));
                }

                systemDesignTradeoffOutput.textContent = JSON.stringify(body.data, null, 2);
                systemDesignTradeoffMemo.textContent = body.data.decision_memo_markdown;
                systemDesignTradeoffPacket.textContent = body.data.interview_packet_markdown;
                systemDesignTradeoffReview.textContent = body.data.candidate_answer_review.review_markdown;
                renderSystemDesignTradeoffSummary(body.data);
                systemDesignTradeoffStatus.textContent = 'Plan generated.';
            } catch (error) {
                systemDesignTradeoffStatus.textContent = 'Request failed.';
                systemDesignTradeoffOutput.textContent = error.message;
            }
        });

        copySystemDesignTradeoffMemo.addEventListener('click', async () => {
            await navigator.clipboard.writeText(systemDesignTradeoffMemo.textContent);
            copySystemDesignTradeoffMemo.textContent = 'Copied';
            setTimeout(() => {
                copySystemDesignTradeoffMemo.textContent = 'Copy memo';
            }, 1400);
        });

        copySystemDesignTradeoffPacket.addEventListener('click', async () => {
            await navigator.clipboard.writeText(systemDesignTradeoffPacket.textContent);
            copySystemDesignTradeoffPacket.textContent = 'Copied';
            setTimeout(() => {
                copySystemDesignTradeoffPacket.textContent = 'Copy packet';
            }, 1400);
        });

        copySystemDesignTradeoffReview.addEventListener('click', async () => {
            await navigator.clipboard.writeText(systemDesignTradeoffReview.textContent);
            copySystemDesignTradeoffReview.textContent = 'Copied';
            setTimeout(() => {
                copySystemDesignTradeoffReview.textContent = 'Copy review';
            }, 1400);
        });
    </script>
@endsection
