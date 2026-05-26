<?php

declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;

final class ConfigurationIncidentDrillTest extends TestCase
{
    /**
     * The configuration incident drill page renders scenario and recovery sections.
     */
    public function test_configuration_incident_drill_page_renders_sections(): void
    {
        $response = $this->get('/practice/configuration-incident-drill');

        $response
            ->assertOk()
            ->assertSee('Configuration Incident Drill')
            ->assertSee('INC-CONFIG-001')
            ->assertSee('Scenario')
            ->assertSee('Diagnosis Steps')
            ->assertSee('Open incident API');
    }

    /**
     * The configuration incident drill API returns timeline and diagnosis data.
     */
    public function test_configuration_incident_drill_api_returns_payload(): void
    {
        $response = $this->getJson('/api/practice/configuration-incident-drill');

        $response
            ->assertOk()
            ->assertJsonPath('data.incident_id', 'INC-CONFIG-001')
            ->assertJsonPath('data.runbook.runbook_id', 'RUNBOOK-CONFIG-001')
            ->assertJsonPath('data.timeline.0.minute', 0)
            ->assertJsonPath('data.diagnosis_steps.0.command', 'php artisan test --filter ConfigurationDecisionRecordTest')
            ->assertJsonStructure([
                'data' => [
                    'title',
                    'incident_id',
                    'summary',
                    'scenario',
                    'runbook',
                    'timeline',
                    'diagnosis_steps',
                    'patch_plan',
                    'recovery_evidence',
                    'commands',
                    'handoff',
                    'assessment',
                ],
            ]);
    }
}
