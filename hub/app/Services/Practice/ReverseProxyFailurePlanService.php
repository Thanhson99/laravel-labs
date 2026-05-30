<?php

declare(strict_types=1);

namespace App\Services\Practice;

use Illuminate\Support\Str;

final class ReverseProxyFailurePlanService
{
    /**
     * Build a reverse-proxy failure-mode plan for edge and origin reachability practice.
     *
     * @param  array{service_name: string, proxy_layer: string, change_type: string, origin_count: int, rollout_strategy: string, has_health_gate: bool, fail_behavior: string, observed_failure: string}  $input
     * @return array<string, mixed>
     */
    public function plan(array $input): array
    {
        $service = Str::studly($input['service_name']);
        $risk = $this->riskLevel($input);
        $coreLesson = $this->coreLesson($input);
        $riskSignals = $this->riskSignals($input);
        $blastRadiusControls = $this->blastRadiusControls($input);
        $rolloutPlan = $this->rolloutPlan($input);
        $observabilityPlan = $this->observabilityPlan($input);
        $incidentTriage = $this->incidentTriage($input);
        $originReachabilityChecks = $this->originReachabilityChecks($input['origin_count']);
        $decisionTree = $this->decisionTree($input);
        $reviewChecklist = $this->reviewChecklist($input);
        $scenarioPlaybook = $this->scenarioPlaybook($input);
        $simulationPlan = $this->simulationPlan($input);
        $chaosDrill = $this->chaosDrill($input);
        $readinessScore = $this->readinessScore($input);
        $healthGatePolicy = $this->healthGatePolicy($input);
        $alertRules = $this->alertRules($input);
        $ownershipMatrix = $this->ownershipMatrix($input);
        $postmortemActions = $this->postmortemActions($input);
        $incidentTimeline = $this->incidentTimeline($input);
        $evidencePack = $this->evidencePack($input);

        return [
            'service' => $service,
            'risk_level' => $risk,
            'readiness_score' => $readinessScore,
            'executive_summary' => $this->executiveSummary($input, $risk, $coreLesson, $riskSignals, $blastRadiusControls),
            'core_lesson' => $coreLesson,
            'request_path' => $this->requestPath($input['proxy_layer']),
            'risk_signals' => $riskSignals,
            'blast_radius_controls' => $blastRadiusControls,
            'config_validation_plan' => $this->configValidationPlan($input),
            'rollout_plan' => $rolloutPlan,
            'fail_small_design' => $this->failSmallDesign($input),
            'observability_plan' => $observabilityPlan,
            'incident_triage' => $incidentTriage,
            'origin_reachability_checks' => $originReachabilityChecks,
            'decision_tree' => $decisionTree,
            'configuration_review' => $this->configurationReview($input),
            'review_checklist' => $reviewChecklist,
            'scenario_playbook' => $scenarioPlaybook,
            'simulation_plan' => $simulationPlan,
            'chaos_drill' => $chaosDrill,
            'health_gate_policy' => $healthGatePolicy,
            'alert_rules' => $alertRules,
            'ownership_matrix' => $ownershipMatrix,
            'postmortem_actions' => $postmortemActions,
            'incident_timeline' => $incidentTimeline,
            'evidence_pack' => $evidencePack,
            'edge_outage_lesson_map' => $this->edgeOutageLessonMap(),
            'interview_answer' => $this->interviewAnswer($input, $risk),
            'incident_memo_markdown' => $this->incidentMemoMarkdown($service, $input, $risk, $readinessScore, $coreLesson, $riskSignals, $blastRadiusControls, $rolloutPlan, $observabilityPlan, $incidentTriage, $originReachabilityChecks, $decisionTree, $scenarioPlaybook, $simulationPlan, $healthGatePolicy, $ownershipMatrix, $postmortemActions, $incidentTimeline, $evidencePack),
            'postmortem_note' => 'The useful interview lesson is not that reverse proxies are bad. The lesson is that shared edge request paths need validation, staged rollout, health gates, rollback, and small blast radius.',
            'tests' => [
                'Reject config or generated data that exceeds expected schema, size, or cardinality.',
                'Canary the proxy change through a small traffic slice before global rollout.',
                'Prove healthy origins become reachable again after rollback or edge bypass.',
                'Alert separately on proxy-generated 5xx, origin-generated 5xx, request volume, and config publish events.',
            ],
            'commands' => [
                'php artisan test --filter ReverseProxyFailurePlan',
                'php artisan route:list --path=reverse-proxy-failure-plan',
                'vendor\\bin\\pint --test',
            ],
        ];
    }

    /**
     * Return a compact human-readable summary for the workbench UI.
     *
     * @param  array{proxy_layer: string, change_type: string, origin_count: int, rollout_strategy: string, has_health_gate: bool, fail_behavior: string, observed_failure: string}  $input
     * @param  array<int, string>  $riskSignals
     * @param  array<int, string>  $blastRadiusControls
     * @return array{headline: string, risk: string, first_action: string, verification_focus: string, why_it_matters: string}
     */
    private function executiveSummary(array $input, string $risk, string $coreLesson, array $riskSignals, array $blastRadiusControls): array
    {
        return [
            'headline' => "{$risk} risk: {$input['proxy_layer']} {$input['change_type']} can block {$input['origin_count']} healthy origins.",
            'risk' => $riskSignals[0] ?? 'No major proxy risk signal was detected.',
            'first_action' => $blastRadiusControls[0] ?? 'Limit rollout blast radius before changing proxy behavior.',
            'verification_focus' => $input['has_health_gate']
                ? 'Keep the automated health gate focused on proxy-generated errors, parser errors, CPU, memory, and request reachability.'
                : 'Add an automated health gate before another proxy rollout.',
            'why_it_matters' => $coreLesson,
        ];
    }

    /**
     * Return the practical lesson for the chosen proxy scenario.
     *
     * @param  array{proxy_layer: string, change_type: string, origin_count: int}  $input
     */
    private function coreLesson(array $input): string
    {
        return "Healthy origins do not guarantee availability when every request must pass through a {$input['proxy_layer']} affected by a {$input['change_type']} change.";
    }

    /**
     * Return the request path that must stay healthy before origin servers matter.
     *
     * @return array<int, array{step: string, responsibility: string, failure_effect: string}>
     */
    private function requestPath(string $proxyLayer): array
    {
        return [
            [
                'step' => 'client',
                'responsibility' => 'Sends traffic to the public hostname.',
                'failure_effect' => 'Users see connection failures or stale DNS symptoms before the app is reached.',
            ],
            [
                'step' => $proxyLayer,
                'responsibility' => 'Terminates TLS, applies routing, security, cache, and forwarding rules.',
                'failure_effect' => 'A hot-path proxy failure can return errors before traffic reaches any origin.',
            ],
            [
                'step' => 'load balancer',
                'responsibility' => 'Distributes accepted proxy traffic across healthy upstreams.',
                'failure_effect' => 'It cannot help when the shared proxy path rejects or breaks requests first.',
            ],
            [
                'step' => 'origin servers',
                'responsibility' => 'Serve the application after the proxy and balancer forward the request.',
                'failure_effect' => 'Hundreds of healthy origins still look down if the edge path is broken.',
            ],
        ];
    }

    /**
     * Return signals that determine operational risk.
     *
     * @param  array{rollout_strategy: string, has_health_gate: bool, fail_behavior: string, observed_failure: string, origin_count: int}  $input
     * @return array<int, string>
     */
    private function riskSignals(array $input): array
    {
        $signals = [];

        if ($input['rollout_strategy'] === 'global') {
            $signals[] = 'Global rollout makes a bad proxy change affect the whole request path quickly.';
        }

        if (! $input['has_health_gate']) {
            $signals[] = 'No health gate means bad proxy behavior can keep propagating after errors start.';
        }

        if ($input['fail_behavior'] === 'fail_closed') {
            $signals[] = 'Fail-closed behavior protects security but can make all origins unreachable.';
        }

        if ($input['observed_failure'] === 'http_500') {
            $signals[] = 'HTTP 500 from the proxy layer usually points to edge execution or generated config, not origin capacity.';
        }

        if ($input['origin_count'] >= 20) {
            $signals[] = 'A large origin pool increases capacity, but it does not reduce shared proxy blast radius by itself.';
        }

        return $signals === [] ? ['The scenario is lower risk because rollout, health gates, and failure behavior limit blast radius.'] : $signals;
    }

    /**
     * Return the summarized risk level.
     *
     * @param  array{rollout_strategy: string, has_health_gate: bool, fail_behavior: string, origin_count: int}  $input
     */
    private function riskLevel(array $input): string
    {
        $score = 0;
        $score += $input['rollout_strategy'] === 'global' ? 3 : 0;
        $score += $input['has_health_gate'] ? 0 : 3;
        $score += $input['fail_behavior'] === 'fail_closed' ? 2 : 0;
        $score += $input['origin_count'] >= 20 && ($input['rollout_strategy'] === 'global' || ! $input['has_health_gate']) ? 1 : 0;

        return match (true) {
            $score >= 6 => 'critical',
            $score >= 3 => 'high',
            $score >= 1 => 'medium',
            default => 'controlled',
        };
    }

    /**
     * Return a release-readiness score and the next actions needed before rollout.
     *
     * @param  array{rollout_strategy: string, has_health_gate: bool, fail_behavior: string, observed_failure: string, origin_count: int}  $input
     * @return array{score: int, label: string, blockers: array<int, string>, next_actions: array<int, string>}
     */
    private function readinessScore(array $input): array
    {
        $score = 100;
        $blockers = [];
        $nextActions = [];

        if ($input['rollout_strategy'] === 'global') {
            $score -= 30;
            $blockers[] = 'Global rollout is too broad for a shared proxy request path.';
            $nextActions[] = 'Convert rollout to canary or staged region rollout.';
        }

        if (! $input['has_health_gate']) {
            $score -= 30;
            $blockers[] = 'Automated health gates are missing.';
            $nextActions[] = 'Gate rollout on proxy 5xx, parser errors, CPU, memory, and edge-to-origin smoke checks.';
        }

        if ($input['fail_behavior'] === 'fail_closed') {
            $score -= 15;
            $nextActions[] = 'Document which routes must fail closed and which optional proxy modules can fail small or bypass.';
        }

        if ($input['origin_count'] >= 20 && ($input['rollout_strategy'] === 'global' || ! $input['has_health_gate'])) {
            $score -= 10;
            $nextActions[] = 'Prove public edge-to-origin reachability, not only direct origin health.';
        }

        if ($input['observed_failure'] === 'http_500') {
            $score -= 5;
            $nextActions[] = 'Separate proxy-generated 5xx from origin-generated 5xx in alerts and dashboards.';
        }

        $score = max(0, $score);

        if ($nextActions === []) {
            $nextActions[] = 'Keep canary monitoring, rollback automation, and edge-to-origin smoke checks active during rollout.';
        }

        return [
            'score' => $score,
            'label' => $this->readinessLabel($score),
            'blockers' => $blockers,
            'next_actions' => array_values(array_unique($nextActions)),
        ];
    }

    /**
     * Return a release-readiness label from a numeric score.
     */
    private function readinessLabel(int $score): string
    {
        return match (true) {
            $score >= 85 => 'ready-for-canary',
            $score >= 65 => 'needs-hardening',
            $score >= 40 => 'risky',
            default => 'blocked',
        };
    }

    /**
     * Return blast-radius controls for proxy and edge changes.
     *
     * @param  array{rollout_strategy: string}  $input
     * @return array<int, string>
     */
    private function blastRadiusControls(array $input): array
    {
        return [
            $input['rollout_strategy'] === 'global'
                ? 'Replace global rollout with canary, region-by-region, or customer-cohort rollout before touching the whole edge.'
                : 'Keep staged rollout and define the maximum region, POP, or customer cohort allowed to receive the change first.',
            'Keep generated feature/config files below explicit size, field count, and schema limits.',
            'Separate proxy module health from origin health so an edge failure does not look like app capacity failure.',
            'Keep a one-command rollback path that removes the new proxy data from the hot request path.',
            'Prefer fail-small behavior: isolate the failing rule, dataset, tenant, route, or region instead of failing every request.',
        ];
    }

    /**
     * Return validation checks before a proxy change reaches the request path.
     *
     * @param  array{change_type: string}  $input
     * @return array<int, array{check: string, reason: string}>
     */
    private function configValidationPlan(array $input): array
    {
        return [
            [
                'check' => "Validate {$input['change_type']} schema before publishing.",
                'reason' => 'The proxy should reject malformed fields before the data reaches runtime code.',
            ],
            [
                'check' => 'Enforce maximum file size, array cardinality, generated-rule count, and per-tenant limits.',
                'reason' => 'A syntactically valid file can still break memory, CPU, parsing, or hot-path assumptions.',
            ],
            [
                'check' => 'Run a compatibility check against the proxy parser version deployed at the edge.',
                'reason' => 'Generated data and deployed code can drift during staggered deploys.',
            ],
            [
                'check' => 'Publish with a checksum, version, owner, and rollback pointer.',
                'reason' => 'Incident response needs to know exactly which proxy artifact changed.',
            ],
        ];
    }

    /**
     * Return a rollout plan with health gates and rollback behavior.
     *
     * @param  array{rollout_strategy: string, has_health_gate: bool}  $input
     * @return array<int, string>
     */
    private function rolloutPlan(array $input): array
    {
        return [
            'Start in shadow or dry-run mode when the proxy rule can be evaluated without affecting live requests.',
            $input['rollout_strategy'] === 'canary'
                ? 'Send the change to one small canary slice first and hold until proxy 5xx, latency, and saturation stay normal.'
                : 'Convert the rollout into canary, then staged regions, then broad rollout after each health gate passes.',
            $input['has_health_gate']
                ? 'Keep the rollout blocked by automated health gates on proxy 5xx, parser errors, CPU, memory, and request volume.'
                : 'Add automated health gates before the next rollout; manual dashboards alone are too slow for shared edge risk.',
            'Rollback immediately when proxy-generated errors rise, even if origin servers remain healthy.',
        ];
    }

    /**
     * Return fail-small design choices for proxy failures.
     *
     * @param  array{fail_behavior: string}  $input
     * @return array<int, array{area: string, decision: string}>
     */
    private function failSmallDesign(array $input): array
    {
        return [
            [
                'area' => 'security-sensitive paths',
                'decision' => $input['fail_behavior'] === 'fail_closed'
                    ? 'Fail closed only for routes where bypass would be unsafe, and isolate that decision to the affected rule.'
                    : 'Fail open or bypass only when the route can safely skip the failing proxy feature.',
            ],
            [
                'area' => 'cacheable content',
                'decision' => 'Serve stale content when possible so a proxy feature failure does not become a full outage.',
            ],
            [
                'area' => 'dynamic app traffic',
                'decision' => 'Bypass the failing optional proxy module while preserving TLS, routing, and essential security controls.',
            ],
            [
                'area' => 'tenant or route scope',
                'decision' => 'Disable only the affected tenant, route pattern, region, or rule version instead of removing the whole proxy layer.',
            ],
        ];
    }

    /**
     * Return observability needed to distinguish proxy from origin failure.
     *
     * @param  array{observed_failure: string}  $input
     * @return array<int, array{metric: string, why: string}>
     */
    private function observabilityPlan(array $input): array
    {
        return [
            [
                'metric' => 'proxy_generated_5xx_rate',
                'why' => "Confirms whether {$input['observed_failure']} is produced before the request reaches origins.",
            ],
            [
                'metric' => 'origin_5xx_rate',
                'why' => 'Separates app/server failure from edge proxy failure.',
            ],
            [
                'metric' => 'config_publish_version',
                'why' => 'Correlates incidents with the exact proxy config or generated feature file version.',
            ],
            [
                'metric' => 'edge_cpu_memory_parser_errors',
                'why' => 'Catches oversized or unexpected generated data that stresses the proxy path.',
            ],
            [
                'metric' => 'request_reachability_by_region',
                'why' => 'Shows whether the outage is global, regional, route-specific, or tenant-specific.',
            ],
        ];
    }

    /**
     * Return incident triage steps for reverse-proxy outages.
     *
     * @param  array{observed_failure: string}  $input
     * @return array<int, string>
     */
    private function incidentTriage(array $input): array
    {
        return [
            "Classify whether {$input['observed_failure']} is generated by the proxy, load balancer, or origin application.",
            'Compare error start time with proxy config publish, feature-file generation, deploy, and traffic-shape changes.',
            'Check whether direct origin health is green while public traffic fails through the edge.',
            'Rollback the latest proxy artifact before scaling origins when the failure is edge-generated.',
            'After mitigation, write a postmortem action for validation, staged rollout, health gates, and blast-radius limit.',
        ];
    }

    /**
     * Return checks proving origin health is not enough by itself.
     *
     * @return array<int, string>
     */
    private function originReachabilityChecks(int $originCount): array
    {
        return [
            "Confirm all {$originCount} origins pass direct readiness checks from inside the trusted network.",
            'Confirm public requests still pass through the proxy path before they can use those healthy origins.',
            'Verify the load balancer can receive proxy-forwarded traffic after the failing edge artifact is removed.',
            'Avoid declaring recovery based only on origin health; require public edge-to-origin success.',
        ];
    }

    /**
     * Return an incident decision tree that separates proxy, balancer, and origin action.
     *
     * @param  array{observed_failure: string}  $input
     * @return array<int, array{if: string, then: string, evidence: string}>
     */
    private function decisionTree(array $input): array
    {
        return [
            [
                'if' => "public traffic shows {$input['observed_failure']} while direct origin readiness is healthy",
                'then' => 'rollback or bypass the latest proxy artifact before scaling origins',
                'evidence' => 'proxy_generated_5xx_rate rises while origin_5xx_rate stays normal',
            ],
            [
                'if' => 'proxy parser errors, memory, or CPU spike immediately after config publish',
                'then' => 'block rollout, remove the generated data from the hot path, and enforce size/cardinality limits',
                'evidence' => 'config_publish_version aligns with parser errors or resource saturation',
            ],
            [
                'if' => 'origin 5xx rises after traffic reaches the load balancer',
                'then' => 'investigate app, database, queue, downstream dependency, or origin capacity before changing proxy rules',
                'evidence' => 'origin logs and load-balancer upstream metrics show failures after forwarding',
            ],
            [
                'if' => 'only one region, tenant, route, or rule version fails',
                'then' => 'disable that scope and keep unaffected traffic flowing',
                'evidence' => 'request_reachability_by_region or route-level metrics isolate the failing slice',
            ],
        ];
    }

    /**
     * Return review checklist items for approving proxy-layer changes.
     *
     * @param  array{change_type: string, rollout_strategy: string}  $input
     * @return array<int, array{group: string, checks: array<int, string>}>
     */
    private function reviewChecklist(array $input): array
    {
        return [
            [
                'group' => 'pre-merge',
                'checks' => [
                    "Validate {$input['change_type']} schema, size, generated rule count, and parser compatibility.",
                    'Document owner, rollback pointer, rollout scope, and expected metric movement.',
                    'Prove optional proxy modules can be disabled without breaking TLS, routing, or essential security.',
                ],
            ],
            [
                'group' => 'rollout',
                'checks' => [
                    $input['rollout_strategy'] === 'global'
                        ? 'Replace global rollout with canary or staged rollout before approval.'
                        : 'Confirm canary or staged rollout has a maximum blast-radius limit.',
                    'Stop rollout automatically on proxy 5xx, parser errors, CPU, memory, or reachability regression.',
                    'Keep public edge-to-origin smoke checks separate from direct origin health checks.',
                ],
            ],
            [
                'group' => 'post-incident',
                'checks' => [
                    'Record whether the first bad signal was proxy-generated, load-balancer-generated, or origin-generated.',
                    'Add a regression test or validation rule for the config shape that failed.',
                    'Update runbooks so responders rollback edge artifacts before scaling healthy origins when proxy errors dominate.',
                ],
            ],
        ];
    }

    /**
     * Return scenario-specific proxy change guidance.
     *
     * @param  array{change_type: string, observed_failure: string}  $input
     * @return array{change_type: string, likely_failure_modes: array<int, string>, validation_focus: array<int, string>, rollback_trigger: string, owner_question: string}
     */
    private function scenarioPlaybook(array $input): array
    {
        $playbooks = [
            'feature-file' => [
                'likely_failure_modes' => [
                    'Generated file exceeds parser, memory, CPU, cardinality, or hot-path timing assumptions.',
                    'A new field shape is valid JSON but incompatible with the deployed proxy module.',
                    'Feature data propagates globally before health signals can stop it.',
                ],
                'validation_focus' => [
                    'Schema, maximum byte size, item count, nested depth, and per-tenant cardinality.',
                    'Compatibility against the oldest proxy parser version still serving traffic.',
                    'Shadow evaluation against sampled production-shaped requests before live enforcement.',
                ],
                'rollback_trigger' => 'Rollback when proxy parser errors, memory, CPU, or proxy-generated 5xx move after feature-file publish.',
                'owner_question' => 'Who owns generation, validation, publish, and rollback for this feature file?',
            ],
            'config-file' => [
                'likely_failure_modes' => [
                    'A config reload succeeds syntactically but changes routing, timeout, header, cache, or TLS behavior unexpectedly.',
                    'A config version reaches some proxy nodes but not others, creating inconsistent request behavior.',
                    'A default value changes behavior for routes or tenants that were not part of the intended rollout.',
                ],
                'validation_focus' => [
                    'Diff rendered config, not only source templates.',
                    'Check route match order, header forwarding, timeout values, cache rules, and TLS settings.',
                    'Run config test and edge smoke checks before broad rollout.',
                ],
                'rollback_trigger' => 'Rollback when route-level reachability, TLS handshakes, header correctness, or proxy 5xx regresses after config publish.',
                'owner_question' => 'Who can approve a config rollback when security, platform, and application owners disagree?',
            ],
            'routing-rule' => [
                'likely_failure_modes' => [
                    'A broad route pattern catches traffic intended for another service.',
                    'A regional or tenant route sends traffic to the wrong origin pool.',
                    'Route priority changes make healthy origins unreachable through the public path.',
                ],
                'validation_focus' => [
                    'Route specificity, priority, tenant scope, region scope, and fallback behavior.',
                    'Replay representative URLs through the router before deployment.',
                    'Verify each route reaches the expected origin pool and health check path.',
                ],
                'rollback_trigger' => 'Rollback or disable the rule when route-level bad_routing, 404, 502, or unexpected origin selection appears.',
                'owner_question' => 'Who owns route priority and who confirms the intended origin pool for each affected route?',
            ],
            'waf-rule' => [
                'likely_failure_modes' => [
                    'A WAF rule blocks legitimate traffic because a pattern is too broad.',
                    'Fail-closed behavior turns a rule bug into an availability incident.',
                    'The rule detects malicious-looking payloads without enough route, tenant, or method scope.',
                ],
                'validation_focus' => [
                    'False-positive rate, route scope, HTTP method scope, bypass procedure, and security severity.',
                    'Dry-run mode against recent traffic before enforcement.',
                    'Separate block, challenge, log-only, and bypass actions by risk.',
                ],
                'rollback_trigger' => 'Rollback or move to log-only when legitimate traffic is blocked or bad_routing appears after WAF enforcement.',
                'owner_question' => 'Who can downgrade a WAF rule from block to log-only during an availability incident?',
            ],
            'deploy' => [
                'likely_failure_modes' => [
                    'New proxy code cannot parse existing config or generated data.',
                    'A hot-path module changes timeout, retry, cache, header, or TLS behavior.',
                    'The deploy rolls forward while old and new proxy versions disagree on runtime contracts.',
                ],
                'validation_focus' => [
                    'Backward compatibility with current config and generated data.',
                    'Canary deploy with proxy 5xx, latency, CPU, memory, and parser-error gates.',
                    'Fast rollback and version skew testing between proxy code and data artifacts.',
                ],
                'rollback_trigger' => 'Rollback when proxy-generated errors, parser errors, latency, or resource saturation moves with the deploy version.',
                'owner_question' => 'Who owns deploy rollback when the proxy code is healthy in staging but fails against production-shaped traffic?',
            ],
        ];

        return [
            'change_type' => $input['change_type'],
            ...$playbooks[$input['change_type']],
        ];
    }

    /**
     * Return concrete simulation steps that prove edge and origin reachability separately.
     *
     * @param  array{service_name: string, proxy_layer: string, change_type: string, observed_failure: string}  $input
     * @return array<int, array{step: string, command: string, expected_signal: string}>
     */
    private function simulationPlan(array $input): array
    {
        $service = Str::kebab($input['service_name']);

        return [
            [
                'step' => 'baseline-public-path',
                'command' => "curl -sS -o /dev/null -w '%{http_code} %{time_total}\\n' https://{$service}.example.test/health",
                'expected_signal' => 'Public edge-to-origin health returns 2xx with normal latency before the proxy change.',
            ],
            [
                'step' => 'direct-origin-control',
                'command' => "curl -sS -H 'Host: {$service}.example.test' http://origin-1.internal/health",
                'expected_signal' => 'Direct origin readiness stays healthy so the test can separate origin health from proxy reachability.',
            ],
            [
                'step' => 'shadow-proxy-change',
                'command' => "proxyctl validate --layer={$input['proxy_layer']} --type={$input['change_type']} --shadow --sample=production-shape",
                'expected_signal' => 'Parser, schema, size, route, and policy checks pass without changing live request handling.',
            ],
            [
                'step' => 'canary-public-path',
                'command' => "proxyctl rollout --layer={$input['proxy_layer']} --scope=canary --watch=proxy_5xx,parser_errors,edge_cpu,edge_memory",
                'expected_signal' => "The canary does not increase {$input['observed_failure']}, proxy-generated 5xx, parser errors, CPU, or memory.",
            ],
            [
                'step' => 'rollback-proof',
                'command' => "proxyctl rollback --layer={$input['proxy_layer']} --last-artifact && curl -sS https://{$service}.example.test/health",
                'expected_signal' => 'Removing the latest proxy artifact restores public edge-to-origin reachability.',
            ],
        ];
    }

    /**
     * Return a controlled drill for practicing proxy failure response.
     *
     * @param  array{change_type: string, fail_behavior: string}  $input
     * @return array{objective: string, inject: string, guardrails: array<int, string>, success_criteria: array<int, string>}
     */
    private function chaosDrill(array $input): array
    {
        return [
            'objective' => 'Practice proving whether an outage is generated by the proxy layer before scaling or debugging healthy origins.',
            'inject' => "Introduce a staged {$input['change_type']} artifact that triggers {$input['fail_behavior']} for a small canary route.",
            'guardrails' => [
                'Run only in staging or a production canary slice with explicit approval.',
                'Keep rollback automation ready before injecting the failure.',
                'Stop immediately on customer-impacting errors outside the approved blast radius.',
            ],
            'success_criteria' => [
                'Alert separates proxy-generated errors from origin-generated errors.',
                'Responder rolls back or bypasses the proxy artifact before scaling origins.',
                'Public edge-to-origin smoke check proves recovery.',
                'A new validation rule or regression test is added after the drill.',
            ],
        ];
    }

    /**
     * Return health-gate thresholds that decide whether rollout can continue.
     *
     * @param  array{has_health_gate: bool, rollout_strategy: string, observed_failure: string}  $input
     * @return array{mode: string, stop_conditions: array<int, string>, promote_conditions: array<int, string>, rollback_conditions: array<int, string>}
     */
    private function healthGatePolicy(array $input): array
    {
        return [
            'mode' => $input['has_health_gate'] ? 'automated' : 'required-before-rollout',
            'stop_conditions' => [
                "Any increase in {$input['observed_failure']} above baseline during canary.",
                'proxy_generated_5xx_rate exceeds origin_5xx_rate or rises for two consecutive checks.',
                'parser_errors, edge_cpu, or edge_memory moves after the proxy artifact is published.',
                'edge-to-origin smoke check fails while direct origin readiness remains healthy.',
            ],
            'promote_conditions' => [
                $input['rollout_strategy'] === 'canary'
                    ? 'Canary stays within baseline for proxy 5xx, latency, CPU, memory, parser errors, and reachability.'
                    : 'Staged rollout passes the same health gate for each region, POP, tenant, or route cohort.',
                'No route, tenant, or region slice shows a worse error rate than the control group.',
                'Rollback command has been tested against the current artifact version.',
            ],
            'rollback_conditions' => [
                'Proxy-generated errors rise while direct origin readiness is green.',
                'Config publish version correlates with parser errors, resource saturation, or reachability loss.',
                'Canary failure leaks outside the approved blast radius.',
            ],
        ];
    }

    /**
     * Return alert rules that separate edge and origin failure during rollout.
     *
     * @param  array{proxy_layer: string, observed_failure: string}  $input
     * @return array<int, array{name: string, signal: string, action: string}>
     */
    private function alertRules(array $input): array
    {
        return [
            [
                'name' => "{$input['proxy_layer']}.proxy-generated-errors",
                'signal' => "proxy_generated_5xx_rate or {$input['observed_failure']} rises above rollout baseline",
                'action' => 'Stop rollout and compare with origin_5xx_rate before scaling origins.',
            ],
            [
                'name' => "{$input['proxy_layer']}.artifact-parser-health",
                'signal' => 'parser_errors, artifact_load_failures, edge_cpu, or edge_memory rises after config publish',
                'action' => 'Rollback the artifact and inspect schema, size, cardinality, and parser compatibility.',
            ],
            [
                'name' => "{$input['proxy_layer']}.reachability-split-brain",
                'signal' => 'direct origin readiness is healthy but public edge-to-origin smoke checks fail',
                'action' => 'Treat the proxy path as suspect and bypass or rollback the latest proxy change.',
            ],
            [
                'name' => "{$input['proxy_layer']}.blast-radius-leak",
                'signal' => 'errors appear outside the approved canary, region, tenant, route, or cohort',
                'action' => 'Stop rollout, disable the scoped rule, and verify routing boundaries.',
            ],
        ];
    }

    /**
     * Return ownership boundaries for proxy failure prevention and response.
     *
     * @param  array{proxy_layer: string, change_type: string}  $input
     * @return array<int, array{owner: string, responsibility: string, evidence: string}>
     */
    private function ownershipMatrix(array $input): array
    {
        return [
            [
                'owner' => 'platform-edge',
                'responsibility' => "Own {$input['proxy_layer']} rollout, rollback, health gates, and blast-radius controls.",
                'evidence' => 'Approved rollout record, rollback artifact, health-gate result, and edge-to-origin smoke check.',
            ],
            [
                'owner' => 'config-or-feature-owner',
                'responsibility' => "Own {$input['change_type']} generation, schema, size limits, compatibility, and expected behavior.",
                'evidence' => 'Schema validation report, generated artifact diff, compatibility check, and owner approval.',
            ],
            [
                'owner' => 'service-owner',
                'responsibility' => 'Own direct origin readiness, app-level logs, and confirmation that origins were healthy or unhealthy.',
                'evidence' => 'Origin readiness checks, app logs, upstream 5xx metrics, and dependency status.',
            ],
            [
                'owner' => 'incident-commander',
                'responsibility' => 'Decide whether to rollback proxy artifact, bypass optional module, or investigate origins based on evidence.',
                'evidence' => 'Timeline, decision log, first bad signal, mitigation timestamp, and recovery proof.',
            ],
        ];
    }

    /**
     * Return postmortem follow-up actions that convert the incident into durable controls.
     *
     * @param  array{change_type: string, observed_failure: string}  $input
     * @return array<int, array{action: string, owner: string, done_when: string}>
     */
    private function postmortemActions(array $input): array
    {
        return [
            [
                'action' => "Add a regression validator for {$input['change_type']} artifacts that could trigger {$input['observed_failure']}.",
                'owner' => 'config-or-feature-owner',
                'done_when' => 'The validator fails before publish when the same bad shape, size, route, or parser condition appears.',
            ],
            [
                'action' => 'Add a public edge-to-origin smoke check to the rollout gate.',
                'owner' => 'platform-edge',
                'done_when' => 'The gate blocks rollout when public traffic fails but direct origin readiness remains healthy.',
            ],
            [
                'action' => 'Update the incident runbook with proxy-first rollback criteria.',
                'owner' => 'incident-commander',
                'done_when' => 'Responders can distinguish proxy-generated errors from origin-generated errors in the first five minutes.',
            ],
            [
                'action' => 'Run one controlled drill that proves rollback and alert routing.',
                'owner' => 'platform-edge',
                'done_when' => 'The drill records detection time, rollback time, and recovery proof without leaving the approved blast radius.',
            ],
        ];
    }

    /**
     * Return a time-boxed response timeline for reverse-proxy incidents.
     *
     * @param  array{observed_failure: string, proxy_layer: string, change_type: string}  $input
     * @return array<int, array{window: string, goal: string, actions: array<int, string>, exit_criteria: string}>
     */
    private function incidentTimeline(array $input): array
    {
        return [
            [
                'window' => '0-5 minutes',
                'goal' => 'Classify whether the first bad signal is proxy-generated or origin-generated.',
                'actions' => [
                    "Compare {$input['observed_failure']} against proxy_generated_5xx_rate and origin_5xx_rate.",
                    'Check direct origin readiness and public edge-to-origin smoke checks side by side.',
                    "Correlate start time with {$input['change_type']} publish, deploy, or routing events.",
                ],
                'exit_criteria' => 'Incident commander knows whether to rollback proxy artifact or investigate origin service.',
            ],
            [
                'window' => '5-15 minutes',
                'goal' => 'Mitigate the shared request path before changing healthy origins.',
                'actions' => [
                    "Rollback or bypass the latest {$input['proxy_layer']} artifact when proxy-generated errors dominate.",
                    'Freeze rollout and stop propagation outside the approved blast radius.',
                    'Keep status updates focused on user reachability, rollback progress, and recovery proof.',
                ],
                'exit_criteria' => 'Public edge-to-origin success improves or the team has evidence that origin failure is primary.',
            ],
            [
                'window' => '15-60 minutes',
                'goal' => 'Prove recovery and prevent repeated bad rollout.',
                'actions' => [
                    'Verify public traffic by region, tenant, route, and canary cohort.',
                    'Confirm direct origin health and load-balancer upstream metrics remain normal.',
                    'Disable automatic re-publish of the bad artifact until validation is fixed.',
                ],
                'exit_criteria' => 'Recovery is proven by public edge-to-origin checks, not only by origin readiness.',
            ],
            [
                'window' => 'follow-up',
                'goal' => 'Convert the incident into validation, ownership, and drill improvements.',
                'actions' => [
                    'Add regression validation for the failed artifact shape.',
                    'Update rollback criteria and ownership matrix in the runbook.',
                    'Schedule a controlled drill to verify alert routing and rollback time.',
                ],
                'exit_criteria' => 'Postmortem actions have owners, done criteria, and verification evidence.',
            ],
        ];
    }

    /**
     * Return evidence artifacts needed to prove cause, mitigation, and recovery.
     *
     * @param  array{proxy_layer: string, change_type: string, observed_failure: string}  $input
     * @return array<int, array{artifact: string, owner: string, proves: string, retention: string}>
     */
    private function evidencePack(array $input): array
    {
        return [
            [
                'artifact' => "{$input['proxy_layer']} rollout record for the latest {$input['change_type']} artifact",
                'owner' => 'platform-edge',
                'proves' => 'Which artifact version changed, when it propagated, and whether rollout scope matched the approved blast radius.',
                'retention' => 'Attach to incident record and keep with postmortem evidence.',
            ],
            [
                'artifact' => 'proxy-generated error and parser-error graphs',
                'owner' => 'platform-edge',
                'proves' => "Whether {$input['observed_failure']} started before requests reached origin servers.",
                'retention' => 'Export dashboard snapshot covering at least 30 minutes before and after mitigation.',
            ],
            [
                'artifact' => 'direct origin readiness and upstream 5xx graphs',
                'owner' => 'service-owner',
                'proves' => 'Whether origins were healthy while public traffic failed through the proxy path.',
                'retention' => 'Attach readiness logs and load-balancer upstream metrics to the timeline.',
            ],
            [
                'artifact' => 'edge-to-origin smoke check before and after rollback',
                'owner' => 'incident-commander',
                'proves' => 'Recovery was public-path recovery, not only direct origin recovery.',
                'retention' => 'Store command output, timestamp, region, route, and artifact version.',
            ],
            [
                'artifact' => "validation report for {$input['change_type']} schema, size, cardinality, and compatibility",
                'owner' => 'config-or-feature-owner',
                'proves' => 'The failed shape is now blocked before publish.',
                'retention' => 'Keep with the regression test or validator change.',
            ],
        ];
    }

    /**
     * Return durable lessons from edge outages without overfitting to one vendor.
     *
     * @return array<int, array{lesson: string, practice: string}>
     */
    private function edgeOutageLessonMap(): array
    {
        return [
            [
                'lesson' => 'Generated data is production input.',
                'practice' => 'Validate generated files with schema, size, cardinality, compatibility, and rollback metadata.',
            ],
            [
                'lesson' => 'A shared request path has shared blast radius.',
                'practice' => 'Stage rollout by region, route, tenant, or cohort and stop automatically on edge health regression.',
            ],
            [
                'lesson' => 'Healthy origins do not prove public availability.',
                'practice' => 'Require public edge-to-origin checks before declaring recovery.',
            ],
            [
                'lesson' => 'Global 5xx symptoms are not automatically a DDoS.',
                'practice' => 'Correlate error start time with config publish, deploy, traffic, and dependency events before choosing mitigation.',
            ],
        ];
    }

    /**
     * Return a copy-ready incident memo for proxy failure reviews.
     *
     * @param  array{proxy_layer: string, change_type: string, origin_count: int, rollout_strategy: string, has_health_gate: bool, fail_behavior: string, observed_failure: string}  $input
     * @param  array{score: int, label: string, blockers: array<int, string>, next_actions: array<int, string>}  $readinessScore
     * @param  array<int, string>  $riskSignals
     * @param  array<int, string>  $blastRadiusControls
     * @param  array<int, string>  $rolloutPlan
     * @param  array<int, array{metric: string, why: string}>  $observabilityPlan
     * @param  array<int, string>  $incidentTriage
     * @param  array<int, string>  $originReachabilityChecks
     * @param  array<int, array{if: string, then: string, evidence: string}>  $decisionTree
     * @param  array{change_type: string, likely_failure_modes: array<int, string>, validation_focus: array<int, string>, rollback_trigger: string, owner_question: string}  $scenarioPlaybook
     * @param  array<int, array{step: string, command: string, expected_signal: string}>  $simulationPlan
     * @param  array{mode: string, stop_conditions: array<int, string>, promote_conditions: array<int, string>, rollback_conditions: array<int, string>}  $healthGatePolicy
     * @param  array<int, array{owner: string, responsibility: string, evidence: string}>  $ownershipMatrix
     * @param  array<int, array{action: string, owner: string, done_when: string}>  $postmortemActions
     * @param  array<int, array{window: string, goal: string, actions: array<int, string>, exit_criteria: string}>  $incidentTimeline
     * @param  array<int, array{artifact: string, owner: string, proves: string, retention: string}>  $evidencePack
     */
    private function incidentMemoMarkdown(
        string $service,
        array $input,
        string $risk,
        array $readinessScore,
        string $coreLesson,
        array $riskSignals,
        array $blastRadiusControls,
        array $rolloutPlan,
        array $observabilityPlan,
        array $incidentTriage,
        array $originReachabilityChecks,
        array $decisionTree,
        array $scenarioPlaybook,
        array $simulationPlan,
        array $healthGatePolicy,
        array $ownershipMatrix,
        array $postmortemActions,
        array $incidentTimeline,
        array $evidencePack,
    ): string {
        $healthGate = $input['has_health_gate'] ? 'present' : 'missing';
        $topRiskSignals = array_pad($riskSignals, 2, 'No additional major risk signal was detected.');
        $readinessActions = array_pad($readinessScore['next_actions'], 2, 'Keep rollback evidence and owner approval attached to the rollout record.');
        $metrics = collect($observabilityPlan)
            ->pluck('metric')
            ->implode(', ');

        return <<<MARKDOWN
# Reverse Proxy Failure Plan: {$service}

## Decision
- Risk level: {$risk}
- Readiness: {$readinessScore['label']} ({$readinessScore['score']}/100)
- Proxy layer: {$input['proxy_layer']}
- Change type: {$input['change_type']}
- Origin count: {$input['origin_count']}
- Rollout strategy: {$input['rollout_strategy']}
- Health gate: {$healthGate}
- Fail behavior: {$input['fail_behavior']}
- Observed failure: {$input['observed_failure']}

## Core Lesson
{$coreLesson}

## Top Risk Signals
- {$topRiskSignals[0]}
- {$topRiskSignals[1]}

## Blast-Radius Controls
- {$blastRadiusControls[0]}
- {$blastRadiusControls[1]}
- {$blastRadiusControls[3]}

## Rollout And Rollback
- {$rolloutPlan[1]}
- {$rolloutPlan[2]}
- {$rolloutPlan[3]}

## Health Gate
- Mode: {$healthGatePolicy['mode']}
- Stop: {$healthGatePolicy['stop_conditions'][0]}
- Rollback: {$healthGatePolicy['rollback_conditions'][0]}

## Ownership
- {$ownershipMatrix[0]['owner']}: {$ownershipMatrix[0]['responsibility']}
- {$ownershipMatrix[3]['owner']}: {$ownershipMatrix[3]['responsibility']}

## Postmortem Actions
- {$postmortemActions[0]['action']}
- {$postmortemActions[1]['action']}

## Readiness Actions
- {$readinessActions[0]}
- {$readinessActions[1]}

## Observability
Watch: {$metrics}.

## Triage
- {$incidentTriage[0]}
- {$incidentTriage[2]}
- {$incidentTriage[3]}

## Incident Timeline
- {$incidentTimeline[0]['window']}: {$incidentTimeline[0]['goal']}
- {$incidentTimeline[1]['window']}: {$incidentTimeline[1]['goal']}

## Evidence Pack
- {$evidencePack[0]['artifact']}: {$evidencePack[0]['proves']}
- {$evidencePack[3]['artifact']}: {$evidencePack[3]['proves']}

## Decision Tree
- If {$decisionTree[0]['if']}, then {$decisionTree[0]['then']}.
- If {$decisionTree[2]['if']}, then {$decisionTree[2]['then']}.

## Scenario Playbook
- Likely failure: {$scenarioPlaybook['likely_failure_modes'][0]}
- Validate: {$scenarioPlaybook['validation_focus'][0]}
- Rollback trigger: {$scenarioPlaybook['rollback_trigger']}

## Simulation
- {$simulationPlan[0]['step']}: {$simulationPlan[0]['expected_signal']}
- {$simulationPlan[4]['step']}: {$simulationPlan[4]['expected_signal']}

## Recovery Proof
- {$originReachabilityChecks[0]}
- {$originReachabilityChecks[3]}
MARKDOWN;
    }

    /**
     * Return configuration review questions for the change.
     *
     * @param  array{change_type: string, proxy_layer: string}  $input
     * @return array<int, array{area: string, question: string}>
     */
    private function configurationReview(array $input): array
    {
        return [
            [
                'area' => 'ownership',
                'question' => "Who owns the {$input['change_type']} artifact and who can approve rollback for {$input['proxy_layer']}?",
            ],
            [
                'area' => 'limits',
                'question' => 'What are the enforced schema, size, cardinality, and generated-rule limits?',
            ],
            [
                'area' => 'runtime isolation',
                'question' => 'Can the failing proxy feature be disabled without disabling TLS, routing, or essential security?',
            ],
            [
                'area' => 'health gates',
                'question' => 'Which automated signals stop rollout before the change reaches the whole edge?',
            ],
        ];
    }

    /**
     * Return an interview-ready answer for the selected scenario.
     *
     * @param  array{proxy_layer: string, change_type: string, origin_count: int, has_health_gate: bool}  $input
     */
    private function interviewAnswer(array $input, string $risk): string
    {
        $healthGate = $input['has_health_gate'] ? 'with automated health gates' : 'but I would add automated health gates before rollout';

        return "A reverse proxy is part of availability, not just a routing detail. In this {$risk}-risk scenario, {$input['origin_count']} healthy origins can still be unreachable because traffic must pass through the {$input['proxy_layer']} first. I would validate the {$input['change_type']} shape and size, roll it out in stages {$healthGate}, watch proxy-generated errors separately from origin errors, and keep rollback or fail-small controls ready.";
    }
}
