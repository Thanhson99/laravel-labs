@extends('learning.layout', ['title' => 'JWT Token Storage Plan Workbench'])

@section('content')
    <section class="hero">
        <span class="badge">Runnable workbench</span>
        <h1>Choose safer JWT storage by threat model.</h1>
        <p>
            This workbench compares localStorage, HttpOnly cookies, bearer tokens, and platform storage
            so learners can explain XSS, CSRF, expiry, refresh, and revocation tradeoffs clearly.
        </p>
    </section>

    <section class="section">
        <div class="grid">
            <article class="panel">
                <h2>Storage Context</h2>
                <form id="jwtTokenStoragePlanForm">
                    <label>
                        Scenario preset
                        <select id="jwtTokenStoragePreset">
                            <option value="sameDomainSpa">Same-domain SPA with CSRF controls</option>
                            <option value="crossDomainSpa">Cross-domain SPA needing hardening</option>
                            <option value="mobileApp">Mobile app with secure platform storage</option>
                            <option value="thirdPartyApi">Third-party API integration</option>
                            <option value="learningDemo">Low-risk learning demo</option>
                        </select>
                    </label>
                    <label style="margin-top: 12px;">
                        Client type
                        <select name="client_type">
                            <option value="same-domain-spa">same-domain-spa</option>
                            <option value="cross-domain-spa">cross-domain-spa</option>
                            <option value="mobile-app">mobile-app</option>
                            <option value="third-party-api">third-party-api</option>
                            <option value="low-risk-demo">low-risk-demo</option>
                        </select>
                    </label>
                    <label style="margin-top: 12px;">
                        Current storage
                        <select name="current_storage">
                            <option value="unknown">unknown</option>
                            <option value="localStorage">localStorage</option>
                            <option value="http-only-cookie">http-only-cookie</option>
                            <option value="memory">memory</option>
                            <option value="bearer-token">bearer-token</option>
                            <option value="platform-storage">platform-storage</option>
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
                        XSS risk
                        <select name="xss_risk">
                            <option value="high">high</option>
                            <option value="medium">medium</option>
                            <option value="low">low</option>
                        </select>
                    </label>
                    <label style="margin-top: 12px;">
                        CSRF controls
                        <select name="csrf_controls">
                            <option value="strong">strong</option>
                            <option value="basic">basic</option>
                            <option value="none">none</option>
                        </select>
                    </label>
                    <label style="margin-top: 12px;">
                        Refresh token
                        <select name="refresh_token">
                            <option value="yes">yes</option>
                            <option value="no">no</option>
                        </select>
                    </label>
                    <button class="button primary" type="submit" style="margin-top: 14px;">Plan JWT storage</button>
                </form>
            </article>

            <article class="panel">
                <h2>Storage Plan</h2>
                <p class="muted" id="jwtTokenStoragePlanStatus">Submit input to build the JWT token-storage plan.</p>
                <pre class="raw-json"><code id="jwtTokenStoragePlanOutput">POST /api/practice/jwt-token-storage-plan</code></pre>
            </article>

            <article class="panel">
                <h2>Files To Read</h2>
                <ul>
                    <li><code>routes/api/practice-actions.php</code></li>
                    <li><code>app/Http/Requests/Api/PlanJwtTokenStorageRequest.php</code></li>
                    <li><code>app/Http/Controllers/Api/JwtTokenStoragePlanController.php</code></li>
                    <li><code>app/Services/Practice/JwtTokenStoragePlanService.php</code></li>
                    <li><code>tests/Feature/JwtTokenStoragePlanWorkbenchTest.php</code></li>
                </ul>
            </article>
        </div>
    </section>

    <script>
        const jwtTokenStoragePresets = {
            sameDomainSpa: {
                client_type: 'same-domain-spa',
                current_storage: 'localStorage',
                token_lifetime: 'minutes',
                xss_risk: 'high',
                csrf_controls: 'strong',
                refresh_token: 'yes',
            },
            crossDomainSpa: {
                client_type: 'cross-domain-spa',
                current_storage: 'localStorage',
                token_lifetime: 'hours',
                xss_risk: 'medium',
                csrf_controls: 'basic',
                refresh_token: 'yes',
            },
            mobileApp: {
                client_type: 'mobile-app',
                current_storage: 'platform-storage',
                token_lifetime: 'hours',
                xss_risk: 'low',
                csrf_controls: 'none',
                refresh_token: 'yes',
            },
            thirdPartyApi: {
                client_type: 'third-party-api',
                current_storage: 'bearer-token',
                token_lifetime: 'hours',
                xss_risk: 'medium',
                csrf_controls: 'none',
                refresh_token: 'no',
            },
            learningDemo: {
                client_type: 'low-risk-demo',
                current_storage: 'localStorage',
                token_lifetime: 'minutes',
                xss_risk: 'low',
                csrf_controls: 'none',
                refresh_token: 'no',
            },
        };

        const jwtTokenStoragePreset = document.querySelector('#jwtTokenStoragePreset');
        const jwtTokenStoragePlanForm = document.querySelector('#jwtTokenStoragePlanForm');
        const jwtTokenStoragePlanStatus = document.querySelector('#jwtTokenStoragePlanStatus');
        const jwtTokenStoragePlanOutput = document.querySelector('#jwtTokenStoragePlanOutput');

        function applyJwtTokenStoragePreset(presetName) {
            const preset = jwtTokenStoragePresets[presetName];

            if (!preset) {
                return;
            }

            Object.entries(preset).forEach(([field, value]) => {
                const input = jwtTokenStoragePlanForm.elements.namedItem(field);

                if (input) {
                    input.value = value;
                }
            });
        }

        jwtTokenStoragePreset.addEventListener('change', (event) => {
            applyJwtTokenStoragePreset(event.target.value);
        });

        applyJwtTokenStoragePreset(jwtTokenStoragePreset.value);

        jwtTokenStoragePlanForm.addEventListener('submit', async (event) => {
            event.preventDefault();
            const payload = Object.fromEntries(new FormData(jwtTokenStoragePlanForm).entries());

            jwtTokenStoragePlanStatus.textContent = 'Running POST /api/practice/jwt-token-storage-plan...';
            jwtTokenStoragePlanOutput.textContent = JSON.stringify(payload, null, 2);

            try {
                const response = await fetch('{{ route('api.practice.jwt-token-storage-plan.store') }}', {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(payload),
                });
                const body = await response.json();

                jwtTokenStoragePlanStatus.textContent = `HTTP ${response.status}`;
                jwtTokenStoragePlanOutput.textContent = JSON.stringify(body, null, 2);
            } catch (error) {
                jwtTokenStoragePlanStatus.textContent = 'Request failed';
                jwtTokenStoragePlanOutput.textContent = error instanceof Error ? error.message : 'Unknown request error';
            }
        });
    </script>
@endsection
