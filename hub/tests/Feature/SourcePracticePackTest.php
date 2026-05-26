<?php

declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;

final class SourcePracticePackTest extends TestCase
{
    /**
     * The source practice pack page renders one JSON file as technology paths.
     */
    public function test_source_practice_pack_page_renders_source_workflow(): void
    {
        $response = $this->get('/practice/source-packs/laravel-api-integration-en-json');

        $response
            ->assertOk()
            ->assertSee('Source practice pack')
            ->assertSee('laravel/api-integration.en.json')
            ->assertSee('Technology Paths')
            ->assertSee('Open sample drill');
    }

    /**
     * The source practice pack API returns source, technologies, workflow, and commands.
     */
    public function test_source_practice_pack_api_returns_pack_data(): void
    {
        $response = $this->getJson('/api/practice/source-packs/laravel-api-integration-en-json');

        $response
            ->assertOk()
            ->assertJsonPath('data.source.path', 'laravel/api-integration.en.json')
            ->assertJsonPath('data.technologies.0.technology', 'api-validation')
            ->assertJsonPath('data.technologies.0.practice.slug', 'api-form-request-slice')
            ->assertJsonStructure([
                'data' => [
                    'source',
                    'record_count',
                    'technology_count',
                    'technologies' => [
                        '*' => [
                            'technology',
                            'record_count',
                            'practice',
                            'sample',
                            'drill_query',
                        ],
                    ],
                    'commands',
                    'workflow',
                ],
            ]);
    }

    /**
     * The source practice pack API returns 404 for unknown sources.
     */
    public function test_source_practice_pack_api_returns_not_found_for_unknown_source(): void
    {
        $response = $this->getJson('/api/practice/source-packs/not-found');

        $response->assertNotFound();
    }
}
