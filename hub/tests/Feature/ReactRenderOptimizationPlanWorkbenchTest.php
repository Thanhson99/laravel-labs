<?php

declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;

final class ReactRenderOptimizationPlanWorkbenchTest extends TestCase
{
    /**
     * The React render optimization workbench renders the planning form.
     */
    public function test_react_render_optimization_workbench_renders(): void
    {
        $this->get('/workbench/react-render-optimization-plan')
            ->assertOk()
            ->assertSee('Optimize React re-renders with measurement')
            ->assertSee('POST /api/practice/react-render-optimization-plan')
            ->assertSee('React.memo')
            ->assertSee('useMemo')
            ->assertSee('useCallback')
            ->assertSee('Tool Decision Matrix')
            ->assertSee('Readiness Score')
            ->assertSee('Implementation Steps')
            ->assertSee('Measurement Template')
            ->assertSee('Dependency Review')
            ->assertSee('Regression Checks')
            ->assertSee('Review Packet Markdown')
            ->assertSee('Scenario preset')
            ->assertSee('Large table with slow commits')
            ->assertSee('Copy review packet')
            ->assertSee('Plan optimization');
    }

    /**
     * The API returns a memoization plan from measured render symptoms.
     */
    public function test_react_render_optimization_api_returns_plan(): void
    {
        $this->postJson('/api/practice/react-render-optimization-plan', [
            'component_name' => 'Customer Search Panel',
            'component_type' => 'search-panel',
            'render_issue' => 'prop-churn',
            'state_shape' => 'parent-state',
            'list_size' => 250,
            'profiler_signal' => 'prop-churn',
        ])
            ->assertOk()
            ->assertJsonPath('data.component', 'CustomerSearchPanel')
            ->assertJsonPath('data.risk_level', 'low')
            ->assertJsonPath('data.readiness_score.score', 80)
            ->assertJsonPath('data.readiness_score.label', 'ready')
            ->assertJsonPath('data.readiness_score.next_actions.0', 'Apply the smallest change and keep the before/after profiler note in the PR.')
            ->assertJsonPath('data.recommendation', 'Stabilize object, array, and callback props with useMemo or useCallback, then wrap expensive children with React.memo.')
            ->assertJsonPath('data.tool_decision_matrix.0.tool', 'React.memo')
            ->assertJsonPath('data.tool_decision_matrix.0.fit', 'strong')
            ->assertJsonPath('data.tool_decision_matrix.4.tool', 'virtualization / pagination')
            ->assertJsonPath('data.optimization_plan.0.tool', 'React.memo')
            ->assertJsonPath('data.optimization_plan.1.tool', 'useMemo')
            ->assertJsonPath('data.optimization_plan.2.tool', 'useCallback')
            ->assertJsonPath('data.optimization_plan.3.tool', 'state locality')
            ->assertJsonPath('data.implementation_steps.0.step', 'Capture baseline')
            ->assertJsonPath('data.implementation_steps.1.step', 'Stabilize render inputs')
            ->assertJsonPath('data.profiler_checklist.0', 'Start from the profiler signal: prop-churn.')
            ->assertJsonPath('data.measurement_template.component', 'CustomerSearchPanel')
            ->assertJsonPath('data.measurement_template.pass_condition', 'The optimized interaction renders fewer expensive components or shorter commits without stale UI.')
            ->assertJsonPath('data.dependency_review.0.target', 'React.memo props')
            ->assertJsonPath('data.dependency_review.3.target', 'state owner')
            ->assertJsonPath('data.regression_checks.0.name', 'stale-derived-data')
            ->assertJsonPath('data.regression_checks.1.name', 'callback-fresh-state')
            ->assertJsonPath(
                'data.pull_request_note',
                fn (string $note): bool => str_contains($note, 'Evidence required: before/after React Profiler capture')
            )
            ->assertJsonPath(
                'data.review_packet_markdown',
                fn (string $markdown): bool => str_contains($markdown, '# React Render Review Packet: CustomerSearchPanel')
                    && str_contains($markdown, '## Dependency Review')
                    && str_contains($markdown, '**React.memo props**')
            )
            ->assertJsonPath('data.commands.1', 'php artisan route:list --path=react-render-optimization-plan')
            ->assertSee('React re-render optimization starts with measurement')
            ->assertSee('React.memo');
    }

    /**
     * Plans without profiler evidence are marked as blocked until measurement exists.
     */
    public function test_react_render_optimization_api_blocks_unmeasured_changes(): void
    {
        $this->postJson('/api/practice/react-render-optimization-plan', [
            'component_name' => 'Settings Panel',
            'component_type' => 'dashboard',
            'render_issue' => 'unknown',
            'state_shape' => 'global-state',
            'list_size' => 50,
            'profiler_signal' => 'not-measured',
        ])
            ->assertOk()
            ->assertJsonPath('data.readiness_score.label', 'blocked')
            ->assertJsonPath('data.readiness_score.blockers.0', 'No React Profiler signal has been captured yet.')
            ->assertJsonPath('data.readiness_score.next_actions.0', 'Capture a React Profiler baseline for the target interaction.');
    }

    /**
     * Large-list plans include list-specific regression coverage.
     */
    public function test_react_render_optimization_api_adds_large_list_regression_checks(): void
    {
        $this->postJson('/api/practice/react-render-optimization-plan', [
            'component_name' => 'Audit Table',
            'component_type' => 'table',
            'render_issue' => 'large-list',
            'state_shape' => 'global-state',
            'list_size' => 2000,
            'profiler_signal' => 'slow-commit',
        ])
            ->assertOk()
            ->assertJsonPath('data.regression_checks.3.name', 'virtualized-list-visibility')
            ->assertJsonPath('data.regression_checks.4.name', 'context-subscriber-scope');
    }

    /**
     * Invalid planner payloads return field-level validation errors.
     */
    public function test_react_render_optimization_api_validates_payload(): void
    {
        $this->postJson('/api/practice/react-render-optimization-plan', [
            'component_name' => '<bad>',
            'component_type' => 'canvas',
            'render_issue' => 'everything',
            'state_shape' => 'server-state',
            'list_size' => 0,
            'profiler_signal' => 'fast',
        ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors([
                'component_name',
                'component_type',
                'render_issue',
                'state_shape',
                'list_size',
                'profiler_signal',
            ]);
    }
}
