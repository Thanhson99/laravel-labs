<?php

declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;

final class GraphqlRestDecisionWorkbenchTest extends TestCase
{
    /**
     * The GraphQL REST decision workbench renders the planning form.
     */
    public function test_graphql_rest_decision_workbench_renders(): void
    {
        $response = $this->get('/workbench/graphql-rest-decision');

        $response
            ->assertOk()
            ->assertSee('GraphQL REST Decision Workbench')
            ->assertSee('POST /api/practice/graphql-rest-decision')
            ->assertSee('GraphqlRestDecisionService')
            ->assertSee('SPA dashboard with composed data')
            ->assertSee('Decision Summary')
            ->assertSee('Run the planner to see recommendation')
            ->assertSee('Decision Memo')
            ->assertSee('Copy memo')
            ->assertSee('renderGraphqlRestSummary')
            ->assertSee('escapeGraphqlRestHtml')
            ->assertSee('replaceAll', false)
            ->assertSee('Plan API style');
    }

    /**
     * Graph-shaped client data recommends GraphQL with resolver guardrails.
     */
    public function test_graphql_rest_decision_api_recommends_graphql_for_composed_dashboard(): void
    {
        $response = $this->postJson('/api/practice/graphql-rest-decision', [
            'client_type' => 'spa-dashboard',
            'data_shape' => 'screen-composition',
            'field_flexibility' => 'high',
            'cache_priority' => 'low',
            'relationship_depth' => 'deep',
            'team_graphql_experience' => 'some',
            'authorization_complexity' => 'medium',
        ]);

        $response
            ->assertOk()
            ->assertJsonPath('data.recommendation.style', 'graphql')
            ->assertJsonPath('data.score_breakdown.graphql_score', 6)
            ->assertJsonPath('data.score_breakdown.rest_score', 0)
            ->assertJsonPath('data.score_breakdown.confidence', 'high')
            ->assertJsonPath('data.score_breakdown.winner', 'graphql')
            ->assertJsonPath('data.score_breakdown.signals.0.signal', 'data_shape')
            ->assertJsonPath('data.decision_matrix.0.favors', 'GraphQL')
            ->assertJsonPath('data.contract_shape.style', 'schema-first query and mutation contract')
            ->assertJsonPath('data.laravel_boundaries.0', 'Schema definitions describe client-visible types, fields, queries, mutations, and nullability.')
            ->assertJsonPath('data.caching_plan.checks.0', 'Persisted query IDs are part of cache keys when response caching is used.')
            ->assertJsonPath('data.authorization_plan.2', 'Check authorization at query, mutation, object, field, and resolver boundaries when data exposure differs by field.')
            ->assertJsonPath('data.n_plus_one_plan.0', 'Batch or eager-load resolver data before enabling nested relationship fields.')
            ->assertJsonPath('data.anti_patterns.0.pattern', 'GraphQL as a database tunnel')
            ->assertJsonPath('data.migration_path.0.phase', 'pilot schema')
            ->assertJsonPath('data.reconsideration_triggers.0.trigger', 'Resolver latency or query count grows with nested list size.')
            ->assertJsonPath('data.observability_plan.metrics.0', 'graphql_operation_latency_ms by operation_name and client')
            ->assertJsonPath('data.observability_plan.alerts.0', 'Alert when p95 operation latency crosses the API SLO for two release windows.')
            ->assertJsonPath('data.implementation_plan.0', 'Draft schema, nullability, query, mutation, and deprecation rules before implementing resolvers.')
            ->assertJsonPath('data.tests.0', 'Schema query returns only requested fields.')
            ->assertJsonPath('data.decision_memo_markdown', fn (string $memo): bool => str_contains($memo, '# API Style Decision: REST vs GraphQL')
                && str_contains($memo, 'Decision: Use GraphQL for this API boundary')
                && str_contains($memo, 'Decision Score:')
                && str_contains($memo, 'Anti-Patterns To Avoid:')
                && str_contains($memo, 'Migration Path:')
                && str_contains($memo, 'Reconsider This Decision When:')
                && str_contains($memo, 'Observability Plan:'))
            ->assertJsonPath('data.commands.1', 'php artisan test --filter GraphqlRestDecision');
    }

    /**
     * Resource CRUD with high cache priority recommends REST.
     */
    public function test_graphql_rest_decision_api_recommends_rest_for_public_crud(): void
    {
        $response = $this->postJson('/api/practice/graphql-rest-decision', [
            'client_type' => 'public-api',
            'data_shape' => 'resource-crud',
            'field_flexibility' => 'low',
            'cache_priority' => 'high',
            'relationship_depth' => 'shallow',
            'team_graphql_experience' => 'none',
            'authorization_complexity' => 'medium',
        ]);

        $response
            ->assertOk()
            ->assertJsonPath('data.recommendation.style', 'rest')
            ->assertJsonPath('data.score_breakdown.graphql_score', 0)
            ->assertJsonPath('data.score_breakdown.rest_score', 6)
            ->assertJsonPath('data.score_breakdown.confidence', 'high')
            ->assertJsonPath('data.score_breakdown.winner', 'rest')
            ->assertJsonPath('data.contract_shape.style', 'endpoint/resource contract')
            ->assertJsonPath('data.caching_plan.primary_strategy', 'Use HTTP cache headers, ETags, explicit URLs, and resource-specific invalidation where the data allows it.')
            ->assertJsonPath('data.laravel_boundaries.0', 'routes/api.php names stable endpoint contracts.')
            ->assertJsonPath('data.anti_patterns.0.pattern', 'Endpoint explosion')
            ->assertJsonPath('data.migration_path.0.phase', 'contract inventory')
            ->assertJsonPath('data.reconsideration_triggers.0.trigger', 'Clients add many round trips to compose one screen.')
            ->assertJsonPath('data.observability_plan.metrics.0', 'http_request_latency_ms by route, method, status, and client')
            ->assertJsonPath('data.tests.0', 'Endpoint returns documented status codes and response resource shape.')
            ->assertJsonPath('data.decision_memo_markdown', fn (string $memo): bool => str_contains($memo, 'Decision: Use REST for this API boundary')
                && str_contains($memo, 'Cache Plan:')
                && str_contains($memo, 'GET /api/projects'))
            ->assertJsonPath('data.review_checklist.3.area', 'HTTP');
    }

    /**
     * Invalid planning payloads return validation errors.
     */
    public function test_graphql_rest_decision_api_validates_payload(): void
    {
        $response = $this->postJson('/api/practice/graphql-rest-decision', [
            'client_type' => 'desktop',
            'data_shape' => 'unknown',
            'field_flexibility' => 'huge',
            'cache_priority' => 'critical',
            'relationship_depth' => 'recursive',
            'team_graphql_experience' => 'maybe',
            'authorization_complexity' => 'wild',
        ]);

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors([
                'client_type',
                'data_shape',
                'field_flexibility',
                'cache_priority',
                'relationship_depth',
                'team_graphql_experience',
                'authorization_complexity',
            ]);
    }
}
