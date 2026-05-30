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
            ->assertSee('Security Misconfiguration Controls')
            ->assertSee('Deployment Smoke Matrix')
            ->assertSee('Release Blockers')
            ->assertSee('Production debug guard')
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
            ->assertJsonPath('data.misconfiguration_controls.0.area', 'Runtime environment')
            ->assertJsonPath('data.misconfiguration_controls.2.expected_control', 'CORS uses explicit origins, headers are present, secure cookies are enabled, HTTPS is enforced, and proxy trust is scoped.')
            ->assertJsonPath('data.deployment_smoke_matrix.0.check', 'Production debug guard')
            ->assertJsonPath('data.deployment_smoke_matrix.2.unsafe_signal', 'CORS allows wildcard credentialed origins, required security headers are missing, or cookies are not secure.')
            ->assertJsonPath('data.release_blockers.1', 'Any production debug, secret exposure, broad CORS, missing header, weak cookie, proxy drift, or public storage signal blocks release.')
            ->assertJsonPath('data.commands.0', 'php artisan test --filter ConfigurationReadinessTest')
            ->assertJsonPath('data.commands.2', 'php artisan route:list --path=configuration-readiness')
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
                    'misconfiguration_controls' => [
                        '*' => [
                            'area',
                            'risk',
                            'expected_control',
                            'evidence',
                            'owner',
                        ],
                    ],
                    'deployment_smoke_matrix' => [
                        '*' => [
                            'check',
                            'unsafe_signal',
                            'fail_closed_action',
                            'verify_with',
                        ],
                    ],
                    'release_blockers',
                    'quality_gate',
                    'baseline',
                    'commands',
                    'practice_targets',
                    'next_steps',
                ],
            ]);
    }
}
