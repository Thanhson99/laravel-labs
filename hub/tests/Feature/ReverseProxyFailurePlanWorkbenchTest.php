<?php

declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;

final class ReverseProxyFailurePlanWorkbenchTest extends TestCase
{
    /**
     * The reverse-proxy failure workbench renders the edge availability planning loop.
     */
    public function test_reverse_proxy_failure_plan_workbench_renders(): void
    {
        $response = $this->get('/workbench/reverse-proxy-failure-plan');

        $response
            ->assertOk()
            ->assertSee('Reverse Proxy Failure Plan Workbench')
            ->assertSee('POST /api/practice/reverse-proxy-failure-plan')
            ->assertSee('ReverseProxyFailurePlanService')
            ->assertSee('Edge feature file')
            ->assertSee('WAF rule rollout')
            ->assertSee('Executive Summary')
            ->assertSee('Incident Memo')
            ->assertSee('Decision Tree')
            ->assertSee('Scenario Playbook')
            ->assertSee('Simulation Plan')
            ->assertSee('Health Gate')
            ->assertSee('Ownership')
            ->assertSee('Incident Timeline')
            ->assertSee('Evidence Pack')
            ->assertSee('Copy memo')
            ->assertSee('Plan proxy failure');
    }

    /**
     * The reverse-proxy failure API returns a critical blast-radius plan for global edge changes.
     */
    public function test_reverse_proxy_failure_plan_api_returns_plan(): void
    {
        $response = $this->postJson('/api/practice/reverse-proxy-failure-plan', [
            'service_name' => 'Public Checkout Edge',
            'proxy_layer' => 'edge-cdn',
            'change_type' => 'feature-file',
            'origin_count' => 120,
            'rollout_strategy' => 'global',
            'has_health_gate' => false,
            'fail_behavior' => 'fail_closed',
            'observed_failure' => 'http_500',
        ]);

        $response
            ->assertOk()
            ->assertJsonPath('data.service', 'PublicCheckoutEdge')
            ->assertJsonPath('data.risk_level', 'critical')
            ->assertJsonPath('data.readiness_score.score', 10)
            ->assertJsonPath('data.readiness_score.label', 'blocked')
            ->assertJsonPath('data.readiness_score.blockers.0', 'Global rollout is too broad for a shared proxy request path.')
            ->assertJsonPath('data.readiness_score.next_actions.0', 'Convert rollout to canary or staged region rollout.')
            ->assertJsonPath('data.executive_summary.headline', 'critical risk: edge-cdn feature-file can block 120 healthy origins.')
            ->assertJsonPath('data.executive_summary.verification_focus', 'Add an automated health gate before another proxy rollout.')
            ->assertJsonPath('data.request_path.1.step', 'edge-cdn')
            ->assertJsonPath('data.request_path.3.failure_effect', 'Hundreds of healthy origins still look down if the edge path is broken.')
            ->assertJsonPath('data.risk_signals.0', 'Global rollout makes a bad proxy change affect the whole request path quickly.')
            ->assertJsonPath('data.risk_signals.1', 'No health gate means bad proxy behavior can keep propagating after errors start.')
            ->assertJsonPath('data.blast_radius_controls.0', 'Replace global rollout with canary, region-by-region, or customer-cohort rollout before touching the whole edge.')
            ->assertJsonPath('data.config_validation_plan.1.check', 'Enforce maximum file size, array cardinality, generated-rule count, and per-tenant limits.')
            ->assertJsonPath('data.rollout_plan.2', 'Add automated health gates before the next rollout; manual dashboards alone are too slow for shared edge risk.')
            ->assertJsonPath('data.fail_small_design.0.area', 'security-sensitive paths')
            ->assertJsonPath('data.observability_plan.0.metric', 'proxy_generated_5xx_rate')
            ->assertJsonPath('data.incident_triage.3', 'Rollback the latest proxy artifact before scaling origins when the failure is edge-generated.')
            ->assertJsonPath('data.origin_reachability_checks.0', 'Confirm all 120 origins pass direct readiness checks from inside the trusted network.')
            ->assertJsonPath('data.decision_tree.0.then', 'rollback or bypass the latest proxy artifact before scaling origins')
            ->assertJsonPath('data.review_checklist.1.checks.0', 'Replace global rollout with canary or staged rollout before approval.')
            ->assertJsonPath('data.scenario_playbook.change_type', 'feature-file')
            ->assertJsonPath('data.scenario_playbook.validation_focus.0', 'Schema, maximum byte size, item count, nested depth, and per-tenant cardinality.')
            ->assertJsonPath('data.scenario_playbook.rollback_trigger', 'Rollback when proxy parser errors, memory, CPU, or proxy-generated 5xx move after feature-file publish.')
            ->assertJsonPath('data.simulation_plan.2.step', 'shadow-proxy-change')
            ->assertJsonPath('data.simulation_plan.4.expected_signal', 'Removing the latest proxy artifact restores public edge-to-origin reachability.')
            ->assertJsonPath('data.chaos_drill.success_criteria.1', 'Responder rolls back or bypasses the proxy artifact before scaling origins.')
            ->assertJsonPath('data.health_gate_policy.mode', 'required-before-rollout')
            ->assertJsonPath('data.health_gate_policy.stop_conditions.0', 'Any increase in http_500 above baseline during canary.')
            ->assertJsonPath('data.alert_rules.0.name', 'edge-cdn.proxy-generated-errors')
            ->assertJsonPath('data.alert_rules.2.action', 'Treat the proxy path as suspect and bypass or rollback the latest proxy change.')
            ->assertJsonPath('data.ownership_matrix.0.owner', 'platform-edge')
            ->assertJsonPath('data.ownership_matrix.1.responsibility', 'Own feature-file generation, schema, size limits, compatibility, and expected behavior.')
            ->assertJsonPath('data.postmortem_actions.0.owner', 'config-or-feature-owner')
            ->assertJsonPath('data.postmortem_actions.2.done_when', 'Responders can distinguish proxy-generated errors from origin-generated errors in the first five minutes.')
            ->assertJsonPath('data.incident_timeline.0.window', '0-5 minutes')
            ->assertJsonPath('data.incident_timeline.0.exit_criteria', 'Incident commander knows whether to rollback proxy artifact or investigate origin service.')
            ->assertJsonPath('data.incident_timeline.1.actions.0', 'Rollback or bypass the latest edge-cdn artifact when proxy-generated errors dominate.')
            ->assertJsonPath('data.evidence_pack.0.owner', 'platform-edge')
            ->assertJsonPath('data.evidence_pack.0.artifact', 'edge-cdn rollout record for the latest feature-file artifact')
            ->assertJsonPath('data.evidence_pack.3.proves', 'Recovery was public-path recovery, not only direct origin recovery.')
            ->assertJsonPath('data.edge_outage_lesson_map.3.lesson', 'Global 5xx symptoms are not automatically a DDoS.')
            ->assertJsonPath('data.configuration_review.1.area', 'limits')
            ->assertJsonPath('data.tests.1', 'Canary the proxy change through a small traffic slice before global rollout.')
            ->assertJsonPath('data.commands.0', 'php artisan test --filter ReverseProxyFailurePlan')
            ->assertJsonFragment(['postmortem_note' => 'The useful interview lesson is not that reverse proxies are bad. The lesson is that shared edge request paths need validation, staged rollout, health gates, rollback, and small blast radius.'])
            ->assertSee('healthy origins can still be unreachable');

        $this->assertStringContainsString('## Decision Tree', $response->json('data.incident_memo_markdown'));
        $this->assertStringContainsString('## Scenario Playbook', $response->json('data.incident_memo_markdown'));
        $this->assertStringContainsString('## Simulation', $response->json('data.incident_memo_markdown'));
        $this->assertStringContainsString('Readiness: blocked (10/100)', $response->json('data.incident_memo_markdown'));
        $this->assertStringContainsString('## Health Gate', $response->json('data.incident_memo_markdown'));
        $this->assertStringContainsString('## Ownership', $response->json('data.incident_memo_markdown'));
        $this->assertStringContainsString('## Postmortem Actions', $response->json('data.incident_memo_markdown'));
        $this->assertStringContainsString('## Incident Timeline', $response->json('data.incident_memo_markdown'));
        $this->assertStringContainsString('## Evidence Pack', $response->json('data.incident_memo_markdown'));
    }

    /**
     * Invalid reverse-proxy payloads return validation errors.
     */
    public function test_reverse_proxy_failure_plan_api_validates_payload(): void
    {
        $response = $this->postJson('/api/practice/reverse-proxy-failure-plan', [
            'service_name' => '<bad>',
            'proxy_layer' => 'browser',
            'change_type' => 'magic',
            'origin_count' => 0,
            'rollout_strategy' => 'everywhere',
            'has_health_gate' => 'maybe',
            'fail_behavior' => 'panic',
            'observed_failure' => 'unknown',
        ]);

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors([
                'service_name',
                'proxy_layer',
                'change_type',
                'origin_count',
                'rollout_strategy',
                'has_health_gate',
                'fail_behavior',
                'observed_failure',
            ]);
    }
}
