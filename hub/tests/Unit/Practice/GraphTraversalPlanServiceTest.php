<?php

declare(strict_types=1);

namespace Tests\Unit\Practice;

use App\Services\Practice\GraphTraversalPlanService;
use PHPUnit\Framework\TestCase;

final class GraphTraversalPlanServiceTest extends TestCase
{
    /**
     * Nearest-match plans choose BFS and describe queue memory pressure.
     */
    public function test_nearest_match_plan_chooses_bfs_with_frontier_complexity(): void
    {
        $plan = (new GraphTraversalPlanService)->plan([
            'scenario_name' => 'Nearest API Resource',
            'goal' => 'nearest-match',
            'graph_shape' => 'wide',
            'node_count' => 1200,
            'max_depth' => 5,
            'weighted_edges' => false,
            'production_context' => 'api-crawling',
        ]);

        $this->assertSame('bfs', $plan['recommended_strategy']);
        $this->assertSame('queue', $plan['traversal_plan']['data_structure']);
        $this->assertSame('BFS is O(V + E) when each node and edge is visited at most once.', $plan['complexity_profile']['time_complexity']);
        $this->assertSame('Queue frontier can grow quickly on wide graphs because many same-depth nodes are held at once.', $plan['complexity_profile']['memory_pressure']);
        $this->assertSame('Cap queue expansion at 1200 nodes and depth 5.', $plan['complexity_profile']['production_limit']);
        $this->assertSame('medium', $plan['risk_assessment']['level']);
        $this->assertSame(40, $plan['risk_assessment']['score']);
        $this->assertContains('BFS on wide or link-heavy graphs can grow a large frontier in memory.', $plan['risk_assessment']['reasons']);
        $this->assertSame('Queue-based BFS sketch', $plan['implementation_examples']['primary']['label']);
        $this->assertStringContainsString('array_shift($queue)', $plan['implementation_examples']['primary']['code']);
        $this->assertStringContainsString('Track visited nodes before enqueueing', $plan['implementation_examples']['guard']['code']);
        $this->assertSame('termination', $plan['production_checklist'][0]['area']);
        $this->assertSame('pagination', $plan['production_checklist'][2]['area']);
        $this->assertSame('Throttle each BFS level so wide frontiers do not burst external APIs.', $plan['production_checklist'][3]['check']);
        $this->assertContains('graph_traversal_queue_frontier_size', $plan['observability_plan']['metrics']);
        $this->assertContains('frontier_size', $plan['observability_plan']['log_fields']);
        $this->assertSame('Alert when BFS frontier size grows faster than nodes completed for two consecutive samples.', $plan['observability_plan']['alerts'][2]);
        $this->assertContains('Paginate API crawling and rate-limit each hop or level.', $plan['guardrails']);
    }

    /**
     * Subtree validation plans choose DFS and warn about deep recursion.
     */
    public function test_subtree_validation_plan_chooses_dfs_with_depth_complexity(): void
    {
        $plan = (new GraphTraversalPlanService)->plan([
            'scenario_name' => 'Category Tree Validation',
            'goal' => 'subtree-validation',
            'graph_shape' => 'tree',
            'node_count' => 2000,
            'max_depth' => 8,
            'weighted_edges' => false,
            'production_context' => 'database-hierarchy',
        ]);

        $this->assertSame('dfs', $plan['recommended_strategy']);
        $this->assertSame('stack or recursion', $plan['traversal_plan']['data_structure']);
        $this->assertSame('DFS is O(V + E) when each node and edge is visited at most once.', $plan['complexity_profile']['time_complexity']);
        $this->assertSame('Stack or recursion memory is usually tied to depth, so very deep graphs risk overflow or long branches.', $plan['complexity_profile']['memory_pressure']);
        $this->assertSame('Cap branch depth at 8 and stop after 2000 visited nodes.', $plan['complexity_profile']['production_limit']);
        $this->assertSame('low', $plan['risk_assessment']['level']);
        $this->assertSame(35, $plan['risk_assessment']['score']);
        $this->assertContains('DFS on deep hierarchies can hit depth limits or recursion-style failure modes.', $plan['risk_assessment']['reasons']);
        $this->assertSame('Explicit-stack DFS sketch', $plan['implementation_examples']['primary']['label']);
        $this->assertStringContainsString('array_pop($stack)', $plan['implementation_examples']['primary']['code']);
        $this->assertStringContainsString('Use an explicit stack for untrusted depth', $plan['implementation_examples']['guard']['code']);
        $this->assertSame('query shape', $plan['production_checklist'][2]['area']);
        $this->assertSame('Index parent_id and any ordering column used while expanding or validating the tree.', $plan['production_checklist'][3]['check']);
        $this->assertContains('graph_traversal_stack_depth', $plan['observability_plan']['metrics']);
        $this->assertSame('Alert when DFS branch depth approaches the configured max depth or recursion fallback is attempted.', $plan['observability_plan']['alerts'][2]);
        $this->assertSame('Use level batching for expandable trees or DFS for subtree validation and nested menu rendering.', $plan['api_database_examples'][1]['recommendation']);
    }

    /**
     * Weighted shortest-path plans avoid plain BFS claims and require priority-queue behavior.
     */
    public function test_weighted_shortest_path_plan_warns_against_plain_bfs(): void
    {
        $plan = (new GraphTraversalPlanService)->plan([
            'scenario_name' => 'Weighted Dependency Path',
            'goal' => 'shortest-path',
            'graph_shape' => 'cyclic',
            'node_count' => 250,
            'max_depth' => 6,
            'weighted_edges' => true,
            'production_context' => 'dependency-graph',
        ]);

        $this->assertSame('weighted-shortest-path-warning', $plan['recommended_strategy']);
        $this->assertSame('priority queue', $plan['traversal_plan']['data_structure']);
        $this->assertSame('Dijkstra-style traversal is usually O((V + E) log V) with a priority queue.', $plan['complexity_profile']['time_complexity']);
        $this->assertSame('Cap candidates at 250 nodes, depth 6, and a documented cost budget.', $plan['complexity_profile']['production_limit']);
        $this->assertSame('high', $plan['risk_assessment']['level']);
        $this->assertSame(70, $plan['risk_assessment']['score']);
        $this->assertContains('Weighted shortest path cannot rely on plain BFS and needs priority-queue cost handling.', $plan['risk_assessment']['reasons']);
        $this->assertContains('Cyclic graphs require a reliable visited set and cycle evidence.', $plan['risk_assessment']['reasons']);
        $this->assertSame('Priority queue weighted path sketch', $plan['implementation_examples']['primary']['label']);
        $this->assertStringContainsString('SplPriorityQueue', $plan['implementation_examples']['primary']['code']);
        $this->assertStringContainsString('Reject plain BFS when edge costs differ', $plan['implementation_examples']['guard']['code']);
        $this->assertSame('determinism', $plan['production_checklist'][2]['area']);
        $this->assertSame('Record the path that introduced a dependency, cycle, or weighted cost conflict.', $plan['production_checklist'][3]['check']);
        $this->assertContains('graph_traversal_priority_queue_size', $plan['observability_plan']['metrics']);
        $this->assertSame('Alert when stale priority-queue entries or cost updates exceed the expected candidate count.', $plan['observability_plan']['alerts'][2]);
        $this->assertContains('Weighted shortest path needs Dijkstra or another weighted algorithm, not plain BFS.', $plan['failure_modes']);
    }

    /**
     * Menu rendering plans keep traversal work outside Blade rendering.
     */
    public function test_menu_rendering_plan_adds_render_contract_checklist(): void
    {
        $plan = (new GraphTraversalPlanService)->plan([
            'scenario_name' => 'Nested Menu Rendering',
            'goal' => 'branch-exploration',
            'graph_shape' => 'tree',
            'node_count' => 150,
            'max_depth' => 4,
            'weighted_edges' => false,
            'production_context' => 'menu-rendering',
        ]);

        $this->assertSame('dfs', $plan['recommended_strategy']);
        $this->assertSame('render contract', $plan['production_checklist'][2]['area']);
        $this->assertSame('Preload the tree data and keep traversal output separate from Blade rendering logic.', $plan['production_checklist'][2]['check']);
        $this->assertSame('Keep sibling order deterministic so menu output and snapshots remain stable.', $plan['production_checklist'][3]['check']);
        $this->assertSame('Check stop_reason first: found target, exhausted graph, hit max depth, hit max nodes, timeout, or cycle detected.', $plan['observability_plan']['runbook'][0]);
    }

    /**
     * Large traversal inputs increase risk and recommend queued processing.
     */
    public function test_large_traversal_inputs_are_high_risk(): void
    {
        $plan = (new GraphTraversalPlanService)->plan([
            'scenario_name' => 'Large API Crawl',
            'goal' => 'nearest-match',
            'graph_shape' => 'api-links',
            'node_count' => 25000,
            'max_depth' => 12,
            'weighted_edges' => false,
            'production_context' => 'api-crawling',
        ]);

        $this->assertSame('high', $plan['risk_assessment']['level']);
        $this->assertSame(70, $plan['risk_assessment']['score']);
        $this->assertContains('Large node limits should run outside latency-sensitive request paths.', $plan['risk_assessment']['reasons']);
        $this->assertContains('Move traversal to a queued job when node count, frontier size, or latency exceeds request budgets.', $plan['risk_assessment']['mitigations']);
    }
}
