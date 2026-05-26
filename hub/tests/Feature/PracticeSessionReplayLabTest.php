<?php

declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;

final class PracticeSessionReplayLabTest extends TestCase
{
    /**
     * The session replay page renders replay sections.
     */
    public function test_session_replay_lab_page_renders_replay_sections(): void
    {
        $response = $this->get('/practice/session-replay-lab?family=laravel&language=en&search=api&phase_limit=2&tasks_per_phase=2&days=3');

        $response
            ->assertOk()
            ->assertSee('Session replay turns handoff cards into verified before-and-after coding rounds.')
            ->assertSee('Replay Rules')
            ->assertSee('Replay Rounds')
            ->assertSee('Session Replay Progress Payload');
    }

    /**
     * The session replay API returns replay rounds from handoff cards.
     */
    public function test_session_replay_lab_api_returns_replay_payload(): void
    {
        $response = $this->getJson('/api/practice/session-replay-lab?family=laravel&language=en&search=api&phase_limit=2&tasks_per_phase=2&days=3');

        $response
            ->assertOk()
            ->assertJsonPath('data.source_handoff_lab.handoff_count', 3)
            ->assertJsonPath('data.replay_summary.round_count', 3)
            ->assertJsonPath('data.replay_summary.promotion_rounds', 3)
            ->assertJsonPath('data.replay_summary.repeat_rounds', 0)
            ->assertJsonPath('data.replay_rounds.0.route_name', 'practice.portfolio-lab')
            ->assertJsonStructure([
                'data' => [
                    'title',
                    'source_handoff_lab',
                    'replay_rules',
                    'replay_rounds' => [
                        '*' => [
                            'round',
                            'technology_segment',
                            'route_name',
                            'handoff_type',
                            'before_check',
                            'coding_run',
                            'after_check',
                            'evidence_capture',
                            'retry_policy',
                        ],
                    ],
                    'replay_summary',
                    'progress_payload' => [
                        'items',
                    ],
                ],
            ]);
    }
}
