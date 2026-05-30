<?php

declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;

final class ConfigurationMaintenanceRoadmapTest extends TestCase
{
    /**
     * The configuration maintenance roadmap page renders cadence and escalation.
     */
    public function test_configuration_maintenance_roadmap_page_renders_sections(): void
    {
        $response = $this->get('/practice/configuration-maintenance-roadmap');

        $response
            ->assertOk()
            ->assertSee('Configuration Maintenance Roadmap')
            ->assertSee('active: configuration-contract-promote-100-session-debrief')
            ->assertSee('Cadence')
            ->assertSee('Escalation Paths')
            ->assertSee('Open roadmap API');
    }

    /**
     * The configuration maintenance roadmap API returns cadence and health signals.
     */
    public function test_configuration_maintenance_roadmap_api_returns_payload(): void
    {
        $response = $this->getJson('/api/practice/configuration-maintenance-roadmap');

        $response
            ->assertOk()
            ->assertJsonPath('data.archive_id', 'configuration-contract-promote-100-session-debrief')
            ->assertJsonPath('data.roadmap_status', 'active')
            ->assertJsonPath('data.cadence.0.window', 'Weekly')
            ->assertJsonPath('data.cadence.1.window', 'Before each release')
            ->assertJsonPath('data.owners.Security reviewer', 'Confirms release blockers, owners, rollback actions, and smoke evidence are still valid.')
            ->assertJsonPath('data.health_signals.1', 'Security Misconfiguration release blockers still map to owners, rollback actions, and fail-closed smoke checks.')
            ->assertJsonPath('data.quality_gate.status', 'ready')
            ->assertJsonStructure([
                'data' => [
                    'title',
                    'summary',
                    'archive_id',
                    'roadmap_status',
                    'cadence',
                    'owners',
                    'health_signals',
                    'escalation_paths',
                    'commands',
                    'quality_gate',
                ],
            ]);
    }
}
