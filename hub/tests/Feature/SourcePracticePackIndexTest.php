<?php

declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;

final class SourcePracticePackIndexTest extends TestCase
{
    /**
     * The source pack index page renders JSON sources with practice links.
     */
    public function test_source_pack_index_page_renders_source_packs(): void
    {
        $response = $this->get('/practice/source-packs?family=laravel&language=en&search=api&limit=5');

        $response
            ->assertOk()
            ->assertSee('Choose a source file and turn it into a practice pack.')
            ->assertSee('laravel/api-integration.en.json')
            ->assertSee('Open source pack')
            ->assertSee('Open sample workspace');
    }

    /**
     * The source pack index API returns source summaries and technology counts.
     */
    public function test_source_pack_index_api_returns_source_summaries(): void
    {
        $response = $this->getJson('/api/practice/source-packs?family=laravel&language=en&search=api&limit=5');

        $response
            ->assertOk()
            ->assertJsonPath('data.items.0.source.path', 'laravel/api-integration.en.json')
            ->assertJsonPath('data.items.0.technologies.0.technology', 'api-validation')
            ->assertJsonPath('data.items.0.sample_workspace_query.technology', 'api-validation')
            ->assertJsonStructure([
                'data' => [
                    'items' => [
                        '*' => [
                            'source',
                            'record_count',
                            'technologies',
                            'sample_workspace_query',
                        ],
                    ],
                    'meta' => [
                        'filters',
                        'count',
                        'available_sources',
                    ],
                ],
            ]);
    }

    /**
     * The source pack index defaults to English source files.
     */
    public function test_source_pack_index_defaults_to_english_sources(): void
    {
        $this->get('/practice/source-packs?family=laravel&search=Sail&limit=5')
            ->assertOk()
            ->assertDontSee('Sail, môi trường và triển khai')
            ->assertDontSee('từ local đồng nhất tới production sống khỏe');

        $this->getJson('/api/practice/source-packs?family=laravel&search=Sail&limit=5')
            ->assertOk()
            ->assertJsonPath('data.meta.filters.language', 'en')
            ->assertJsonMissing([
                'language' => 'vi',
            ]);
    }
}
