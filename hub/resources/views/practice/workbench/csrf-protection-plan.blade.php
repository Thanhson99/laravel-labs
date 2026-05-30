@extends('learning.layout', ['title' => 'CSRF Protection Workbench'])

@section('content')
    <section class="hero">
        <span class="badge">Runnable workbench</span>
        <h1>CSRF protection starts with browser intent, cookies, and tokens.</h1>
        <p>
            Practice explaining how CSRF works, why Laravel tokens matter,
            and how SameSite cookies reduce cross-site cookie exposure.
        </p>
    </section>

    <section class="section">
        <div class="grid">
            <article class="panel">
                <h2>Flow Input</h2>
                <form id="csrfForm">
                    <label>Flow name <input name="flow_name" value="Profile Update"></label>
                    <label style="margin-top: 12px;">Client type
                        <select name="client_type">
                            <option value="blade-web" selected>blade-web</option>
                            <option value="spa-stateful">spa-stateful</option>
                            <option value="api-bearer">api-bearer</option>
                        </select>
                    </label>
                    <label style="margin-top: 12px;">State-changing method
                        <select name="state_changing_method">
                            <option value="post" selected>post</option>
                            <option value="put">put</option>
                            <option value="patch">patch</option>
                            <option value="delete">delete</option>
                            <option value="get">get</option>
                        </select>
                    </label>
                    <label style="margin-top: 12px;">SameSite
                        <select name="same_site">
                            <option value="lax" selected>lax</option>
                            <option value="strict">strict</option>
                            <option value="none">none</option>
                            <option value="missing">missing</option>
                        </select>
                    </label>
                    <label style="margin-top: 12px;">
                        <input name="has_csrf_token" type="checkbox" checked>
                        Has CSRF token
                    </label>
                    <label style="margin-top: 12px;">
                        <input name="uses_cookie_auth" type="checkbox" checked>
                        Uses cookie auth
                    </label>
                    <button class="button primary" type="submit" style="margin-top: 14px;">Plan CSRF defense</button>
                    <button class="button" id="copyCsrfPacket" type="button" style="margin-top: 14px;">Copy review packet</button>
                </form>
            </article>

            <article class="panel">
                <h2>Scenario Presets</h2>
                <div class="list">
                    <button class="button" type="button" data-csrf-preset="blade-form">Blade form</button>
                    <button class="button" type="button" data-csrf-preset="spa-sanctum">SPA Sanctum</button>
                    <button class="button" type="button" data-csrf-preset="unsafe-get">Unsafe GET mutation</button>
                </div>
            </article>

            <article class="panel">
                <h2>Defense Plan</h2>
                <p class="muted" id="csrfStatus">Submit input to generate a CSRF protection plan.</p>
                <pre class="raw-json"><code id="csrfOutput">POST /api/practice/csrf-protection-plan</code></pre>
            </article>

            <article class="panel">
                <h2>Summary</h2>
                <div id="csrfSummary" class="list">
                    <p class="muted">Run the planner to see risk, attack flow, controls, SameSite review, tests, and interview answer.</p>
                </div>
            </article>
        </div>
    </section>

    <script>
        const csrfForm = document.querySelector('#csrfForm');
        const csrfStatus = document.querySelector('#csrfStatus');
        const csrfOutput = document.querySelector('#csrfOutput');
        const csrfSummary = document.querySelector('#csrfSummary');
        const copyCsrfPacket = document.querySelector('#copyCsrfPacket');
        const csrfPresetButtons = document.querySelectorAll('[data-csrf-preset]');
        let lastCsrfPacket = '';

        const csrfPresets = {
            'blade-form': {
                flow_name: 'Profile Update',
                client_type: 'blade-web',
                state_changing_method: 'post',
                same_site: 'lax',
                has_csrf_token: true,
                uses_cookie_auth: true,
            },
            'spa-sanctum': {
                flow_name: 'SPA Login',
                client_type: 'spa-stateful',
                state_changing_method: 'post',
                same_site: 'none',
                has_csrf_token: true,
                uses_cookie_auth: true,
            },
            'unsafe-get': {
                flow_name: 'Email Change',
                client_type: 'blade-web',
                state_changing_method: 'get',
                same_site: 'missing',
                has_csrf_token: false,
                uses_cookie_auth: true,
            },
        };

        function escapeCsrfHtml(value) {
            return String(value ?? '')
                .replaceAll('&', '&amp;')
                .replaceAll('<', '&lt;')
                .replaceAll('>', '&gt;')
                .replaceAll('"', '&quot;')
                .replaceAll("'", '&#039;');
        }

        function renderCsrfSummary(data) {
            lastCsrfPacket = String(data.review_packet_markdown || '');
            const attackFlow = data.attack_flow.map((item) => `<li>${escapeCsrfHtml(item)}</li>`).join('');
            const controls = data.controls.map((item) => `<li><strong>${escapeCsrfHtml(item.control)}</strong>: ${escapeCsrfHtml(item.purpose)}</li>`).join('');
            const tests = data.test_matrix.map((item) => `<li><strong>${escapeCsrfHtml(item.case)}</strong>: ${escapeCsrfHtml(item.expected)}</li>`).join('');
            const questions = data.review_questions.map((item) => `<li>${escapeCsrfHtml(item)}</li>`).join('');

            csrfSummary.innerHTML = `
                <div class="item">
                    <div class="meta">
                        <span class="badge pending">${escapeCsrfHtml(data.risk_score.label)} ${escapeCsrfHtml(data.risk_score.score)}/100</span>
                        <span class="badge">${escapeCsrfHtml(data.flow)}</span>
                        <span class="badge">${escapeCsrfHtml(data.same_site_review.setting)}</span>
                    </div>
                    <h3>${escapeCsrfHtml(data.recommendation)}</h3>
                    <p>${escapeCsrfHtml(data.interview_answer)}</p>
                </div>
                <div class="item"><h3>Attack Flow</h3><ul>${attackFlow}</ul></div>
                <div class="item"><h3>Controls</h3><ul>${controls}</ul></div>
                <div class="item"><h3>SameSite Review</h3><p>${escapeCsrfHtml(data.same_site_review.guidance)}</p><p>${escapeCsrfHtml(data.same_site_review.caution)}</p></div>
                <div class="item"><h3>Test Matrix</h3><ul>${tests}</ul></div>
                <div class="item"><h3>Feature Test Snippet</h3><pre class="raw-json"><code>${escapeCsrfHtml(data.feature_test_snippet)}</code></pre></div>
                <div class="item"><h3>Review Questions</h3><ul>${questions}</ul></div>
                <div class="item"><h3>Review Packet Markdown</h3><pre class="raw-json"><code>${escapeCsrfHtml(data.review_packet_markdown)}</code></pre></div>
            `;
        }

        function applyCsrfPreset(preset) {
            csrfForm.elements.flow_name.value = preset.flow_name;
            csrfForm.elements.client_type.value = preset.client_type;
            csrfForm.elements.state_changing_method.value = preset.state_changing_method;
            csrfForm.elements.same_site.value = preset.same_site;
            csrfForm.elements.has_csrf_token.checked = preset.has_csrf_token;
            csrfForm.elements.uses_cookie_auth.checked = preset.uses_cookie_auth;
            csrfStatus.textContent = `Loaded preset: ${preset.flow_name}`;
        }

        csrfForm.addEventListener('submit', async (event) => {
            event.preventDefault();

            const formData = new FormData(csrfForm);
            const payload = {
                flow_name: String(formData.get('flow_name') || ''),
                client_type: String(formData.get('client_type') || 'blade-web'),
                state_changing_method: String(formData.get('state_changing_method') || 'post'),
                same_site: String(formData.get('same_site') || 'lax'),
                has_csrf_token: formData.has('has_csrf_token'),
                uses_cookie_auth: formData.has('uses_cookie_auth'),
            };

            csrfStatus.textContent = 'Running POST /api/practice/csrf-protection-plan...';
            csrfOutput.textContent = JSON.stringify(payload, null, 2);

            try {
                const response = await fetch('{{ route('api.practice.csrf-protection-plan.store') }}', {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(payload),
                });
                const body = await response.json();

                csrfStatus.textContent = `HTTP ${response.status}`;
                csrfOutput.textContent = JSON.stringify(body, null, 2);

                if (body.data) {
                    renderCsrfSummary(body.data);
                }
            } catch (error) {
                csrfStatus.textContent = 'Request failed';
                csrfOutput.textContent = error instanceof Error ? error.message : 'Unknown request error';
                csrfSummary.innerHTML = '<p class="muted">No summary available.</p>';
            }
        });

        async function writeCsrfClipboard(value) {
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

        copyCsrfPacket.addEventListener('click', async () => {
            if (!lastCsrfPacket) {
                csrfStatus.textContent = 'Run the planner before copying a review packet.';
                return;
            }

            await writeCsrfClipboard(lastCsrfPacket);
            copyCsrfPacket.textContent = 'Copied review packet';

            window.setTimeout(() => {
                copyCsrfPacket.textContent = 'Copy review packet';
            }, 1600);
        });

        csrfPresetButtons.forEach((button) => {
            button.addEventListener('click', () => {
                const preset = csrfPresets[button.dataset.csrfPreset];

                if (preset) {
                    applyCsrfPreset(preset);
                    csrfForm.requestSubmit();
                }
            });
        });
    </script>
@endsection
