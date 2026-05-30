@extends('learning.layout', ['title' => 'OOP Abstraction Decision Workbench'])

@section('content')
    <section class="hero">
        <span class="badge">Runnable workbench</span>
        <h1>Choose between abstract class, interface, or concrete class.</h1>
        <p>
            This workbench turns PHP OOP theory into a concrete design decision with tradeoffs,
            implementation steps, a comparison table, a decision matrix, code examples, and a structured interview answer.
        </p>
    </section>

    <section class="section">
        <div class="grid">
            <article class="panel">
                <h2>Decision Input</h2>
                <label>
                    Scenario preset
                    <select id="oopAbstractionPreset">
                        <option value="payment" selected>Payment gateway</option>
                        <option value="report">File report family</option>
                        <option value="formatter">Invoice formatter</option>
                    </select>
                </label>

                <form id="oopAbstractionDecisionForm">
                    <label style="margin-top: 12px;">
                        Scenario
                        <input name="scenario" value="Payment Gateway" autocomplete="off">
                    </label>

                    <label style="margin-top: 12px;">
                        Class relationship
                        <select name="relationship">
                            <option value="unrelated" selected>unrelated classes, same behavior</option>
                            <option value="same-family">same family of objects</option>
                            <option value="single-class">single stable class</option>
                        </select>
                    </label>

                    <label style="margin-top: 12px;">
                        Shared behavior or state
                        <input name="shared_behavior" value="send a payment request" autocomplete="off">
                    </label>

                    <label style="margin-top: 12px;">
                        <input type="checkbox" name="needs_multiple_implementations" value="1" checked>
                        Needs multiple implementations
                    </label>

                    <label style="margin-top: 12px;">
                        <input type="checkbox" name="has_shared_state" value="1">
                        Has shared state or base helpers
                    </label>

                    <button class="button primary" type="submit" style="margin-top: 14px;">Choose abstraction</button>
                </form>
            </article>

            <article class="panel">
                <h2>Decision Plan</h2>
                <p class="muted" id="oopAbstractionDecisionStatus">Submit input to compare choices and generate files, steps, review checks, and an interview-answer rubric.</p>
                <pre class="raw-json"><code id="oopAbstractionDecisionOutput">POST /api/practice/oop-abstraction-decision</code></pre>
            </article>

            <article class="panel">
                <h2>Files To Read</h2>
                <ul>
                    <li><code>data/php/advanced.en.json</code></li>
                    <li><code>data/php/advanced.vi.json</code></li>
                    <li><code>app/Http/Requests/Api/PlanOopAbstractionDecisionRequest.php</code></li>
                    <li><code>app/Http/Controllers/Api/OopAbstractionDecisionController.php</code></li>
                    <li><code>app/Services/Practice/OopAbstractionDecisionService.php</code></li>
                    <li><code>tests/Feature/OopAbstractionDecisionWorkbenchTest.php</code></li>
                </ul>
            </article>
        </div>
    </section>

    <script>
        const oopAbstractionDecisionForm = document.querySelector('#oopAbstractionDecisionForm');
        const oopAbstractionPreset = document.querySelector('#oopAbstractionPreset');
        const oopAbstractionDecisionStatus = document.querySelector('#oopAbstractionDecisionStatus');
        const oopAbstractionDecisionOutput = document.querySelector('#oopAbstractionDecisionOutput');
        const oopAbstractionPresets = {
            payment: {
                scenario: 'Payment Gateway',
                relationship: 'unrelated',
                shared_behavior: 'send a payment request',
                needs_multiple_implementations: true,
                has_shared_state: false,
            },
            report: {
                scenario: 'File Report',
                relationship: 'same-family',
                shared_behavior: 'read file contents and normalize rows',
                needs_multiple_implementations: false,
                has_shared_state: true,
            },
            formatter: {
                scenario: 'Invoice Formatter',
                relationship: 'single-class',
                shared_behavior: '',
                needs_multiple_implementations: false,
                has_shared_state: false,
            },
        };

        function applyOopAbstractionPreset(name) {
            const preset = oopAbstractionPresets[name] || oopAbstractionPresets.payment;

            Object.entries(preset).forEach(([field, value]) => {
                const input = oopAbstractionDecisionForm.elements[field];

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

        oopAbstractionPreset.addEventListener('change', (event) => {
            applyOopAbstractionPreset(event.target.value);
        });

        oopAbstractionDecisionForm.addEventListener('submit', async (event) => {
            event.preventDefault();

            const formData = new FormData(oopAbstractionDecisionForm);
            const payload = {
                scenario: formData.get('scenario'),
                relationship: formData.get('relationship'),
                shared_behavior: formData.get('shared_behavior'),
                needs_multiple_implementations: formData.has('needs_multiple_implementations'),
                has_shared_state: formData.has('has_shared_state'),
            };

            oopAbstractionDecisionStatus.textContent = 'Running POST /api/practice/oop-abstraction-decision...';
            oopAbstractionDecisionOutput.textContent = JSON.stringify(payload, null, 2);

            try {
                const response = await fetch('{{ route('api.practice.oop-abstraction-decision.store') }}', {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(payload),
                });
                const body = await response.json();

                oopAbstractionDecisionStatus.textContent = `HTTP ${response.status}`;
                oopAbstractionDecisionOutput.textContent = JSON.stringify(body, null, 2);
            } catch (error) {
                oopAbstractionDecisionStatus.textContent = 'Request failed';
                oopAbstractionDecisionOutput.textContent = error instanceof Error ? error.message : 'Unknown request error';
            }
        });
    </script>
@endsection
