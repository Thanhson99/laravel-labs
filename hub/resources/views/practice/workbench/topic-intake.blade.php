@extends('learning.layout', ['title' => 'Practice Topic Intake Workbench'])

@section('content')
    <section class="hero">
        <span class="badge">Runnable workbench</span>
        <h1>API validation, Form Request, and service response in one loop.</h1>
        <p>
            This workbench calls the real `POST /api/practice/topics` endpoint.
            Change the payload, submit it, and inspect how Laravel validation and the topic service shape the response.
        </p>
    </section>

    <section class="section">
        <div class="grid">
            <article class="panel">
                <h2>Submit Topic</h2>
                <form id="topicIntakeForm">
                    <label>
                        Title
                        <input name="title" value="Build a validated API slice" autocomplete="off">
                    </label>

                    <label style="margin-top: 12px;">
                        Track
                        <select name="track">
                            @foreach ($tracks as $value => $label)
                                <option value="{{ $value }}" @selected($value === 'api-validation')>{{ $label }}</option>
                            @endforeach
                        </select>
                    </label>

                    <label style="margin-top: 12px;">
                        Difficulty
                        <select name="difficulty">
                            <option value="beginner">Beginner</option>
                            <option value="intermediate">Intermediate</option>
                        </select>
                    </label>

                    <button class="button primary" type="submit" style="margin-top: 14px;">Submit to API</button>
                </form>
            </article>

            <article class="panel">
                <h2>API Response</h2>
                <p class="muted" id="topicIntakeStatus">Submit the form to run the endpoint.</p>
                <pre class="raw-json"><code id="topicIntakeOutput">POST /api/practice/topics</code></pre>
            </article>

            <article class="panel">
                <h2>Files To Study</h2>
                <ul>
                    <li><code>routes/api/practice-actions.php</code></li>
                    <li><code>app/Http/Requests/Api/StorePracticeTopicRequest.php</code></li>
                    <li><code>app/Http/Controllers/Api/PracticeTopicController.php</code></li>
                    <li><code>app/Services/Practice/PracticeTopicIntakeService.php</code></li>
                    <li><code>tests/Feature/PracticeTopicApiTest.php</code></li>
                </ul>
            </article>
        </div>
    </section>

    <section class="section">
        <h2>Practice Variations</h2>
        <div class="list">
            <article class="item">
                <div class="meta">
                    <span class="badge">Validation</span>
                </div>
                <p>Submit a two-character title to trigger a 422 response and inspect the validation error shape.</p>
            </article>
            <article class="item">
                <div class="meta">
                    <span class="badge">Service</span>
                </div>
                <p>Switch tracks and compare how the service changes the next action and command list.</p>
            </article>
            <article class="item">
                <div class="meta">
                    <span class="badge">Test</span>
                </div>
                <p>Run <code>php artisan test --filter PracticeTopic</code> after changing request rules or response shape.</p>
            </article>
        </div>
    </section>

    <script>
        const form = document.querySelector('#topicIntakeForm');
        const statusNode = document.querySelector('#topicIntakeStatus');
        const outputNode = document.querySelector('#topicIntakeOutput');

        form.addEventListener('submit', async (event) => {
            event.preventDefault();

            const formData = new FormData(form);
            const payload = Object.fromEntries(formData.entries());

            statusNode.textContent = 'Running POST /api/practice/topics...';
            outputNode.textContent = JSON.stringify(payload, null, 2);

            try {
                const response = await fetch('{{ route('api.practice.topics.store') }}', {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(payload),
                });

                const body = await response.json();

                statusNode.textContent = `HTTP ${response.status}`;
                outputNode.textContent = JSON.stringify(body, null, 2);
            } catch (error) {
                statusNode.textContent = 'Request failed';
                outputNode.textContent = error instanceof Error ? error.message : 'Unknown request error';
            }
        });
    </script>
@endsection
