@extends('learning.layout', ['title' => 'LSM Tree Plan Workbench'])

@section('content')
    <section class="hero">
        <span class="badge">Runnable workbench</span>
        <h1>Explain why NoSQL writes are fast with LSM Trees.</h1>
        <p>
            This workbench models the LSM Tree path behind many modern databases:
            memtables, immutable segments, compaction, Bloom Filters, and the tradeoffs
            behind fast sequential writes.
        </p>
    </section>

    <section class="section">
        <div class="grid">
            <article class="panel">
                <h2>Storage Engine Context</h2>
                <form id="lsmTreePlanForm">
                    <label>
                        Workload pattern
                        <select name="workload_pattern">
                            <option value="write-heavy">write-heavy</option>
                            <option value="mixed">mixed</option>
                            <option value="read-heavy">read-heavy</option>
                        </select>
                    </label>
                    <label style="margin-top: 12px;">
                        Write rate per second
                        <input name="write_rate_per_second" type="number" min="1" max="1000000" value="25000">
                    </label>
                    <label style="margin-top: 12px;">
                        Read miss ratio
                        <select name="read_miss_ratio">
                            <option value="high">high</option>
                            <option value="medium">medium</option>
                            <option value="low">low</option>
                        </select>
                    </label>
                    <label style="margin-top: 12px;">
                        Compaction strategy
                        <select name="compaction_strategy">
                            <option value="leveled">leveled</option>
                            <option value="size-tiered">size-tiered</option>
                            <option value="universal">universal</option>
                        </select>
                    </label>
                    <label style="margin-top: 12px;">
                        Bloom Filter enabled
                        <select name="bloom_filter_enabled">
                            <option value="yes">yes</option>
                            <option value="no">no</option>
                        </select>
                    </label>
                    <label style="margin-top: 12px;">
                        Schema expectation
                        <select name="schema_expectation">
                            <option value="schema-flexible">schema-flexible</option>
                            <option value="schema-strict">schema-strict</option>
                        </select>
                    </label>
                    <button class="button primary" type="submit" style="margin-top: 14px;">Plan LSM Tree tradeoffs</button>
                </form>
            </article>

            <article class="panel">
                <h2>LSM Tree Plan</h2>
                <p class="muted" id="lsmTreePlanStatus">Submit input to explain storage-engine tradeoffs.</p>
                <pre class="raw-json"><code id="lsmTreePlanOutput">POST /api/practice/lsm-tree-plan</code></pre>
            </article>

            <article class="panel">
                <h2>Files To Read</h2>
                <ul>
                    <li><code>routes/api/practice-actions.php</code></li>
                    <li><code>app/Http/Requests/Api/PlanLsmTreeRequest.php</code></li>
                    <li><code>app/Http/Controllers/Api/LsmTreePlanController.php</code></li>
                    <li><code>app/Services/Practice/LsmTreePlanService.php</code></li>
                    <li><code>tests/Feature/LsmTreePlanWorkbenchTest.php</code></li>
                </ul>
            </article>
        </div>
    </section>

    <script>
        const lsmTreePlanForm = document.querySelector('#lsmTreePlanForm');
        const lsmTreePlanStatus = document.querySelector('#lsmTreePlanStatus');
        const lsmTreePlanOutput = document.querySelector('#lsmTreePlanOutput');

        lsmTreePlanForm.addEventListener('submit', async (event) => {
            event.preventDefault();
            const formData = new FormData(lsmTreePlanForm);
            const payload = {
                workload_pattern: String(formData.get('workload_pattern') || ''),
                write_rate_per_second: Number(formData.get('write_rate_per_second') || 1),
                read_miss_ratio: String(formData.get('read_miss_ratio') || ''),
                compaction_strategy: String(formData.get('compaction_strategy') || ''),
                bloom_filter_enabled: String(formData.get('bloom_filter_enabled') || ''),
                schema_expectation: String(formData.get('schema_expectation') || ''),
            };

            lsmTreePlanStatus.textContent = 'Running POST /api/practice/lsm-tree-plan...';
            lsmTreePlanOutput.textContent = JSON.stringify(payload, null, 2);

            try {
                const response = await fetch('{{ route('api.practice.lsm-tree-plan.store') }}', {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(payload),
                });
                const body = await response.json();

                lsmTreePlanStatus.textContent = `HTTP ${response.status}`;
                lsmTreePlanOutput.textContent = JSON.stringify(body, null, 2);
            } catch (error) {
                lsmTreePlanStatus.textContent = 'Request failed';
                lsmTreePlanOutput.textContent = error instanceof Error ? error.message : 'Unknown request error';
            }
        });
    </script>
@endsection
