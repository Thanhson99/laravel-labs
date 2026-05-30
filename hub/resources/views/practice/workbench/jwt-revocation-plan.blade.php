@extends('learning.layout', ['title' => 'JWT Revocation Plan Workbench'])

@section('content')
    <section class="hero">
        <span class="badge">Runnable workbench</span>
        <h1>Plan JWT revocation with API and database checks.</h1>
        <p>
            This workbench shows why a stateless JWT cannot be revoked by itself, then builds
            denylist, token-version, refresh-rotation, middleware, and database steps.
        </p>
    </section>

    <section class="section">
        <div class="grid">
            <article class="panel">
                <h2>Revocation Context</h2>
                <form id="jwtRevocationPlanForm">
                    <label>
                        Scenario preset
                        <select id="jwtRevocationPreset">
                            <option value="logoutEverywhere">Logout everywhere after account risk</option>
                            <option value="roleChange">Role change needs immediate downgrade</option>
                            <option value="mobileLostDevice">Mobile lost-device response</option>
                            <option value="lowRiskApi">Low-risk API with short access tokens</option>
                        </select>
                    </label>
                    <label style="margin-top: 12px;">
                        Client type
                        <select name="client_type">
                            <option value="browser-spa">browser-spa</option>
                            <option value="mobile-app">mobile-app</option>
                            <option value="server-api">server-api</option>
                            <option value="third-party-api">third-party-api</option>
                        </select>
                    </label>
                    <label style="margin-top: 12px;">
                        Revocation model
                        <select name="revocation_model">
                            <option value="auto">auto</option>
                            <option value="denylist">denylist</option>
                            <option value="token-version">token-version</option>
                            <option value="refresh-rotation">refresh-rotation</option>
                            <option value="short-lived-access-token">short-lived-access-token</option>
                        </select>
                    </label>
                    <label style="margin-top: 12px;">
                        Token lifetime
                        <select name="token_lifetime">
                            <option value="minutes">minutes</option>
                            <option value="hours">hours</option>
                            <option value="days">days</option>
                        </select>
                    </label>
                    <label style="margin-top: 12px;">
                        Revocation store
                        <select name="revocation_store">
                            <option value="cache">cache</option>
                            <option value="database">database</option>
                            <option value="redis">redis</option>
                        </select>
                    </label>
                    <label style="margin-top: 12px;">
                        Immediate logout
                        <select name="immediate_logout">
                            <option value="yes">yes</option>
                            <option value="no">no</option>
                        </select>
                    </label>
                    <label style="margin-top: 12px;">
                        Refresh rotation
                        <select name="refresh_rotation">
                            <option value="yes">yes</option>
                            <option value="no">no</option>
                        </select>
                    </label>
                    <button class="button primary" type="submit" style="margin-top: 14px;">Plan JWT revocation</button>
                </form>
            </article>

            <article class="panel">
                <h2>Revocation Plan</h2>
                <p class="muted" id="jwtRevocationPlanStatus">Submit input to build the JWT revocation plan.</p>
                <pre class="raw-json"><code id="jwtRevocationPlanOutput">POST /api/practice/jwt-revocation-plan</code></pre>
            </article>

            <article class="panel">
                <h2>Files To Read</h2>
                <ul>
                    <li><code>routes/api/practice-actions.php</code></li>
                    <li><code>app/Http/Requests/Api/PlanJwtRevocationRequest.php</code></li>
                    <li><code>app/Http/Controllers/Api/JwtRevocationPlanController.php</code></li>
                    <li><code>app/Services/Practice/JwtRevocationPlanService.php</code></li>
                    <li><code>tests/Feature/JwtRevocationPlanWorkbenchTest.php</code></li>
                </ul>
            </article>
        </div>
    </section>

    <script>
        const jwtRevocationPresets = {
            logoutEverywhere: {
                client_type: 'browser-spa',
                revocation_model: 'denylist',
                token_lifetime: 'hours',
                revocation_store: 'database',
                immediate_logout: 'yes',
                refresh_rotation: 'yes',
            },
            roleChange: {
                client_type: 'server-api',
                revocation_model: 'token-version',
                token_lifetime: 'hours',
                revocation_store: 'database',
                immediate_logout: 'yes',
                refresh_rotation: 'yes',
            },
            mobileLostDevice: {
                client_type: 'mobile-app',
                revocation_model: 'denylist',
                token_lifetime: 'days',
                revocation_store: 'redis',
                immediate_logout: 'yes',
                refresh_rotation: 'yes',
            },
            lowRiskApi: {
                client_type: 'third-party-api',
                revocation_model: 'short-lived-access-token',
                token_lifetime: 'minutes',
                revocation_store: 'cache',
                immediate_logout: 'no',
                refresh_rotation: 'no',
            },
        };

        const jwtRevocationPreset = document.querySelector('#jwtRevocationPreset');
        const jwtRevocationPlanForm = document.querySelector('#jwtRevocationPlanForm');
        const jwtRevocationPlanStatus = document.querySelector('#jwtRevocationPlanStatus');
        const jwtRevocationPlanOutput = document.querySelector('#jwtRevocationPlanOutput');

        function applyJwtRevocationPreset(presetName) {
            const preset = jwtRevocationPresets[presetName];

            if (!preset) {
                return;
            }

            Object.entries(preset).forEach(([field, value]) => {
                const input = jwtRevocationPlanForm.elements.namedItem(field);

                if (input) {
                    input.value = value;
                }
            });
        }

        jwtRevocationPreset.addEventListener('change', (event) => {
            applyJwtRevocationPreset(event.target.value);
        });

        applyJwtRevocationPreset(jwtRevocationPreset.value);

        jwtRevocationPlanForm.addEventListener('submit', async (event) => {
            event.preventDefault();
            const payload = Object.fromEntries(new FormData(jwtRevocationPlanForm).entries());

            jwtRevocationPlanStatus.textContent = 'Running POST /api/practice/jwt-revocation-plan...';
            jwtRevocationPlanOutput.textContent = JSON.stringify(payload, null, 2);

            try {
                const response = await fetch('{{ route('api.practice.jwt-revocation-plan.store') }}', {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(payload),
                });
                const body = await response.json();

                jwtRevocationPlanStatus.textContent = `HTTP ${response.status}`;
                jwtRevocationPlanOutput.textContent = JSON.stringify(body, null, 2);
            } catch (error) {
                jwtRevocationPlanStatus.textContent = 'Request failed';
                jwtRevocationPlanOutput.textContent = error instanceof Error ? error.message : 'Unknown request error';
            }
        });
    </script>
@endsection
