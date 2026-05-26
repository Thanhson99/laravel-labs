<?php

declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;

final class ContentPracticeDrillTest extends TestCase
{
    /**
     * The content drill page turns one source record into concrete coding instructions.
     */
    public function test_content_practice_drill_page_renders_coding_instructions(): void
    {
        $response = $this->get('/practice/content-drill?source_key=laravel-api-integration-en-json&technology=api-validation');

        $response
            ->assertOk()
            ->assertSee('Content Drill')
            ->assertSee('laravel/api-integration.en.json')
            ->assertSee('Create a Form Request and API response')
            ->assertSee('Implementation Steps')
            ->assertSee('Files')
            ->assertSee('Starter Code')
            ->assertSee('Runnable workbench')
            ->assertSee('Open API validation workbench')
            ->assertSee('ContentBackedApiDrillController')
            ->assertSee('ContentBackedApiDrillService')
            ->assertSee('StoreContentBackedDrillRequest')
            ->assertSee('Open starter kit');
    }

    /**
     * The content drill API returns source, practice, steps, files, and commands.
     */
    public function test_content_practice_drill_api_returns_drill_plan(): void
    {
        $response = $this->getJson('/api/practice/content-drill?record_id=laravel-api-integration-en-json-item-1&technology=api-validation');

        $response
            ->assertOk()
            ->assertJsonPath('data.technology', 'api-validation')
            ->assertJsonPath('data.source.path', 'laravel/api-integration.en.json')
            ->assertJsonPath('data.content.title', '1. What is an API in a Laravel context?')
            ->assertJsonPath('data.practice.slug', 'api-form-request-slice')
            ->assertJsonStructure([
                'data' => [
                    'title',
                    'technology',
                    'source',
                    'content',
                    'practice',
                    'goal',
                    'implementation_steps',
                    'files',
                    'related_workbench',
                    'starter_snippets',
                    'commands',
                    'acceptance',
                ],
            ]);

        $response
            ->assertJsonPath('data.starter_snippets.0.file', 'routes/api/practice-actions.php')
            ->assertJsonPath('data.starter_snippets.3.file', 'app/Http/Controllers/Api/ContentBackedApiDrillController.php')
            ->assertJsonPath('data.starter_snippets.4.file', 'app/Services/Practice/ContentBackedApiDrillService.php')
            ->assertJsonPath('data.related_workbench.path', '/workbench/topic-intake')
            ->assertJsonPath('data.commands.0', 'php artisan test --filter ContentBackedDrill');
    }

    /**
     * Exact record links from quiz cards should not be blocked by default Laravel API filters.
     */
    public function test_content_practice_drill_exact_record_lookup_ignores_default_filters(): void
    {
        $response = $this->getJson('/api/practice/content-drill?record_id=php-starter-en-json-phase-topic-1&source_key=php-starter-en-json');

        $response
            ->assertOk()
            ->assertJsonPath('data.technology', 'php')
            ->assertJsonPath('data.source.path', 'php/starter.en.json')
            ->assertJsonStructure([
                'data' => [
                    'starter_snippets' => [
                        '*' => [
                            'label',
                            'file',
                            'code',
                        ],
                    ],
                ],
            ]);
    }

    /**
     * The content drill API returns 404 when no source matches.
     */
    public function test_content_practice_drill_api_returns_not_found_for_unknown_source(): void
    {
        $response = $this->getJson('/api/practice/content-drill?source_key=missing-source');

        $response->assertNotFound();
    }
}
