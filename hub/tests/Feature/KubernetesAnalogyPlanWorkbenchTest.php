<?php

declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;

final class KubernetesAnalogyPlanWorkbenchTest extends TestCase
{
    /**
     * The Kubernetes analogy workbench renders the beginner DevOps form.
     */
    public function test_kubernetes_analogy_plan_workbench_renders(): void
    {
        $response = $this->get('/workbench/kubernetes-analogy-plan');

        $response
            ->assertOk()
            ->assertSee('Kubernetes Analogy Workbench')
            ->assertSee('POST /api/practice/kubernetes-analogy-plan')
            ->assertSee('KubernetesAnalogyPlanService')
            ->assertSee('Scenario preset')
            ->assertSee('Explain Kubernetes');
    }

    /**
     * The Kubernetes analogy API maps the ship story to core cluster resources.
     */
    public function test_kubernetes_analogy_plan_api_returns_ship_mapping(): void
    {
        $response = $this->postJson('/api/practice/kubernetes-analogy-plan', [
            'learning_goal' => 'one-minute',
            'app_type' => 'web-api',
            'replicas' => 3,
            'needs_external_access' => true,
            'has_stateful_data' => false,
        ]);

        $response
            ->assertOk()
            ->assertJsonPath('data.summary.plain_answer', 'Kubernetes is a system that runs containers across many machines and keeps the desired application state alive.')
            ->assertJsonPath('data.analogy_map.0.ship_word', 'command ship')
            ->assertJsonPath('data.analogy_map.0.kubernetes_word', 'control plane')
            ->assertJsonPath('data.analogy_map.1.kubernetes_word', 'worker node')
            ->assertJsonPath('data.analogy_map.3.kubernetes_word', 'pod')
            ->assertJsonPath('data.analogy_map.5.kubernetes_word', 'service')
            ->assertJsonPath('data.control_loop.0.step', '1. Declare desired state')
            ->assertJsonPath('data.workload_plan.resource', 'Deployment')
            ->assertJsonPath('data.workload_plan.replica_count', 3)
            ->assertJsonPath('data.probe_plan.0.name', 'readinessProbe')
            ->assertJsonPath('data.probe_plan.1.name', 'livenessProbe')
            ->assertJsonPath('data.scaling_plan.replicas', 3)
            ->assertJsonPath('data.resource_plan.0.setting', 'resources.requests.cpu')
            ->assertJsonPath('data.rollout_plan.1.phase', 'watch rollout')
            ->assertJsonPath('data.rollout_plan.3.command', 'kubectl rollout undo deployment/<app-name>')
            ->assertJsonPath('data.config_secret_plan.0.name', 'ConfigMap')
            ->assertJsonPath('data.config_secret_plan.1.name', 'Secret')
            ->assertJsonPath('data.namespace_rbac_plan.0.area', 'namespace')
            ->assertJsonPath('data.namespace_rbac_plan.2.area', 'RBAC')
            ->assertJsonPath('data.network_policy_plan.0.rule', 'default deny ingress')
            ->assertJsonPath('data.observability_plan.0.signal', 'pod events')
            ->assertJsonPath('data.cost_capacity_plan.0.area', 'replicas')
            ->assertJsonPath('data.availability_plan.0.control', 'PodDisruptionBudget')
            ->assertJsonPath('data.shutdown_plan.0.control', 'SIGTERM handling')
            ->assertJsonPath('data.image_security_plan.0.control', 'immutable image tag')
            ->assertJsonPath('data.backend_runtime_plan.0.concern', 'database migrations')
            ->assertJsonPath('data.backend_runtime_plan.1.concern', 'queue workers')
            ->assertJsonPath('data.backend_runtime_plan.2.concern', 'sessions and cache')
            ->assertJsonPath('data.manifest_review_checklist.0.area', 'labels and selectors')
            ->assertJsonPath('data.manifest_review_checklist.3.area', 'rollout safety')
            ->assertJsonPath('data.cicd_gate_plan.1.gate', 'server-side dry run')
            ->assertJsonPath('data.cicd_gate_plan.3.gate', 'rollout smoke test')
            ->assertJsonPath('data.yaml_snippets.0.name', 'deployment probes and resources')
            ->assertJsonPath('data.one_minute_script.hook', 'Kubernetes is easiest to understand as a fleet manager for containers.')
            ->assertJsonPath('data.one_minute_script.production_note', 'Stateless apps scale more safely when sessions, cache, and durable state live outside the pod.')
            ->assertJsonPath('data.interview_rubric.0.criterion', 'definition')
            ->assertJsonPath('data.interview_rubric.4.weak_signal', 'Jumps straight to redeploying without inspecting evidence.')
            ->assertJsonPath('data.command_ladder.0.level', 'cluster map')
            ->assertJsonPath('data.command_ladder.4.command', 'kubectl get ingress,service,endpoints')
            ->assertJsonPath('data.resource_decision_guide.0.question', 'What runs the workload?')
            ->assertJsonPath('data.resource_decision_guide.1.choose', 'Choose Service plus Ingress or LoadBalancer so users reach stable endpoints instead of pod IPs.')
            ->assertJsonPath('data.manifest_smell_catalog.0.smell', 'selector labels do not match pod labels')
            ->assertJsonPath('data.manifest_smell_catalog.3.fix', 'Use immutable image tags such as a release version or commit SHA.')
            ->assertJsonPath('data.practice_drills.0.name', 'explain without notes')
            ->assertJsonPath('data.practice_drills.2.expected_signal', 'The first action is evidence gathering, not redeploying blindly.')
            ->assertJsonPath('data.production_readiness_score.score', 80)
            ->assertJsonPath('data.production_readiness_score.level', 'ready with review')
            ->assertJsonPath('data.slo_observability_plan.0.signal', 'availability')
            ->assertJsonPath('data.slo_observability_plan.3.signal', 'resource saturation')
            ->assertJsonPath('data.deployment_review_questions.2.question', 'Can the team recover from a bad release?')
            ->assertJsonPath('data.deployment_review_questions.3.question', 'Is the external route controlled and observable?')
            ->assertJsonPath('data.traffic_flow.0', 'User request reaches DNS, then the ingress or cloud load balancer.')
            ->assertJsonPath('data.manifest_outline.2.file', 'ingress.yaml')
            ->assertJsonPath('data.kubectl_commands.0', 'kubectl get nodes')
            ->assertJsonPath('data.kubectl_commands.5', 'kubectl get service,ingress')
            ->assertJsonPath('data.troubleshooting_runbook.0.symptom', 'Pod is Pending')
            ->assertJsonPath('data.troubleshooting_runbook.2.symptom', 'Service has no traffic')
            ->assertJsonPath('data.failure_diagnosis_matrix.0.status', 'ImagePullBackOff')
            ->assertJsonPath('data.failure_diagnosis_matrix.1.status', 'OOMKilled')
            ->assertJsonPath('data.failure_diagnosis_matrix.3.status', 'Forbidden')
            ->assertJsonPath('data.beginner_misconceptions.0.myth', 'Kubernetes is only Docker with a bigger name.')
            ->assertJsonFragment(['interview_answer' => 'Kubernetes is a container orchestration platform. Using the ship analogy: the control plane is the command ship, worker nodes are cargo ships, pods carry the app containers, deployments describe how many copies should run, and services give those pods a stable address. For a web-api, I would declare 3 replica(s), add readiness and liveness checks, expose it only if needed, and keep durable state outside disposable pods.'])
            ->assertJsonPath('data.commands.1', 'php artisan test --filter KubernetesAnalogyPlan');
    }

    /**
     * Stateful scheduled jobs warn learners about storage and disposable pods.
     */
    public function test_kubernetes_analogy_plan_api_warns_about_stateful_workloads(): void
    {
        $response = $this->postJson('/api/practice/kubernetes-analogy-plan', [
            'learning_goal' => 'deployment-debug',
            'app_type' => 'scheduled-job',
            'replicas' => 5,
            'needs_external_access' => false,
            'has_stateful_data' => true,
        ]);

        $response
            ->assertOk()
            ->assertJsonPath('data.workload_plan.resource', 'CronJob')
            ->assertJsonPath('data.workload_plan.replica_count', 1)
            ->assertJsonPath('data.workload_plan.state_note', 'Use PersistentVolume, StatefulSet, or managed storage for durable data.')
            ->assertJsonPath('data.scaling_plan.replicas', 1)
            ->assertJsonPath('data.scaling_plan.caution', 'Avoid starting duplicate scheduled work unless the job is explicitly idempotent and concurrency-safe.')
            ->assertJsonPath('data.rollout_plan.0.phase', 'validate schedule')
            ->assertJsonPath('data.rollout_plan.1.command', 'kubectl create job --from=cronjob/<job-name> <job-name>-manual-check')
            ->assertJsonPath('data.network_policy_plan.0.caution', 'Background workloads often need no external ingress at all.')
            ->assertJsonPath('data.cost_capacity_plan.3.area', 'stateful storage')
            ->assertJsonPath('data.availability_plan.0.control', 'concurrencyPolicy')
            ->assertJsonPath('data.shutdown_plan.2.control', 'terminationGracePeriodSeconds')
            ->assertJsonPath('data.manifest_outline.2.file', 'storage.yaml')
            ->assertJsonPath('data.traffic_flow.0', 'Internal caller or scheduler starts the workload.')
            ->assertJsonPath('data.troubleshooting_runbook.3.symptom', 'Data disappears after pod replacement')
            ->assertJsonPath('data.failure_diagnosis_matrix.5.status', 'Volume mount failed')
            ->assertJsonPath('data.backend_runtime_plan.4.concern', 'scheduled tasks')
            ->assertJsonPath('data.cicd_gate_plan.3.gate', 'manual job smoke test')
            ->assertJsonPath('data.yaml_snippets.2.name', 'cronjob safety')
            ->assertJsonPath('data.one_minute_script.production_note', 'Stateful data needs persistent storage or managed services because pod files are replaceable.')
            ->assertJsonPath('data.command_ladder.4.level', 'internal routing')
            ->assertJsonPath('data.command_ladder.5.level', 'storage')
            ->assertJsonPath('data.resource_decision_guide.0.choose', 'Choose CronJob because the work is triggered by a schedule.')
            ->assertJsonPath('data.manifest_smell_catalog.4.smell', 'stateful writes go to emptyDir or container filesystem')
            ->assertJsonPath('data.practice_drills.3.name', 'prove storage durability')
            ->assertJsonPath('data.production_readiness_score.score', 65)
            ->assertJsonPath('data.production_readiness_score.level', 'needs hardening')
            ->assertJsonPath('data.slo_observability_plan.4.signal', 'storage durability')
            ->assertJsonPath('data.deployment_review_questions.4.question', 'What proves data survives pod replacement?');
    }

    /**
     * Invalid Kubernetes analogy payloads return validation errors.
     */
    public function test_kubernetes_analogy_plan_api_validates_payload(): void
    {
        $response = $this->postJson('/api/practice/kubernetes-analogy-plan', [
            'learning_goal' => 'magic',
            'app_type' => 'database',
            'replicas' => 0,
            'needs_external_access' => 'maybe',
            'has_stateful_data' => 'sometimes',
        ]);

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors([
                'learning_goal',
                'app_type',
                'replicas',
                'needs_external_access',
                'has_stateful_data',
            ]);
    }
}
