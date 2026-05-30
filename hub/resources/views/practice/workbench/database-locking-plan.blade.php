@extends('learning.layout', ['title' => 'Database Locking Plan Workbench'])

@section('content')
    <section class="hero">
        <span class="badge">Runnable workbench</span>
        <h1>Plan transaction-bound row locking.</h1>
        <p>
            Build a Laravel locking plan from the protected invariant first, then connect
            DB::transaction(), lockForUpdate(), failure tests, deadlock handling, and lock-wait evidence.
        </p>
    </section>

    <section class="section">
        <div class="grid">
            <article class="panel">
                <h2>Locking Input</h2>
                <label>
                    Scenario preset
                    <select id="databaseLockingPreset">
                        <option value="inventory" selected>Inventory reservation</option>
                        <option value="balance">Wallet debit</option>
                        <option value="booking">Booking slot</option>
                    </select>
                </label>

                <form id="databaseLockingPlanForm">
                    <label style="margin-top: 12px;">
                        Scenario name
                        <input name="scenario_name" value="Inventory Reservation" autocomplete="off">
                    </label>

                    <label style="margin-top: 12px;">
                        Protected invariant
                        <select name="invariant">
                            <option value="inventory" selected>inventory</option>
                            <option value="balance">balance</option>
                            <option value="booking">booking</option>
                            <option value="workflow-state">workflow state</option>
                        </select>
                    </label>

                    <label style="margin-top: 12px;">
                        Resource type
                        <select name="resource_type">
                            <option value="product" selected>product</option>
                            <option value="account">account</option>
                            <option value="booking_slot">booking slot</option>
                            <option value="workflow_item">workflow item</option>
                        </select>
                    </label>

                    <label style="margin-top: 12px;">
                        Concurrent requests
                        <input name="concurrent_requests" type="number" min="2" max="1000" value="25">
                    </label>

                    <label style="margin-top: 12px;">
                        <input type="checkbox" name="touches_multiple_rows" value="1">
                        Touches multiple rows
                    </label>

                    <label style="margin-top: 12px;">
                        <input type="checkbox" name="has_external_side_effect" value="1">
                        Has external side effect
                    </label>

                    <label style="margin-top: 12px;">
                        Expected failure
                        <select name="expected_failure">
                            <option value="reject" selected>reject business rule</option>
                            <option value="retry">retry transient failure</option>
                            <option value="fail-closed">fail closed</option>
                        </select>
                    </label>

                    <button class="button primary" type="submit" style="margin-top: 14px;">Plan locking</button>
                </form>
            </article>

            <article class="panel">
                <h2>Locking Plan</h2>
                <p class="muted" id="databaseLockingPlanStatus">Submit input to generate transaction boundary, lock scope, tests, observability, and interview evidence.</p>
                <div style="display: grid; gap: 12px; margin: 14px 0;">
                    <div>
                        <strong>Readiness</strong>
                        <p class="muted" id="databaseLockingReadiness">No score yet.</p>
                    </div>
                    <div>
                        <strong>Blockers</strong>
                        <ul id="databaseLockingBlockers">
                            <li>No blockers generated yet.</li>
                        </ul>
                    </div>
                    <div>
                        <strong>Next actions</strong>
                        <ul id="databaseLockingNextActions">
                            <li>No next actions generated yet.</li>
                        </ul>
                    </div>
                    <div>
                        <strong>Failure mode matrix</strong>
                        <ul id="databaseLockingFailureModes">
                            <li>No failure modes generated yet.</li>
                        </ul>
                    </div>
                    <div>
                        <strong>Implementation blueprint</strong>
                        <p class="muted" id="databaseLockingBlueprintSummary">No implementation blueprint generated yet.</p>
                        <ul id="databaseLockingBlueprintFiles">
                            <li>No implementation files generated yet.</li>
                        </ul>
                    </div>
                    <div>
                        <strong>Strategy decision table</strong>
                        <ul id="databaseLockingStrategies">
                            <li>No strategy comparison generated yet.</li>
                        </ul>
                    </div>
                </div>
                <pre class="raw-json"><code id="databaseLockingPlanOutput">POST /api/practice/database-locking-plan</code></pre>
            </article>

            <article class="panel">
                <h2>Files To Read</h2>
                <ul>
                    <li><code>data/laravel/data.en.json</code></li>
                    <li><code>app/Http/Requests/Api/PlanDatabaseLockingRequest.php</code></li>
                    <li><code>app/Services/Practice/DatabaseLockingPlanService.php</code></li>
                    <li><code>app/Services/Practice/DatabaseLockingTopicService.php</code></li>
                    <li><code>tests/Feature/DatabaseLockingPlanWorkbenchTest.php</code></li>
                    <li><code>tests/Unit/Practice/DatabaseLockingTopicServiceTest.php</code></li>
                </ul>
            </article>
        </div>
    </section>

    <script>
        const databaseLockingPlanForm = document.querySelector('#databaseLockingPlanForm');
        const databaseLockingPreset = document.querySelector('#databaseLockingPreset');
        const databaseLockingPlanStatus = document.querySelector('#databaseLockingPlanStatus');
        const databaseLockingPlanOutput = document.querySelector('#databaseLockingPlanOutput');
        const databaseLockingReadiness = document.querySelector('#databaseLockingReadiness');
        const databaseLockingBlockers = document.querySelector('#databaseLockingBlockers');
        const databaseLockingNextActions = document.querySelector('#databaseLockingNextActions');
        const databaseLockingFailureModes = document.querySelector('#databaseLockingFailureModes');
        const databaseLockingBlueprintSummary = document.querySelector('#databaseLockingBlueprintSummary');
        const databaseLockingBlueprintFiles = document.querySelector('#databaseLockingBlueprintFiles');
        const databaseLockingStrategies = document.querySelector('#databaseLockingStrategies');
        const databaseLockingPresets = {
            inventory: {
                scenario_name: 'Inventory Reservation',
                invariant: 'inventory',
                resource_type: 'product',
                concurrent_requests: 25,
                touches_multiple_rows: false,
                has_external_side_effect: false,
                expected_failure: 'reject',
            },
            balance: {
                scenario_name: 'Wallet Debit',
                invariant: 'balance',
                resource_type: 'account',
                concurrent_requests: 40,
                touches_multiple_rows: true,
                has_external_side_effect: true,
                expected_failure: 'retry',
            },
            booking: {
                scenario_name: 'Booking Slot',
                invariant: 'booking',
                resource_type: 'booking_slot',
                concurrent_requests: 12,
                touches_multiple_rows: false,
                has_external_side_effect: false,
                expected_failure: 'fail-closed',
            },
        };

        function applyDatabaseLockingPreset(name) {
            const preset = databaseLockingPresets[name] || databaseLockingPresets.inventory;

            Object.entries(preset).forEach(([field, value]) => {
                const input = databaseLockingPlanForm.elements[field];

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

        function renderDatabaseLockingList(element, items, emptyText) {
            const values = Array.isArray(items) ? items : [];
            element.innerHTML = '';

            if (values.length === 0) {
                const empty = document.createElement('li');
                empty.textContent = emptyText;
                element.appendChild(empty);
                return;
            }

            values.forEach((item) => {
                const li = document.createElement('li');
                li.textContent = String(item);
                element.appendChild(li);
            });
        }

        function renderDatabaseLockingSummary(body) {
            const scorecard = body?.data?.readiness_scorecard;
            const failureModes = body?.data?.failure_mode_matrix;
            const blueprint = body?.data?.implementation_blueprint;
            const strategies = body?.data?.strategy_decision_table;

            if (!scorecard) {
                databaseLockingReadiness.textContent = 'No score yet.';
                renderDatabaseLockingList(databaseLockingBlockers, [], 'No blockers generated yet.');
                renderDatabaseLockingList(databaseLockingNextActions, [], 'No next actions generated yet.');
                renderDatabaseLockingList(databaseLockingFailureModes, [], 'No failure modes generated yet.');
                databaseLockingBlueprintSummary.textContent = 'No implementation blueprint generated yet.';
                renderDatabaseLockingList(databaseLockingBlueprintFiles, [], 'No implementation files generated yet.');
                renderDatabaseLockingList(databaseLockingStrategies, [], 'No strategy comparison generated yet.');
                return;
            }

            databaseLockingReadiness.textContent = `${scorecard.score}/100 - ${scorecard.verdict}`;
            renderDatabaseLockingList(databaseLockingBlockers, scorecard.blockers, 'No blockers. Ready for focused implementation review.');
            renderDatabaseLockingList(databaseLockingNextActions, scorecard.next_actions, 'No next actions generated yet.');
            renderDatabaseLockingList(
                databaseLockingFailureModes,
                Array.isArray(failureModes)
                    ? failureModes.map((row) => `${row.mode}: ${row.guardrail} Test: ${row.test_evidence}`)
                    : [],
                'No failure modes generated yet.'
            );
            databaseLockingBlueprintSummary.textContent = blueprint
                ? `${blueprint.service_class}::${blueprint.service_method}()`
                : 'No implementation blueprint generated yet.';
            renderDatabaseLockingList(
                databaseLockingBlueprintFiles,
                blueprint && Array.isArray(blueprint.files)
                    ? blueprint.files.map((file) => `${file.path}: ${file.purpose}`)
                    : [],
                'No implementation files generated yet.'
            );
            renderDatabaseLockingList(
                databaseLockingStrategies,
                Array.isArray(strategies)
                    ? strategies.map((row) => `${row.strategy}: ${row.decision_for_input}`)
                    : [],
                'No strategy comparison generated yet.'
            );
        }

        databaseLockingPreset.addEventListener('change', (event) => {
            applyDatabaseLockingPreset(event.target.value);
        });

        databaseLockingPlanForm.addEventListener('submit', async (event) => {
            event.preventDefault();

            const formData = new FormData(databaseLockingPlanForm);
            const payload = {
                scenario_name: String(formData.get('scenario_name') || ''),
                invariant: String(formData.get('invariant') || 'inventory'),
                resource_type: String(formData.get('resource_type') || 'product'),
                concurrent_requests: Number(formData.get('concurrent_requests') || 2),
                touches_multiple_rows: formData.has('touches_multiple_rows'),
                has_external_side_effect: formData.has('has_external_side_effect'),
                expected_failure: String(formData.get('expected_failure') || 'reject'),
            };

            databaseLockingPlanStatus.textContent = 'Running POST /api/practice/database-locking-plan...';
            databaseLockingPlanOutput.textContent = JSON.stringify(payload, null, 2);
            renderDatabaseLockingSummary(null);

            try {
                const response = await fetch('{{ route('api.practice.database-locking-plan.store') }}', {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(payload),
                });
                const body = await response.json();

                databaseLockingPlanStatus.textContent = `HTTP ${response.status}`;
                databaseLockingPlanOutput.textContent = JSON.stringify(body, null, 2);
                renderDatabaseLockingSummary(body);
            } catch (error) {
                databaseLockingPlanStatus.textContent = 'Request failed';
                databaseLockingPlanOutput.textContent = error instanceof Error ? error.message : 'Unknown request error';
                renderDatabaseLockingSummary(null);
            }
        });
    </script>
@endsection
