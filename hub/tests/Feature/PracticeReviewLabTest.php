<?php

declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;

final class PracticeReviewLabTest extends TestCase
{
    /**
     * The review lab page renders review items tied to generated files.
     */
    public function test_review_lab_page_renders_review_checklist(): void
    {
        $response = $this->get('/practice/review-lab?record_id=laravel-api-integration-en-json-item-1&technology=api-validation');

        $response
            ->assertOk()
            ->assertSee('Review lab for checking code after TDD practice.')
            ->assertSee('Thin controller')
            ->assertSee('StoreWhatIsAnApiInALaravelContextRequest.php')
            ->assertSee('Review Progress Payload');
    }

    /**
     * The review lab API returns checklist, commands, and progress payload.
     */
    public function test_review_lab_api_returns_checklist_and_progress_payload(): void
    {
        $response = $this->getJson('/api/practice/review-lab?record_id=laravel-api-integration-en-json-item-1&technology=api-validation');

        $response
            ->assertOk()
            ->assertJsonPath('data.technology', 'api-validation')
            ->assertJsonPath('data.route.path', '/api/practice/what-is-an-api-in-a-laravel-context')
            ->assertJsonPath('data.review_items.3.label', 'Thin controller')
            ->assertJsonPath('data.commands.0.command', 'php artisan test --filter WhatIsAnApiInALaravelContextApiTest')
            ->assertJsonStructure([
                'data' => [
                    'title',
                    'source',
                    'technology',
                    'record',
                    'route',
                    'files',
                    'review_items' => [
                        '*' => [
                            'label',
                            'question',
                            'file',
                        ],
                    ],
                    'commands',
                    'quality_gate_payload',
                    'progress_payload' => [
                        'items',
                    ],
                ],
            ]);
    }

    /**
     * The review lab API returns 404 for unknown records.
     */
    public function test_review_lab_api_returns_not_found_for_unknown_record(): void
    {
        $response = $this->getJson('/api/practice/review-lab?record_id=missing-record');

        $response->assertNotFound();
    }

    /**
     * JavaScript closure review labs check closure evidence, not generic Laravel behavior.
     */
    public function test_javascript_closure_review_lab_uses_scope_checklist(): void
    {
        $response = $this->getJson('/api/practice/review-lab?record_id=laravel-frontend-en-json-item-8&technology=javascript-closures');

        $response
            ->assertOk()
            ->assertJsonPath('data.technology', 'javascript-closures')
            ->assertJsonPath('data.review_items.2.label', 'Evidence boundary')
            ->assertJsonPath('data.review_items.4.label', 'Closure behavior');

        $this->assertStringContainsString('closure evidence shape', $response->json('data.review_items.1.question'));
        $this->assertStringContainsString('lexical scope', $response->json('data.review_items.4.question'));
        $this->assertStringContainsString('stale-closure evidence drifts', $response->json('data.review_items.5.question'));
    }
}
