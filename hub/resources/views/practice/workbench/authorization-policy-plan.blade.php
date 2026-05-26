@extends('learning.layout', ['title' => 'Authorization Policy Plan Workbench'])

@section('content')
    <section class="hero">
        <span class="badge">Runnable workbench</span>
        <h1>Move access-control rules into policies.</h1>
        <p>
            This workbench plans a Laravel policy method, controller authorization call,
            decision rule, and tests for allowed and forbidden users.
        </p>
    </section>

    <section class="section">
        <div class="grid">
            <article class="panel">
                <h2>Policy Input</h2>
                <form id="authorizationPolicyPlanForm">
                    <label>
                        Model name
                        <input name="model_name" value="Practice Task" autocomplete="off">
                    </label>
                    <label style="margin-top: 12px;">
                        Ability
                        <input name="ability" value="update" autocomplete="off">
                    </label>
                    <label style="margin-top: 12px;">
                        Actor role
                        <input name="actor_role" value="owner" autocomplete="off">
                    </label>
                    <label style="margin-top: 12px;">
                        Rule
                        <input name="rule" value="Only the owner can update an unfinished practice task." autocomplete="off">
                    </label>
                    <button class="button primary" type="submit" style="margin-top: 14px;">Plan policy</button>
                </form>
            </article>

            <article class="panel">
                <h2>Policy Plan</h2>
                <p class="muted" id="authorizationPolicyPlanStatus">Submit input to build the policy plan.</p>
                <pre class="raw-json"><code id="authorizationPolicyPlanOutput">POST /api/practice/authorization-policy-plan</code></pre>
            </article>

            <article class="panel">
                <h2>Files To Read</h2>
                <ul>
                    <li><code>routes/api/practice-actions.php</code></li>
                    <li><code>app/Http/Requests/Api/PlanAuthorizationPolicyRequest.php</code></li>
                    <li><code>app/Http/Controllers/Api/AuthorizationPolicyPlanController.php</code></li>
                    <li><code>app/Services/Practice/AuthorizationPolicyPlanService.php</code></li>
                    <li><code>tests/Feature/AuthorizationPolicyPlanWorkbenchTest.php</code></li>
                </ul>
            </article>
        </div>
    </section>

    <script>
        const authorizationPolicyPlanForm = document.querySelector('#authorizationPolicyPlanForm');
        const authorizationPolicyPlanStatus = document.querySelector('#authorizationPolicyPlanStatus');
        const authorizationPolicyPlanOutput = document.querySelector('#authorizationPolicyPlanOutput');

        authorizationPolicyPlanForm.addEventListener('submit', async (event) => {
            event.preventDefault();
            const payload = Object.fromEntries(new FormData(authorizationPolicyPlanForm).entries());

            authorizationPolicyPlanStatus.textContent = 'Running POST /api/practice/authorization-policy-plan...';
            authorizationPolicyPlanOutput.textContent = JSON.stringify(payload, null, 2);

            try {
                const response = await fetch('{{ route('api.practice.authorization-policy-plan.store') }}', {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(payload),
                });
                const body = await response.json();

                authorizationPolicyPlanStatus.textContent = `HTTP ${response.status}`;
                authorizationPolicyPlanOutput.textContent = JSON.stringify(body, null, 2);
            } catch (error) {
                authorizationPolicyPlanStatus.textContent = 'Request failed';
                authorizationPolicyPlanOutput.textContent = error instanceof Error ? error.message : 'Unknown request error';
            }
        });
    </script>
@endsection
