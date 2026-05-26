<?php

declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;

final class ConfigurationTestPlanTest extends TestCase
{
    /**
     * The configuration test plan page renders grouped assertions and a starter snippet.
     */
    public function test_configuration_test_plan_page_renders_grouped_assertions(): void
    {
        $response = $this->get('/practice/configuration-test-plan');

        $response
            ->assertOk()
            ->assertSee('Configuration Test Plan')
            ->assertSee('Application runtime contract')
            ->assertSee('Authentication contract')
            ->assertSee("config('auth.guards.web.driver')")
            ->assertSee('Starter Snippet')
            ->assertSee('Open test plan API');
    }

    /**
     * The configuration test plan API returns snippet and verification commands.
     */
    public function test_configuration_test_plan_api_returns_snippet_and_commands(): void
    {
        $response = $this->getJson('/api/practice/configuration-test-plan');

        $response
            ->assertOk()
            ->assertJsonPath('data.target_test', 'hub/tests/Feature/ConfigurationReadinessTest.php')
            ->assertJsonPath('data.test_groups.1.name', 'Authentication contract')
            ->assertJsonPath('data.commands.1', 'php artisan test --filter ConfigurationTestPlanTest')
            ->assertJsonPath('data.quality_gate.status', 'ready')
            ->assertJsonStructure([
                'data' => [
                    'title',
                    'summary',
                    'target_test',
                    'source_files',
                    'test_groups' => [
                        '*' => [
                            'name',
                            'checks',
                            'assertions',
                        ],
                    ],
                    'snippet',
                    'commands',
                    'quality_gate',
                    'baseline',
                ],
            ]);
    }
}
