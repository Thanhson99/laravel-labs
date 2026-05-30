<?php

declare(strict_types=1);

namespace App\Services\Practice;

final class GraphqlRestDecisionService
{
    /**
     * Build a REST versus GraphQL decision plan from API design signals.
     *
     * @param  array{client_type: string, data_shape: string, field_flexibility: string, cache_priority: string, relationship_depth: string, team_graphql_experience: string, authorization_complexity: string}  $input
     * @return array<string, mixed>
     */
    public function plan(array $input): array
    {
        $scoreBreakdown = $this->scoreBreakdownFor($input);
        $recommendation = $this->recommendationFor($scoreBreakdown);
        $riskScore = $this->riskScoreFor($input, $recommendation['style']);
        $contractShape = $this->contractShapeFor($input, $recommendation['style']);
        $cachingPlan = $this->cachingPlanFor($input, $recommendation['style']);
        $authorizationPlan = $this->authorizationPlanFor($input, $recommendation['style']);
        $nPlusOnePlan = $this->nPlusOnePlanFor($input, $recommendation['style']);
        $tests = $this->testsFor($recommendation['style']);
        $antiPatterns = $this->antiPatternsFor($recommendation['style']);
        $migrationPath = $this->migrationPathFor($input, $recommendation['style']);
        $reconsiderationTriggers = $this->reconsiderationTriggersFor($recommendation['style']);
        $observabilityPlan = $this->observabilityPlanFor($recommendation['style']);

        return [
            'recommendation' => $recommendation,
            'score_breakdown' => $scoreBreakdown,
            'decision_matrix' => $this->decisionMatrixFor($input),
            'risk_score' => $riskScore,
            'contract_shape' => $contractShape,
            'laravel_boundaries' => $this->laravelBoundariesFor($recommendation['style']),
            'caching_plan' => $cachingPlan,
            'authorization_plan' => $authorizationPlan,
            'n_plus_one_plan' => $nPlusOnePlan,
            'anti_patterns' => $antiPatterns,
            'migration_path' => $migrationPath,
            'reconsideration_triggers' => $reconsiderationTriggers,
            'observability_plan' => $observabilityPlan,
            'implementation_plan' => $this->implementationPlanFor($recommendation['style']),
            'review_checklist' => $this->reviewChecklistFor($recommendation['style']),
            'tests' => $tests,
            'interview_answer' => $this->interviewAnswerFor($input, $recommendation['style']),
            'decision_memo_markdown' => $this->decisionMemoMarkdownFor($input, $recommendation, $scoreBreakdown, $riskScore, $contractShape, $cachingPlan, $authorizationPlan, $nPlusOnePlan, $antiPatterns, $migrationPath, $reconsiderationTriggers, $observabilityPlan, $tests),
            'commands' => [
                'php artisan route:list --path=graphql-rest-decision',
                'php artisan test --filter GraphqlRestDecision',
                'vendor\\bin\\pint --test',
            ],
        ];
    }

    /**
     * Recommend REST or GraphQL from the computed score breakdown.
     *
     * @param  array{graphql_score: int, rest_score: int, margin: int, confidence: string, winner: string, signals: array<int, array{signal: string, graphql_points: int, rest_points: int, reason: string}>}  $scoreBreakdown
     * @return array{style: string, label: string, reason: string}
     */
    private function recommendationFor(array $scoreBreakdown): array
    {
        $graphqlSignals = $scoreBreakdown['graphql_score'];
        $restSignals = $scoreBreakdown['rest_score'];

        if ($graphqlSignals > $restSignals) {
            return [
                'style' => 'graphql',
                'label' => 'Use GraphQL for this API boundary',
                'reason' => 'The client needs flexible field selection or composed graph-shaped data enough to justify schema, resolver, query-cost, caching, and authorization discipline.',
            ];
        }

        return [
            'style' => 'rest',
            'label' => 'Use REST for this API boundary',
            'reason' => 'The resource shape, HTTP caching needs, team maturity, or authorization risk favor explicit endpoint contracts and simpler operations.',
        ];
    }

    /**
     * Return transparent scoring signals behind the REST versus GraphQL recommendation.
     *
     * @param  array{client_type: string, data_shape: string, field_flexibility: string, cache_priority: string, relationship_depth: string, team_graphql_experience: string, authorization_complexity: string}  $input
     * @return array{graphql_score: int, rest_score: int, margin: int, confidence: string, winner: string, signals: array<int, array{signal: string, graphql_points: int, rest_points: int, reason: string}>}
     */
    private function scoreBreakdownFor(array $input): array
    {
        $signals = [
            [
                'signal' => 'data_shape',
                'graphql_points' => in_array($input['data_shape'], ['screen-composition', 'graph-shaped'], true) ? 2 : 0,
                'rest_points' => $input['data_shape'] === 'resource-crud' ? 2 : 0,
                'reason' => 'Screen composition and graph-shaped data favor GraphQL; simple resource CRUD favors REST.',
            ],
            [
                'signal' => 'field_flexibility',
                'graphql_points' => $input['field_flexibility'] === 'high' ? 2 : ($input['field_flexibility'] === 'medium' ? 1 : 0),
                'rest_points' => 0,
                'reason' => 'Client-selected fields are a strong GraphQL signal when the need is real.',
            ],
            [
                'signal' => 'relationship_depth',
                'graphql_points' => $input['relationship_depth'] === 'deep' ? 2 : ($input['relationship_depth'] === 'moderate' ? 1 : 0),
                'rest_points' => 0,
                'reason' => 'Nested relationships can justify GraphQL, but they also require resolver guardrails.',
            ],
            [
                'signal' => 'client_type',
                'graphql_points' => $input['client_type'] === 'bff' ? 1 : 0,
                'rest_points' => 0,
                'reason' => 'A BFF boundary can be a good GraphQL composition point.',
            ],
            [
                'signal' => 'cache_priority',
                'graphql_points' => 0,
                'rest_points' => $input['cache_priority'] === 'high' ? 2 : ($input['cache_priority'] === 'medium' ? 1 : 0),
                'reason' => 'High HTTP cache priority favors explicit REST URLs and cache headers.',
            ],
            [
                'signal' => 'team_graphql_experience',
                'graphql_points' => 0,
                'rest_points' => $input['team_graphql_experience'] === 'none' ? 2 : 0,
                'reason' => 'A team with no GraphQL experience should avoid adopting GraphQL without a pilot and guardrails.',
            ],
            [
                'signal' => 'authorization_complexity',
                'graphql_points' => 0,
                'rest_points' => $input['authorization_complexity'] === 'high' ? 1 : 0,
                'reason' => 'High authorization complexity favors simpler endpoint boundaries unless field-level checks are mature.',
            ],
        ];

        $graphqlScore = array_sum(array_column($signals, 'graphql_points'));
        $restScore = array_sum(array_column($signals, 'rest_points'));
        $margin = abs($graphqlScore - $restScore);

        return [
            'graphql_score' => $graphqlScore,
            'rest_score' => $restScore,
            'margin' => $margin,
            'confidence' => $this->confidenceFor($margin),
            'winner' => $graphqlScore > $restScore ? 'graphql' : 'rest',
            'signals' => $signals,
        ];
    }

    /**
     * Convert score margin into a decision confidence label.
     */
    private function confidenceFor(int $margin): string
    {
        return match (true) {
            $margin >= 4 => 'high',
            $margin >= 2 => 'medium',
            default => 'low',
        };
    }

    /**
     * Explain how each signal affects the decision.
     *
     * @return array<int, array{signal: string, value: string, favors: string, note: string}>
     */
    private function decisionMatrixFor(array $input): array
    {
        return [
            [
                'signal' => 'data shape',
                'value' => $input['data_shape'],
                'favors' => in_array($input['data_shape'], ['screen-composition', 'graph-shaped'], true) ? 'GraphQL' : 'REST',
                'note' => 'Composed screen data and graph-shaped relationships are stronger GraphQL signals; simple resource CRUD favors REST.',
            ],
            [
                'signal' => 'field flexibility',
                'value' => $input['field_flexibility'],
                'favors' => $input['field_flexibility'] === 'high' ? 'GraphQL' : 'REST',
                'note' => 'GraphQL is most useful when clients genuinely need to select fields instead of accepting one fixed response shape.',
            ],
            [
                'signal' => 'cache priority',
                'value' => $input['cache_priority'],
                'favors' => $input['cache_priority'] === 'high' ? 'REST' : 'Either',
                'note' => 'REST works naturally with URLs, verbs, cache headers, proxies, and CDNs; GraphQL needs operation-aware cache design.',
            ],
            [
                'signal' => 'team GraphQL experience',
                'value' => $input['team_graphql_experience'],
                'favors' => $input['team_graphql_experience'] === 'none' ? 'REST' : 'GraphQL',
                'note' => 'GraphQL is an operational commitment, not only a nicer request format.',
            ],
        ];
    }

    /**
     * Score the main operational risk of the recommended style.
     *
     * @return array{score: int, level: string, reasons: array<int, string>}
     */
    private function riskScoreFor(array $input, string $style): array
    {
        $score = $style === 'graphql' ? 35 : 20;
        $reasons = [];

        if ($style === 'graphql' && $input['relationship_depth'] === 'deep') {
            $score += 20;
            $reasons[] = 'Deep relationships increase resolver N+1 and query-cost risk.';
        }

        if ($style === 'graphql' && $input['authorization_complexity'] === 'high') {
            $score += 20;
            $reasons[] = 'High authorization complexity requires field, object, mutation, and resolver-level checks.';
        }

        if ($style === 'graphql' && $input['team_graphql_experience'] === 'none') {
            $score += 25;
            $reasons[] = 'A team without GraphQL experience can miss schema governance, resolver batching, and cache semantics.';
        }

        if ($style === 'rest' && $input['field_flexibility'] === 'high') {
            $score += 15;
            $reasons[] = 'High field flexibility can create many REST endpoints or sparse-fieldset complexity.';
        }

        if ($style === 'rest' && $input['data_shape'] === 'screen-composition') {
            $score += 15;
            $reasons[] = 'Screen composition can push REST clients into too many round trips unless a BFF or aggregate endpoint exists.';
        }

        $score = min(100, $score);

        return [
            'score' => $score,
            'level' => $score >= 70 ? 'high' : ($score >= 45 ? 'medium' : 'controlled'),
            'reasons' => $reasons === [] ? ['The selected style fits the visible constraints with manageable risk.'] : $reasons,
        ];
    }

    /**
     * Return the proposed contract shape.
     *
     * @return array{style: string, example: string, contract_owner: string}
     */
    private function contractShapeFor(array $input, string $style): array
    {
        if ($style === 'graphql') {
            return [
                'style' => 'schema-first query and mutation contract',
                'example' => 'query Dashboard { viewer { id name teams { id name projects { id title } } } }',
                'contract_owner' => 'Schema, type nullability, resolver behavior, query cost, and deprecation policy.',
            ];
        }

        return [
            'style' => 'endpoint/resource contract',
            'example' => 'GET /api/projects?include=owner,stats&fields=id,title,status',
            'contract_owner' => $input['data_shape'] === 'screen-composition'
                ? 'A BFF or aggregate endpoint with explicit response resources.'
                : 'Routes, Form Requests, API Resources, status codes, and cache headers.',
        ];
    }

    /**
     * Return Laravel file boundaries for the selected style.
     *
     * @return array<int, string>
     */
    private function laravelBoundariesFor(string $style): array
    {
        if ($style === 'graphql') {
            return [
                'Schema definitions describe client-visible types, fields, queries, mutations, and nullability.',
                'Resolvers stay thin and delegate business rules to services or actions.',
                'Policies or authorization services run inside every resolver boundary that can expose protected data.',
                'Eloquent loading strategy is planned before allowing nested relationship queries.',
            ];
        }

        return [
            'routes/api.php names stable endpoint contracts.',
            'Form Requests validate input before controller orchestration.',
            'Controllers stay thin and delegate workflows to services or actions.',
            'API Resources centralize response shape and relation inclusion rules.',
        ];
    }

    /**
     * Return cache guidance for the selected style.
     *
     * @return array{primary_strategy: string, checks: array<int, string>}
     */
    private function cachingPlanFor(array $input, string $style): array
    {
        if ($style === 'graphql') {
            return [
                'primary_strategy' => $input['cache_priority'] === 'high'
                    ? 'Use persisted queries plus operation-aware cache keys; avoid pretending POST /graphql behaves like normal REST cache.'
                    : 'Cache expensive resolver results behind explicit keys and document freshness per field group.',
                'checks' => [
                    'Persisted query IDs are part of cache keys when response caching is used.',
                    'Authorization context is included in any user-specific cache key.',
                    'Mutation responses invalidate affected field groups or projections.',
                ],
            ];
        }

        return [
            'primary_strategy' => 'Use HTTP cache headers, ETags, explicit URLs, and resource-specific invalidation where the data allows it.',
            'checks' => [
                'Cache headers match data freshness requirements.',
                'Query parameters that change output are included in cache keys.',
                'Authenticated responses avoid shared cache leakage.',
            ],
        ];
    }

    /**
     * Return authorization guidance for the selected style.
     *
     * @return array<int, string>
     */
    private function authorizationPlanFor(array $input, string $style): array
    {
        $base = [
            'Authorization decisions must live server-side and be covered by tests.',
            'Sensitive fields are excluded by policy, not only hidden by frontend code.',
        ];

        if ($style === 'graphql') {
            $base[] = 'Check authorization at query, mutation, object, field, and resolver boundaries when data exposure differs by field.';
            $base[] = $input['authorization_complexity'] === 'high'
                ? 'Add denied-field tests and partial-data behavior tests before exposing broad nested queries.'
                : 'Keep resolver authorization explicit even when endpoint-level auth already exists.';

            return $base;
        }

        $base[] = 'Authorize each endpoint before returning a resource or running a mutation.';
        $base[] = 'Test 403 behavior for list, detail, create, update, delete, and aggregate endpoints.';

        return $base;
    }

    /**
     * Return N+1 and query-cost guidance.
     *
     * @return array<int, string>
     */
    private function nPlusOnePlanFor(array $input, string $style): array
    {
        if ($style === 'graphql') {
            return [
                'Batch or eager-load resolver data before enabling nested relationship fields.',
                'Set depth, complexity, pagination, and maximum result-size limits.',
                'Log slow operations by operation name, selected fields, and resolver group.',
                $input['relationship_depth'] === 'deep'
                    ? 'Create a fixture that proves deep queries do not grow linearly per child row.'
                    : 'Still test one nested relation because shallow schemas often grow later.',
            ];
        }

        return [
            'Use eager loading for requested includes and assert query count on list/detail endpoints.',
            'Prefer explicit aggregate endpoints over making clients stitch many small requests.',
            'Paginate list endpoints and cap includes that can expand response size.',
        ];
    }

    /**
     * Return implementation phases.
     *
     * @return array<int, string>
     */
    private function implementationPlanFor(string $style): array
    {
        if ($style === 'graphql') {
            return [
                'Draft schema, nullability, query, mutation, and deprecation rules before implementing resolvers.',
                'Write resolver tests for success, denied fields, invalid input, query complexity, and N+1 guardrails.',
                'Move business logic into services so GraphQL does not become the domain layer.',
                'Add observability for operation name, resolver timing, error extension, and query complexity.',
            ];
        }

        return [
            'Draft routes, request validation, status codes, and API Resource response shape.',
            'Write feature tests for success, validation failure, forbidden access, caching headers, and pagination.',
            'Move business logic into services and keep controllers as transport orchestration.',
            'Add logs and metrics by endpoint, status code, latency, and client.',
        ];
    }

    /**
     * Return review checks.
     *
     * @return array<int, array{area: string, question: string}>
     */
    private function reviewChecklistFor(string $style): array
    {
        $checks = [
            ['area' => 'contract', 'question' => 'Is the API contract stable enough for the clients that will consume it?'],
            ['area' => 'authorization', 'question' => 'Can a client access data it should not see by changing fields, includes, or IDs?'],
            ['area' => 'performance', 'question' => 'Does the design avoid accidental N+1 queries and unbounded payload size?'],
        ];

        $checks[] = $style === 'graphql'
            ? ['area' => 'operations', 'question' => 'Are query depth, complexity, persisted queries, caching, and error extensions defined?']
            : ['area' => 'HTTP', 'question' => 'Are verbs, status codes, cache headers, pagination, and versioning rules clear?'];

        return $checks;
    }

    /**
     * Return verification tests.
     *
     * @return array<int, string>
     */
    private function testsFor(string $style): array
    {
        if ($style === 'graphql') {
            return [
                'Schema query returns only requested fields.',
                'Unauthorized field or mutation is rejected or omitted according to the contract.',
                'Nested query stays within query-count and complexity limits.',
                'Validation and resolver errors use the standardized GraphQL error extension shape.',
            ];
        }

        return [
            'Endpoint returns documented status codes and response resource shape.',
            'Validation failure returns stable 422 field errors.',
            'Forbidden access returns 403 before data is exposed.',
            'List endpoints paginate and include cache headers when cacheable.',
        ];
    }

    /**
     * Return common design mistakes for the selected API style.
     *
     * @return array<int, array{pattern: string, risk: string, correction: string}>
     */
    private function antiPatternsFor(string $style): array
    {
        if ($style === 'graphql') {
            return [
                [
                    'pattern' => 'GraphQL as a database tunnel',
                    'risk' => 'Clients can shape expensive nested reads that bypass use-case boundaries and surprise the database.',
                    'correction' => 'Expose task-focused schema fields, cap depth and complexity, and keep business rules in services.',
                ],
                [
                    'pattern' => 'Endpoint-level auth only',
                    'risk' => 'A user can query fields or nested relations that the endpoint middleware did not intend to expose.',
                    'correction' => 'Authorize fields, objects, mutations, and resolver services where data visibility differs.',
                ],
                [
                    'pattern' => 'Resolver per row',
                    'risk' => 'Nested lists create N+1 query storms under normal dashboard usage.',
                    'correction' => 'Batch, eager-load, paginate, and test query counts for representative nested operations.',
                ],
            ];
        }

        return [
            [
                'pattern' => 'Endpoint explosion',
                'risk' => 'Every screen gets a bespoke endpoint and the API becomes hard to document, cache, and version.',
                'correction' => 'Group around stable resources or explicit BFF aggregates with documented response resources.',
            ],
            [
                'pattern' => 'Overfetching by default',
                'risk' => 'Clients receive large fixed payloads and later add more endpoints or ad hoc field flags.',
                'correction' => 'Use API Resources, includes, pagination, or a small aggregate endpoint when payload shape really differs.',
            ],
            [
                'pattern' => 'Status-code drift',
                'risk' => 'Clients cannot reliably distinguish validation, auth, missing resource, conflict, and async acceptance cases.',
                'correction' => 'Standardize status codes, error bodies, validation errors, and idempotency behavior.',
            ],
        ];
    }

    /**
     * Return a low-risk migration or rollout path for the selected API style.
     *
     * @return array<int, array{phase: string, action: string, exit_signal: string}>
     */
    private function migrationPathFor(array $input, string $style): array
    {
        if ($style === 'graphql') {
            return [
                [
                    'phase' => 'pilot schema',
                    'action' => 'Start with one read-only screen or BFF boundary that has clear field-selection pain.',
                    'exit_signal' => 'One client screen uses the schema without adding hidden REST fallbacks.',
                ],
                [
                    'phase' => 'guardrails',
                    'action' => 'Add auth checks, query depth, complexity limits, pagination rules, and resolver timing logs before broad usage.',
                    'exit_signal' => 'Representative nested queries pass query-count, latency, and denial tests.',
                ],
                [
                    'phase' => 'contract governance',
                    'action' => 'Document schema ownership, deprecation policy, naming rules, nullability rules, and mutation error shape.',
                    'exit_signal' => 'Schema changes require review and clients can see deprecation guidance.',
                ],
            ];
        }

        return [
            [
                'phase' => 'contract inventory',
                'action' => 'List required resources, client screens, includes, status codes, cache behavior, and error body shape.',
                'exit_signal' => 'Every endpoint has an owner, request contract, response resource, and test case.',
            ],
            [
                'phase' => 'endpoint consolidation',
                'action' => $input['data_shape'] === 'screen-composition'
                    ? 'Create one explicit aggregate endpoint or BFF endpoint instead of many hidden client round trips.'
                    : 'Keep endpoints resource-oriented and avoid screen-specific variants unless the use case requires them.',
                'exit_signal' => 'Client round trips and payload size are measured and accepted.',
            ],
            [
                'phase' => 'operational hardening',
                'action' => 'Add cache headers, pagination limits, idempotency rules, auth tests, and endpoint-level metrics.',
                'exit_signal' => 'Feature tests and monitoring cover success, validation failure, forbidden access, and slow requests.',
            ],
        ];
    }

    /**
     * Return signals that should cause the team to revisit the API style decision.
     *
     * @return array<int, array{trigger: string, why_it_matters: string, next_review: string}>
     */
    private function reconsiderationTriggersFor(string $style): array
    {
        if ($style === 'graphql') {
            return [
                [
                    'trigger' => 'Resolver latency or query count grows with nested list size.',
                    'why_it_matters' => 'The schema may be exposing flexible graph traversal without enough batching, limits, or purpose-built fields.',
                    'next_review' => 'Review query complexity, dataloader/eager-loading behavior, and whether some fields should become explicit aggregate endpoints.',
                ],
                [
                    'trigger' => 'Authorization rules differ heavily by field or nested object.',
                    'why_it_matters' => 'GraphQL can quietly expose data through fields that endpoint-level middleware never checked.',
                    'next_review' => 'Add denied-field tests and review resolver-level policy coverage before adding more fields.',
                ],
                [
                    'trigger' => 'Clients mostly use fixed queries with no field-selection benefit.',
                    'why_it_matters' => 'The team may be paying GraphQL operational cost without using the flexibility that justified it.',
                    'next_review' => 'Compare against REST or BFF endpoints for the top fixed workflows.',
                ],
            ];
        }

        return [
            [
                'trigger' => 'Clients add many round trips to compose one screen.',
                'why_it_matters' => 'REST may be forcing client-side orchestration where a BFF, aggregate endpoint, or GraphQL boundary would be clearer.',
                'next_review' => 'Measure round trips, payload size, and duplicate client composition logic.',
            ],
            [
                'trigger' => 'Endpoint variants multiply around field selection.',
                'why_it_matters' => 'Endpoint explosion can make REST harder to document, cache, version, and secure.',
                'next_review' => 'Review includes, sparse fieldsets, aggregate resources, or a small GraphQL pilot.',
            ],
            [
                'trigger' => 'Mobile payload size becomes a recurring performance issue.',
                'why_it_matters' => 'Fixed REST response shapes may be overfetching enough to justify client-selected fields.',
                'next_review' => 'Compare response size, cache hit rate, and screen-specific field requirements.',
            ],
        ];
    }

    /**
     * Return production signals that prove the API style is healthy after release.
     *
     * @return array{metrics: array<int, string>, log_events: array<int, string>, alerts: array<int, string>}
     */
    private function observabilityPlanFor(string $style): array
    {
        if ($style === 'graphql') {
            return [
                'metrics' => [
                    'graphql_operation_latency_ms by operation_name and client',
                    'graphql_resolver_latency_ms by resolver_group',
                    'graphql_query_complexity by operation_name',
                    'graphql_denied_field_total by field and client',
                    'graphql_n_plus_one_guard_total by operation_name',
                ],
                'log_events' => [
                    'graphql_operation_started with operation_name, client_id, depth, complexity, and selected_field_count',
                    'graphql_resolver_slow with resolver_group, duration_ms, query_count, and operation_name',
                    'graphql_authorization_denied with field, object_type, policy, and client_id',
                ],
                'alerts' => [
                    'Alert when p95 operation latency crosses the API SLO for two release windows.',
                    'Alert when query complexity or depth exceeds the approved threshold.',
                    'Alert when denied-field events spike after schema changes.',
                ],
            ];
        }

        return [
            'metrics' => [
                'http_request_latency_ms by route, method, status, and client',
                'http_response_payload_bytes by route and client',
                'http_cache_hit_ratio by route',
                'http_validation_error_total by route and field',
                'http_forbidden_total by route and policy',
            ],
            'log_events' => [
                'api_request_completed with route, method, status, duration_ms, client_id, and correlation_id',
                'api_contract_error with route, validation_fields, status, and client_id',
                'api_cache_decision with route, cache_key_hash, hit, freshness_seconds, and client_id',
            ],
            'alerts' => [
                'Alert when p95 route latency crosses the endpoint SLO.',
                'Alert when payload size grows above the documented contract budget.',
                'Alert when cache hit ratio drops suddenly on cacheable endpoints.',
            ],
        ];
    }

    /**
     * Return a copyable ADR-style decision memo for docs, PRs, or review notes.
     *
     * @param  array{client_type: string, data_shape: string, field_flexibility: string, cache_priority: string, relationship_depth: string, team_graphql_experience: string, authorization_complexity: string}  $input
     * @param  array{style: string, label: string, reason: string}  $recommendation
     * @param  array{graphql_score: int, rest_score: int, margin: int, confidence: string, winner: string, signals: array<int, array{signal: string, graphql_points: int, rest_points: int, reason: string}>}  $scoreBreakdown
     * @param  array{score: int, level: string, reasons: array<int, string>}  $riskScore
     * @param  array{style: string, example: string, contract_owner: string}  $contractShape
     * @param  array{primary_strategy: string, checks: array<int, string>}  $cachingPlan
     * @param  array<int, string>  $authorizationPlan
     * @param  array<int, string>  $nPlusOnePlan
     * @param  array<int, array{pattern: string, risk: string, correction: string}>  $antiPatterns
     * @param  array<int, array{phase: string, action: string, exit_signal: string}>  $migrationPath
     * @param  array<int, array{trigger: string, why_it_matters: string, next_review: string}>  $reconsiderationTriggers
     * @param  array{metrics: array<int, string>, log_events: array<int, string>, alerts: array<int, string>}  $observabilityPlan
     * @param  array<int, string>  $tests
     */
    private function decisionMemoMarkdownFor(array $input, array $recommendation, array $scoreBreakdown, array $riskScore, array $contractShape, array $cachingPlan, array $authorizationPlan, array $nPlusOnePlan, array $antiPatterns, array $migrationPath, array $reconsiderationTriggers, array $observabilityPlan, array $tests): string
    {
        $lines = [
            '# API Style Decision: REST vs GraphQL',
            '',
            "Decision: {$recommendation['label']}",
            '',
            'Context:',
            "- Client type: {$input['client_type']}",
            "- Data shape: {$input['data_shape']}",
            "- Field flexibility: {$input['field_flexibility']}",
            "- Cache priority: {$input['cache_priority']}",
            "- Relationship depth: {$input['relationship_depth']}",
            "- Team GraphQL experience: {$input['team_graphql_experience']}",
            "- Authorization complexity: {$input['authorization_complexity']}",
            '',
            'Reason:',
            $recommendation['reason'],
            '',
            'Decision Score:',
            "- GraphQL: {$scoreBreakdown['graphql_score']}",
            "- REST: {$scoreBreakdown['rest_score']}",
            "- Margin: {$scoreBreakdown['margin']}",
            "- Confidence: {$scoreBreakdown['confidence']}",
            ...$this->scoreSignalMarkdownBullets($scoreBreakdown['signals']),
            '',
            'Contract Shape:',
            "- Style: {$contractShape['style']}",
            "- Example: `{$contractShape['example']}`",
            "- Owner: {$contractShape['contract_owner']}",
            '',
            'Risk:',
            "- Level: {$riskScore['level']} ({$riskScore['score']}/100)",
            ...$this->markdownBullets($riskScore['reasons']),
            '',
            'Cache Plan:',
            "- {$cachingPlan['primary_strategy']}",
            ...$this->markdownBullets($cachingPlan['checks']),
            '',
            'Authorization Plan:',
            ...$this->markdownBullets($authorizationPlan),
            '',
            'Performance / N+1 Plan:',
            ...$this->markdownBullets($nPlusOnePlan),
            '',
            'Anti-Patterns To Avoid:',
            ...$this->antiPatternMarkdownBullets($antiPatterns),
            '',
            'Migration Path:',
            ...$this->migrationMarkdownBullets($migrationPath),
            '',
            'Reconsider This Decision When:',
            ...$this->reconsiderationMarkdownBullets($reconsiderationTriggers),
            '',
            'Observability Plan:',
            ...$this->markdownBullets($observabilityPlan['metrics']),
            ...$this->markdownBullets($observabilityPlan['alerts']),
            '',
            'Verification Tests:',
            ...$this->markdownBullets($tests),
        ];

        return implode("\n", $lines);
    }

    /**
     * Format a list as markdown bullets.
     *
     * @param  array<int, string>  $items
     * @return array<int, string>
     */
    private function markdownBullets(array $items): array
    {
        return array_map(
            fn (string $item): string => "- {$item}",
            $items,
        );
    }

    /**
     * Format anti-patterns as markdown bullets.
     *
     * @param  array<int, array{pattern: string, risk: string, correction: string}>  $items
     * @return array<int, string>
     */
    private function antiPatternMarkdownBullets(array $items): array
    {
        return array_map(
            fn (array $item): string => "- {$item['pattern']}: {$item['risk']} Correction: {$item['correction']}",
            $items,
        );
    }

    /**
     * Format migration phases as markdown bullets.
     *
     * @param  array<int, array{phase: string, action: string, exit_signal: string}>  $items
     * @return array<int, string>
     */
    private function migrationMarkdownBullets(array $items): array
    {
        return array_map(
            fn (array $item): string => "- {$item['phase']}: {$item['action']} Exit: {$item['exit_signal']}",
            $items,
        );
    }

    /**
     * Format reconsideration triggers as markdown bullets.
     *
     * @param  array<int, array{trigger: string, why_it_matters: string, next_review: string}>  $items
     * @return array<int, string>
     */
    private function reconsiderationMarkdownBullets(array $items): array
    {
        return array_map(
            fn (array $item): string => "- {$item['trigger']} Why: {$item['why_it_matters']} Review: {$item['next_review']}",
            $items,
        );
    }

    /**
     * Format score signals as markdown bullets.
     *
     * @param  array<int, array{signal: string, graphql_points: int, rest_points: int, reason: string}>  $items
     * @return array<int, string>
     */
    private function scoreSignalMarkdownBullets(array $items): array
    {
        return array_map(
            fn (array $item): string => "- {$item['signal']}: GraphQL +{$item['graphql_points']}, REST +{$item['rest_points']}. {$item['reason']}",
            $items,
        );
    }

    /**
     * Return an interview-ready explanation.
     */
    private function interviewAnswerFor(array $input, string $style): string
    {
        $choice = $style === 'graphql' ? 'GraphQL' : 'REST';

        return "I would choose {$choice} for this case. REST is endpoint/resource-oriented and maps naturally to HTTP verbs, status codes, cache headers, and simple resource contracts. GraphQL is schema/query-oriented and lets clients select fields and compose related data, but it requires stronger resolver performance controls, query-cost limits, cache design, and field-level authorization. For this context, data_shape={$input['data_shape']}, field_flexibility={$input['field_flexibility']}, cache_priority={$input['cache_priority']}, and relationship_depth={$input['relationship_depth']} are the main deciding signals.";
    }
}
