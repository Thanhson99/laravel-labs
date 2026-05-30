<?php

declare(strict_types=1);

namespace Tests\Unit\Practice;

use App\Services\Practice\JwtTokenStoragePlanService;
use PHPUnit\Framework\TestCase;

final class JwtTokenStoragePlanServiceTest extends TestCase
{
    /**
     * Browser flows with strong CSRF controls prefer HttpOnly cookies.
     */
    public function test_browser_flow_with_strong_csrf_controls_prefers_http_only_cookie(): void
    {
        $plan = (new JwtTokenStoragePlanService)->plan([
            'client_type' => 'cross-domain-spa',
            'current_storage' => 'localStorage',
            'token_lifetime' => 'hours',
            'xss_risk' => 'medium',
            'csrf_controls' => 'strong',
            'refresh_token' => 'yes',
        ]);

        $this->assertSame('http-only-cookie', $plan['recommendation']['storage']);
        $this->assertSame('Client type', $plan['decision_matrix'][0]['signal']);
        $this->assertSame('cross-domain-spa', $plan['decision_matrix'][0]['value']);
        $this->assertSame('medium', $plan['risk_score']['level']);
        $this->assertContains('Set HttpOnly, Secure, SameSite, Path, Domain, and CSRF headers deliberately.', $plan['controls']);
        $this->assertSame('cookie flags', $plan['review_checklist'][3]['area']);
        $this->assertStringContainsString("credentials: 'include'", $plan['implementation_snippets']['frontend_request']);
        $this->assertTrue($plan['migration_plan']['needed']);
        $this->assertSame('http-only-cookie', $plan['migration_plan']['to']);
    }

    /**
     * Mobile clients use platform storage instead of browser storage assumptions.
     */
    public function test_mobile_client_uses_secure_platform_storage(): void
    {
        $plan = (new JwtTokenStoragePlanService)->plan([
            'client_type' => 'mobile-app',
            'current_storage' => 'platform-storage',
            'token_lifetime' => 'hours',
            'xss_risk' => 'low',
            'csrf_controls' => 'none',
            'refresh_token' => 'yes',
        ]);

        $this->assertSame('secure-platform-storage', $plan['recommendation']['storage']);
        $this->assertSame('Mobile clients should use OS-backed secure storage rather than browser storage assumptions.', $plan['decision_matrix'][0]['impact']);
        $this->assertSame('Use secure platform storage', $plan['recommendation']['label']);
        $this->assertSame('medium', $plan['risk_score']['level']);
        $this->assertArrayHasKey('mobile_storage', $plan['implementation_snippets']);
        $this->assertFalse($plan['migration_plan']['needed']);
    }

    /**
     * Long-lived localStorage plans are not promoted into production-safe guidance.
     */
    public function test_local_storage_demo_plan_includes_promotion_guard(): void
    {
        $plan = (new JwtTokenStoragePlanService)->plan([
            'client_type' => 'low-risk-demo',
            'current_storage' => 'localStorage',
            'token_lifetime' => 'minutes',
            'xss_risk' => 'low',
            'csrf_controls' => 'none',
            'refresh_token' => 'no',
        ]);

        $this->assertSame('localStorage-limited-demo', $plan['recommendation']['storage']);
        $this->assertSame('promotion guard', $plan['review_checklist'][3]['area']);
        $this->assertArrayHasKey('review_warning', $plan['implementation_snippets']);
        $this->assertContains('localStorage is JavaScript-readable, so it should stay limited to low-risk learning contexts.', $plan['risk_score']['reasons']);
    }
}
