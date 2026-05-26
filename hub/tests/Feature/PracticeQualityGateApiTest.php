<?php

declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;

final class PracticeQualityGateApiTest extends TestCase
{
    /**
     * The quality-gate API marks clean verification results as ready.
     */
    public function test_quality_gate_api_accepts_clean_results(): void
    {
        $response = $this->postJson('/api/practice/quality-gate', [
            'tests' => 31,
            'assertions' => 1519,
            'failures' => 0,
            'pint' => true,
        ]);

        $response
            ->assertOk()
            ->assertJsonPath('data.status', 'ready')
            ->assertJsonPath('data.passed', true)
            ->assertJsonPath('data.checks.tests_pass', true)
            ->assertJsonPath('data.checks.style_passes', true);
    }

    /**
     * The quality-gate API rejects invalid verification payloads.
     */
    public function test_quality_gate_api_validates_payload(): void
    {
        $response = $this->postJson('/api/practice/quality-gate', [
            'tests' => -1,
            'assertions' => 0,
            'failures' => 0,
            'pint' => 'maybe',
        ]);

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['tests', 'pint']);
    }
}
