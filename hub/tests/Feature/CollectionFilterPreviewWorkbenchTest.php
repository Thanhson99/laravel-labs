<?php

declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;

final class CollectionFilterPreviewWorkbenchTest extends TestCase
{
    /**
     * The collection filter workbench renders the list-page practice loop.
     */
    public function test_collection_filter_preview_workbench_renders(): void
    {
        $response = $this->get('/workbench/collection-filter-preview');

        $response
            ->assertOk()
            ->assertSee('Collection Filter Preview Workbench')
            ->assertSee('POST /api/practice/collection-filter-preview')
            ->assertSee('CollectionFilterPreviewService')
            ->assertSee('Preview filtered list');
    }

    /**
     * The collection filter API returns filtered records with pagination metadata.
     */
    public function test_collection_filter_preview_api_returns_filtered_records(): void
    {
        $response = $this->postJson('/api/practice/collection-filter-preview', [
            'search' => 'api',
            'status' => 'open',
            'page' => 1,
            'per_page' => 2,
        ]);

        $response
            ->assertOk()
            ->assertJsonPath('data.items.0.title', 'Add API validation tests')
            ->assertJsonPath('data.items.0.status', 'open')
            ->assertJsonPath('data.meta.total', 1)
            ->assertJsonPath('data.meta.last_page', 1)
            ->assertJsonPath('data.applied_filters.search', 'api');
    }

    /**
     * Invalid filter payloads return validation errors.
     */
    public function test_collection_filter_preview_api_validates_payload(): void
    {
        $response = $this->postJson('/api/practice/collection-filter-preview', [
            'status' => 'blocked',
            'page' => 0,
            'per_page' => 99,
        ]);

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['status', 'page', 'per_page']);
    }
}
