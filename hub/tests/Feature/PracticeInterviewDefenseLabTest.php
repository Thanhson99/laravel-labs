<?php

declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;

final class PracticeInterviewDefenseLabTest extends TestCase
{
    /**
     * The interview defense page renders defense cards and scoring sections.
     */
    public function test_interview_defense_lab_page_renders_defense_sections(): void
    {
        $response = $this->get('/practice/interview-defense-lab?family=laravel&language=en&search=api&phase_limit=2&tasks_per_phase=2&days=3');

        $response
            ->assertOk()
            ->assertSee('Interview defense lab turns release evidence into technical answers.')
            ->assertSee('Defense Rules')
            ->assertSee('Defense Cards')
            ->assertSee('Scoring Rubric')
            ->assertSee('Defense Progress Payload');
    }

    /**
     * The interview defense API returns cards generated from release evidence.
     */
    public function test_interview_defense_lab_api_returns_defense_payload(): void
    {
        $response = $this->getJson('/api/practice/interview-defense-lab?family=laravel&language=en&search=api&phase_limit=2&tasks_per_phase=2&days=3');

        $response
            ->assertOk()
            ->assertJsonPath('data.source_release_lab.release_item_count', 3)
            ->assertJsonPath('data.source_release_lab.technology_coverage.0', 'api-validation')
            ->assertJsonPath('data.defense_cards.0.answer_outline.0', 'Start with the behavior: Improved `Day 1 demo: api-validation` practice flow: Improve the `tests/Feature/*Test.php` implementation without changing the verified behavior for `Day 1 demo: api-validation`.')
            ->assertJsonPath('data.scoring_rubric.0.points', 20)
            ->assertJsonStructure([
                'data' => [
                    'title',
                    'source_release_lab',
                    'defense_rules',
                    'defense_cards' => [
                        '*' => [
                            'round',
                            'technology_segment',
                            'question',
                            'answer_outline',
                            'evidence_to_cite',
                            'follow_up_risk',
                        ],
                    ],
                    'scoring_rubric',
                    'progress_payload' => [
                        'items',
                    ],
                ],
            ]);
    }
}
