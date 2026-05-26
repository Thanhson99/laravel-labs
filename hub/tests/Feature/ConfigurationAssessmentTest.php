<?php

declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;

final class ConfigurationAssessmentTest extends TestCase
{
    /**
     * The configuration assessment page renders the score and rubric.
     */
    public function test_configuration_assessment_page_renders_score_and_rubric(): void
    {
        $response = $this->get('/practice/configuration-assessment');

        $response
            ->assertOk()
            ->assertSee('Configuration Assessment')
            ->assertSee('ready with 100 points')
            ->assertSee('Scope control')
            ->assertSee('Improvement Tasks')
            ->assertSee('Open assessment API');
    }

    /**
     * The configuration assessment API returns scored readiness data.
     */
    public function test_configuration_assessment_api_returns_scored_payload(): void
    {
        $response = $this->getJson('/api/practice/configuration-assessment');

        $response
            ->assertOk()
            ->assertJsonPath('data.score', 100)
            ->assertJsonPath('data.result', 'ready')
            ->assertJsonPath('data.rubric.0.criterion', 'Scope control')
            ->assertJsonPath('data.status.quality', 'ready')
            ->assertJsonStructure([
                'data' => [
                    'title',
                    'summary',
                    'score',
                    'result',
                    'result_label',
                    'rubric',
                    'readiness_signals',
                    'improvement_tasks',
                    'evidence',
                    'commands',
                    'status',
                ],
            ]);
    }
}
