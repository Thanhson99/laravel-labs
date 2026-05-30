@extends('learning.layout', ['title' => 'RESTful API Naming Workbench'])

@section('content')
    <section class="hero">
        <span class="badge">Runnable workbench</span>
        <h1>Name Laravel API endpoints from resource contracts.</h1>
        <p>
            Turn a feature brief into RESTful route paths, route names, query parameters,
            business-action endpoints, review checks, tests, and a markdown decision memo.
        </p>
    </section>

    <section class="section">
        <div class="grid">
            <article class="panel">
                <h2>Endpoint Context</h2>
                <form id="restfulNamingForm">
                    <label>
                        Resource name
                        <input name="resource_name" value="Order" maxlength="60" required>
                    </label>
                    <label style="margin-top: 12px;">
                        Parent resource
                        <input name="parent_resource" value="User" maxlength="60">
                    </label>
                    <label style="margin-top: 12px;">
                        Current endpoint draft
                        <input name="current_endpoint" value="/api/v1/getUserOrders/paid/recent" maxlength="120">
                    </label>
                    <label style="margin-top: 12px;">
                        Operation type
                        <select name="operation_type">
                            <option value="read">read</option>
                            <option value="create">create</option>
                            <option value="update">update</option>
                            <option value="delete">delete</option>
                            <option value="action">action</option>
                        </select>
                    </label>
                    <label style="margin-top: 12px;">
                        API version
                        <select name="version">
                            <option value="v1">v1</option>
                            <option value="v2">v2</option>
                        </select>
                    </label>
                    <label style="margin-top: 12px;">
                        Needs filtering
                        <select name="needs_filtering">
                            <option value="1">yes</option>
                            <option value="0">no</option>
                        </select>
                    </label>
                    <label style="margin-top: 12px;">
                        Needs business action
                        <select name="needs_business_action">
                            <option value="1">yes</option>
                            <option value="0">no</option>
                        </select>
                    </label>
                    <button class="button primary" type="submit" style="margin-top: 14px;">Plan endpoint names</button>
                </form>
            </article>

            <article class="panel">
                <h2>Naming Plan</h2>
                <p class="muted" id="restfulNamingStatus">Submit input to generate route names.</p>
                <pre class="raw-json"><code id="restfulNamingOutput">POST /api/practice/restful-api-naming-plan</code></pre>
            </article>

            <article class="panel">
                <h2>Route Summary</h2>
                <div id="restfulNamingSummary" class="list">
                    <p class="muted">Run the planner to see endpoint paths, route names, and checks.</p>
                </div>
            </article>

            <article class="panel">
                <h2>Quality Review</h2>
                <div id="restfulNamingQuality" class="list">
                    <p class="muted">Run the planner to score an existing endpoint draft.</p>
                </div>
            </article>

            <article class="panel">
                <h2>Decision Memo</h2>
                <button class="button" id="copyRestfulNamingMemo" type="button">Copy memo</button>
                <pre class="raw-json" style="margin-top: 12px;"><code id="restfulNamingMemo">Run the planner to generate a markdown memo.</code></pre>
            </article>

            <article class="panel">
                <h2>Contract Artifact</h2>
                <button class="button" id="copyRestfulNamingContract" type="button">Copy OpenAPI</button>
                <div id="restfulNamingContractSummary" class="list" style="margin-top: 12px;">
                    <p class="muted">Run the planner to generate an OpenAPI path fragment.</p>
                </div>
                <pre class="raw-json" style="margin-top: 12px;"><code id="restfulNamingContract">No OpenAPI artifact yet.</code></pre>
            </article>

            <article class="panel">
                <h2>Implementation Blueprint</h2>
                <div id="restfulNamingBlueprint" class="list">
                    <p class="muted">Run the planner to see controller, request, policy, and test steps.</p>
                </div>
            </article>

            <article class="panel">
                <h2>Migration Plan</h2>
                <div id="restfulNamingMigration" class="list">
                    <p class="muted">Run the planner to see deprecation phases for an existing endpoint.</p>
                </div>
            </article>

            <article class="panel">
                <h2>Response Contract</h2>
                <div id="restfulNamingResponseContract" class="list">
                    <p class="muted">Run the planner to see success envelopes, errors, pagination, and headers.</p>
                </div>
            </article>

            <article class="panel">
                <h2>Operational Readiness</h2>
                <div id="restfulNamingReadiness" class="list">
                    <p class="muted">Run the planner to see logs, metrics, alerts, and release checks.</p>
                </div>
            </article>

            <article class="panel">
                <h2>Naming Rubric</h2>
                <div id="restfulNamingRubric" class="list">
                    <p class="muted">Run the planner to score naming quality by review criterion.</p>
                </div>
            </article>

            <article class="panel">
                <h2>Client Examples</h2>
                <div id="restfulNamingClients" class="list">
                    <p class="muted">Run the planner to generate curl, HTTPie, and fetch examples.</p>
                </div>
            </article>

            <article class="panel">
                <h2>Files To Read</h2>
                <ul>
                    <li><code>routes/api/practice-actions.php</code></li>
                    <li><code>routes/web/workbench.php</code></li>
                    <li><code>app/Http/Requests/Api/PlanRestfulApiNamingRequest.php</code></li>
                    <li><code>app/Http/Controllers/Api/RestfulApiNamingPlanController.php</code></li>
                    <li><code>app/Services/Practice/RestfulApiNamingPlanService.php</code></li>
                    <li><code>tests/Feature/RestfulApiNamingPlanWorkbenchTest.php</code></li>
                </ul>
            </article>
        </div>
    </section>

    <script>
        const restfulNamingForm = document.querySelector('#restfulNamingForm');
        const restfulNamingStatus = document.querySelector('#restfulNamingStatus');
        const restfulNamingOutput = document.querySelector('#restfulNamingOutput');
        const restfulNamingSummary = document.querySelector('#restfulNamingSummary');
        const restfulNamingQuality = document.querySelector('#restfulNamingQuality');
        const restfulNamingMemo = document.querySelector('#restfulNamingMemo');
        const restfulNamingContract = document.querySelector('#restfulNamingContract');
        const restfulNamingContractSummary = document.querySelector('#restfulNamingContractSummary');
        const restfulNamingBlueprint = document.querySelector('#restfulNamingBlueprint');
        const restfulNamingMigration = document.querySelector('#restfulNamingMigration');
        const restfulNamingResponseContract = document.querySelector('#restfulNamingResponseContract');
        const restfulNamingReadiness = document.querySelector('#restfulNamingReadiness');
        const restfulNamingRubric = document.querySelector('#restfulNamingRubric');
        const restfulNamingClients = document.querySelector('#restfulNamingClients');
        const copyRestfulNamingMemo = document.querySelector('#copyRestfulNamingMemo');
        const copyRestfulNamingContract = document.querySelector('#copyRestfulNamingContract');

        function escapeRestfulNamingHtml(value) {
            return String(value ?? '')
                .replaceAll('&', '&amp;')
                .replaceAll('<', '&lt;')
                .replaceAll('>', '&gt;')
                .replaceAll('"', '&quot;')
                .replaceAll("'", '&#039;');
        }

        function renderRestfulNamingSummary(data) {
            const routes = data.routes
                .map((route) => `<li><strong>${escapeRestfulNamingHtml(route.method)}</strong> <code>${escapeRestfulNamingHtml(route.path)}</code> <span class="muted">${escapeRestfulNamingHtml(route.route_name)}</span></li>`)
                .join('');
            const checks = data.review_checklist
                .map((check) => `<li>${escapeRestfulNamingHtml(check)}</li>`)
                .join('');

            restfulNamingSummary.innerHTML = `
                <div class="item">
                    <div class="meta">
                        <span class="badge done">${escapeRestfulNamingHtml(data.resource.version)}</span>
                        <span class="badge">${escapeRestfulNamingHtml(data.recommendation.style)}</span>
                    </div>
                    <h3>${escapeRestfulNamingHtml(data.recommendation.base_path)}</h3>
                    <p class="muted">${escapeRestfulNamingHtml(data.recommendation.summary)}</p>
                </div>
                <div class="item">
                    <h3>Routes</h3>
                    <ul>${routes}</ul>
                </div>
                <div class="item">
                    <h3>Review Checklist</h3>
                    <ul>${checks}</ul>
                </div>
            `;
        }

        function renderRestfulNamingQuality(data) {
            const smells = data.quality_review.smells.length > 0
                ? data.quality_review.smells.map((smell) => `<li>${escapeRestfulNamingHtml(smell)}</li>`).join('')
                : '<li>No naming smells detected.</li>';
            const improvements = data.quality_review.improvements
                .map((improvement) => `<li>${escapeRestfulNamingHtml(improvement)}</li>`)
                .join('');

            restfulNamingQuality.innerHTML = `
                <div class="item">
                    <div class="meta">
                        <span class="badge done">${escapeRestfulNamingHtml(data.quality_review.score)}/100</span>
                        <span class="badge">${escapeRestfulNamingHtml(data.quality_review.target_endpoint)}</span>
                    </div>
                    <h3>${escapeRestfulNamingHtml(data.quality_review.verdict)}</h3>
                    <p class="muted">${escapeRestfulNamingHtml(data.quality_review.current_endpoint ?? 'No current endpoint supplied.')}</p>
                </div>
                <div class="item">
                    <h3>Naming Smells</h3>
                    <ul>${smells}</ul>
                </div>
                <div class="item">
                    <h3>Improvements</h3>
                    <ul>${improvements}</ul>
                </div>
            `;
        }

        function renderRestfulNamingContract(data) {
            const expectations = data.contract_artifacts.route_list_expectations
                .map((expectation) => `<li><code>${escapeRestfulNamingHtml(expectation)}</code></li>`)
                .join('');
            const checks = data.contract_artifacts.consumer_contract_checks
                .map((check) => `<li>${escapeRestfulNamingHtml(check)}</li>`)
                .join('');

            restfulNamingContractSummary.innerHTML = `
                <div class="item">
                    <h3>${escapeRestfulNamingHtml(data.contract_artifacts.openapi_path)}</h3>
                    <p class="muted">Use this as a small contract artifact before controller implementation.</p>
                </div>
                <div class="item">
                    <h3>Route List Expectations</h3>
                    <ul>${expectations}</ul>
                </div>
                <div class="item">
                    <h3>Consumer Checks</h3>
                    <ul>${checks}</ul>
                </div>
            `;
        }

        function renderRestfulNamingBlueprint(data) {
            const requests = data.implementation_blueprint.form_requests
                .map((request) => `<li><code>${escapeRestfulNamingHtml(request)}</code></li>`)
                .join('');
            const abilities = data.implementation_blueprint.policy_abilities
                .map((ability) => `<li><code>${escapeRestfulNamingHtml(ability)}</code></li>`)
                .join('');
            const order = data.implementation_blueprint.implementation_order
                .map((step) => `<li>${escapeRestfulNamingHtml(step)}</li>`)
                .join('');

            restfulNamingBlueprint.innerHTML = `
                <div class="item">
                    <h3>${escapeRestfulNamingHtml(data.implementation_blueprint.controller)}</h3>
                    <p class="muted">${escapeRestfulNamingHtml(data.implementation_blueprint.route_model_binding.join(' '))}</p>
                </div>
                <div class="item">
                    <h3>Form Requests</h3>
                    <ul>${requests}</ul>
                </div>
                <div class="item">
                    <h3>Policy Abilities</h3>
                    <ul>${abilities}</ul>
                </div>
                <div class="item">
                    <h3>Implementation Order</h3>
                    <ul>${order}</ul>
                </div>
            `;
        }

        function renderRestfulNamingMigration(data) {
            const phases = data.migration_plan.phases
                .map((phase) => `<li><strong>${escapeRestfulNamingHtml(phase.phase)}</strong>: ${escapeRestfulNamingHtml(phase.action)} <span class="muted">${escapeRestfulNamingHtml(phase.verification)}</span></li>`)
                .join('');
            const headers = data.migration_plan.response_headers.length > 0
                ? data.migration_plan.response_headers.map((header) => `<li><code>${escapeRestfulNamingHtml(header.name)}: ${escapeRestfulNamingHtml(header.value)}</code> ${escapeRestfulNamingHtml(header.purpose)}</li>`).join('')
                : '<li>No deprecation headers needed.</li>';
            const notes = data.migration_plan.release_notes
                .map((note) => `<li>${escapeRestfulNamingHtml(note)}</li>`)
                .join('');

            restfulNamingMigration.innerHTML = `
                <div class="item">
                    <div class="meta">
                        <span class="badge ${data.migration_plan.requires_deprecation ? '' : 'done'}">${data.migration_plan.requires_deprecation ? 'deprecation needed' : 'canonical only'}</span>
                        <span class="badge">${escapeRestfulNamingHtml(data.migration_plan.target_endpoint)}</span>
                    </div>
                    <h3>${escapeRestfulNamingHtml(data.migration_plan.legacy_endpoint ?? 'No legacy endpoint supplied.')}</h3>
                </div>
                <div class="item">
                    <h3>Phases</h3>
                    <ul>${phases}</ul>
                </div>
                <div class="item">
                    <h3>Response Headers</h3>
                    <ul>${headers}</ul>
                </div>
                <div class="item">
                    <h3>Release Notes</h3>
                    <ul>${notes}</ul>
                </div>
            `;
        }

        function renderRestfulNamingResponseContract(data) {
            const successes = data.response_contract.success_responses
                .map((response) => `<li><code>${escapeRestfulNamingHtml(response.route_name)}</code> ${escapeRestfulNamingHtml(response.status)} <code>${escapeRestfulNamingHtml(JSON.stringify(response.envelope))}</code></li>`)
                .join('');
            const errors = data.response_contract.error_responses
                .map((response) => `<li><strong>${escapeRestfulNamingHtml(response.status)}</strong> ${escapeRestfulNamingHtml(response.reason)}</li>`)
                .join('');
            const headers = data.response_contract.headers
                .map((header) => `<li><code>${escapeRestfulNamingHtml(header.name)}</code> ${escapeRestfulNamingHtml(header.purpose)}</li>`)
                .join('');
            const pagination = data.response_contract.pagination
                ? `<p class="muted">${escapeRestfulNamingHtml(data.response_contract.pagination.strategy)}: ${escapeRestfulNamingHtml(data.response_contract.pagination.meta_keys.join(', '))}</p>`
                : '<p class="muted">No collection pagination expected for this operation.</p>';

            restfulNamingResponseContract.innerHTML = `
                <div class="item">
                    <h3>Success Responses</h3>
                    <ul>${successes}</ul>
                    ${pagination}
                </div>
                <div class="item">
                    <h3>Error Responses</h3>
                    <ul>${errors}</ul>
                </div>
                <div class="item">
                    <h3>Headers</h3>
                    <ul>${headers}</ul>
                </div>
            `;
        }

        function renderRestfulNamingReadiness(data) {
            const logs = data.operational_readiness.logs
                .map((log) => `<li><code>${escapeRestfulNamingHtml(log.field)}</code> ${escapeRestfulNamingHtml(log.purpose)}</li>`)
                .join('');
            const metrics = data.operational_readiness.metrics
                .map((metric) => `<li><code>${escapeRestfulNamingHtml(metric)}</code></li>`)
                .join('');
            const alerts = data.operational_readiness.alerts
                .map((alert) => `<li>${escapeRestfulNamingHtml(alert)}</li>`)
                .join('');
            const checks = data.operational_readiness.acceptance_checks
                .map((check) => `<li>${escapeRestfulNamingHtml(check)}</li>`)
                .join('');

            restfulNamingReadiness.innerHTML = `
                <div class="item">
                    <h3>Logs</h3>
                    <ul>${logs}</ul>
                </div>
                <div class="item">
                    <h3>Metrics</h3>
                    <ul>${metrics}</ul>
                </div>
                <div class="item">
                    <h3>Alerts</h3>
                    <ul>${alerts}</ul>
                </div>
                <div class="item">
                    <h3>Acceptance Checks</h3>
                    <ul>${checks}</ul>
                </div>
            `;
        }

        function renderRestfulNamingRubric(data) {
            const criteria = data.naming_rubric.criteria
                .map((criterion) => `
                    <li>
                        <strong>${escapeRestfulNamingHtml(criterion.name)}</strong>
                        <code>${escapeRestfulNamingHtml(criterion.score)}/${escapeRestfulNamingHtml(criterion.weight)}</code>
                        <span class="muted">${escapeRestfulNamingHtml(criterion.recommendation)}</span>
                    </li>
                `)
                .join('');

            restfulNamingRubric.innerHTML = `
                <div class="item">
                    <div class="meta">
                        <span class="badge done">${escapeRestfulNamingHtml(data.naming_rubric.total_score)}/${escapeRestfulNamingHtml(data.naming_rubric.max_score)}</span>
                        <span class="badge">${escapeRestfulNamingHtml(data.naming_rubric.grade)}</span>
                    </div>
                    <h3>Endpoint naming review score</h3>
                </div>
                <div class="item">
                    <h3>Criteria</h3>
                    <ul>${criteria}</ul>
                </div>
            `;
        }

        function renderRestfulNamingClients(data) {
            const examples = data.client_examples
                .map((example) => `
                    <div class="item">
                        <div class="meta">
                            <span class="badge">${escapeRestfulNamingHtml(example.method)}</span>
                            <span class="badge">${escapeRestfulNamingHtml(example.route_name)}</span>
                        </div>
                        <h3>${escapeRestfulNamingHtml(example.path)}</h3>
                        <pre class="raw-json"><code>${escapeRestfulNamingHtml(example.curl)}</code></pre>
                        <pre class="raw-json"><code>${escapeRestfulNamingHtml(example.httpie)}</code></pre>
                        <pre class="raw-json"><code>${escapeRestfulNamingHtml(example.fetch)}</code></pre>
                    </div>
                `)
                .join('');

            restfulNamingClients.innerHTML = examples;
        }

        restfulNamingForm.addEventListener('submit', async (event) => {
            event.preventDefault();
            const payload = Object.fromEntries(new FormData(restfulNamingForm).entries());
            payload.needs_filtering = payload.needs_filtering === '1';
            payload.needs_business_action = payload.needs_business_action === '1';

            restfulNamingStatus.textContent = 'Running POST /api/practice/restful-api-naming-plan...';
            restfulNamingOutput.textContent = JSON.stringify(payload, null, 2);

            try {
                const response = await fetch('{{ route('api.practice.restful-api-naming-plan.store') }}', {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(payload),
                });
                const body = await response.json();

                restfulNamingStatus.textContent = `HTTP ${response.status}`;
                restfulNamingOutput.textContent = JSON.stringify(body, null, 2);
                restfulNamingMemo.textContent = body.data?.decision_memo_markdown ?? 'No memo returned.';
                restfulNamingContract.textContent = body.data?.contract_artifacts?.openapi_yaml ?? 'No OpenAPI artifact returned.';

                if (body.data) {
                    renderRestfulNamingSummary(body.data);
                    renderRestfulNamingQuality(body.data);
                    renderRestfulNamingContract(body.data);
                    renderRestfulNamingBlueprint(body.data);
                    renderRestfulNamingMigration(body.data);
                    renderRestfulNamingResponseContract(body.data);
                    renderRestfulNamingReadiness(body.data);
                    renderRestfulNamingRubric(body.data);
                    renderRestfulNamingClients(body.data);
                }
            } catch (error) {
                restfulNamingStatus.textContent = 'Request failed';
                restfulNamingOutput.textContent = error instanceof Error ? error.message : 'Unknown request error';
                restfulNamingMemo.textContent = 'No memo available.';
                restfulNamingContract.textContent = 'No OpenAPI artifact available.';
                restfulNamingSummary.innerHTML = '<p class="muted">No summary available.</p>';
                restfulNamingQuality.innerHTML = '<p class="muted">No quality review available.</p>';
                restfulNamingContractSummary.innerHTML = '<p class="muted">No contract artifact available.</p>';
                restfulNamingBlueprint.innerHTML = '<p class="muted">No implementation blueprint available.</p>';
                restfulNamingMigration.innerHTML = '<p class="muted">No migration plan available.</p>';
                restfulNamingResponseContract.innerHTML = '<p class="muted">No response contract available.</p>';
                restfulNamingReadiness.innerHTML = '<p class="muted">No operational readiness available.</p>';
                restfulNamingRubric.innerHTML = '<p class="muted">No naming rubric available.</p>';
                restfulNamingClients.innerHTML = '<p class="muted">No client examples available.</p>';
            }
        });

        copyRestfulNamingMemo.addEventListener('click', async () => {
            await navigator.clipboard.writeText(restfulNamingMemo.textContent);
            copyRestfulNamingMemo.textContent = 'Copied';
            window.setTimeout(() => {
                copyRestfulNamingMemo.textContent = 'Copy memo';
            }, 1200);
        });

        copyRestfulNamingContract.addEventListener('click', async () => {
            await navigator.clipboard.writeText(restfulNamingContract.textContent);
            copyRestfulNamingContract.textContent = 'Copied';
            window.setTimeout(() => {
                copyRestfulNamingContract.textContent = 'Copy OpenAPI';
            }, 1200);
        });
    </script>
@endsection
