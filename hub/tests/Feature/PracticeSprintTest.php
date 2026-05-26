<?php

declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;

final class PracticeSprintTest extends TestCase
{
    /**
     * The sprint page renders phase tasks with workspace and verification links.
     */
    public function test_practice_sprint_page_renders_workspace_and_verification_links(): void
    {
        $response = $this->get('/practice/sprint?family=laravel&language=en&search=api&phase_limit=1&tasks_per_phase=2');

        $response
            ->assertOk()
            ->assertSee('Practice sprint turns the syllabus into concrete coding work.')
            ->assertSee('Open workspace')
            ->assertSee('Open verification plan')
            ->assertSee('Sprint Progress Payload');
    }

    /**
     * The sprint API returns phases, tasks, and progress payload.
     */
    public function test_practice_sprint_api_returns_phases_tasks_and_progress_payload(): void
    {
        $response = $this->getJson('/api/practice/sprint?family=laravel&language=en&search=api&phase_limit=1&tasks_per_phase=2');

        $response
            ->assertOk()
            ->assertJsonPath('data.phases.0.technology', 'api-validation')
            ->assertJsonPath('data.phases.0.queue_query.limit', 2)
            ->assertJsonPath('data.phases.0.tasks.0.workspace_query.technology', 'api-validation')
            ->assertJsonPath('data.meta.phase_count', 1)
            ->assertJsonPath('data.meta.task_count', 2)
            ->assertJsonStructure([
                'data' => [
                    'title',
                    'phases' => [
                        '*' => [
                            'phase',
                            'technology',
                            'title',
                            'exercise',
                            'board_query',
                            'queue_query',
                            'tasks' => [
                                '*' => [
                                    'position',
                                    'record_id',
                                    'question',
                                    'source',
                                    'workspace_query',
                                    'verification_query',
                                    'estimated_minutes',
                                ],
                            ],
                            'estimated_minutes',
                        ],
                    ],
                    'meta',
                    'progress_payload' => [
                        'items',
                    ],
                ],
            ]);
    }
}
