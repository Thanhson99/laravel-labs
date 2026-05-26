<?php

declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;

final class PracticeSessionDebriefLabTest extends TestCase
{
    /**
     * The session debrief page renders debrief sections.
     */
    public function test_session_debrief_lab_page_renders_debrief_sections(): void
    {
        $response = $this->get('/practice/session-debrief-lab?family=laravel&language=en&search=api&phase_limit=2&tasks_per_phase=2&days=3');

        $response
            ->assertOk()
            ->assertSee('Session debrief turns replay rounds into learning notes and next actions.')
            ->assertSee('Debrief Rules')
            ->assertSee('Debrief Cards')
            ->assertSee('Session Debrief Progress Payload');
    }

    /**
     * The session debrief API returns debrief cards from replay rounds.
     */
    public function test_session_debrief_lab_api_returns_debrief_payload(): void
    {
        $response = $this->getJson('/api/practice/session-debrief-lab?family=laravel&language=en&search=api&phase_limit=2&tasks_per_phase=2&days=3');

        $response
            ->assertOk()
            ->assertJsonPath('data.source_replay_lab.round_count', 3)
            ->assertJsonPath('data.debrief_summary.card_count', 3)
            ->assertJsonPath('data.debrief_summary.passed_review_count', 3)
            ->assertJsonPath('data.debrief_summary.needs_retry_count', 0)
            ->assertJsonPath('data.debrief_cards.0.debrief_status', 'ready-for-archive')
            ->assertJsonStructure([
                'data' => [
                    'title',
                    'source_replay_lab',
                    'debrief_rules',
                    'debrief_cards' => [
                        '*' => [
                            'round',
                            'technology_segment',
                            'route_name',
                            'command',
                            'debrief_status',
                            'result_notes',
                            'lesson_prompts',
                            'blocker_checks',
                            'next_action',
                        ],
                    ],
                    'debrief_summary',
                    'progress_payload' => [
                        'items',
                    ],
                ],
            ]);
    }
}
