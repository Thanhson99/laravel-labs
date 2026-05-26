<?php

declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;

final class TechnologyPipelineIndexTest extends TestCase
{
    /**
     * The technology pipeline index page lists discoverable pipeline links.
     */
    public function test_technology_pipeline_index_page_lists_pipeline_links(): void
    {
        $response = $this->get('/practice/technology-pipelines?family=laravel&language=en&search=api');

        $response
            ->assertOk()
            ->assertSee('Technology pipelines from JSON content.')
            ->assertSee('api-validation')
            ->assertSee('Open pipeline')
            ->assertSee('Open code examples')
            ->assertSee('Open pipelines API');
    }

    /**
     * The technology pipeline index API returns grouped pipeline metadata and routes.
     */
    public function test_technology_pipeline_index_api_returns_grouped_pipeline_routes(): void
    {
        $response = $this->getJson('/api/practice/technology-pipelines?family=laravel&language=en&search=api');

        $response
            ->assertOk()
            ->assertJsonPath('data.items.0.technology', 'api-validation')
            ->assertJsonPath('data.items.0.pipeline_route', '/practice/technology-learning-pipeline/api-validation?family=laravel&language=en&search=api')
            ->assertJsonPath('data.meta.query.search', 'api')
            ->assertJsonStructure([
                'data' => [
                    'items' => [
                        '*' => [
                            'technology',
                            'record_count',
                            'source_count',
                            'families',
                            'sources',
                            'sample',
                            'practice',
                            'pipeline_route',
                            'api_pipeline_route',
                            'code_examples_route',
                        ],
                    ],
                    'meta' => [
                        'filters',
                        'record_count',
                        'technology_count',
                        'query',
                        'pipeline_count',
                    ],
                ],
            ]);
    }
}
