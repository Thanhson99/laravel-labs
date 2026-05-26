<?php

declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;

final class CacheStrategyPlanWorkbenchTest extends TestCase
{
    /**
     * The cache strategy workbench renders the performance planning loop.
     */
    public function test_cache_strategy_plan_workbench_renders(): void
    {
        $response = $this->get('/workbench/cache-strategy-plan');

        $response
            ->assertOk()
            ->assertSee('Cache Strategy Plan Workbench')
            ->assertSee('POST /api/practice/cache-strategy-plan')
            ->assertSee('CacheStrategyPlanService')
            ->assertSee('Plan cache strategy');
    }

    /**
     * The cache strategy API returns key, TTL, strategy, and commands.
     */
    public function test_cache_strategy_plan_api_returns_plan(): void
    {
        $response = $this->postJson('/api/practice/cache-strategy-plan', [
            'resource_name' => 'Practice dashboard',
            'scope' => 'user 42',
            'ttl_minutes' => 15,
            'invalidation_trigger' => 'Clear when practice progress is updated',
        ]);

        $response
            ->assertOk()
            ->assertJsonPath('data.cache_key', 'practice:practice-dashboard:user-42')
            ->assertJsonPath('data.ttl_seconds', 900)
            ->assertJsonPath('data.strategy.invalidation_trigger', 'Clear when practice progress is updated')
            ->assertJsonPath('data.steps.0', 'Name the cache key with resource and scope.');
    }

    /**
     * Invalid cache strategy payloads return validation errors.
     */
    public function test_cache_strategy_plan_api_validates_payload(): void
    {
        $response = $this->postJson('/api/practice/cache-strategy-plan', [
            'resource_name' => 'x',
            'scope' => '',
            'ttl_minutes' => 0,
            'invalidation_trigger' => 'bad',
        ]);

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['resource_name', 'scope', 'ttl_minutes', 'invalidation_trigger']);
    }
}
