<?php

declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;

final class PracticeCompetencyMapLabTest extends TestCase
{
    /**
     * The competency map page renders map rules and competency cards.
     */
    public function test_competency_map_lab_page_renders_map_sections(): void
    {
        $response = $this->get('/practice/competency-map-lab?family=laravel&language=en&search=api&phase_limit=2&tasks_per_phase=2&days=3');

        $response
            ->assertOk()
            ->assertSee('Competency map lab turns mastery evidence into a skill map.')
            ->assertSee('Competency Rules')
            ->assertSee('Competencies')
            ->assertSee('Competency Map Progress Payload');
    }

    /**
     * The competency map API returns readiness levels generated from mastery evidence.
     */
    public function test_competency_map_lab_api_returns_competency_payload(): void
    {
        $response = $this->getJson('/api/practice/competency-map-lab?family=laravel&language=en&search=api&phase_limit=2&tasks_per_phase=2&days=3');

        $response
            ->assertOk()
            ->assertJsonPath('data.source_mastery_lab.evidence_card_count', 3)
            ->assertJsonPath('data.map_summary.ready_count', 3)
            ->assertJsonPath('data.competencies.0.competency_levels.0.skill', 'implementation')
            ->assertJsonPath('data.competencies.0.competency_levels.0.level', 'ready')
            ->assertJsonStructure([
                'data' => [
                    'title',
                    'source_mastery_lab',
                    'competency_rules',
                    'competencies' => [
                        '*' => [
                            'technology_segment',
                            'readiness',
                            'evidence_score',
                            'competency_levels',
                            'proof_summary',
                            'next_action',
                        ],
                    ],
                    'map_summary',
                    'progress_payload' => [
                        'items',
                    ],
                ],
            ]);
    }
}
