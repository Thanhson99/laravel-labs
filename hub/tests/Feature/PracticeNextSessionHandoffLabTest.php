<?php

declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;

final class PracticeNextSessionHandoffLabTest extends TestCase
{
    /**
     * The next session handoff page renders handoff sections.
     */
    public function test_next_session_handoff_lab_page_renders_handoff_sections(): void
    {
        $response = $this->get('/practice/next-session-handoff-lab?family=laravel&language=en&search=api&phase_limit=2&tasks_per_phase=2&days=3');

        $response
            ->assertOk()
            ->assertSee('Next session handoff turns promotion decisions into the next coding session.')
            ->assertSee('Handoff Rules')
            ->assertSee('Next Session Handoff Cards')
            ->assertSee('Next Session Handoff Progress Payload');
    }

    /**
     * The next session handoff API returns handoff cards from promotion decisions.
     */
    public function test_next_session_handoff_lab_api_returns_handoff_payload(): void
    {
        $response = $this->getJson('/api/practice/next-session-handoff-lab?family=laravel&language=en&search=api&phase_limit=2&tasks_per_phase=2&days=3');

        $response
            ->assertOk()
            ->assertJsonPath('data.source_promotion_lab.decision_count', 3)
            ->assertJsonPath('data.handoff_summary.handoff_count', 3)
            ->assertJsonPath('data.handoff_summary.promotion_handoffs', 3)
            ->assertJsonPath('data.handoff_summary.repeat_handoffs', 0)
            ->assertJsonPath('data.handoff_cards.0.open_route_name', 'practice.portfolio-lab')
            ->assertJsonStructure([
                'data' => [
                    'title',
                    'source_promotion_lab',
                    'handoff_rules',
                    'handoff_cards' => [
                        '*' => [
                            'step',
                            'technology_segment',
                            'handoff_type',
                            'open_route_name',
                            'carry_over_route_name',
                            'verification_command',
                            'session_goal',
                            'preflight_checklist',
                            'coding_focus',
                            'done_evidence',
                            'handoff_note_prompt',
                        ],
                    ],
                    'handoff_summary',
                    'progress_payload' => [
                        'items',
                    ],
                ],
            ]);
    }
}
