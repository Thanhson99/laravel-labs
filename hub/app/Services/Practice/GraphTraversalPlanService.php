<?php

declare(strict_types=1);

namespace App\Services\Practice;

use Illuminate\Support\Str;

/**
 * Builds bounded BFS, DFS, and weighted-path decision plans for practice workbenches.
 */
final class GraphTraversalPlanService
{
    /**
     * Build a BFS/DFS traversal decision plan for API and database practice.
     *
     * @param  array{scenario_name: string, goal: string, graph_shape: string, node_count: int, max_depth: int, weighted_edges: bool, production_context: string}  $input
     * @return array<string, mixed>
     */
    public function plan(array $input): array
    {
        $strategy = $this->strategyFor($input['goal'], $input['weighted_edges']);

        return [
            'scenario' => Str::headline($input['scenario_name']),
            'recommended_strategy' => $strategy,
            'state_model' => $this->stateModel($strategy),
            'reason' => $this->reason($strategy, $input['goal'], $input['weighted_edges']),
            'decision_matrix' => $this->decisionMatrix($input, $strategy),
            'traversal_plan' => $this->traversalPlan($strategy, $input),
            'complexity_profile' => $this->complexityProfile($strategy, $input),
            'risk_assessment' => $this->riskAssessment($strategy, $input),
            'implementation_examples' => $this->implementationExamples($strategy),
            'guardrails' => $this->guardrails($input),
            'production_checklist' => $this->productionChecklist($input['production_context'], $strategy),
            'observability_plan' => $this->observabilityPlan($strategy, $input),
            'api_database_examples' => $this->examples($strategy, $input['production_context']),
            'failure_modes' => [
                'Skipping a visited set can create repeated work or infinite loops on cyclic graphs.',
                'Using DFS for nearest result can return a deep match while a closer match exists.',
                'Using BFS on a very wide graph can hold a large frontier in memory.',
                'Recursive DFS can overflow or time out when hierarchy depth is not bounded.',
                'Weighted shortest path needs Dijkstra or another weighted algorithm, not plain BFS.',
            ],
            'interview_answer' => $this->interviewAnswer($strategy, $input),
            'tests' => [
                'Assert BFS order for a known fixture when the goal is nearest or shortest unweighted path.',
                'Assert DFS order for a known fixture when the goal is branch exploration or subtree validation.',
                'Assert cyclic graphs terminate through a visited set.',
                'Assert max depth and max node limits stop traversal before production limits are exceeded.',
            ],
            'commands' => [
                'php artisan test --filter GraphTraversalPlanWorkbenchTest',
                'php artisan route:list --path=graph-traversal-plan',
                'vendor\\bin\\pint --test',
            ],
        ];
    }

    /**
     * Score traversal risk from graph shape, scale, depth, and algorithm constraints.
     *
     * @return array{level: string, score: int, reasons: array<int, string>, mitigations: array<int, string>}
     */
    private function riskAssessment(string $strategy, array $input): array
    {
        $score = 20;
        $reasons = [];

        if ($strategy === 'weighted-shortest-path-warning') {
            $score += 35;
            $reasons[] = 'Weighted shortest path cannot rely on plain BFS and needs priority-queue cost handling.';
        }

        if ($strategy === 'bfs' && in_array($input['graph_shape'], ['wide', 'api-links'], true)) {
            $score += 20;
            $reasons[] = 'BFS on wide or link-heavy graphs can grow a large frontier in memory.';
        }

        if ($strategy === 'dfs' && in_array($input['graph_shape'], ['deep', 'tree'], true)) {
            $score += 15;
            $reasons[] = 'DFS on deep hierarchies can hit depth limits or recursion-style failure modes.';
        }

        if ($input['graph_shape'] === 'cyclic') {
            $score += 15;
            $reasons[] = 'Cyclic graphs require a reliable visited set and cycle evidence.';
        }

        if ($input['node_count'] >= 10000) {
            $score += 20;
            $reasons[] = 'Large node limits should run outside latency-sensitive request paths.';
        }

        if ($input['max_depth'] >= 10) {
            $score += 10;
            $reasons[] = 'High depth limits increase traversal time and stack or frontier pressure.';
        }

        $score = min(100, $score);

        return [
            'level' => match (true) {
                $score >= 70 => 'high',
                $score >= 40 => 'medium',
                default => 'low',
            },
            'score' => $score,
            'reasons' => $reasons === [] ? ['Bounded traversal with explicit limits stays low risk for this practice input.'] : $reasons,
            'mitigations' => [
                'Keep visited set, max depth, max nodes, timeout, and stop reason mandatory.',
                'Move traversal to a queued job when node count, frontier size, or latency exceeds request budgets.',
                'Capture traversal metrics and path evidence before raising limits.',
            ],
        ];
    }

    /**
     * Return metrics, logs, alerts, and triage steps for traversal operations.
     *
     * @return array{metrics: array<int, string>, log_fields: array<int, string>, alerts: array<int, string>, runbook: array<int, string>}
     */
    private function observabilityPlan(string $strategy, array $input): array
    {
        $frontierMetric = match ($strategy) {
            'bfs' => 'graph_traversal_queue_frontier_size',
            'weighted-shortest-path-warning' => 'graph_traversal_priority_queue_size',
            default => 'graph_traversal_stack_depth',
        };

        $strategyAlert = match ($strategy) {
            'bfs' => 'Alert when BFS frontier size grows faster than nodes completed for two consecutive samples.',
            'weighted-shortest-path-warning' => 'Alert when stale priority-queue entries or cost updates exceed the expected candidate count.',
            default => 'Alert when DFS branch depth approaches the configured max depth or recursion fallback is attempted.',
        };

        return [
            'metrics' => [
                'graph_traversal_nodes_visited_total',
                'graph_traversal_edges_examined_total',
                $frontierMetric,
                'graph_traversal_stop_reason_total',
                'graph_traversal_duration_ms',
            ],
            'log_fields' => [
                'scenario',
                'strategy',
                'production_context',
                'start_node_id',
                'current_depth',
                'visited_count',
                'frontier_size',
                'stop_reason',
            ],
            'alerts' => [
                "Alert when visited_count reaches {$input['node_count']} before finding a valid stop condition.",
                "Alert when current_depth reaches {$input['max_depth']} for {$input['production_context']} traversal.",
                $strategyAlert,
            ],
            'runbook' => [
                'Check stop_reason first: found target, exhausted graph, hit max depth, hit max nodes, timeout, or cycle detected.',
                'Inspect visited_count versus frontier_size to separate broad fan-out from deep traversal.',
                'Sample the logged path or parent chain that led to the slowest or failed node.',
                'Lower max depth, add batching, or move traversal to a queued job when request latency exceeds the budget.',
            ],
        ];
    }

    /**
     * Return context-specific checks before using traversal logic in production.
     *
     * @return array<int, array{area: string, check: string}>
     */
    private function productionChecklist(string $context, string $strategy): array
    {
        $shared = [
            [
                'area' => 'termination',
                'check' => 'Define max depth, max nodes, timeout, and stop reason before traversal starts.',
            ],
            [
                'area' => 'cycle safety',
                'check' => 'Key the visited set by stable node ID, URL, database key, or dependency name.',
            ],
        ];

        $specific = match ($context) {
            'api-crawling' => [
                [
                    'area' => 'pagination',
                    'check' => 'Persist cursor, page token, depth, and visited IDs so a retry does not restart the crawl.',
                ],
                [
                    'area' => 'rate limit',
                    'check' => $strategy === 'bfs'
                        ? 'Throttle each BFS level so wide frontiers do not burst external APIs.'
                        : 'Throttle each branch and add per-host concurrency limits before deep crawling.',
                ],
            ],
            'database-hierarchy' => [
                [
                    'area' => 'query shape',
                    'check' => 'Batch children by parent IDs and select only ID, parent ID, sort key, and fields required by the traversal.',
                ],
                [
                    'area' => 'indexing',
                    'check' => 'Index parent_id and any ordering column used while expanding or validating the tree.',
                ],
            ],
            'dependency-graph' => [
                [
                    'area' => 'determinism',
                    'check' => 'Sort dependencies before traversal so output order is stable across machines and test runs.',
                ],
                [
                    'area' => 'conflict evidence',
                    'check' => 'Record the path that introduced a dependency, cycle, or weighted cost conflict.',
                ],
            ],
            default => [
                [
                    'area' => 'render contract',
                    'check' => 'Preload the tree data and keep traversal output separate from Blade rendering logic.',
                ],
                [
                    'area' => 'ordering',
                    'check' => 'Keep sibling order deterministic so menu output and snapshots remain stable.',
                ],
            ],
        };

        return [...$shared, ...$specific];
    }

    /**
     * Return implementation-oriented PHP examples for the selected traversal strategy.
     *
     * @return array{primary: array{label: string, code: string}, guard: array{label: string, code: string}}
     */
    private function implementationExamples(string $strategy): array
    {
        if ($strategy === 'weighted-shortest-path-warning') {
            return [
                'primary' => [
                    'label' => 'Priority queue weighted path sketch',
                    'code' => <<<'PHP'
$queue = new SplPriorityQueue();
$queue->insert($start, 0);
$distance = [$start => 0];

while (! $queue->isEmpty()) {
    $node = $queue->extract();

    foreach ($weightedGraph[$node] ?? [] as $next => $cost) {
        $candidate = $distance[$node] + $cost;

        if (! isset($distance[$next]) || $candidate < $distance[$next]) {
            $distance[$next] = $candidate;
            $queue->insert($next, -$candidate);
        }
    }
}
PHP,
                ],
                'guard' => [
                    'label' => 'Weighted-path guard',
                    'code' => 'Reject plain BFS when edge costs differ; require a cost budget, settled-node tracking, and stale-priority handling.',
                ],
            ];
        }

        if ($strategy === 'bfs') {
            return [
                'primary' => [
                    'label' => 'Queue-based BFS sketch',
                    'code' => <<<'PHP'
$queue = [[$start, 0]];
$visited = [$start => true];

while ($queue !== []) {
    [$node, $depth] = array_shift($queue);

    foreach ($graph[$node] ?? [] as $next) {
        if (! isset($visited[$next])) {
            $visited[$next] = true;
            $queue[] = [$next, $depth + 1];
        }
    }
}
PHP,
                ],
                'guard' => [
                    'label' => 'BFS guard',
                    'code' => 'Track visited nodes before enqueueing, then stop when max depth, max nodes, timeout, or nearest match is reached.',
                ],
            ];
        }

        return [
            'primary' => [
                'label' => 'Explicit-stack DFS sketch',
                'code' => <<<'PHP'
$stack = [[$start, 0]];
$visited = [];

while ($stack !== []) {
    [$node, $depth] = array_pop($stack);

    if (isset($visited[$node])) {
        continue;
    }

    $visited[$node] = true;

    foreach (array_reverse($graph[$node] ?? []) as $next) {
        $stack[] = [$next, $depth + 1];
    }
}
PHP,
            ],
            'guard' => [
                'label' => 'DFS guard',
                'code' => 'Use an explicit stack for untrusted depth, record visited nodes, and stop when max depth or branch validation fails.',
            ],
        ];
    }

    /**
     * Return time, memory, and production scaling notes for the selected traversal.
     *
     * @return array{time_complexity: string, memory_pressure: string, scale_warning: string, production_limit: string}
     */
    private function complexityProfile(string $strategy, array $input): array
    {
        if ($strategy === 'weighted-shortest-path-warning') {
            return [
                'time_complexity' => 'Dijkstra-style traversal is usually O((V + E) log V) with a priority queue.',
                'memory_pressure' => 'Priority queue and distance map can grow with discovered nodes and candidate edges.',
                'scale_warning' => 'Weighted paths require cost modeling, stale-cost handling, and explicit limits before production use.',
                'production_limit' => "Cap candidates at {$input['node_count']} nodes, depth {$input['max_depth']}, and a documented cost budget.",
            ];
        }

        if ($strategy === 'bfs') {
            return [
                'time_complexity' => 'BFS is O(V + E) when each node and edge is visited at most once.',
                'memory_pressure' => 'Queue frontier can grow quickly on wide graphs because many same-depth nodes are held at once.',
                'scale_warning' => 'Use batching, pagination, and background jobs when the frontier can exceed request memory.',
                'production_limit' => "Cap queue expansion at {$input['node_count']} nodes and depth {$input['max_depth']}.",
            ];
        }

        return [
            'time_complexity' => 'DFS is O(V + E) when each node and edge is visited at most once.',
            'memory_pressure' => 'Stack or recursion memory is usually tied to depth, so very deep graphs risk overflow or long branches.',
            'scale_warning' => 'Prefer an explicit stack over recursion when hierarchy depth is user-controlled or untrusted.',
            'production_limit' => "Cap branch depth at {$input['max_depth']} and stop after {$input['node_count']} visited nodes.",
        ];
    }

    private function strategyFor(string $goal, bool $weightedEdges): string
    {
        if ($weightedEdges && $goal === 'shortest-path') {
            return 'weighted-shortest-path-warning';
        }

        return in_array($goal, ['nearest-match', 'shortest-path'], true) ? 'bfs' : 'dfs';
    }

    private function stateModel(string $strategy): string
    {
        return match ($strategy) {
            'bfs' => 'Use a queue frontier, visit nodes level by level, and stop when the nearest valid match is found.',
            'weighted-shortest-path-warning' => 'Plain BFS is not enough for weighted edges; use Dijkstra-style priority queue behavior.',
            default => 'Use a stack or recursion path, visit one branch deeply, and backtrack after each branch is complete.',
        };
    }

    private function reason(string $strategy, string $goal, bool $weightedEdges): string
    {
        if ($strategy === 'weighted-shortest-path-warning') {
            return 'Shortest path with weighted edges needs weighted-path logic because plain BFS only proves shortest path by hop count.';
        }

        if ($strategy === 'bfs') {
            return match ($goal) {
                'shortest-path' => 'BFS finds shortest path by hop count when edges are unweighted.',
                default => 'BFS reaches closer nodes before deeper nodes, so it fits nearest-match work.',
            };
        }

        return 'DFS fits depth-first branch exploration, dependency reasoning, subtree validation, and backtracking work.';
    }

    /**
     * @return array<int, array{signal: string, value: string, impact: string}>
     */
    private function decisionMatrix(array $input, string $strategy): array
    {
        return [
            [
                'signal' => 'Goal',
                'value' => $input['goal'],
                'impact' => $strategy === 'bfs' ? 'Favor level-order traversal.' : 'Favor depth-first traversal or a weighted-path alternative.',
            ],
            [
                'signal' => 'Graph shape',
                'value' => $input['graph_shape'],
                'impact' => $input['graph_shape'] === 'wide' ? 'Watch BFS frontier memory.' : 'Watch DFS depth and recursion risk.',
            ],
            [
                'signal' => 'Scale',
                'value' => "{$input['node_count']} nodes, depth {$input['max_depth']}",
                'impact' => 'Set max nodes and max depth before crawling production data.',
            ],
            [
                'signal' => 'Context',
                'value' => $input['production_context'],
                'impact' => 'Translate algorithm choice into API pagination, database batching, or dependency checks.',
            ],
        ];
    }

    /**
     * @return array{steps: array<int, string>, data_structure: string, stop_condition: string}
     */
    private function traversalPlan(string $strategy, array $input): array
    {
        if ($strategy === 'bfs') {
            return [
                'data_structure' => 'queue',
                'stop_condition' => 'Stop when the nearest target is found or max nodes/depth is reached.',
                'steps' => [
                    'Seed the queue with the start node and mark it visited.',
                    'Pop the next node from the front of the queue.',
                    'Push unvisited neighbors to the back of the queue with depth metadata.',
                    'Stop early when the target is found at the shallowest level.',
                ],
            ];
        }

        if ($strategy === 'weighted-shortest-path-warning') {
            return [
                'data_structure' => 'priority queue',
                'stop_condition' => 'Stop when the lowest-cost target path is settled or the cost budget is exceeded.',
                'steps' => [
                    'Store cumulative edge cost for each candidate path.',
                    'Visit the lowest-cost candidate before higher-cost candidates.',
                    'Update a node only when a cheaper path is found.',
                    'Do not claim plain BFS shortest-path behavior when edge costs differ.',
                ],
            ];
        }

        return [
            'data_structure' => 'stack or recursion',
            'stop_condition' => 'Stop when the branch is complete, validation fails, or max depth is reached.',
            'steps' => [
                'Seed the stack or recursive call with the start node and mark it visited.',
                'Visit one neighbor branch before siblings.',
                'Backtrack after the branch is complete or invalid.',
                'Record path evidence for dependency or subtree validation.',
            ],
        ];
    }

    /**
     * @return array<int, string>
     */
    private function guardrails(array $input): array
    {
        return [
            'Maintain a visited set keyed by stable node ID.',
            "Stop traversal at depth {$input['max_depth']} unless the caller explicitly raises the limit.",
            "Stop after {$input['node_count']} candidate nodes or switch to queued/background processing.",
            'Batch database hierarchy reads by parent IDs instead of issuing one query per node.',
            'Paginate API crawling and rate-limit each hop or level.',
            'Log stop reason: found target, exhausted graph, hit max depth, hit max nodes, timeout, or invalid cycle.',
        ];
    }

    /**
     * @return array<int, array{context: string, recommendation: string}>
     */
    private function examples(string $strategy, string $context): array
    {
        return [
            [
                'context' => 'api-crawling',
                'recommendation' => $strategy === 'bfs'
                    ? 'Crawl linked resources by hop count and stop at the closest match.'
                    : 'Use depth-first traversal only when each branch must be fully inspected before moving to the next branch.',
            ],
            [
                'context' => 'database-hierarchy',
                'recommendation' => $context === 'database-hierarchy'
                    ? 'Use level batching for expandable trees or DFS for subtree validation and nested menu rendering.'
                    : 'Document how the same visited-set and depth-limit guardrails would apply to hierarchy reads.',
            ],
        ];
    }

    private function interviewAnswer(string $strategy, array $input): string
    {
        return match ($strategy) {
            'bfs' => 'I choose BFS when the goal is nearest match or shortest path in an unweighted graph because queue-based level order reaches closer nodes first. I still add visited set, max depth, max nodes, pagination, rate limits, and memory guardrails.',
            'weighted-shortest-path-warning' => 'I would not use plain BFS for weighted shortest path. I would use Dijkstra-style priority queue logic, then keep the same production guardrails for cycles, depth, node count, pagination, and observability.',
            default => 'I choose DFS when the goal is branch exploration, dependency reasoning, subtree validation, or backtracking because stack or recursion follows one branch deeply. I still bound depth, track visited nodes, and document API or database limits.',
        };
    }
}
