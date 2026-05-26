<?php

declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;

final class PracticeSpacedRepetitionLabTest extends TestCase
{
    /**
     * The spaced repetition page renders review schedule and promotion criteria.
     */
    public function test_spaced_repetition_lab_page_renders_review_sections(): void
    {
        $response = $this->get('/practice/spaced-repetition-lab?family=laravel&language=en&search=api&phase_limit=2&tasks_per_phase=2&days=3');

        $response
            ->assertOk()
            ->assertSee('Spaced repetition lab turns knowledge gaps into a recurring coding schedule.')
            ->assertSee('Review Rules')
            ->assertSee('Review Schedule')
            ->assertSee('Promotion Criteria')
            ->assertSee('Spaced Repetition Progress Payload');
    }

    /**
     * The spaced repetition API returns repeated reviews generated from gap cards.
     */
    public function test_spaced_repetition_lab_api_returns_review_payload(): void
    {
        $response = $this->getJson('/api/practice/spaced-repetition-lab?family=laravel&language=en&search=api&phase_limit=2&tasks_per_phase=2&days=3');

        $response
            ->assertOk()
            ->assertJsonPath('data.source_gap_lab.gap_count', 3)
            ->assertJsonPath('data.source_gap_lab.technology_coverage.0', 'api-validation')
            ->assertJsonPath('data.review_schedule.0.day', 1)
            ->assertJsonPath('data.review_schedule.1.day', 3)
            ->assertJsonPath('data.review_schedule.2.day', 7)
            ->assertJsonStructure([
                'data' => [
                    'title',
                    'source_gap_lab',
                    'review_rules',
                    'review_schedule' => [
                        '*' => [
                            'day',
                            'round',
                            'technology_segment',
                            'review_goal',
                            'coding_action',
                            'recall_prompt',
                            'verification_hint',
                        ],
                    ],
                    'promotion_criteria',
                    'progress_payload' => [
                        'items',
                    ],
                ],
            ]);
    }
}
