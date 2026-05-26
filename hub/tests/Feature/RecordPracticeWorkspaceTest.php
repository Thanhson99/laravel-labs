<?php

declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;

final class RecordPracticeWorkspaceTest extends TestCase
{
    /**
     * The record workspace page renders the full source-to-code workflow.
     */
    public function test_record_workspace_page_renders_full_workflow(): void
    {
        $response = $this->get('/practice/record-workspace?record_id=laravel-api-integration-en-json-item-1&technology=api-validation');

        $response
            ->assertOk()
            ->assertSee('One question, one Laravel coding workspace.')
            ->assertSee('WhatIsAnApiInALaravelContextApiTest')
            ->assertSee('StoreWhatIsAnApiInALaravelContextRequest')
            ->assertSee('Starter Snippets');
    }

    /**
     * The record workspace API returns source, blueprint, checklist, and starter snippets.
     */
    public function test_record_workspace_api_returns_full_workflow(): void
    {
        $response = $this->getJson('/api/practice/record-workspace?record_id=laravel-api-integration-en-json-item-1&technology=api-validation');

        $response
            ->assertOk()
            ->assertJsonPath('data.source.path', 'laravel/api-integration.en.json')
            ->assertJsonPath('data.blueprint.names.test_class', 'WhatIsAnApiInALaravelContextApiTest')
            ->assertJsonPath('data.starter_kit.snippets.0.file', 'tests/Feature/WhatIsAnApiInALaravelContextApiTest.php')
            ->assertJsonStructure([
                'data' => [
                    'title',
                    'source',
                    'content',
                    'technology',
                    'practice',
                    'goal',
                    'blueprint',
                    'checklist',
                    'starter_kit',
                    'workflow_links',
                ],
            ]);
    }

    /**
     * The record workspace API returns 404 when the record is unknown.
     */
    public function test_record_workspace_api_returns_not_found_for_unknown_record(): void
    {
        $response = $this->getJson('/api/practice/record-workspace?record_id=missing-record');

        $response->assertNotFound();
    }
}
