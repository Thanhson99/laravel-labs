<?php

declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;

final class TechnologyCodeExampleTest extends TestCase
{
    /**
     * The technology code example page renders generated snippets from JSON records.
     */
    public function test_technology_code_example_page_renders_generated_snippets(): void
    {
        $response = $this->get('/practice/technology-code-examples?family=laravel&language=en&search=api&limit=10');

        $response
            ->assertOk()
            ->assertSee('Code samples generated from the technologies inside JSON content.')
            ->assertSee('api-validation')
            ->assertSee('ContentBackedApiDrillController')
            ->assertSee('StoreContentBackedDrillRequest')
            ->assertSee('Open pipeline')
            ->assertSee('Open technology detail')
            ->assertSee('Open workspace')
            ->assertSee('Open examples API');
    }

    /**
     * The technology code example API returns source-backed code snippets by technology.
     */
    public function test_technology_code_example_api_returns_snippets_by_technology(): void
    {
        $response = $this->getJson('/api/practice/technology-code-examples?family=laravel&language=en&search=api&limit=10');

        $response
            ->assertOk()
            ->assertJsonPath('data.items.0.technology', 'api-validation')
            ->assertJsonPath('data.items.0.snippets.0.file', 'routes/api/practice-actions.php')
            ->assertJsonPath('data.items.0.workspace_query.technology', 'api-validation')
            ->assertJsonStructure([
                'data' => [
                    'items' => [
                        '*' => [
                            'technology',
                            'record_count',
                            'source',
                            'content',
                            'practice',
                            'task',
                            'workspace_query',
                            'snippets' => [
                                '*' => [
                                    'label',
                                    'file',
                                    'code',
                                ],
                            ],
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
     * The technology detail page renders multiple source-backed records for one technology.
     */
    public function test_technology_code_example_detail_page_renders_record_level_examples(): void
    {
        $response = $this->get('/practice/technology-code-examples/api-validation?family=laravel&language=en&search=api&limit=3');

        $response
            ->assertOk()
            ->assertSee('Record-level code examples for api-validation.')
            ->assertSee('Open record workspace')
            ->assertSee('ContentBackedApiDrillController')
            ->assertSee('Open detail API');
    }

    /**
     * The technology detail API returns record-specific snippets and workspace links.
     */
    public function test_technology_code_example_detail_api_returns_record_level_examples(): void
    {
        $response = $this->getJson('/api/practice/technology-code-examples/api-validation?family=laravel&language=en&search=api&limit=3');

        $response
            ->assertOk()
            ->assertJsonPath('data.technology', 'api-validation')
            ->assertJsonPath('data.items.0.technology', 'api-validation')
            ->assertJsonPath('data.items.0.snippets.0.file', 'routes/api/practice-actions.php')
            ->assertJsonPath('data.items.0.workspace_query.technology', 'api-validation')
            ->assertJsonStructure([
                'data' => [
                    'technology',
                    'items' => [
                        '*' => [
                            'record_id',
                            'technology',
                            'source',
                            'content',
                            'practice',
                            'task',
                            'workspace_query',
                            'snippets',
                        ],
                    ],
                    'meta' => [
                        'filters',
                        'record_count',
                    ],
                ],
            ]);
    }
}
