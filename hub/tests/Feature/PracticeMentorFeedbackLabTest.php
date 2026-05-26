<?php

declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;

final class PracticeMentorFeedbackLabTest extends TestCase
{
    /**
     * The mentor feedback lab page renders task feedback and mentor questions.
     */
    public function test_mentor_feedback_lab_page_renders_feedback_items(): void
    {
        $response = $this->get('/practice/mentor-feedback-lab?technology=api-validation&family=laravel&language=en&search=api&limit=2');

        $response
            ->assertOk()
            ->assertSee('Mentor feedback lab turns capstone work into guided review.')
            ->assertSee('Task Feedback')
            ->assertSee('Mentor Questions')
            ->assertSee('Mentor Feedback Progress Payload');
    }

    /**
     * The mentor feedback lab API returns feedback items and progress payload.
     */
    public function test_mentor_feedback_lab_api_returns_feedback_items(): void
    {
        $response = $this->getJson('/api/practice/mentor-feedback-lab?technology=api-validation&family=laravel&language=en&search=api&limit=2');

        $response
            ->assertOk()
            ->assertJsonPath('data.technology', 'api-validation')
            ->assertJsonPath('data.meta.task_count', 2)
            ->assertJsonPath('data.feedback_items.0.source_path', 'laravel/api-integration.en.json')
            ->assertJsonPath('data.feedback_items.0.workspace_query.technology', 'api-validation')
            ->assertJsonStructure([
                'data' => [
                    'title',
                    'technology',
                    'mission',
                    'feedback_items' => [
                        '*' => [
                            'position',
                            'record_id',
                            'question',
                            'source_path',
                            'mentor_comment',
                            'risk',
                            'action_item',
                            'workspace_query',
                        ],
                    ],
                    'mentor_questions',
                    'review_focus',
                    'meta',
                    'progress_payload' => [
                        'items',
                    ],
                ],
            ]);
    }
}
