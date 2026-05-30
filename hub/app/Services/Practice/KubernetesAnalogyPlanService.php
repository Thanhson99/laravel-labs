<?php

declare(strict_types=1);

namespace App\Services\Practice;

final class KubernetesAnalogyPlanService
{
    /**
     * Build a beginner-friendly Kubernetes explanation using the ship analogy.
     *
     * @param  array{learning_goal: string, app_type: string, replicas: int, needs_external_access: bool, has_stateful_data: bool}  $input
     * @return array{summary: array{plain_answer: string, ship_story: string, warning: string}, analogy_map: array<int, array{ship_word: string, kubernetes_word: string, meaning: string}>, control_loop: array<int, array{step: string, explanation: string}>, workload_plan: array{resource: string, replica_count: int, exposure: string, state_note: string}, probe_plan: array<int, array{name: string, purpose: string, beginner_rule: string}>, scaling_plan: array{replicas: int, scaling_signal: string, caution: string}, resource_plan: array<int, array{setting: string, purpose: string, beginner_rule: string}>, rollout_plan: array<int, array{phase: string, goal: string, command: string}>, config_secret_plan: array<int, array{name: string, use_for: string, caution: string}>, namespace_rbac_plan: array<int, array{area: string, beginner_rule: string, reason: string}>, network_policy_plan: array<int, array{rule: string, purpose: string, caution: string}>, observability_plan: array<int, array{signal: string, command_or_source: string, why: string}>, cost_capacity_plan: array<int, array{area: string, risk: string, control: string}>, availability_plan: array<int, array{control: string, purpose: string, caution: string}>, shutdown_plan: array<int, array{control: string, purpose: string, beginner_rule: string}>, image_security_plan: array<int, array{control: string, purpose: string, beginner_rule: string}>, backend_runtime_plan: array<int, array{concern: string, kubernetes_shape: string, caution: string}>, manifest_review_checklist: array<int, array{area: string, check: string, failure_if_missing: string}>, cicd_gate_plan: array<int, array{gate: string, purpose: string, example: string}>, yaml_snippets: array<int, array{name: string, snippet: string}>, one_minute_script: array{hook: string, analogy: string, mechanics: string, production_note: string, closing: string}, interview_rubric: array<int, array{criterion: string, strong_signal: string, weak_signal: string}>, command_ladder: array<int, array{level: string, command: string, use_when: string}>, resource_decision_guide: array<int, array{question: string, choose: string, avoid: string}>, manifest_smell_catalog: array<int, array{smell: string, risk: string, fix: string}>, practice_drills: array<int, array{name: string, task: string, expected_signal: string}>, production_readiness_score: array{score: int, level: string, reasons: array<int, string>, next_action: string}, slo_observability_plan: array<int, array{signal: string, target: string, alert: string}>, deployment_review_questions: array<int, array{question: string, evidence: string}>, traffic_flow: array<int, string>, manifest_outline: array<int, array{file: string, purpose: string}>, kubectl_commands: array<int, string>, troubleshooting_runbook: array<int, array{symptom: string, first_check: string, likely_fix: string}>, failure_diagnosis_matrix: array<int, array{status: string, likely_layer: string, inspect: string, next_action: string}>, beginner_misconceptions: array<int, array{myth: string, correction: string}>, interview_answer: string, commands: array<int, string>}
     */
    public function plan(array $input): array
    {
        return [
            'summary' => $this->summaryFor($input),
            'analogy_map' => $this->analogyMap(),
            'control_loop' => $this->controlLoopFor($input),
            'workload_plan' => $this->workloadPlanFor($input),
            'probe_plan' => $this->probePlanFor($input),
            'scaling_plan' => $this->scalingPlanFor($input),
            'resource_plan' => $this->resourcePlanFor($input),
            'rollout_plan' => $this->rolloutPlanFor($input),
            'config_secret_plan' => $this->configSecretPlan(),
            'namespace_rbac_plan' => $this->namespaceRbacPlanFor($input),
            'network_policy_plan' => $this->networkPolicyPlanFor($input),
            'observability_plan' => $this->observabilityPlanFor($input),
            'cost_capacity_plan' => $this->costCapacityPlanFor($input),
            'availability_plan' => $this->availabilityPlanFor($input),
            'shutdown_plan' => $this->shutdownPlanFor($input),
            'image_security_plan' => $this->imageSecurityPlan(),
            'backend_runtime_plan' => $this->backendRuntimePlanFor($input),
            'manifest_review_checklist' => $this->manifestReviewChecklistFor($input),
            'cicd_gate_plan' => $this->cicdGatePlanFor($input),
            'yaml_snippets' => $this->yamlSnippetsFor($input),
            'one_minute_script' => $this->oneMinuteScriptFor($input),
            'interview_rubric' => $this->interviewRubric(),
            'command_ladder' => $this->commandLadderFor($input),
            'resource_decision_guide' => $this->resourceDecisionGuideFor($input),
            'manifest_smell_catalog' => $this->manifestSmellCatalogFor($input),
            'practice_drills' => $this->practiceDrillsFor($input),
            'production_readiness_score' => $this->productionReadinessScoreFor($input),
            'slo_observability_plan' => $this->sloObservabilityPlanFor($input),
            'deployment_review_questions' => $this->deploymentReviewQuestionsFor($input),
            'traffic_flow' => $this->trafficFlowFor($input),
            'manifest_outline' => $this->manifestOutlineFor($input),
            'kubectl_commands' => $this->kubectlCommandsFor($input),
            'troubleshooting_runbook' => $this->troubleshootingRunbookFor($input),
            'failure_diagnosis_matrix' => $this->failureDiagnosisMatrixFor($input),
            'beginner_misconceptions' => $this->beginnerMisconceptions(),
            'interview_answer' => $this->interviewAnswerFor($input),
            'commands' => [
                'php artisan route:list --path=kubernetes-analogy-plan',
                'php artisan test --filter KubernetesAnalogyPlan',
                'vendor\\bin\\pint --test',
            ],
        ];
    }

    /**
     * Return the short explanation for the requested learning goal.
     *
     * @param  array{learning_goal: string, app_type: string, replicas: int, needs_external_access: bool, has_stateful_data: bool}  $input
     * @return array{plain_answer: string, ship_story: string, warning: string}
     */
    private function summaryFor(array $input): array
    {
        return [
            'plain_answer' => 'Kubernetes is a system that runs containers across many machines and keeps the desired application state alive.',
            'ship_story' => "Think of the control plane as the command ship, worker nodes as cargo ships, and containers as application cargo. You declare {$input['replicas']} running copies, and Kubernetes keeps steering the fleet toward that state.",
            'warning' => $input['has_stateful_data']
                ? 'Stateful data needs storage planning; do not treat a database container like disposable cargo.'
                : 'Stateless workloads are easier to move, replace, and scale across worker nodes.',
        ];
    }

    /**
     * Return the ship-to-Kubernetes vocabulary map.
     *
     * @return array<int, array{ship_word: string, kubernetes_word: string, meaning: string}>
     */
    private function analogyMap(): array
    {
        return [
            [
                'ship_word' => 'command ship',
                'kubernetes_word' => 'control plane',
                'meaning' => 'Decides what should run, schedules work, stores cluster state, and watches for drift.',
            ],
            [
                'ship_word' => 'cargo ship',
                'kubernetes_word' => 'worker node',
                'meaning' => 'A machine that actually runs application workloads.',
            ],
            [
                'ship_word' => 'container cargo',
                'kubernetes_word' => 'container',
                'meaning' => 'The packaged application process with its runtime dependencies.',
            ],
            [
                'ship_word' => 'container stack',
                'kubernetes_word' => 'pod',
                'meaning' => 'The smallest deployable unit; one or more containers sharing network and lifecycle.',
            ],
            [
                'ship_word' => 'shipping order',
                'kubernetes_word' => 'deployment',
                'meaning' => 'Declares the app version and how many pod replicas should exist.',
            ],
            [
                'ship_word' => 'harbor address',
                'kubernetes_word' => 'service',
                'meaning' => 'A stable network name that sends traffic to healthy pods.',
            ],
        ];
    }

    /**
     * Return the reconciliation loop in beginner language.
     *
     * @param  array{learning_goal: string, app_type: string, replicas: int, needs_external_access: bool, has_stateful_data: bool}  $input
     * @return array<int, array{step: string, explanation: string}>
     */
    private function controlLoopFor(array $input): array
    {
        return [
            [
                'step' => '1. Declare desired state',
                'explanation' => "You say the {$input['app_type']} should run with {$input['replicas']} pod replica(s).",
            ],
            [
                'step' => '2. Schedule pods',
                'explanation' => 'The control plane chooses worker nodes that have enough capacity and match scheduling rules.',
            ],
            [
                'step' => '3. Run containers',
                'explanation' => 'Each worker node pulls the image and starts the container inside a pod.',
            ],
            [
                'step' => '4. Reconcile drift',
                'explanation' => 'If a pod dies or a node disappears, Kubernetes creates replacement pods to return to the desired state.',
            ],
        ];
    }

    /**
     * Return a small workload plan.
     *
     * @param  array{learning_goal: string, app_type: string, replicas: int, needs_external_access: bool, has_stateful_data: bool}  $input
     * @return array{resource: string, replica_count: int, exposure: string, state_note: string}
     */
    private function workloadPlanFor(array $input): array
    {
        return [
            'resource' => $input['app_type'] === 'scheduled-job' ? 'CronJob' : 'Deployment',
            'replica_count' => $input['app_type'] === 'scheduled-job' ? 1 : $input['replicas'],
            'exposure' => $input['needs_external_access'] ? 'Expose through Service plus Ingress or LoadBalancer.' : 'Keep internal with ClusterIP or no Service if it only runs background work.',
            'state_note' => $input['has_stateful_data'] ? 'Use PersistentVolume, StatefulSet, or managed storage for durable data.' : 'Keep pods replaceable by moving durable state outside the pod filesystem.',
        ];
    }

    /**
     * Return practical probe guidance for the workload.
     *
     * @param  array{learning_goal: string, app_type: string, replicas: int, needs_external_access: bool, has_stateful_data: bool}  $input
     * @return array<int, array{name: string, purpose: string, beginner_rule: string}>
     */
    private function probePlanFor(array $input): array
    {
        return [
            [
                'name' => 'readinessProbe',
                'purpose' => $input['needs_external_access']
                    ? 'Keeps a pod out of Service or Ingress traffic until the app can safely receive requests.'
                    : 'Shows whether the workload is ready for internal callers or worker coordination.',
                'beginner_rule' => 'Use readiness for traffic eligibility, not for restarting the container.',
            ],
            [
                'name' => 'livenessProbe',
                'purpose' => 'Restarts a stuck container when the process is alive but no longer healthy.',
                'beginner_rule' => 'Make liveness less aggressive than readiness so slow startup does not create restart loops.',
            ],
            [
                'name' => 'startupProbe',
                'purpose' => 'Gives slow-starting apps time to boot before liveness checks begin.',
                'beginner_rule' => $input['app_type'] === 'web-api'
                    ? 'Use startupProbe if framework boot, cache warmup, or migrations make startup slow.'
                    : 'Use startupProbe when worker initialization or dependency checks can take longer than normal.',
            ],
        ];
    }

    /**
     * Return beginner scaling guidance.
     *
     * @param  array{learning_goal: string, app_type: string, replicas: int, needs_external_access: bool, has_stateful_data: bool}  $input
     * @return array{replicas: int, scaling_signal: string, caution: string}
     */
    private function scalingPlanFor(array $input): array
    {
        if ($input['app_type'] === 'scheduled-job') {
            return [
                'replicas' => 1,
                'scaling_signal' => 'Scale by schedule frequency, job duration, queue backlog, or parallelism settings rather than Deployment replicas.',
                'caution' => 'Avoid starting duplicate scheduled work unless the job is explicitly idempotent and concurrency-safe.',
            ];
        }

        return [
            'replicas' => $input['replicas'],
            'scaling_signal' => $input['app_type'] === 'worker'
                ? 'Scale workers from queue depth, job duration, retry rate, and downstream capacity.'
                : 'Scale web APIs from CPU, memory, request rate, latency, and error rate.',
            'caution' => $input['has_stateful_data']
                ? 'Replicas are safer when durable state is outside the pod and writes are coordinated.'
                : 'Replicas help capacity only when the app is stateless and dependencies can handle the extra load.',
        ];
    }

    /**
     * Return CPU and memory planning guidance.
     *
     * @param  array{learning_goal: string, app_type: string, replicas: int, needs_external_access: bool, has_stateful_data: bool}  $input
     * @return array<int, array{setting: string, purpose: string, beginner_rule: string}>
     */
    private function resourcePlanFor(array $input): array
    {
        return [
            [
                'setting' => 'resources.requests.cpu',
                'purpose' => 'Tells the scheduler how much CPU the pod needs to be placed safely on a worker node.',
                'beginner_rule' => 'Without requests, Kubernetes may pack pods too tightly and cause noisy-neighbor pressure.',
            ],
            [
                'setting' => 'resources.requests.memory',
                'purpose' => 'Reserves enough memory for normal operation so scheduling decisions are realistic.',
                'beginner_rule' => 'Set memory requests from observed usage, not from a guess copied across every service.',
            ],
            [
                'setting' => 'resources.limits.memory',
                'purpose' => 'Caps memory so a runaway pod cannot consume the whole worker node.',
                'beginner_rule' => 'If the app exceeds the memory limit, expect OOMKilled events and inspect logs plus metrics.',
            ],
            [
                'setting' => 'horizontal scaling',
                'purpose' => $input['app_type'] === 'worker'
                    ? 'Adds more worker pods when queue pressure is proven and downstream systems can keep up.'
                    : 'Adds more serving pods when request load grows beyond current pod capacity.',
                'beginner_rule' => 'Scaling pods is not a substitute for fixing slow database calls, bad cache design, or missing indexes.',
            ],
        ];
    }

    /**
     * Return rollout and rollback guidance for a beginner deployment.
     *
     * @param  array{learning_goal: string, app_type: string, replicas: int, needs_external_access: bool, has_stateful_data: bool}  $input
     * @return array<int, array{phase: string, goal: string, command: string}>
     */
    private function rolloutPlanFor(array $input): array
    {
        if ($input['app_type'] === 'scheduled-job') {
            return [
                [
                    'phase' => 'validate schedule',
                    'goal' => 'Confirm the CronJob schedule, concurrency policy, retry limit, and idempotency before enabling it.',
                    'command' => 'kubectl describe cronjob <job-name>',
                ],
                [
                    'phase' => 'run once',
                    'goal' => 'Create one manual Job from the CronJob and inspect logs before trusting the schedule.',
                    'command' => 'kubectl create job --from=cronjob/<job-name> <job-name>-manual-check',
                ],
                [
                    'phase' => 'observe',
                    'goal' => 'Check successful jobs, failed jobs, duration, and duplicate execution risk.',
                    'command' => 'kubectl get jobs,pods',
                ],
            ];
        }

        return [
            [
                'phase' => 'apply',
                'goal' => 'Apply the manifest and let Kubernetes create a new ReplicaSet for the updated pod template.',
                'command' => 'kubectl apply -f deployment.yaml',
            ],
            [
                'phase' => 'watch rollout',
                'goal' => 'Confirm new pods become ready before old pods are removed from traffic.',
                'command' => 'kubectl rollout status deployment/<app-name>',
            ],
            [
                'phase' => 'inspect history',
                'goal' => 'Keep release history visible so a bad image or config change can be traced.',
                'command' => 'kubectl rollout history deployment/<app-name>',
            ],
            [
                'phase' => 'rollback',
                'goal' => 'Return to the previous working ReplicaSet if readiness, errors, or latency show the rollout is bad.',
                'command' => 'kubectl rollout undo deployment/<app-name>',
            ],
        ];
    }

    /**
     * Return ConfigMap and Secret guidance.
     *
     * @return array<int, array{name: string, use_for: string, caution: string}>
     */
    private function configSecretPlan(): array
    {
        return [
            [
                'name' => 'ConfigMap',
                'use_for' => 'Non-sensitive configuration such as feature flags, log levels, queue names, or public service URLs.',
                'caution' => 'Changing config does not always restart pods automatically; plan rollout or reload behavior explicitly.',
            ],
            [
                'name' => 'Secret',
                'use_for' => 'Sensitive values such as database passwords, API tokens, private keys, and webhook signing secrets.',
                'caution' => 'Treat Secrets as sensitive operational data and control access with RBAC and external secret management where needed.',
            ],
            [
                'name' => 'Environment variables',
                'use_for' => 'Inject ConfigMap or Secret values into containers in a framework-friendly way.',
                'caution' => 'Avoid scattering config across manifests without ownership, rotation, and audit rules.',
            ],
        ];
    }

    /**
     * Return namespace and RBAC guidance.
     *
     * @param  array{learning_goal: string, app_type: string, replicas: int, needs_external_access: bool, has_stateful_data: bool}  $input
     * @return array<int, array{area: string, beginner_rule: string, reason: string}>
     */
    private function namespaceRbacPlanFor(array $input): array
    {
        return [
            [
                'area' => 'namespace',
                'beginner_rule' => 'Separate environments or teams with namespaces instead of placing every workload in default.',
                'reason' => 'Namespaces make names, access, quotas, and troubleshooting boundaries easier to understand.',
            ],
            [
                'area' => 'service account',
                'beginner_rule' => "Give the {$input['app_type']} its own service account only when it must call the Kubernetes API.",
                'reason' => 'Most app pods do not need cluster-wide credentials.',
            ],
            [
                'area' => 'RBAC',
                'beginner_rule' => 'Grant the smallest Role or ClusterRole needed and bind it only to the right service account.',
                'reason' => 'Over-broad permissions turn one compromised pod into a larger cluster incident.',
            ],
            [
                'area' => 'quota',
                'beginner_rule' => 'Use ResourceQuota and LimitRange when a namespace should not consume unlimited CPU, memory, or objects.',
                'reason' => 'Quotas protect shared clusters from one app exhausting common capacity.',
            ],
        ];
    }

    /**
     * Return beginner network-policy guidance.
     *
     * @param  array{learning_goal: string, app_type: string, replicas: int, needs_external_access: bool, has_stateful_data: bool}  $input
     * @return array<int, array{rule: string, purpose: string, caution: string}>
     */
    private function networkPolicyPlanFor(array $input): array
    {
        return [
            [
                'rule' => 'default deny ingress',
                'purpose' => 'Start from no inbound pod-to-pod traffic, then allow only the callers this workload needs.',
                'caution' => $input['needs_external_access']
                    ? 'Allow ingress-controller or gateway traffic to the web pods, otherwise external requests will time out.'
                    : 'Background workloads often need no external ingress at all.',
            ],
            [
                'rule' => 'allow required egress',
                'purpose' => 'Permit only required outbound calls such as database, Redis, queue broker, DNS, or external APIs.',
                'caution' => 'Blocking DNS or dependency egress can look like an application bug even when the pod is healthy.',
            ],
            [
                'rule' => 'select by labels',
                'purpose' => 'Use stable app and role labels so policies follow pods through rollout and replacement.',
                'caution' => 'A label mismatch can silently isolate healthy pods from needed traffic.',
            ],
        ];
    }

    /**
     * Return observability guidance for first Kubernetes operations.
     *
     * @param  array{learning_goal: string, app_type: string, replicas: int, needs_external_access: bool, has_stateful_data: bool}  $input
     * @return array<int, array{signal: string, command_or_source: string, why: string}>
     */
    private function observabilityPlanFor(array $input): array
    {
        return [
            [
                'signal' => 'pod events',
                'command_or_source' => 'kubectl describe pod <pod-name>',
                'why' => 'Events explain scheduling failures, image pull errors, probe failures, restarts, and volume mount problems.',
            ],
            [
                'signal' => 'container logs',
                'command_or_source' => 'kubectl logs <pod-name> --previous',
                'why' => 'Previous logs are essential when a container already restarted or is in CrashLoopBackOff.',
            ],
            [
                'signal' => 'resource usage',
                'command_or_source' => 'kubectl top pod or metrics dashboard',
                'why' => 'CPU and memory pressure explain throttling, OOMKilled events, and poor scaling decisions.',
            ],
            [
                'signal' => $input['app_type'] === 'worker' ? 'queue lag' : 'request health',
                'command_or_source' => $input['app_type'] === 'worker' ? 'queue dashboard and worker metrics' : 'ingress metrics, Service endpoints, latency, 4xx, and 5xx rate',
                'why' => 'Kubernetes health must be connected to application-level success, not only pod Running status.',
            ],
        ];
    }

    /**
     * Return cost and capacity guidance for cluster planning.
     *
     * @param  array{learning_goal: string, app_type: string, replicas: int, needs_external_access: bool, has_stateful_data: bool}  $input
     * @return array<int, array{area: string, risk: string, control: string}>
     */
    private function costCapacityPlanFor(array $input): array
    {
        return [
            [
                'area' => 'replicas',
                'risk' => "Running {$input['replicas']} requested replica(s) costs CPU and memory even before traffic proves the need.",
                'control' => 'Start with measured requests, watch utilization, then scale from latency, queue lag, or saturation signals.',
            ],
            [
                'area' => 'node capacity',
                'risk' => 'Pods can stay Pending when requested resources do not fit available worker nodes.',
                'control' => 'Review requests, quotas, node autoscaling, and bin-packing before increasing replicas.',
            ],
            [
                'area' => 'external traffic',
                'risk' => $input['needs_external_access']
                    ? 'Ingress controllers, cloud load balancers, and data transfer can add cost and operational surface.'
                    : 'Internal-only workloads still consume cluster resources and may hide inefficient background processing.',
                'control' => 'Track traffic, idle capacity, load balancer count, and per-namespace resource spend.',
            ],
            [
                'area' => 'stateful storage',
                'risk' => $input['has_stateful_data']
                    ? 'Persistent volumes, backups, and retained data can dominate cost and recovery complexity.'
                    : 'Even stateless apps create cost through logs, metrics, image pulls, and over-provisioned pods.',
                'control' => 'Set retention, right-size storage, and prefer managed stateful services when the team cannot operate storage safely.',
            ],
        ];
    }

    /**
     * Return availability controls for node drain and disruption events.
     *
     * @param  array{learning_goal: string, app_type: string, replicas: int, needs_external_access: bool, has_stateful_data: bool}  $input
     * @return array<int, array{control: string, purpose: string, caution: string}>
     */
    private function availabilityPlanFor(array $input): array
    {
        if ($input['app_type'] === 'scheduled-job') {
            return [
                [
                    'control' => 'concurrencyPolicy',
                    'purpose' => 'Prevents overlapping scheduled job executions when the previous run is still active.',
                    'caution' => 'Pick Forbid or Replace based on whether duplicate work is more dangerous than skipping a run.',
                ],
                [
                    'control' => 'backoffLimit',
                    'purpose' => 'Limits retry storms when a job keeps failing.',
                    'caution' => 'Retries should match idempotency guarantees and downstream capacity.',
                ],
            ];
        }

        return [
            [
                'control' => 'PodDisruptionBudget',
                'purpose' => 'Keeps enough replicas available during voluntary disruptions such as node drain or cluster maintenance.',
                'caution' => $input['replicas'] > 1
                    ? 'Set minAvailable or maxUnavailable based on real capacity, not just replica count.'
                    : 'A single replica cannot stay available during every disruption; explain that limit clearly.',
            ],
            [
                'control' => 'pod anti-affinity or topology spread',
                'purpose' => 'Spreads replicas across nodes or zones so one failure does not remove every copy.',
                'caution' => 'Strict spreading can keep pods Pending when the cluster is small or uneven.',
            ],
            [
                'control' => 'readiness gates',
                'purpose' => 'Keep pods out of traffic until the app and dependencies are ready after rollout or restart.',
                'caution' => 'A bad readiness rule can make every pod look unavailable.',
            ],
        ];
    }

    /**
     * Return graceful shutdown guidance.
     *
     * @param  array{learning_goal: string, app_type: string, replicas: int, needs_external_access: bool, has_stateful_data: bool}  $input
     * @return array<int, array{control: string, purpose: string, beginner_rule: string}>
     */
    private function shutdownPlanFor(array $input): array
    {
        return [
            [
                'control' => 'SIGTERM handling',
                'purpose' => $input['app_type'] === 'worker'
                    ? 'Let workers stop accepting new jobs and finish or safely release the current job.'
                    : 'Let the app stop accepting traffic and finish in-flight requests before exit.',
                'beginner_rule' => 'Do not assume container stop is instant; the app must cooperate with shutdown.',
            ],
            [
                'control' => 'preStop hook',
                'purpose' => 'Give the pod a small window to drain connections or notify the app before termination.',
                'beginner_rule' => 'Use preStop carefully; sleeping forever only hides bad shutdown behavior.',
            ],
            [
                'control' => 'terminationGracePeriodSeconds',
                'purpose' => 'Defines how long Kubernetes waits before forcefully killing the container.',
                'beginner_rule' => 'Set it from real request, job, or cleanup duration rather than copying a random value.',
            ],
        ];
    }

    /**
     * Return image and pod security basics.
     *
     * @return array<int, array{control: string, purpose: string, beginner_rule: string}>
     */
    private function imageSecurityPlan(): array
    {
        return [
            [
                'control' => 'immutable image tag',
                'purpose' => 'Makes rollouts repeatable by pointing to a fixed version or digest.',
                'beginner_rule' => 'Avoid using latest in production because the same manifest can deploy different code later.',
            ],
            [
                'control' => 'imagePullPolicy',
                'purpose' => 'Controls when nodes pull images and helps avoid stale or surprising runtime versions.',
                'beginner_rule' => 'Use a clear tag strategy so pull policy behavior is predictable.',
            ],
            [
                'control' => 'securityContext',
                'purpose' => 'Runs containers with safer defaults such as non-root users and reduced privileges.',
                'beginner_rule' => 'Start with runAsNonRoot, readOnlyRootFilesystem where possible, and no privileged containers.',
            ],
            [
                'control' => 'image scanning',
                'purpose' => 'Finds known vulnerabilities before the image enters the cluster.',
                'beginner_rule' => 'Scanning does not replace patching; it creates a feedback loop for fixing base images and dependencies.',
            ],
        ];
    }

    /**
     * Return backend and Laravel-oriented runtime guidance for Kubernetes.
     *
     * @param  array{learning_goal: string, app_type: string, replicas: int, needs_external_access: bool, has_stateful_data: bool}  $input
     * @return array<int, array{concern: string, kubernetes_shape: string, caution: string}>
     */
    private function backendRuntimePlanFor(array $input): array
    {
        return [
            [
                'concern' => 'database migrations',
                'kubernetes_shape' => 'Run migrations as a controlled Job or release step before routing new app pods.',
                'caution' => 'Do not let every web pod run migrations on startup; concurrent migrations can block deploys or corrupt release order.',
            ],
            [
                'concern' => 'queue workers',
                'kubernetes_shape' => $input['app_type'] === 'worker'
                    ? 'Use a dedicated worker Deployment scaled from queue depth, duration, retry rate, and downstream capacity.'
                    : 'Keep worker pods separate from web pods so HTTP traffic and background jobs can scale independently.',
                'caution' => 'Workers need graceful shutdown so jobs are finished, released, or retried safely during rollout.',
            ],
            [
                'concern' => 'sessions and cache',
                'kubernetes_shape' => 'Move session, cache, lock, and rate-limit state to Redis, database, or another shared service.',
                'caution' => 'Local pod filesystem or in-memory state breaks when traffic moves across replicas or a pod is replaced.',
            ],
            [
                'concern' => 'readiness dependency',
                'kubernetes_shape' => 'Readiness should prove the app can serve its basic request path and required lightweight dependencies.',
                'caution' => 'Do not make readiness run expensive migrations, broad database scans, or slow external calls.',
            ],
            [
                'concern' => 'scheduled tasks',
                'kubernetes_shape' => $input['app_type'] === 'scheduled-job'
                    ? 'Use CronJob with clear concurrencyPolicy, backoffLimit, and idempotent command behavior.'
                    : 'Move scheduler commands into a dedicated CronJob or scheduler pod instead of hiding them in every web replica.',
                'caution' => 'Duplicate schedulers can send duplicate emails, duplicate invoices, or double-process records.',
            ],
        ];
    }

    /**
     * Return a practical manifest review checklist.
     *
     * @param  array{learning_goal: string, app_type: string, replicas: int, needs_external_access: bool, has_stateful_data: bool}  $input
     * @return array<int, array{area: string, check: string, failure_if_missing: string}>
     */
    private function manifestReviewChecklistFor(array $input): array
    {
        return [
            [
                'area' => 'labels and selectors',
                'check' => 'Deployment, Pod template, Service, NetworkPolicy, and monitoring selectors use stable matching labels.',
                'failure_if_missing' => 'Healthy pods may receive no traffic or bypass policy and dashboards.',
            ],
            [
                'area' => 'probes',
                'check' => 'Readiness, liveness, and startup probes match real app behavior and startup time.',
                'failure_if_missing' => 'Traffic can reach unready pods or Kubernetes can restart slow but healthy apps.',
            ],
            [
                'area' => 'resources',
                'check' => 'CPU and memory requests are set from observed usage, and memory limits are intentional.',
                'failure_if_missing' => 'Pods can be packed badly, stay Pending, or die with OOMKilled events.',
            ],
            [
                'area' => 'rollout safety',
                'check' => $input['app_type'] === 'scheduled-job'
                    ? 'CronJob has concurrencyPolicy, backoffLimit, activeDeadlineSeconds, and idempotent command behavior.'
                    : 'Deployment has rollout strategy, graceful shutdown, and enough replicas for safe replacement.',
                'failure_if_missing' => 'Deploys can duplicate work, drop traffic, or make rollback harder.',
            ],
            [
                'area' => 'runtime state',
                'check' => 'Sessions, cache, queue state, uploads, and durable data do not depend on one pod filesystem.',
                'failure_if_missing' => 'Requests fail or data disappears when pods move, restart, or scale.',
            ],
        ];
    }

    /**
     * Return CI/CD gates for Kubernetes manifests.
     *
     * @param  array{learning_goal: string, app_type: string, replicas: int, needs_external_access: bool, has_stateful_data: bool}  $input
     * @return array<int, array{gate: string, purpose: string, example: string}>
     */
    private function cicdGatePlanFor(array $input): array
    {
        return [
            [
                'gate' => 'manifest render',
                'purpose' => 'Prove overlays, variables, or templates produce valid Kubernetes YAML before apply.',
                'example' => 'kustomize build overlays/staging or helm template app chart/',
            ],
            [
                'gate' => 'server-side dry run',
                'purpose' => 'Ask the cluster API whether the rendered manifests are structurally acceptable.',
                'example' => 'kubectl apply --dry-run=server -f rendered.yaml',
            ],
            [
                'gate' => 'policy check',
                'purpose' => 'Reject manifests missing required labels, probes, resource requests, non-root settings, or allowed registries.',
                'example' => 'Run kube-linter, conftest, or an admission-policy test in CI.',
            ],
            [
                'gate' => $input['app_type'] === 'scheduled-job' ? 'manual job smoke test' : 'rollout smoke test',
                'purpose' => $input['app_type'] === 'scheduled-job'
                    ? 'Run one job safely before enabling schedule-based repetition.'
                    : 'Confirm rollout status, endpoint health, logs, and one real request after deploy.',
                'example' => $input['app_type'] === 'scheduled-job'
                    ? 'kubectl create job --from=cronjob/<job-name> <job-name>-smoke'
                    : 'kubectl rollout status deployment/<app-name> && curl https://app.example/up',
            ],
        ];
    }

    /**
     * Return small YAML snippets for learners to recognize.
     *
     * @param  array{learning_goal: string, app_type: string, replicas: int, needs_external_access: bool, has_stateful_data: bool}  $input
     * @return array<int, array{name: string, snippet: string}>
     */
    private function yamlSnippetsFor(array $input): array
    {
        return [
            [
                'name' => 'deployment probes and resources',
                'snippet' => "readinessProbe:\n  httpGet:\n    path: /up\n    port: 8080\nresources:\n  requests:\n    cpu: 100m\n    memory: 128Mi\n  limits:\n    memory: 256Mi",
            ],
            [
                'name' => 'service selector',
                'snippet' => "selector:\n  app.kubernetes.io/name: app\nports:\n  - port: 80\n    targetPort: 8080",
            ],
            [
                'name' => $input['app_type'] === 'scheduled-job' ? 'cronjob safety' : 'pod disruption budget',
                'snippet' => $input['app_type'] === 'scheduled-job'
                    ? "concurrencyPolicy: Forbid\nbackoffLimit: 2\nactiveDeadlineSeconds: 900"
                    : "minAvailable: 2\nselector:\n  matchLabels:\n    app.kubernetes.io/name: app",
            ],
        ];
    }

    /**
     * Return a short spoken script learners can rehearse.
     *
     * @param  array{learning_goal: string, app_type: string, replicas: int, needs_external_access: bool, has_stateful_data: bool}  $input
     * @return array{hook: string, analogy: string, mechanics: string, production_note: string, closing: string}
     */
    private function oneMinuteScriptFor(array $input): array
    {
        return [
            'hook' => 'Kubernetes is easiest to understand as a fleet manager for containers.',
            'analogy' => 'The control plane is the command ship, worker nodes are cargo ships, pods carry app containers, and Services are stable harbor addresses.',
            'mechanics' => "For a {$input['app_type']}, I declare {$input['replicas']} desired replica(s); Kubernetes schedules pods, checks readiness, routes traffic, and replaces failed pods.",
            'production_note' => $input['has_stateful_data']
                ? 'Stateful data needs persistent storage or managed services because pod files are replaceable.'
                : 'Stateless apps scale more safely when sessions, cache, and durable state live outside the pod.',
            'closing' => 'Kubernetes is desired-state orchestration: scheduling, service discovery, rollout, self-healing, and operations guardrails.',
        ];
    }

    /**
     * Return an interview rubric for judging Kubernetes explanations.
     *
     * @return array<int, array{criterion: string, strong_signal: string, weak_signal: string}>
     */
    private function interviewRubric(): array
    {
        return [
            [
                'criterion' => 'definition',
                'strong_signal' => 'Defines Kubernetes as container orchestration that keeps desired state across machines.',
                'weak_signal' => 'Says Kubernetes is just Docker, hosting, or a server.',
            ],
            [
                'criterion' => 'analogy',
                'strong_signal' => 'Maps control plane, worker node, pod, container, Deployment, and Service without mixing roles.',
                'weak_signal' => 'Uses the ship story but cannot connect it to real Kubernetes resources.',
            ],
            [
                'criterion' => 'mechanism',
                'strong_signal' => 'Explains scheduling, readiness, service routing, rollout, and reconciliation.',
                'weak_signal' => 'Only says Kubernetes runs apps automatically.',
            ],
            [
                'criterion' => 'production tradeoff',
                'strong_signal' => 'Mentions probes, resource requests, external state, storage, and least-privilege access.',
                'weak_signal' => 'Ignores failure modes, stateful data, and operational boundaries.',
            ],
            [
                'criterion' => 'debugging',
                'strong_signal' => 'Starts from events, describe, logs, endpoints, rollout status, and storage checks.',
                'weak_signal' => 'Jumps straight to redeploying without inspecting evidence.',
            ],
        ];
    }

    /**
     * Return a progressive kubectl command ladder for beginners.
     *
     * @param  array{learning_goal: string, app_type: string, replicas: int, needs_external_access: bool, has_stateful_data: bool}  $input
     * @return array<int, array{level: string, command: string, use_when: string}>
     */
    private function commandLadderFor(array $input): array
    {
        $commands = [
            [
                'level' => 'cluster map',
                'command' => 'kubectl get nodes',
                'use_when' => 'Confirm the cargo ships exist and are Ready before blaming the app.',
            ],
            [
                'level' => 'workload map',
                'command' => 'kubectl get deploy,pods,svc',
                'use_when' => 'See whether Deployment, pods, and stable Service address exist together.',
            ],
            [
                'level' => 'pod evidence',
                'command' => 'kubectl describe pod <pod-name>',
                'use_when' => 'Inspect events, scheduling failures, image pulls, probes, restarts, and volume issues.',
            ],
            [
                'level' => 'logs',
                'command' => 'kubectl logs <pod-name> --previous',
                'use_when' => 'Read the last crashed container output before changing manifests.',
            ],
            [
                'level' => $input['needs_external_access'] ? 'traffic' : 'internal routing',
                'command' => $input['needs_external_access']
                    ? 'kubectl get ingress,service,endpoints'
                    : 'kubectl get service,endpoints',
                'use_when' => $input['needs_external_access']
                    ? 'Verify external route, Service selector, and endpoint readiness.'
                    : 'Verify internal Service DNS, selectors, and ready endpoints.',
            ],
        ];

        if ($input['has_stateful_data']) {
            $commands[] = [
                'level' => 'storage',
                'command' => 'kubectl get pvc,pv',
                'use_when' => 'Check whether durable storage exists and is bound before relying on pod files.',
            ];
        }

        return $commands;
    }

    /**
     * Return resource selection guidance for common beginner decisions.
     *
     * @param  array{learning_goal: string, app_type: string, replicas: int, needs_external_access: bool, has_stateful_data: bool}  $input
     * @return array<int, array{question: string, choose: string, avoid: string}>
     */
    private function resourceDecisionGuideFor(array $input): array
    {
        return [
            [
                'question' => 'What runs the workload?',
                'choose' => $input['app_type'] === 'scheduled-job'
                    ? 'Choose CronJob because the work is triggered by a schedule.'
                    : 'Choose Deployment because the app should keep a desired number of pods running.',
                'avoid' => 'Avoid creating raw Pods for normal apps because replacement, rollout, and history are then manual.',
            ],
            [
                'question' => 'How does traffic find pods?',
                'choose' => $input['needs_external_access']
                    ? 'Choose Service plus Ingress or LoadBalancer so users reach stable endpoints instead of pod IPs.'
                    : 'Choose ClusterIP Service only when another workload must call this app internally.',
                'avoid' => 'Avoid sending callers directly to pod IPs because pods are replaceable.',
            ],
            [
                'question' => 'Where does durable state live?',
                'choose' => $input['has_stateful_data']
                    ? 'Choose PersistentVolume, StatefulSet, database, object storage, or a managed service based on the data shape.'
                    : 'Choose external session, cache, queue, and database services so pods stay disposable.',
                'avoid' => 'Avoid writing important data only to the container filesystem.',
            ],
            [
                'question' => 'How should rollout risk be reduced?',
                'choose' => 'Choose readiness probes, rollout status checks, history, rollback, and smoke tests.',
                'avoid' => 'Avoid treating a successful kubectl apply as proof the app is healthy.',
            ],
        ];
    }

    /**
     * Return manifest smells learners should catch during review.
     *
     * @param  array{learning_goal: string, app_type: string, replicas: int, needs_external_access: bool, has_stateful_data: bool}  $input
     * @return array<int, array{smell: string, risk: string, fix: string}>
     */
    private function manifestSmellCatalogFor(array $input): array
    {
        $smells = [
            [
                'smell' => 'selector labels do not match pod labels',
                'risk' => 'Service has no endpoints, so traffic never reaches healthy pods.',
                'fix' => 'Use the same stable app labels in Deployment template metadata and Service selector.',
            ],
            [
                'smell' => 'missing readinessProbe',
                'risk' => 'Service can route traffic to a pod before framework boot, cache warmup, or dependency checks are ready.',
                'fix' => 'Add a lightweight readiness endpoint that proves the app can serve the basic request path.',
            ],
            [
                'smell' => 'no memory request or limit',
                'risk' => 'Scheduling becomes guesswork and memory spikes can create OOMKilled events.',
                'fix' => 'Set memory requests from observed normal usage and a memory limit from tested peak behavior.',
            ],
            [
                'smell' => 'image tag is latest',
                'risk' => 'Rollbacks and incident analysis become unreliable because the same tag can point to different code.',
                'fix' => 'Use immutable image tags such as a release version or commit SHA.',
            ],
        ];

        if ($input['has_stateful_data']) {
            $smells[] = [
                'smell' => 'stateful writes go to emptyDir or container filesystem',
                'risk' => 'Data can disappear when a pod is replaced, rescheduled, or rolled back.',
                'fix' => 'Move durable data to persistent storage, a database, object storage, or a managed service.',
            ];
        }

        return $smells;
    }

    /**
     * Return small practice drills for turning the analogy into operations habits.
     *
     * @param  array{learning_goal: string, app_type: string, replicas: int, needs_external_access: bool, has_stateful_data: bool}  $input
     * @return array<int, array{name: string, task: string, expected_signal: string}>
     */
    private function practiceDrillsFor(array $input): array
    {
        $drills = [
            [
                'name' => 'explain without notes',
                'task' => 'Give the one-minute ship analogy, then name the matching Kubernetes resource after each ship word.',
                'expected_signal' => 'The answer mentions control plane, worker node, pod, container, Deployment, Service, and reconciliation.',
            ],
            [
                'name' => 'trace one request',
                'task' => $input['needs_external_access']
                    ? 'Trace a request from DNS to ingress, Service, endpoint, pod, and readiness check.'
                    : 'Trace an internal call or scheduler trigger to Service, endpoint, pod, and readiness check.',
                'expected_signal' => 'The learner can explain why Service addresses stay stable while pods can be replaced.',
            ],
            [
                'name' => 'debug a failed rollout',
                'task' => 'Inspect rollout status, describe pod events, previous logs, probe failures, and resource pressure before changing YAML.',
                'expected_signal' => 'The first action is evidence gathering, not redeploying blindly.',
            ],
        ];

        if ($input['has_stateful_data']) {
            $drills[] = [
                'name' => 'prove storage durability',
                'task' => 'Show which PVC, database, object store, or managed service keeps data after pod replacement.',
                'expected_signal' => 'The learner can point to storage outside the disposable pod filesystem.',
            ];
        }

        return $drills;
    }

    /**
     * Return a lightweight readiness score for the requested scenario.
     *
     * @param  array{learning_goal: string, app_type: string, replicas: int, needs_external_access: bool, has_stateful_data: bool}  $input
     * @return array{score: int, level: string, reasons: array<int, string>, next_action: string}
     */
    private function productionReadinessScoreFor(array $input): array
    {
        $score = 90;
        $reasons = [
            'Baseline assumes probes, resource requests, rollout checks, and logs are planned.',
        ];

        if ($input['app_type'] !== 'scheduled-job' && $input['replicas'] < 2) {
            $score -= 15;
            $reasons[] = 'A serving or worker Deployment with one replica has limited availability during node failure or rollout.';
        }

        if ($input['needs_external_access']) {
            $score -= 10;
            $reasons[] = 'External access adds ingress, TLS, DNS, load-balancer, and route health risks.';
        }

        if ($input['has_stateful_data']) {
            $score -= 20;
            $reasons[] = 'Stateful data needs storage, backup, restore, and pod replacement checks before production use.';
        }

        if ($input['app_type'] === 'scheduled-job') {
            $score -= 5;
            $reasons[] = 'Scheduled work needs idempotency, concurrency policy, retry limits, and manual smoke runs.';
        }

        $level = match (true) {
            $score >= 80 => 'ready with review',
            $score >= 60 => 'needs hardening',
            default => 'high risk',
        };

        return [
            'score' => $score,
            'level' => $level,
            'reasons' => $reasons,
            'next_action' => $score >= 80
                ? 'Run a rollout smoke test and confirm SLO signals before release.'
                : 'Close the highest-risk storage, availability, or traffic gap before release.',
        ];
    }

    /**
     * Return SLO-oriented signals for the workload.
     *
     * @param  array{learning_goal: string, app_type: string, replicas: int, needs_external_access: bool, has_stateful_data: bool}  $input
     * @return array<int, array{signal: string, target: string, alert: string}>
     */
    private function sloObservabilityPlanFor(array $input): array
    {
        $signals = [
            [
                'signal' => 'availability',
                'target' => $input['app_type'] === 'scheduled-job'
                    ? 'Scheduled job completes successfully within its expected window.'
                    : 'Healthy endpoints remain available during rollout and node disruption.',
                'alert' => $input['app_type'] === 'scheduled-job'
                    ? 'Alert when the job misses a schedule, exceeds deadline, or fails repeatedly.'
                    : 'Alert when ready endpoints drop below the safe replica count.',
            ],
            [
                'signal' => 'latency or duration',
                'target' => $input['app_type'] === 'worker'
                    ? 'Queue wait time and job duration stay within the business target.'
                    : 'Request latency or job duration stays inside the agreed SLO.',
                'alert' => 'Alert when p95 latency, queue age, or job duration stays above target for a sustained window.',
            ],
            [
                'signal' => 'error rate',
                'target' => 'Application errors, failed probes, and restart loops remain rare.',
                'alert' => 'Alert on rising 5xx, failed jobs, CrashLoopBackOff, probe failure, or restart rate.',
            ],
            [
                'signal' => 'resource saturation',
                'target' => 'CPU, memory, and node pressure leave enough headroom for normal bursts.',
                'alert' => 'Alert on OOMKilled, high memory usage, CPU throttling, unschedulable pods, or disk pressure.',
            ],
        ];

        if ($input['has_stateful_data']) {
            $signals[] = [
                'signal' => 'storage durability',
                'target' => 'Persistent storage is bound, backed up, restorable, and monitored for capacity.',
                'alert' => 'Alert on PVC not bound, volume mount failure, backup failure, or fast storage growth.',
            ];
        }

        return $signals;
    }

    /**
     * Return review questions that connect manifests to evidence.
     *
     * @param  array{learning_goal: string, app_type: string, replicas: int, needs_external_access: bool, has_stateful_data: bool}  $input
     * @return array<int, array{question: string, evidence: string}>
     */
    private function deploymentReviewQuestionsFor(array $input): array
    {
        $questions = [
            [
                'question' => 'Can Kubernetes decide where to place the pod safely?',
                'evidence' => 'Manifest includes realistic CPU and memory requests, plus limits where they reduce blast radius.',
            ],
            [
                'question' => 'Can traffic avoid pods that are not ready?',
                'evidence' => 'Readiness probe matches a lightweight application health path and Service endpoints show only ready pods.',
            ],
            [
                'question' => 'Can the team recover from a bad release?',
                'evidence' => 'Rollout status, rollout history, immutable image tag, smoke test, and rollback command are known.',
            ],
            [
                'question' => $input['needs_external_access']
                    ? 'Is the external route controlled and observable?'
                    : 'Is internal routing scoped to the callers that need it?',
                'evidence' => $input['needs_external_access']
                    ? 'Ingress or LoadBalancer has host/path rules, TLS ownership, Service endpoints, and health checks.'
                    : 'ClusterIP Service, endpoints, DNS name, and NetworkPolicy egress rules are documented.',
            ],
        ];

        if ($input['has_stateful_data']) {
            $questions[] = [
                'question' => 'What proves data survives pod replacement?',
                'evidence' => 'PVC, database, object store, backup, restore drill, and ownership model are documented.',
            ];
        }

        return $questions;
    }

    /**
     * Return the request flow through the cluster.
     *
     * @param  array{learning_goal: string, app_type: string, replicas: int, needs_external_access: bool, has_stateful_data: bool}  $input
     * @return array<int, string>
     */
    private function trafficFlowFor(array $input): array
    {
        if (! $input['needs_external_access']) {
            return [
                'Internal caller or scheduler starts the workload.',
                'Pod talks to internal services by Kubernetes DNS name.',
                'Readiness checks keep unhealthy pods out of service traffic.',
            ];
        }

        return [
            'User request reaches DNS, then the ingress or cloud load balancer.',
            'Ingress routes by host or path to a Kubernetes Service.',
            'Service selects healthy pods by label and balances traffic across replicas.',
            'Readiness checks prevent pods from receiving traffic before the app is ready.',
        ];
    }

    /**
     * Return manifest files a beginner should recognize.
     *
     * @param  array{learning_goal: string, app_type: string, replicas: int, needs_external_access: bool, has_stateful_data: bool}  $input
     * @return array<int, array{file: string, purpose: string}>
     */
    private function manifestOutlineFor(array $input): array
    {
        $files = [
            [
                'file' => 'deployment.yaml',
                'purpose' => 'Defines image, replicas, labels, environment variables, probes, and rollout behavior.',
            ],
            [
                'file' => 'service.yaml',
                'purpose' => 'Gives pods a stable internal address and selects them by label.',
            ],
        ];

        if ($input['needs_external_access']) {
            $files[] = [
                'file' => 'ingress.yaml',
                'purpose' => 'Routes external HTTP traffic to the Service by host or path.',
            ];
        }

        if ($input['has_stateful_data']) {
            $files[] = [
                'file' => 'storage.yaml',
                'purpose' => 'Defines persistent storage instead of depending on the pod filesystem.',
            ];
        }

        return $files;
    }

    /**
     * Return kubectl commands for first inspection.
     *
     * @param  array{learning_goal: string, app_type: string, replicas: int, needs_external_access: bool, has_stateful_data: bool}  $input
     * @return array<int, string>
     */
    private function kubectlCommandsFor(array $input): array
    {
        $commands = [
            'kubectl get nodes',
            'kubectl get pods',
            'kubectl describe pod <pod-name>',
            'kubectl logs <pod-name>',
            'kubectl rollout status deployment/<app-name>',
        ];

        if ($input['needs_external_access']) {
            $commands[] = 'kubectl get service,ingress';
        }

        return $commands;
    }

    /**
     * Return first-response checks for common Kubernetes beginner failures.
     *
     * @param  array{learning_goal: string, app_type: string, replicas: int, needs_external_access: bool, has_stateful_data: bool}  $input
     * @return array<int, array{symptom: string, first_check: string, likely_fix: string}>
     */
    private function troubleshootingRunbookFor(array $input): array
    {
        $items = [
            [
                'symptom' => 'Pod is Pending',
                'first_check' => 'Run kubectl describe pod and inspect scheduling events.',
                'likely_fix' => 'Check node capacity, resource requests, taints, tolerations, node selectors, and image pull secrets.',
            ],
            [
                'symptom' => 'Pod is CrashLoopBackOff',
                'first_check' => 'Run kubectl logs --previous and inspect the container exit reason.',
                'likely_fix' => 'Fix startup config, missing environment variables, failed dependency checks, or an overly aggressive probe.',
            ],
            [
                'symptom' => 'Service has no traffic',
                'first_check' => 'Check Service selector labels, endpoints, readiness status, and ingress routing.',
                'likely_fix' => 'Align labels between Deployment and Service, then verify readiness probes and ingress rules.',
            ],
        ];

        if ($input['has_stateful_data']) {
            $items[] = [
                'symptom' => 'Data disappears after pod replacement',
                'first_check' => 'Check whether the app wrote to the pod filesystem instead of persistent storage.',
                'likely_fix' => 'Move durable data to a PersistentVolume, StatefulSet, database, or managed storage service.',
            ];
        }

        return $items;
    }

    /**
     * Return a diagnosis matrix for common Kubernetes status words.
     *
     * @param  array{learning_goal: string, app_type: string, replicas: int, needs_external_access: bool, has_stateful_data: bool}  $input
     * @return array<int, array{status: string, likely_layer: string, inspect: string, next_action: string}>
     */
    private function failureDiagnosisMatrixFor(array $input): array
    {
        $items = [
            [
                'status' => 'ImagePullBackOff',
                'likely_layer' => 'image registry or image reference',
                'inspect' => 'kubectl describe pod <pod-name> and check Events for pull, auth, or tag errors.',
                'next_action' => 'Fix image name, tag, registry credentials, imagePullSecret, or network path to the registry.',
            ],
            [
                'status' => 'OOMKilled',
                'likely_layer' => 'memory limit and application memory behavior',
                'inspect' => 'kubectl describe pod <pod-name>, kubectl top pod, and previous logs.',
                'next_action' => 'Review memory limit, memory requests, leaks, batch size, cache size, and traffic spike.',
            ],
            [
                'status' => 'Readiness probe failed',
                'likely_layer' => 'application readiness or dependency availability',
                'inspect' => 'kubectl describe pod <pod-name>, probe path, app logs, dependency health, and startup timing.',
                'next_action' => 'Fix the readiness endpoint, dependency config, startup timing, or remove traffic until the app is truly ready.',
            ],
            [
                'status' => 'Forbidden',
                'likely_layer' => 'RBAC or service account permissions',
                'inspect' => 'kubectl auth can-i <verb> <resource> --as system:serviceaccount:<namespace>:<service-account>',
                'next_action' => 'Grant the smallest Role and RoleBinding needed, or remove unnecessary Kubernetes API access from the pod.',
            ],
            [
                'status' => 'DNS or connection timeout',
                'likely_layer' => 'service discovery, NetworkPolicy, DNS, or dependency reachability',
                'inspect' => 'Check Service name, endpoints, NetworkPolicy egress, CoreDNS health, and dependency address.',
                'next_action' => $input['needs_external_access']
                    ? 'Verify ingress path separately from internal service DNS and pod-to-pod egress.'
                    : 'Verify internal DNS, allowed egress, and dependency service readiness.',
            ],
        ];

        if ($input['has_stateful_data']) {
            $items[] = [
                'status' => 'Volume mount failed',
                'likely_layer' => 'persistent volume, storage class, or access mode',
                'inspect' => 'kubectl describe pod <pod-name>, PVC status, PV binding, storage class, and node attach events.',
                'next_action' => 'Fix storage class, access mode, quota, zone placement, or move state to a managed service.',
            ];
        }

        return $items;
    }

    /**
     * Return common beginner misconceptions.
     *
     * @return array<int, array{myth: string, correction: string}>
     */
    private function beginnerMisconceptions(): array
    {
        return [
            [
                'myth' => 'Kubernetes is only Docker with a bigger name.',
                'correction' => 'Docker packages and runs containers; Kubernetes schedules, connects, heals, and rolls out containers across machines.',
            ],
            [
                'myth' => 'A pod is the same thing as a container.',
                'correction' => 'A pod is the Kubernetes unit that wraps one or more containers with shared networking and lifecycle.',
            ],
            [
                'myth' => 'If a pod restarts, all application data is safe automatically.',
                'correction' => 'Pod files can disappear; durable data needs persistent storage or an external managed service.',
            ],
            [
                'myth' => 'Scaling replicas fixes every production problem.',
                'correction' => 'Replicas help stateless capacity, but they do not fix database bottlenecks, bad probes, broken config, or stateful design.',
            ],
        ];
    }

    /**
     * Return an interview-ready Kubernetes answer.
     *
     * @param  array{learning_goal: string, app_type: string, replicas: int, needs_external_access: bool, has_stateful_data: bool}  $input
     */
    private function interviewAnswerFor(array $input): string
    {
        return "Kubernetes is a container orchestration platform. Using the ship analogy: the control plane is the command ship, worker nodes are cargo ships, pods carry the app containers, deployments describe how many copies should run, and services give those pods a stable address. For a {$input['app_type']}, I would declare {$input['replicas']} replica(s), add readiness and liveness checks, expose it only if needed, and keep durable state outside disposable pods.";
    }
}
