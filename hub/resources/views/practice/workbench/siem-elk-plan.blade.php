@extends('learning.layout', ['title' => 'SIEM ELK Plan Workbench'])

@section('content')
    <section class="hero">
        <span class="badge">Runnable workbench</span>
        <h1>Plan a SIEM pipeline with ELK before alerts become noise.</h1>
        <p>
            This workbench turns SIEM and ELK concepts into an implementation plan: log sources, normalized fields,
            detection rules, retention, privacy controls, dashboards, runbooks, and an interview answer.
        </p>
    </section>

    <section class="section">
        <div class="grid">
            <article class="panel">
                <h2>SIEM Input</h2>
                <label>
                    Scenario preset
                    <select id="siemElkPreset">
                        <option value="auth" selected>Cloud auth abuse</option>
                        <option value="web">Laravel web attack</option>
                        <option value="enterprise">Mixed enterprise SIEM</option>
                    </select>
                </label>

                <form id="siemElkPlanForm">
                    <label style="margin-top: 12px;">
                        Environment name
                        <input name="environment_name" value="Production Security Logs" autocomplete="off">
                    </label>

                    <label style="margin-top: 12px;">
                        Log sources
                        <select name="log_sources">
                            <option value="laravel-app">laravel-app</option>
                            <option value="cloud-auth" selected>cloud-auth</option>
                            <option value="linux-nginx">linux-nginx</option>
                            <option value="kubernetes">kubernetes</option>
                            <option value="mixed-enterprise">mixed-enterprise</option>
                        </select>
                    </label>

                    <label style="margin-top: 12px;">
                        Detection goal
                        <select name="detection_goal">
                            <option value="auth-abuse" selected>auth-abuse</option>
                            <option value="web-attack">web-attack</option>
                            <option value="privilege-change">privilege-change</option>
                            <option value="incident-investigation">incident-investigation</option>
                            <option value="compliance">compliance</option>
                        </select>
                    </label>

                    <label style="margin-top: 12px;">
                        Retention need
                        <select name="retention_need">
                            <option value="short">short</option>
                            <option value="medium">medium</option>
                            <option value="long" selected>long</option>
                        </select>
                    </label>

                    <label style="margin-top: 12px;">
                        Alert maturity
                        <select name="alert_maturity">
                            <option value="low" selected>low</option>
                            <option value="medium">medium</option>
                            <option value="high">high</option>
                        </select>
                    </label>

                    <label style="margin-top: 12px;">
                        Data sensitivity
                        <select name="data_sensitivity">
                            <option value="low">low</option>
                            <option value="medium">medium</option>
                            <option value="high" selected>high</option>
                        </select>
                    </label>

                    <label style="margin-top: 12px;">
                        Team size
                        <select name="team_size">
                            <option value="solo">solo</option>
                            <option value="small" selected>small</option>
                            <option value="soc">soc</option>
                        </select>
                    </label>

                    <button class="button primary" type="submit" style="margin-top: 14px;">Plan SIEM pipeline</button>
                </form>
            </article>

            <article class="panel">
                <h2>Plan Output</h2>
                <p class="muted" id="siemElkPlanStatus">Submit input to generate ELK roles, pipeline, fields, detections, retention, privacy, and runbook.</p>
                <pre class="raw-json"><code id="siemElkPlanOutput">POST /api/practice/siem-elk-plan</code></pre>
            </article>

            <article class="panel">
                <h2>Executive Summary</h2>
                <div id="siemElkPlanSummary" class="list">
                    <p class="muted">Run the planner to see the recommendation, first action, detection rules, and review checklist.</p>
                </div>
            </article>

            <article class="panel">
                <h2>Implementation Prompt</h2>
                <button class="button" id="copySiemElkPrompt" type="button">Copy prompt</button>
                <pre class="raw-json" style="margin-top: 12px;"><code id="siemElkPrompt">Run the planner to generate a prompt.</code></pre>
            </article>

            <article class="panel">
                <h2>Evidence Packet</h2>
                <button class="button" id="copySiemElkEvidence" type="button">Copy evidence</button>
                <pre class="raw-json" style="margin-top: 12px;"><code id="siemElkEvidence">Run the planner to generate an incident evidence packet.</code></pre>
            </article>

            <article class="panel">
                <h2>Files To Read</h2>
                <ul>
                    <li><code>data/interview/devops.en.json</code></li>
                    <li><code>data/interview/devops.vi.json</code></li>
                    <li><code>routes/api/practice-actions.php</code></li>
                    <li><code>app/Http/Requests/Api/PlanSiemElkRequest.php</code></li>
                    <li><code>app/Services/Practice/SiemElkPlanService.php</code></li>
                    <li><code>tests/Feature/SiemElkPlanWorkbenchTest.php</code></li>
                </ul>
            </article>
        </div>
    </section>

    <script>
        const siemElkPlanForm = document.querySelector('#siemElkPlanForm');
        const siemElkPreset = document.querySelector('#siemElkPreset');
        const siemElkPlanStatus = document.querySelector('#siemElkPlanStatus');
        const siemElkPlanOutput = document.querySelector('#siemElkPlanOutput');
        const siemElkPlanSummary = document.querySelector('#siemElkPlanSummary');
        const siemElkPrompt = document.querySelector('#siemElkPrompt');
        const copySiemElkPrompt = document.querySelector('#copySiemElkPrompt');
        const siemElkEvidence = document.querySelector('#siemElkEvidence');
        const copySiemElkEvidence = document.querySelector('#copySiemElkEvidence');
        const siemElkPresets = {
            auth: {
                environment_name: 'Production Security Logs',
                log_sources: 'cloud-auth',
                detection_goal: 'auth-abuse',
                retention_need: 'long',
                alert_maturity: 'low',
                data_sensitivity: 'high',
                team_size: 'small',
            },
            web: {
                environment_name: 'Laravel Customer Portal',
                log_sources: 'laravel-app',
                detection_goal: 'web-attack',
                retention_need: 'medium',
                alert_maturity: 'medium',
                data_sensitivity: 'medium',
                team_size: 'small',
            },
            enterprise: {
                environment_name: 'Enterprise SOC',
                log_sources: 'mixed-enterprise',
                detection_goal: 'incident-investigation',
                retention_need: 'long',
                alert_maturity: 'high',
                data_sensitivity: 'high',
                team_size: 'soc',
            },
        };

        function applySiemElkPreset(name) {
            const preset = siemElkPresets[name] || siemElkPresets.auth;

            Object.entries(preset).forEach(([field, value]) => {
                const input = siemElkPlanForm.elements[field];

                if (input) {
                    input.value = value;
                }
            });
        }

        function escapeSiemElkHtml(value) {
            return String(value ?? '')
                .replaceAll('&', '&amp;')
                .replaceAll('<', '&lt;')
                .replaceAll('>', '&gt;')
                .replaceAll('"', '&quot;')
                .replaceAll("'", '&#039;');
        }

        function renderSiemElkSummary(data) {
            const roles = data.elk_roles
                .map((role) => `<li><strong>${escapeSiemElkHtml(role.component)}</strong>: ${escapeSiemElkHtml(role.responsibility)}</li>`)
                .join('');
            const pipeline = data.pipeline
                .map((step) => `<li><strong>${escapeSiemElkHtml(step.step)}</strong>: ${escapeSiemElkHtml(step.output)}</li>`)
                .join('');
            const detections = data.detection_rules
                .slice(0, 3)
                .map((rule) => `<li><strong>${escapeSiemElkHtml(rule.name)}</strong>: ${escapeSiemElkHtml(rule.signal)}</li>`)
                .join('');
            const savedQueries = data.saved_queries
                .slice(0, 3)
                .map((query) => `<li><strong>${escapeSiemElkHtml(query.name)}</strong>: <code>${escapeSiemElkHtml(query.query)}</code></li>`)
                .join('');
            const parserTests = data.parser_tests
                .slice(0, 2)
                .map((test) => `<li><strong>${escapeSiemElkHtml(test.name)}</strong>: ${escapeSiemElkHtml(test.expected.join(', '))}</li>`)
                .join('');
            const promotionChecks = data.detection_as_code.promotion_checks
                .slice(0, 3)
                .map((check) => `<li>${escapeSiemElkHtml(check)}</li>`)
                .join('');
            const ilm = data.index_lifecycle_policy
                .map((phase) => `<li><strong>${escapeSiemElkHtml(phase.phase)}</strong> (${escapeSiemElkHtml(phase.window)}): ${escapeSiemElkHtml(phase.action)}</li>`)
                .join('');
            const dashboardPanels = data.dashboard_panels
                .map((panel) => `<li><strong>${escapeSiemElkHtml(panel.panel)}</strong>: ${escapeSiemElkHtml(panel.decision)}</li>`)
                .join('');
            const rollout = data.rollout_plan
                .map((stage) => `<li><strong>${escapeSiemElkHtml(stage.stage)}</strong>: ${escapeSiemElkHtml(stage.goal)}</li>`)
                .join('');
            const capacityChecks = data.capacity_plan.scale_checks
                .map((check) => `<li>${escapeSiemElkHtml(check)}</li>`)
                .join('');
            const correlations = data.correlation_matrix
                .map((row) => `<li><strong>${escapeSiemElkHtml(row.join_key)}</strong>: ${escapeSiemElkHtml(row.detection_value)}</li>`)
                .join('');
            const access = data.access_model
                .map((role) => `<li><strong>${escapeSiemElkHtml(role.role)}</strong>: ${escapeSiemElkHtml(role.can_do)}</li>`)
                .join('');
            const threats = data.threat_model
                .map((threat) => `<li><strong>${escapeSiemElkHtml(threat.threat)}</strong>: ${escapeSiemElkHtml(threat.control)}</li>`)
                .join('');
            const falsePositiveSteps = data.false_positive_workflow
                .map((step) => `<li><strong>${escapeSiemElkHtml(step.step)}</strong>: ${escapeSiemElkHtml(step.action)}</li>`)
                .join('');
            const maturity = data.maturity_roadmap
                .map((phase) => `<li><strong>${escapeSiemElkHtml(phase.phase)}</strong>: ${escapeSiemElkHtml(phase.focus)}</li>`)
                .join('');
            const decommission = data.decommission_plan
                .map((item) => `<li><strong>${escapeSiemElkHtml(item.trigger)}</strong>: ${escapeSiemElkHtml(item.action)}</li>`)
                .join('');
            const artifacts = data.artifact_bundle
                .map((artifact) => `
                    <li>
                        <strong>${escapeSiemElkHtml(artifact.filename)}</strong>
                        <span class="muted">(${escapeSiemElkHtml(artifact.type)})</span><br>
                        ${escapeSiemElkHtml(artifact.purpose)}
                    </li>
                `)
                .join('');
            const manifestFiles = data.artifact_manifest.files
                .map((file) => `<li><strong>${escapeSiemElkHtml(file.filename)}</strong>: <code>${escapeSiemElkHtml(file.sha256.slice(0, 16))}</code></li>`)
                .join('');
            const manifestChecks = data.artifact_manifest.validation_checks
                .map((check) => `<li>${escapeSiemElkHtml(check)}</li>`)
                .join('');
            const gateBlockers = data.promotion_gate.blockers
                .map((blocker) => `<li>${escapeSiemElkHtml(blocker)}</li>`)
                .join('');
            const gateRequirements = data.promotion_gate.required_before_paging
                .slice(0, 5)
                .map((requirement) => `<li>${escapeSiemElkHtml(requirement)}</li>`)
                .join('');
            const securitySlo = data.security_slo
                .map((slo) => `<li><strong>${escapeSiemElkHtml(slo.metric)}</strong>: ${escapeSiemElkHtml(slo.target)}</li>`)
                .join('');
            const drillSteps = data.tabletop_drill.steps
                .map((step) => `<li>${escapeSiemElkHtml(step)}</li>`)
                .join('');
            const feedbackLoop = data.post_incident_feedback_loop
                .map((item) => `<li><strong>${escapeSiemElkHtml(item.signal)}</strong>: ${escapeSiemElkHtml(item.update)}</li>`)
                .join('');
            const cadence = data.operating_cadence
                .map((item) => `<li><strong>${escapeSiemElkHtml(item.cadence)}</strong> (${escapeSiemElkHtml(item.owner)}): ${escapeSiemElkHtml(item.checks[0])}</li>`)
                .join('');
            const rubricStrong = data.interview_rubric.strong_answer
                .map((item) => `<li>${escapeSiemElkHtml(item)}</li>`)
                .join('');
            const rubricFollowUps = data.interview_rubric.follow_up_questions
                .slice(0, 3)
                .map((item) => `<li>${escapeSiemElkHtml(item)}</li>`)
                .join('');
            const dataQuality = data.data_quality_scorecard
                .map((item) => `<li><strong>${escapeSiemElkHtml(item.dimension)}</strong>: ${escapeSiemElkHtml(item.target)}</li>`)
                .join('');
            const costControls = data.cost_control_plan
                .map((item) => `<li><strong>${escapeSiemElkHtml(item.lever)}</strong>: ${escapeSiemElkHtml(item.action)}</li>`)
                .join('');
            const onboarding = data.source_onboarding_checklist
                .map((item) => `<li>${escapeSiemElkHtml(item)}</li>`)
                .join('');
            const checklist = data.review_checklist
                .slice(0, 4)
                .map((item) => `<li>${escapeSiemElkHtml(item)}</li>`)
                .join('');

            siemElkPlanSummary.innerHTML = `
                <div class="item">
                    <div class="meta">
                        <span class="badge pending">${escapeSiemElkHtml(data.risk_level)} risk</span>
                        <span class="badge done">${escapeSiemElkHtml(data.readiness_score.label)} ${escapeSiemElkHtml(data.readiness_score.score)}/100</span>
                        <span class="badge">${escapeSiemElkHtml(data.environment)}</span>
                    </div>
                    <h3>${escapeSiemElkHtml(data.executive_summary.headline)}</h3>
                    <p>${escapeSiemElkHtml(data.executive_summary.decision)}</p>
                    <p class="muted">${escapeSiemElkHtml(data.executive_summary.first_action)}</p>
                </div>
                <div class="item">
                    <h3>ELK Roles</h3>
                    <ul>${roles}</ul>
                </div>
                <div class="item">
                    <h3>Pipeline</h3>
                    <ul>${pipeline}</ul>
                </div>
                <div class="item">
                    <h3>Detection Rules</h3>
                    <ul>${detections}</ul>
                </div>
                <div class="item">
                    <h3>Saved Queries</h3>
                    <ul>${savedQueries}</ul>
                </div>
                <div class="item">
                    <h3>Parser Tests</h3>
                    <ul>${parserTests}</ul>
                </div>
                <div class="item">
                    <h3>Detection-as-Code</h3>
                    <p><strong>${escapeSiemElkHtml(data.detection_as_code.name)}</strong></p>
                    <ul>${promotionChecks}</ul>
                </div>
                <div class="item">
                    <h3>Logstash Pipeline</h3>
                    <pre class="raw-json"><code>${escapeSiemElkHtml(data.logstash_pipeline_example)}</code></pre>
                </div>
                <div class="item">
                    <h3>Detection Rule YAML</h3>
                    <pre class="raw-json"><code>${escapeSiemElkHtml(data.detection_rule_yaml)}</code></pre>
                </div>
                <div class="item">
                    <h3>Index Lifecycle Policy</h3>
                    <ul>${ilm}</ul>
                </div>
                <div class="item">
                    <h3>Dashboard Panels</h3>
                    <ul>${dashboardPanels}</ul>
                </div>
                <div class="item">
                    <h3>Rollout Plan</h3>
                    <ul>${rollout}</ul>
                </div>
                <div class="item">
                    <h3>Capacity Plan</h3>
                    <p><strong>${escapeSiemElkHtml(data.capacity_plan.daily_ingest_estimate)}</strong> into <code>${escapeSiemElkHtml(data.capacity_plan.index_pattern)}</code></p>
                    <p class="muted">${escapeSiemElkHtml(data.capacity_plan.storage_warning)}</p>
                    <ul>${capacityChecks}</ul>
                </div>
                <div class="item">
                    <h3>Correlation Matrix</h3>
                    <ul>${correlations}</ul>
                </div>
                <div class="item">
                    <h3>Access Model</h3>
                    <ul>${access}</ul>
                </div>
                <div class="item">
                    <h3>Threat Model</h3>
                    <ul>${threats}</ul>
                </div>
                <div class="item">
                    <h3>False-Positive Workflow</h3>
                    <ul>${falsePositiveSteps}</ul>
                </div>
                <div class="item">
                    <h3>Maturity Roadmap</h3>
                    <ul>${maturity}</ul>
                </div>
                <div class="item">
                    <h3>Decommission Plan</h3>
                    <ul>${decommission}</ul>
                </div>
                <div class="item">
                    <h3>Artifact Bundle</h3>
                    <ul>${artifacts}</ul>
                    <pre class="raw-json"><code>${escapeSiemElkHtml(JSON.stringify(data.artifact_bundle, null, 2))}</code></pre>
                </div>
                <div class="item">
                    <h3>Artifact Manifest</h3>
                    <p><strong>${escapeSiemElkHtml(data.artifact_manifest.bundle_name)}</strong> (${escapeSiemElkHtml(data.artifact_manifest.artifact_count)} files)</p>
                    <ul>${manifestFiles}</ul>
                    <p class="muted">${escapeSiemElkHtml(data.artifact_manifest.promotion_note)}</p>
                    <ul>${manifestChecks}</ul>
                </div>
                <div class="item">
                    <h3>Promotion Gate</h3>
                    <p><strong>${escapeSiemElkHtml(data.promotion_gate.decision)}</strong> (${escapeSiemElkHtml(data.promotion_gate.score)}/100)</p>
                    <ul>${gateBlockers}</ul>
                    <p class="muted">Required before paging:</p>
                    <ul>${gateRequirements}</ul>
                </div>
                <div class="item">
                    <h3>Security SLO</h3>
                    <ul>${securitySlo}</ul>
                </div>
                <div class="item">
                    <h3>Tabletop Drill</h3>
                    <p>${escapeSiemElkHtml(data.tabletop_drill.scenario)}</p>
                    <ul>${drillSteps}</ul>
                </div>
                <div class="item">
                    <h3>Post-Incident Feedback Loop</h3>
                    <ul>${feedbackLoop}</ul>
                </div>
                <div class="item">
                    <h3>Executive Brief</h3>
                    <p><strong>${escapeSiemElkHtml(data.executive_brief.status)}</strong>: ${escapeSiemElkHtml(data.executive_brief.message)}</p>
                    <p class="muted">${escapeSiemElkHtml(data.executive_brief.next_decision)}</p>
                </div>
                <div class="item">
                    <h3>Operating Cadence</h3>
                    <ul>${cadence}</ul>
                </div>
                <div class="item">
                    <h3>Interview Rubric</h3>
                    <p class="muted">Strong answer signals:</p>
                    <ul>${rubricStrong}</ul>
                    <p class="muted">Follow-up questions:</p>
                    <ul>${rubricFollowUps}</ul>
                </div>
                <div class="item">
                    <h3>Data Quality Scorecard</h3>
                    <ul>${dataQuality}</ul>
                </div>
                <div class="item">
                    <h3>Cost Control Plan</h3>
                    <ul>${costControls}</ul>
                </div>
                <div class="item">
                    <h3>Source Onboarding Checklist</h3>
                    <ul>${onboarding}</ul>
                </div>
                <div class="item">
                    <h3>Retention</h3>
                    <p><strong>Hot:</strong> ${escapeSiemElkHtml(data.retention_policy.hot)}</p>
                    <p><strong>Warm:</strong> ${escapeSiemElkHtml(data.retention_policy.warm)}</p>
                    <p class="muted">${escapeSiemElkHtml(data.retention_policy.archive_or_delete)}</p>
                </div>
                <div class="item">
                    <h3>Review Checklist</h3>
                    <ul>${checklist}</ul>
                </div>
                <div class="item">
                    <h3>Interview Answer</h3>
                    <p>${escapeSiemElkHtml(data.interview_answer)}</p>
                </div>
            `;
        }

        siemElkPreset.addEventListener('change', (event) => {
            applySiemElkPreset(event.target.value);
        });

        applySiemElkPreset(siemElkPreset.value);

        siemElkPlanForm.addEventListener('submit', async (event) => {
            event.preventDefault();

            const formData = new FormData(siemElkPlanForm);
            const payload = {
                environment_name: String(formData.get('environment_name') || ''),
                log_sources: String(formData.get('log_sources') || 'cloud-auth'),
                detection_goal: String(formData.get('detection_goal') || 'auth-abuse'),
                retention_need: String(formData.get('retention_need') || 'medium'),
                alert_maturity: String(formData.get('alert_maturity') || 'low'),
                data_sensitivity: String(formData.get('data_sensitivity') || 'medium'),
                team_size: String(formData.get('team_size') || 'small'),
            };

            siemElkPlanStatus.textContent = 'Running POST /api/practice/siem-elk-plan...';
            siemElkPlanOutput.textContent = JSON.stringify(payload, null, 2);

            try {
                const response = await fetch('{{ route('api.practice.siem-elk-plan.store') }}', {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(payload),
                });
                const body = await response.json();

                siemElkPlanStatus.textContent = `HTTP ${response.status}`;
                siemElkPlanOutput.textContent = JSON.stringify(body, null, 2);
                siemElkPrompt.textContent = body.data?.implementation_prompt ?? 'No prompt returned.';
                siemElkEvidence.textContent = body.data?.incident_evidence_packet_markdown ?? 'No evidence packet returned.';

                if (body.data) {
                    renderSiemElkSummary(body.data);
                }
            } catch (error) {
                siemElkPlanStatus.textContent = 'Request failed';
                siemElkPlanOutput.textContent = error instanceof Error ? error.message : 'Unknown request error';
                siemElkPrompt.textContent = 'No prompt available.';
                siemElkEvidence.textContent = 'No evidence packet available.';
                siemElkPlanSummary.innerHTML = '<p class="muted">No summary available.</p>';
            }
        });

        copySiemElkPrompt.addEventListener('click', async () => {
            try {
                await navigator.clipboard.writeText(siemElkPrompt.textContent);
                copySiemElkPrompt.textContent = 'Copied prompt';
            } catch (error) {
                copySiemElkPrompt.textContent = 'Copy failed';
            }

            window.setTimeout(() => {
                copySiemElkPrompt.textContent = 'Copy prompt';
            }, 1600);
        });

        copySiemElkEvidence.addEventListener('click', async () => {
            try {
                await navigator.clipboard.writeText(siemElkEvidence.textContent);
                copySiemElkEvidence.textContent = 'Copied evidence';
            } catch (error) {
                copySiemElkEvidence.textContent = 'Copy failed';
            }

            window.setTimeout(() => {
                copySiemElkEvidence.textContent = 'Copy evidence';
            }, 1600);
        });
    </script>
@endsection
