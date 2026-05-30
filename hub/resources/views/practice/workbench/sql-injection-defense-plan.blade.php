@extends('learning.layout', ['title' => 'SQL Injection Defense Workbench'])

@section('content')
    <section class="hero">
        <span class="badge">Runnable workbench</span>
        <h1>SQL Injection defense starts with parameterized queries.</h1>
        <p>
            Practice explaining how SQL Injection works, why bindings stop value injection,
            and when dynamic identifiers need allowlists.
        </p>
    </section>

    <section class="section">
        <div class="grid">
            <article class="panel">
                <h2>Query Input</h2>
                <form id="sqlInjectionForm">
                    <label>Query name <input name="query_name" value="User Search"></label>
                    <label style="margin-top: 12px;">Query style
                        <select name="query_style">
                            <option value="query-builder" selected>query-builder</option>
                            <option value="eloquent">eloquent</option>
                            <option value="raw-sql">raw-sql</option>
                        </select>
                    </label>
                    <label style="margin-top: 12px;">Input surface
                        <select name="input_surface">
                            <option value="search-box" selected>search-box</option>
                            <option value="login-form">login-form</option>
                            <option value="filter-api">filter-api</option>
                            <option value="admin-report">admin-report</option>
                        </select>
                    </label>
                    <label style="margin-top: 12px;">Dynamic parts
                        <select name="dynamic_parts">
                            <option value="where-value" selected>where-value</option>
                            <option value="order-by">order-by</option>
                            <option value="table-name">table-name</option>
                            <option value="column-name">column-name</option>
                        </select>
                    </label>
                    <label style="margin-top: 12px;">
                        <input name="uses_bindings" type="checkbox" checked>
                        Uses parameter bindings
                    </label>
                    <button class="button primary" type="submit" style="margin-top: 14px;">Plan SQL defense</button>
                    <button class="button" id="copySqlInjectionPacket" type="button" style="margin-top: 14px;">Copy review packet</button>
                </form>
            </article>

            <article class="panel">
                <h2>Scenario Presets</h2>
                <div class="list">
                    <button class="button" type="button" data-sql-injection-preset="unsafe-search">Unsafe search SQL</button>
                    <button class="button" type="button" data-sql-injection-preset="safe-builder">Safe query builder</button>
                    <button class="button" type="button" data-sql-injection-preset="admin-report-sort">Admin report sort</button>
                </div>
            </article>

            <article class="panel">
                <h2>Defense Plan</h2>
                <p class="muted" id="sqlInjectionStatus">Submit input to generate a SQL Injection defense plan.</p>
                <pre class="raw-json"><code id="sqlInjectionOutput">POST /api/practice/sql-injection-defense-plan</code></pre>
            </article>

            <article class="panel">
                <h2>Summary</h2>
                <div id="sqlInjectionSummary" class="list">
                    <p class="muted">Run the planner to see risk, safe query patterns, test payloads, and interview answer.</p>
                </div>
            </article>
        </div>
    </section>

    <script>
        const sqlInjectionForm = document.querySelector('#sqlInjectionForm');
        const sqlInjectionStatus = document.querySelector('#sqlInjectionStatus');
        const sqlInjectionOutput = document.querySelector('#sqlInjectionOutput');
        const sqlInjectionSummary = document.querySelector('#sqlInjectionSummary');
        const copySqlInjectionPacket = document.querySelector('#copySqlInjectionPacket');
        const sqlInjectionPresetButtons = document.querySelectorAll('[data-sql-injection-preset]');
        let lastSqlInjectionPacket = '';

        const sqlInjectionPresets = {
            'unsafe-search': {
                query_name: 'User Search',
                query_style: 'raw-sql',
                input_surface: 'search-box',
                dynamic_parts: 'where-value',
                uses_bindings: false,
            },
            'safe-builder': {
                query_name: 'Customer Filter',
                query_style: 'query-builder',
                input_surface: 'filter-api',
                dynamic_parts: 'where-value',
                uses_bindings: true,
            },
            'admin-report-sort': {
                query_name: 'Admin Report',
                query_style: 'raw-sql',
                input_surface: 'admin-report',
                dynamic_parts: 'order-by',
                uses_bindings: false,
            },
        };

        function escapeSqlInjectionHtml(value) {
            return String(value ?? '')
                .replaceAll('&', '&amp;')
                .replaceAll('<', '&lt;')
                .replaceAll('>', '&gt;')
                .replaceAll('"', '&quot;')
                .replaceAll("'", '&#039;');
        }

        function renderSqlInjectionSummary(data) {
            lastSqlInjectionPacket = String(data.review_packet_markdown || '');
            const attacks = data.attack_explanation.map((item) => `<li>${escapeSqlInjectionHtml(item)}</li>`).join('');
            const unsafe = data.unsafe_patterns.map((item) => `<li>${escapeSqlInjectionHtml(item)}</li>`).join('');
            const fixExamples = data.fix_examples.map((item) => `
                <li>
                    <strong>${escapeSqlInjectionHtml(item.label)}</strong>
                    <p>${escapeSqlInjectionHtml(item.review_note)}</p>
                    <pre class="raw-json"><code>Before:
${escapeSqlInjectionHtml(item.before)}

After:
${escapeSqlInjectionHtml(item.after)}</code></pre>
                </li>
            `).join('');
            const allowlist = data.allowlist_review.map((item) => `<li>${escapeSqlInjectionHtml(item)}</li>`).join('');
            const tests = data.test_payloads.map((item) => `<li><code>${escapeSqlInjectionHtml(item.payload)}</code>: ${escapeSqlInjectionHtml(item.expected)}</li>`).join('');
            const testMatrix = data.test_matrix.map((item) => `<li><strong>${escapeSqlInjectionHtml(item.case)}</strong>: <code>${escapeSqlInjectionHtml(item.payload)}</code> -> ${escapeSqlInjectionHtml(item.assertion)}</li>`).join('');
            const defenseTaxonomy = data.defense_taxonomy.map((item) => `
                <li>
                    <strong>${escapeSqlInjectionHtml(item.category)}</strong>
                    <p>${escapeSqlInjectionHtml(item.binding_rule)}</p>
                    <p><strong>Examples:</strong> ${item.examples.map((example) => `<code>${escapeSqlInjectionHtml(example)}</code>`).join(' ')}</p>
                    <p><strong>Review control:</strong> ${escapeSqlInjectionHtml(item.review_control)}</p>
                    <p><strong>Common mistake:</strong> ${escapeSqlInjectionHtml(item.common_mistake)}</p>
                </li>
            `).join('');
            const threatModel = data.threat_model.map((item) => `<li><strong>${escapeSqlInjectionHtml(item.boundary)}</strong>: ${escapeSqlInjectionHtml(item.risk)} Control: ${escapeSqlInjectionHtml(item.control)}</li>`).join('');
            const reviewQuestions = data.review_questions.map((item) => `<li>${escapeSqlInjectionHtml(item)}</li>`).join('');
            const rolloutSteps = data.rollout_steps.map((item) => `<li>${escapeSqlInjectionHtml(item)}</li>`).join('');
            const checklist = data.laravel_review_checklist.map((item) => `<li>${escapeSqlInjectionHtml(item)}</li>`).join('');
            const searchTerms = data.search_terms.map((item) => `<code>${escapeSqlInjectionHtml(item)}</code>`).join(' ');
            const readinessBlockers = data.readiness_score.blockers.length
                ? data.readiness_score.blockers.map((item) => `<li>${escapeSqlInjectionHtml(item)}</li>`).join('')
                : '<li>No merge-blocking SQL Injection issue is currently detected.</li>';
            const readinessActions = data.readiness_score.next_actions.map((item) => `<li>${escapeSqlInjectionHtml(item)}</li>`).join('');
            const mergeEvidence = data.merge_gate.required_evidence.map((item) => `<li>${escapeSqlInjectionHtml(item)}</li>`).join('');
            const mergeChecks = data.merge_gate.ci_checks.map((item) => `<li><code>${escapeSqlInjectionHtml(item)}</code></li>`).join('');

            sqlInjectionSummary.innerHTML = `
                <div class="item">
                    <div class="meta">
                        <span class="badge pending">${escapeSqlInjectionHtml(data.risk_score.label)} ${escapeSqlInjectionHtml(data.risk_score.score)}/100</span>
                        <span class="badge">${escapeSqlInjectionHtml(data.readiness_score.label)} ${escapeSqlInjectionHtml(data.readiness_score.score)}/100</span>
                        <span class="badge">${escapeSqlInjectionHtml(data.merge_gate.decision)}</span>
                        <span class="badge">${escapeSqlInjectionHtml(data.query)}</span>
                    </div>
                    <h3>${escapeSqlInjectionHtml(data.recommendation)}</h3>
                    <p>${escapeSqlInjectionHtml(data.interview_answer)}</p>
                </div>
                <div class="item"><h3>Readiness</h3><ul>${readinessBlockers}</ul><h4>Next actions</h4><ul>${readinessActions}</ul></div>
                <div class="item"><h3>Merge Gate</h3><p>${escapeSqlInjectionHtml(data.merge_gate.reason)}</p><h4>Required evidence</h4><ul>${mergeEvidence}</ul><h4>CI checks</h4><ul>${mergeChecks}</ul></div>
                <div class="item"><h3>Attack Explanation</h3><ul>${attacks}</ul></div>
                <div class="item"><h3>Safe Query Patterns</h3><pre class="raw-json"><code>${escapeSqlInjectionHtml(JSON.stringify(data.safe_query_patterns, null, 2))}</code></pre></div>
                <div class="item"><h3>Defense Taxonomy</h3><ul>${defenseTaxonomy}</ul></div>
                <div class="item"><h3>Fix Examples</h3><ul>${fixExamples}</ul></div>
                <div class="item"><h3>Unsafe Patterns</h3><ul>${unsafe}</ul></div>
                <div class="item"><h3>Allowlist Review</h3><ul>${allowlist}</ul></div>
                <div class="item"><h3>Test Payloads</h3><ul>${tests}</ul></div>
                <div class="item"><h3>Test Matrix</h3><ul>${testMatrix}</ul></div>
                <div class="item"><h3>Feature Test Snippet</h3><pre class="raw-json"><code>${escapeSqlInjectionHtml(data.feature_test_snippet)}</code></pre></div>
                <div class="item"><h3>Threat Model</h3><ul>${threatModel}</ul></div>
                <div class="item"><h3>Review Questions</h3><ul>${reviewQuestions}</ul></div>
                <div class="item"><h3>Rollout Steps</h3><ul>${rolloutSteps}</ul></div>
                <div class="item"><h3>Laravel Review Checklist</h3><ul>${checklist}</ul></div>
                <div class="item"><h3>Search Terms</h3><p>${searchTerms}</p></div>
                <div class="item"><h3>Review Packet Markdown</h3><pre class="raw-json"><code>${escapeSqlInjectionHtml(data.review_packet_markdown)}</code></pre></div>
            `;
        }

        function applySqlInjectionPreset(preset) {
            sqlInjectionForm.elements.query_name.value = preset.query_name;
            sqlInjectionForm.elements.query_style.value = preset.query_style;
            sqlInjectionForm.elements.input_surface.value = preset.input_surface;
            sqlInjectionForm.elements.dynamic_parts.value = preset.dynamic_parts;
            sqlInjectionForm.elements.uses_bindings.checked = preset.uses_bindings;
            sqlInjectionStatus.textContent = `Loaded preset: ${preset.query_name}`;
        }

        sqlInjectionForm.addEventListener('submit', async (event) => {
            event.preventDefault();

            const formData = new FormData(sqlInjectionForm);
            const payload = {
                query_name: String(formData.get('query_name') || ''),
                query_style: String(formData.get('query_style') || 'query-builder'),
                input_surface: String(formData.get('input_surface') || 'search-box'),
                dynamic_parts: String(formData.get('dynamic_parts') || 'where-value'),
                uses_bindings: formData.has('uses_bindings'),
            };

            sqlInjectionStatus.textContent = 'Running POST /api/practice/sql-injection-defense-plan...';
            sqlInjectionOutput.textContent = JSON.stringify(payload, null, 2);

            try {
                const response = await fetch('{{ route('api.practice.sql-injection-defense-plan.store') }}', {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(payload),
                });
                const body = await response.json();

                sqlInjectionStatus.textContent = `HTTP ${response.status}`;
                sqlInjectionOutput.textContent = JSON.stringify(body, null, 2);

                if (body.data) {
                    renderSqlInjectionSummary(body.data);
                }
            } catch (error) {
                sqlInjectionStatus.textContent = 'Request failed';
                sqlInjectionOutput.textContent = error instanceof Error ? error.message : 'Unknown request error';
                sqlInjectionSummary.innerHTML = '<p class="muted">No summary available.</p>';
            }
        });

        async function writeSqlInjectionClipboard(value) {
            if (navigator.clipboard) {
                await navigator.clipboard.writeText(value);
                return;
            }

            const fallback = document.createElement('textarea');
            fallback.value = value;
            fallback.setAttribute('readonly', 'readonly');
            fallback.style.position = 'fixed';
            fallback.style.left = '-9999px';
            document.body.appendChild(fallback);
            fallback.select();
            document.execCommand('copy');
            document.body.removeChild(fallback);
        }

        copySqlInjectionPacket.addEventListener('click', async () => {
            if (!lastSqlInjectionPacket) {
                sqlInjectionStatus.textContent = 'Run the planner before copying a review packet.';
                return;
            }

            await writeSqlInjectionClipboard(lastSqlInjectionPacket);
            copySqlInjectionPacket.textContent = 'Copied review packet';

            window.setTimeout(() => {
                copySqlInjectionPacket.textContent = 'Copy review packet';
            }, 1600);
        });

        sqlInjectionPresetButtons.forEach((button) => {
            button.addEventListener('click', () => {
                const preset = sqlInjectionPresets[button.dataset.sqlInjectionPreset];

                if (preset) {
                    applySqlInjectionPreset(preset);
                    sqlInjectionForm.requestSubmit();
                }
            });
        });
    </script>
@endsection
