@extends('learning.layout', ['title' => 'Container Binding Plan Workbench'])

@section('content')
    <section class="hero">
        <span class="badge">Runnable workbench</span>
        <h1>Plan dependency injection before wiring classes together.</h1>
        <p>
            This workbench teaches how Laravel resolves contracts through the service container,
            where bindings live, and why callers should depend on interfaces for swappable behavior.
        </p>
    </section>

    <section class="section">
        <div class="grid">
            <article class="panel">
                <h2>Binding Input</h2>
                <form id="containerBindingPlanForm">
                    <label>
                        Contract name
                        <input name="contract_name" value="Payment Gateway" autocomplete="off">
                    </label>

                    <label style="margin-top: 12px;">
                        Implementation name
                        <input name="implementation_name" value="Stripe Payment Gateway" autocomplete="off">
                    </label>

                    <label style="margin-top: 12px;">
                        Lifetime
                        <select name="lifetime">
                            <option value="bind" selected>bind</option>
                            <option value="singleton">singleton</option>
                        </select>
                    </label>

                    <label style="margin-top: 12px;">
                        Injection target
                        <input name="injection_target" value="Checkout Controller" autocomplete="off">
                    </label>

                    <button class="button primary" type="submit" style="margin-top: 14px;">Plan binding</button>
                </form>
            </article>

            <article class="panel">
                <h2>Binding Plan</h2>
                <p class="muted" id="containerBindingPlanStatus">Submit input to build the binding plan.</p>
                <pre class="raw-json"><code id="containerBindingPlanOutput">POST /api/practice/container-binding-plan</code></pre>
            </article>

            <article class="panel">
                <h2>Files To Read</h2>
                <ul>
                    <li><code>routes/api/practice-actions.php</code></li>
                    <li><code>app/Http/Requests/Api/PlanContainerBindingRequest.php</code></li>
                    <li><code>app/Http/Controllers/Api/ContainerBindingPlanController.php</code></li>
                    <li><code>app/Services/Practice/ContainerBindingPlanService.php</code></li>
                    <li><code>bootstrap/providers.php</code></li>
                    <li><code>tests/Feature/ContainerBindingPlanWorkbenchTest.php</code></li>
                </ul>
            </article>
        </div>
    </section>

    <section class="section">
        <h2>Practice Variations</h2>
        <div class="list">
            <article class="item">
                <div class="meta">
                    <span class="badge">Contract</span>
                </div>
                <p>Rename the contract so it describes what the caller needs, not the vendor or implementation detail.</p>
            </article>
            <article class="item">
                <div class="meta">
                    <span class="badge">Lifetime</span>
                </div>
                <p>Switch between <code>bind</code> and <code>singleton</code> and inspect the generated provider binding.</p>
            </article>
            <article class="item">
                <div class="meta">
                    <span class="badge">Testing</span>
                </div>
                <p>Read the generated injection example and think about how a fake implementation would be swapped in a test.</p>
            </article>
        </div>
    </section>

    <script>
        const containerBindingPlanForm = document.querySelector('#containerBindingPlanForm');
        const containerBindingPlanStatus = document.querySelector('#containerBindingPlanStatus');
        const containerBindingPlanOutput = document.querySelector('#containerBindingPlanOutput');

        containerBindingPlanForm.addEventListener('submit', async (event) => {
            event.preventDefault();

            const payload = Object.fromEntries(new FormData(containerBindingPlanForm).entries());

            containerBindingPlanStatus.textContent = 'Running POST /api/practice/container-binding-plan...';
            containerBindingPlanOutput.textContent = JSON.stringify(payload, null, 2);

            try {
                const response = await fetch('{{ route('api.practice.container-binding-plan.store') }}', {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(payload),
                });
                const body = await response.json();

                containerBindingPlanStatus.textContent = `HTTP ${response.status}`;
                containerBindingPlanOutput.textContent = JSON.stringify(body, null, 2);
            } catch (error) {
                containerBindingPlanStatus.textContent = 'Request failed';
                containerBindingPlanOutput.textContent = error instanceof Error ? error.message : 'Unknown request error';
            }
        });
    </script>
@endsection
