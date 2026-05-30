<?php

declare(strict_types=1);

namespace Tests\Unit\Practice;

use App\Services\Practice\ReverseProxyFailurePlanService;
use PHPUnit\Framework\TestCase;

final class ReverseProxyFailurePlanServiceTest extends TestCase
{
    /**
     * Controlled proxy rollouts should keep blast radius smaller.
     */
    public function test_plan_marks_canary_rollout_with_health_gate_as_controlled(): void
    {
        $plan = (new ReverseProxyFailurePlanService)->plan([
            'service_name' => 'Account Security Edge',
            'proxy_layer' => 'edge-cdn',
            'change_type' => 'waf-rule',
            'origin_count' => 40,
            'rollout_strategy' => 'canary',
            'has_health_gate' => true,
            'fail_behavior' => 'serve_stale',
            'observed_failure' => 'bad_routing',
        ]);

        $this->assertSame('AccountSecurityEdge', $plan['service']);
        $this->assertSame('controlled', $plan['risk_level']);
        $this->assertSame(100, $plan['readiness_score']['score']);
        $this->assertSame('ready-for-canary', $plan['readiness_score']['label']);
        $this->assertSame([], $plan['readiness_score']['blockers']);
        $this->assertSame('controlled risk: edge-cdn waf-rule can block 40 healthy origins.', $plan['executive_summary']['headline']);
        $this->assertSame('A large origin pool increases capacity, but it does not reduce shared proxy blast radius by itself.', $plan['risk_signals'][0]);
        $this->assertSame('Keep staged rollout and define the maximum region, POP, or customer cohort allowed to receive the change first.', $plan['blast_radius_controls'][0]);
        $this->assertSame('Send the change to one small canary slice first and hold until proxy 5xx, latency, and saturation stay normal.', $plan['rollout_plan'][1]);
        $this->assertStringContainsString('with automated health gates', $plan['interview_answer']);
        $this->assertStringContainsString('# Reverse Proxy Failure Plan: AccountSecurityEdge', $plan['incident_memo_markdown']);
        $this->assertSame('Confirm canary or staged rollout has a maximum blast-radius limit.', $plan['review_checklist'][1]['checks'][0]);
        $this->assertSame('False-positive rate, route scope, HTTP method scope, bypass procedure, and security severity.', $plan['scenario_playbook']['validation_focus'][0]);
        $this->assertSame('Who can downgrade a WAF rule from block to log-only during an availability incident?', $plan['scenario_playbook']['owner_question']);
        $this->assertSame('Introduce a staged waf-rule artifact that triggers serve_stale for a small canary route.', $plan['chaos_drill']['inject']);
        $this->assertSame('Public edge-to-origin health returns 2xx with normal latency before the proxy change.', $plan['simulation_plan'][0]['expected_signal']);
        $this->assertSame('automated', $plan['health_gate_policy']['mode']);
        $this->assertSame('edge-cdn.artifact-parser-health', $plan['alert_rules'][1]['name']);
        $this->assertSame('platform-edge', $plan['ownership_matrix'][0]['owner']);
        $this->assertSame('Run one controlled drill that proves rollback and alert routing.', $plan['postmortem_actions'][3]['action']);
        $this->assertSame('follow-up', $plan['incident_timeline'][3]['window']);
        $this->assertSame('Postmortem actions have owners, done criteria, and verification evidence.', $plan['incident_timeline'][3]['exit_criteria']);
        $this->assertSame('validation report for waf-rule schema, size, cardinality, and compatibility', $plan['evidence_pack'][4]['artifact']);
        $this->assertSame('Generated data is production input.', $plan['edge_outage_lesson_map'][0]['lesson']);
        $this->assertSame('Serve stale content when possible so a proxy feature failure does not become a full outage.', $plan['fail_small_design'][1]['decision']);
    }

    /**
     * Global proxy changes without gates should be treated as critical.
     */
    public function test_plan_marks_global_rollout_without_health_gate_as_critical(): void
    {
        $plan = (new ReverseProxyFailurePlanService)->plan([
            'service_name' => 'Public Checkout Edge',
            'proxy_layer' => 'edge-cdn',
            'change_type' => 'feature-file',
            'origin_count' => 120,
            'rollout_strategy' => 'global',
            'has_health_gate' => false,
            'fail_behavior' => 'fail_closed',
            'observed_failure' => 'http_500',
        ]);

        $this->assertSame('critical', $plan['risk_level']);
        $this->assertSame(10, $plan['readiness_score']['score']);
        $this->assertSame('blocked', $plan['readiness_score']['label']);
        $this->assertSame('Add an automated health gate before another proxy rollout.', $plan['executive_summary']['verification_focus']);
        $this->assertSame('Healthy origins do not guarantee availability when every request must pass through a edge-cdn affected by a feature-file change.', $plan['core_lesson']);
        $this->assertSame('HTTP 500 from the proxy layer usually points to edge execution or generated config, not origin capacity.', $plan['risk_signals'][3]);
        $this->assertSame('Rollback the latest proxy artifact before scaling origins when the failure is edge-generated.', $plan['incident_triage'][3]);
        $this->assertSame('public traffic shows http_500 while direct origin readiness is healthy', $plan['decision_tree'][0]['if']);
        $this->assertSame('proxy_generated_5xx_rate', $plan['observability_plan'][0]['metric']);
        $this->assertStringContainsString('Health gate: missing', $plan['incident_memo_markdown']);
        $this->assertStringContainsString('If public traffic shows http_500 while direct origin readiness is healthy', $plan['incident_memo_markdown']);
        $this->assertStringContainsString('Rollback trigger: Rollback when proxy parser errors, memory, CPU, or proxy-generated 5xx move after feature-file publish.', $plan['incident_memo_markdown']);
        $this->assertStringContainsString('proxyctl validate --layer=edge-cdn --type=feature-file', $plan['simulation_plan'][2]['command']);
        $this->assertSame('required-before-rollout', $plan['health_gate_policy']['mode']);
        $this->assertSame('Stop rollout and compare with origin_5xx_rate before scaling origins.', $plan['alert_rules'][0]['action']);
        $this->assertSame('Own feature-file generation, schema, size limits, compatibility, and expected behavior.', $plan['ownership_matrix'][1]['responsibility']);
        $this->assertSame('Add a public edge-to-origin smoke check to the rollout gate.', $plan['postmortem_actions'][1]['action']);
        $this->assertSame('Classify whether the first bad signal is proxy-generated or origin-generated.', $plan['incident_timeline'][0]['goal']);
        $this->assertSame('Whether http_500 started before requests reached origin servers.', $plan['evidence_pack'][1]['proves']);
        $this->assertStringContainsString('rollback-proof', $plan['incident_memo_markdown']);
        $this->assertStringContainsString('120 healthy origins can still be unreachable', $plan['interview_answer']);
    }
}
