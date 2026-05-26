<?php

declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;

final class PracticeChallengeEvidenceReviewLabTest extends TestCase
{
    /**
     * The challenge evidence review page renders review sections.
     */
    public function test_challenge_evidence_review_lab_page_renders_review_sections(): void
    {
        $response = $this->get('/practice/challenge-evidence-review-lab?family=laravel&language=en&search=api&phase_limit=2&tasks_per_phase=2&days=3');

        $response
            ->assertOk()
            ->assertSee('Challenge evidence review turns executed work into a results checklist.')
            ->assertSee('Review Rules')
            ->assertSee('Evidence Review Cards')
            ->assertSee('Challenge Evidence Review Progress Payload');
    }

    /**
     * The challenge evidence review API returns review cards from execution steps.
     */
    public function test_challenge_evidence_review_lab_api_returns_review_payload(): void
    {
        $response = $this->getJson('/api/practice/challenge-evidence-review-lab?family=laravel&language=en&search=api&phase_limit=2&tasks_per_phase=2&days=3');

        $response
            ->assertOk()
            ->assertJsonPath('data.source_execution_lab.challenge_count', 3)
            ->assertJsonPath('data.review_summary.review_card_count', 3)
            ->assertJsonPath('data.review_summary.required_evidence_count', 12)
            ->assertJsonPath('data.evidence_cards.0.route_name', 'practice.checkpoint-exam-lab')
            ->assertJsonStructure([
                'data' => [
                    'title',
                    'source_execution_lab',
                    'review_rules',
                    'evidence_cards' => [
                        '*' => [
                            'step',
                            'technology_segment',
                            'route_name',
                            'challenge_type',
                            'verification_command',
                            'required_evidence',
                            'review_questions',
                            'pass_signals',
                            'risk_checks',
                            'follow_up_action',
                        ],
                    ],
                    'review_summary',
                    'progress_payload' => [
                        'items',
                    ],
                ],
            ]);
    }
}
