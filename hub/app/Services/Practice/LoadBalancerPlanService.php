<?php

declare(strict_types=1);

namespace App\Services\Practice;

use Illuminate\Support\Str;

final class LoadBalancerPlanService
{
    /**
     * Build a practical load-balancer plan for system-design interview practice.
     *
     * @param  array{service_name: string, traffic_pattern: string, algorithm: string, upstream_count: int, has_sticky_sessions: bool, health_check_path: string}  $input
     * @return array{service: string, recommended_algorithm: string, reason: string, risk_assessment: array{level: string, reasons: array<int, string>, mitigations: array<int, string>}, algorithm_catalog: array<int, array{name: string, best_for: string, risk: string}>, decision_matrix: array<int, array{signal: string, value: string, impact: string}>, capacity_plan: array<int, array{upstream: string, weight: int, expected_share: string, note: string}>, observability_metrics: array<int, array{metric: string, why: string, warning: string}>, incident_runbook: array<int, string>, simulation_steps: array<int, array{step: string, action: string, expected_signal: string}>, configuration_review: array<int, array{area: string, check: string, risk: string}>, nginx_example: string, interview_answer: string, interview_rubric: array{definition: string, algorithm_choice: string, production_guardrail: string, tradeoff: string}, rollout_checks: array<int, string>, failure_modes: array<int, string>, tests: array<int, string>, commands: array<int, string>}
     */
    public function plan(array $input): array
    {
        $service = Str::studly($input['service_name']);
        $algorithm = $this->algorithm($input);

        return [
            'service' => $service,
            'recommended_algorithm' => $algorithm,
            'reason' => $this->reason($algorithm, $input['traffic_pattern'], $input['has_sticky_sessions']),
            'risk_assessment' => $this->riskAssessment($algorithm, $input['traffic_pattern'], $input['upstream_count'], $input['has_sticky_sessions']),
            'algorithm_catalog' => $this->algorithmCatalog(),
            'decision_matrix' => $this->decisionMatrix($input),
            'capacity_plan' => $this->capacityPlan($algorithm, $input['upstream_count']),
            'observability_metrics' => $this->observabilityMetrics($algorithm),
            'incident_runbook' => $this->incidentRunbook($algorithm),
            'simulation_steps' => $this->simulationSteps($service, $algorithm, $input['upstream_count'], $input['health_check_path']),
            'configuration_review' => $this->configurationReview($algorithm, $input['has_sticky_sessions'], $input['health_check_path']),
            'nginx_example' => $this->nginxExample($service, $algorithm, $input['upstream_count'], $input['health_check_path']),
            'interview_answer' => $this->interviewAnswer($algorithm),
            'interview_rubric' => $this->interviewRubric($algorithm),
            'rollout_checks' => [
                'Start with a health check that removes unhealthy upstreams before routing traffic to them.',
                'Watch latency, error rate, saturation, and upstream distribution during rollout.',
                'Keep session state out of local memory when possible; use Redis, database, or stateless tokens.',
                'Document how to drain one upstream before deploy or maintenance.',
            ],
            'failure_modes' => [
                'A dead upstream still receives traffic because health checks are missing or too weak.',
                'Sticky sessions hide uneven load and make deploys harder when app state is stored locally.',
                'Least-connections can still overload a slow upstream if latency and capacity are not observed.',
                'Weighted routing becomes risky when weights are changed without metrics or rollback notes.',
            ],
            'tests' => [
                "Send repeated requests and verify traffic reaches {$input['upstream_count']} upstreams as expected.",
                "Stop one {$service} upstream and verify the load balancer stops routing to it after the health check fails.",
                'Verify response headers, client IP forwarding, timeout behavior, and retry policy behind the balancer.',
                'Run a short spike test and compare latency distribution between upstreams.',
            ],
            'commands' => [
                'php artisan test --filter LoadBalancerPlanWorkbenchTest',
                'php artisan route:list --path=load-balancer-plan',
                'vendor\\bin\\pint --test',
            ],
        ];
    }

    /**
     * Return an operational risk assessment for the selected balancer design.
     *
     * @return array{level: string, reasons: array<int, string>, mitigations: array<int, string>}
     */
    private function riskAssessment(string $algorithm, string $trafficPattern, int $upstreamCount, bool $hasStickySessions): array
    {
        $reasons = [];

        if ($upstreamCount < 3) {
            $reasons[] = 'Only two upstreams leaves less room for maintenance, draining, and failure recovery.';
        }

        if ($algorithm === 'ip_hash' || $hasStickySessions) {
            $reasons[] = 'Session affinity can create uneven traffic and makes failover harder.';
        }

        if ($algorithm === 'weighted_round_robin' || $trafficPattern === 'heterogeneous') {
            $reasons[] = 'Unequal capacity requires accurate weights and metric review after changes.';
        }

        if ($algorithm === 'least_connections' || $trafficPattern === 'bursty') {
            $reasons[] = 'Long-running requests can hide saturation if connection count is the only signal.';
        }

        if ($reasons === []) {
            $reasons[] = 'The design is a normal baseline as long as upstream health and latency remain similar.';
        }

        return [
            'level' => $this->riskLevel($algorithm, $upstreamCount, $hasStickySessions),
            'reasons' => $reasons,
            'mitigations' => [
                'Keep health checks strict enough to remove broken upstreams quickly.',
                'Track traffic share, latency, and 5xx rate by upstream during every routing change.',
                'Practice draining and restoring one upstream before using the plan in production.',
                'Move session state out of local memory before relying on horizontal scale.',
            ],
        ];
    }

    /**
     * Return a concise risk level for the selected plan.
     */
    private function riskLevel(string $algorithm, int $upstreamCount, bool $hasStickySessions): string
    {
        if ($algorithm === 'ip_hash' || $hasStickySessions) {
            return 'high';
        }

        if ($upstreamCount < 3 || in_array($algorithm, ['weighted_round_robin', 'least_connections'], true)) {
            return 'medium';
        }

        return 'normal';
    }

    /**
     * Return configuration review checks before promoting a balancer change.
     *
     * @return array<int, array{area: string, check: string, risk: string}>
     */
    private function configurationReview(string $algorithm, bool $hasStickySessions, string $healthCheckPath): array
    {
        return [
            [
                'area' => 'forwarded headers',
                'check' => 'Forward Host, X-Forwarded-For, and X-Forwarded-Proto consistently to the application.',
                'risk' => 'Wrong headers can break URL generation, HTTPS detection, logs, rate limiting, and trusted proxy behavior.',
            ],
            [
                'area' => 'health check',
                'check' => "Use {$healthCheckPath} as a lightweight readiness signal and remove upstreams that fail it.",
                'risk' => 'Weak health checks keep sending users to broken upstreams.',
            ],
            [
                'area' => 'timeouts',
                'check' => 'Set connect, read, and upstream timeout values deliberately for this traffic pattern.',
                'risk' => 'Timeouts that are too short cause false failures; timeouts that are too long hold connections and hide saturation.',
            ],
            [
                'area' => 'session state',
                'check' => $hasStickySessions || $algorithm === 'ip_hash'
                    ? 'Document why affinity is needed and plan migration toward shared session storage.'
                    : 'Confirm requests can be served by any healthy upstream without local session state.',
                'risk' => 'Local-only session state makes failover, deploys, and horizontal scale fragile.',
            ],
            [
                'area' => 'draining',
                'check' => 'Define how to drain one upstream before deploy, restart, or maintenance.',
                'risk' => 'Killing an upstream without draining can interrupt in-flight requests and distort incident signals.',
            ],
        ];
    }

    /**
     * Return a hands-on simulation loop for proving the load-balancer choice.
     *
     * @return array<int, array{step: string, action: string, expected_signal: string}>
     */
    private function simulationSteps(string $service, string $algorithm, int $upstreamCount, string $healthCheckPath): array
    {
        return [
            [
                'step' => 'baseline',
                'action' => 'Send at least '.($upstreamCount * 10).' requests through the balancer before changing any upstream.',
                'expected_signal' => $this->baselineSignal($algorithm),
            ],
            [
                'step' => 'health-check-failure',
                'action' => "Make one {$service} upstream fail {$healthCheckPath}, then keep sending traffic.",
                'expected_signal' => 'The failed upstream should leave rotation after the health check fails.',
            ],
            [
                'step' => 'recovery',
                'action' => 'Restore the failed upstream and reload or wait for the balancer to mark it healthy again.',
                'expected_signal' => 'Traffic should return gradually without a spike in 5xx responses.',
            ],
            [
                'step' => 'algorithm-specific-check',
                'action' => $this->algorithmSimulationAction($algorithm),
                'expected_signal' => $this->algorithmSimulationSignal($algorithm),
            ],
        ];
    }

    /**
     * Return the baseline signal to watch for a selected algorithm.
     */
    private function baselineSignal(string $algorithm): string
    {
        return match ($algorithm) {
            'weighted_round_robin' => 'Request share should roughly follow configured weights.',
            'least_connections' => 'Busy upstreams should receive fewer new requests while active connections stay high.',
            'ip_hash' => 'The same client IP should keep returning to the same upstream until health changes.',
            default => 'Requests should be close to evenly distributed across healthy upstreams.',
        };
    }

    /**
     * Return the hands-on action for the selected algorithm.
     */
    private function algorithmSimulationAction(string $algorithm): string
    {
        return match ($algorithm) {
            'weighted_round_robin' => 'Change one upstream weight in a staging config and compare request share before and after reload.',
            'least_connections' => 'Create several slow requests against one upstream and observe whether new requests prefer less busy upstreams.',
            'ip_hash' => 'Send requests from two different client IP sources and compare upstream affinity.',
            default => 'Temporarily add one extra equal upstream and verify distribution rebalances after reload.',
        };
    }

    /**
     * Return the expected signal for the algorithm-specific simulation.
     */
    private function algorithmSimulationSignal(string $algorithm): string
    {
        return match ($algorithm) {
            'weighted_round_robin' => 'The observed traffic split should move toward the new weight ratio.',
            'least_connections' => 'New requests should avoid the upstream holding slow connections.',
            'ip_hash' => 'Affinity should remain stable per client IP, but distribution may be uneven.',
            default => 'The new upstream should receive a similar share once it is healthy.',
        };
    }

    /**
     * Return first-response steps for load-balancer incidents.
     *
     * @return array<int, string>
     */
    private function incidentRunbook(string $algorithm): array
    {
        return array_merge([
            'Confirm whether the issue is global or isolated to one upstream by checking status, latency, and 5xx rate by node.',
            'Drain or remove the unhealthy upstream before changing application code.',
            'Verify forwarded headers, timeout settings, and health-check behavior before declaring the app layer broken.',
        ], match ($algorithm) {
            'weighted_round_robin' => [
                'Compare actual request share with configured weights before changing capacity assumptions.',
                'Temporarily lower the weight of a stressed upstream instead of fully removing it if partial traffic is still safe.',
            ],
            'least_connections' => [
                'Check whether long-lived requests are holding connections open and starving new traffic.',
                'Pair connection metrics with latency and CPU before increasing upstream count.',
            ],
            'ip_hash' => [
                'Check for hot client IP ranges that pin too much traffic to one upstream.',
                'Move session state to shared storage before removing affinity if users depend on sticky sessions.',
            ],
            default => [
                'Check whether one upstream is weaker even though round robin assumes similar capacity.',
                'Move to weighted routing only after metrics prove capacity is not equal.',
            ],
        });
    }

    /**
     * Return a structured rubric for answering load-balancer interview questions.
     *
     * @return array{definition: string, algorithm_choice: string, production_guardrail: string, tradeoff: string}
     */
    private function interviewRubric(string $algorithm): array
    {
        return [
            'definition' => 'A load balancer distributes requests across healthy upstreams to improve capacity, availability, and rollout control.',
            'algorithm_choice' => $this->algorithmChoiceSentence($algorithm),
            'production_guardrail' => 'The algorithm must be paired with health checks, forwarded headers, timeout policy, and metrics by upstream.',
            'tradeoff' => $this->algorithmTradeoffSentence($algorithm),
        ];
    }

    /**
     * Return one interview sentence for why the selected algorithm fits.
     */
    private function algorithmChoiceSentence(string $algorithm): string
    {
        return match ($algorithm) {
            'weighted_round_robin' => 'I choose weighted round robin when upstreams have different capacity and traffic should follow explicit weights.',
            'least_connections' => 'I choose least connections when request duration varies and active connection count is a better signal than simple rotation.',
            'ip_hash' => 'I choose IP hash only when the system temporarily needs session affinity to the same upstream.',
            default => 'I choose round robin when upstreams are similar and traffic is even enough for simple rotation.',
        };
    }

    /**
     * Return one interview sentence for the selected algorithm risk.
     */
    private function algorithmTradeoffSentence(string $algorithm): string
    {
        return match ($algorithm) {
            'weighted_round_robin' => 'The risk is that wrong weights can hide bottlenecks or route too much traffic to one node.',
            'least_connections' => 'The risk is that connection count does not always represent CPU, memory, queue depth, or downstream latency.',
            'ip_hash' => 'The risk is skew: a few large client IP ranges can overload one upstream and make failover harder.',
            default => 'The risk is assuming all upstreams are equal when capacity, latency, or health differs.',
        };
    }

    /**
     * Return the expected traffic distribution by upstream.
     *
     * @return array<int, array{upstream: string, weight: int, expected_share: string, note: string}>
     */
    private function capacityPlan(string $algorithm, int $upstreamCount): array
    {
        $weights = collect(range(1, $upstreamCount))
            ->mapWithKeys(fn (int $index): array => ["app-{$index}" => $this->weightFor($index, $algorithm)])
            ->all();
        $totalWeight = array_sum($weights);

        return collect($weights)
            ->map(fn (int $weight, string $upstream): array => [
                'upstream' => $upstream,
                'weight' => $weight,
                'expected_share' => $this->expectedShare($algorithm, $weight, $totalWeight, $upstreamCount),
                'note' => $this->capacityNote($algorithm),
            ])
            ->values()
            ->all();
    }

    /**
     * Return the configured weight for one upstream.
     */
    private function weightFor(int $index, string $algorithm): int
    {
        if ($algorithm !== 'weighted_round_robin') {
            return 1;
        }

        return max(1, 4 - $index);
    }

    /**
     * Return a readable expected traffic share.
     */
    private function expectedShare(string $algorithm, int $weight, int $totalWeight, int $upstreamCount): string
    {
        if ($algorithm === 'least_connections') {
            return 'dynamic by active connections';
        }

        if ($algorithm === 'ip_hash') {
            return 'dynamic by client IP distribution';
        }

        $share = $algorithm === 'weighted_round_robin'
            ? ($weight / max(1, $totalWeight)) * 100
            : (1 / max(1, $upstreamCount)) * 100;

        return number_format($share, 1).'%';
    }

    /**
     * Return a short note for the selected capacity model.
     */
    private function capacityNote(string $algorithm): string
    {
        return match ($algorithm) {
            'weighted_round_robin' => 'Expected share should roughly match configured weights while all upstreams are healthy.',
            'least_connections' => 'Share changes with request duration and active connection count.',
            'ip_hash' => 'Share depends on client IP distribution and can become skewed.',
            default => 'Expected share should be close to even while upstreams are similarly healthy.',
        };
    }

    /**
     * Return metrics that prove whether the chosen algorithm is healthy in practice.
     *
     * @return array<int, array{metric: string, why: string, warning: string}>
     */
    private function observabilityMetrics(string $algorithm): array
    {
        $shared = [
            [
                'metric' => 'upstream_5xx_rate',
                'why' => 'Shows whether one upstream or the whole pool is failing after traffic is routed.',
                'warning' => 'A rising 5xx rate after a routing change usually means health checks or upstream capacity need review.',
            ],
            [
                'metric' => 'p95_latency_by_upstream',
                'why' => 'Shows whether one upstream is slower even when it still returns successful responses.',
                'warning' => 'A slow but healthy upstream can quietly hurt users while basic health checks stay green.',
            ],
        ];

        return array_merge($shared, match ($algorithm) {
            'weighted_round_robin' => [
                [
                    'metric' => 'request_share_by_upstream',
                    'why' => 'Confirms the actual traffic split follows the configured weights.',
                    'warning' => 'If request share does not match weights, check config reload, upstream availability, and sticky behavior.',
                ],
            ],
            'least_connections' => [
                [
                    'metric' => 'active_connections_by_upstream',
                    'why' => 'Confirms new requests avoid upstreams that already have many active connections.',
                    'warning' => 'Balanced connection counts can still hide CPU or queue saturation, so pair this with latency.',
                ],
            ],
            'ip_hash' => [
                [
                    'metric' => 'session_affinity_skew',
                    'why' => 'Shows whether client distribution creates hot upstreams under sticky routing.',
                    'warning' => 'High skew means a small group of client IPs can overload one upstream.',
                ],
            ],
            default => [
                [
                    'metric' => 'request_count_by_upstream',
                    'why' => 'Confirms round robin is distributing traffic evenly across healthy upstreams.',
                    'warning' => 'Uneven request count means an upstream may be unhealthy, missing, or affected by connection reuse.',
                ],
            ],
        });
    }

    /**
     * Pick the requested algorithm, or infer one when the caller asks for auto.
     *
     * @param  array{traffic_pattern: string, algorithm: string, has_sticky_sessions: bool}  $input
     */
    private function algorithm(array $input): string
    {
        if ($input['algorithm'] !== 'auto') {
            return $input['algorithm'];
        }

        if ($input['has_sticky_sessions'] || $input['traffic_pattern'] === 'session-affinity') {
            return 'ip_hash';
        }

        return match ($input['traffic_pattern']) {
            'heterogeneous' => 'weighted_round_robin',
            'bursty' => 'least_connections',
            default => 'round_robin',
        };
    }

    /**
     * Return the four algorithm choices with practical tradeoffs.
     *
     * @return array<int, array{name: string, best_for: string, risk: string}>
     */
    private function algorithmCatalog(): array
    {
        return [
            [
                'name' => 'round_robin',
                'best_for' => 'Even traffic and upstreams with similar capacity.',
                'risk' => 'Can overload weaker servers when capacity is not equal.',
            ],
            [
                'name' => 'weighted_round_robin',
                'best_for' => 'Upstreams with different CPU, memory, region, or instance size.',
                'risk' => 'Bad weights create unfair routing and hide real bottlenecks.',
            ],
            [
                'name' => 'least_connections',
                'best_for' => 'Long-running or bursty requests where active connection count matters.',
                'risk' => 'Connection count is not the same as CPU, memory, queue depth, or latency.',
            ],
            [
                'name' => 'ip_hash',
                'best_for' => 'Transitional session affinity when users must keep hitting the same upstream.',
                'risk' => 'Uneven client distribution and harder failover when sessions are not externalized.',
            ],
        ];
    }

    /**
     * Explain the selected algorithm.
     */
    private function reason(string $algorithm, string $trafficPattern, bool $hasStickySessions): string
    {
        return match ($algorithm) {
            'weighted_round_robin' => 'Use weighted round robin because upstream capacity is not equal and traffic should be distributed by weight.',
            'least_connections' => 'Use least connections because bursty or long-running requests should avoid piling onto already busy upstreams.',
            'ip_hash' => $hasStickySessions
                ? 'Use IP hash only when session affinity is required and session state has not been externalized yet.'
                : 'Use IP hash when the traffic pattern explicitly needs affinity to the same upstream.',
            default => "Use round robin because {$trafficPattern} traffic with similar upstream capacity is the simplest reliable baseline.",
        };
    }

    /**
     * Return the signals that drove the recommendation.
     *
     * @param  array{traffic_pattern: string, algorithm: string, upstream_count: int, has_sticky_sessions: bool, health_check_path: string}  $input
     * @return array<int, array{signal: string, value: string, impact: string}>
     */
    private function decisionMatrix(array $input): array
    {
        return [
            [
                'signal' => 'Traffic pattern',
                'value' => $input['traffic_pattern'],
                'impact' => 'Traffic shape decides whether simple rotation, weights, active connections, or affinity matters most.',
            ],
            [
                'signal' => 'Requested algorithm',
                'value' => $input['algorithm'],
                'impact' => $input['algorithm'] === 'auto'
                    ? 'The planner chooses from the traffic signals.'
                    : 'The planner keeps the selected algorithm and explains its risk.',
            ],
            [
                'signal' => 'Sticky sessions',
                'value' => $input['has_sticky_sessions'] ? 'required' : 'not required',
                'impact' => $input['has_sticky_sessions']
                    ? 'Affinity may be needed, but external session storage is usually healthier.'
                    : 'The balancer can freely route requests to healthy upstreams.',
            ],
            [
                'signal' => 'Health check',
                'value' => $input['health_check_path'],
                'impact' => 'A balancer should stop sending traffic to upstreams that fail this path.',
            ],
        ];
    }

    /**
     * Return a compact Nginx-style upstream example.
     */
    private function nginxExample(string $service, string $algorithm, int $upstreamCount, string $healthCheckPath): string
    {
        $upstreams = collect(range(1, $upstreamCount))
            ->map(fn (int $index): string => $this->serverLine($index, $algorithm))
            ->implode("\n");
        $directive = $algorithm === 'least_connections' ? "    least_conn;\n" : ($algorithm === 'ip_hash' ? "    ip_hash;\n" : '');
        $name = Str::kebab($service);

        return <<<NGINX
upstream {$name} {
{$directive}{$upstreams}
}

server {
    location / {
        proxy_pass http://{$name};
        proxy_set_header Host \$host;
        proxy_set_header X-Forwarded-For \$proxy_add_x_forwarded_for;
        proxy_set_header X-Forwarded-Proto \$scheme;
    }

    location {$healthCheckPath} {
        access_log off;
    }
}
NGINX;
    }

    /**
     * Return one upstream server line.
     */
    private function serverLine(int $index, string $algorithm): string
    {
        $weight = $algorithm === 'weighted_round_robin' ? ' weight='.$this->weightFor($index, $algorithm) : '';

        return "    server app-{$index}:9000{$weight};";
    }

    /**
     * Return a short interview-ready answer.
     */
    private function interviewAnswer(string $algorithm): string
    {
        return match ($algorithm) {
            'weighted_round_robin' => 'I would explain four algorithms: round robin for equal nodes, weighted round robin for unequal capacity, least connections for long-running traffic, and IP hash for session affinity. In this case I choose weighted round robin because capacity differs, but I would verify it with metrics and health checks.',
            'least_connections' => 'I would explain round robin, weighted round robin, least connections, and IP hash. For bursty or long-running requests, least connections is safer than blind rotation because it avoids sending new work to already busy upstreams.',
            'ip_hash' => 'I would mention IP hash as a session-affinity option, but also say it is often a transition strategy. The stronger design is to move session state to Redis or another shared store so any healthy upstream can serve the request.',
            default => 'I would start with round robin when servers are similar and traffic is even, then move to weighted round robin, least connections, or IP hash only when capacity, request duration, or session affinity requires it.',
        };
    }
}
