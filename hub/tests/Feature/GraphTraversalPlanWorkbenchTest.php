<?php

declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;

final class GraphTraversalPlanWorkbenchTest extends TestCase
{
    /**
     * The graph traversal workbench renders the BFS/DFS planning UI.
     */
    public function test_graph_traversal_plan_workbench_renders(): void
    {
        $response = $this->get('/workbench/graph-traversal-plan');

        $response
            ->assertOk()
            ->assertSee('Graph Traversal Plan Workbench')
            ->assertSee('POST /api/practice/graph-traversal-plan')
            ->assertSee('GraphTraversalPlanService')
            ->assertSee('Scenario preset')
            ->assertSee('Database category tree')
            ->assertSee('Weighted path warning')
            ->assertSee('GraphTraversalPlanServiceTest')
            ->assertSee('Plan traversal');
    }

    /**
     * The graph traversal API recommends BFS for nearest-match API crawling.
     */
    public function test_graph_traversal_plan_api_returns_bfs_plan(): void
    {
        $response = $this->postJson('/api/practice/graph-traversal-plan', [
            'scenario_name' => 'API Resource Crawl',
            'goal' => 'nearest-match',
            'graph_shape' => 'api-links',
            'node_count' => 500,
            'max_depth' => 4,
            'weighted_edges' => false,
            'production_context' => 'api-crawling',
        ]);

        $response
            ->assertOk()
            ->assertJsonPath('data.scenario', 'Api Resource Crawl')
            ->assertJsonPath('data.recommended_strategy', 'bfs')
            ->assertJsonPath('data.state_model', 'Use a queue frontier, visit nodes level by level, and stop when the nearest valid match is found.')
            ->assertJsonPath('data.reason', 'BFS reaches closer nodes before deeper nodes, so it fits nearest-match work.')
            ->assertJsonPath('data.decision_matrix.0.signal', 'Goal')
            ->assertJsonPath('data.traversal_plan.data_structure', 'queue')
            ->assertJsonPath('data.traversal_plan.steps.0', 'Seed the queue with the start node and mark it visited.')
            ->assertJsonPath('data.complexity_profile.time_complexity', 'BFS is O(V + E) when each node and edge is visited at most once.')
            ->assertJsonPath('data.complexity_profile.production_limit', 'Cap queue expansion at 500 nodes and depth 4.')
            ->assertJsonPath('data.risk_assessment.level', 'medium')
            ->assertJsonPath('data.risk_assessment.score', 40)
            ->assertJsonPath('data.risk_assessment.reasons.0', 'BFS on wide or link-heavy graphs can grow a large frontier in memory.')
            ->assertJsonPath('data.implementation_examples.primary.label', 'Queue-based BFS sketch')
            ->assertJsonPath('data.implementation_examples.guard.label', 'BFS guard')
            ->assertJsonPath('data.production_checklist.0.area', 'termination')
            ->assertJsonPath('data.production_checklist.2.area', 'pagination')
            ->assertJsonPath('data.production_checklist.3.check', 'Throttle each BFS level so wide frontiers do not burst external APIs.')
            ->assertJsonPath('data.observability_plan.metrics.2', 'graph_traversal_queue_frontier_size')
            ->assertJsonPath('data.observability_plan.alerts.0', 'Alert when visited_count reaches 500 before finding a valid stop condition.')
            ->assertJsonPath('data.observability_plan.runbook.0', 'Check stop_reason first: found target, exhausted graph, hit max depth, hit max nodes, timeout, or cycle detected.')
            ->assertJsonPath('data.guardrails.0', 'Maintain a visited set keyed by stable node ID.')
            ->assertJsonPath('data.guardrails.1', 'Stop traversal at depth 4 unless the caller explicitly raises the limit.')
            ->assertJsonPath('data.api_database_examples.0.context', 'api-crawling')
            ->assertJsonPath('data.failure_modes.0', 'Skipping a visited set can create repeated work or infinite loops on cyclic graphs.')
            ->assertJsonPath('data.tests.0', 'Assert BFS order for a known fixture when the goal is nearest or shortest unweighted path.')
            ->assertJsonPath('data.commands.0', 'php artisan test --filter GraphTraversalPlanWorkbenchTest');
    }

    /**
     * The graph traversal API warns when weighted shortest path needs a weighted algorithm.
     */
    public function test_graph_traversal_plan_api_returns_weighted_path_warning(): void
    {
        $response = $this->postJson('/api/practice/graph-traversal-plan', [
            'scenario_name' => 'Weighted Service Path',
            'goal' => 'shortest-path',
            'graph_shape' => 'cyclic',
            'node_count' => 250,
            'max_depth' => 6,
            'weighted_edges' => true,
            'production_context' => 'dependency-graph',
        ]);

        $response
            ->assertOk()
            ->assertJsonPath('data.recommended_strategy', 'weighted-shortest-path-warning')
            ->assertJsonPath('data.traversal_plan.data_structure', 'priority queue')
            ->assertJsonPath('data.complexity_profile.time_complexity', 'Dijkstra-style traversal is usually O((V + E) log V) with a priority queue.')
            ->assertJsonPath('data.risk_assessment.level', 'high')
            ->assertJsonPath('data.risk_assessment.score', 70)
            ->assertJsonPath('data.implementation_examples.primary.label', 'Priority queue weighted path sketch')
            ->assertJsonPath('data.production_checklist.2.area', 'determinism')
            ->assertJsonPath('data.observability_plan.metrics.2', 'graph_traversal_priority_queue_size')
            ->assertJsonPath('data.reason', 'Shortest path with weighted edges needs weighted-path logic because plain BFS only proves shortest path by hop count.');
    }

    /**
     * Invalid graph traversal payloads return validation errors.
     */
    public function test_graph_traversal_plan_api_validates_payload(): void
    {
        $response = $this->postJson('/api/practice/graph-traversal-plan', [
            'scenario_name' => '<bad>',
            'goal' => 'random',
            'graph_shape' => 'flat',
            'node_count' => 1,
            'max_depth' => 0,
            'weighted_edges' => 'maybe',
            'production_context' => 'unknown',
        ]);

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors([
                'scenario_name',
                'goal',
                'graph_shape',
                'node_count',
                'max_depth',
                'weighted_edges',
                'production_context',
            ]);
    }
}
