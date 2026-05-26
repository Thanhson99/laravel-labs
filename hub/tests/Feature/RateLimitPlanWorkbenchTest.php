<?php

declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;

final class RateLimitPlanWorkbenchTest extends TestCase
{
    /**
     * The rate-limit workbench renders the API throttling planning loop.
     */
    public function test_rate_limit_plan_workbench_renders(): void
    {
        $response = $this->get('/workbench/rate-limit-plan');

        $response
            ->assertOk()
            ->assertSee('Rate Limit Plan Workbench')
            ->assertSee('POST /api/practice/rate-limit-plan')
            ->assertSee('RateLimitPlanService')
            ->assertSee('Plan rate limit');
    }

    /**
     * The rate-limit API returns limiter, middleware, identity, and test details.
     */
    public function test_rate_limit_plan_api_returns_plan(): void
    {
        $response = $this->postJson('/api/practice/rate-limit-plan', [
            'endpoint_name' => 'Password reset request',
            'actor_type' => 'user',
            'max_attempts' => 5,
            'decay_minutes' => 10,
            'sensitivity' => 'sensitive',
        ]);

        $response
            ->assertOk()
            ->assertJsonPath('data.limiter_name', 'practice-password-reset-request')
            ->assertJsonPath('data.middleware', 'throttle:practice-password-reset-request')
            ->assertJsonPath('data.retry_after_seconds', 600)
            ->assertJsonPath('data.identity_key', 'Use the authenticated user ID when available.')
            ->assertJsonPath('data.response.status', '429 Too Many Requests');
    }

    /**
     * Invalid rate-limit payloads return validation errors.
     */
    public function test_rate_limit_plan_api_validates_payload(): void
    {
        $response = $this->postJson('/api/practice/rate-limit-plan', [
            'endpoint_name' => 'x',
            'actor_type' => 'session',
            'max_attempts' => 0,
            'decay_minutes' => 0,
            'sensitivity' => 'unknown',
        ]);

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors([
                'endpoint_name',
                'actor_type',
                'max_attempts',
                'decay_minutes',
                'sensitivity',
            ]);
    }
}
