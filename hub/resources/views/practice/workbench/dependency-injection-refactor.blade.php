@extends('learning.layout', ['title' => 'Dependency Injection Refactor Workbench'])

@section('content')
    <section class="hero">
        <span class="badge">Runnable workbench</span>
        <h1>Refactor manual dependencies into constructor injection.</h1>
        <p>
            This workbench turns a concrete <code>new</code> call into a contract, provider binding,
            constructor injection, file-by-file refactor map, container-resolution test, and a test swap so the refactor is easy to practice end to end.
        </p>
    </section>

    <section class="section">
        <div class="grid">
            <article class="panel">
                <h2>Refactor Input</h2>
                <label>
                    Scenario preset
                    <select id="dependencyInjectionPreset">
                        <option value="report" selected>Report export</option>
                        <option value="payment">Payment gateway</option>
                        <option value="sync">External sync</option>
                    </select>
                </label>

                <form id="dependencyInjectionRefactorForm">
                    <label style="margin-top: 12px;">
                        Class being refactored
                        <input name="class_name" value="Report Controller" autocomplete="off">
                    </label>

                    <label style="margin-top: 12px;">
                        Manual dependency
                        <input name="manual_dependency" value="Csv Report Exporter" autocomplete="off">
                    </label>

                    <label style="margin-top: 12px;">
                        Dependency role
                        <input name="dependency_role" value="export monthly report rows" autocomplete="off">
                    </label>

                    <label style="margin-top: 12px;">
                        Contract name
                        <input name="contract_name" value="Report Exporter" autocomplete="off">
                    </label>

                    <label style="margin-top: 12px;">
                        Contract method
                        <input name="method_name" value="export" autocomplete="off">
                    </label>

                    <label style="margin-top: 12px;">
                        Binding lifetime
                        <select name="binding_lifetime">
                            <option value="bind" selected>bind</option>
                            <option value="singleton">singleton</option>
                        </select>
                    </label>

                    <label style="margin-top: 12px;">
                        Test fake name
                        <input name="fake_name" value="Fake Report Exporter" autocomplete="off">
                    </label>

                    <button class="button primary" type="submit" style="margin-top: 14px;">Plan DI refactor</button>
                </form>
            </article>

            <article class="panel">
                <h2>Refactor Plan</h2>
                <p class="muted" id="dependencyInjectionRefactorStatus">Submit input to generate the refactor plan, implementation map, and container-resolution test.</p>
                <pre class="raw-json"><code id="dependencyInjectionRefactorOutput">POST /api/practice/dependency-injection-refactor</code></pre>
            </article>

            <article class="panel">
                <h2>Files To Read</h2>
                <ul>
                    <li><code>routes/api/practice-actions.php</code></li>
                    <li><code>routes/web/workbench.php</code></li>
                    <li><code>app/Http/Requests/Api/PlanDependencyInjectionRefactorRequest.php</code></li>
                    <li><code>app/Http/Controllers/Api/DependencyInjectionRefactorController.php</code></li>
                    <li><code>app/Services/Practice/DependencyInjectionRefactorService.php</code></li>
                    <li><code>tests/Feature/DependencyInjectionRefactorWorkbenchTest.php</code></li>
                </ul>
                <p style="margin-top: 14px;">
                    <a class="button" href="{{ route('practice.workbench.container-binding-plan') }}">Open binding planner</a>
                </p>
            </article>
        </div>
    </section>

    <section class="section">
        <h2>Refactor Checklist</h2>
        <div class="list">
            <article class="item">
                <div class="meta">
                    <span class="badge">Before</span>
                </div>
                <p>Find the exact <code>new</code> call that hides a concrete collaborator inside business logic.</p>
            </article>
            <article class="item">
                <div class="meta">
                    <span class="badge">Boundary</span>
                </div>
                <p>Name the contract after the behavior the caller needs, not after the vendor or package detail.</p>
            </article>
            <article class="item">
                <div class="meta">
                    <span class="badge">Proof</span>
                </div>
                <p>Swap the contract with a fake in a test to prove the code no longer depends on one concrete class.</p>
            </article>
            <article class="item">
                <div class="meta">
                    <span class="badge">Review</span>
                </div>
                <p>Use the generated checklist to confirm the concrete dependency is not recreated in a deeper layer.</p>
            </article>
        </div>
    </section>

    <script>
        const dependencyInjectionRefactorForm = document.querySelector('#dependencyInjectionRefactorForm');
        const dependencyInjectionPreset = document.querySelector('#dependencyInjectionPreset');
        const dependencyInjectionRefactorStatus = document.querySelector('#dependencyInjectionRefactorStatus');
        const dependencyInjectionRefactorOutput = document.querySelector('#dependencyInjectionRefactorOutput');
        const dependencyInjectionPresets = {
            report: {
                class_name: 'Report Controller',
                manual_dependency: 'Csv Report Exporter',
                dependency_role: 'export monthly report rows',
                contract_name: 'Report Exporter',
                method_name: 'export',
                binding_lifetime: 'bind',
                fake_name: 'Fake Report Exporter',
            },
            payment: {
                class_name: 'Checkout Service',
                manual_dependency: 'Stripe Payment Gateway',
                dependency_role: 'charge customer payments',
                contract_name: 'Payment Gateway Contract',
                method_name: 'chargeCustomer',
                binding_lifetime: 'singleton',
                fake_name: 'Fake Payment Gateway',
            },
            sync: {
                class_name: 'Customer Sync Job',
                manual_dependency: 'Hubspot Customer Client',
                dependency_role: 'sync customer records to an external CRM',
                contract_name: 'Customer Sync Client',
                method_name: 'syncCustomer',
                binding_lifetime: 'bind',
                fake_name: 'Fake Customer Sync Client',
            },
        };

        function applyDependencyInjectionPreset(name) {
            const preset = dependencyInjectionPresets[name] || dependencyInjectionPresets.report;

            Object.entries(preset).forEach(([field, value]) => {
                const input = dependencyInjectionRefactorForm.elements[field];

                if (input) {
                    input.value = value;
                }
            });
        }

        dependencyInjectionPreset.addEventListener('change', (event) => {
            applyDependencyInjectionPreset(event.target.value);
        });

        dependencyInjectionRefactorForm.addEventListener('submit', async (event) => {
            event.preventDefault();

            const payload = Object.fromEntries(new FormData(dependencyInjectionRefactorForm).entries());

            dependencyInjectionRefactorStatus.textContent = 'Running POST /api/practice/dependency-injection-refactor...';
            dependencyInjectionRefactorOutput.textContent = JSON.stringify(payload, null, 2);

            try {
                const response = await fetch('{{ route('api.practice.dependency-injection-refactor.store') }}', {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(payload),
                });
                const body = await response.json();

                dependencyInjectionRefactorStatus.textContent = `HTTP ${response.status}`;
                dependencyInjectionRefactorOutput.textContent = JSON.stringify(body, null, 2);
            } catch (error) {
                dependencyInjectionRefactorStatus.textContent = 'Request failed';
                dependencyInjectionRefactorOutput.textContent = error instanceof Error ? error.message : 'Unknown request error';
            }
        });
    </script>
@endsection
