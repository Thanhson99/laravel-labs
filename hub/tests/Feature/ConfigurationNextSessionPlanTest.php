<?php

declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;

final class ConfigurationNextSessionPlanTest extends TestCase
{
    /**
     * The configuration next-session plan page renders timed session sections.
     */
    public function test_configuration_next_session_plan_page_renders_sections(): void
    {
        $response = $this->get('/practice/configuration-next-session-plan');

        $response
            ->assertOk()
            ->assertSee('Configuration Next Session Plan')
            ->assertSee('Session Goal')
            ->assertSee('Practice Blocks')
            ->assertSee('Stop Criteria')
            ->assertSee('Open session API');
    }

    /**
     * The configuration next-session plan API returns session blocks and deliverables.
     */
    public function test_configuration_next_session_plan_api_returns_payload(): void
    {
        $response = $this->getJson('/api/practice/configuration-next-session-plan');

        $response
            ->assertOk()
            ->assertJsonPath('data.archive_id', 'configuration-contract-promote-100')
            ->assertJsonPath('data.session_status', 'ready')
            ->assertJsonPath('data.preflight.2', 'Choose one channel: portfolio, security review, interview, or code review.')
            ->assertJsonPath('data.practice_blocks.0.focus', 'Recall')
            ->assertJsonPath('data.practice_blocks.1.task', 'Use one required evidence item to write or speak the selected channel output, including a Security Misconfiguration blocker when security review is selected.')
            ->assertJsonPath('data.deliverables.1', 'One Security Misconfiguration release-blocker proof when the selected channel is security review.')
            ->assertJsonPath('data.stop_criteria.2', 'The security review output names a risk but no fail-closed smoke check.')
            ->assertJsonPath('data.quality_gate.status', 'ready')
            ->assertJsonStructure([
                'data' => [
                    'title',
                    'summary',
                    'archive_id',
                    'session_status',
                    'session_goal',
                    'preflight',
                    'practice_blocks',
                    'deliverables',
                    'stop_criteria',
                    'commands',
                    'quality_gate',
                ],
            ]);
    }
}
