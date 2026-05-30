<?php

declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;

final class IdorAccessReviewWorkbenchTest extends TestCase
{
    /**
     * The IDOR access review workbench renders the object-level authorization loop.
     */
    public function test_idor_access_review_workbench_renders(): void
    {
        $this->get('/workbench/idor-access-review')
            ->assertOk()
            ->assertSee('IDOR Access Review Workbench')
            ->assertSee('POST /api/practice/idor-access-review')
            ->assertSee('Review IDOR access')
            ->assertSee('Attack Simulation')
            ->assertSee('Risk Drivers')
            ->assertSee('Route Surface Review')
            ->assertSee('Authorization Map')
            ->assertSee('Merge Evidence')
            ->assertSee('Monitoring Guidance')
            ->assertSee('Remediation Plan')
            ->assertSee('403 vs 404 Guidance')
            ->assertSee('escapeIdorHtml');
    }

    /**
     * The API returns risk, attacker flow, Laravel controls, and interview guidance.
     */
    public function test_idor_access_review_api_returns_review(): void
    {
        $this->postJson('/api/practice/idor-access-review', [
            'resource_name' => 'Invoice',
            'route_pattern' => '/api/invoices/{invoice}',
            'access_model' => 'tenant',
            'uses_policy' => false,
            'query_scoped' => false,
            'attacker_changes_id' => true,
        ])
            ->assertOk()
            ->assertJsonPath('data.topic', 'idor-access-review')
            ->assertJsonPath('data.resource', 'Invoice')
            ->assertJsonPath('data.risk_score.score', 90)
            ->assertJsonPath('data.risk_score.label', 'high')
            ->assertJsonPath('data.risk_drivers.0', 'The object identifier is user-controlled, so attackers can enumerate neighboring IDs or saved URLs.')
            ->assertJsonPath('data.route_surface_review.surface', 'single-resource')
            ->assertJsonPath('data.route_surface_review.extra_tests.2', 'Enumeration attempts receive consistent denial.')
            ->assertJsonPath('data.attack_simulation.1', 'Change the object identifier in /api/invoices/{invoice} to a neighboring Invoice ID.')
            ->assertJsonPath('data.attack_variants.0.name', 'Sequential ID swap')
            ->assertJsonPath('data.abuse_case_table.1.expected_control', 'Tenant or team scope is checked in addition to role.')
            ->assertJsonPath('data.authorization_map.0.policy_method', 'view')
            ->assertJsonPath('data.laravel_controls.0.control', 'Policy or Gate authorization')
            ->assertJsonPath('data.remediation_plan.0.step', 'Scope lookup')
            ->assertJsonPath('data.test_matrix.1.expected', '403 Forbidden or 404 Not Found')
            ->assertJsonPath('data.status_code_guidance.when_to_use_404', 'The object belongs to another tenant/team/account or existence leakage could help enumeration.')
            ->assertJsonPath('data.merge_evidence.1.artifact', 'Failing-first denial test')
            ->assertJsonPath('data.monitoring_guidance.log_fields.0', 'authenticated_user_id')
            ->assertJsonPath('data.interview_questions.0', 'Why is IDOR an authorization bug even when the attacker is logged in?')
            ->assertJsonPath('data.commands.1', 'php artisan test --filter IdorAccessReviewWorkbenchTest')
            ->assertJson(
                fn ($json) => $json->where(
                    'data.interview_answer',
                    fn (string $answer): bool => str_contains($answer, 'IDOR is object-level authorization failure')
                )->where(
                    'data.policy_skeleton',
                    fn (string $policy): bool => str_contains($policy, 'class InvoicePolicy')
                        && str_contains($policy, '$user->tenant_id')
                )->where(
                    'data.feature_test_snippet',
                    fn (string $snippet): bool => str_contains($snippet, 'test_user_cannot_view_another_users_invoice')
                        && str_contains($snippet, 'assertForbidden')
                )->where(
                    'data.review_packet_markdown',
                    fn (string $packet): bool => str_contains($packet, '# IDOR Access Review: Invoice')
                        && str_contains($packet, '## Remediation')
                        && str_contains($packet, '## Merge Evidence')
                        && str_contains($packet, '## Merge Gate')
                )
            );
    }

    /**
     * Invalid IDOR review payloads return validation errors.
     */
    public function test_idor_access_review_api_validates_payload(): void
    {
        $this->postJson('/api/practice/idor-access-review', [
            'resource_name' => '<bad>',
            'route_pattern' => '/x',
            'access_model' => 'global-admin',
            'uses_policy' => 'maybe',
            'query_scoped' => 'maybe',
            'attacker_changes_id' => 'maybe',
        ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors([
                'resource_name',
                'route_pattern',
                'access_model',
                'uses_policy',
                'query_scoped',
                'attacker_changes_id',
            ]);
    }
}
