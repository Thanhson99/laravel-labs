<?php

declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;

final class ContentPracticeMapTest extends TestCase
{
    /**
     * The content map page links JSON learning content to native practice exercises.
     */
    public function test_content_practice_map_page_renders_mapped_tasks(): void
    {
        $response = $this->get('/practice/content-map?family=laravel&language=en&search=api&technology=api-validation&limit=5');

        $response
            ->assertOk()
            ->assertSee('Learning content is connected directly to Laravel coding work.')
            ->assertSee('api-validation')
            ->assertSee('Build a validated practice API endpoint')
            ->assertSee('Open source JSON');
    }

    /**
     * The content map API returns source, content, technology, and practice data.
     */
    public function test_content_practice_map_api_returns_mapped_tasks(): void
    {
        $response = $this->getJson('/api/practice/content-map?family=laravel&language=en&search=api&technology=api-validation&limit=3');

        $response
            ->assertOk()
            ->assertJsonPath('data.meta.filters.family', 'laravel')
            ->assertJsonPath('data.meta.filters.language', 'en')
            ->assertJsonPath('data.meta.filters.technology', 'api-validation')
            ->assertJsonPath('data.items.0.technology', 'api-validation')
            ->assertJsonPath('data.items.0.practice.slug', 'api-form-request-slice')
            ->assertJsonStructure([
                'data' => [
                    'items' => [
                        '*' => [
                            'id',
                            'technology',
                            'source' => [
                                'key',
                                'path',
                                'family',
                                'topic',
                                'language',
                                'title',
                            ],
                            'content' => [
                                'title',
                                'type',
                                'group',
                            ],
                            'practice' => [
                                'slug',
                                'title',
                                'track',
                            ],
                            'task',
                        ],
                    ],
                    'meta' => [
                        'filters',
                        'count',
                        'technologies',
                    ],
                ],
            ]);
    }
}
