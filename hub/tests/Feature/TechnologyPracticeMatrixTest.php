<?php

declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;

final class TechnologyPracticeMatrixTest extends TestCase
{
    /**
     * The technology matrix page groups content records by practice technology.
     */
    public function test_technology_matrix_page_renders_content_coverage(): void
    {
        $response = $this->get('/practice/technology-matrix?family=laravel&language=en&search=api');

        $response
            ->assertOk()
            ->assertSee('Technology map from source files to coding exercises.')
            ->assertSee('api-validation')
            ->assertSee('Open sample drill')
            ->assertSee('Drills for technology')
            ->assertSee('Open pipeline')
            ->assertSee('Open code examples');
    }

    /**
     * The technology matrix API returns counts, source samples, and practice links.
     */
    public function test_technology_matrix_api_returns_grouped_practice_data(): void
    {
        $response = $this->getJson('/api/practice/technology-matrix?family=laravel&language=en&search=api');

        $response
            ->assertOk()
            ->assertJsonPath('data.items.0.technology', 'api-validation')
            ->assertJsonPath('data.items.0.practice.slug', 'api-form-request-slice')
            ->assertJsonPath('data.items.0.drill_query.technology', 'api-validation')
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
                            'drill_query',
                        ],
                    ],
                    'meta' => [
                        'filters',
                        'record_count',
                        'technology_count',
                    ],
                ],
            ]);
    }

    /**
     * The technology matrix defaults to English records.
     */
    public function test_technology_matrix_defaults_to_english_records(): void
    {
        $this->get('/practice/technology-matrix?family=laravel&search=Sail')
            ->assertOk()
            ->assertDontSee('Sail, môi trường và triển khai')
            ->assertDontSee('từ local đồng nhất tới production sống khỏe');

        $this->getJson('/api/practice/technology-matrix?family=laravel&search=Sail')
            ->assertOk()
            ->assertJsonPath('data.meta.filters.language', 'en')
            ->assertJsonMissing([
                'source_path' => 'laravel/devops.vi.json',
            ]);
    }
}
