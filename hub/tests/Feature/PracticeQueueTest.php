<?php

declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;

final class PracticeQueueTest extends TestCase
{
    /**
     * The practice queue page renders record workspace links.
     */
    public function test_practice_queue_page_renders_workspace_links(): void
    {
        $response = $this->get('/practice/queue?family=laravel&language=en&search=api&technology=api-validation&limit=2');

        $response
            ->assertOk()
            ->assertSee('A practice queue from source questions to coding workspaces.')
            ->assertSee('Open record workspace')
            ->assertSee('Queue Progress Payload');
    }

    /**
     * The practice queue API returns ordered items and progress payload.
     */
    public function test_practice_queue_api_returns_ordered_items_and_progress_payload(): void
    {
        $response = $this->getJson('/api/practice/queue?family=laravel&language=en&search=api&technology=api-validation&limit=2');

        $response
            ->assertOk()
            ->assertJsonPath('data.items.0.position', 1)
            ->assertJsonPath('data.items.0.technology', 'api-validation')
            ->assertJsonPath('data.items.0.workspace_query.technology', 'api-validation')
            ->assertJsonPath('data.meta.estimated_minutes', 70)
            ->assertJsonStructure([
                'data' => [
                    'items' => [
                        '*' => [
                            'position',
                            'record_id',
                            'question',
                            'technology',
                            'source',
                            'practice',
                            'task',
                            'workspace_query',
                            'estimated_minutes',
                        ],
                    ],
                    'meta',
                    'progress_payload' => [
                        'items',
                    ],
                ],
            ]);
    }
}
