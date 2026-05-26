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
                    'rollback_steps',
                    'evidence',
                    'commands',
                    'quality_gate',
                ],
            ]);
    }
}
