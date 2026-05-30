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

    /**
     * JavaScript closure guided checklists use closure evidence steps.
     */
    public function test_javascript_closure_guided_checklist_uses_scope_steps(): void
    {
        $response = $this->getJson('/api/practice/guided-checklist?record_id=laravel-frontend-en-json-item-8&technology=javascript-closures');

        $response
            ->assertOk()
            ->assertJsonPath('data.blueprint.drill.technology', 'javascript-closures')
            ->assertJsonPath('data.items.0.label', 'Read closure source record')
            ->assertJsonPath('data.items.1.label', 'Write failing closure test')
            ->assertJsonPath('data.items.2.label', 'Define evidence payload')
            ->assertJsonPath('data.items.4.label', 'Move closure logic to service');

        $this->assertStringContainsString('captured binding', $response->json('data.items.1.detail'));
        $this->assertStringContainsString('createCounter()', $response->json('data.items.2.detail'));
    }

    /**
     * Arrow-function this guided checklists use lexical-this evidence steps.
     */
    public function test_arrow_this_guided_checklist_uses_lexical_this_steps(): void
    {
        $response = $this->getJson('/api/practice/guided-checklist?record_id=laravel-frontend-en-json-item-62&technology=javascript-closures');

        $response
            ->assertOk()
            ->assertJsonPath('data.blueprint.drill.technology', 'javascript-closures')
            ->assertJsonPath('data.items.0.label', 'Read arrow-this source record')
            ->assertJsonPath('data.items.1.label', 'Write failing arrow-this test')
            ->assertJsonPath('data.items.2.label', 'Define this-binding evidence')
            ->assertJsonPath('data.items.4.label', 'Move this-binding logic to service');

        $this->assertStringContainsString('lexical `this`', $response->json('data.items.1.detail'));
        $this->assertStringContainsString('call/apply/bind limitation', $response->json('data.items.2.detail'));
    }

    /**
     * IDOR guided checklists use object authorization steps.
     */
    public function test_idor_guided_checklist_uses_object_authorization_steps(): void
    {
        $response = $this->getJson('/api/practice/guided-checklist?record_id=laravel-auth-security-en-json-item-113&technology=idor-access-control');

        $response
            ->assertOk()
            ->assertJsonPath('data.blueprint.drill.technology', 'idor-access-control')
            ->assertJsonPath('data.items.0.label', 'Read IDOR source record')
            ->assertJsonPath('data.items.1.label', 'Write failing ID-swap test')
            ->assertJsonPath('data.items.2.label', 'Inventory object surface')
            ->assertJsonPath('data.items.3.label', 'Add scoped lookup evidence')
            ->assertJsonPath('data.items.4.label', 'Add object authorization check');

        $this->assertStringContainsString('second user or tenant', $response->json('data.items.1.detail'));
        $this->assertStringContainsString('downloads, exports', $response->json('data.items.2.detail'));
        $this->assertStringContainsString('policy, Gate', $response->json('data.items.4.detail'));
    }
}
