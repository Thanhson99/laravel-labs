<?php

declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;

final class ConfigurationReadinessTest extends TestCase
{
    /**
     * The configuration readiness page renders app and auth checks.
     */
    public function test_configuration_readiness_page_renders_app_and_auth_checks(): void
    {
        $response = $this->get('/practice/configuration-readiness');

        $response
            ->assertOk()
            ->assertSee('Configuration Readiness Plan')
            ->assertSee('app_name_present')
            ->assertSee('web_guard_uses_session')
            ->assertSee('users_provider_uses_eloquent')
            ->assertSee('Open readiness API');
    }

    /**
     * The configuration readiness API returns quality-gate compatible baseline data.
     */
    public function test_configuration_readiness_api_returns_quality_gate_baseline(): void
    {
        $response = $this->getJson('/api/practice/configuration-readiness');

        $response
            ->assertOk()
            ->assertJsonPath('data.quality_gate.status', 'ready')
            ->assertJsonPath('data.baseline.failures', 0)
            ->assertJsonPath('data.checks.3.key', 'web_guard_uses_session')
            ->assertJsonPath('data.commands.0', 'php artisan test --filter ConfigurationReadinessTest')
            ->assertJsonStructure([
                'data' => [
                    'title',
                    'summary',
                    'checks' => [
                        '*' => [
                            'key',
                            'label',
                            'value',
                            'passed',
                            'file',
                        ],
                    ],
                    'quality_gate',
                    'baseline',
                    'commands',
                    'practice_targets',
                    'next_steps',
                ],
            ]);
    }
}
