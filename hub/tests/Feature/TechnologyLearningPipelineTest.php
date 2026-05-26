<?php

declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;

final class TechnologyLearningPipelineTest extends TestCase
{
    /**
     * The technology learning pipeline page renders ordered stage links.
     */
    public function test_technology_learning_pipeline_page_renders_full_stage_flow(): void
    {
        $response = $this->get('/practice/technology-learning-pipeline/api-validation?family=laravel&language=en&search=api&limit=3');

        $response
            ->assertOk()
            ->assertSee('Technology Learning Pipeline: api-validation')
            ->assertSee('Code examples')
            ->assertSee('Implementation lab')
            ->assertSee('Evidence archive')
            ->assertSee('Sample Records')
            ->assertSee('Open pipeline API');
    }

    /**
     * The technology learning pipeline API returns stage routes and progress payload.
     */
    public function test_technology_learning_pipeline_api_returns_stages_and_progress_payload(): void
    {
        $response = $this->getJson('/api/practice/technology-learning-pipeline/api-validation?family=laravel&language=en&search=api&limit=3');

        $response
            ->assertOk()
            ->assertJsonPath('data.technology', 'api-validation')
            ->assertJsonPath('data.stages.0.label', 'Code examples')
            ->assertJsonPath('data.stages.10.label', 'Evidence archive')
            ->assertJsonPath('data.progress_payload.items.0.label', 'Code examples')
            ->assertJsonStructure([
                'data' => [
                    'title',
                    'technology',
                    'record_count',
                    'sample_records',
                    'stages' => [
                        '*' => [
                            'step',
                            'label',
                            'purpose',
                            'route',
                            'api_route',
                        ],
                    ],
                    'progress_payload',
                    'meta',
                ],
            ]);
    }
}
