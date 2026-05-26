<?php

declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;

final class ConfigurationEvidenceReusePlanTest extends TestCase
{
    /**
     * The configuration evidence reuse plan page renders reuse tasks.
     */
    public function test_configuration_evidence_reuse_plan_page_renders_tasks(): void
    {
        $response = $this->get('/practice/configuration-evidence-reuse-plan');

        $response
            ->assertOk()
            ->assertSee('Configuration Evidence Reuse Plan')
            ->assertSee('Reuse Tasks')
            ->assertSee('Portfolio note')
            ->assertSee('Incident recovery')
            ->assertSee('Open reuse API');
    }

    /**
     * The configuration evidence reuse plan API returns reuse tasks and checks.
     */
    public function test_configuration_evidence_reuse_plan_api_returns_payload(): void
    {
        $response = $this->getJson('/api/practice/configuration-evidence-reuse-plan');

        $response
            ->assertOk()
            ->assertJsonPath('data.archive_id', 'configuration-contract-promote-100')
            ->assertJsonPath('data.reuse_tasks.0.audience', 'Portfolio note')
            ->assertJsonPath('data.reuse_tasks.2.source_key', 'INC-CONFIG-001 POSTMORTEM-CONFIG-001')
            ->assertJsonPath('data.quality_gate.status', 'ready')
            ->assertJsonStructure([
                'data' => [
                    'title',
                    'summary',
                    'archive_id',
                    'reuse_tasks',
                    'quality_checks',
                    'handoff_notes',
                    'commands',
                    'quality_gate',
                ],
            ]);
    }
}
