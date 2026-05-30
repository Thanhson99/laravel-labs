<?php

declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;

final class LoadBalancerPlanWorkbenchTest extends TestCase
{
    /**
     * The load-balancer workbench renders the system-design planning loop.
     */
    public function test_load_balancer_plan_workbench_renders(): void
    {
        $response = $this->get('/workbench/load-balancer-plan');

        $response
            ->assertOk()
            ->assertSee('Load Balancer Plan Workbench')
            ->assertSee('POST /api/practice/load-balancer-plan')
            ->assertSee('LoadBalancerPlanService')
            ->assertSee('Scenario preset')
            ->assertSee('Report exports')
            ->assertSee('Legacy session app')
            ->assertSee('Plan load balancing');
    }

    /**
     * The load-balancer API recommends least connections for bursty traffic.
     */
    public function test_load_balancer_plan_api_returns_plan(): void
    {
        $response = $this->postJson('/api/practice/load-balancer-plan', [
            'service_name' => 'Report Export Worker',
            'traffic_pattern' => 'bursty',
            'algorithm' => 'auto',
            'upstream_count' => 4,
            'has_sticky_sessions' => false,
            'health_check_path' => '/health',
        ]);

        $response
            ->assertOk()
            ->assertJsonPath('data.service', 'ReportExportWorker')
            ->assertJsonPath('data.recommended_algorithm', 'least_connections')
            ->assertJsonPath('data.reason', 'Use least connections because bursty or long-running requests should avoid piling onto already busy upstreams.')
            ->assertJsonPath('data.risk_assessment.level', 'medium')
            ->assertJsonPath('data.risk_assessment.reasons.0', 'Long-running requests can hide saturation if connection count is the only signal.')
            ->assertJsonPath('data.risk_assessment.mitigations.1', 'Track traffic share, latency, and 5xx rate by upstream during every routing change.')
            ->assertJsonPath('data.algorithm_catalog.0.name', 'round_robin')
            ->assertJsonPath('data.algorithm_catalog.1.name', 'weighted_round_robin')
            ->assertJsonPath('data.algorithm_catalog.2.name', 'least_connections')
            ->assertJsonPath('data.algorithm_catalog.3.name', 'ip_hash')
            ->assertJsonPath('data.decision_matrix.0.signal', 'Traffic pattern')
            ->assertJsonPath('data.decision_matrix.3.value', '/health')
            ->assertJsonPath('data.capacity_plan.0.upstream', 'app-1')
            ->assertJsonPath('data.capacity_plan.0.expected_share', 'dynamic by active connections')
            ->assertJsonPath('data.capacity_plan.0.note', 'Share changes with request duration and active connection count.')
            ->assertJsonPath('data.observability_metrics.0.metric', 'upstream_5xx_rate')
            ->assertJsonPath('data.observability_metrics.2.metric', 'active_connections_by_upstream')
            ->assertJsonPath('data.observability_metrics.2.warning', 'Balanced connection counts can still hide CPU or queue saturation, so pair this with latency.')
            ->assertJsonPath('data.incident_runbook.0', 'Confirm whether the issue is global or isolated to one upstream by checking status, latency, and 5xx rate by node.')
            ->assertJsonPath('data.incident_runbook.3', 'Check whether long-lived requests are holding connections open and starving new traffic.')
            ->assertJsonPath('data.simulation_steps.0.step', 'baseline')
            ->assertJsonPath('data.simulation_steps.0.expected_signal', 'Busy upstreams should receive fewer new requests while active connections stay high.')
            ->assertJsonPath('data.simulation_steps.3.action', 'Create several slow requests against one upstream and observe whether new requests prefer less busy upstreams.')
            ->assertJsonPath('data.configuration_review.0.area', 'forwarded headers')
            ->assertJsonPath('data.configuration_review.1.check', 'Use /health as a lightweight readiness signal and remove upstreams that fail it.')
            ->assertJsonPath('data.configuration_review.3.check', 'Confirm requests can be served by any healthy upstream without local session state.')
            ->assertJsonPath('data.interview_rubric.definition', 'A load balancer distributes requests across healthy upstreams to improve capacity, availability, and rollout control.')
            ->assertJsonPath('data.interview_rubric.algorithm_choice', 'I choose least connections when request duration varies and active connection count is a better signal than simple rotation.')
            ->assertJsonPath('data.interview_rubric.tradeoff', 'The risk is that connection count does not always represent CPU, memory, queue depth, or downstream latency.')
            ->assertJsonPath('data.rollout_checks.0', 'Start with a health check that removes unhealthy upstreams before routing traffic to them.')
            ->assertJsonPath('data.failure_modes.0', 'A dead upstream still receives traffic because health checks are missing or too weak.')
            ->assertJsonPath('data.commands.0', 'php artisan test --filter LoadBalancerPlanWorkbenchTest')
            ->assertJsonFragment(['interview_answer' => 'I would explain round robin, weighted round robin, least connections, and IP hash. For bursty or long-running requests, least connections is safer than blind rotation because it avoids sending new work to already busy upstreams.'])
            ->assertSee('least_conn');
    }

    /**
     * Invalid load-balancer payloads return validation errors.
     */
    public function test_load_balancer_plan_api_validates_payload(): void
    {
        $response = $this->postJson('/api/practice/load-balancer-plan', [
            'service_name' => '<bad>',
            'traffic_pattern' => 'random',
            'algorithm' => 'magic',
            'upstream_count' => 1,
            'has_sticky_sessions' => 'maybe',
            'health_check_path' => 'health',
        ]);

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors([
                'service_name',
                'traffic_pattern',
                'algorithm',
                'upstream_count',
                'has_sticky_sessions',
                'health_check_path',
            ]);
    }
}
