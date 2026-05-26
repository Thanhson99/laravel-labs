@extends('learning.layout', ['title' => 'Event Listener Plan Workbench'])

@section('content')
    <section class="hero">
        <span class="badge">Runnable workbench</span>
        <h1>Separate core workflow from side effects.</h1>
        <p>
            This workbench plans a Laravel event and listener pair so you can study when to dispatch events,
            when to queue listeners, and how to test side effects without coupling them to controllers.
        </p>
    </section>

    <section class="section">
        <div class="grid">
            <article class="panel">
                <h2>Event Input</h2>
                <form id="eventListenerPlanForm">
                    <label>
                        Event name
                        <input name="event_name" value="Practice Task Completed" autocomplete="off">
                    </label>

                    <label style="margin-top: 12px;">
                        Listener name
                        <input name="listener_name" value="Send Practice Completion Notification" autocomplete="off">
                    </label>

                    <label style="margin-top: 12px;">
                        Side effect
                        <input name="side_effect" value="Send learner notification" autocomplete="off">
                    </label>

                    <label style="margin-top: 12px;">
                        Listener runtime
                        <select name="queued">
                            <option value="true" selected>Queued listener</option>
                            <option value="false">Synchronous listener</option>
                        </select>
                    </label>

                    <button class="button primary" type="submit" style="margin-top: 14px;">Plan event flow</button>
                </form>
            </article>

            <article class="panel">
                <h2>Event Plan</h2>
                <p class="muted" id="eventListenerPlanStatus">Submit input to build the event/listener plan.</p>
                <pre class="raw-json"><code id="eventListenerPlanOutput">POST /api/practice/event-listener-plan</code></pre>
            </article>

            <article class="panel">
                <h2>Files To Read</h2>
                <ul>
                    <li><code>routes/api/practice-actions.php</code></li>
                    <li><code>app/Http/Requests/Api/PlanEventListenerRequest.php</code></li>
                    <li><code>app/Http/Controllers/Api/EventListenerPlanController.php</code></li>
                    <li><code>app/Services/Practice/EventListenerPlanService.php</code></li>
                    <li><code>tests/Feature/EventListenerPlanWorkbenchTest.php</code></li>
                </ul>
            </article>
        </div>
    </section>

    <section class="section">
        <h2>Practice Variations</h2>
        <div class="list">
            <article class="item">
                <div class="meta">
                    <span class="badge">Event</span>
                </div>
                <p>Rename the event so it describes something that already happened, not a command you want to run.</p>
            </article>
            <article class="item">
                <div class="meta">
                    <span class="badge">Listener</span>
                </div>
                <p>Switch between queued and synchronous listener mode and inspect how the command list changes.</p>
            </article>
            <article class="item">
                <div class="meta">
                    <span class="badge">Test</span>
                </div>
                <p>Read the test strategy and decide where `Event::fake()`, `Queue::fake()`, or a focused listener test belongs.</p>
            </article>
        </div>
    </section>

    <script>
        const eventListenerPlanForm = document.querySelector('#eventListenerPlanForm');
        const eventListenerPlanStatus = document.querySelector('#eventListenerPlanStatus');
        const eventListenerPlanOutput = document.querySelector('#eventListenerPlanOutput');

        eventListenerPlanForm.addEventListener('submit', async (event) => {
            event.preventDefault();

            const formData = new FormData(eventListenerPlanForm);
            const payload = {
                event_name: String(formData.get('event_name') || ''),
                listener_name: String(formData.get('listener_name') || ''),
                side_effect: String(formData.get('side_effect') || ''),
                queued: formData.get('queued') === 'true',
            };

            eventListenerPlanStatus.textContent = 'Running POST /api/practice/event-listener-plan...';
            eventListenerPlanOutput.textContent = JSON.stringify(payload, null, 2);

            try {
                const response = await fetch('{{ route('api.practice.event-listener-plan.store') }}', {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(payload),
                });
                const body = await response.json();

                eventListenerPlanStatus.textContent = `HTTP ${response.status}`;
                eventListenerPlanOutput.textContent = JSON.stringify(body, null, 2);
            } catch (error) {
                eventListenerPlanStatus.textContent = 'Request failed';
                eventListenerPlanOutput.textContent = error instanceof Error ? error.message : 'Unknown request error';
            }
        });
    </script>
@endsection
