<?php

declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;

final class CsrfProtectionPlanWorkbenchTest extends TestCase
{
    /**
     * The CSRF protection workbench renders the planner.
     */
    public function test_csrf_protection_workbench_renders(): void
    {
        $this->get('/workbench/csrf-protection-plan')
            ->assertOk()
            ->assertSee('CSRF protection starts with browser intent')
            ->assertSee('POST /api/practice/csrf-protection-plan')
            ->assertSee('Plan CSRF defense')
            ->assertSee('Scenario Presets')
            ->assertSee('data-csrf-preset="unsafe-get"', false)
            ->assertSee('Has CSRF token')
            ->assertSee('Uses cookie auth');
    }

    /**
     * The API returns risk, controls, tests, and interview guidance.
     */
    public function test_csrf_protection_api_returns_plan(): void
    {
        $this->postJson('/api/practice/csrf-protection-plan', [
            'flow_name' => 'Email Change',
            'client_type' => 'blade-web',
            'state_changing_method' => 'get',
            'has_csrf_token' => false,
            'same_site' => 'none',
            'uses_cookie_auth' => true,
        ])
            ->assertOk()
            ->assertJsonPath('data.flow', 'EmailChange')
            ->assertJsonPath('data.risk_score.label', 'critical')
            ->assertJsonPath('data.recommendation', 'Move the action to POST, PUT, PATCH, or DELETE before relying on CSRF controls.')
            ->assertJsonPath('data.controls.0.control', 'CSRF token')
            ->assertJsonPath('data.same_site_review.setting', 'None')
            ->assertJsonPath('data.test_matrix.1.case', 'missing CSRF token')
            ->assertJsonPath('data.test_matrix.3.case', 'GET request attempts a state change')
            ->assertJson(
                fn ($json) => $json->where(
                    'data.feature_test_snippet',
                    fn (string $snippet): bool => str_contains($snippet, 'test_email_change_requires_csrf_protection')
                        && str_contains($snippet, "->post('/profile'")
                        && str_contains($snippet, '->assertStatus(419)')
                )
            )
            ->assertJson(
                fn ($json) => $json->where(
                    'data.review_packet_markdown',
                    fn (string $packet): bool => str_contains($packet, '# CSRF Protection Packet: EmailChange')
                        && str_contains($packet, '## Controls')
                        && str_contains($packet, '## Test Matrix')
                        && str_contains($packet, '## Interview Answer')
                )
            )
            ->assertJsonPath('data.commands.1', 'php artisan route:list --path=csrf-protection-plan')
            ->assertSee('CSRF tricks a logged-in browser into sending an unwanted state-changing request');
    }

    /**
     * Invalid CSRF protection payloads return validation errors.
     */
    public function test_csrf_protection_api_validates_payload(): void
    {
        $this->postJson('/api/practice/csrf-protection-plan', [
            'flow_name' => '<bad>',
            'client_type' => 'unknown',
            'state_changing_method' => 'trace',
            'has_csrf_token' => 'maybe',
            'same_site' => 'loose',
            'uses_cookie_auth' => 'sometimes',
        ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors([
                'flow_name',
                'client_type',
                'state_changing_method',
                'has_csrf_token',
                'same_site',
                'uses_cookie_auth',
            ]);
    }
}
