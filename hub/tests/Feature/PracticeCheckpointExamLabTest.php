<?php

declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;

final class PracticeCheckpointExamLabTest extends TestCase
{
    /**
     * The checkpoint exam page renders warmups, coding tasks, and pass criteria.
     */
    public function test_checkpoint_exam_lab_page_renders_exam_sections(): void
    {
        $response = $this->get('/practice/checkpoint-exam-lab?technology=api-validation&family=laravel&language=en&search=api&limit=2');

        $response
            ->assertOk()
            ->assertSee('Checkpoint exam lab validates one technology through a timed coding task.')
            ->assertSee('Warmup Questions')
            ->assertSee('Coding Tasks')
            ->assertSee('Checkpoint Progress Payload');
    }

    /**
     * The checkpoint exam API returns exam tasks and pass criteria.
     */
    public function test_checkpoint_exam_lab_api_returns_exam_payload(): void
    {
        $response = $this->getJson('/api/practice/checkpoint-exam-lab?technology=api-validation&family=laravel&language=en&search=api&limit=2');

        $response
            ->assertOk()
            ->assertJsonPath('data.technology', 'api-validation')
            ->assertJsonPath('data.duration_minutes', 70)
            ->assertJsonPath('data.coding_tasks.0.workspace_query.technology', 'api-validation')
            ->assertJsonPath('data.pass_criteria.0', 'All coding tasks have focused verification evidence.')
            ->assertJsonStructure([
                'data' => [
                    'title',
                    'technology',
                    'duration_minutes',
                    'instructions',
                    'warmup_questions',
                    'coding_tasks' => [
                        '*' => [
                            'position',
                            'record_id',
                            'prompt',
                            'source_path',
                            'workspace_query',
                            'expected_evidence',
                        ],
                    ],
                    'oral_review',
                    'pass_criteria',
                    'meta',
                    'progress_payload' => [
                        'items',
                    ],
                ],
            ]);
    }
}
