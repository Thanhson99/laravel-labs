<?php

declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;

final class OauthFlowPlanWorkbenchTest extends TestCase
{
    /**
     * The OAuth flow workbench renders the flow comparison form.
     */
    public function test_oauth_flow_plan_workbench_renders(): void
    {
        $response = $this->get('/workbench/oauth-flow-plan');

        $response
            ->assertOk()
            ->assertSee('OAuth Flow Plan Workbench')
            ->assertSee('POST /api/practice/oauth-flow-plan')
            ->assertSee('OauthFlowPlanService')
            ->assertSee('Legacy SPA using Implicit')
            ->assertSee('Service-to-service Client Credentials')
            ->assertSee('Recommendation')
            ->assertSee('Risk score')
            ->assertSee('Security tests')
            ->assertSee('Implementation snippets')
            ->assertSee('PKCE simulation')
            ->assertSee('Run the planner to see the Authorization Code with PKCE sequence.')
            ->assertSee('PKCE failure drill')
            ->assertSee('Run the planner to generate verifier and code-reuse failure tests.')
            ->assertSee('Client credentials checks')
            ->assertSee('Access contract')
            ->assertSee('Run the planner to map service-token audience and scope boundaries.')
            ->assertSee('Token validation policy')
            ->assertSee('Run the planner to generate resource-server token validation rules.')
            ->assertSee('Operational plan')
            ->assertSee('Audit events appear after planning.')
            ->assertSee('Rotation drill')
            ->assertSee('Run the planner to generate client-secret rotation steps.')
            ->assertSee('Monitoring signals')
            ->assertSee('Run the planner to generate service-token metrics, logs, and alerts.')
            ->assertSee('Failure playbook')
            ->assertSee('Run the planner to generate service-token failure responses.')
            ->assertSee('Security review rubric')
            ->assertSee('Run the planner to generate Client Credentials review criteria.')
            ->assertSee('Interview checklist')
            ->assertSee('Run the planner to generate Client Credentials interview talking points.')
            ->assertSee('Interview answer')
            ->assertSee('Plan OAuth flow');
    }

    /**
     * Legacy SPAs are moved from Implicit to Authorization Code with PKCE.
     */
    public function test_oauth_flow_plan_api_recommends_pkce_for_legacy_spa(): void
    {
        $response = $this->postJson('/api/practice/oauth-flow-plan', [
            'client_type' => 'legacy-spa',
            'current_flow' => 'implicit',
            'can_keep_secret' => 'no',
            'pkce_supported' => 'yes',
            'refresh_token_needed' => 'yes',
            'browser_history_risk' => 'high',
        ]);

        $response
            ->assertOk()
            ->assertJsonPath('data.recommendation.flow', 'authorization-code-pkce')
            ->assertJsonPath('data.recommendation.migration_needed', true)
            ->assertJsonPath('data.architecture_decision_record.status', 'proposed-migration')
            ->assertJsonPath('data.compatibility_notes.0.recommended_flow', 'authorization-code-pkce')
            ->assertJsonPath('data.flow_comparison.0.area', 'token delivery')
            ->assertJsonPath('data.risk_score.level', 'high')
            ->assertJsonPath('data.threat_model.0.asset', 'access token')
            ->assertJsonPath('data.provider_capability_matrix.0.capability', 'PKCE S256')
            ->assertJsonPath('data.provider_capability_matrix.2.current_status', 'gap')
            ->assertJsonPath('data.sequence_steps.2.implicit', 'Redirect URI receives access_token in the URL fragment.')
            ->assertJsonPath('data.callback_validation_rules.0.rule', 'require authorization code')
            ->assertJsonPath('data.callback_validation_rules.3.rule', 'require PKCE verifier')
            ->assertJsonPath('data.pkce_checklist.3', 'Send response_type=code with code_challenge and code_challenge_method=S256.')
            ->assertJsonPath('data.pkce_sequence_simulation.applies', true)
            ->assertJsonPath('data.pkce_sequence_simulation.title', 'Authorization Code with PKCE sequence')
            ->assertJsonPath('data.pkce_sequence_simulation.steps.0.stage', '1. Create verifier')
            ->assertJsonPath('data.pkce_sequence_simulation.steps.1.security_point', 'The real verifier is not sent through the browser redirect.')
            ->assertJsonPath('data.pkce_sequence_simulation.steps.3.security_point', 'A stolen code fails without the original verifier.')
            ->assertJsonPath('data.pkce_failure_drill.0.case', 'missing verifier')
            ->assertJsonPath('data.pkce_failure_drill.1.response', 'Reject the exchange, clear login attempt state, and record oauth_pkce_verifier_failed without raw verifier.')
            ->assertJsonPath('data.pkce_failure_drill.2.case', 'reused code')
            ->assertJsonPath('data.token_lifetime_policy.access_token', 'Replace long-lived implicit access tokens with short-lived access tokens after code exchange.')
            ->assertJsonPath('data.scope_consent_matrix.2.scope_area', 'offline_access')
            ->assertJsonPath('data.authorize_request_hardening.0.parameter', 'response_type')
            ->assertJsonPath('data.authorize_request_hardening.4.parameter', 'code_challenge')
            ->assertJsonPath('data.token_endpoint_contract.3.field', 'code_verifier')
            ->assertJsonPath('data.frontend_cleanup_plan.0.surface', 'callback route')
            ->assertJsonPath('data.frontend_cleanup_plan.4.surface', 'PKCE verifier cache')
            ->assertJsonPath('data.id_token_validation_rules.1.claim', 'aud')
            ->assertJsonPath('data.implementation_snippets.pkce_authorize_url', 'GET /authorize?response_type=code&client_id=spa&redirect_uri=https://app.example/callback&code_challenge=...&code_challenge_method=S256')
            ->assertJsonPath('data.client_credentials_checklist.0.area', 'flow fit')
            ->assertJsonPath('data.client_credentials_access_contract.0.resource', 'user-facing OAuth client')
            ->assertJsonPath('data.client_credentials_access_contract.0.denied_by_default.0', 'client_credentials')
            ->assertJsonPath('data.client_credentials_token_validation_policy.0.claim', 'grant_type')
            ->assertJsonPath('data.client_credentials_token_validation_policy.0.evidence', 'Authorization middleware separates service-token routes from user-session routes.')
            ->assertJsonPath('data.client_credentials_operational_plan.applies', false)
            ->assertJsonPath('data.client_credentials_operational_plan.audit_events.0', 'oauth_flow_selected')
            ->assertJsonPath('data.client_credentials_rotation_drill.0.phase', 'not applicable')
            ->assertJsonPath('data.client_credentials_rotation_drill.0.verification', 'Token endpoint rejects grant_type=client_credentials for this client_id.')
            ->assertJsonPath('data.client_credentials_monitoring_signals.applies', false)
            ->assertJsonPath('data.client_credentials_monitoring_signals.metrics.0', 'oauth_client_credentials_rejected_total by client_type and client_id')
            ->assertJsonPath('data.client_credentials_failure_playbook.0.failure', 'wrong flow requested')
            ->assertJsonPath('data.client_credentials_failure_playbook.0.owner', 'auth platform owner')
            ->assertJsonPath('data.client_credentials_security_review_rubric.0.criterion', 'flow eligibility')
            ->assertJsonPath('data.client_credentials_security_review_rubric.0.weight', 100)
            ->assertJsonPath('data.client_credentials_interview_checklist.0.point', 'flow fit')
            ->assertJsonPath('data.client_credentials_interview_checklist.0.red_flag', 'Treating a browser, mobile, or user-facing app as if it can safely keep a client secret.')
            ->assertJsonPath('data.review_checklist.0.area', 'flow choice')
            ->assertJsonPath('data.rollout_plan.2.phase', 'enforce')
            ->assertJsonPath('data.client_cutover_checklist.0.item', 'replace authorize URL')
            ->assertJsonPath('data.observability_plan.metrics.3', 'oauth_token_bearing_callback_total for callbacks containing access_token, id_token, token_type, or expires_in')
            ->assertJsonPath('data.failure_modes.2.failure', 'token-bearing callback')
            ->assertJsonPath('data.incident_response_playbook.1.phase', 'contain')
            ->assertJsonPath('data.deprecation_policy.0.rule', 'no new implicit clients')
            ->assertJsonPath('data.security_test_matrix.0.category', 'front-channel token blocking')
            ->assertJsonPath('data.tests.0', 'Authorization callback rejects access_token in the URL fragment or query.')
            ->assertJsonPath('data.commands.1', 'php artisan test --filter OauthFlowPlan');
    }

    /**
     * Backend web clients can use confidential Authorization Code flow.
     */
    public function test_oauth_flow_plan_api_recommends_confidential_client_for_backend(): void
    {
        $response = $this->postJson('/api/practice/oauth-flow-plan', [
            'client_type' => 'server-rendered-web',
            'current_flow' => 'authorization-code',
            'can_keep_secret' => 'yes',
            'pkce_supported' => 'yes',
            'refresh_token_needed' => 'yes',
            'browser_history_risk' => 'low',
        ]);

        $response
            ->assertOk()
            ->assertJsonPath('data.recommendation.flow', 'authorization-code-confidential-client')
            ->assertJsonPath('data.recommendation.migration_needed', false)
            ->assertJsonPath('data.pkce_sequence_simulation.applies', false)
            ->assertJsonPath('data.pkce_sequence_simulation.title', 'PKCE is optional for this confidential-client scenario.')
            ->assertJsonPath('data.architecture_decision_record.status', 'accepted')
            ->assertJsonPath('data.compatibility_notes.2.recommended_flow', 'authorization-code-confidential-client')
            ->assertJsonPath('data.provider_capability_matrix.2.current_status', 'must-verify')
            ->assertJsonPath('data.token_lifetime_policy.refresh_token', 'Use refresh-token rotation with reuse detection and family revocation.')
            ->assertJsonPath('data.scope_consent_matrix.3.scope_area', 'admin or write scopes')
            ->assertJsonPath('data.authorize_request_hardening.1.parameter', 'redirect_uri')
            ->assertJsonPath('data.token_endpoint_contract.3.field', 'client_secret')
            ->assertJsonPath('data.frontend_cleanup_plan.1.surface', 'browser URL')
            ->assertJsonPath('data.id_token_validation_rules.3.claim', 'nonce')
            ->assertJsonPath('data.rollout_plan.0.phase', 'inventory')
            ->assertJsonPath('data.client_cutover_checklist.1.owner', 'client developer')
            ->assertJsonPath('data.observability_plan.log_events.2', 'oauth_code_exchanged with client_id, grant_type, pkce_verified, and token_family_id')
            ->assertJsonPath('data.failure_modes.3.failure', 'redirect URI drift')
            ->assertJsonPath('data.incident_response_playbook.2.phase', 'recover')
            ->assertJsonPath('data.deprecation_policy.2.rule', 'public clients require PKCE')
            ->assertJsonPath('data.security_test_matrix.4.category', 'refresh-token rotation')
            ->assertJsonPath('data.callback_validation_rules.2.rule', 'verify redirect URI')
            ->assertJsonPath('data.tests.5', 'Client secret is used only from the backend and never rendered to JavaScript.');
    }

    /**
     * Machine-to-machine clients use Client Credentials Flow.
     */
    public function test_oauth_flow_plan_api_recommends_client_credentials_for_service_clients(): void
    {
        $response = $this->postJson('/api/practice/oauth-flow-plan', [
            'client_type' => 'service-to-service',
            'current_flow' => 'client-credentials',
            'can_keep_secret' => 'yes',
            'pkce_supported' => 'no',
            'refresh_token_needed' => 'no',
            'browser_history_risk' => 'low',
        ]);

        $response
            ->assertOk()
            ->assertJsonPath('data.recommendation.flow', 'client-credentials')
            ->assertJsonPath('data.recommendation.migration_needed', false)
            ->assertJsonPath('data.pkce_sequence_simulation.applies', false)
            ->assertJsonPath('data.pkce_failure_drill.3.case', 'provider lacks PKCE')
            ->assertJsonPath('data.compatibility_notes.3.recommended_flow', 'client-credentials')
            ->assertJsonPath('data.implementation_snippets.client_credentials_token_request', "POST /oauth/token\nAuthorization: Basic base64(client_id:client_secret)\nContent-Type: application/x-www-form-urlencoded\n\ngrant_type=client_credentials&scope=orders.read")
            ->assertJsonPath('data.security_test_matrix.0.category', 'front-channel token blocking')
            ->assertJsonPath('data.security_test_matrix.4.category', 'client authentication')
            ->assertJsonPath('data.security_test_matrix.5.category', 'audience and scope')
            ->assertJsonPath('data.client_credentials_checklist.0.area', 'client authentication')
            ->assertJsonPath('data.client_credentials_checklist.1.area', 'audience')
            ->assertJsonPath('data.client_credentials_checklist.4.area', 'no user delegation')
            ->assertJsonPath('data.client_credentials_access_contract.0.resource', 'Orders API')
            ->assertJsonPath('data.client_credentials_access_contract.0.audience', 'api://orders')
            ->assertJsonPath('data.client_credentials_access_contract.0.allowed_scopes.0', 'orders.read')
            ->assertJsonPath('data.client_credentials_access_contract.1.denied_by_default.0', 'billing.write')
            ->assertJsonPath('data.client_credentials_access_contract.2.allowed_scopes', [])
            ->assertJsonPath('data.client_credentials_token_validation_policy.0.claim', 'iss')
            ->assertJsonPath('data.client_credentials_token_validation_policy.1.claim', 'aud')
            ->assertJsonPath('data.client_credentials_token_validation_policy.2.claim', 'exp')
            ->assertJsonPath('data.client_credentials_token_validation_policy.3.claim', 'scope')
            ->assertJsonPath('data.client_credentials_token_validation_policy.4.reject_when', 'Token has a user subject, impersonation scope, or ambiguous subject type.')
            ->assertJsonPath('data.client_credentials_operational_plan.applies', true)
            ->assertJsonPath('data.client_credentials_operational_plan.audit_events.4', 'oauth_client_secret_rotated')
            ->assertJsonPath('data.client_credentials_operational_plan.incident_trigger', 'Treat unexpected audience, excessive scope, repeated failed client authentication, or leaked client secret as a service-credential incident.')
            ->assertJsonPath('data.client_credentials_rotation_drill.0.phase', 'prepare')
            ->assertJsonPath('data.client_credentials_rotation_drill.1.phase', 'dual-secret rollout')
            ->assertJsonPath('data.client_credentials_rotation_drill.2.phase', 'revoke old secret')
            ->assertJsonPath('data.client_credentials_rotation_drill.3.verification', 'Audit log contains oauth_client_secret_rotated and no raw secret material.')
            ->assertJsonPath('data.client_credentials_monitoring_signals.applies', true)
            ->assertJsonPath('data.client_credentials_monitoring_signals.metrics.1', 'oauth_client_auth_failed_total by client_id, credential_fingerprint, and failure_reason')
            ->assertJsonPath('data.client_credentials_monitoring_signals.metrics.3', 'oauth_service_audience_denied_total by client_id, token_audience, and resource_audience')
            ->assertJsonPath('data.client_credentials_monitoring_signals.logs.3', 'oauth_client_secret_rotated with client_id, owner_team, old_fingerprint, new_fingerprint, and approver')
            ->assertJsonPath('data.client_credentials_monitoring_signals.alerts.2', 'Alert when a client secret exceeds the maximum rotation age.')
            ->assertJsonPath('data.client_credentials_failure_playbook.0.failure', 'client authentication failure')
            ->assertJsonPath('data.client_credentials_failure_playbook.1.failure', 'wrong audience')
            ->assertJsonPath('data.client_credentials_failure_playbook.2.owner', 'authorization policy owner')
            ->assertJsonPath('data.client_credentials_failure_playbook.3.failure', 'expired service token')
            ->assertJsonPath('data.client_credentials_failure_playbook.4.owner', 'incident commander')
            ->assertJsonPath('data.client_credentials_security_review_rubric.0.criterion', 'confidential credential boundary')
            ->assertJsonPath('data.client_credentials_security_review_rubric.0.weight', 25)
            ->assertJsonPath('data.client_credentials_security_review_rubric.1.criterion', 'audience and scope enforcement')
            ->assertJsonPath('data.client_credentials_security_review_rubric.2.weight', 20)
            ->assertJsonPath('data.client_credentials_security_review_rubric.4.criterion', 'monitoring and incident response')
            ->assertJsonPath('data.client_credentials_interview_checklist.0.point', 'machine-to-machine only')
            ->assertJsonPath('data.client_credentials_interview_checklist.1.point', 'confidential client authentication')
            ->assertJsonPath('data.client_credentials_interview_checklist.2.point', 'audience and scope boundary')
            ->assertJsonPath('data.client_credentials_interview_checklist.3.point', 'no user delegation')
            ->assertJsonPath('data.client_credentials_interview_checklist.4.point', 'operations')
            ->assertJsonPath('data.tests.0', 'Token endpoint requires confidential client authentication.')
            ->assertJsonPath('data.tests.2', 'Resource server rejects service token with missing scope or wrong audience.')
            ->assertJsonPath('data.interview_answer', 'Client Credentials Flow is OAuth2 for machine-to-machine access. There is no user login: a confidential backend service authenticates to the token endpoint as itself, receives a scoped access token, and the resource server validates issuer, audience, expiry, signature, and scopes. I would use it for internal service calls, workers, scheduled jobs, and microservice APIs, but not for SPAs or mobile apps because they cannot keep a client secret.');
    }

    /**
     * Invalid OAuth flow payloads return validation errors.
     */
    public function test_oauth_flow_plan_api_validates_payload(): void
    {
        $response = $this->postJson('/api/practice/oauth-flow-plan', [
            'client_type' => 'desktop',
            'current_flow' => 'password',
            'can_keep_secret' => 'maybe',
            'pkce_supported' => 'maybe',
            'refresh_token_needed' => 'maybe',
            'browser_history_risk' => 'unknown',
        ]);

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors([
                'client_type',
                'current_flow',
                'can_keep_secret',
                'pkce_supported',
                'refresh_token_needed',
                'browser_history_risk',
            ]);
    }
}
