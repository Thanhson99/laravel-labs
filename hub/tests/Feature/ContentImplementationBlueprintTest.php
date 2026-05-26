<?php

declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;

final class ContentImplementationBlueprintTest extends TestCase
{
    /**
     * The implementation blueprint page renders class and file names from a JSON record.
     */
    public function test_implementation_blueprint_page_renders_concrete_names(): void
    {
        $response = $this->get('/practice/implementation-blueprint?record_id=laravel-api-integration-en-json-item-1&technology=api-validation');

        $response
            ->assertOk()
            ->assertSee('Implementation Blueprint')
            ->assertSee('StoreWhatIsAnApiInALaravelContextRequest')
            ->assertSee('WhatIsAnApiInALaravelContextApiTest')
            ->assertSee('/api/practice/what-is-an-api-in-a-laravel-context');
    }

    /**
     * The implementation blueprint API returns concrete implementation names.
     */
    public function test_implementation_blueprint_api_returns_concrete_names(): void
    {
        $response = $this->getJson('/api/practice/implementation-blueprint?record_id=laravel-api-integration-en-json-item-1&technology=api-validation');

        $response
            ->assertOk()
            ->assertJsonPath('data.drill.source.path', 'laravel/api-integration.en.json')
            ->assertJsonPath('data.names.request_class', 'StoreWhatIsAnApiInALaravelContextRequest')
            ->assertJsonPath('data.names.test_class', 'WhatIsAnApiInALaravelContextApiTest')
            ->assertJsonPath('data.names.route_path', '/api/practice/what-is-an-api-in-a-laravel-context')
            ->assertJsonStructure([
                'data' => [
                    'title',
                    'drill',
                    'names',
                    'sequence',
                    'commands',
                ],
            ]);
    }

    /**
     * The implementation blueprint API returns 404 for unknown records.
     */
    public function test_implementation_blueprint_api_returns_not_found_for_unknown_record(): void
    {
        $response = $this->getJson('/api/practice/implementation-blueprint?record_id=missing-record');

        $response->assertNotFound();
    }
}
