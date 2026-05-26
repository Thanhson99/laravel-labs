<?php

declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;

final class ConfigurationDecisionRecordTest extends TestCase
{
    /**
     * The configuration decision record page renders ADR-style sections.
     */
    public function test_configuration_decision_record_page_renders_sections(): void
    {
        $response = $this->get('/practice/configuration-decision-record');

        $response
            ->assertOk()
            ->assertSee('Configuration Decision Record')
            ->assertSee('ADR-CONFIG-001')
            ->assertSee('Context')
            ->assertSee('Alternatives')
            ->assertSee('Open decision API');
    }

    /**
     * The configuration decision record API returns the decision payload.
     */
    public function test_configuration_decision_record_api_returns_payload(): void
    {
        $response = $this->getJson('/api/practice/configuration-decision-record');

        $response
            ->assertOk()
            ->assertJsonPath('data.record_id', 'ADR-CONFIG-001')
            ->assertJsonPath('data.status', 'accepted')
            ->assertJsonPath('data.assessment.score', 100)
            ->assertJsonPath('data.alternatives.0.option', 'Controller-owned configuration checks')
            ->assertJsonStructure([
                'data' => [
                    'title',
                    'record_id',
                    'status',
                    'summary',
                    'context',
                    'decision',
                    'alternatives',
                    'consequences',
                    'evidence',
                    'commands',
                    'assessment',
                ],
            ]);
    }
}
