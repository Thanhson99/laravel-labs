@extends('learning.layout', ['title' => 'JavaScript Hoisting Lab'])

@section('content')
    <section class="hero">
        <span class="badge">Runnable workbench</span>
        <h1>Trace JavaScript hoisting before interview day.</h1>
        <p>
            Paste a snippet and get a plain explanation, execution trace, safer rewrite,
            interview answer, and project guidelines for hoisting behavior.
        </p>
    </section>

    <section class="section">
        <div class="grid">
            <article class="panel">
                <h2>Snippet</h2>
                <form id="hoistingForm">
                    <label>
                        JavaScript snippet
                        <textarea name="snippet" rows="13" required>sayHi();

function sayHi() {
  console.log('Hello');
}

console.log(userName);
var userName = 'Son';

// console.log(score);
let score = 10;

// run();
var run = function () {
  console.log('Assigned later');
};</textarea>
                    </label>
                    <label style="margin-top: 12px;">
                        Focus
                        <select name="focus">
                            <option value="mixed">mixed</option>
                            <option value="var-let-const">var-let-const</option>
                            <option value="function-declaration">function-declaration</option>
                            <option value="function-expression">function-expression</option>
                            <option value="temporal-dead-zone">temporal-dead-zone</option>
                        </select>
                    </label>
                    <label style="margin-top: 12px;">
                        Interview level
                        <select name="interview_level">
                            <option value="junior">junior</option>
                            <option value="middle">middle</option>
                            <option value="senior">senior</option>
                        </select>
                    </label>
                    <button class="button primary" type="submit" style="margin-top: 14px;">Analyze hoisting</button>
                </form>
            </article>

            <article class="panel">
                <h2>Analysis</h2>
                <p class="muted" id="hoistingStatus">Submit the snippet to analyze hoisting behavior.</p>
                <pre class="raw-json"><code id="hoistingOutput">POST /api/practice/javascript-hoisting-lab</code></pre>
            </article>

            <article class="panel">
                <h2>Execution Trace</h2>
                <div id="hoistingTrace" class="list">
                    <p class="muted">Run the lab to see creation, initialization, and execution phases.</p>
                </div>
            </article>

            <article class="panel">
                <h2>Interview Answer</h2>
                <div id="hoistingInterview" class="list">
                    <p class="muted">Run the lab to generate an interview-ready answer.</p>
                </div>
            </article>

            <article class="panel">
                <h2>Interview Rubric</h2>
                <div id="hoistingRubric" class="list">
                    <p class="muted">Run the lab to see how to score a hoisting answer.</p>
                </div>
            </article>

            <article class="panel">
                <h2>Interview Drill Plan</h2>
                <div id="hoistingDrillPlan" class="list">
                    <p class="muted">Run the lab to generate a short practice sequence.</p>
                </div>
            </article>

            <article class="panel">
                <h2>Prediction Quiz</h2>
                <div id="hoistingQuiz" class="list">
                    <p class="muted">Run the lab to generate output prediction prompts.</p>
                </div>
            </article>

            <article class="panel">
                <h2>Knowledge Check</h2>
                <div id="hoistingKnowledgeCheck" class="list">
                    <p class="muted">Run the lab to generate quick self-check questions.</p>
                </div>
            </article>

            <article class="panel">
                <h2>Debug Checklist</h2>
                <div id="hoistingDebugChecklist" class="list">
                    <p class="muted">Run the lab to generate review checks for hoisting-related bugs.</p>
                </div>
            </article>

            <article class="panel">
                <h2>Bug Report Template</h2>
                <div id="hoistingBugReport" class="list">
                    <p class="muted">Run the lab to generate an issue-ready bug report template.</p>
                </div>
            </article>

            <article class="panel">
                <h2>Anti-patterns</h2>
                <div id="hoistingAntiPatterns" class="list">
                    <p class="muted">Run the lab to see common hoisting mistakes and corrections.</p>
                </div>
            </article>

            <article class="panel">
                <h2>Test Cases</h2>
                <div id="hoistingTestCases" class="list">
                    <p class="muted">Run the lab to generate assertions for practice or live coding.</p>
                </div>
            </article>

            <article class="panel">
                <h2>Runtime Examples</h2>
                <div id="hoistingRuntimeExamples" class="list">
                    <p class="muted">Run the lab to see isolated runnable examples.</p>
                </div>
            </article>

            <article class="panel">
                <h2>Module Notes</h2>
                <div id="hoistingModuleNotes" class="list">
                    <p class="muted">Run the lab to connect hoisting with ES module behavior.</p>
                </div>
            </article>

            <article class="panel">
                <h2>Refactor Plan</h2>
                <div id="hoistingRefactorPlan" class="list">
                    <p class="muted">Run the lab to generate a safe refactor sequence.</p>
                </div>
            </article>

            <article class="panel">
                <h2>Team Review Prompts</h2>
                <div id="hoistingTeamPrompts" class="list">
                    <p class="muted">Run the lab to generate PR review prompts.</p>
                </div>
            </article>

            <article class="panel">
                <h2>Reviewer Notes</h2>
                <div id="hoistingReviewerNotes" class="list">
                    <p class="muted">Run the lab to generate code-review comments and merge requirements.</p>
                </div>
            </article>

            <article class="panel">
                <h2>ESLint Suggestions</h2>
                <div id="hoistingEslint" class="list">
                    <p class="muted">Run the lab to see lint rules that reduce hoisting bugs.</p>
                </div>
            </article>

            <article class="panel">
                <h2>Commit Plan</h2>
                <div id="hoistingCommitPlan" class="list">
                    <p class="muted">Run the lab to generate a PR-sized implementation plan.</p>
                </div>
            </article>

            <article class="panel">
                <h2>Flashcards</h2>
                <div id="hoistingFlashcards" class="list">
                    <p class="muted">Run the lab to generate spaced-review cards.</p>
                </div>
            </article>

            <article class="panel">
                <h2>Safer Rewrite</h2>
                <button class="button" id="copyHoistingRewrite" type="button">Copy rewrite</button>
                <pre class="raw-json" style="margin-top: 12px;"><code id="hoistingRewrite">No rewrite yet.</code></pre>
            </article>

            <article class="panel">
                <h2>Study Memo</h2>
                <button class="button" id="copyHoistingMemo" type="button">Copy memo</button>
                <pre class="raw-json" style="margin-top: 12px;"><code id="hoistingMemo">No memo yet.</code></pre>
            </article>

            <article class="panel">
                <h2>Files To Read</h2>
                <ul>
                    <li><code>app/Services/Practice/JavascriptHoistingLabService.php</code></li>
                    <li><code>app/Http/Requests/Api/AnalyzeJavascriptHoistingRequest.php</code></li>
                    <li><code>routes/api/practice-actions.php</code></li>
                    <li><code>tests/Feature/JavascriptHoistingLabWorkbenchTest.php</code></li>
                </ul>
            </article>
        </div>
    </section>

    <script>
        const hoistingForm = document.querySelector('#hoistingForm');
        const hoistingStatus = document.querySelector('#hoistingStatus');
        const hoistingOutput = document.querySelector('#hoistingOutput');
        const hoistingTrace = document.querySelector('#hoistingTrace');
        const hoistingInterview = document.querySelector('#hoistingInterview');
        const hoistingRubric = document.querySelector('#hoistingRubric');
        const hoistingDrillPlan = document.querySelector('#hoistingDrillPlan');
        const hoistingQuiz = document.querySelector('#hoistingQuiz');
        const hoistingKnowledgeCheck = document.querySelector('#hoistingKnowledgeCheck');
        const hoistingDebugChecklist = document.querySelector('#hoistingDebugChecklist');
        const hoistingBugReport = document.querySelector('#hoistingBugReport');
        const hoistingAntiPatterns = document.querySelector('#hoistingAntiPatterns');
        const hoistingTestCases = document.querySelector('#hoistingTestCases');
        const hoistingRuntimeExamples = document.querySelector('#hoistingRuntimeExamples');
        const hoistingModuleNotes = document.querySelector('#hoistingModuleNotes');
        const hoistingRefactorPlan = document.querySelector('#hoistingRefactorPlan');
        const hoistingTeamPrompts = document.querySelector('#hoistingTeamPrompts');
        const hoistingReviewerNotes = document.querySelector('#hoistingReviewerNotes');
        const hoistingEslint = document.querySelector('#hoistingEslint');
        const hoistingCommitPlan = document.querySelector('#hoistingCommitPlan');
        const hoistingFlashcards = document.querySelector('#hoistingFlashcards');
        const hoistingRewrite = document.querySelector('#hoistingRewrite');
        const hoistingMemo = document.querySelector('#hoistingMemo');
        const copyHoistingRewrite = document.querySelector('#copyHoistingRewrite');
        const copyHoistingMemo = document.querySelector('#copyHoistingMemo');

        function escapeHoistingHtml(value) {
            return String(value ?? '')
                .replaceAll('&', '&amp;')
                .replaceAll('<', '&lt;')
                .replaceAll('>', '&gt;')
                .replaceAll('"', '&quot;')
                .replaceAll("'", '&#039;');
        }

        function renderHoistingTrace(data) {
            const trace = data.execution_trace
                .map((step) => `<li><strong>${escapeHoistingHtml(step.phase)}</strong>: ${escapeHoistingHtml(step.detail)}</li>`)
                .join('');
            const checks = data.practice_checks
                .map((check) => `<li>${escapeHoistingHtml(check)}</li>`)
                .join('');

            hoistingTrace.innerHTML = `
                <div class="item">
                    <div class="meta">
                        <span class="badge">${escapeHoistingHtml(data.focus)}</span>
                        <span class="badge done">${escapeHoistingHtml(data.risk_score)}/100 risk</span>
                    </div>
                    <h3>${escapeHoistingHtml(data.plain_explanation)}</h3>
                </div>
                <div class="item">
                    <h3>Trace</h3>
                    <ul>${trace}</ul>
                </div>
                <div class="item">
                    <h3>Practice Checks</h3>
                    <ul>${checks}</ul>
                </div>
            `;
        }

        function renderHoistingInterview(data) {
            const guidelines = data.project_guidelines
                .map((guideline) => `<li>${escapeHoistingHtml(guideline)}</li>`)
                .join('');

            hoistingInterview.innerHTML = `
                <div class="item">
                    <h3>${escapeHoistingHtml(data.interview_level)} answer</h3>
                    <p class="muted">${escapeHoistingHtml(data.interview_answer)}</p>
                </div>
                <div class="item">
                    <h3>Project Guidelines</h3>
                    <ul>${guidelines}</ul>
                </div>
            `;
        }

        function renderHoistingRubric(data) {
            const criteria = data.interview_rubric.criteria
                .map((criterion) => `
                    <li>
                        <strong>${escapeHoistingHtml(criterion.name)}</strong>
                        <code>${escapeHoistingHtml(criterion.points)} pts</code>
                        <span class="muted">${escapeHoistingHtml(criterion.evidence)}</span>
                    </li>
                `)
                .join('');

            hoistingRubric.innerHTML = `
                <div class="item">
                    <div class="meta">
                        <span class="badge done">${escapeHoistingHtml(data.interview_rubric.passing_score)}/${escapeHoistingHtml(data.interview_rubric.max_score)} pass</span>
                    </div>
                    <h3>Answer scoring criteria</h3>
                    <ul>${criteria}</ul>
                </div>
            `;
        }

        function renderHoistingDrillPlan(data) {
            const rounds = data.interview_drill_plan.rounds
                .map((round) => `
                    <li>
                        <strong>${escapeHoistingHtml(round.round)}</strong>
                        <span class="muted">${escapeHoistingHtml(round.goal)}</span>
                        <code>${escapeHoistingHtml(round.exercise)}</code>
                        <span class="muted">${escapeHoistingHtml(round.pass_condition)}</span>
                    </li>
                `)
                .join('');
            const followUp = data.interview_drill_plan.follow_up
                .map((item) => `<li>${escapeHoistingHtml(item)}</li>`)
                .join('');

            hoistingDrillPlan.innerHTML = `
                <div class="item">
                    <div class="meta">
                        <span class="badge">${escapeHoistingHtml(data.interview_drill_plan.timebox)}</span>
                    </div>
                    <h3>Practice Sequence</h3>
                    <ul>${rounds}</ul>
                </div>
                <div class="item">
                    <h3>Follow-up</h3>
                    <ul>${followUp}</ul>
                </div>
            `;
        }

        function renderHoistingQuiz(data) {
            const prompts = data.prediction_quiz
                .map((item) => `
                    <li>
                        <strong>${escapeHoistingHtml(item.prompt)}</strong>
                        <code>${escapeHoistingHtml(item.expected)}</code>
                        <span class="muted">${escapeHoistingHtml(item.explanation)}</span>
                    </li>
                `)
                .join('');

            hoistingQuiz.innerHTML = `
                <div class="item">
                    <h3>Predict before you run</h3>
                    <ul>${prompts}</ul>
                </div>
            `;
        }

        function renderHoistingKnowledgeCheck(data) {
            const questions = data.knowledge_check
                .map((question) => {
                    const options = question.options
                        .map((option) => `<li>${escapeHoistingHtml(option)}</li>`)
                        .join('');

                    return `
                        <div class="item">
                            <div class="meta">
                                <span class="badge">${escapeHoistingHtml(question.tag)}</span>
                            </div>
                            <h3>${escapeHoistingHtml(question.prompt)}</h3>
                            <ul>${options}</ul>
                            <p class="muted"><strong>Answer:</strong> ${escapeHoistingHtml(question.answer)}</p>
                            <p class="muted">${escapeHoistingHtml(question.explanation)}</p>
                        </div>
                    `;
                })
                .join('');

            hoistingKnowledgeCheck.innerHTML = questions;
        }

        function renderHoistingDebugChecklist(data) {
            const checks = data.debug_checklist
                .map((item) => `
                    <li>
                        <strong>${escapeHoistingHtml(item.check)}</strong>
                        <span class="muted">${escapeHoistingHtml(item.reason)}</span>
                        <code>${escapeHoistingHtml(item.action)}</code>
                    </li>
                `)
                .join('');

            hoistingDebugChecklist.innerHTML = `
                <div class="item">
                    <h3>Before Merge</h3>
                    <ul>${checks}</ul>
                </div>
            `;
        }

        function renderHoistingBugReport(data) {
            const reproductionSteps = data.bug_report_template.reproduction_steps
                .map((step) => `<li>${escapeHoistingHtml(step)}</li>`)
                .join('');
            const proposedFix = data.bug_report_template.proposed_fix
                .map((fix) => `<li>${escapeHoistingHtml(fix)}</li>`)
                .join('');
            const verification = data.bug_report_template.verification
                .map((check) => `<li>${escapeHoistingHtml(check)}</li>`)
                .join('');

            hoistingBugReport.innerHTML = `
                <div class="item">
                    <div class="meta">
                        <span class="badge">${escapeHoistingHtml(data.bug_report_template.severity)}</span>
                    </div>
                    <h3>${escapeHoistingHtml(data.bug_report_template.title)}</h3>
                    <p class="muted">${escapeHoistingHtml(data.bug_report_template.summary)}</p>
                    <p class="muted">${escapeHoistingHtml(data.bug_report_template.suspected_cause)}</p>
                    <p class="muted">${escapeHoistingHtml(data.bug_report_template.user_impact)}</p>
                </div>
                <div class="item">
                    <h3>Reproduction Steps</h3>
                    <ul>${reproductionSteps}</ul>
                </div>
                <div class="item">
                    <h3>Proposed Fix</h3>
                    <ul>${proposedFix}</ul>
                </div>
                <div class="item">
                    <h3>Verification</h3>
                    <ul>${verification}</ul>
                </div>
            `;
        }

        function renderHoistingAntiPatterns(data) {
            const items = data.anti_patterns
                .map((item) => `
                    <li>
                        <strong>${escapeHoistingHtml(item.mistake)}</strong>
                        <span class="muted">${escapeHoistingHtml(item.correction)}</span>
                    </li>
                `)
                .join('');

            hoistingAntiPatterns.innerHTML = `
                <div class="item">
                    <h3>Correct the misconception</h3>
                    <ul>${items}</ul>
                </div>
            `;
        }

        function renderHoistingTestCases(data) {
            const cases = data.test_cases
                .map((testCase) => `
                    <li>
                        <strong>${escapeHoistingHtml(testCase.name)}</strong>
                        <span class="muted">${escapeHoistingHtml(testCase.assertion)}</span>
                        <code>${escapeHoistingHtml(testCase.expected)}</code>
                    </li>
                `)
                .join('');

            hoistingTestCases.innerHTML = `
                <div class="item">
                    <h3>Assertions</h3>
                    <ul>${cases}</ul>
                </div>
            `;
        }

        function renderHoistingRuntimeExamples(data) {
            const examples = data.runtime_examples
                .map((example) => `
                    <div class="item">
                        <div class="meta">
                            <span class="badge">${escapeHoistingHtml(example.label)}</span>
                        </div>
                        <pre class="raw-json"><code>${escapeHoistingHtml(example.code)}</code></pre>
                        <p class="muted">${escapeHoistingHtml(example.expected)}</p>
                    </div>
                `)
                .join('');

            hoistingRuntimeExamples.innerHTML = examples;
        }

        function renderHoistingModuleNotes(data) {
            const notes = data.module_notes
                .map((note) => `
                    <li>
                        <strong>${escapeHoistingHtml(note.topic)}</strong>
                        <span class="muted">${escapeHoistingHtml(note.note)}</span>
                        <code>${escapeHoistingHtml(note.project_check)}</code>
                    </li>
                `)
                .join('');

            hoistingModuleNotes.innerHTML = `
                <div class="item">
                    <h3>Modern Module Context</h3>
                    <ul>${notes}</ul>
                </div>
            `;
        }

        function renderHoistingRefactorPlan(data) {
            const steps = data.refactor_plan
                .map((step) => `
                    <li>
                        <strong>${escapeHoistingHtml(step.step)}</strong>
                        <span class="muted">${escapeHoistingHtml(step.change)}</span>
                        <code>${escapeHoistingHtml(step.verification)}</code>
                    </li>
                `)
                .join('');

            hoistingRefactorPlan.innerHTML = `
                <div class="item">
                    <h3>Safe Change Order</h3>
                    <ul>${steps}</ul>
                </div>
            `;
        }

        function renderHoistingTeamPrompts(data) {
            const prompts = data.team_review_prompts
                .map((prompt) => `
                    <li>
                        <strong>${escapeHoistingHtml(prompt.prompt)}</strong>
                        <span class="muted">${escapeHoistingHtml(prompt.why)}</span>
                    </li>
                `)
                .join('');

            hoistingTeamPrompts.innerHTML = `
                <div class="item">
                    <h3>PR Questions</h3>
                    <ul>${prompts}</ul>
                </div>
            `;
        }

        function renderHoistingReviewerNotes(data) {
            const blockingComments = data.reviewer_notes.blocking_comments
                .map((comment) => `<li>${escapeHoistingHtml(comment)}</li>`)
                .join('');
            const authorQuestions = data.reviewer_notes.author_questions
                .map((question) => `<li>${escapeHoistingHtml(question)}</li>`)
                .join('');
            const mergeRequirements = data.reviewer_notes.merge_requirements
                .map((requirement) => `<li>${escapeHoistingHtml(requirement)}</li>`)
                .join('');

            hoistingReviewerNotes.innerHTML = `
                <div class="item">
                    <div class="meta">
                        <span class="badge">${escapeHoistingHtml(data.reviewer_notes.decision)}</span>
                        <span class="badge done">${escapeHoistingHtml(data.reviewer_notes.priority)}</span>
                    </div>
                    <h3>Review Summary</h3>
                    <p class="muted">${escapeHoistingHtml(data.reviewer_notes.summary_comment)}</p>
                </div>
                <div class="item">
                    <h3>Blocking Comments</h3>
                    <ul>${blockingComments}</ul>
                </div>
                <div class="item">
                    <h3>Author Questions</h3>
                    <ul>${authorQuestions}</ul>
                </div>
                <div class="item">
                    <h3>Merge Requirements</h3>
                    <ul>${mergeRequirements}</ul>
                </div>
            `;
        }

        function renderHoistingEslint(data) {
            const rules = data.eslint_suggestions
                .map((rule) => `
                    <li>
                        <strong>${escapeHoistingHtml(rule.rule)}</strong>
                        <code>${escapeHoistingHtml(rule.level)}</code>
                        <span class="muted">${escapeHoistingHtml(rule.reason)}</span>
                    </li>
                `)
                .join('');

            hoistingEslint.innerHTML = `
                <div class="item">
                    <h3>Recommended Rules</h3>
                    <ul>${rules}</ul>
                </div>
            `;
        }

        function renderHoistingCommitPlan(data) {
            const files = data.commit_plan.changed_files
                .map((file) => `<li><code>${escapeHoistingHtml(file)}</code></li>`)
                .join('');
            const commits = data.commit_plan.commits
                .map((commit) => `<li><strong>${escapeHoistingHtml(commit.message)}</strong><span class="muted">${escapeHoistingHtml(commit.verification)}</span></li>`)
                .join('');
            const checklist = data.commit_plan.pull_request_checklist
                .map((item) => `<li>${escapeHoistingHtml(item)}</li>`)
                .join('');

            hoistingCommitPlan.innerHTML = `
                <div class="item">
                    <h3>${escapeHoistingHtml(data.commit_plan.branch)}</h3>
                    <ul>${files}</ul>
                </div>
                <div class="item">
                    <h3>Commits</h3>
                    <ul>${commits}</ul>
                </div>
                <div class="item">
                    <h3>Pull Request Checklist</h3>
                    <ul>${checklist}</ul>
                </div>
            `;
        }

        function renderHoistingFlashcards(data) {
            const cards = data.flashcards
                .map((card) => `
                    <div class="item">
                        <div class="meta">
                            <span class="badge">${escapeHoistingHtml(card.tag)}</span>
                        </div>
                        <h3>${escapeHoistingHtml(card.front)}</h3>
                        <p class="muted">${escapeHoistingHtml(card.back)}</p>
                    </div>
                `)
                .join('');

            hoistingFlashcards.innerHTML = cards;
        }

        hoistingForm.addEventListener('submit', async (event) => {
            event.preventDefault();
            const payload = Object.fromEntries(new FormData(hoistingForm).entries());

            hoistingStatus.textContent = 'Running POST /api/practice/javascript-hoisting-lab...';
            hoistingOutput.textContent = JSON.stringify(payload, null, 2);

            try {
                const response = await fetch('{{ route('api.practice.javascript-hoisting-lab.store') }}', {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(payload),
                });
                const body = await response.json();

                hoistingStatus.textContent = `HTTP ${response.status}`;
                hoistingOutput.textContent = JSON.stringify(body, null, 2);
                hoistingRewrite.textContent = body.data?.fixed_example ?? 'No rewrite returned.';
                hoistingMemo.textContent = body.data?.study_memo_markdown ?? 'No memo returned.';

                if (body.data) {
                    renderHoistingTrace(body.data);
                    renderHoistingInterview(body.data);
                    renderHoistingRubric(body.data);
                    renderHoistingDrillPlan(body.data);
                    renderHoistingQuiz(body.data);
                    renderHoistingKnowledgeCheck(body.data);
                    renderHoistingDebugChecklist(body.data);
                    renderHoistingBugReport(body.data);
                    renderHoistingAntiPatterns(body.data);
                    renderHoistingTestCases(body.data);
                    renderHoistingRuntimeExamples(body.data);
                    renderHoistingModuleNotes(body.data);
                    renderHoistingRefactorPlan(body.data);
                    renderHoistingTeamPrompts(body.data);
                    renderHoistingReviewerNotes(body.data);
                    renderHoistingEslint(body.data);
                    renderHoistingCommitPlan(body.data);
                    renderHoistingFlashcards(body.data);
                }
            } catch (error) {
                hoistingStatus.textContent = 'Request failed';
                hoistingOutput.textContent = error instanceof Error ? error.message : 'Unknown request error';
                hoistingRewrite.textContent = 'No rewrite available.';
                hoistingMemo.textContent = 'No memo available.';
                hoistingTrace.innerHTML = '<p class="muted">No trace available.</p>';
                hoistingInterview.innerHTML = '<p class="muted">No interview answer available.</p>';
                hoistingRubric.innerHTML = '<p class="muted">No interview rubric available.</p>';
                hoistingDrillPlan.innerHTML = '<p class="muted">No interview drill plan available.</p>';
                hoistingQuiz.innerHTML = '<p class="muted">No prediction quiz available.</p>';
                hoistingKnowledgeCheck.innerHTML = '<p class="muted">No knowledge check available.</p>';
                hoistingDebugChecklist.innerHTML = '<p class="muted">No debug checklist available.</p>';
                hoistingBugReport.innerHTML = '<p class="muted">No bug report template available.</p>';
                hoistingAntiPatterns.innerHTML = '<p class="muted">No anti-patterns available.</p>';
                hoistingTestCases.innerHTML = '<p class="muted">No test cases available.</p>';
                hoistingRuntimeExamples.innerHTML = '<p class="muted">No runtime examples available.</p>';
                hoistingModuleNotes.innerHTML = '<p class="muted">No module notes available.</p>';
                hoistingRefactorPlan.innerHTML = '<p class="muted">No refactor plan available.</p>';
                hoistingTeamPrompts.innerHTML = '<p class="muted">No team review prompts available.</p>';
                hoistingReviewerNotes.innerHTML = '<p class="muted">No reviewer notes available.</p>';
                hoistingEslint.innerHTML = '<p class="muted">No ESLint suggestions available.</p>';
                hoistingCommitPlan.innerHTML = '<p class="muted">No commit plan available.</p>';
                hoistingFlashcards.innerHTML = '<p class="muted">No flashcards available.</p>';
            }
        });

        copyHoistingRewrite.addEventListener('click', async () => {
            await navigator.clipboard.writeText(hoistingRewrite.textContent);
            copyHoistingRewrite.textContent = 'Copied';
            window.setTimeout(() => {
                copyHoistingRewrite.textContent = 'Copy rewrite';
            }, 1200);
        });

        copyHoistingMemo.addEventListener('click', async () => {
            await navigator.clipboard.writeText(hoistingMemo.textContent);
            copyHoistingMemo.textContent = 'Copied';
            window.setTimeout(() => {
                copyHoistingMemo.textContent = 'Copy memo';
            }, 1200);
        });
    </script>
@endsection
