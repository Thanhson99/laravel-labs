<?php

declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;

final class PracticeTddLabTest extends TestCase
{
    /**
     * The TDD lab page renders Red-Green-Refactor steps for one record.
     */
    public function test_tdd_lab_page_renders_red_green_refactor_steps(): void
    {
        $response = $this->get('/practice/tdd-lab?record_id=laravel-api-integration-en-json-item-1&technology=api-validation');

        $response
            ->assertOk()
            ->assertSee('Red-Green-Refactor lab turns one question into Laravel code.')
            ->assertSee('WhatIsAnApiInALaravelContextApiTest')
            ->assertSee('Open verification plan')
            ->assertSee('TDD Progress Payload');
    }

    /**
     * The TDD lab API returns cycle stages, snippets, and verification data.
     */
    public function test_tdd_lab_api_returns_cycle_and_verification_data(): void
    {
        $response = $this->getJson('/api/practice/tdd-lab?record_id=laravel-api-integration-en-json-item-1&technology=api-validation');

        $response
            ->assertOk()
            ->assertJsonPath('data.technology', 'api-validation')
            ->assertJsonPath('data.route.path', '/api/practice/what-is-an-api-in-a-laravel-context')
            ->assertJsonPath('data.cycle.0.stage', 'red')
            ->assertJsonPath('data.cycle.1.stage', 'green')
            ->assertJsonPath('data.cycle.2.stage', 'refactor')
            ->assertJsonPath('data.cycle.2.quality_gate_payload.pint', true)
            ->assertJsonStructure([
                'data' => [
                    'title',
                    'source',
                    'technology',
                    'record',
                    'route',
                    'files',
                    'cycle',
                    'progress_payload' => [
                        'items',
                    ],
                ],
            ]);
    }

    /**
     * The TDD lab API returns 404 for unknown records.
     */
    public function test_tdd_lab_api_returns_not_found_for_unknown_record(): void
    {
        $response = $this->getJson('/api/practice/tdd-lab?record_id=missing-record');

        $response->assertNotFound();
    }

    /**
     * JavaScript closure TDD labs use closure evidence goals and progress labels.
     */
    public function test_javascript_closure_tdd_lab_uses_scope_cycle(): void
    {
        $response = $this->getJson('/api/practice/tdd-lab?record_id=laravel-frontend-en-json-item-8&technology=javascript-closures');

        $response
            ->assertOk()
            ->assertJsonPath('data.technology', 'javascript-closures')
            ->assertJsonPath('data.cycle.0.title', 'Write the failing closure evidence test first')
            ->assertJsonPath('data.cycle.1.title', 'Implement the smallest closure evidence slice')
            ->assertJsonPath('data.cycle.2.title', 'Clean up and prove the closure explanation')
            ->assertJsonPath('data.progress_payload.items.0.label', 'Read source record and closure evidence blueprint');

        $this->assertStringContainsString('captured binding', $response->json('data.cycle.0.goal'));
        $this->assertStringContainsString('stale-closure evidence', $response->json('data.cycle.2.goal'));
    }
}
