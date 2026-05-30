<?php

declare(strict_types=1);

namespace Tests\Unit\Practice;

use App\Services\Practice\ReactRenderOptimizationPlanService;
use PHPUnit\Framework\TestCase;

final class ReactRenderOptimizationPlanServiceTest extends TestCase
{
    /**
     * Prop churn plans stabilize props before memoizing children.
     */
    public function test_prop_churn_plan_uses_memoization_discipline(): void
    {
        $plan = (new ReactRenderOptimizationPlanService)->plan([
            'component_name' => 'Customer Search Panel',
            'component_type' => 'search-panel',
            'render_issue' => 'prop-churn',
            'state_shape' => 'parent-state',
            'list_size' => 250,
            'profiler_signal' => 'prop-churn',
        ]);

        $this->assertSame('CustomerSearchPanel', $plan['component']);
        $this->assertSame('low', $plan['risk_level']);
        $this->assertSame('ready', $plan['readiness_score']['label']);
        $this->assertSame(80, $plan['readiness_score']['score']);
        $this->assertSame('React.memo', $plan['tool_decision_matrix'][0]['tool']);
        $this->assertSame('strong', $plan['tool_decision_matrix'][0]['fit']);
        $this->assertSame('weak', $plan['tool_decision_matrix'][4]['fit']);
        $this->assertSame('React.memo', $plan['optimization_plan'][0]['tool']);
        $this->assertSame('useMemo', $plan['optimization_plan'][1]['tool']);
        $this->assertSame('useCallback', $plan['optimization_plan'][2]['tool']);
        $this->assertSame('state locality', $plan['optimization_plan'][3]['tool']);
        $this->assertSame('Stabilize render inputs', $plan['implementation_steps'][1]['step']);
        $this->assertStringContainsString('React.memo', $plan['code_examples']['memo_child']);
        $this->assertSame('CustomerSearchPanel', $plan['measurement_template']['component']);
        $this->assertSame('React.memo props', $plan['dependency_review'][0]['target']);
        $this->assertSame('stale-derived-data', $plan['regression_checks'][0]['name']);
        $this->assertSame('callback-fresh-state', $plan['regression_checks'][1]['name']);
        $this->assertStringContainsString('Evidence required: before/after React Profiler capture', $plan['pull_request_note']);
        $this->assertStringContainsString('# React Render Review Packet: CustomerSearchPanel', $plan['review_packet_markdown']);
        $this->assertStringContainsString('## Dependency Review', $plan['review_packet_markdown']);
        $this->assertStringContainsString('React Profiler', $plan['interview_answer']);
    }

    /**
     * Very large lists are flagged as structural render problems before memoization.
     */
    public function test_large_list_plan_prioritizes_virtualization(): void
    {
        $plan = (new ReactRenderOptimizationPlanService)->plan([
            'component_name' => 'Audit Table',
            'component_type' => 'table',
            'render_issue' => 'large-list',
            'state_shape' => 'global-state',
            'list_size' => 2000,
            'profiler_signal' => 'slow-commit',
        ]);

        $this->assertSame('high', $plan['risk_level']);
        $this->assertSame('blocked', $plan['readiness_score']['label']);
        $this->assertContains('High render risk needs before/after evidence and a rollback path.', $plan['readiness_score']['blockers']);
        $this->assertSame('strong', $plan['tool_decision_matrix'][4]['fit']);
        $this->assertSame('virtualization', $plan['optimization_plan'][3]['tool']);
        $this->assertSame('Reduce rendered DOM work', $plan['implementation_steps'][1]['step']);
        $this->assertSame('list rendering boundary', $plan['dependency_review'][3]['target']);
        $this->assertSame('virtualized-list-visibility', $plan['regression_checks'][3]['name']);
        $this->assertSame('context-subscriber-scope', $plan['regression_checks'][4]['name']);
        $this->assertSame(
            'Start with list virtualization and stable item props; memo alone will not save a very large rendered list.',
            $plan['recommendation']
        );
        $this->assertSame('php artisan test --filter ReactRenderOptimizationPlan', $plan['commands'][0]);
    }

    /**
     * Context and calculation issues get structural recommendations instead of generic memo advice.
     */
    public function test_context_and_calculation_plans_get_specific_steps(): void
    {
        $context = (new ReactRenderOptimizationPlanService)->plan([
            'component_name' => 'Notification Dashboard',
            'component_type' => 'dashboard',
            'render_issue' => 'context-update',
            'state_shape' => 'context-state',
            'list_size' => 80,
            'profiler_signal' => 'many-children',
        ]);

        $this->assertStringContainsString('Split broad context updates', $context['recommendation']);
        $this->assertSame('Reduce context blast radius', $context['implementation_steps'][1]['step']);

        $calculation = (new ReactRenderOptimizationPlanService)->plan([
            'component_name' => 'Pricing Form',
            'component_type' => 'form',
            'render_issue' => 'expensive-calculation',
            'state_shape' => 'local-state',
            'list_size' => 40,
            'profiler_signal' => 'slow-commit',
        ]);

        $this->assertStringContainsString('expensive derived calculation', $calculation['recommendation']);
        $this->assertSame('strong', $calculation['tool_decision_matrix'][1]['fit']);
        $this->assertSame('Isolate expensive derivation', $calculation['implementation_steps'][1]['step']);
    }
}
