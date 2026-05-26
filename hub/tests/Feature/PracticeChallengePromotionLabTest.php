<?php

declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;

final class PracticeChallengePromotionLabTest extends TestCase
{
    /**
     * The challenge promotion page renders decision sections.
     */
    public function test_challenge_promotion_lab_page_renders_decision_sections(): void
    {
        $response = $this->get('/practice/challenge-promotion-lab?family=laravel&language=en&search=api&phase_limit=2&tasks_per_phase=2&days=3');

        $response
            ->assertOk()
            ->assertSee('Challenge promotion turns reviewed evidence into the next learning decision.')
            ->assertSee('Promotion Rules')
            ->assertSee('Promotion Decisions')
            ->assertSee('Challenge Promotion Progress Payload');
    }

    /**
     * The challenge promotion API returns promotion decisions from evidence review cards.
     */
    public function test_challenge_promotion_lab_api_returns_decision_payload(): void
    {
        $response = $this->getJson('/api/practice/challenge-promotion-lab?family=laravel&language=en&search=api&phase_limit=2&tasks_per_phase=2&days=3');

        $response
            ->assertOk()
            ->assertJsonPath('data.source_review_lab.review_card_count', 3)
            ->assertJsonPath('data.promotion_summary.decision_count', 3)
            ->assertJsonPath('data.promotion_summary.promoted_count', 3)
            ->assertJsonPath('data.promotion_summary.repeat_count', 0)
            ->assertJsonPath('data.promotion_decisions.0.next_route_name', 'practice.portfolio-lab')
            ->assertJsonStructure([
                'data' => [
                    'title',
                    'source_review_lab',
                    'promotion_rules',
                    'promotion_decisions' => [
                        '*' => [
                            'step',
                            'technology_segment',
                            'source_route_name',
                            'decision',
                            'next_route_name',
                            'verification_command',
                            'promotion_proof',
                            'repeat_triggers',
                            'learner_note_prompt',
                        ],
                    ],
                    'promotion_summary',
                    'progress_payload' => [
                        'items',
                    ],
                ],
            ]);
    }
}
