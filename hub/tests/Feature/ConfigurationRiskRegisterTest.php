<?php

declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;

final class ConfigurationRiskRegisterTest extends TestCase
{
    /**
     * The configuration risk register page renders risk cards and review cadence.
     */
    public function test_configuration_risk_register_page_renders_risks(): void
    {
        $response = $this->get('/practice/configuration-risk-register');

        $response
            ->assertOk()
            ->assertSee('Configuration Risk Register')
            ->assertSee('auth-contract-drift')
            ->assertSee('quality-gate-shape-break')
            ->assertSee('Review Cadence')
            ->assertSee('Open risk API');
    }

    /**
     * The configuration risk register API returns risk cards and dashboard status.
     */
    public function test_configuration_risk_register_api_returns_risk_payload(): void
    {
        $response = $this->getJson('/api/practice/configuration-risk-register');

        $response
            ->assertOk()
            ->assertJsonPath('data.status.quality', 'ready')
            ->assertJsonPath('data.risk_count', 4)
            ->assertJsonPath('data.highest_severity', 'high')
            ->assertJsonPath('data.risks.1.key', 'auth-contract-drift')
            ->assertJsonStructure([
                'data' => [
                    'title',
                    'summary',
                    'status',
                    'risk_count',
                    'highest_severity',
                    'risks' => [
                        '*' => [
                            'key',
                            'severity',
                            'area',
                            'signal',
                            'mitigation',
                            'owner_route',
                        ],
                    ],
                    'review_cadence',
                    'commands',
                ],
            ]);
    }
}
