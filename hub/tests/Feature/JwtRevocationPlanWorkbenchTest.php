<?php

declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;

final class JwtRevocationPlanWorkbenchTest extends TestCase
{
    /**
     * The JWT revocation workbench renders the API and database planning loop.
     */
    public function test_jwt_revocation_plan_workbench_renders(): void
    {
        $response = $this->get('/workbench/jwt-revocation-plan');

        $response
            ->assertOk()
            ->assertSee('JWT Revocation Plan Workbench')
            ->assertSee('POST /api/practice/jwt-revocation-plan')
            ->assertSee('JwtRevocationPlanService')
            ->assertSee('Scenario preset')
            ->assertSee('Role change needs immediate downgrade')
            ->assertSee('Plan JWT revocation');
    }

    /**
     * The JWT revocation API returns denylist, API, and middleware guidance.
     */
    public function test_jwt_revocation_plan_api_returns_denylist_guidance(): void
    {
        $response = $this->postJson('/api/practice/jwt-revocation-plan', [
            'client_type' => 'browser-spa',
            'revocation_model' => 'denylist',
            'token_lifetime' => 'hours',
            'revocation_store' => 'database',
            'immediate_logout' => 'yes',
            'refresh_rotation' => 'yes',
        ]);

        $response
            ->assertOk()
            ->assertJsonPath('data.strategy.model', 'denylist')
            ->assertJsonPath('data.strategy.label', 'Use a denylist keyed by JWT ID')
            ->assertJsonPath('data.architecture_summary.primary_tradeoff', 'Precise per-token revocation, but every protected request pays for a revocation lookup and store availability matters.')
            ->assertJsonPath('data.threat_model.0.asset', 'access token')
            ->assertJsonPath('data.incident_playbook.0.phase', 'triage')
            ->assertJsonPath('data.decision_matrix.3.signal', 'Revocation store')
            ->assertJsonPath('data.risk_score.level', 'controlled')
            ->assertJsonPath('data.retention_plan.storage', 'database')
            ->assertJsonPath('data.rollout_plan.2.phase', 'enforce')
            ->assertJsonPath('data.observability_plan.metrics.1', 'jwt_revoked_total by reason and revocation_model')
            ->assertJsonPath('data.failure_mode_plan.0.failure', 'revocation store unavailable')
            ->assertJsonPath('data.test_matrix.1.category', 'security')
            ->assertJsonPath('data.review_checklist.1.area', 'server-side state')
            ->assertJsonPath('data.api_endpoints.3.path', '/api/auth/logout-all')
            ->assertJsonPath('data.tests.1', 'A revoked jti is rejected with 401 even when signature and exp are valid.')
            ->assertJsonPath('data.commands.1', 'php artisan test --filter JwtRevocationPlan');
    }

    /**
     * The JWT revocation API supports token-version invalidation.
     */
    public function test_jwt_revocation_plan_api_returns_token_version_guidance(): void
    {
        $response = $this->postJson('/api/practice/jwt-revocation-plan', [
            'client_type' => 'server-api',
            'revocation_model' => 'token-version',
            'token_lifetime' => 'hours',
            'revocation_store' => 'database',
            'immediate_logout' => 'yes',
            'refresh_rotation' => 'yes',
        ]);

        $response
            ->assertOk()
            ->assertJsonPath('data.strategy.model', 'token-version')
            ->assertJsonPath('data.strategy.label', 'Use a user token version or security version')
            ->assertJsonPath('data.middleware_flow.2', 'Compare the token version claim with the user or session security version stored server-side.');
    }

    /**
     * Invalid JWT revocation payloads return validation errors.
     */
    public function test_jwt_revocation_plan_api_validates_payload(): void
    {
        $response = $this->postJson('/api/practice/jwt-revocation-plan', [
            'client_type' => 'desktop',
            'revocation_model' => 'black-hole',
            'token_lifetime' => 'forever',
            'revocation_store' => 'spreadsheet',
            'immediate_logout' => 'maybe',
            'refresh_rotation' => 'sometimes',
        ]);

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors([
                'client_type',
                'revocation_model',
                'token_lifetime',
                'revocation_store',
                'immediate_logout',
                'refresh_rotation',
            ]);
    }
}
