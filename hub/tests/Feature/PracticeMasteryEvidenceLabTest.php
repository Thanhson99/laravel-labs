<?php

declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;

final class PracticeMasteryEvidenceLabTest extends TestCase
{
    /**
     * The mastery evidence page renders evidence and next-harder-lab sections.
     */
    public function test_mastery_evidence_lab_page_renders_evidence_sections(): void
    {
        $response = $this->get('/practice/mastery-evidence-lab?family=laravel&language=en&search=api&phase_limit=2&tasks_per_phase=2&days=3');

        $response
            ->assertOk()
            ->assertSee('Mastery evidence lab converts repetition schedules into proof of skill.')
            ->assertSee('Mastery Rules')
            ->assertSee('Evidence Cards')
            ->assertSee('Next Harder Labs')
            ->assertSee('Mastery Evidence Progress Payload');
    }

    /**
     * The mastery evidence API returns evidence cards generated from repeated practice.
     */
    public function test_mastery_evidence_lab_api_returns_evidence_payload(): void
    {
        $response = $this->getJson('/api/practice/mastery-evidence-lab?family=laravel&language=en&search=api&phase_limit=2&tasks_per_phase=2&days=3');

        $response
            ->assertOk()
            ->assertJsonPath('data.source_repetition_lab.review_count', 9)
            ->assertJsonPath('data.source_repetition_lab.technology_coverage.0', 'api-validation')
            ->assertJsonPath('data.evidence_cards.0.evidence_score', 85)
            ->assertJsonPath('data.evidence_cards.0.status', 'ready-for-harder-lab')
            ->assertJsonStructure([
                'data' => [
                    'title',
                    'source_repetition_lab',
                    'mastery_rules',
                    'evidence_cards' => [
                        '*' => [
                            'technology_segment',
                            'review_days',
                            'evidence_score',
                            'status',
                            'proof_items',
                            'missing_evidence',
                        ],
                    ],
                    'next_harder_labs',
                    'progress_payload' => [
                        'items',
                    ],
                ],
            ]);
    }
}
