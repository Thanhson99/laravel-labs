@extends('learning.layout', ['title' => 'OAuth Flow Plan Workbench'])

@section('content')
    <section class="hero">
        <span class="badge">Runnable workbench</span>
        <h1>Replace Implicit Flow with Authorization Code plus PKCE.</h1>
        <p>
            This workbench compares OAuth2 Implicit Flow with Authorization Code flow,
            explains why token-in-URL patterns are no longer recommended, and builds a
            migration plan for browser, mobile, and backend clients.
        </p>
    </section>

    <section class="section">
        <div class="grid">
            <article class="panel">
                <h2>OAuth Context</h2>
                <form id="oauthFlowPlanForm">
                    <label>
                        Scenario preset
                        <select id="oauthFlowPreset">
                            <option value="legacySpa">Legacy SPA using Implicit</option>
                            <option value="modernSpa">Modern SPA with PKCE</option>
                            <option value="serverWeb">Server-rendered backend client</option>
                            <option value="mobile">Mobile app public client</option>
                            <option value="serviceClient">Service-to-service Client Credentials</option>
                        </select>
                    </label>
                    <label style="margin-top: 12px;">
                        Client type
                        <select name="client_type">
                            <option value="browser-spa">browser-spa</option>
                            <option value="server-rendered-web">server-rendered-web</option>
                            <option value="mobile-app">mobile-app</option>
                            <option value="legacy-spa">legacy-spa</option>
                            <option value="service-to-service">service-to-service</option>
                        </select>
                    </label>
                    <label style="margin-top: 12px;">
                        Current flow
                        <select name="current_flow">
                            <option value="implicit">implicit</option>
                            <option value="authorization-code">authorization-code</option>
                            <option value="authorization-code-pkce">authorization-code-pkce</option>
                            <option value="client-credentials">client-credentials</option>
                            <option value="unknown">unknown</option>
                        </select>
                    </label>
                    <label style="margin-top: 12px;">
                        Can keep client secret
                        <select name="can_keep_secret">
                            <option value="no">no</option>
                            <option value="yes">yes</option>
                        </select>
                    </label>
                    <label style="margin-top: 12px;">
                        PKCE supported
                        <select name="pkce_supported">
                            <option value="yes">yes</option>
                            <option value="no">no</option>
                        </select>
                    </label>
                    <label style="margin-top: 12px;">
                        Refresh token needed
                        <select name="refresh_token_needed">
                            <option value="yes">yes</option>
                            <option value="no">no</option>
                        </select>
                    </label>
                    <label style="margin-top: 12px;">
                        Browser history risk
                        <select name="browser_history_risk">
                            <option value="high">high</option>
                            <option value="medium">medium</option>
                            <option value="low">low</option>
                        </select>
                    </label>
                    <button class="button primary" type="submit" style="margin-top: 14px;">Plan OAuth flow</button>
                </form>
            </article>

            <article class="panel">
                <h2>OAuth Flow Plan</h2>
                <p class="muted" id="oauthFlowPlanStatus">Submit input to compare OAuth flows.</p>
                <div class="list" style="margin-top: 14px;">
                    <article class="item">
                        <div class="meta">
                            <span class="badge">Recommendation</span>
                        </div>
                        <p id="oauthFlowRecommendation">Run the planner to see the selected OAuth flow.</p>
                    </article>
                    <article class="item">
                        <div class="meta">
                            <span class="badge">Risk score</span>
                        </div>
                        <p id="oauthFlowRisk">Risk score appears after planning.</p>
                    </article>
                    <article class="item">
                        <div class="meta">
                            <span class="badge">Security tests</span>
                        </div>
                        <ul id="oauthFlowSecurityTests">
                            <li>Run the planner to generate security test cases.</li>
                        </ul>
                    </article>
                    <article class="item">
                        <div class="meta">
                            <span class="badge">Implementation snippets</span>
                        </div>
                        <ul id="oauthFlowSnippets">
                            <li>Run the planner to generate OAuth request snippets.</li>
                        </ul>
                    </article>
                    <article class="item">
                        <div class="meta">
                            <span class="badge">PKCE simulation</span>
                        </div>
                        <p id="oauthFlowPkceSimulation">
                            Run the planner to see the Authorization Code with PKCE sequence.
                        </p>
                        <ul id="oauthFlowPkceSimulationSteps">
                            <li>PKCE steps appear after planning.</li>
                        </ul>
                    </article>
                    <article class="item">
                        <div class="meta">
                            <span class="badge">PKCE failure drill</span>
                        </div>
                        <ul id="oauthFlowPkceFailureDrill">
                            <li>Run the planner to generate verifier and code-reuse failure tests.</li>
                        </ul>
                    </article>
                    <article class="item">
                        <div class="meta">
                            <span class="badge">Client credentials checks</span>
                        </div>
                        <ul id="oauthFlowClientCredentialsChecks">
                            <li>Run the planner to generate machine-to-machine review checks.</li>
                        </ul>
                    </article>
                    <article class="item">
                        <div class="meta">
                            <span class="badge">Access contract</span>
                        </div>
                        <ul id="oauthFlowAccessContract">
                            <li>Run the planner to map service-token audience and scope boundaries.</li>
                        </ul>
                    </article>
                    <article class="item">
                        <div class="meta">
                            <span class="badge">Token validation policy</span>
                        </div>
                        <ul id="oauthFlowTokenValidationPolicy">
                            <li>Run the planner to generate resource-server token validation rules.</li>
                        </ul>
                    </article>
                    <article class="item">
                        <div class="meta">
                            <span class="badge">Operational plan</span>
                        </div>
                        <p id="oauthFlowOperationalPlan">
                            Run the planner to generate ownership, rotation, and incident guidance.
                        </p>
                        <ul id="oauthFlowAuditEvents">
                            <li>Audit events appear after planning.</li>
                        </ul>
                    </article>
                    <article class="item">
                        <div class="meta">
                            <span class="badge">Rotation drill</span>
                        </div>
                        <ul id="oauthFlowRotationDrill">
                            <li>Run the planner to generate client-secret rotation steps.</li>
                        </ul>
                    </article>
                    <article class="item">
                        <div class="meta">
                            <span class="badge">Monitoring signals</span>
                        </div>
                        <ul id="oauthFlowMonitoringSignals">
                            <li>Run the planner to generate service-token metrics, logs, and alerts.</li>
                        </ul>
                    </article>
                    <article class="item">
                        <div class="meta">
                            <span class="badge">Failure playbook</span>
                        </div>
                        <ul id="oauthFlowFailurePlaybook">
                            <li>Run the planner to generate service-token failure responses.</li>
                        </ul>
                    </article>
                    <article class="item">
                        <div class="meta">
                            <span class="badge">Security review rubric</span>
                        </div>
                        <ul id="oauthFlowSecurityReviewRubric">
                            <li>Run the planner to generate Client Credentials review criteria.</li>
                        </ul>
                    </article>
                    <article class="item">
                        <div class="meta">
                            <span class="badge">Interview checklist</span>
                        </div>
                        <ul id="oauthFlowInterviewChecklist">
                            <li>Run the planner to generate Client Credentials interview talking points.</li>
                        </ul>
                    </article>
                    <article class="item">
                        <div class="meta">
                            <span class="badge">Interview answer</span>
                        </div>
                        <p id="oauthFlowInterviewAnswer">Run the planner to generate a concise answer.</p>
                    </article>
                </div>
                <pre class="raw-json"><code id="oauthFlowPlanOutput">POST /api/practice/oauth-flow-plan</code></pre>
            </article>

            <article class="panel">
                <h2>Files To Read</h2>
                <ul>
                    <li><code>routes/api/practice-actions.php</code></li>
                    <li><code>app/Http/Requests/Api/PlanOauthFlowRequest.php</code></li>
                    <li><code>app/Http/Controllers/Api/OauthFlowPlanController.php</code></li>
                    <li><code>app/Services/Practice/OauthFlowPlanService.php</code></li>
                    <li><code>tests/Feature/OauthFlowPlanWorkbenchTest.php</code></li>
                </ul>
            </article>
        </div>
    </section>

    <script>
        const oauthFlowPresets = {
            legacySpa: {
                client_type: 'legacy-spa',
                current_flow: 'implicit',
                can_keep_secret: 'no',
                pkce_supported: 'yes',
                refresh_token_needed: 'yes',
                browser_history_risk: 'high',
            },
            modernSpa: {
                client_type: 'browser-spa',
                current_flow: 'authorization-code-pkce',
                can_keep_secret: 'no',
                pkce_supported: 'yes',
                refresh_token_needed: 'yes',
                browser_history_risk: 'medium',
            },
            serverWeb: {
                client_type: 'server-rendered-web',
                current_flow: 'authorization-code',
                can_keep_secret: 'yes',
                pkce_supported: 'yes',
                refresh_token_needed: 'yes',
                browser_history_risk: 'low',
            },
            mobile: {
                client_type: 'mobile-app',
                current_flow: 'unknown',
                can_keep_secret: 'no',
                pkce_supported: 'yes',
                refresh_token_needed: 'yes',
                browser_history_risk: 'medium',
            },
            serviceClient: {
                client_type: 'service-to-service',
                current_flow: 'client-credentials',
                can_keep_secret: 'yes',
                pkce_supported: 'no',
                refresh_token_needed: 'no',
                browser_history_risk: 'low',
            },
        };

        const oauthFlowPreset = document.querySelector('#oauthFlowPreset');
        const oauthFlowPlanForm = document.querySelector('#oauthFlowPlanForm');
        const oauthFlowPlanStatus = document.querySelector('#oauthFlowPlanStatus');
        const oauthFlowPlanOutput = document.querySelector('#oauthFlowPlanOutput');
        const oauthFlowRecommendation = document.querySelector('#oauthFlowRecommendation');
        const oauthFlowRisk = document.querySelector('#oauthFlowRisk');
        const oauthFlowSecurityTests = document.querySelector('#oauthFlowSecurityTests');
        const oauthFlowSnippets = document.querySelector('#oauthFlowSnippets');
        const oauthFlowPkceSimulation = document.querySelector('#oauthFlowPkceSimulation');
        const oauthFlowPkceSimulationSteps = document.querySelector('#oauthFlowPkceSimulationSteps');
        const oauthFlowPkceFailureDrill = document.querySelector('#oauthFlowPkceFailureDrill');
        const oauthFlowClientCredentialsChecks = document.querySelector('#oauthFlowClientCredentialsChecks');
        const oauthFlowAccessContract = document.querySelector('#oauthFlowAccessContract');
        const oauthFlowTokenValidationPolicy = document.querySelector('#oauthFlowTokenValidationPolicy');
        const oauthFlowOperationalPlan = document.querySelector('#oauthFlowOperationalPlan');
        const oauthFlowAuditEvents = document.querySelector('#oauthFlowAuditEvents');
        const oauthFlowRotationDrill = document.querySelector('#oauthFlowRotationDrill');
        const oauthFlowMonitoringSignals = document.querySelector('#oauthFlowMonitoringSignals');
        const oauthFlowFailurePlaybook = document.querySelector('#oauthFlowFailurePlaybook');
        const oauthFlowSecurityReviewRubric = document.querySelector('#oauthFlowSecurityReviewRubric');
        const oauthFlowInterviewChecklist = document.querySelector('#oauthFlowInterviewChecklist');
        const oauthFlowInterviewAnswer = document.querySelector('#oauthFlowInterviewAnswer');

        const replaceList = (target, items, formatter) => {
            target.replaceChildren();

            if (!Array.isArray(items) || items.length === 0) {
                const empty = document.createElement('li');
                empty.textContent = 'No items returned.';
                target.append(empty);

                return;
            }

            items.forEach((item) => {
                const row = document.createElement('li');
                row.textContent = formatter(item);
                target.append(row);
            });
        };

        const renderOauthFlowSummary = (body) => {
            const data = body && body.data ? body.data : {};
            const recommendation = data.recommendation || {};
            const risk = data.risk_score || {};
            const snippets = data.implementation_snippets || {};

            oauthFlowRecommendation.textContent = `${recommendation.flow || 'unknown'}: ${recommendation.reason || 'No recommendation returned.'}`;
            oauthFlowRisk.textContent = `${risk.level || 'unknown'} (${risk.score ?? 'n/a'}): ${(risk.reasons || []).join(' ')}`;
            oauthFlowInterviewAnswer.textContent = data.interview_answer || 'No interview answer returned.';
            replaceList(
                oauthFlowSecurityTests,
                data.security_test_matrix || [],
                (item) => `${item.category || 'test'}: ${item.expected_result || item.scenario || 'Review OAuth behavior.'}`
            );
            replaceList(
                oauthFlowSnippets,
                Object.entries(snippets),
                ([label, code]) => `${label}: ${code}`
            );
            const pkceSimulation = data.pkce_sequence_simulation || {};
            oauthFlowPkceSimulation.textContent = `${pkceSimulation.title || 'PKCE sequence'}: ${pkceSimulation.summary || 'No PKCE simulation returned.'}`;
            replaceList(
                oauthFlowPkceSimulationSteps,
                pkceSimulation.steps || [],
                (item) => `${item.stage || 'stage'} - ${item.actor || 'actor'} sends ${item.sends || 'nothing listed'} Stores: ${item.stores || 'nothing listed'} Security: ${item.security_point || 'No security point returned.'}`
            );
            replaceList(
                oauthFlowPkceFailureDrill,
                data.pkce_failure_drill || [],
                (item) => `${item.case || 'case'}: ${item.signal || 'No signal returned.'} Response: ${item.response || 'No response returned.'} Test: ${item.test || 'No test returned.'}`
            );
            replaceList(
                oauthFlowClientCredentialsChecks,
                data.client_credentials_checklist || [],
                (item) => `${item.area || 'check'}: ${item.check || 'Review OAuth client boundary.'}`
            );
            replaceList(
                oauthFlowAccessContract,
                data.client_credentials_access_contract || [],
                (item) => {
                    const scopes = Array.isArray(item.allowed_scopes) && item.allowed_scopes.length > 0
                        ? item.allowed_scopes.join(', ')
                        : 'no service scopes';
                    const denied = Array.isArray(item.denied_by_default) && item.denied_by_default.length > 0
                        ? item.denied_by_default.join(', ')
                        : 'nothing listed';

                    return `${item.resource || 'resource'} (${item.audience || 'audience'}): allow ${scopes}; deny ${denied}. ${item.verification || ''}`;
                }
            );
            replaceList(
                oauthFlowTokenValidationPolicy,
                data.client_credentials_token_validation_policy || [],
                (item) => `${item.claim || 'claim'}: ${item.rule || 'No rule returned.'} Reject when: ${item.reject_when || 'No reject condition returned.'}`
            );
            const operationalPlan = data.client_credentials_operational_plan || {};
            const appliesLabel = operationalPlan.applies === true ? 'applies' : 'not applicable';
            oauthFlowOperationalPlan.textContent = `${appliesLabel}: ${operationalPlan.owner_model || 'No owner model returned.'} ${operationalPlan.rotation_model || ''} ${operationalPlan.incident_trigger || ''}`;
            replaceList(oauthFlowAuditEvents, operationalPlan.audit_events || [], (event) => event);
            replaceList(
                oauthFlowRotationDrill,
                data.client_credentials_rotation_drill || [],
                (item) => `${item.phase || 'phase'}: ${item.action || 'No action returned.'} Verify: ${item.verification || 'No verification returned.'}`
            );
            const monitoringSignals = data.client_credentials_monitoring_signals || {};
            const monitoringItems = [
                ...(monitoringSignals.metrics || []).map((value) => `metric: ${value}`),
                ...(monitoringSignals.logs || []).map((value) => `log: ${value}`),
                ...(monitoringSignals.alerts || []).map((value) => `alert: ${value}`),
            ];
            replaceList(oauthFlowMonitoringSignals, monitoringItems, (item) => item);
            replaceList(
                oauthFlowFailurePlaybook,
                data.client_credentials_failure_playbook || [],
                (item) => `${item.failure || 'failure'} (${item.owner || 'owner'}): ${item.trigger || 'No trigger returned.'} Action: ${item.action || 'No action returned.'}`
            );
            replaceList(
                oauthFlowSecurityReviewRubric,
                data.client_credentials_security_review_rubric || [],
                (item) => `${item.criterion || 'criterion'} (${item.weight ?? 0}): pass when ${item.pass_signal || 'No pass signal returned.'} Fail when ${item.fail_signal || 'No fail signal returned.'}`
            );
            replaceList(
                oauthFlowInterviewChecklist,
                data.client_credentials_interview_checklist || [],
                (item) => `${item.point || 'point'}: ${item.answer_signal || 'No answer signal returned.'} Red flag: ${item.red_flag || 'No red flag returned.'}`
            );
        };

        function applyOauthFlowPreset(presetName) {
            const preset = oauthFlowPresets[presetName];

            if (!preset) {
                return;
            }

            Object.entries(preset).forEach(([field, value]) => {
                const input = oauthFlowPlanForm.elements.namedItem(field);

                if (input) {
                    input.value = value;
                }
            });
        }

        oauthFlowPreset.addEventListener('change', (event) => {
            applyOauthFlowPreset(event.target.value);
        });

        applyOauthFlowPreset(oauthFlowPreset.value);

        oauthFlowPlanForm.addEventListener('submit', async (event) => {
            event.preventDefault();
            const payload = Object.fromEntries(new FormData(oauthFlowPlanForm).entries());

            oauthFlowPlanStatus.textContent = 'Running POST /api/practice/oauth-flow-plan...';
            oauthFlowPlanOutput.textContent = JSON.stringify(payload, null, 2);

            try {
                const response = await fetch('{{ route('api.practice.oauth-flow-plan.store') }}', {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(payload),
                });
                const body = await response.json();

                oauthFlowPlanStatus.textContent = `HTTP ${response.status}`;
                oauthFlowPlanOutput.textContent = JSON.stringify(body, null, 2);
                renderOauthFlowSummary(body);
            } catch (error) {
                oauthFlowPlanStatus.textContent = 'Request failed';
                oauthFlowPlanOutput.textContent = error instanceof Error ? error.message : 'Unknown request error';
            }
        });
    </script>
@endsection
