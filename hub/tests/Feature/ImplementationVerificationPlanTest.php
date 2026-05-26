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
}
