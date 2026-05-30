<?php

declare(strict_types=1);

namespace Tests\Unit\Practice;

use App\Services\Practice\LoadBalancerPlanService;
use PHPUnit\Framework\TestCase;

final class LoadBalancerPlanServiceTest extends TestCase
{
    /**
     * Session affinity should choose IP hash while warning about state placement.
     */
    public function test_plan_recommends_ip_hash_for_sticky_sessions(): void
    {
        $plan = (new LoadBalancerPlanService)->plan([
            'service_name' => 'Legacy Session App',
            'traffic_pattern' => 'session-affinity',
            'algorithm' => 'auto',
            'upstream_count' => 3,
            'has_sticky_sessions' => true,
            'health_check_path' => '/up',
        ]);

        $this->assertSame('LegacySessionApp', $plan['service']);
        $this->assertSame('ip_hash', $plan['recommended_algorithm']);
        $this->assertSame('Use IP hash only when session affinity is required and session state has not been externalized yet.', $plan['reason']);
        $this->assertSame('high', $plan['risk_assessment']['level']);
        $this->assertSame('Session affinity can create uneven traffic and makes failover harder.', $plan['risk_assessment']['reasons'][0]);
        $this->assertSame('Move session state out of local memory before relying on horizontal scale.', $plan['risk_assessment']['mitigations'][3]);
        $this->assertSame('Transitional session affinity when users must keep hitting the same upstream.', $plan['algorithm_catalog'][3]['best_for']);
        $this->assertSame('Sticky sessions', $plan['decision_matrix'][2]['signal']);
        $this->assertSame('app-1', $plan['capacity_plan'][0]['upstream']);
        $this->assertSame('dynamic by client IP distribution', $plan['capacity_plan'][0]['expected_share']);
        $this->assertSame('Share depends on client IP distribution and can become skewed.', $plan['capacity_plan'][0]['note']);
        $this->assertSame('session_affinity_skew', $plan['observability_metrics'][2]['metric']);
        $this->assertSame('High skew means a small group of client IPs can overload one upstream.', $plan['observability_metrics'][2]['warning']);
        $this->assertSame('Check for hot client IP ranges that pin too much traffic to one upstream.', $plan['incident_runbook'][3]);
        $this->assertSame('Move session state to shared storage before removing affinity if users depend on sticky sessions.', $plan['incident_runbook'][4]);
        $this->assertSame('The same client IP should keep returning to the same upstream until health changes.', $plan['simulation_steps'][0]['expected_signal']);
        $this->assertSame('Send requests from two different client IP sources and compare upstream affinity.', $plan['simulation_steps'][3]['action']);
        $this->assertSame('session state', $plan['configuration_review'][3]['area']);
        $this->assertSame('Document why affinity is needed and plan migration toward shared session storage.', $plan['configuration_review'][3]['check']);
        $this->assertSame('I choose IP hash only when the system temporarily needs session affinity to the same upstream.', $plan['interview_rubric']['algorithm_choice']);
        $this->assertSame('The risk is skew: a few large client IP ranges can overload one upstream and make failover harder.', $plan['interview_rubric']['tradeoff']);
        $this->assertStringContainsString('ip_hash;', $plan['nginx_example']);
        $this->assertStringContainsString('proxy_set_header X-Forwarded-For', $plan['nginx_example']);
        $this->assertStringContainsString('session-affinity option', $plan['interview_answer']);
        $this->assertSame('Sticky sessions hide uneven load and make deploys harder when app state is stored locally.', $plan['failure_modes'][1]);
    }

    /**
     * Heterogeneous upstream capacity should choose weighted round robin.
     */
    public function test_plan_recommends_weighted_round_robin_for_unequal_capacity(): void
    {
        $plan = (new LoadBalancerPlanService)->plan([
            'service_name' => 'Checkout API',
            'traffic_pattern' => 'heterogeneous',
            'algorithm' => 'auto',
            'upstream_count' => 2,
            'has_sticky_sessions' => false,
            'health_check_path' => '/health',
        ]);

        $this->assertSame('weighted_round_robin', $plan['recommended_algorithm']);
        $this->assertSame('medium', $plan['risk_assessment']['level']);
        $this->assertSame('Only two upstreams leaves less room for maintenance, draining, and failure recovery.', $plan['risk_assessment']['reasons'][0]);
        $this->assertStringContainsString('weight=3', $plan['nginx_example']);
        $this->assertStringContainsString('weight=2', $plan['nginx_example']);
        $this->assertSame('60.0%', $plan['capacity_plan'][0]['expected_share']);
        $this->assertSame('40.0%', $plan['capacity_plan'][1]['expected_share']);
        $this->assertSame('request_share_by_upstream', $plan['observability_metrics'][2]['metric']);
        $this->assertSame('Compare actual request share with configured weights before changing capacity assumptions.', $plan['incident_runbook'][3]);
        $this->assertSame('Request share should roughly follow configured weights.', $plan['simulation_steps'][0]['expected_signal']);
        $this->assertSame('The observed traffic split should move toward the new weight ratio.', $plan['simulation_steps'][3]['expected_signal']);
        $this->assertSame('Use /health as a lightweight readiness signal and remove upstreams that fail it.', $plan['configuration_review'][1]['check']);
        $this->assertSame('draining', $plan['configuration_review'][4]['area']);
        $this->assertSame('I choose weighted round robin when upstreams have different capacity and traffic should follow explicit weights.', $plan['interview_rubric']['algorithm_choice']);
        $this->assertSame('Use weighted round robin because upstream capacity is not equal and traffic should be distributed by weight.', $plan['reason']);
    }
}
