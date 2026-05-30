<?php

declare(strict_types=1);

namespace Tests\Unit\Practice;

use App\Services\Practice\JwtRevocationPlanService;
use PHPUnit\Framework\TestCase;

final class JwtRevocationPlanServiceTest extends TestCase
{
    /**
     * Denylist plans store revoked JWT IDs until token expiry.
     */
    public function test_denylist_plan_uses_jti_lookup_and_database_schema(): void
    {
        $plan = (new JwtRevocationPlanService)->plan([
            'client_type' => 'browser-spa',
            'revocation_model' => 'denylist',
            'token_lifetime' => 'hours',
            'revocation_store' => 'database',
            'immediate_logout' => 'yes',
            'refresh_rotation' => 'yes',
        ]);

        $this->assertSame('denylist', $plan['strategy']['model']);
        $this->assertStringContainsString('Use a denylist keyed by JWT ID', $plan['architecture_summary']['decision']);
        $this->assertStringContainsString('per-token revocation', $plan['architecture_summary']['primary_tradeoff']);
        $this->assertStringContainsString('revocation store', $plan['architecture_summary']['when_not_to_use']);
        $this->assertSame('access token', $plan['threat_model'][0]['asset']);
        $this->assertStringContainsString('denylist', $plan['threat_model'][0]['control']);
        $this->assertSame('browser session', $plan['threat_model'][3]['asset']);
        $this->assertSame('triage', $plan['incident_playbook'][0]['phase']);
        $this->assertContains('token_rejected and refresh_reuse_detected events', $plan['incident_playbook'][0]['evidence_to_capture']);
        $this->assertStringContainsString('logout-all', $plan['incident_playbook'][1]['actions'][2]);
        $this->assertContains('Inspect browser-facing XSS, CSRF, cookie, and CORS controls before trusting the recovered session.', $plan['incident_playbook'][2]['actions']);
        $this->assertSame('Client type', $plan['decision_matrix'][0]['signal']);
        $this->assertSame('browser-spa', $plan['decision_matrix'][0]['value']);
        $this->assertSame('controlled', $plan['risk_score']['level']);
        $this->assertContains('denylist adds server-side enforcement before authorization.', $plan['risk_score']['reasons']);
        $this->assertStringContainsString('jti', $plan['strategy']['reason']);
        $this->assertStringContainsString('revoked_tokens', $plan['database_schema'][2]);
        $this->assertSame('database', $plan['retention_plan']['storage']);
        $this->assertContains('index on expires_at for pruning expired revocation records', $plan['retention_plan']['indexes']);
        $this->assertStringContainsString('scheduled prune job', $plan['retention_plan']['prune_policy']);
        $this->assertSame('/api/auth/logout-all', $plan['api_endpoints'][3]['path']);
        $this->assertStringContainsString('database', $plan['middleware_flow'][2]);
        $this->assertSame('instrument', $plan['rollout_plan'][0]['phase']);
        $this->assertSame('dual-read', $plan['rollout_plan'][1]['phase']);
        $this->assertContains('Run refresh-token rotation in compatibility mode before rejecting reused refresh tokens.', $plan['rollout_plan'][1]['actions']);
        $this->assertContains('jwt_revoked_total by reason and revocation_model', $plan['observability_plan']['metrics']);
        $this->assertContains('token_rejected with reason, route name, guard, user_id when known, and request correlation ID', $plan['observability_plan']['log_events']);
        $this->assertSame('revocation store unavailable', $plan['failure_mode_plan'][0]['failure']);
        $this->assertStringContainsString('Fail closed', $plan['failure_mode_plan'][0]['default_policy']);
        $this->assertContains('Use database audit records when cache eviction cannot be ruled out.', $plan['failure_mode_plan'][1]['mitigation']);
        $this->assertSame('feature', $plan['test_matrix'][0]['category']);
        $this->assertSame('security', $plan['test_matrix'][1]['category']);
        $this->assertStringContainsString('denylist', $plan['test_matrix'][1]['case']);
        $this->assertSame('privacy', $plan['test_matrix'][3]['category']);
        $this->assertSame('server-side state', $plan['review_checklist'][1]['area']);
        $this->assertArrayHasKey('migration', $plan['implementation_snippets']);
        $this->assertContains('A revoked jti is rejected with 401 even when signature and exp are valid.', $plan['tests']);
    }

    /**
     * Token-version plans invalidate user-wide access after security-sensitive changes.
     */
    public function test_token_version_plan_invalidates_stale_versions(): void
    {
        $plan = (new JwtRevocationPlanService)->plan([
            'client_type' => 'server-api',
            'revocation_model' => 'token-version',
            'token_lifetime' => 'hours',
            'revocation_store' => 'database',
            'immediate_logout' => 'yes',
            'refresh_rotation' => 'yes',
        ]);

        $this->assertSame('token-version', $plan['strategy']['model']);
        $this->assertStringContainsString('user-wide invalidation', $plan['architecture_summary']['primary_tradeoff']);
        $this->assertStringContainsString('Compare token version', $plan['threat_model'][2]['control']);
        $this->assertStringContainsString('token-version', $plan['incident_playbook'][1]['actions'][0]);
        $this->assertStringContainsString('token_version', $plan['database_schema'][2]);
        $this->assertStringContainsString('version claim', $plan['middleware_flow'][2]);
        $this->assertStringContainsString('token-version', $plan['decision_matrix'][1]['decision_impact']);
        $this->assertContains('jwt_stale_version_rejected_total by user_status and route group', $plan['observability_plan']['metrics']);
        $this->assertContains('token_version_bumped with user_id, old_version, new_version, reason, and actor_id', $plan['observability_plan']['log_events']);
        $this->assertContains('index user security_version lookups by user_id or session_id', $plan['retention_plan']['indexes']);
        $this->assertSame('token version drift', $plan['failure_mode_plan'][3]['failure']);
        $this->assertSame('authorization', $plan['test_matrix'][5]['category']);
        $this->assertStringContainsString('stale token version', $plan['test_matrix'][5]['case']);
        $this->assertStringContainsString('stale', $plan['interview_answer']);
    }

    /**
     * Long-lived tokens without refresh rotation are scored as high risk.
     */
    public function test_long_lived_tokens_without_refresh_rotation_are_high_risk(): void
    {
        $plan = (new JwtRevocationPlanService)->plan([
            'client_type' => 'mobile-app',
            'revocation_model' => 'short-lived-access-token',
            'token_lifetime' => 'days',
            'revocation_store' => 'cache',
            'immediate_logout' => 'yes',
            'refresh_rotation' => 'no',
        ]);

        $this->assertSame('high', $plan['risk_score']['level']);
        $this->assertStringContainsString('re-authentication', $plan['architecture_summary']['next_design_decision']);
        $this->assertStringContainsString('very short-lived', $plan['threat_model'][0]['control']);
        $this->assertGreaterThanOrEqual(70, $plan['risk_score']['score']);
        $this->assertContains('Day-long access tokens create a large abuse window after token leakage.', $plan['risk_score']['reasons']);
        $this->assertContains('Without refresh-token rotation, stolen refresh credentials are harder to contain.', $plan['risk_score']['reasons']);
        $this->assertStringContainsString('Day-long tokens keep revoked entries around longer', $plan['retention_plan']['capacity_note']);
    }
}
