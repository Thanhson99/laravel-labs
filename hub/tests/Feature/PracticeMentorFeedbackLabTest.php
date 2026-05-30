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

    /**
     * Predictive AI mentor feedback reviews output contracts, metrics, and failure evidence.
     */
    public function test_predictive_generative_ai_mentor_feedback_uses_ai_type_review(): void
    {
        $response = $this->getJson('/api/practice/mentor-feedback-lab?technology=llm-foundations&family=vibe-coding&language=en&search=Predictive%20AI&limit=1');

        $response
            ->assertOk()
            ->assertJsonPath('data.technology', 'llm-foundations')
            ->assertJsonPath('data.feedback_items.0.source_path', 'vibe-coding/prompting.en.json')
            ->assertJsonPath('data.feedback_items.0.workspace_query.technology', 'llm-foundations')
            ->assertJsonPath('data.feedback_items.0.risk', 'The explanation may collapse prediction and generation into one generic AI story without separate evidence.')
            ->assertJsonPath('data.feedback_items.0.action_item', 'Open the workspace for task 1, add one predictive metric and one generative quality check, then update portfolio evidence.')
            ->assertJsonPath('data.mentor_questions.0', 'Which source record most clearly separates prediction output from generation output?')
            ->assertJsonPath('data.review_focus.2', 'Predictive metrics and generative quality checks are not reused interchangeably.');

        $this->assertStringContainsString(
            'output contract',
            $response->json('data.feedback_items.0.mentor_comment')
        );
    }

    /**
     * JavaScript closure mentor feedback checks lexical scope and common interview traps.
     */
    public function test_javascript_closure_mentor_feedback_uses_scope_review(): void
    {
        $response = $this->getJson('/api/practice/mentor-feedback-lab?technology=javascript-closures&family=laravel&language=en&search=JavaScript%20closure&limit=1');

        $response
            ->assertOk()
            ->assertJsonPath('data.technology', 'javascript-closures')
            ->assertJsonPath('data.feedback_items.0.source_path', 'laravel/frontend.en.json')
            ->assertJsonPath('data.feedback_items.0.workspace_query.technology', 'javascript-closures')
            ->assertJsonPath('data.feedback_items.0.risk', 'The explanation may stop at "inner function remembers variables" without tracing lexical scope, captured bindings, var versus let, or stale closures.')
            ->assertJsonPath('data.mentor_questions.1', 'Where does createCounter() keep state between calls, and which binding is captured?')
            ->assertJsonPath('data.review_focus.3', 'Interview-trap evidence covers var versus let and stale closures.');

        $this->assertStringContainsString(
            'captured bindings',
            $response->json('data.feedback_items.0.mentor_comment')
        );
    }
}
