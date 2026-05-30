<?php

declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;

final class JwtTokenStoragePlanWorkbenchTest extends TestCase
{
    /**
     * The JWT token-storage workbench renders the auth security planning loop.
     */
    public function test_jwt_token_storage_plan_workbench_renders(): void
    {
        $response = $this->get('/workbench/jwt-token-storage-plan');

        $response
            ->assertOk()
            ->assertSee('JWT Token Storage Plan Workbench')
            ->assertSee('POST /api/practice/jwt-token-storage-plan')
            ->assertSee('JwtTokenStoragePlanService')
            ->assertSee('Scenario preset')
            ->assertSee('Cross-domain SPA needing hardening')
            ->assertSee('Current storage')
            ->assertSee('Plan JWT storage');
    }

    /**
     * The JWT token-storage API recommends HttpOnly cookies for hardened browser flows.
     */
    public function test_jwt_token_storage_plan_api_recommends_http_only_cookie_for_browser_flow(): void
    {
        $response = $this->postJson('/api/practice/jwt-token-storage-plan', [
            'client_type' => 'same-domain-spa',
            'current_storage' => 'localStorage',
            'token_lifetime' => 'minutes',
            'xss_risk' => 'high',
            'csrf_controls' => 'strong',
            'refresh_token' => 'yes',
        ]);

        $response
            ->assertOk()
            ->assertJsonPath('data.recommendation.storage', 'http-only-cookie')
            ->assertJsonPath('data.recommendation.label', 'Use HttpOnly Secure SameSite cookies')
            ->assertJsonPath('data.decision_matrix.0.signal', 'Client type')
            ->assertJsonPath('data.decision_matrix.1.value', 'localStorage')
            ->assertJsonPath('data.risk_score.level', 'medium')
            ->assertJsonPath('data.review_checklist.3.area', 'cookie flags')
            ->assertJsonPath('data.implementation_snippets.cookie_header', 'Set-Cookie: refresh_token=...; HttpOnly; Secure; SameSite=Lax; Path=/auth/refresh')
            ->assertJsonPath('data.migration_plan.needed', true)
            ->assertJsonPath('data.migration_plan.from', 'localStorage')
            ->assertJsonPath('data.migration_plan.to', 'http-only-cookie')
            ->assertJsonPath('data.tests.3', 'Frontend JavaScript cannot read the auth cookie.');
    }

    /**
     * The JWT token-storage API limits localStorage to low-risk demo scenarios.
     */
    public function test_jwt_token_storage_plan_api_limits_local_storage_to_demo_context(): void
    {
        $response = $this->postJson('/api/practice/jwt-token-storage-plan', [
            'client_type' => 'low-risk-demo',
            'current_storage' => 'localStorage',
            'token_lifetime' => 'minutes',
            'xss_risk' => 'low',
            'csrf_controls' => 'none',
            'refresh_token' => 'no',
        ]);

        $response
            ->assertOk()
            ->assertJsonPath('data.recommendation.storage', 'localStorage-limited-demo')
            ->assertJsonPath('data.recommendation.refresh_token', 'Do not store long-lived refresh tokens in localStorage.')
            ->assertJsonPath('data.review_checklist.3.area', 'promotion guard')
            ->assertJsonPath('data.implementation_snippets.review_warning', 'Block production promotion until token storage is redesigned away from localStorage for sensitive access.');
    }

    /**
     * Invalid JWT token-storage payloads return validation errors.
     */
    public function test_jwt_token_storage_plan_api_validates_payload(): void
    {
        $response = $this->postJson('/api/practice/jwt-token-storage-plan', [
            'client_type' => 'desktop',
            'current_storage' => 'sessionStorage',
            'token_lifetime' => 'forever',
            'xss_risk' => 'unknown',
            'csrf_controls' => 'maybe',
            'refresh_token' => 'sometimes',
        ]);

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors([
                'client_type',
                'current_storage',
                'token_lifetime',
                'xss_risk',
                'csrf_controls',
                'refresh_token',
            ]);
    }
}
