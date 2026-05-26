<?php

declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;

final class PracticeSessionArchiveLabTest extends TestCase
{
    /**
     * The session archive page renders archive sections.
     */
    public function test_session_archive_lab_page_renders_archive_sections(): void
    {
        $response = $this->get('/practice/session-archive-lab?family=laravel&language=en&search=api&phase_limit=2&tasks_per_phase=2&days=3');

        $response
            ->assertOk()
            ->assertSee('Session archive turns debrief cards into reusable evidence records.')
            ->assertSee('Archive Rules')
            ->assertSee('Archive Entries')
            ->assertSee('Session Archive Progress Payload');
    }

    /**
     * The session archive API returns archive entries from debrief cards.
     */
    public function test_session_archive_lab_api_returns_archive_payload(): void
    {
        $response = $this->getJson('/api/practice/session-archive-lab?family=laravel&language=en&search=api&phase_limit=2&tasks_per_phase=2&days=3');

        $response
            ->assertOk()
            ->assertJsonPath('data.source_debrief_lab.card_count', 3)
            ->assertJsonPath('data.archive_summary.entry_count', 3)
            ->assertJsonPath('data.archive_summary.archived_count', 3)
            ->assertJsonPath('data.archive_summary.retry_archive_count', 0)
            ->assertJsonPath('data.archive_entries.0.archive_status', 'archived')
            ->assertJsonStructure([
                'data' => [
                    'title',
                    'source_debrief_lab',
                    'archive_rules',
                    'archive_entries' => [
                        '*' => [
                            'entry',
                            'technology_segment',
                            'route_name',
                            'command',
                            'archive_status',
                            'proof_bundle',
                            'learning_summary',
                            'blocker_status',
                            'retrieval_tags',
                            'next_reference',
                        ],
                    ],
                    'archive_summary',
                    'progress_payload' => [
                        'items',
                    ],
                ],
            ]);
    }
}
