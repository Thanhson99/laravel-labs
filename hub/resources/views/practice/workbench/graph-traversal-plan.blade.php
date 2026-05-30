@extends('learning.layout', ['title' => 'Graph Traversal Plan Workbench'])

@section('content')
    <section class="hero">
        <span class="badge">Runnable workbench</span>
        <h1>Choose BFS or DFS from the traversal goal.</h1>
        <p>
            This workbench turns graph traversal theory into a bounded implementation plan for API crawling,
            dependency graphs, tree menus, and database hierarchies.
        </p>
    </section>

    <section class="section">
        <div class="grid">
            <article class="panel">
                <h2>Traversal Input</h2>
                <label>
                    Scenario preset
                    <select id="graphTraversalPreset">
                        <option value="api" selected>Nearest API resource</option>
                        <option value="tree">Database category tree</option>
                        <option value="weighted">Weighted path warning</option>
                    </select>
                </label>

                <form id="graphTraversalPlanForm">
                    <label style="margin-top: 12px;">
                        Scenario name
                        <input name="scenario_name" value="API Resource Crawl" autocomplete="off">
                    </label>

                    <label style="margin-top: 12px;">
                        Traversal goal
                        <select name="goal">
                            <option value="nearest-match" selected>nearest match</option>
                            <option value="shortest-path">shortest path</option>
                            <option value="branch-exploration">branch exploration</option>
                            <option value="dependency-reasoning">dependency reasoning</option>
                            <option value="subtree-validation">subtree validation</option>
                        </select>
                    </label>

                    <label style="margin-top: 12px;">
                        Graph shape
                        <select name="graph_shape">
                            <option value="wide" selected>wide</option>
                            <option value="deep">deep</option>
                            <option value="cyclic">cyclic</option>
                            <option value="tree">tree</option>
                            <option value="api-links">api links</option>
                        </select>
                    </label>

                    <label style="margin-top: 12px;">
                        Node limit
                        <input name="node_count" type="number" min="2" max="100000" value="500">
                    </label>

                    <label style="margin-top: 12px;">
                        Max depth
                        <input name="max_depth" type="number" min="1" max="50" value="4">
                    </label>

                    <label style="margin-top: 12px;">
                        <input type="checkbox" name="weighted_edges" value="1">
                        Weighted edges
                    </label>

                    <label style="margin-top: 12px;">
                        Production context
                        <select name="production_context">
                            <option value="api-crawling" selected>API crawling</option>
                            <option value="database-hierarchy">database hierarchy</option>
                            <option value="dependency-graph">dependency graph</option>
                            <option value="menu-rendering">menu rendering</option>
                        </select>
                    </label>

                    <button class="button primary" type="submit" style="margin-top: 14px;">Plan traversal</button>
                </form>
            </article>

            <article class="panel">
                <h2>Traversal Plan</h2>
                <p class="muted" id="graphTraversalPlanStatus">Submit input to generate a BFS/DFS decision, traversal state, guardrails, examples, tests, and an interview answer.</p>
                <pre class="raw-json"><code id="graphTraversalPlanOutput">POST /api/practice/graph-traversal-plan</code></pre>
            </article>

            <article class="panel">
                <h2>Files To Read</h2>
                <ul>
                    <li><code>data/laravel/performance-search.en.json</code></li>
                    <li><code>routes/api/practice-actions.php</code></li>
                    <li><code>app/Http/Requests/Api/PlanGraphTraversalRequest.php</code></li>
                    <li><code>app/Services/Practice/GraphTraversalPlanService.php</code></li>
                    <li><code>tests/Feature/GraphTraversalPlanWorkbenchTest.php</code></li>
                    <li><code>tests/Unit/Practice/GraphTraversalPlanServiceTest.php</code></li>
                </ul>
            </article>
        </div>
    </section>

    <script>
        const graphTraversalPlanForm = document.querySelector('#graphTraversalPlanForm');
        const graphTraversalPreset = document.querySelector('#graphTraversalPreset');
        const graphTraversalPlanStatus = document.querySelector('#graphTraversalPlanStatus');
        const graphTraversalPlanOutput = document.querySelector('#graphTraversalPlanOutput');
        const graphTraversalPresets = {
            api: {
                scenario_name: 'API Resource Crawl',
                goal: 'nearest-match',
                graph_shape: 'api-links',
                node_count: 500,
                max_depth: 4,
                weighted_edges: false,
                production_context: 'api-crawling',
            },
            tree: {
                scenario_name: 'Category Tree Validation',
                goal: 'subtree-validation',
                graph_shape: 'tree',
                node_count: 2000,
                max_depth: 8,
                weighted_edges: false,
                production_context: 'database-hierarchy',
            },
            weighted: {
                scenario_name: 'Weighted Service Path',
                goal: 'shortest-path',
                graph_shape: 'cyclic',
                node_count: 250,
                max_depth: 6,
                weighted_edges: true,
                production_context: 'dependency-graph',
            },
        };

        function applyGraphTraversalPreset(name) {
            const preset = graphTraversalPresets[name] || graphTraversalPresets.api;

            Object.entries(preset).forEach(([field, value]) => {
                const input = graphTraversalPlanForm.elements[field];

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

        graphTraversalPreset.addEventListener('change', (event) => {
            applyGraphTraversalPreset(event.target.value);
        });

        graphTraversalPlanForm.addEventListener('submit', async (event) => {
            event.preventDefault();

            const formData = new FormData(graphTraversalPlanForm);
            const payload = {
                scenario_name: String(formData.get('scenario_name') || ''),
                goal: String(formData.get('goal') || 'nearest-match'),
                graph_shape: String(formData.get('graph_shape') || 'wide'),
                node_count: Number(formData.get('node_count') || 2),
                max_depth: Number(formData.get('max_depth') || 1),
                weighted_edges: formData.has('weighted_edges'),
                production_context: String(formData.get('production_context') || 'api-crawling'),
            };

            graphTraversalPlanStatus.textContent = 'Running POST /api/practice/graph-traversal-plan...';
            graphTraversalPlanOutput.textContent = JSON.stringify(payload, null, 2);

            try {
                const response = await fetch('{{ route('api.practice.graph-traversal-plan.store') }}', {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(payload),
                });
                const body = await response.json();

                graphTraversalPlanStatus.textContent = `HTTP ${response.status}`;
                graphTraversalPlanOutput.textContent = JSON.stringify(body, null, 2);
            } catch (error) {
                graphTraversalPlanStatus.textContent = 'Request failed';
                graphTraversalPlanOutput.textContent = error instanceof Error ? error.message : 'Unknown request error';
            }
        });
    </script>
@endsection
