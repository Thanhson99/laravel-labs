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

    /**
     * Predictive AI capstones keep AI type comparison tasks in the LLM foundations lane.
     */
    public function test_predictive_generative_ai_capstone_routes_to_ai_type_workspace(): void
    {
        $response = $this->getJson('/api/practice/capstone-lab?technology=llm-foundations&family=vibe-coding&language=en&search=Predictive%20AI&limit=1');

        $response
            ->assertOk()
            ->assertJsonPath('data.technology', 'llm-foundations')
            ->assertJsonPath('data.meta.task_count', 1)
            ->assertJsonPath('data.tasks.0.workspace_query.technology', 'llm-foundations')
            ->assertJsonPath('data.tasks.0.source.path', 'vibe-coding/prompting.en.json')
            ->assertJsonPath('data.source_coverage.0.sample_workspace_query.technology', 'llm-foundations')
            ->assertJsonPath('data.deliverables.1', 'Produce one Predictive AI versus Generative AI comparison table.')
            ->assertJsonPath('data.deliverables.2', 'Attach predictive metrics, generative quality checks, and failure-mode evidence.');

        $this->assertStringContainsString(
            'Predictive AI',
            $response->json('data.tasks.0.question')
        );
        $this->assertStringContainsString(
            'AI type comparison table',
            $response->json('data.tasks.0.deliverable')
        );
    }

    /**
     * JavaScript closure capstones require lexical-scope and interview-trap evidence.
     */
    public function test_javascript_closure_capstone_uses_scope_deliverables(): void
    {
        $response = $this->getJson('/api/practice/capstone-lab?technology=javascript-closures&family=laravel&language=en&search=JavaScript%20closure&limit=1');

        $response
            ->assertOk()
            ->assertJsonPath('data.technology', 'javascript-closures')
            ->assertJsonPath('data.tasks.0.workspace_query.technology', 'javascript-closures')
            ->assertJsonPath('data.tasks.0.source.path', 'laravel/frontend.en.json')
            ->assertJsonPath('data.deliverables.1', 'Produce one lexical-scope trace that names the outer scope, inner function, and captured binding.')
            ->assertJsonPath('data.deliverables.2', 'Attach createCounter(), private state, var versus let, and stale-closure evidence.');

        $this->assertStringContainsString(
            'lexical scope',
            $response->json('data.tasks.0.deliverable')
        );
    }
}
