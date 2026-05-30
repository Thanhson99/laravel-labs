<?php

declare(strict_types=1);

namespace Tests\Unit\Practice;

use App\Services\Practice\CsrfProtectionPlanService;
use PHPUnit\Framework\TestCase;

final class CsrfProtectionPlanServiceTest extends TestCase
{
    /**
     * Cookie-authenticated state changes without tokens are critical.
     */
    public function test_cookie_state_change_without_token_is_critical(): void
    {
        $plan = (new CsrfProtectionPlanService)->plan([
            'flow_name' => 'Email Change',
            'client_type' => 'blade-web',
            'state_changing_method' => 'get',
            'has_csrf_token' => false,
            'same_site' => 'none',
            'uses_cookie_auth' => true,
        ]);

        $this->assertSame('EmailChange', $plan['flow']);
        $this->assertSame('critical', $plan['risk_score']['label']);
        $this->assertContains('The state-changing flow has no CSRF token proof.', $plan['risk_score']['reasons']);
        $this->assertStringContainsString('Move the action to POST', $plan['recommendation']);
        $this->assertSame('CSRF token', $plan['controls'][0]['control']);
        $this->assertSame('None', $plan['same_site_review']['setting']);
        $this->assertSame('GET request attempts a state change', $plan['test_matrix'][3]['case']);
        $this->assertStringContainsString('test_email_change_requires_csrf_protection', $plan['feature_test_snippet']);
        $this->assertStringContainsString('# CSRF Protection Packet: EmailChange', $plan['review_packet_markdown']);
        $this->assertStringContainsString('## Feature Test Snippet', $plan['review_packet_markdown']);
        $this->assertStringContainsString('SameSite cookies reduce', $plan['interview_answer']);
        $this->assertSame('php artisan test --filter CsrfProtectionPlan', $plan['commands'][0]);
    }

    /**
     * Tokenized Blade form flows remain lower risk.
     */
    public function test_tokenized_blade_form_is_lower_risk(): void
    {
        $plan = (new CsrfProtectionPlanService)->plan([
            'flow_name' => 'Profile Update',
            'client_type' => 'blade-web',
            'state_changing_method' => 'post',
            'has_csrf_token' => true,
            'same_site' => 'lax',
            'uses_cookie_auth' => true,
        ]);

        $this->assertSame('medium', $plan['risk_score']['label']);
        $this->assertStringContainsString('keep token validation', $plan['recommendation']);
        $this->assertSame('Lax', $plan['same_site_review']['setting']);
        $this->assertCount(3, $plan['test_matrix']);
        $this->assertStringContainsString('test_profile_update_requires_csrf_protection', $plan['feature_test_snippet']);
    }
}
