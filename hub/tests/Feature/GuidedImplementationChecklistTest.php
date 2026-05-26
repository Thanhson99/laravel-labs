<?php

declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;

final class GuidedImplementationChecklistTest extends TestCase
{
    /**
     * The guided checklist page renders TDD steps for one content-backed implementation.
     */
    public function test_guided_checklist_page_renders_tdd_steps(): void
    {
        $response = $this->get('/practice/guided-checklist?record_id=laravel-api-integration-en-json-item-1&technology=api-validation');

        $response
            ->assertOk()
            ->assertSee('A TDD checklist for turning one question into Laravel code.')
            ->assertSee('Write failing test')
            ->assertSee('StoreWhatIsAnApiInALaravelContextRequest.php')
            ->assertSee('Progress Payload');
    }

    /**
     * The guided checklist API returns checklist items and progress payload.
     */
    public function test_guided_checklist_api_returns_items_and_progress_payload(): void
    {
        $response = $this->getJson('/api/practice/guided-checklist?record_id=laravel-api-integration-en-json-item-1&technology=api-validation');

        $response
            ->assertOk()
            ->assertJsonPath('data.blueprint.names.test_class', 'WhatIsAnApiInALaravelContextApiTest')
            ->assertJsonPath('data.items.1.label', 'Write failing test')
            ->assertJsonPath('data.progress_api', '/api/practice/progress-checklist')
            ->assertJsonStructure([
                'data' => [
                    'title',
                    'blueprint',
                    'items' => [
                        '*' => [
                            'label',
                            'detail',
                            'done',
                        ],
                    ],
                    'progress_payload' => [
                        'items',
                    ],
                    'progress_api',
                ],
            ]);
    }

    /**
     * The guided checklist API returns 404 when the underlying blueprint is missing.
     */
    public function test_guided_checklist_api_returns_not_found_for_unknown_record(): void
    {
        $response = $this->getJson('/api/practice/guided-checklist?record_id=missing-record');

        $response->assertNotFound();
    }
}
