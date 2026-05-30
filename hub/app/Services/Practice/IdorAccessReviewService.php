<?php

declare(strict_types=1);

namespace App\Services\Practice;

use Illuminate\Support\Str;

final class IdorAccessReviewService
{
    /**
     * Build an IDOR access review for object-level authorization practice.
     *
     * @param  array{resource_name: string, route_pattern: string, access_model: string, uses_policy: bool, query_scoped: bool, attacker_changes_id: bool}  $input
     * @return array<string, mixed>
     */
    public function review(array $input): array
    {
        $resource = Str::studly($input['resource_name']);
        $riskScore = $this->riskScoreFor($input);

        return [
            'topic' => 'idor-access-review',
            'resource' => $resource,
            'route_pattern' => $input['route_pattern'],
            'access_model' => $input['access_model'],
            'risk_score' => $riskScore,
            'risk_drivers' => $this->riskDriversFor($input),
            'route_surface_review' => $this->routeSurfaceReviewFor($input['route_pattern']),
            'attack_simulation' => $this->attackSimulationFor($input, $resource),
            'attack_variants' => $this->attackVariantsFor($resource),
            'abuse_case_table' => $this->abuseCaseTableFor($resource),
            'vulnerable_pattern' => $this->vulnerablePatternFor($resource),
            'secure_pattern' => $this->securePatternFor($resource, $input['access_model']),
            'policy_skeleton' => $this->policySkeletonFor($resource, $input['access_model']),
            'authorization_map' => $this->authorizationMapFor($resource),
            'laravel_controls' => $this->laravelControls(),
            'remediation_plan' => $this->remediationPlanFor($input),
            'test_matrix' => $this->testMatrix(),
            'feature_test_snippet' => $this->featureTestSnippetFor($resource),
            'status_code_guidance' => $this->statusCodeGuidanceFor($input['access_model']),
            'review_checklist' => $this->reviewChecklist(),
            'merge_evidence' => $this->mergeEvidence(),
            'monitoring_guidance' => $this->monitoringGuidance(),
            'interview_questions' => $this->interviewQuestions(),
            'interview_answer' => 'IDOR is object-level authorization failure. I prevent it by checking ownership, tenant, team, or permission on every object read, update, delete, download, and export, then proving the denial path with a second user or tenant test.',
            'commands' => [
                'php artisan route:list --path=idor-access-review',
                'php artisan test --filter IdorAccessReviewWorkbenchTest',
                'vendor\\bin\\pint --test',
            ],
            'review_packet_markdown' => $this->reviewPacketFor($resource, $input, $riskScore),
        ];
    }

    /**
     * Score the IDOR risk from missing controls and object boundary complexity.
     *
     * @param  array{access_model: string, uses_policy: bool, query_scoped: bool, attacker_changes_id: bool}  $input
     * @return array{score: int, label: string}
     */
    private function riskScoreFor(array $input): array
    {
        $score = 0;
        $score += $input['attacker_changes_id'] ? 25 : 0;
        $score += $input['uses_policy'] ? 0 : 25;
        $score += $input['query_scoped'] ? 0 : 25;
        $score += in_array($input['access_model'], ['tenant', 'team'], true) ? 15 : 0;
        $score += $input['access_model'] === 'role' ? 10 : 0;
        $score = min(100, $score);

        return [
            'score' => $score,
            'label' => match (true) {
                $score >= 70 => 'high',
                $score >= 35 => 'medium',
                default => 'low',
            },
        ];
    }

    /**
     * Explain the inputs that caused the current risk score.
     *
     * @param  array{access_model: string, uses_policy: bool, query_scoped: bool, attacker_changes_id: bool}  $input
     * @return array<int, string>
     */
    private function riskDriversFor(array $input): array
    {
        $drivers = [];

        if ($input['attacker_changes_id']) {
            $drivers[] = 'The object identifier is user-controlled, so attackers can enumerate neighboring IDs or saved URLs.';
        }

        if (! $input['uses_policy']) {
            $drivers[] = 'No policy or Gate check proves object-level permission after authentication.';
        }

        if (! $input['query_scoped']) {
            $drivers[] = 'The lookup is not scoped before return, so another tenant or owner record can be resolved first.';
        }

        if (in_array($input['access_model'], ['tenant', 'team'], true)) {
            $drivers[] = 'Tenant or team boundaries raise impact because one leaked ID can cross an organization boundary.';
        }

        if ($input['access_model'] === 'role') {
            $drivers[] = 'Role checks are too broad unless they are combined with object ownership or tenant scope.';
        }

        return $drivers === [] ? ['Current inputs describe a low-risk public or already-scoped route, but direct API tests should still prove the boundary.'] : $drivers;
    }

    /**
     * Explain the concrete attacker flow.
     *
     * @param  array{route_pattern: string, attacker_changes_id: bool}  $input
     * @return array<int, string>
     */
    private function attackSimulationFor(array $input, string $resource): array
    {
        return [
            "Login as a valid low-privilege user and request {$input['route_pattern']}.",
            "Change the object identifier in {$input['route_pattern']} to a neighboring {$resource} ID.",
            $input['attacker_changes_id']
                ? 'If the response returns another user record, this is a confirmed IDOR exposure.'
                : 'Even if the current UI does not expose ID changes, test direct API calls and saved links.',
            'Repeat the request from another tenant or team to prove the boundary is enforced.',
        ];
    }

    /**
     * Classify the route surface so the review can suggest targeted tests.
     *
     * @return array{surface: string, sensitivity: string, warning: string, extra_tests: array<int, string>}
     */
    private function routeSurfaceReviewFor(string $routePattern): array
    {
        $route = Str::lower($routePattern);

        if (str_contains($route, 'download') || str_contains($route, 'export')) {
            return [
                'surface' => 'file-or-export',
                'sensitivity' => 'high',
                'warning' => 'File and export routes often bypass normal JSON resource policies, so repeat authorization before generating the file or signed URL.',
                'extra_tests' => [
                    'Other user cannot download the file.',
                    'Other tenant cannot generate an export link.',
                    'Expired or replayed signed URLs do not reveal the object.',
                ],
            ];
        }

        if (substr_count(trim($routePattern, '/'), '/') >= 3) {
            return [
                'surface' => 'nested-resource',
                'sensitivity' => 'medium',
                'warning' => 'Nested routes must verify both the parent boundary and the child object boundary.',
                'extra_tests' => [
                    'Object belongs to requested parent.',
                    'Parent belongs to current tenant or owner.',
                    'Swapping only the child ID is denied.',
                ],
            ];
        }

        return [
            'surface' => 'single-resource',
            'sensitivity' => 'medium',
            'warning' => 'Single resource routes still need object-level authorization because authentication only proves who the caller is.',
            'extra_tests' => [
                'Other user cannot read the object.',
                'Other user cannot update or delete the object through sibling routes.',
                'Enumeration attempts receive consistent denial.',
            ],
        ];
    }

    /**
     * @return array<int, array{name: string, example: string, defense: string}>
     */
    private function attackVariantsFor(string $resource): array
    {
        $variable = Str::camel($resource);

        return [
            [
                'name' => 'Sequential ID swap',
                'example' => "/api/{$variable}s/1001 -> /api/{$variable}s/1002",
                'defense' => 'Scope the query to the current owner, tenant, or team before returning the model.',
            ],
            [
                'name' => 'Download or export replay',
                'example' => "/api/{$variable}s/1002/download",
                'defense' => 'Repeat the same policy check on file, export, and signed URL generation routes.',
            ],
            [
                'name' => 'Nested resource confusion',
                'example' => "/api/accounts/10/{$variable}s/1002",
                'defense' => 'Verify both the parent account boundary and the child object ownership.',
            ],
        ];
    }

    /**
     * @return array<int, array{actor: string, attempt: string, expected_control: string}>
     */
    private function abuseCaseTableFor(string $resource): array
    {
        $label = Str::headline($resource);

        return [
            [
                'actor' => 'Authenticated attacker',
                'attempt' => "Reads another user's {$label} by changing the path ID.",
                'expected_control' => 'Scoped query plus `view` policy denies the object.',
            ],
            [
                'actor' => 'Same-role user',
                'attempt' => "Uses a valid role to access a {$label} owned by another tenant.",
                'expected_control' => 'Tenant or team scope is checked in addition to role.',
            ],
            [
                'actor' => 'Support/admin workflow',
                'attempt' => "Opens a {$label} preview without explicit support permission.",
                'expected_control' => 'Dedicated support policy, audit reason, and limited read fields.',
            ],
        ];
    }

    private function vulnerablePatternFor(string $resource): string
    {
        $variable = Str::camel($resource);

        return "Route::get('/api/{$variable}s/{id}', function (int \$id) {\n"
            ."    \${$variable} = {$resource}::findOrFail(\$id);\n\n"
            ."    return new {$resource}Resource(\${$variable});\n"
            .'});';
    }

    private function securePatternFor(string $resource, string $accessModel): string
    {
        $variable = Str::camel($resource);
        $scopeColumn = match ($accessModel) {
            'tenant' => 'tenant_id',
            'team' => 'team_id',
            'owner' => 'user_id',
            default => 'account_id',
        };

        return "Route::get('/api/{$variable}s/{id}', function (Request \$request, int \$id) {\n"
            ."    \${$variable} = {$resource}::query()\n"
            ."        ->where('{$scopeColumn}', \$request->user()->{$scopeColumn})\n"
            ."        ->findOrFail(\$id);\n\n"
            ."    Gate::authorize('view', \${$variable});\n\n"
            ."    return new {$resource}Resource(\${$variable});\n"
            .'});';
    }

    private function policySkeletonFor(string $resource, string $accessModel): string
    {
        $scopeColumn = match ($accessModel) {
            'tenant' => 'tenant_id',
            'team' => 'team_id',
            'owner' => 'user_id',
            'role' => 'tenant_id',
            default => 'visibility',
        };

        if ($accessModel === 'public') {
            return "class {$resource}Policy\n{\n"
                ."    public function view(User \$user, {$resource} \${$this->variableFor($resource)}): bool\n"
                ."    {\n"
                ."        return \${$this->variableFor($resource)}->is_public === true\n"
                ."            || \$user->id === \${$this->variableFor($resource)}->user_id;\n"
                ."    }\n"
                .'}';
        }

        return "class {$resource}Policy\n{\n"
            ."    public function view(User \$user, {$resource} \${$this->variableFor($resource)}): bool\n"
            ."    {\n"
            ."        return \$user->{$scopeColumn} === \${$this->variableFor($resource)}->{$scopeColumn};\n"
            ."    }\n"
            .'}';
    }

    /**
     * @return array<int, array{operation: string, policy_method: string, route_example: string, denial_test: string}>
     */
    private function authorizationMapFor(string $resource): array
    {
        $variable = Str::camel($resource);
        $routeSegment = Str::kebab(Str::plural($resource));

        return [
            [
                'operation' => 'read',
                'policy_method' => 'view',
                'route_example' => "GET /api/{$routeSegment}/{{$variable}}",
                'denial_test' => 'other user receives 403 or 404',
            ],
            [
                'operation' => 'update',
                'policy_method' => 'update',
                'route_example' => "PATCH /api/{$routeSegment}/{{$variable}}",
                'denial_test' => 'other user cannot change fields',
            ],
            [
                'operation' => 'delete',
                'policy_method' => 'delete',
                'route_example' => "DELETE /api/{$routeSegment}/{{$variable}}",
                'denial_test' => 'other user cannot delete or soft-delete',
            ],
            [
                'operation' => 'download/export',
                'policy_method' => 'download or export',
                'route_example' => "GET /api/{$routeSegment}/{{$variable}}/download",
                'denial_test' => 'other user cannot receive file or signed URL',
            ],
        ];
    }

    /**
     * @return array<int, array{control: string, purpose: string}>
     */
    private function laravelControls(): array
    {
        return [
            ['control' => 'Policy or Gate authorization', 'purpose' => 'Check whether this exact user may access this exact object.'],
            ['control' => 'Scoped query', 'purpose' => 'Limit lookup by owner, tenant, team, or account before returning a model.'],
            ['control' => 'Two-user feature test', 'purpose' => 'Prove user A cannot read, update, delete, download, or export user B data.'],
            ['control' => 'Audit log', 'purpose' => 'Record denied object access attempts for investigation and abuse detection.'],
        ];
    }

    /**
     * @param  array{uses_policy: bool, query_scoped: bool, attacker_changes_id: bool, access_model: string}  $input
     * @return array<int, array{step: string, action: string}>
     */
    private function remediationPlanFor(array $input): array
    {
        $steps = [];

        if (! $input['query_scoped']) {
            $steps[] = ['step' => 'Scope lookup', 'action' => 'Replace direct `findOrFail($id)` with a query constrained by owner, tenant, team, or account before resolving the object.'];
        }

        if (! $input['uses_policy']) {
            $steps[] = ['step' => 'Authorize object', 'action' => 'Add a policy method such as `view`, `update`, `delete`, `download`, or `export` and call `Gate::authorize` or `$this->authorize`.'];
        }

        if ($input['attacker_changes_id']) {
            $steps[] = ['step' => 'Test attacker path', 'action' => 'Write a second-user or second-tenant feature test that changes only the object ID and expects denial.'];
        }

        $steps[] = ['step' => 'Review sibling routes', 'action' => 'Check nested routes, exports, file downloads, admin previews, batch endpoints, and signed URL creation for the same boundary.'];
        $steps[] = ['step' => 'Record evidence', 'action' => 'Keep route-list output, failing-first test, passing policy test, and one code review note in the merge packet.'];

        return $steps;
    }

    /**
     * @return array<int, array{case: string, expected: string}>
     */
    private function testMatrix(): array
    {
        return [
            ['case' => 'owner can view their object', 'expected' => '200 OK'],
            ['case' => 'other user cannot view the object', 'expected' => '403 Forbidden or 404 Not Found'],
            ['case' => 'other tenant cannot discover the object', 'expected' => '404 Not Found or 403 Forbidden'],
            ['case' => 'download/export route repeats the same check', 'expected' => 'Denied for non-owner context'],
        ];
    }

    private function featureTestSnippetFor(string $resource): string
    {
        $variable = Str::camel($resource);
        $routeSegment = Str::kebab(Str::plural($resource));

        return "public function test_user_cannot_view_another_users_{$variable}(): void\n"
            ."{\n"
            ."    \$owner = User::factory()->create();\n"
            ."    \$attacker = User::factory()->create();\n"
            ."    \${$variable} = {$resource}::factory()->for(\$owner)->create();\n\n"
            ."    \$this->actingAs(\$attacker)\n"
            ."        ->getJson('/api/{$routeSegment}/'.\${$variable}->id)\n"
            ."        ->assertForbidden();\n"
            .'}';
    }

    /**
     * @return array{recommended: string, when_to_use_403: string, when_to_use_404: string}
     */
    private function statusCodeGuidanceFor(string $accessModel): array
    {
        return [
            'recommended' => in_array($accessModel, ['tenant', 'team'], true)
                ? 'Prefer 404 for cross-tenant or cross-team reads when object existence itself is sensitive; use 403 when the user may know the object exists but lacks permission.'
                : 'Use 403 when the authenticated user is known but not allowed; use 404 when revealing existence creates unnecessary risk.',
            'when_to_use_403' => 'The user is authenticated, the object existence is not secret, and the API wants a clear authorization failure.',
            'when_to_use_404' => 'The object belongs to another tenant/team/account or existence leakage could help enumeration.',
        ];
    }

    /**
     * @return array<int, string>
     */
    private function reviewChecklist(): array
    {
        return [
            'Every route with `{id}`, `{uuid}`, download, export, or nested resource parameter has object-level authorization.',
            'The database query is scoped before the model is returned.',
            'Policy methods cover read, update, delete, restore, download, and export where relevant.',
            'Feature tests include at least two users and, for SaaS apps, two tenants or teams.',
            'Frontend hidden buttons are treated as convenience only, never as the authorization control.',
        ];
    }

    /**
     * @return array<int, array{artifact: string, purpose: string}>
     */
    private function mergeEvidence(): array
    {
        return [
            ['artifact' => 'Route list excerpt', 'purpose' => 'Shows every object-ID route reviewed for IDOR risk.'],
            ['artifact' => 'Failing-first denial test', 'purpose' => 'Proves the previous behavior allowed or risked cross-object access.'],
            ['artifact' => 'Passing two-user or two-tenant test', 'purpose' => 'Proves object-level authorization blocks the attacker path.'],
            ['artifact' => 'Policy or scoped-query diff', 'purpose' => 'Shows where the real backend control lives.'],
            ['artifact' => 'Manual replay note', 'purpose' => 'Documents the exact ID swap request and final expected status.'],
        ];
    }

    /**
     * @return array{log_fields: array<int, string>, alert_rule: string, privacy_note: string}
     */
    private function monitoringGuidance(): array
    {
        return [
            'log_fields' => [
                'authenticated_user_id',
                'route_name',
                'requested_object_type',
                'requested_object_id',
                'owner_or_tenant_id_when_safe',
                'denial_status',
            ],
            'alert_rule' => 'Alert on repeated denied object access across many IDs, tenants, or download/export routes from the same user, IP, or token.',
            'privacy_note' => 'Do not log raw tokens, full exported data, or sensitive object payloads while investigating IDOR attempts.',
        ];
    }

    /**
     * @return array<int, string>
     */
    private function interviewQuestions(): array
    {
        return [
            'Why is IDOR an authorization bug even when the attacker is logged in?',
            'Where should Laravel check object ownership: middleware, controller, policy, or query scope?',
            'When would you return 403 versus 404 for a denied object?',
            'How do you test IDOR with two users or two tenants?',
            'Why are UUIDs or hidden buttons not enough as the primary defense?',
        ];
    }

    /**
     * @param  array{route_pattern: string, access_model: string, uses_policy: bool, query_scoped: bool, attacker_changes_id: bool}  $input
     * @param  array{score: int, label: string}  $riskScore
     */
    private function reviewPacketFor(string $resource, array $input, array $riskScore): string
    {
        return "# IDOR Access Review: {$resource}\n\n"
            ."## Route\n{$input['route_pattern']}\n\n"
            ."## Risk\n{$riskScore['label']} ({$riskScore['score']}/100)\n\n"
            ."## Controls\n"
            .'- Access model: '.$input['access_model']."\n"
            .'- Uses policy: '.($input['uses_policy'] ? 'yes' : 'no')."\n"
            .'- Query scoped before return: '.($input['query_scoped'] ? 'yes' : 'no')."\n"
            .'- Attacker can change ID: '.($input['attacker_changes_id'] ? 'yes' : 'unknown')."\n\n"
            ."## Remediation\n"
            .collect($this->remediationPlanFor($input))
                ->map(fn (array $step): string => "- {$step['step']}: {$step['action']}")
                ->implode("\n")
            ."\n\n## Merge Evidence\n"
            .collect($this->mergeEvidence())
                ->map(fn (array $item): string => "- {$item['artifact']}: {$item['purpose']}")
                ->implode("\n")
            ."\n\n## Merge Gate\nDo not ship until a second-user or second-tenant test proves object-level denial.";
    }

    private function variableFor(string $resource): string
    {
        return Str::camel($resource);
    }
}
