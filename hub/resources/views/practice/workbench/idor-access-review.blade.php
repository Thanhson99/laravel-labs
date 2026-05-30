@extends('learning.layout', ['title' => 'IDOR Access Review Workbench'])

@section('content')
    <section class="hero">
        <span class="badge">Runnable workbench</span>
        <h1>Understand IDOR before it leaks another user's data.</h1>
        <p>
            Simulate object ID swapping, score object-level authorization risk,
            and turn the review into Laravel policies, scoped queries, tests, and interview answers.
        </p>
    </section>

    <section class="section">
        <div class="grid">
            <article class="panel">
                <h2>Access Review Input</h2>
                <form id="idorForm">
                    <label>Resource name <input name="resource_name" value="Invoice"></label>
                    <label style="margin-top: 12px;">Route pattern <input name="route_pattern" value="/api/invoices/{invoice}"></label>
                    <label style="margin-top: 12px;">Access model
                        <select name="access_model">
                            <option value="owner">owner</option>
                            <option value="tenant" selected>tenant</option>
                            <option value="team">team</option>
                            <option value="role">role</option>
                            <option value="public">public</option>
                        </select>
                    </label>
                    <label style="margin-top: 12px;">
                        <input name="uses_policy" type="checkbox">
                        Uses policy or Gate
                    </label>
                    <label style="margin-top: 12px;">
                        <input name="query_scoped" type="checkbox">
                        Query scoped before return
                    </label>
                    <label style="margin-top: 12px;">
                        <input name="attacker_changes_id" type="checkbox" checked>
                        Attacker can change object ID
                    </label>
                    <button class="button primary" type="submit" style="margin-top: 14px;">Review IDOR access</button>
                    <button class="button" id="copyIdorPacket" type="button" style="margin-top: 14px;">Copy review packet</button>
                </form>
            </article>

            <article class="panel">
                <h2>Scenario Presets</h2>
                <div class="list">
                    <button class="button" type="button" data-idor-preset="vulnerable-invoice">Vulnerable invoice</button>
                    <button class="button" type="button" data-idor-preset="tenant-order">Tenant-safe order</button>
                    <button class="button" type="button" data-idor-preset="public-catalog">Public catalog</button>
                </div>
            </article>

            <article class="panel">
                <h2>Access Review</h2>
                <p class="muted" id="idorStatus">Submit input to generate an IDOR access review.</p>
                <pre class="raw-json"><code id="idorOutput">POST /api/practice/idor-access-review</code></pre>
            </article>

            <article class="panel">
                <h2>Summary</h2>
                <div id="idorSummary" class="list">
                    <p class="muted">Run the reviewer to see route surface risk, attacker variants, authorization map, evidence, monitoring guidance, tests, code patterns, and interview answers.</p>
                </div>
            </article>
        </div>
    </section>

    <script>
        const idorForm = document.querySelector('#idorForm');
        const idorStatus = document.querySelector('#idorStatus');
        const idorOutput = document.querySelector('#idorOutput');
        const idorSummary = document.querySelector('#idorSummary');
        const copyIdorPacket = document.querySelector('#copyIdorPacket');
        const idorPresetButtons = document.querySelectorAll('[data-idor-preset]');
        let lastIdorPacket = '';

        const idorPresets = {
            'vulnerable-invoice': {
                resource_name: 'Invoice',
                route_pattern: '/api/invoices/{invoice}',
                access_model: 'tenant',
                uses_policy: false,
                query_scoped: false,
                attacker_changes_id: true,
            },
            'tenant-order': {
                resource_name: 'Order',
                route_pattern: '/api/orders/{order}',
                access_model: 'tenant',
                uses_policy: true,
                query_scoped: true,
                attacker_changes_id: true,
            },
            'public-catalog': {
                resource_name: 'CatalogItem',
                route_pattern: '/api/catalog-items/{catalogItem}',
                access_model: 'public',
                uses_policy: false,
                query_scoped: true,
                attacker_changes_id: false,
            },
        };

        function escapeIdorHtml(value) {
            return String(value ?? '')
                .replaceAll('&', '&amp;')
                .replaceAll('<', '&lt;')
                .replaceAll('>', '&gt;')
                .replaceAll('"', '&quot;')
                .replaceAll("'", '&#039;');
        }

        function renderIdorSummary(data) {
            lastIdorPacket = String(data.review_packet_markdown || '');
            const attackSteps = data.attack_simulation.map((item) => `<li>${escapeIdorHtml(item)}</li>`).join('');
            const riskDrivers = data.risk_drivers.map((item) => `<li>${escapeIdorHtml(item)}</li>`).join('');
            const surfaceTests = data.route_surface_review.extra_tests.map((item) => `<li>${escapeIdorHtml(item)}</li>`).join('');
            const attackVariants = data.attack_variants.map((item) => `<li><strong>${escapeIdorHtml(item.name)}</strong>: ${escapeIdorHtml(item.example)}<br><span class="muted">${escapeIdorHtml(item.defense)}</span></li>`).join('');
            const abuseCases = data.abuse_case_table.map((item) => `<li><strong>${escapeIdorHtml(item.actor)}</strong>: ${escapeIdorHtml(item.attempt)}<br><span class="muted">${escapeIdorHtml(item.expected_control)}</span></li>`).join('');
            const authorizationMap = data.authorization_map.map((item) => `<li><strong>${escapeIdorHtml(item.operation)}</strong> -> ${escapeIdorHtml(item.policy_method)}<br><span class="muted">${escapeIdorHtml(item.route_example)} | ${escapeIdorHtml(item.denial_test)}</span></li>`).join('');
            const controls = data.laravel_controls.map((item) => `<li><strong>${escapeIdorHtml(item.control)}</strong>: ${escapeIdorHtml(item.purpose)}</li>`).join('');
            const remediation = data.remediation_plan.map((item) => `<li><strong>${escapeIdorHtml(item.step)}</strong>: ${escapeIdorHtml(item.action)}</li>`).join('');
            const tests = data.test_matrix.map((item) => `<li><strong>${escapeIdorHtml(item.case)}</strong>: ${escapeIdorHtml(item.expected)}</li>`).join('');
            const checklist = data.review_checklist.map((item) => `<li>${escapeIdorHtml(item)}</li>`).join('');
            const mergeEvidence = data.merge_evidence.map((item) => `<li><strong>${escapeIdorHtml(item.artifact)}</strong>: ${escapeIdorHtml(item.purpose)}</li>`).join('');
            const logFields = data.monitoring_guidance.log_fields.map((item) => `<li>${escapeIdorHtml(item)}</li>`).join('');
            const interviewQuestions = data.interview_questions.map((item) => `<li>${escapeIdorHtml(item)}</li>`).join('');

            idorSummary.innerHTML = `
                <div class="item">
                    <div class="meta">
                        <span class="badge pending">${escapeIdorHtml(data.risk_score.label)} ${escapeIdorHtml(data.risk_score.score)}/100</span>
                        <span class="badge">${escapeIdorHtml(data.resource)}</span>
                        <span class="badge">${escapeIdorHtml(data.access_model)}</span>
                    </div>
                    <h3>${escapeIdorHtml(data.route_pattern)}</h3>
                    <p>${escapeIdorHtml(data.interview_answer)}</p>
                </div>
                <div class="item"><h3>Risk Drivers</h3><ul>${riskDrivers}</ul></div>
                <div class="item">
                    <h3>Route Surface Review</h3>
                    <div class="meta">
                        <span class="badge">${escapeIdorHtml(data.route_surface_review.surface)}</span>
                        <span class="badge pending">${escapeIdorHtml(data.route_surface_review.sensitivity)}</span>
                    </div>
                    <p>${escapeIdorHtml(data.route_surface_review.warning)}</p>
                    <ul>${surfaceTests}</ul>
                </div>
                <div class="item"><h3>Attack Simulation</h3><ul>${attackSteps}</ul></div>
                <div class="item"><h3>Attack Variants</h3><ul>${attackVariants}</ul></div>
                <div class="item"><h3>Abuse Case Table</h3><ul>${abuseCases}</ul></div>
                <div class="item"><h3>Authorization Map</h3><ul>${authorizationMap}</ul></div>
                <div class="item"><h3>Laravel Controls</h3><ul>${controls}</ul></div>
                <div class="item"><h3>Remediation Plan</h3><ul>${remediation}</ul></div>
                <div class="item"><h3>Test Matrix</h3><ul>${tests}</ul></div>
                <div class="item">
                    <h3>403 vs 404 Guidance</h3>
                    <p>${escapeIdorHtml(data.status_code_guidance.recommended)}</p>
                    <p><strong>403:</strong> ${escapeIdorHtml(data.status_code_guidance.when_to_use_403)}</p>
                    <p><strong>404:</strong> ${escapeIdorHtml(data.status_code_guidance.when_to_use_404)}</p>
                </div>
                <div class="item"><h3>Review Checklist</h3><ul>${checklist}</ul></div>
                <div class="item"><h3>Vulnerable Pattern</h3><pre class="raw-json"><code>${escapeIdorHtml(data.vulnerable_pattern)}</code></pre></div>
                <div class="item"><h3>Secure Pattern</h3><pre class="raw-json"><code>${escapeIdorHtml(data.secure_pattern)}</code></pre></div>
                <div class="item"><h3>Policy Skeleton</h3><pre class="raw-json"><code>${escapeIdorHtml(data.policy_skeleton)}</code></pre></div>
                <div class="item"><h3>Feature Test Snippet</h3><pre class="raw-json"><code>${escapeIdorHtml(data.feature_test_snippet)}</code></pre></div>
                <div class="item"><h3>Merge Evidence</h3><ul>${mergeEvidence}</ul></div>
                <div class="item"><h3>Monitoring Guidance</h3><p>${escapeIdorHtml(data.monitoring_guidance.alert_rule)}</p><ul>${logFields}</ul><p>${escapeIdorHtml(data.monitoring_guidance.privacy_note)}</p></div>
                <div class="item"><h3>Interview Questions</h3><ul>${interviewQuestions}</ul></div>
                <div class="item"><h3>Review Packet Markdown</h3><pre class="raw-json"><code>${escapeIdorHtml(data.review_packet_markdown)}</code></pre></div>
            `;
        }

        function applyIdorPreset(preset) {
            idorForm.elements.resource_name.value = preset.resource_name;
            idorForm.elements.route_pattern.value = preset.route_pattern;
            idorForm.elements.access_model.value = preset.access_model;
            idorForm.elements.uses_policy.checked = preset.uses_policy;
            idorForm.elements.query_scoped.checked = preset.query_scoped;
            idorForm.elements.attacker_changes_id.checked = preset.attacker_changes_id;
            idorStatus.textContent = `Loaded preset: ${preset.resource_name}`;
        }

        idorForm.addEventListener('submit', async (event) => {
            event.preventDefault();

            const formData = new FormData(idorForm);
            const payload = {
                resource_name: String(formData.get('resource_name') || ''),
                route_pattern: String(formData.get('route_pattern') || ''),
                access_model: String(formData.get('access_model') || 'owner'),
                uses_policy: formData.has('uses_policy'),
                query_scoped: formData.has('query_scoped'),
                attacker_changes_id: formData.has('attacker_changes_id'),
            };

            idorStatus.textContent = 'Running POST /api/practice/idor-access-review...';
            idorOutput.textContent = JSON.stringify(payload, null, 2);

            try {
                const response = await fetch('{{ route('api.practice.idor-access-review.store') }}', {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(payload),
                });
                const body = await response.json();

                idorStatus.textContent = `HTTP ${response.status}`;
                idorOutput.textContent = JSON.stringify(body, null, 2);

                if (body.data) {
                    renderIdorSummary(body.data);
                }
            } catch (error) {
                idorStatus.textContent = 'Request failed';
                idorOutput.textContent = error instanceof Error ? error.message : 'Unknown request error';
                idorSummary.innerHTML = '<p class="muted">No summary available.</p>';
            }
        });

        async function writeIdorClipboard(value) {
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

        copyIdorPacket.addEventListener('click', async () => {
            if (!lastIdorPacket) {
                idorStatus.textContent = 'Run the reviewer before copying a review packet.';
                return;
            }

            await writeIdorClipboard(lastIdorPacket);
            copyIdorPacket.textContent = 'Copied review packet';

            window.setTimeout(() => {
                copyIdorPacket.textContent = 'Copy review packet';
            }, 1600);
        });

        idorPresetButtons.forEach((button) => {
            button.addEventListener('click', () => {
                const preset = idorPresets[button.dataset.idorPreset];

                if (preset) {
                    applyIdorPreset(preset);
                    idorForm.requestSubmit();
                }
            });
        });
    </script>
@endsection
