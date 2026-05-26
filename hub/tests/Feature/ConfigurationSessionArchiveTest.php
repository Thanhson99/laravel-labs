<?php

declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;

final class ConfigurationSessionArchiveTest extends TestCase
{
    /**
     * The configuration session archive page renders archive sections.
     */
    public function test_configuration_session_archive_page_renders_sections(): void
    {
        $response = $this->get('/practice/configuration-session-archive');

        $response
            ->assertOk()
            ->assertSee('Configuration Session Archive')
            ->assertSee('configuration-contract-promote-100-session-debrief')
            ->assertSee('Evidence Tags')
            ->assertSee('Reuse Paths')
            ->assertSee('Open archive API');
    }

    /**
     * The configuration session archive API returns archive entries and retrieval prompts.
     */
    public function test_configuration_session_archive_api_returns_payload(): void
    {
        $response = $this->getJson('/api/practice/configuration-session-archive');

        $response
            ->assertOk()
            ->assertJsonPath('data.archive_id', 'configuration-contract-promote-100-session-debrief')
            ->assertJsonPath('data.source_archive_id', 'configuration-contract-promote-100')
            ->assertJsonPath('data.archive_entries.0.status', 'captured')
            ->assertJsonPath('data.quality_gate.status', 'ready')
            ->assertJsonStructure([
                'data' => [
                    'title',
                    'summary',
                    'archive_id',
                    'source_archive_id',
                    'session_result',
                    'evidence_tags',
                    'archive_entries',
                    'retrieval_prompts',
                    'reuse_paths',
                    'commands',
                    'quality_gate',
                ],
            ]);
    }
}
