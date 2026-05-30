<?php

declare(strict_types=1);

namespace Tests\Unit\Practice;

use App\Services\Practice\OauthFlowPlanService;
use PHPUnit\Framework\TestCase;

final class OauthFlowPlanServiceTest extends TestCase
{
    /**
     * Service-to-service clients receive Client Credentials guidance.
     */
    public function test_service_client_uses_client_credentials_flow(): void
    {
        $plan = (new OauthFlowPlanService)->plan([
            'client_type' => 'service-to-service',
            'current_flow' => 'client-credentials',
            'can_keep_secret' => 'yes',
            'pkce_supported' => 'no',
            'refresh_token_needed' => 'no',
            'browser_history_risk' => 'low',
        ]);

        $this->assertSame('client-credentials', $plan['recommendation']['flow']);
        $this->assertFalse($plan['recommendation']['migration_needed']);
        $this->assertFalse($plan['pkce_sequence_simulation']['applies']);
        $this->assertSame('provider lacks PKCE', $plan['pkce_failure_drill'][3]['case']);
        $this->assertSame('client authentication', $plan['client_credentials_checklist'][0]['area']);
        $this->assertSame('audience', $plan['client_credentials_checklist'][1]['area']);
        $this->assertSame('Orders API', $plan['client_credentials_access_contract'][0]['resource']);
        $this->assertSame('api://orders', $plan['client_credentials_access_contract'][0]['audience']);
        $this->assertContains('users.impersonate', $plan['client_credentials_access_contract'][2]['denied_by_default']);
        $this->assertSame('iss', $plan['client_credentials_token_validation_policy'][0]['claim']);
        $this->assertSame('aud', $plan['client_credentials_token_validation_policy'][1]['claim']);
        $this->assertSame('scope', $plan['client_credentials_token_validation_policy'][3]['claim']);
        $this->assertSame('sub', $plan['client_credentials_token_validation_policy'][4]['claim']);
        $this->assertTrue($plan['client_credentials_operational_plan']['applies']);
        $this->assertContains('oauth_client_secret_rotated', $plan['client_credentials_operational_plan']['audit_events']);
        $this->assertSame('prepare', $plan['client_credentials_rotation_drill'][0]['phase']);
        $this->assertSame('dual-secret rollout', $plan['client_credentials_rotation_drill'][1]['phase']);
        $this->assertSame('audit', $plan['client_credentials_rotation_drill'][3]['phase']);
        $this->assertTrue($plan['client_credentials_monitoring_signals']['applies']);
        $this->assertContains('oauth_client_auth_failed_total by client_id, credential_fingerprint, and failure_reason', $plan['client_credentials_monitoring_signals']['metrics']);
        $this->assertContains('Alert when a client secret exceeds the maximum rotation age.', $plan['client_credentials_monitoring_signals']['alerts']);
        $this->assertSame('client authentication failure', $plan['client_credentials_failure_playbook'][0]['failure']);
        $this->assertSame('wrong audience', $plan['client_credentials_failure_playbook'][1]['failure']);
        $this->assertSame('suspected client secret leak', $plan['client_credentials_failure_playbook'][4]['failure']);
        $this->assertSame('confidential credential boundary', $plan['client_credentials_security_review_rubric'][0]['criterion']);
        $this->assertSame(100, array_sum(array_column($plan['client_credentials_security_review_rubric'], 'weight')));
        $this->assertSame('machine-to-machine only', $plan['client_credentials_interview_checklist'][0]['point']);
        $this->assertSame('operations', $plan['client_credentials_interview_checklist'][4]['point']);
        $this->assertSame('Token endpoint requires confidential client authentication.', $plan['tests'][0]);
    }

    /**
     * Browser clients do not get service-token ownership as the main plan.
     */
    public function test_browser_client_gets_non_applicable_client_credentials_plan(): void
    {
        $plan = (new OauthFlowPlanService)->plan([
            'client_type' => 'legacy-spa',
            'current_flow' => 'implicit',
            'can_keep_secret' => 'no',
            'pkce_supported' => 'yes',
            'refresh_token_needed' => 'yes',
            'browser_history_risk' => 'high',
        ]);

        $this->assertSame('authorization-code-pkce', $plan['recommendation']['flow']);
        $this->assertTrue($plan['pkce_sequence_simulation']['applies']);
        $this->assertSame('Authorization Code with PKCE sequence', $plan['pkce_sequence_simulation']['title']);
        $this->assertSame('1. Create verifier', $plan['pkce_sequence_simulation']['steps'][0]['stage']);
        $this->assertSame('wrong verifier', $plan['pkce_failure_drill'][1]['case']);
        $this->assertFalse($plan['client_credentials_operational_plan']['applies']);
        $this->assertSame('flow fit', $plan['client_credentials_checklist'][0]['area']);
        $this->assertSame('user-facing OAuth client', $plan['client_credentials_access_contract'][0]['resource']);
        $this->assertContains('client_credentials', $plan['client_credentials_access_contract'][0]['denied_by_default']);
        $this->assertSame('grant_type', $plan['client_credentials_token_validation_policy'][0]['claim']);
        $this->assertSame('oauth_flow_selected', $plan['client_credentials_operational_plan']['audit_events'][0]);
        $this->assertSame('not applicable', $plan['client_credentials_rotation_drill'][0]['phase']);
        $this->assertFalse($plan['client_credentials_monitoring_signals']['applies']);
        $this->assertSame('oauth_client_credentials_rejected_total by client_type and client_id', $plan['client_credentials_monitoring_signals']['metrics'][0]);
        $this->assertSame('wrong flow requested', $plan['client_credentials_failure_playbook'][0]['failure']);
        $this->assertSame('flow eligibility', $plan['client_credentials_security_review_rubric'][0]['criterion']);
        $this->assertSame(100, $plan['client_credentials_security_review_rubric'][0]['weight']);
        $this->assertSame('flow fit', $plan['client_credentials_interview_checklist'][0]['point']);
    }
}
