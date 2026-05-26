<?php

declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;

final class PracticeCapstoneLabTest extends TestCase
{
    /**
     * The capstone lab page renders source coverage and task links.
     */
    public function test_capstone_lab_page_renders_tasks_and_source_coverage(): void
    {
        $response = $this->get('/practice/capstone-lab?technology=api-validation&family=laravel&language=en&search=api&limit=2');

        $response
            ->assertOk()
            ->assertSee('Capstone lab turns multiple records into a focused Laravel mini-project.')
            ->assertSee('Source Coverage')
            ->assertSee('Capstone Tasks')
            ->assertSee('Capstone Progress Payload');
    }

    /**
     * The capstone lab API returns tasks, deliverables, and progress payload.
     */
    public function test_capstone_lab_api_returns_tasks_and_deliverables(): void
    {
        $response = $this->getJson('/api/practice/capstone-lab?technology=api-validation&family=laravel&language=en&search=api&limit=2');

        $response
            ->assertOk()
            ->assertJsonPath('data.technology', 'api-validation')
            ->assertJsonPath('data.meta.task_count', 2)
            ->assertJsonPath('data.tasks.0.workspace_query.technology', 'api-validation')
            ->assertJsonPath('data.source_coverage.0.path', 'laravel/api-integration.en.json')
            ->assertJsonStructure([
                'data' => [
                    'title',
                    'technology',
                    'mission',
                    'source_coverage',
                    'tasks' => [
                        '*' => [
                            'position',
                            'record_id',
                            'question',
                            'source',
                            'workspace_query',
                            'estimated_minutes',
                            'deliverable',
                        ],
                    ],
                    'deliverables',
                    'artifact_queries',
                    'meta',
                    'progress_payload' => [
                        'items',
                    ],
                ],
            ]);
    }
}
