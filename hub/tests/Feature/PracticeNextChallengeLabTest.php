<?php

declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;

final class PracticeNextChallengeLabTest extends TestCase
{
    /**
     * The next challenge page renders challenge rules and cards.
     */
    public function test_next_challenge_lab_page_renders_challenge_sections(): void
    {
        $response = $this->get('/practice/next-challenge-lab?family=laravel&language=en&search=api&phase_limit=2&tasks_per_phase=2&days=3');

        $response
            ->assertOk()
            ->assertSee('Next challenge lab turns the competency map into the next practice challenge.')
            ->assertSee('Challenge Rules')
            ->assertSee('Challenge Cards')
            ->assertSee('Next Challenge Progress Payload');
    }

    /**
     * The next challenge API returns challenge cards from competency readiness.
     */
    public function test_next_challenge_lab_api_returns_challenge_payload(): void
    {
        $response = $this->getJson('/api/practice/next-challenge-lab?family=laravel&language=en&search=api&phase_limit=2&tasks_per_phase=2&days=3');

        $response
            ->assertOk()
            ->assertJsonPath('data.source_competency_lab.competency_count', 3)
            ->assertJsonPath('data.challenge_summary.harder_lab_count', 3)
            ->assertJsonPath('data.challenge_cards.0.challenge_type', 'harder-lab')
            ->assertJsonPath('data.challenge_cards.0.recommended_route', 'practice.checkpoint-exam-lab')
            ->assertJsonStructure([
                'data' => [
                    'title',
                    'source_competency_lab',
                    'challenge_rules',
                    'challenge_cards' => [
                        '*' => [
                            'technology_segment',
                            'readiness',
                            'challenge_type',
                            'recommended_route',
                            'why_this_challenge',
                            'verification_command',
                            'evidence_to_submit',
                        ],
                    ],
                    'challenge_summary',
                    'progress_payload' => [
                        'items',
                    ],
                ],
            ]);
    }
}
