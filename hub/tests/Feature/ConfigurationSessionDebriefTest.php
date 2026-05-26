<?php

declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;

final class ConfigurationSessionDebriefTest extends TestCase
{
    /**
     * The configuration session debrief page renders debrief sections.
     */
    public function test_configuration_session_debrief_page_renders_sections(): void
    {
        $response = $this->get('/practice/configuration-session-debrief');

        $response
            ->assertOk()
            ->assertSee('Configuration Session Debrief')
            ->assertSee('completed: configuration-contract-promote-100')
            ->assertSee('Evidence Review')
            ->assertSee('Blockers')
            ->assertSee('Open debrief API');
    }

    /**
     * The configuration session debrief API returns evidence review data.
     */
    public function test_configuration_session_debrief_api_returns_payload(): void
    {
        $response = $this->getJson('/api/practice/configuration-session-debrief');

        $response
            ->assertOk()
            ->assertJsonPath('data.archive_id', 'configuration-contract-promote-100')
            ->assertJsonPath('data.session_result', 'completed')
            ->assertJsonPath('data.evidence_review.0.status', 'captured')
            ->assertJsonPath('data.quality_gate.status', 'ready')
            ->assertJsonStructure([
                'data' => [
                    'title',
                    'summary',
                    'archive_id',
                    'session_result',
                    'completed_outputs',
                    'evidence_review',
                    'blockers',
                    'next_actions',
                    'commands',
                    'quality_gate',
                ],
            ]);
    }
}
