<?php

declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;

final class ConfigurationOperationsRunbookTest extends TestCase
{
    /**
     * The configuration operations runbook page renders operational sections.
     */
    public function test_configuration_operations_runbook_page_renders_sections(): void
    {
        $response = $this->get('/practice/configuration-operations-runbook');

        $response
            ->assertOk()
            ->assertSee('Configuration Operations Runbook')
            ->assertSee('RUNBOOK-CONFIG-001')
            ->assertSee('Diagnostics')
            ->assertSee('Rollback')
            ->assertSee('Open runbook API');
    }

    /**
     * The configuration operations runbook API returns diagnostics and handoff data.
     */
    public function test_configuration_operations_runbook_api_returns_payload(): void
    {
        $response = $this->getJson('/api/practice/configuration-operations-runbook');

        $response
            ->assertOk()
            ->assertJsonPath('data.runbook_id', 'RUNBOOK-CONFIG-001')
            ->assertJsonPath('data.decision_record.record_id', 'ADR-CONFIG-001')
            ->assertJsonPath('data.diagnostics.0.command', 'php artisan test --filter ConfigurationDecisionRecordTest')
            ->assertJsonPath('data.assessment.score', 100)
            ->assertJsonStructure([
                'data' => [
                    'title',
                    'runbook_id',
                    'summary',
                    'decision_record',
                    'triggers',
                    'diagnostics',
                    'rollback',
                    'handoff',
                    'evidence',
                    'commands',
                    'assessment',
                ],
            ]);
    }
}
