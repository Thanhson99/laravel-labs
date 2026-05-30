@extends('learning.layout', ['title' => 'Kubernetes Analogy Workbench'])

@section('content')
    <section class="hero">
        <span class="badge">Runnable workbench</span>
        <h1>Explain Kubernetes in one minute with a ship fleet.</h1>
        <p>
            This workbench maps command ships, cargo ships, and container cargo to Kubernetes
            control plane, worker nodes, pods, containers, deployments, services, ingress, probes,
            and the reconciliation loop.
        </p>
    </section>

    <section class="section">
        <div class="grid">
            <article class="panel">
                <h2>Kubernetes Input</h2>
                <label>
                    Scenario preset
                    <select id="kubernetesAnalogyPreset">
                        <option value="web" selected>Public web API</option>
                        <option value="worker">Queue worker</option>
                        <option value="cron">Scheduled job</option>
                    </select>
                </label>

                <form id="kubernetesAnalogyPlanForm">
                    <label style="margin-top: 12px;">
                        Learning goal
                        <select name="learning_goal">
                            <option value="one-minute" selected>one-minute explanation</option>
                            <option value="interview">interview answer</option>
                            <option value="deployment-debug">deployment debug</option>
                        </select>
                    </label>

                    <label style="margin-top: 12px;">
                        App type
                        <select name="app_type">
                            <option value="web-api" selected>web-api</option>
                            <option value="worker">worker</option>
                            <option value="scheduled-job">scheduled-job</option>
                        </select>
                    </label>

                    <label style="margin-top: 12px;">
                        Replicas
                        <input name="replicas" type="number" min="1" max="20" value="3">
                    </label>

                    <label style="margin-top: 12px;">
                        <input type="checkbox" name="needs_external_access" value="1" checked>
                        Needs external access
                    </label>

                    <label style="margin-top: 12px;">
                        <input type="checkbox" name="has_stateful_data" value="1">
                        Has stateful data
                    </label>

                    <button class="button primary" type="submit" style="margin-top: 14px;">Explain Kubernetes</button>
                </form>
            </article>

            <article class="panel">
                <h2>Kubernetes Plan</h2>
                <p class="muted" id="kubernetesAnalogyPlanStatus">Submit input to map the ship analogy to Kubernetes resources and commands.</p>
                <pre class="raw-json"><code id="kubernetesAnalogyPlanOutput">POST /api/practice/kubernetes-analogy-plan</code></pre>
            </article>

            <article class="panel">
                <h2>Files To Read</h2>
                <ul>
                    <li><code>data/interview/devops.en.json</code></li>
                    <li><code>data/interview/devops.vi.json</code></li>
                    <li><code>routes/api/practice-actions.php</code></li>
                    <li><code>app/Http/Requests/Api/PlanKubernetesAnalogyRequest.php</code></li>
                    <li><code>app/Services/Practice/KubernetesAnalogyPlanService.php</code></li>
                    <li><code>tests/Feature/KubernetesAnalogyPlanWorkbenchTest.php</code></li>
                </ul>
            </article>
        </div>
    </section>

    <script>
        const kubernetesAnalogyPlanForm = document.querySelector('#kubernetesAnalogyPlanForm');
        const kubernetesAnalogyPreset = document.querySelector('#kubernetesAnalogyPreset');
        const kubernetesAnalogyPlanStatus = document.querySelector('#kubernetesAnalogyPlanStatus');
        const kubernetesAnalogyPlanOutput = document.querySelector('#kubernetesAnalogyPlanOutput');
        const kubernetesAnalogyPresets = {
            web: {
                learning_goal: 'one-minute',
                app_type: 'web-api',
                replicas: 3,
                needs_external_access: true,
                has_stateful_data: false,
            },
            worker: {
                learning_goal: 'interview',
                app_type: 'worker',
                replicas: 4,
                needs_external_access: false,
                has_stateful_data: false,
            },
            cron: {
                learning_goal: 'deployment-debug',
                app_type: 'scheduled-job',
                replicas: 1,
                needs_external_access: false,
                has_stateful_data: true,
            },
        };

        function applyKubernetesAnalogyPreset(name) {
            const preset = kubernetesAnalogyPresets[name] || kubernetesAnalogyPresets.web;

            Object.entries(preset).forEach(([field, value]) => {
                const input = kubernetesAnalogyPlanForm.elements[field];

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

        kubernetesAnalogyPreset.addEventListener('change', (event) => {
            applyKubernetesAnalogyPreset(event.target.value);
        });

        kubernetesAnalogyPlanForm.addEventListener('submit', async (event) => {
            event.preventDefault();

            const formData = new FormData(kubernetesAnalogyPlanForm);
            const payload = {
                learning_goal: String(formData.get('learning_goal') || 'one-minute'),
                app_type: String(formData.get('app_type') || 'web-api'),
                replicas: Number(formData.get('replicas') || 1),
                needs_external_access: formData.has('needs_external_access'),
                has_stateful_data: formData.has('has_stateful_data'),
            };

            kubernetesAnalogyPlanStatus.textContent = 'Running POST /api/practice/kubernetes-analogy-plan...';
            kubernetesAnalogyPlanOutput.textContent = JSON.stringify(payload, null, 2);

            try {
                const response = await fetch('{{ route('api.practice.kubernetes-analogy-plan.store') }}', {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(payload),
                });
                const body = await response.json();

                kubernetesAnalogyPlanStatus.textContent = `HTTP ${response.status}`;
                kubernetesAnalogyPlanOutput.textContent = JSON.stringify(body, null, 2);
            } catch (error) {
                kubernetesAnalogyPlanStatus.textContent = 'Request failed';
                kubernetesAnalogyPlanOutput.textContent = error instanceof Error ? error.message : 'Unknown request error';
            }
        });
    </script>
@endsection
