<?php

declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;

final class TechnologyImplementationLabTest extends TestCase
{
    /**
     * The technology implementation lab page renders a sequential implementation path.
     */
    public function test_technology_implementation_lab_page_renders_phases_and_workspaces(): void
    {
        $response = $this->get('/practice/technology-implementation-lab/api-validation?family=laravel&language=en&search=api&limit=3');

        $response
            ->assertOk()
            ->assertSee('Technology Implementation Lab: api-validation')
            ->assertSee('Read source records')
            ->assertSee('Create focused implementation files')
            ->assertSee('Connect route, service, and verification')
            ->assertSee('php artisan test --filter ContentBackedApiDrill')
            ->assertSee('Open workspace')
            ->assertSee('Open lab API');
    }

    /**
     * The technology implementation lab API returns phases, commands, and progress data.
     */
    public function test_technology_implementation_lab_api_returns_phases_and_progress_payload(): void
    {
        $response = $this->getJson('/api/practice/technology-implementation-lab/api-validation?family=laravel&language=en&search=api&limit=3');

        $response
            ->assertOk()
            ->assertJsonPath('data.technology', 'api-validation')
            ->assertJsonPath('data.phases.0.label', 'Read source records')
            ->assertJsonPath('data.commands.0', 'php artisan test --filter ContentBackedApiDrill')
            ->assertJsonPath('data.progress_payload.items.0.label', 'Read source records')
            ->assertJsonStructure([
                'data' => [
                    'title',
                    'technology',
                    'source_examples' => [
                        'items' => [
                            '*' => [
                                'record_id',
                                'workspace_query',
                                'snippets',
                            ],
                        ],
                    ],
                    'phases' => [
                        '*' => [
                            'label',
                            'goal',
                            'tasks',
                        ],
                    ],
                    'commands',
                    'progress_payload',
                    'progress_api',
                    'meta',
                ],
            ]);
    }
}
