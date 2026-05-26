<?php

declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;

final class ConfigurationArchiveRefreshPlanTest extends TestCase
{
    /**
     * The configuration archive refresh plan page renders refresh guidance.
     */
    public function test_configuration_archive_refresh_plan_page_renders_guidance(): void
    {
        $response = $this->get('/practice/configuration-archive-refresh-plan');

        $response
            ->assertOk()
            ->assertSee('Configuration Archive Refresh Plan')
            ->assertSee('scheduled: configuration-contract-promote-100-session-debrief')
            ->assertSee('Refresh Tasks')
            ->assertSee('Remediation Triggers')
            ->assertSee('Open refresh API');
    }

    /**
     * The configuration archive refresh plan API returns refresh tasks.
     */
    public function test_configuration_archive_refresh_plan_api_returns_payload(): void
    {
        $response = $this->getJson('/api/practice/configuration-archive-refresh-plan');

        $response
            ->assertOk()
            ->assertJsonPath('data.archive_id', 'configuration-contract-promote-100-session-debrief')
            ->assertJsonPath('data.refresh_status', 'scheduled')
            ->assertJsonPath('data.refresh_tasks.0.current_status', 'captured')
            ->assertJsonPath('data.quality_gate.status', 'ready')
            ->assertJsonStructure([
                'data' => [
                    'title',
                    'summary',
                    'archive_id',
                    'refresh_status',
                    'refresh_triggers',
                    'refresh_tasks',
                    'rerun_commands',
                    'remediation_triggers',
                    'commands',
                    'quality_gate',
                ],
            ]);
    }
}
