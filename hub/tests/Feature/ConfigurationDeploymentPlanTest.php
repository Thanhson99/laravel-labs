<?php

declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;

final class ConfigurationDeploymentPlanTest extends TestCase
{
    /**
     * The configuration deployment plan page renders deploy and rollback guidance.
     */
    public function test_configuration_deployment_plan_page_renders_deploy_and_rollback_guidance(): void
    {
        $response = $this->get('/practice/configuration-deployment-plan');

        $response
            ->assertOk()
            ->assertSee('Configuration Deployment Plan')
            ->assertSee('php artisan config:clear')
            ->assertSee('Smoke Checks')
            ->assertSee('Security Misconfiguration Controls')
            ->assertSee('Release Blockers')
            ->assertSee('Production debug guard')
            ->assertSee('Rollback')
            ->assertSee('Open deployment API');
    }

    /**
     * The configuration deployment plan API returns preflight, smoke, and rollback steps.
     */
    public function test_configuration_deployment_plan_api_returns_deployment_steps(): void
    {
        $response = $this->getJson('/api/practice/configuration-deployment-plan');

        $response
            ->assertOk()
            ->assertJsonPath('data.deploy_steps.1.command', 'php artisan config:clear')
            ->assertJsonPath('data.smoke_checks.0.endpoint', '/practice/configuration-readiness')
            ->assertJsonPath('data.smoke_checks.3.expected', 'Production debug guard: Block deploy and restore the last known-good environment file or secret set.')
            ->assertJsonPath('data.security_misconfiguration_controls.0.area', 'Runtime environment')
            ->assertJsonPath('data.release_blockers.1', 'Any production debug, secret exposure, broad CORS, missing header, weak cookie, proxy drift, or public storage signal blocks release.')
            ->assertJsonPath('data.rollback_steps.1.area', 'Authentication contract')
            ->assertJsonPath('data.quality_gate.status', 'ready')
            ->assertJsonStructure([
                'data' => [
                    'title',
                    'summary',
                    'source_files',
                    'preflight',
                    'deploy_steps',
                    'smoke_checks',
                    'security_misconfiguration_controls',
                    'release_blockers',
                    'rollback_steps',
                    'evidence',
                    'commands',
                    'quality_gate',
                ],
            ]);
    }
}
