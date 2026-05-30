@extends('learning.layout', ['title' => 'GraphQL REST Decision Workbench'])

@section('content')
    <section class="hero">
        <span class="badge">Runnable workbench</span>
        <h1>Choose REST or GraphQL from real API constraints.</h1>
        <p>
            This workbench turns API design signals into a recommendation, contract shape,
            Laravel boundaries, cache plan, authorization plan, N+1 controls, tests, and
            an interview-ready answer.
        </p>
    </section>

    <section class="section">
        <div class="grid">
            <article class="panel">
                <h2>API Context</h2>
                <form id="graphqlRestDecisionForm">
                    <label>
                        Scenario preset
                        <select id="graphqlRestPreset">
                            <option value="dashboardGraph">SPA dashboard with composed data</option>
                            <option value="publicCrud">Public CRUD API with HTTP caching</option>
                            <option value="mobileFeed">Mobile feed with flexible fields</option>
                            <option value="adminInternal">Internal admin tool</option>
                        </select>
                    </label>
                    <label style="margin-top: 12px;">
                        Client type
                        <select name="client_type">
                            <option value="spa-dashboard">spa-dashboard</option>
                            <option value="public-api">public-api</option>
                            <option value="mobile-app">mobile-app</option>
                            <option value="internal-admin">internal-admin</option>
                            <option value="bff">bff</option>
                        </select>
                    </label>
                    <label style="margin-top: 12px;">
                        Data shape
                        <select name="data_shape">
                            <option value="screen-composition">screen-composition</option>
                            <option value="resource-crud">resource-crud</option>
                            <option value="graph-shaped">graph-shaped</option>
                            <option value="reporting">reporting</option>
                        </select>
                    </label>
                    <label style="margin-top: 12px;">
                        Field flexibility
                        <select name="field_flexibility">
                            <option value="high">high</option>
                            <option value="medium">medium</option>
                            <option value="low">low</option>
                        </select>
                    </label>
                    <label style="margin-top: 12px;">
                        Cache priority
                        <select name="cache_priority">
                            <option value="low">low</option>
                            <option value="medium">medium</option>
                            <option value="high">high</option>
                        </select>
                    </label>
                    <label style="margin-top: 12px;">
                        Relationship depth
                        <select name="relationship_depth">
                            <option value="deep">deep</option>
                            <option value="moderate">moderate</option>
                            <option value="shallow">shallow</option>
                        </select>
                    </label>
                    <label style="margin-top: 12px;">
                        Team GraphQL experience
                        <select name="team_graphql_experience">
                            <option value="some">some</option>
                            <option value="none">none</option>
                            <option value="strong">strong</option>
                        </select>
                    </label>
                    <label style="margin-top: 12px;">
                        Authorization complexity
                        <select name="authorization_complexity">
                            <option value="medium">medium</option>
                            <option value="low">low</option>
                            <option value="high">high</option>
                        </select>
                    </label>
                    <button class="button primary" type="submit" style="margin-top: 14px;">Plan API style</button>
                </form>
            </article>

            <article class="panel">
                <h2>Decision Plan</h2>
                <p class="muted" id="graphqlRestDecisionStatus">Submit input to compare REST and GraphQL.</p>
                <pre class="raw-json"><code id="graphqlRestDecisionOutput">POST /api/practice/graphql-rest-decision</code></pre>
            </article>

            <article class="panel">
                <h2>Decision Summary</h2>
                <div id="graphqlRestDecisionSummary" class="list">
                    <p class="muted">Run the planner to see recommendation, score, risk, and key signals.</p>
                </div>
            </article>

            <article class="panel">
                <h2>Decision Memo</h2>
                <button class="button" id="copyGraphqlRestMemo" type="button">Copy memo</button>
                <pre class="raw-json" style="margin-top: 12px;"><code id="graphqlRestDecisionMemo">Run the planner to generate a markdown decision memo.</code></pre>
            </article>

            <article class="panel">
                <h2>Files To Read</h2>
                <ul>
                    <li><code>routes/api/practice-actions.php</code></li>
                    <li><code>app/Http/Requests/Api/PlanGraphqlRestDecisionRequest.php</code></li>
                    <li><code>app/Http/Controllers/Api/GraphqlRestDecisionController.php</code></li>
                    <li><code>app/Services/Practice/GraphqlRestDecisionService.php</code></li>
                    <li><code>tests/Feature/GraphqlRestDecisionWorkbenchTest.php</code></li>
                </ul>
            </article>
        </div>
    </section>

    <script>
        const graphqlRestPresets = {
            dashboardGraph: {
                client_type: 'spa-dashboard',
                data_shape: 'screen-composition',
                field_flexibility: 'high',
                cache_priority: 'low',
                relationship_depth: 'deep',
                team_graphql_experience: 'some',
                authorization_complexity: 'medium',
            },
            publicCrud: {
                client_type: 'public-api',
                data_shape: 'resource-crud',
                field_flexibility: 'low',
                cache_priority: 'high',
                relationship_depth: 'shallow',
                team_graphql_experience: 'none',
                authorization_complexity: 'medium',
            },
            mobileFeed: {
                client_type: 'mobile-app',
                data_shape: 'graph-shaped',
                field_flexibility: 'high',
                cache_priority: 'medium',
                relationship_depth: 'moderate',
                team_graphql_experience: 'strong',
                authorization_complexity: 'medium',
            },
            adminInternal: {
                client_type: 'internal-admin',
                data_shape: 'resource-crud',
                field_flexibility: 'medium',
                cache_priority: 'low',
                relationship_depth: 'moderate',
                team_graphql_experience: 'none',
                authorization_complexity: 'high',
            },
        };

        const graphqlRestPreset = document.querySelector('#graphqlRestPreset');
        const graphqlRestDecisionForm = document.querySelector('#graphqlRestDecisionForm');
        const graphqlRestDecisionStatus = document.querySelector('#graphqlRestDecisionStatus');
        const graphqlRestDecisionOutput = document.querySelector('#graphqlRestDecisionOutput');
        const graphqlRestDecisionMemo = document.querySelector('#graphqlRestDecisionMemo');
        const graphqlRestDecisionSummary = document.querySelector('#graphqlRestDecisionSummary');
        const copyGraphqlRestMemo = document.querySelector('#copyGraphqlRestMemo');

        function applyGraphqlRestPreset(presetName) {
            const preset = graphqlRestPresets[presetName];

            if (!preset) {
                return;
            }

            Object.entries(preset).forEach(([field, value]) => {
                const input = graphqlRestDecisionForm.elements.namedItem(field);

                if (input) {
                    input.value = value;
                }
            });
        }

        graphqlRestPreset.addEventListener('change', (event) => {
            applyGraphqlRestPreset(event.target.value);
        });

        applyGraphqlRestPreset(graphqlRestPreset.value);

        function escapeGraphqlRestHtml(value) {
            return String(value ?? '')
                .replaceAll('&', '&amp;')
                .replaceAll('<', '&lt;')
                .replaceAll('>', '&gt;')
                .replaceAll('"', '&quot;')
                .replaceAll("'", '&#039;');
        }

        function renderGraphqlRestSummary(data) {
            const recommendation = data.recommendation;
            const score = data.score_breakdown;
            const risk = data.risk_score;
            const signals = score.signals
                .map((signal) => `<li><strong>${escapeGraphqlRestHtml(signal.signal)}</strong>: GraphQL +${escapeGraphqlRestHtml(signal.graphql_points)}, REST +${escapeGraphqlRestHtml(signal.rest_points)}</li>`)
                .join('');

            graphqlRestDecisionSummary.innerHTML = `
                <div class="item">
                    <div class="meta">
                        <span class="badge done">${escapeGraphqlRestHtml(recommendation.style)}</span>
                        <span class="badge">${escapeGraphqlRestHtml(risk.level)} risk</span>
                    </div>
                    <h3>${escapeGraphqlRestHtml(recommendation.label)}</h3>
                    <p class="muted">${escapeGraphqlRestHtml(recommendation.reason)}</p>
                </div>
                <div class="item">
                    <h3>Score</h3>
                    <p>GraphQL: <strong>${escapeGraphqlRestHtml(score.graphql_score)}</strong> | REST: <strong>${escapeGraphqlRestHtml(score.rest_score)}</strong> | Margin: <strong>${escapeGraphqlRestHtml(score.margin)}</strong> | Confidence: <strong>${escapeGraphqlRestHtml(score.confidence)}</strong></p>
                    <ul>${signals}</ul>
                </div>
                <div class="item">
                    <h3>Top Risk</h3>
                    <p>${escapeGraphqlRestHtml(risk.reasons[0] ?? 'No major risk signal.')}</p>
                </div>
            `;
        }

        graphqlRestDecisionForm.addEventListener('submit', async (event) => {
            event.preventDefault();
            const payload = Object.fromEntries(new FormData(graphqlRestDecisionForm).entries());

            graphqlRestDecisionStatus.textContent = 'Running POST /api/practice/graphql-rest-decision...';
            graphqlRestDecisionOutput.textContent = JSON.stringify(payload, null, 2);

            try {
                const response = await fetch('{{ route('api.practice.graphql-rest-decision.store') }}', {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(payload),
                });
                const body = await response.json();

                graphqlRestDecisionStatus.textContent = `HTTP ${response.status}`;
                graphqlRestDecisionOutput.textContent = JSON.stringify(body, null, 2);
                graphqlRestDecisionMemo.textContent = body.data?.decision_memo_markdown ?? 'No memo returned.';
                if (body.data) {
                    renderGraphqlRestSummary(body.data);
                }
            } catch (error) {
                graphqlRestDecisionStatus.textContent = 'Request failed';
                graphqlRestDecisionOutput.textContent = error instanceof Error ? error.message : 'Unknown request error';
                graphqlRestDecisionMemo.textContent = 'No memo available.';
                graphqlRestDecisionSummary.innerHTML = '<p class="muted">No summary available.</p>';
            }
        });

        copyGraphqlRestMemo.addEventListener('click', async () => {
            try {
                await navigator.clipboard.writeText(graphqlRestDecisionMemo.textContent);
                copyGraphqlRestMemo.textContent = 'Copied memo';
            } catch (error) {
                copyGraphqlRestMemo.textContent = 'Copy failed';
            }

            window.setTimeout(() => {
                copyGraphqlRestMemo.textContent = 'Copy memo';
            }, 1600);
        });
    </script>
@endsection
