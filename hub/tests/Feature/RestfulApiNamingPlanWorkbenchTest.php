<?php

declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;

final class RestfulApiNamingPlanWorkbenchTest extends TestCase
{
    /**
     * The RESTful API naming workbench renders the planning form.
     */
    public function test_restful_api_naming_workbench_renders(): void
    {
        $response = $this->get('/workbench/restful-api-naming-plan');

        $response
            ->assertOk()
            ->assertSee('RESTful API Naming Workbench')
            ->assertSee('POST /api/practice/restful-api-naming-plan')
            ->assertSee('RestfulApiNamingPlanService')
            ->assertSee('Plan endpoint names')
            ->assertSee('Route Summary')
            ->assertSee('Quality Review')
            ->assertSee('Contract Artifact')
            ->assertSee('Implementation Blueprint')
            ->assertSee('Migration Plan')
            ->assertSee('Response Contract')
            ->assertSee('Operational Readiness')
            ->assertSee('Naming Rubric')
            ->assertSee('Client Examples')
            ->assertSee('Copy memo')
            ->assertSee('Copy OpenAPI')
            ->assertSee('renderRestfulNamingSummary')
            ->assertSee('renderRestfulNamingQuality')
            ->assertSee('renderRestfulNamingContract')
            ->assertSee('renderRestfulNamingBlueprint')
            ->assertSee('renderRestfulNamingMigration')
            ->assertSee('renderRestfulNamingResponseContract')
            ->assertSee('renderRestfulNamingReadiness')
            ->assertSee('renderRestfulNamingRubric')
            ->assertSee('renderRestfulNamingClients')
            ->assertSee('escapeRestfulNamingHtml')
            ->assertSee('replaceAll', false);
    }

    /**
     * The API returns nested collection routes, query parameters, and a memo.
     */
    public function test_restful_api_naming_api_returns_nested_collection_plan(): void
    {
        $response = $this->postJson('/api/practice/restful-api-naming-plan', [
            'resource_name' => 'Order',
            'parent_resource' => 'User',
            'current_endpoint' => '/api/v1/getUserOrders/paid/recent',
            'operation_type' => 'read',
            'needs_filtering' => true,
            'needs_business_action' => true,
            'version' => 'v1',
        ]);

        $response
            ->assertOk()
            ->assertJsonPath('data.resource.name', 'orders')
            ->assertJsonPath('data.resource.parent', 'users')
            ->assertJsonPath('data.recommendation.base_path', '/api/v1/users/{user}/orders')
            ->assertJsonPath('data.routes.0.method', 'GET')
            ->assertJsonPath('data.routes.0.path', '/api/v1/users/{user}/orders')
            ->assertJsonPath('data.routes.0.route_name', 'users.orders.index')
            ->assertJsonPath('data.routes.2.method', 'POST')
            ->assertJsonPath('data.routes.2.path', '/api/v1/users/{user}/orders/{order}/approve')
            ->assertJsonPath('data.query_parameters.0.name', 'status')
            ->assertJsonPath('data.quality_review.score', 55)
            ->assertJsonPath('data.quality_review.target_endpoint', '/api/v1/users/{user}/orders')
            ->assertJsonPath('data.quality_review.smells.0', 'Path segment contains an action verb; let the HTTP method carry CRUD intent.')
            ->assertJsonPath('data.quality_review.smells.1', 'Path appears deeply nested; consider a flatter resource path plus query filters.')
            ->assertJsonPath('data.quality_review.smells.2', 'Filter-like values are embedded in the path; move them to query parameters.')
            ->assertJsonPath('data.contract_artifacts.openapi_path', '/api/v1/users/{user}/orders')
            ->assertJsonPath('data.contract_artifacts.route_list_expectations.0', 'GET /api/v1/users/{user}/orders -> users.orders.index')
            ->assertJsonPath('data.contract_artifacts.consumer_contract_checks.1', 'Keep operationId values stable so generated clients and API docs do not churn.')
            ->assertJsonPath('data.contract_artifacts.openapi_yaml', fn (string $yaml): bool => str_contains($yaml, 'operationId: getUsersOrdersIndex')
                && str_contains($yaml, 'name: status')
                && str_contains($yaml, 'POST /api/v1/users/{user}/orders/{order}/approve') === false)
            ->assertJsonPath('data.implementation_blueprint.controller', 'OrderController')
            ->assertJsonPath('data.implementation_blueprint.form_requests.0', 'IndexOrderRequest')
            ->assertJsonPath('data.implementation_blueprint.policy_abilities.0', 'index')
            ->assertJsonPath('data.implementation_blueprint.policy_abilities.2', 'approve')
            ->assertJsonPath('data.implementation_blueprint.route_model_binding.1', 'Scope child `orders` records under parent `users` before returning a model.')
            ->assertJsonPath('data.implementation_blueprint.implementation_order.3', 'Implement the controller as orchestration only and move workflow decisions into a service/action.')
            ->assertJsonPath('data.migration_plan.requires_deprecation', true)
            ->assertJsonPath('data.migration_plan.legacy_endpoint', '/api/v1/getUserOrders/paid/recent')
            ->assertJsonPath('data.migration_plan.target_endpoint', '/api/v1/users/{user}/orders')
            ->assertJsonPath('data.migration_plan.phases.1.phase', 'warn')
            ->assertJsonPath('data.migration_plan.response_headers.0.name', 'Deprecation')
            ->assertJsonPath('data.migration_plan.response_headers.1.value', '</api/v1/users/{user}/orders>; rel="successor-version"')
            ->assertJsonPath('data.migration_plan.release_notes.1', 'Use `/api/v1/users/{user}/orders` for new integrations.')
            ->assertJsonPath('data.response_contract.success_responses.0.route_name', 'users.orders.index')
            ->assertJsonPath('data.response_contract.success_responses.0.status', 200)
            ->assertJsonPath('data.response_contract.success_responses.0.envelope.meta.pagination', true)
            ->assertJsonPath('data.response_contract.error_responses.2.status', 422)
            ->assertJsonPath('data.response_contract.error_responses.2.shape.message', 'The given data was invalid.')
            ->assertJsonPath('data.response_contract.pagination.strategy', 'cursor-or-length-aware-pagination')
            ->assertJsonPath('data.response_contract.headers.2.name', 'X-Request-Id')
            ->assertJsonPath('data.operational_readiness.logs.1.field', 'route_name')
            ->assertJsonPath('data.operational_readiness.metrics.0', 'api_requests_total{route_name,status}')
            ->assertJsonPath('data.operational_readiness.alerts.1', 'Alert when p95 latency regresses after enabling filters or business actions.')
            ->assertJsonPath('data.operational_readiness.acceptance_checks.3', 'Collection filters are allowlisted and covered by query-parameter tests.')
            ->assertJsonPath('data.operational_readiness.acceptance_checks.4', 'Domain action endpoints emit an audit event with actor, target resource, and outcome.')
            ->assertJsonPath('data.operational_readiness.dashboard_panels.3', 'Legacy endpoint traffic during migration windows.')
            ->assertJsonPath('data.naming_rubric.total_score', 73)
            ->assertJsonPath('data.naming_rubric.max_score', 100)
            ->assertJsonPath('data.naming_rubric.grade', 'revise')
            ->assertJsonPath('data.naming_rubric.criteria.0.name', 'Resource noun path')
            ->assertJsonPath('data.naming_rubric.criteria.2.score', 12)
            ->assertJsonPath('data.naming_rubric.criteria.4.recommendation', 'When replacing an endpoint, dual-route first and communicate deprecation headers.')
            ->assertJsonPath('data.client_examples.0.route_name', 'users.orders.index')
            ->assertJsonPath('data.client_examples.0.path', '/api/v1/users/123/orders?status=paid&sort=-created_at&page=1&per_page=15')
            ->assertJsonPath('data.client_examples.0.curl', fn (string $curl): bool => str_contains($curl, "curl -X GET 'https://api.example.test/api/v1/users/123/orders?status=paid&sort=-created_at&page=1&per_page=15'"))
            ->assertJsonPath('data.client_examples.2.httpie', fn (string $httpie): bool => str_contains($httpie, "http POST 'https://api.example.test/api/v1/users/123/orders/456/approve'"))
            ->assertJsonPath('data.client_examples.2.fetch', fn (string $fetch): bool => str_contains($fetch, '"method": "POST"')
                && str_contains($fetch, 'Created from RESTful naming workbench.'))
            ->assertJsonPath('data.review_checklist.4', 'Filters, search, sort, and pagination stay in query parameters.')
            ->assertJsonPath('data.anti_patterns.0', '`POST /getOrders` duplicates the verb and hides read semantics.')
            ->assertJsonPath('data.tests.3', 'Assert the domain action endpoint changes state only through `POST`.')
            ->assertJsonPath('data.decision_memo_markdown', fn (string $memo): bool => str_contains($memo, '# RESTful API Naming Plan')
                && str_contains($memo, 'Base path: /api/v1/users/{user}/orders')
                && str_contains($memo, 'POST /api/v1/users/{user}/orders/{order}/approve'))
            ->assertJsonPath('data.commands.1', 'php artisan test --filter RestfulApiNamingPlanWorkbenchTest');
    }

    /**
     * Invalid planning payloads return validation errors.
     */
    public function test_restful_api_naming_api_validates_payload(): void
    {
        $response = $this->postJson('/api/practice/restful-api-naming-plan', [
            'resource_name' => 'x',
            'operation_type' => 'merge',
            'needs_filtering' => 'sometimes',
            'needs_business_action' => 'maybe',
            'version' => 'v9',
        ]);

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors([
                'resource_name',
                'operation_type',
                'needs_filtering',
                'needs_business_action',
                'version',
            ]);
    }
}
