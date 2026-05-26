<?php

declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;

final class TechnologyPracticeBoardTest extends TestCase
{
    /**
     * The technology board page renders source groups and workspace links.
     */
    public function test_technology_board_page_renders_source_groups(): void
    {
        $response = $this->get('/practice/technology-board?technology=api-validation&family=laravel&language=en&search=api&limit=5');

        $response
            ->assertOk()
            ->assertSee('Technology practice board for source-backed Laravel drills.')
            ->assertSee('laravel/api-integration.en.json')
            ->assertSee('Open source pack')
            ->assertSee('Open workspace');
    }

    /**
     * The technology board API returns grouped source records and queue query.
     */
    public function test_technology_board_api_returns_grouped_source_records(): void
    {
        $response = $this->getJson('/api/practice/technology-board?technology=api-validation&family=laravel&language=en&search=api&limit=5');

        $response
            ->assertOk()
            ->assertJsonPath('data.technology', 'api-validation')
            ->assertJsonPath('data.sources.0.source.path', 'laravel/api-integration.en.json')
            ->assertJsonPath('data.sources.0.sample_records.0.workspace_query.technology', 'api-validation')
            ->assertJsonPath('data.queue_query.technology', 'api-validation')
            ->assertJsonStructure([
                'data' => [
                    'technology',
                    'sources' => [
                        '*' => [
                            'source',
                            'record_count',
                            'practice',
                            'sample_records',
                        ],
                    ],
                    'queue_query',
                    'meta',
                ],
            ]);
    }
}
