<?php

declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;

final class ConfigurationIncidentPostmortemTest extends TestCase
{
    /**
     * The configuration incident postmortem page renders learning sections.
     */
    public function test_configuration_incident_postmortem_page_renders_sections(): void
    {
        $response = $this->get('/practice/configuration-incident-postmortem');

        $response
            ->assertOk()
            ->assertSee('Configuration Incident Postmortem')
            ->assertSee('POSTMORTEM-CONFIG-001')
            ->assertSee('Root Cause')
            ->assertSee('Action Items')
            ->assertSee('Open postmortem API');
    }

    /**
     * The configuration incident postmortem API returns action items and review inputs.
     */
    public function test_configuration_incident_postmortem_api_returns_payload(): void
    {
        $response = $this->getJson('/api/practice/configuration-incident-postmortem');

        $response
            ->assertOk()
            ->assertJsonPath('data.postmortem_id', 'POSTMORTEM-CONFIG-001')
            ->assertJsonPath('data.incident.incident_id', 'INC-CONFIG-001')
            ->assertJsonPath('data.action_items.0.owner', 'Configuration learner')
            ->assertJsonPath('data.assessment.score', 100)
            ->assertJsonStructure([
                'data' => [
                    'title',
                    'postmortem_id',
                    'summary',
                    'incident',
                    'impact',
                    'root_cause',
                    'action_items',
                    'review_prompts',
                    'spaced_review_inputs',
                    'evidence',
                    'commands',
                    'handoff',
                    'assessment',
                ],
            ]);
    }
}
