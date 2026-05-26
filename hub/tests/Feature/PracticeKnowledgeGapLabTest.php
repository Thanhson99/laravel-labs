<?php

declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;

final class PracticeKnowledgeGapLabTest extends TestCase
{
    /**
     * The knowledge-gap page renders gap cards and next-session sections.
     */
    public function test_knowledge_gap_lab_page_renders_gap_sections(): void
    {
        $response = $this->get('/practice/knowledge-gap-lab?family=laravel&language=en&search=api&phase_limit=2&tasks_per_phase=2&days=3');

        $response
            ->assertOk()
            ->assertSee('Knowledge gap lab turns weak answers into the next coding task.')
            ->assertSee('Gap Rules')
            ->assertSee('Gap Cards')
            ->assertSee('Next Session Plan')
            ->assertSee('Knowledge Gap Progress Payload');
    }

    /**
     * The knowledge-gap API returns cards generated from interview defenses.
     */
    public function test_knowledge_gap_lab_api_returns_gap_payload(): void
    {
        $response = $this->getJson('/api/practice/knowledge-gap-lab?family=laravel&language=en&search=api&phase_limit=2&tasks_per_phase=2&days=3');

        $response
            ->assertOk()
            ->assertJsonPath('data.source_defense_lab.defense_card_count', 3)
            ->assertJsonPath('data.source_defense_lab.technology_coverage.0', 'api-validation')
            ->assertJsonPath('data.gap_cards.0.technology_segment', 'Day 1 demo: api-validation')
            ->assertJsonPath('data.gap_rules.0', 'A weak answer becomes a coding task, not only a note.')
            ->assertJsonStructure([
                'data' => [
                    'title',
                    'source_defense_lab',
                    'gap_rules',
                    'gap_cards' => [
                        '*' => [
                            'round',
                            'technology_segment',
                            'gap',
                            'practice_action',
                            'review_prompt',
                            'evidence_to_recheck',
                            'verification_hint',
                        ],
                    ],
                    'next_session_plan',
                    'progress_payload' => [
                        'items',
                    ],
                ],
            ]);
    }
}
