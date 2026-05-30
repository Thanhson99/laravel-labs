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

    /**
     * JavaScript closure starter kits provide closure evidence snippets.
     */
    public function test_javascript_closure_starter_kit_returns_scope_snippets(): void
    {
        $response = $this->getJson('/api/practice/starter-kit?record_id=laravel-frontend-en-json-item-8&technology=javascript-closures');

        $response
            ->assertOk()
            ->assertJsonPath('data.checklist.blueprint.drill.technology', 'javascript-closures')
            ->assertJsonPath('data.snippets.0.label', 'Feature test')
            ->assertJsonPath('data.snippets.1.label', 'Service')
            ->assertJsonPath('data.usage.1', 'Paste the closure feature test and evidence service snippets.')
            ->assertJsonPath('data.usage.3', 'Run the focused closure test first, then the full suite and Pint.');

        $this->assertStringContainsString('assertSee(\'lexical scope\')', $response->json('data.snippets.0.code'));
        $this->assertStringContainsString('captured_binding', $response->json('data.snippets.1.code'));
        $this->assertStringContainsString('stale closure', $response->json('data.snippets.1.code'));
    }

    /**
     * Arrow-function this starter kits provide lexical-this comparison snippets.
     */
    public function test_arrow_this_starter_kit_returns_lexical_this_snippets(): void
    {
        $response = $this->getJson('/api/practice/starter-kit?record_id=laravel-frontend-en-json-item-62&technology=javascript-closures');

        $response
            ->assertOk()
            ->assertJsonPath('data.checklist.blueprint.drill.technology', 'javascript-closures')
            ->assertJsonPath('data.snippets.0.label', 'Feature test')
            ->assertJsonPath('data.snippets.1.label', 'Service')
            ->assertJsonPath('data.usage.1', 'Paste the arrow-this feature test and comparison service snippets.')
            ->assertJsonPath('data.usage.3', 'Run the focused arrow-this test first, then the full suite and Pint.');

        $this->assertStringContainsString('assertSee(\'lexical this\')', $response->json('data.snippets.0.code'));
        $this->assertStringContainsString('dynamic this comes from the call site', $response->json('data.snippets.1.code'));
        $this->assertStringContainsString('call/apply/bind cannot rebind arrow this', $response->json('data.snippets.1.code'));
    }

    /**
     * IDOR starter kits provide object authorization snippets.
     */
    public function test_idor_starter_kit_returns_object_authorization_snippets(): void
    {
        $response = $this->getJson('/api/practice/starter-kit?record_id=laravel-auth-security-en-json-item-113&technology=idor-access-control');

        $response
            ->assertOk()
            ->assertJsonPath('data.checklist.blueprint.drill.technology', 'idor-access-control')
            ->assertJsonPath('data.snippets.0.label', 'Feature test')
            ->assertJsonPath('data.snippets.1.label', 'Service')
            ->assertJsonPath('data.usage.1', 'Paste the IDOR feature test and object-authorization service snippets.')
            ->assertJsonPath('data.usage.3', 'Run the focused IDOR test first, then the full suite and Pint.');

        $this->assertStringContainsString('ID-swap denial test', $response->json('data.snippets.0.code'));
        $this->assertStringContainsString('object-level authorization', $response->json('data.snippets.1.code'));
        $this->assertStringContainsString('scoped_lookup', $response->json('data.snippets.1.code'));
    }
}
