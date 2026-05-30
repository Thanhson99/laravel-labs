<?php

declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;

final class PracticeMasteryPathLabTest extends TestCase
{
    /**
     * The mastery path page renders milestones and progress payload.
     */
    public function test_mastery_path_lab_page_renders_milestones(): void
    {
        $response = $this->get('/practice/mastery-path-lab?family=laravel&language=en&search=api&phase_limit=2&tasks_per_phase=2');

        $response
            ->assertOk()
            ->assertSee('Mastery path lab turns the syllabus into a multi-technology practice path.')
            ->assertSee('Milestones')
            ->assertSee('Open capstone')
            ->assertSee('Mastery Path Progress Payload');
    }

    /**
     * The mastery path API returns milestones with linked lab queries.
     */
    public function test_mastery_path_lab_api_returns_milestones(): void
    {
        $response = $this->getJson('/api/practice/mastery-path-lab?family=laravel&language=en&search=api&phase_limit=2&tasks_per_phase=2');

        $response
            ->assertOk()
            ->assertJsonPath('data.milestones.0.technology', 'api-validation')
            ->assertJsonPath('data.milestones.0.capstone_query.limit', 2)
            ->assertJsonPath('data.meta.milestone_count', 2)
            ->assertJsonStructure([
                'data' => [
                    'title',
                    'milestones' => [
                        '*' => [
                            'phase',
                            'technology',
                            'title',
                            'record_count',
                            'capstone_query',
                            'checkpoint_query',
                            'mentor_feedback_query',
                            'done_when',
                        ],
                    ],
                    'source_packs',
                    'meta',
                    'progress_payload' => [
                        'items',
                    ],
                ],
            ]);
    }

    /**
     * Predictive AI mastery paths use AI type comparison milestone criteria.
     */
    public function test_predictive_generative_ai_mastery_path_uses_ai_type_milestone(): void
    {
        $this->getJson('/api/practice/mastery-path-lab?family=vibe-coding&language=en&search=Predictive%20AI&phase_limit=1&tasks_per_phase=1')
            ->assertOk()
            ->assertJsonPath('data.milestones.0.technology', 'llm-foundations')
            ->assertJsonPath('data.milestones.0.title', 'Master AI type comparison through source-backed explanation work')
            ->assertJsonPath('data.milestones.0.capstone_query.technology', 'llm-foundations')
            ->assertJsonPath('data.milestones.0.done_when.0', 'Complete the AI type comparison capstone tasks.')
            ->assertJsonPath('data.milestones.0.done_when.1', 'Pass the checkpoint exam with output-contract, metric, and failure-mode evidence.');
    }
}
