<?php

declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;

final class PracticeArchiveRetrievalLabTest extends TestCase
{
    /**
     * The archive retrieval page renders retrieval sections.
     */
    public function test_archive_retrieval_lab_page_renders_retrieval_sections(): void
    {
        $response = $this->get('/practice/archive-retrieval-lab?family=laravel&language=en&search=api&phase_limit=2&tasks_per_phase=2&days=3');

        $response
            ->assertOk()
            ->assertSee('Archive retrieval turns saved session evidence into searchable reuse cards.')
            ->assertSee('Retrieval Rules')
            ->assertSee('Retrieval Cards')
            ->assertSee('Archive Retrieval Progress Payload');
    }

    /**
     * The archive retrieval API returns retrieval cards from archive entries.
     */
    public function test_archive_retrieval_lab_api_returns_retrieval_payload(): void
    {
        $response = $this->getJson('/api/practice/archive-retrieval-lab?family=laravel&language=en&search=api&phase_limit=2&tasks_per_phase=2&days=3');

        $response
            ->assertOk()
            ->assertJsonPath('data.source_archive_lab.entry_count', 3)
            ->assertJsonPath('data.retrieval_summary.card_count', 3)
            ->assertJsonPath('data.retrieval_summary.portfolio_ready_count', 3)
            ->assertJsonPath('data.retrieval_summary.refresh_required_count', 0)
            ->assertJsonPath('data.retrieval_cards.0.retrieval_mode', 'portfolio-ready')
            ->assertJsonStructure([
                'data' => [
                    'title',
                    'source_archive_lab',
                    'retrieval_rules',
                    'retrieval_cards' => [
                        '*' => [
                            'card',
                            'technology_segment',
                            'route_name',
                            'archive_status',
                            'retrieval_mode',
                            'search_keys',
                            'retrieval_prompt',
                            'reuse_targets',
                            'proof_to_quote',
                            'refresh_check',
                        ],
                    ],
                    'retrieval_summary',
                    'progress_payload' => [
                        'items',
                    ],
                ],
            ]);
    }
}
