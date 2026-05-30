<?php

declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;

final class ConfigurationReleaseEvidenceTest extends TestCase
{
    /**
     * The configuration release evidence page renders proof and API evidence.
     */
    public function test_configuration_release_evidence_page_renders_release_artifact(): void
    {
        $response = $this->get('/practice/configuration-release-evidence');

        $response
            ->assertOk()
            ->assertSee('Configuration Release Evidence')
            ->assertSee('Configuration runtime contract is ready for release.')
            ->assertSee('Proof Points')
            ->assertSee('API Evidence')
            ->assertSee('Security review focus')
            ->assertSee('Confirm debug mode, exposed secrets, broad CORS, missing headers, weak cookies, public storage, and proxy drift are release blockers.')
            ->assertSee('Portfolio Notes')
            ->assertSee('Explain how Security Misconfiguration controls block unsafe production defaults before deploy.')
            ->assertSee('Open evidence API');
    }

    /**
     * The configuration release evidence API returns proof, smoke, and rollback evidence.
     */
    public function test_configuration_release_evidence_api_returns_release_artifact(): void
    {
        $response = $this->getJson('/api/practice/configuration-release-evidence');

        $response
            ->assertOk()
            ->assertJsonPath('data.release_summary.quality_status', 'ready')
            ->assertJsonPath('data.release_summary.risk_level', 'low')
            ->assertJsonPath('data.release_summary.security_review_focus.0', 'Confirm debug mode, exposed secrets, broad CORS, missing headers, weak cookies, public storage, and proxy drift are release blockers.')
            ->assertJsonPath('data.api_evidence.0.endpoint', '/practice/configuration-readiness')
            ->assertJsonPath('data.rollback_summary.1.area', 'Authentication contract')
            ->assertJsonPath('data.portfolio_notes.1', 'Explain how Security Misconfiguration controls block unsafe production defaults before deploy.')
            ->assertJsonPath('data.quality_gate.status', 'ready')
            ->assertJsonStructure([
                'data' => [
                    'title',
                    'summary',
                    'release_summary',
                    'proof_points',
                    'api_evidence',
                    'rollback_summary',
                    'portfolio_notes',
                    'commands',
                    'quality_gate',
                ],
            ]);
    }
}
