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

    /**
     * Predictive AI checkpoint exams require AI type comparison evidence.
     */
    public function test_predictive_generative_ai_checkpoint_exam_uses_ai_type_evidence(): void
    {
        $response = $this->getJson('/api/practice/checkpoint-exam-lab?technology=llm-foundations&family=vibe-coding&language=en&search=Predictive%20AI&limit=1');

        $response
            ->assertOk()
            ->assertJsonPath('data.technology', 'llm-foundations')
            ->assertJsonPath('data.coding_tasks.0.workspace_query.technology', 'llm-foundations')
            ->assertJsonPath('data.coding_tasks.0.source_path', 'vibe-coding/prompting.en.json')
            ->assertJsonPath('data.coding_tasks.0.expected_evidence.0', 'A comparison table separates Predictive AI and Generative AI output contracts.')
            ->assertJsonPath('data.coding_tasks.0.expected_evidence.1', 'At least one predictive metric and one generative quality check are named.')
            ->assertJsonPath('data.pass_criteria.0', 'All coding tasks include output-contract evidence.')
            ->assertJsonPath('data.pass_criteria.2', 'You can explain metric fit, groundedness checks, and failure modes without reading the page.');

        $this->assertStringContainsString(
            'Predictive AI',
            $response->json('data.coding_tasks.0.prompt')
        );
    }

    /**
     * JavaScript closure checkpoint exams require scope tracing and stale-closure evidence.
     */
    public function test_javascript_closure_checkpoint_exam_uses_scope_evidence(): void
    {
        $response = $this->getJson('/api/practice/checkpoint-exam-lab?technology=javascript-closures&family=laravel&language=en&search=JavaScript%20closure&limit=1');

        $response
            ->assertOk()
            ->assertJsonPath('data.technology', 'javascript-closures')
            ->assertJsonPath('data.coding_tasks.0.workspace_query.technology', 'javascript-closures')
            ->assertJsonPath('data.coding_tasks.0.source_path', 'laravel/frontend.en.json')
            ->assertJsonPath('data.coding_tasks.0.expected_evidence.0', 'A lexical-scope trace names the outer scope, returned inner function, and captured binding.')
            ->assertJsonPath('data.coding_tasks.0.expected_evidence.2', 'Interview-trap evidence covers var versus let and one stale closure in async code, timers, callbacks, or hooks.')
            ->assertJsonPath('data.pass_criteria.0', 'All coding tasks include lexical-scope and captured-binding evidence.')
            ->assertJsonPath('data.pass_criteria.2', 'You can explain var versus let and stale closures without reading the page.');

        $this->assertStringContainsString(
            'JavaScript closure',
            $response->json('data.coding_tasks.0.prompt')
        );
    }
}
