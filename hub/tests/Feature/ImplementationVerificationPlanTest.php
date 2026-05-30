<?php

declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;

final class ImplementationVerificationPlanTest extends TestCase
{
    /**
     * The verification plan page renders commands and smoke request.
     */
    public function test_verification_plan_page_renders_commands(): void
    {
        $response = $this->get('/practice/verification-plan?record_id=laravel-api-integration-en-json-item-1&technology=api-validation');

        $response
            ->assertOk()
            ->assertSee('Verification commands for code generated from one question.')
            ->assertSee('php artisan test --filter WhatIsAnApiInALaravelContextApiTest')
            ->assertSee('Quality Gate Payload');
    }

    /**
     * The verification plan API returns commands, smoke request, and quality gate payload.
     */
    public function test_verification_plan_api_returns_verification_data(): void
    {
        $response = $this->getJson('/api/practice/verification-plan?record_id=laravel-api-integration-en-json-item-1&technology=api-validation');

        $response
            ->assertOk()
            ->assertJsonPath('data.commands.0.command', 'php artisan test --filter WhatIsAnApiInALaravelContextApiTest')
            ->assertJsonPath('data.smoke_request.method', 'POST')
            ->assertJsonPath('data.smoke_request.path', '/api/practice/what-is-an-api-in-a-laravel-context')
            ->assertJsonPath('data.quality_gate_payload.pint', true)
            ->assertJsonStructure([
                'data' => [
                    'title',
                    'source',
                    'technology',
                    'commands',
                    'smoke_request',
                    'quality_gate_payload',
                    'done_when',
                ],
            ]);
    }

    /**
     * The verification plan API returns 404 for unknown records.
     */
    public function test_verification_plan_api_returns_not_found_for_unknown_record(): void
    {
        $response = $this->getJson('/api/practice/verification-plan?record_id=missing-record');

        $response->assertNotFound();
    }

    /**
     * JavaScript closure verification plans check rendered closure evidence.
     */
    public function test_javascript_closure_verification_plan_uses_scope_done_criteria(): void
    {
        $response = $this->getJson('/api/practice/verification-plan?record_id=laravel-frontend-en-json-item-8&technology=javascript-closures');

        $response
            ->assertOk()
            ->assertJsonPath('data.technology', 'javascript-closures')
            ->assertJsonPath('data.smoke_request.headers.Accept', 'text/html,application/json')
            ->assertJsonPath('data.smoke_request.expected_text.0', 'lexical scope')
            ->assertJsonPath('data.smoke_request.expected_text.3', 'stale closure')
            ->assertJsonPath('data.quality_gate_payload.assertions', 4)
            ->assertJsonPath('data.done_when.0', 'The focused closure test passes for the generated test class.')
            ->assertJsonPath('data.done_when.2', 'The smoke request renders lexical scope, captured binding, createCounter(), and stale-closure evidence.');
    }

    /**
     * Arrow-function this verification plans check lexical-this evidence.
     */
    public function test_arrow_this_verification_plan_uses_lexical_this_done_criteria(): void
    {
        $response = $this->getJson('/api/practice/verification-plan?record_id=laravel-frontend-en-json-item-62&technology=javascript-closures');

        $response
            ->assertOk()
            ->assertJsonPath('data.technology', 'javascript-closures')
            ->assertJsonPath('data.smoke_request.headers.Accept', 'text/html,application/json')
            ->assertJsonPath('data.smoke_request.expected_text.0', 'lexical this')
            ->assertJsonPath('data.smoke_request.expected_text.3', 'call/apply/bind cannot rebind arrow this')
            ->assertJsonPath('data.quality_gate_payload.assertions', 4)
            ->assertJsonPath('data.done_when.0', 'The focused arrow-this test passes for the generated test class.')
            ->assertJsonPath('data.done_when.2', 'The smoke request renders lexical this, dynamic this, obj.arrow() trap, and call/apply/bind evidence.');
    }

    /**
     * IDOR verification plans check object authorization evidence and denial probes.
     */
    public function test_idor_verification_plan_uses_object_authorization_done_criteria(): void
    {
        $response = $this->getJson('/api/practice/verification-plan?record_id=laravel-auth-security-en-json-item-113&technology=idor-access-control');

        $response
            ->assertOk()
            ->assertJsonPath('data.technology', 'idor-access-control')
            ->assertJsonPath('data.smoke_request.headers.Accept', 'text/html,application/json')
            ->assertJsonPath('data.smoke_request.expected_text.0', 'object-level authorization')
            ->assertJsonPath('data.smoke_request.expected_text.3', 'ID-swap denial test')
            ->assertJsonPath('data.smoke_request.denial_probe.expected_status', 403)
            ->assertJsonPath('data.smoke_request.denial_probe.fallback_status', 404)
            ->assertJsonPath('data.quality_gate_payload.assertions', 4)
            ->assertJsonPath('data.done_when.0', 'The focused IDOR test passes for the generated test class.')
            ->assertJsonPath('data.done_when.3', 'The denial probe proves user A cannot access user B object with 403 or deliberately scoped 404 behavior.');
    }
}
