<?php

declare(strict_types=1);

namespace App\Services\Practice;

use Illuminate\Support\Str;

final class RestfulApiNamingPlanService
{
    /**
     * Build a RESTful endpoint naming plan from a feature description.
     *
     * @param  array{resource_name: string, parent_resource?: string|null, current_endpoint?: string|null, operation_type: string, needs_filtering: bool, needs_business_action: bool, version: string}  $input
     * @return array<string, mixed>
     */
    public function plan(array $input): array
    {
        $resource = $this->resourceSlug($input['resource_name']);
        $parent = $this->parentSlug($input['parent_resource'] ?? null);
        $version = $this->versionPrefix($input['version']);
        $parentParameter = $parent === null ? null : $this->singularParameter($parent);
        $basePath = $parent === null
            ? "/api/{$version}/{$resource}"
            : "/api/{$version}/{$parent}/{{$parentParameter}}/{$resource}";

        $routes = $this->routesFor($basePath, $resource, $parent, $input);

        return [
            'resource' => [
                'name' => $resource,
                'singular_parameter' => $this->singularParameter($resource),
                'parent' => $parent,
                'version' => $version,
            ],
            'recommendation' => [
                'style' => 'restful-resource-naming',
                'summary' => $this->summaryFor($resource, $parent, $input),
                'base_path' => $basePath,
            ],
            'routes' => $routes,
            'query_parameters' => $this->queryParametersFor($input['needs_filtering']),
            'quality_review' => $this->qualityReviewFor($basePath, $input),
            'laravel_route_example' => $this->laravelRouteExampleFor($resource, $parent, $input),
            'contract_artifacts' => $this->contractArtifactsFor($basePath, $routes, $resource, $input),
            'implementation_blueprint' => $this->implementationBlueprintFor($routes, $resource, $parent, $input),
            'migration_plan' => $this->migrationPlanFor($basePath, $input),
            'response_contract' => $this->responseContractFor($routes, $resource, $input),
            'operational_readiness' => $this->operationalReadinessFor($routes, $resource, $input),
            'naming_rubric' => $this->namingRubricFor($basePath, $input),
            'client_examples' => $this->clientExamplesFor($routes, $input),
            'review_checklist' => $this->reviewChecklistFor($input),
            'anti_patterns' => $this->antiPatternsFor(),
            'tests' => $this->testsFor($resource, $input),
            'decision_memo_markdown' => $this->decisionMemoFor($resource, $basePath, $routes, $input),
            'commands' => [
                'php artisan route:list --path=restful-api-naming-plan',
                'php artisan test --filter RestfulApiNamingPlanWorkbenchTest',
                'vendor\\bin\\pint --test',
            ],
        ];
    }

    /**
     * Return copy-ready client examples for the generated route set.
     *
     * @param  array<int, array{method: string, path: string, route_name: string, purpose: string}>  $routes
     * @return array<int, array{route_name: string, method: string, path: string, curl: string, httpie: string, fetch: string}>
     */
    private function clientExamplesFor(array $routes, array $input): array
    {
        return collect($routes)
            ->map(function (array $route) use ($input): array {
                $path = $this->examplePathFor($route['path'], $input);
                $body = $this->exampleBodyFor($route['method']);
                $query = $route['method'] === 'GET' && $input['needs_filtering']
                    ? '?status=paid&sort=-created_at&page=1&per_page=15'
                    : '';
                $url = "https://api.example.test{$path}{$query}";

                return [
                    'route_name' => $route['route_name'],
                    'method' => $route['method'],
                    'path' => $path.$query,
                    'curl' => $this->curlExampleFor($route['method'], $url, $body),
                    'httpie' => $this->httpieExampleFor($route['method'], $url, $body),
                    'fetch' => $this->fetchExampleFor($route['method'], $url, $body),
                ];
            })
            ->values()
            ->all();
    }

    /**
     * Replace route parameters with stable example identifiers.
     */
    private function examplePathFor(string $path, array $input): string
    {
        return preg_replace_callback('/\{([a-z_]+)\}/', function (array $matches) use ($input): string {
            $parameter = $matches[1];

            if (str_contains($parameter, 'user')) {
                return '123';
            }

            if ($input['operation_type'] === 'action') {
                return '789';
            }

            return '456';
        }, $path) ?? $path;
    }

    /**
     * Return a compact JSON body for write examples.
     *
     * @return array<string, mixed>|null
     */
    private function exampleBodyFor(string $method): ?array
    {
        return match ($method) {
            'POST' => ['status' => 'draft', 'notes' => 'Created from RESTful naming workbench.'],
            'PATCH' => ['status' => 'active'],
            default => null,
        };
    }

    /**
     * Build a curl example.
     *
     * @param  array<string, mixed>|null  $body
     */
    private function curlExampleFor(string $method, string $url, ?array $body): string
    {
        $command = "curl -X {$method} '{$url}' \\\n  -H 'Accept: application/json' \\\n  -H 'Authorization: Bearer <token>'";

        if ($body !== null) {
            $command .= " \\\n  -H 'Content-Type: application/json' \\\n  -d '".json_encode($body, JSON_THROW_ON_ERROR)."'";
        }

        return $command;
    }

    /**
     * Build an HTTPie example.
     *
     * @param  array<string, mixed>|null  $body
     */
    private function httpieExampleFor(string $method, string $url, ?array $body): string
    {
        $command = "http {$method} '{$url}' Accept:application/json Authorization:'Bearer <token>'";

        if ($body !== null) {
            foreach ($body as $key => $value) {
                $command .= " {$key}='{$value}'";
            }
        }

        return $command;
    }

    /**
     * Build a browser fetch example.
     *
     * @param  array<string, mixed>|null  $body
     */
    private function fetchExampleFor(string $method, string $url, ?array $body): string
    {
        $options = [
            'method' => $method,
            'headers' => [
                'Accept' => 'application/json',
                'Authorization' => 'Bearer <token>',
            ],
        ];

        if ($body !== null) {
            $options['headers']['Content-Type'] = 'application/json';
            $options['body'] = json_encode($body, JSON_THROW_ON_ERROR);
        }

        return 'await fetch('.json_encode($url, JSON_THROW_ON_ERROR).', '.json_encode($options, JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR).');';
    }

    /**
     * Return a weighted rubric for reviewing endpoint naming during PR review.
     *
     * @return array{total_score: int, max_score: int, grade: string, criteria: array<int, array{name: string, weight: int, score: int, evidence: string, recommendation: string}>}
     */
    private function namingRubricFor(string $basePath, array $input): array
    {
        $quality = $this->qualityReviewFor($basePath, $input);
        $hasSmells = $quality['smells'] !== [];
        $criteria = [
            [
                'name' => 'Resource noun path',
                'weight' => 25,
                'score' => $hasSmells ? 15 : 25,
                'evidence' => $hasSmells ? implode(' ', $quality['smells']) : "Generated path `{$basePath}` uses resource nouns.",
                'recommendation' => 'Prefer plural nouns and stable path parameters over action-heavy URL segments.',
            ],
            [
                'name' => 'HTTP method semantics',
                'weight' => 20,
                'score' => $input['operation_type'] === 'action' || ! $hasSmells ? 20 : 14,
                'evidence' => "Operation type is `{$input['operation_type']}`.",
                'recommendation' => 'Let GET, POST, PATCH, and DELETE communicate normal CRUD intent.',
            ],
            [
                'name' => 'Query parameter discipline',
                'weight' => 20,
                'score' => $input['needs_filtering'] && $hasSmells ? 12 : 20,
                'evidence' => $input['needs_filtering'] ? 'Filtering is required for this endpoint.' : 'Filtering is not required for this endpoint.',
                'recommendation' => 'Move filter, sort, search, and pagination concerns into query parameters.',
            ],
            [
                'name' => 'Laravel route name stability',
                'weight' => 20,
                'score' => 20,
                'evidence' => 'Generated route names are derived from normalized resource vocabulary.',
                'recommendation' => 'Use stable route names in tests, docs, logs, and support workflows.',
            ],
            [
                'name' => 'Migration safety',
                'weight' => 15,
                'score' => filled($input['current_endpoint'] ?? null) ? 12 : 15,
                'evidence' => filled($input['current_endpoint'] ?? null)
                    ? 'Existing endpoint requires a deprecation and migration story.'
                    : 'No existing endpoint was supplied.',
                'recommendation' => 'When replacing an endpoint, dual-route first and communicate deprecation headers.',
            ],
        ];
        $total = collect($criteria)->sum('score');

        return [
            'total_score' => $total,
            'max_score' => collect($criteria)->sum('weight'),
            'grade' => match (true) {
                $total >= 90 => 'ready',
                $total >= 75 => 'review',
                default => 'revise',
            },
            'criteria' => $criteria,
        ];
    }

    /**
     * Return release-readiness signals for the endpoint contract.
     *
     * @param  array<int, array{method: string, path: string, route_name: string, purpose: string}>  $routes
     * @return array{logs: array<int, array{field: string, purpose: string}>, metrics: array<int, string>, alerts: array<int, string>, acceptance_checks: array<int, string>, dashboard_panels: array<int, string>}
     */
    private function operationalReadinessFor(array $routes, string $resource, array $input): array
    {
        $routeNames = collect($routes)->pluck('route_name')->implode(', ');

        $acceptanceChecks = [
            "Route names are present in `php artisan route:list`: {$routeNames}.",
            'Every write endpoint has validation, authorization, and error-shape tests.',
            'Every response includes a request correlation identifier in logs.',
        ];

        if ($input['needs_filtering']) {
            $acceptanceChecks[] = 'Collection filters are allowlisted and covered by query-parameter tests.';
        }

        if ($input['needs_business_action']) {
            $acceptanceChecks[] = 'Domain action endpoints emit an audit event with actor, target resource, and outcome.';
        }

        return [
            'logs' => [
                ['field' => 'request_id', 'purpose' => 'Correlate API response, app log, queue jobs, and support tickets.'],
                ['field' => 'route_name', 'purpose' => 'Track endpoint behavior without parsing URL strings.'],
                ['field' => 'actor_id', 'purpose' => 'Audit which authenticated user or client triggered the request.'],
                ['field' => Str::singular($resource).'_id', 'purpose' => 'Attach the target resource identifier when available.'],
            ],
            'metrics' => [
                'api_requests_total{route_name,status}',
                'api_request_duration_ms{route_name}',
                'api_validation_failures_total{route_name,field}',
                'api_authorization_denials_total{route_name,ability}',
            ],
            'alerts' => [
                'Alert when 5xx rate for any planned route exceeds the service threshold.',
                'Alert when p95 latency regresses after enabling filters or business actions.',
                'Alert when validation failures spike after publishing the endpoint contract.',
            ],
            'acceptance_checks' => $acceptanceChecks,
            'dashboard_panels' => [
                'Traffic by route name and status code.',
                'Latency percentile by route name.',
                'Validation and authorization failures by route name.',
                'Legacy endpoint traffic during migration windows.',
            ],
        ];
    }

    /**
     * Return response-shape expectations for successful and failed requests.
     *
     * @param  array<int, array{method: string, path: string, route_name: string, purpose: string}>  $routes
     * @return array{success_responses: array<int, array{route_name: string, status: int, envelope: array<string, mixed>}>, error_responses: array<int, array{status: int, reason: string, shape: array<string, mixed>}>, pagination: array<string, mixed>|null, headers: array<int, array{name: string, purpose: string}>}
     */
    private function responseContractFor(array $routes, string $resource, array $input): array
    {
        return [
            'success_responses' => collect($routes)
                ->map(fn (array $route): array => [
                    'route_name' => $route['route_name'],
                    'status' => $this->successStatusFor($route['method']),
                    'envelope' => $this->successEnvelopeFor($route, $resource),
                ])
                ->values()
                ->all(),
            'error_responses' => [
                [
                    'status' => 401,
                    'reason' => 'Unauthenticated caller.',
                    'shape' => ['message' => 'Unauthenticated.'],
                ],
                [
                    'status' => 403,
                    'reason' => 'Authenticated caller lacks the required policy ability.',
                    'shape' => ['message' => 'This action is unauthorized.'],
                ],
                [
                    'status' => 422,
                    'reason' => 'Request payload or query parameters failed validation.',
                    'shape' => ['message' => 'The given data was invalid.', 'errors' => ['field' => ['Validation message.']]],
                ],
            ],
            'pagination' => $input['operation_type'] === 'read'
                ? [
                    'strategy' => 'cursor-or-length-aware-pagination',
                    'meta_keys' => ['current_page', 'per_page', 'total'],
                    'link_keys' => ['first', 'last', 'prev', 'next'],
                ]
                : null,
            'headers' => [
                ['name' => 'Accept', 'purpose' => 'Require clients to request JSON responses.'],
                ['name' => 'Content-Type', 'purpose' => 'Use application/json for write requests.'],
                ['name' => 'X-Request-Id', 'purpose' => 'Correlate API logs, tests, and support tickets.'],
            ],
        ];
    }

    /**
     * Return the response envelope for one route.
     *
     * @param  array{method: string, path: string, route_name: string, purpose: string}  $route
     * @return array<string, mixed>
     */
    private function successEnvelopeFor(array $route, string $resource): array
    {
        if ($route['method'] === 'DELETE') {
            return ['data' => null];
        }

        if (str_ends_with($route['route_name'], '.index')) {
            return [
                'data' => ["array<{$resource}>"],
                'meta' => ['pagination' => true],
                'links' => ['pagination' => true],
            ];
        }

        return [
            'data' => [
                'type' => Str::singular($resource),
                'id' => '{id}',
                'attributes' => ['...'],
            ],
        ];
    }

    /**
     * Return a rollout plan for replacing an older endpoint with the generated RESTful path.
     *
     * @return array{requires_deprecation: bool, legacy_endpoint: string|null, target_endpoint: string, phases: array<int, array{phase: string, action: string, verification: string}>, response_headers: array<int, array{name: string, value: string, purpose: string}>, release_notes: array<int, string>}
     */
    private function migrationPlanFor(string $basePath, array $input): array
    {
        $legacyEndpoint = trim((string) ($input['current_endpoint'] ?? ''));
        $requiresDeprecation = $legacyEndpoint !== '' && $legacyEndpoint !== $basePath;

        if (! $requiresDeprecation) {
            return [
                'requires_deprecation' => false,
                'legacy_endpoint' => $legacyEndpoint === '' ? null : $legacyEndpoint,
                'target_endpoint' => $basePath,
                'phases' => [
                    [
                        'phase' => 'publish',
                        'action' => 'Publish the generated route contract as the canonical endpoint.',
                        'verification' => 'Route list, OpenAPI fragment, and feature tests agree on the same path.',
                    ],
                ],
                'response_headers' => [],
                'release_notes' => [
                    "Use `{$basePath}` as the canonical endpoint for new clients.",
                ],
            ];
        }

        return [
            'requires_deprecation' => true,
            'legacy_endpoint' => $legacyEndpoint,
            'target_endpoint' => $basePath,
            'phases' => [
                [
                    'phase' => 'dual-route',
                    'action' => 'Keep the legacy endpoint available while adding the canonical RESTful endpoint.',
                    'verification' => 'Both routes hit the same service/action and return the same response contract.',
                ],
                [
                    'phase' => 'warn',
                    'action' => 'Add deprecation headers and logs to the legacy endpoint.',
                    'verification' => 'Access logs show legacy callers, user agents, and client identifiers.',
                ],
                [
                    'phase' => 'migrate-clients',
                    'action' => 'Move documented clients to the canonical endpoint before removal.',
                    'verification' => 'Legacy traffic is below the agreed threshold for a full release window.',
                ],
                [
                    'phase' => 'remove',
                    'action' => 'Remove the legacy route after the announced support window.',
                    'verification' => 'Feature tests assert the canonical route and no tests depend on the legacy path.',
                ],
            ],
            'response_headers' => [
                [
                    'name' => 'Deprecation',
                    'value' => 'true',
                    'purpose' => 'Tell clients the legacy endpoint is deprecated.',
                ],
                [
                    'name' => 'Link',
                    'value' => '<'.$basePath.'>; rel="successor-version"',
                    'purpose' => 'Point clients to the canonical replacement endpoint.',
                ],
            ],
            'release_notes' => [
                "Legacy endpoint `{$legacyEndpoint}` is deprecated.",
                "Use `{$basePath}` for new integrations.",
                'Client teams should migrate before the documented removal date.',
            ],
        ];
    }

    /**
     * Return Laravel implementation steps derived from the endpoint contract.
     *
     * @param  array<int, array{method: string, path: string, route_name: string, purpose: string}>  $routes
     * @return array{controller: string, form_requests: array<int, string>, policy_abilities: array<int, string>, route_model_binding: array<int, string>, feature_tests: array<int, string>, implementation_order: array<int, string>}
     */
    private function implementationBlueprintFor(array $routes, string $resource, ?string $parent, array $input): array
    {
        $model = Str::studly(Str::singular($resource));
        $controller = "{$model}Controller";
        $routeNames = collect($routes)->pluck('route_name');
        $abilities = $routeNames
            ->map(fn (string $routeName): string => str($routeName)->afterLast('.')->value())
            ->map(fn (string $ability): string => match ($ability) {
                'store' => 'create',
                'destroy' => 'delete',
                'approve', 'cancel' => $ability,
                default => $ability,
            })
            ->unique()
            ->values()
            ->all();

        $formRequests = collect($routes)
            ->filter(fn (array $route): bool => in_array($route['method'], ['POST', 'PATCH'], true))
            ->map(fn (array $route): string => Str::studly(str($route['route_name'])->afterLast('.')->value()).$model.'Request')
            ->values()
            ->all();

        if ($input['operation_type'] === 'read') {
            $formRequests = collect(["Index{$model}Request"])
                ->merge($formRequests)
                ->unique()
                ->values()
                ->all();
        }

        $binding = [
            "Bind `{{$this->singularParameter($resource)}}` to `{$model}` through implicit route model binding.",
        ];

        if ($parent !== null) {
            $binding[] = "Scope child `{$resource}` records under parent `{$parent}` before returning a model.";
        }

        return [
            'controller' => $controller,
            'form_requests' => $formRequests,
            'policy_abilities' => $abilities,
            'route_model_binding' => $binding,
            'feature_tests' => collect($routes)
                ->map(fn (array $route): string => "Assert `{$route['route_name']}` handles {$route['method']} {$route['path']} with authorization, validation, and response-shape coverage.")
                ->values()
                ->all(),
            'implementation_order' => [
                'Write or update the feature test for the route contract first.',
                'Add the route definition with the final path and route name.',
                'Create the form request and policy ability before controller logic.',
                'Implement the controller as orchestration only and move workflow decisions into a service/action.',
                'Run route:list, focused feature tests, and Pint before publishing the endpoint.',
            ],
        ];
    }

    /**
     * Return lightweight API contract artifacts learners can paste into docs or PRs.
     *
     * @param  array<int, array{method: string, path: string, route_name: string, purpose: string}>  $routes
     * @return array{openapi_path: string, openapi_yaml: string, route_list_expectations: array<int, string>, consumer_contract_checks: array<int, string>}
     */
    private function contractArtifactsFor(string $basePath, array $routes, string $resource, array $input): array
    {
        return [
            'openapi_path' => $basePath,
            'openapi_yaml' => $this->openApiYamlFor($routes, $resource, $input),
            'route_list_expectations' => collect($routes)
                ->map(fn (array $route): string => "{$route['method']} {$route['path']} -> {$route['route_name']}")
                ->values()
                ->all(),
            'consumer_contract_checks' => [
                'Document path parameters, query parameters, success responses, and error responses before controller work starts.',
                'Keep operationId values stable so generated clients and API docs do not churn.',
                'Add feature tests for route names, authorization, validation errors, and response envelope shape.',
            ],
        ];
    }

    /**
     * Build a minimal OpenAPI path fragment from the recommended route set.
     *
     * @param  array<int, array{method: string, path: string, route_name: string, purpose: string}>  $routes
     */
    private function openApiYamlFor(array $routes, string $resource, array $input): string
    {
        $pathBlocks = collect($routes)
            ->groupBy('path')
            ->map(function ($pathRoutes, string $path) use ($resource, $input): string {
                $operationBlocks = collect($pathRoutes)
                    ->map(function (array $route) use ($resource, $input): string {
                        $method = Str::lower($route['method']);
                        $responseCode = $this->successStatusFor($route['method']);
                        $queryParameters = $route['method'] === 'GET' && $input['needs_filtering']
                            ? "\n      parameters:\n        - name: status\n          in: query\n          schema:\n            type: string\n        - name: search\n          in: query\n          schema:\n            type: string"
                            : '';

                        return <<<YAML
    {$method}:
      operationId: {$this->operationIdFor($route)}
      summary: {$route['purpose']}
      tags:
        - {$resource}{$queryParameters}
      responses:
        '{$responseCode}':
          description: Successful {$method} response
        '401':
          description: Unauthenticated
        '403':
          description: Forbidden
        '422':
          description: Validation failed
YAML;
                    })
                    ->implode("\n");

                return <<<YAML
  {$path}:
{$operationBlocks}
YAML;
            })
            ->implode("\n");

        return <<<YAML
paths:
{$pathBlocks}
YAML;
    }

    /**
     * Return a stable operationId derived from the HTTP method and Laravel route name.
     *
     * @param  array{method: string, path: string, route_name: string, purpose: string}  $route
     */
    private function operationIdFor(array $route): string
    {
        return Str::camel(Str::lower($route['method']).' '.str_replace(['.', '-'], ' ', $route['route_name']));
    }

    /**
     * Pick the conventional success response status for a route method.
     */
    private function successStatusFor(string $method): int
    {
        return match ($method) {
            'POST' => 201,
            'DELETE' => 204,
            default => 200,
        };
    }

    /**
     * Score an existing endpoint draft against the generated RESTful target.
     *
     * @return array{score: int, verdict: string, current_endpoint: string|null, target_endpoint: string, smells: array<int, string>, improvements: array<int, string>}
     */
    private function qualityReviewFor(string $basePath, array $input): array
    {
        $currentEndpoint = trim((string) ($input['current_endpoint'] ?? ''));
        $smells = [];
        $improvements = [
            "Use `{$basePath}` as the canonical collection path.",
            'Keep resource identity in path parameters and variable filters in query parameters.',
            'Name Laravel routes from the resource vocabulary so tests and logs stay readable.',
        ];

        if ($currentEndpoint === '') {
            return [
                'score' => 100,
                'verdict' => 'No existing endpoint supplied; generated plan follows the house RESTful naming rules.',
                'current_endpoint' => null,
                'target_endpoint' => $basePath,
                'smells' => [],
                'improvements' => $improvements,
            ];
        }

        $path = Str::lower(parse_url($currentEndpoint, PHP_URL_PATH) ?: $currentEndpoint);

        if (preg_match('/\/(get|create|update|delete|remove|fetch|list)[a-z0-9_-]*/', $path) === 1) {
            $smells[] = 'Path segment contains an action verb; let the HTTP method carry CRUD intent.';
        }

        if (str_contains($path, '_')) {
            $smells[] = 'Path uses underscores; prefer lowercase kebab-case URL segments.';
        }

        if (preg_match('/\/[a-z0-9-]+\/[a-z0-9-]+\/[a-z0-9-]+\/[a-z0-9-]+/', $path) === 1) {
            $smells[] = 'Path appears deeply nested; consider a flatter resource path plus query filters.';
        }

        if ($input['needs_filtering'] && parse_url($currentEndpoint, PHP_URL_QUERY) === null && preg_match('/\/(status|sort|search|page|recent|paid|active)\//', $path) === 1) {
            $smells[] = 'Filter-like values are embedded in the path; move them to query parameters.';
        }

        $score = max(45, 100 - (count($smells) * 15));

        return [
            'score' => $score,
            'verdict' => $score >= 85 ? 'Endpoint is close to the generated RESTful target.' : 'Endpoint needs naming cleanup before it becomes a stable public contract.',
            'current_endpoint' => $currentEndpoint,
            'target_endpoint' => $basePath,
            'smells' => $smells,
            'improvements' => $improvements,
        ];
    }

    /**
     * Normalize a resource name into a plural URL segment.
     */
    private function resourceSlug(string $resourceName): string
    {
        $slug = Str::slug(Str::plural(Str::lower(trim($resourceName))));

        return $slug === '' ? 'resources' : $slug;
    }

    /**
     * Normalize an optional parent resource segment.
     */
    private function parentSlug(?string $parentResource): ?string
    {
        $value = trim((string) $parentResource);

        return $value === '' ? null : $this->resourceSlug($value);
    }

    /**
     * Normalize API version labels without trusting arbitrary path fragments.
     */
    private function versionPrefix(string $version): string
    {
        return match ($version) {
            'v2' => 'v2',
            default => 'v1',
        };
    }

    /**
     * Convert a plural resource segment into a route parameter name.
     */
    private function singularParameter(string $resource): string
    {
        return Str::slug(Str::singular($resource), '_');
    }

    /**
     * Return route candidates for the requested operation type.
     *
     * @return array<int, array{method: string, path: string, route_name: string, purpose: string}>
     */
    private function routesFor(string $basePath, string $resource, ?string $parent, array $input): array
    {
        $parameter = $this->singularParameter($resource);
        $routeBase = $this->routeNameBase($resource, $parent);

        $routes = match ($input['operation_type']) {
            'create' => [
                ['method' => 'POST', 'path' => $basePath, 'route_name' => "{$routeBase}.store", 'purpose' => 'Create one resource after validation and authorization.'],
            ],
            'update' => [
                ['method' => 'PATCH', 'path' => "{$basePath}/{{$parameter}}", 'route_name' => "{$routeBase}.update", 'purpose' => 'Partially update one existing resource.'],
            ],
            'delete' => [
                ['method' => 'DELETE', 'path' => "{$basePath}/{{$parameter}}", 'route_name' => "{$routeBase}.destroy", 'purpose' => 'Delete or archive one existing resource.'],
            ],
            'action' => [
                ['method' => 'POST', 'path' => "{$basePath}/{{$parameter}}/cancel", 'route_name' => "{$routeBase}.cancel", 'purpose' => 'Run an explicit domain command that does not fit plain CRUD.'],
            ],
            default => [
                ['method' => 'GET', 'path' => $basePath, 'route_name' => "{$routeBase}.index", 'purpose' => 'List resources with optional filters and pagination.'],
                ['method' => 'GET', 'path' => "{$basePath}/{{$parameter}}", 'route_name' => "{$routeBase}.show", 'purpose' => 'Read one resource by identifier.'],
            ],
        };

        if ($input['needs_business_action'] && $input['operation_type'] !== 'action') {
            $routes[] = [
                'method' => 'POST',
                'path' => "{$basePath}/{{$parameter}}/approve",
                'route_name' => "{$routeBase}.approve",
                'purpose' => 'Run a named domain action while keeping it visibly separate from CRUD.',
            ];
        }

        return $routes;
    }

    /**
     * Return a stable route-name prefix for top-level or nested resources.
     */
    private function routeNameBase(string $resource, ?string $parent): string
    {
        $resourceBase = str_replace('-', '_', $resource);

        if ($parent === null) {
            return $resourceBase;
        }

        return str_replace('-', '_', $parent).'.'.$resourceBase;
    }

    /**
     * Return query parameters for collection endpoints.
     *
     * @return array<int, array{name: string, purpose: string}>
     */
    private function queryParametersFor(bool $needsFiltering): array
    {
        if (! $needsFiltering) {
            return [
                ['name' => 'page', 'purpose' => 'Paginate large collections.'],
                ['name' => 'per_page', 'purpose' => 'Keep response size bounded.'],
            ];
        }

        return [
            ['name' => 'status', 'purpose' => 'Filter collection by business state.'],
            ['name' => 'search', 'purpose' => 'Search human-readable fields without changing the resource path.'],
            ['name' => 'sort', 'purpose' => 'Sort by allowlisted fields such as `-created_at`.'],
            ['name' => 'page', 'purpose' => 'Paginate large collections.'],
            ['name' => 'per_page', 'purpose' => 'Keep response size bounded.'],
        ];
    }

    /**
     * Return a Laravel route snippet that follows the naming recommendation.
     */
    private function laravelRouteExampleFor(string $resource, ?string $parent, array $input): string
    {
        $controller = Str::studly(Str::singular($resource)).'Controller';
        $parameter = $this->singularParameter($resource);

        if ($parent !== null) {
            $parentParameter = $this->singularParameter($parent);

            return "Route::prefix('v1/{$parent}/{{$parentParameter}}')->group(function () {\n    Route::apiResource('{$resource}', {$controller}::class);\n});";
        }

        if ($input['needs_business_action']) {
            return "Route::prefix('v1')->group(function () {\n    Route::apiResource('{$resource}', {$controller}::class);\n\n    Route::post('/{$resource}/{{$parameter}}/approve', Approve".Str::studly(Str::singular($resource))."Controller::class)\n        ->name('{$resource}.approve');\n});";
        }

        return "Route::prefix('v1')->group(function () {\n    Route::apiResource('{$resource}', {$controller}::class);\n});";
    }

    /**
     * Summarize the naming strategy in one learner-facing sentence.
     */
    private function summaryFor(string $resource, ?string $parent, array $input): string
    {
        $scope = $parent === null ? "top-level `/{$resource}` resource" : "nested `/{$parent}/{id}/{$resource}` resource";
        $action = $input['needs_business_action'] ? ' with explicit domain-action endpoints when needed' : '';

        return "Use a {$scope}{$action}, keep filters in query parameters, and let HTTP verbs express normal CRUD intent.";
    }

    /**
     * Return review checks for route naming.
     *
     * @return array<int, string>
     */
    private function reviewChecklistFor(array $input): array
    {
        $checks = [
            'Resource path uses a noun, usually plural.',
            'HTTP method communicates read, create, update, delete, or command intent.',
            'Route names are stable and match the resource vocabulary.',
            'Response contract can be documented without reading controller code.',
        ];

        if ($input['needs_filtering']) {
            $checks[] = 'Filters, search, sort, and pagination stay in query parameters.';
        }

        if ($input['needs_business_action']) {
            $checks[] = 'Business actions use explicit POST command endpoints and are not hidden behind GET.';
        }

        return $checks;
    }

    /**
     * Return common naming anti-patterns to avoid.
     *
     * @return array<int, string>
     */
    private function antiPatternsFor(): array
    {
        return [
            '`POST /getOrders` duplicates the verb and hides read semantics.',
            '`GET /orders/{order}/approve` changes state through a read method.',
            '`/orders/paid/recent/customer/42` turns filter combinations into path design.',
            'Four-level nesting usually means the route needs a flatter query or a dedicated resource.',
        ];
    }

    /**
     * Return route-level tests for the naming plan.
     *
     * @return array<int, string>
     */
    private function testsFor(string $resource, array $input): array
    {
        $tests = [
            "Assert `GET /api/{$input['version']}/{$resource}` returns a stable collection shape.",
            'Assert invalid payloads return `422` for write endpoints.',
            'Assert unauthorized actors receive `403` instead of hidden business leakage.',
        ];

        if ($input['needs_business_action']) {
            $tests[] = 'Assert the domain action endpoint changes state only through `POST`.';
        }

        return $tests;
    }

    /**
     * Build a markdown memo learners can paste into a PR or ADR.
     *
     * @param  array<int, array{method: string, path: string, route_name: string, purpose: string}>  $routes
     */
    private function decisionMemoFor(string $resource, string $basePath, array $routes, array $input): string
    {
        $routeLines = collect($routes)
            ->map(fn (array $route): string => "- {$route['method']} {$route['path']} (`{$route['route_name']}`): {$route['purpose']}")
            ->implode("\n");

        return <<<MARKDOWN
# RESTful API Naming Plan

Resource: {$resource}
Base path: {$basePath}
Operation type: {$input['operation_type']}

## Routes
{$routeLines}

## Rules
- Use plural resource nouns for collections.
- Let HTTP verbs carry normal CRUD intent.
- Keep filters, search, sort, and pagination in query parameters.
- Add command-style endpoints only for real domain actions.
- Keep Laravel route names stable for tests, docs, logs, and support.
MARKDOWN;
    }
}
