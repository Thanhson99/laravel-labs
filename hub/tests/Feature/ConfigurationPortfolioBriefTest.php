<?php

declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;

final class ConfigurationPortfolioBriefTest extends TestCase
{
    /**
     * The configuration portfolio brief page renders the portfolio artifact.
     */
    public function test_configuration_portfolio_brief_page_renders_artifact(): void
    {
        $response = $this->get('/practice/configuration-portfolio-brief');

        $response
            ->assertOk()
            ->assertSee('Configuration Portfolio Brief')
            ->assertSee('Proof Table')
            ->assertSee('Talking Points')
            ->assertSee('Review Checklist')
            ->assertSee('Open brief API');
    }

    /**
     * The configuration portfolio brief API returns proof and talking points.
     */
    public function test_configuration_portfolio_brief_api_returns_payload(): void
    {
        $response = $this->getJson('/api/practice/configuration-portfolio-brief');

        $response
            ->assertOk()
            ->assertJsonPath('data.archive_id', 'configuration-contract-promote-100')
            ->assertJsonPath('data.proof_table.0.audience', 'Portfolio note')
            ->assertJsonPath('data.proof_table.1.audience', 'Security review')
            ->assertJsonPath('data.proof_table.1.source_key', 'security misconfiguration release blockers')
            ->assertJsonPath('data.proof_table.3.source_key', 'INC-CONFIG-001 POSTMORTEM-CONFIG-001')
            ->assertJsonPath('data.talking_points.1', 'Security Misconfiguration controls turned debug, secret, CORS, header, cookie, proxy, and storage drift into fail-closed release blockers.')
            ->assertJsonPath('data.review_checklist.3', 'Security Misconfiguration proof names the unsafe signal, release blocker, and smoke-check evidence.')
            ->assertJsonPath('data.quality_gate.status', 'ready')
            ->assertJsonStructure([
                'data' => [
                    'title',
                    'summary',
                    'archive_id',
                    'headline',
                    'portfolio_paragraph',
                    'talking_points',
                    'proof_table',
                    'review_checklist',
                    'commands',
                    'quality_gate',
                ],
            ]);
    }
}
