@extends('learning.layout', ['title' => 'Layered Architecture Decision Workbench'])

@section('content')
    <section class="hero">
        <span class="badge">Runnable workbench</span>
        <h1>Decide when Layered Architecture helps and when it adds ceremony.</h1>
        <p>
            This workbench turns Clean Architecture P6 into a concrete Laravel decision:
            keep a feature simple, add a focused action/service, or introduce explicit layers only when boundaries are real.
        </p>
    </section>

    <section class="section">
        <div class="grid">
            <article class="panel">
                <h2>Feature Input</h2>
                <label>
                    Scenario preset
                    <select id="layeredArchitecturePreset">
                        <option value="crud" selected>Simple CRUD screen</option>
                        <option value="workflow">Order checkout workflow</option>
                        <option value="integration">External payment sync</option>
                    </select>
                </label>

                <form id="layeredArchitectureForm">
                    <label style="margin-top: 12px;">
                        Feature name
                        <input name="feature_name" value="Order Create" autocomplete="off">
                    </label>

                    <label style="margin-top: 12px;">
                        Feature type
                        <select name="feature_type">
                            <option value="crud" selected>simple CRUD</option>
                            <option value="workflow">workflow with business decisions</option>
                            <option value="integration">external integration</option>
                        </select>
                    </label>

                    <label style="margin-top: 12px;">
                        Business rule count
                        <input type="number" name="business_rule_count" min="0" max="12" value="1">
                    </label>

                    <label style="margin-top: 12px;">
                        Integration count
                        <input type="number" name="integration_count" min="0" max="8" value="0">
                    </label>

                    <label style="margin-top: 12px;">
                        Persistence complexity
                        <select name="persistence_complexity">
                            <option value="none">none</option>
                            <option value="simple" selected>simple Eloquent write</option>
                            <option value="complex">complex reusable query/persistence</option>
                        </select>
                    </label>

                    <label style="margin-top: 12px;">
                        <input type="checkbox" name="requires_async_work" value="1">
                        Requires async work
                    </label>

                    <label style="margin-top: 12px;">
                        <input type="checkbox" name="requires_policy" value="1">
                        Requires policy or ownership checks
                    </label>

                    <button class="button primary" type="submit" style="margin-top: 14px;">Decide layers</button>
                </form>
            </article>

            <article class="panel">
                <h2>Decision Output</h2>
                <p class="muted" id="layeredArchitectureStatus">Submit input to generate a layer plan, implementation steps, review questions, tests, and an interview answer.</p>
                <pre class="raw-json"><code id="layeredArchitectureOutput">POST /api/practice/layered-architecture-decision</code></pre>
            </article>

            <article class="panel">
                <h2>Files To Read</h2>
                <ul>
                    <li><code>data/laravel/container-architecture.en.json</code></li>
                    <li><code>data/laravel/container-architecture.vi.json</code></li>
                    <li><code>app/Http/Requests/Api/PlanLayeredArchitectureDecisionRequest.php</code></li>
                    <li><code>app/Http/Controllers/Api/LayeredArchitectureDecisionController.php</code></li>
                    <li><code>app/Services/Practice/LayeredArchitectureDecisionService.php</code></li>
                    <li><code>tests/Feature/LayeredArchitectureDecisionWorkbenchTest.php</code></li>
                </ul>
            </article>
        </div>
    </section>

    <script>
        const layeredArchitectureForm = document.querySelector('#layeredArchitectureForm');
        const layeredArchitecturePreset = document.querySelector('#layeredArchitecturePreset');
        const layeredArchitectureStatus = document.querySelector('#layeredArchitectureStatus');
        const layeredArchitectureOutput = document.querySelector('#layeredArchitectureOutput');
        const layeredArchitecturePresets = {
            crud: {
                feature_name: 'Order Create',
                feature_type: 'crud',
                business_rule_count: 1,
                integration_count: 0,
                persistence_complexity: 'simple',
                requires_async_work: false,
                requires_policy: false,
            },
            workflow: {
                feature_name: 'Checkout Submit',
                feature_type: 'workflow',
                business_rule_count: 4,
                integration_count: 1,
                persistence_complexity: 'complex',
                requires_async_work: true,
                requires_policy: true,
            },
            integration: {
                feature_name: 'Payment Sync',
                feature_type: 'integration',
                business_rule_count: 3,
                integration_count: 2,
                persistence_complexity: 'complex',
                requires_async_work: true,
                requires_policy: false,
            },
        };

        function applyLayeredArchitecturePreset(name) {
            const preset = layeredArchitecturePresets[name] || layeredArchitecturePresets.crud;

            Object.entries(preset).forEach(([field, value]) => {
                const input = layeredArchitectureForm.elements[field];

                if (!input) {
                    return;
                }

                if (input.type === 'checkbox') {
                    input.checked = Boolean(value);
                    return;
                }

                input.value = value;
            });
        }

        layeredArchitecturePreset.addEventListener('change', (event) => {
            applyLayeredArchitecturePreset(event.target.value);
        });

        layeredArchitectureForm.addEventListener('submit', async (event) => {
            event.preventDefault();

            const formData = new FormData(layeredArchitectureForm);
            const payload = {
                feature_name: formData.get('feature_name'),
                feature_type: formData.get('feature_type'),
                business_rule_count: Number(formData.get('business_rule_count')),
                integration_count: Number(formData.get('integration_count')),
                persistence_complexity: formData.get('persistence_complexity'),
                requires_async_work: formData.has('requires_async_work'),
                requires_policy: formData.has('requires_policy'),
            };

            layeredArchitectureStatus.textContent = 'Running POST /api/practice/layered-architecture-decision...';
            layeredArchitectureOutput.textContent = JSON.stringify(payload, null, 2);

            try {
                const response = await fetch('{{ route('api.practice.layered-architecture-decision.store') }}', {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(payload),
                });
                const body = await response.json();

                layeredArchitectureStatus.textContent = `HTTP ${response.status}`;
                layeredArchitectureOutput.textContent = JSON.stringify(body, null, 2);
            } catch (error) {
                layeredArchitectureStatus.textContent = 'Request failed';
                layeredArchitectureOutput.textContent = error instanceof Error ? error.message : 'Unknown request error';
            }
        });
    </script>
@endsection
