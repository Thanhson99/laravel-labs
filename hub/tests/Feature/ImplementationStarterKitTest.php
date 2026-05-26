<?php

declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;

final class ImplementationStarterKitTest extends TestCase
{
    /**
     * The starter kit page renders code snippets for one content-backed implementation.
     */
    public function test_starter_kit_page_renders_code_snippets(): void
    {
        $response = $this->get('/practice/starter-kit?record_id=laravel-api-integration-en-json-item-1&technology=api-validation');

        $response
            ->assertOk()
            ->assertSee('Starter snippets for implementing the practice task.')
            ->assertSee('StoreWhatIsAnApiInALaravelContextRequest')
            ->assertSee('WhatIsAnApiInALaravelContextController')
            ->assertSee('WhatIsAnApiInALaravelContextApiTest');
    }

    /**
     * The starter kit API returns snippets and usage steps.
     */
    public function test_starter_kit_api_returns_snippets_and_usage(): void
    {
        $response = $this->getJson('/api/practice/starter-kit?record_id=laravel-api-integration-en-json-item-1&technology=api-validation');

        $response
            ->assertOk()
            ->assertJsonPath('data.snippets.0.file', 'tests/Feature/WhatIsAnApiInALaravelContextApiTest.php')
            ->assertJsonPath('data.snippets.1.file', 'app/Http/Requests/Api/StoreWhatIsAnApiInALaravelContextRequest.php')
            ->assertJsonPath('data.checklist.blueprint.names.route_path', '/api/practice/what-is-an-api-in-a-laravel-context')
            ->assertJsonStructure([
                'data' => [
                    'title',
                    'checklist',
                    'snippets' => [
                        '*' => [
                            'label',
                            'file',
                            'code',
                        ],
                    ],
                    'usage',
                ],
            ]);
    }

    /**
     * The starter kit API returns 404 when the underlying checklist is missing.
     */
    public function test_starter_kit_api_returns_not_found_for_unknown_record(): void
    {
        $response = $this->getJson('/api/practice/starter-kit?record_id=missing-record');

        $response->assertNotFound();
    }
}
