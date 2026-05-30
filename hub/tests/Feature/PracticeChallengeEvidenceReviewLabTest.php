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

    /**
     * JavaScript closure evidence reviews ask for lexical-scope and stale-closure proof.
     */
    public function test_javascript_closure_challenge_evidence_review_uses_scope_questions(): void
    {
        $response = $this->getJson('/api/practice/challenge-evidence-review-lab?family=laravel&language=en&search=JavaScript%20closure&phase_limit=1&tasks_per_phase=1&days=3');

        $response
            ->assertOk()
            ->assertJsonPath('data.evidence_cards.0.technology_segment', 'Day 1 demo: javascript-closures')
            ->assertJsonPath('data.evidence_cards.0.review_questions.1', 'Which lexical scope and captured binding carried the main behavior?')
            ->assertJsonPath('data.evidence_cards.0.pass_signals.1', 'Evidence names at least one changed closure snippet, payload, route, or test.')
            ->assertJsonPath('data.evidence_cards.0.risk_checks.2', 'The rollback note says how to undo the smallest risky closure-evidence change.');

        $this->assertStringContainsString('closure checkpoint route', $response->json('data.evidence_cards.0.follow_up_action'));
    }

    /**
     * IDOR evidence reviews ask for scoped lookup, policy, and denial-test proof.
     */
    public function test_idor_challenge_evidence_review_uses_object_authorization_questions(): void
    {
        $response = $this->getJson('/api/practice/challenge-evidence-review-lab?family=laravel&language=en&search=IDOR&phase_limit=1&tasks_per_phase=1&days=3');

        $response
            ->assertOk()
            ->assertJsonPath('data.evidence_cards.0.technology_segment', 'Day 1 demo: idor-access-control')
            ->assertJsonPath('data.evidence_cards.0.review_questions.1', 'Which object id, owner or tenant scope, and protected action were reviewed?')
            ->assertJsonPath('data.evidence_cards.0.pass_signals.1', 'Evidence names the protected object route, scoped lookup, object policy or Gate, and denial test.')
            ->assertJsonPath('data.evidence_cards.0.risk_checks.1', 'No direct findOrFail() or route model binding path returns an object before scoping and authorization.');

        $this->assertStringContainsString('IDOR checkpoint route', $response->json('data.evidence_cards.0.follow_up_action'));
    }
}
