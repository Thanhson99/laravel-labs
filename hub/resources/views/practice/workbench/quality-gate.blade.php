@extends('learning.layout', ['title' => 'Quality Gate Workbench'])

@section('content')
    <section class="hero">
        <span class="badge">Runnable workbench</span>
        <h1>Turn verification results into a clear quality decision.</h1>
        <p>
            This workbench calls the real quality-gate API.
            Use it after changing code to decide whether the practice task is ready or still needs test/style work.
        </p>
    </section>

    <section class="section">
        <div class="grid">
            <article class="panel">
                <h2>Verification Input</h2>
                <form id="qualityGateForm">
                    <label>
                        Tests
                        <input name="tests" type="number" min="0" max="10000" value="183">
                    </label>

                    <label style="margin-top: 12px;">
                        Assertions
                        <input name="assertions" type="number" min="0" max="100000" value="13782">
                    </label>

                    <label style="margin-top: 12px;">
                        Failures
                        <input name="failures" type="number" min="0" max="10000" value="0">
                    </label>

                    <label style="margin-top: 12px;">
                        Pint Result
                        <select name="pint">
                            <option value="true" selected>Passed</option>
                            <option value="false">Failed</option>
                        </select>
                    </label>

                    <button class="button primary" type="submit" style="margin-top: 14px;">Evaluate quality gate</button>
                </form>
            </article>

            <article class="panel">
                <h2>Gate Result</h2>
                <p class="muted" id="qualityGateStatus">Submit verification results to evaluate readiness.</p>
                <pre class="raw-json"><code id="qualityGateOutput">POST /api/practice/quality-gate</code></pre>
            </article>

            <article class="panel">
                <h2>Files To Read</h2>
                <ul>
                    <li><code>routes/api/practice-actions.php</code></li>
                    <li><code>app/Http/Requests/Api/EvaluateQualityGateRequest.php</code></li>
                    <li><code>app/Http/Controllers/Api/PracticeQualityGateController.php</code></li>
                    <li><code>app/Services/Practice/PracticeQualityGateService.php</code></li>
                    <li><code>tests/Feature/PracticeQualityGateApiTest.php</code></li>
                    <li><code>tests/Unit/Practice/PracticeQualityGateServiceTest.php</code></li>
                </ul>
            </article>
        </div>
    </section>

    <section class="section">
        <h2>Practice Variations</h2>
        <div class="list">
            <article class="item">
                <div class="meta">
                    <span class="badge">Failure mode</span>
                </div>
                <p>Set failures to <code>1</code> and confirm the API reports <code>needs-work</code>.</p>
            </article>
            <article class="item">
                <div class="meta">
                    <span class="badge">No tests</span>
                </div>
                <p>Set tests to <code>0</code> and read why the service refuses to mark the task ready.</p>
            </article>
            <article class="item">
                <div class="meta">
                    <span class="badge">Service rule</span>
                </div>
                <p>Add a new check in <code>PracticeQualityGateService</code>, then update its unit test first.</p>
            </article>
        </div>
    </section>

    <script>
        const qualityGateForm = document.querySelector('#qualityGateForm');
        const qualityGateStatus = document.querySelector('#qualityGateStatus');
        const qualityGateOutput = document.querySelector('#qualityGateOutput');

        qualityGateForm.addEventListener('submit', async (event) => {
            event.preventDefault();

            const formData = new FormData(qualityGateForm);
            const payload = {
                tests: Number(formData.get('tests')),
                assertions: Number(formData.get('assertions')),
                failures: Number(formData.get('failures')),
                pint: formData.get('pint') === 'true',
            };

            qualityGateStatus.textContent = 'Running POST /api/practice/quality-gate...';
            qualityGateOutput.textContent = JSON.stringify(payload, null, 2);

            try {
                const response = await fetch('{{ route('api.practice.quality-gate.store') }}', {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(payload),
                });
                const body = await response.json();

                qualityGateStatus.textContent = `HTTP ${response.status}`;
                qualityGateOutput.textContent = JSON.stringify(body, null, 2);
            } catch (error) {
                qualityGateStatus.textContent = 'Request failed';
                qualityGateOutput.textContent = error instanceof Error ? error.message : 'Unknown request error';
            }
        });
    </script>
@endsection
