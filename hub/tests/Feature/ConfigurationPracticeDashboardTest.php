<?php

declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;

final class ConfigurationPracticeDashboardTest extends TestCase
{
    /**
     * The configuration dashboard page renders status, next stage, and groups.
     */
    public function test_configuration_dashboard_page_renders_status_and_groups(): void
    {
        $response = $this->get('/practice/configuration-dashboard');

        $response
            ->assertOk()
            ->assertSee('Configuration Practice Dashboard')
            ->assertSee('Next recommended stage')
            ->assertSee('Assessment')
            ->assertSee('Build the contract')
            ->assertSee('Repair the risks')
            ->assertSee('Open dashboard API');
    }

    /**
     * The configuration dashboard API returns status, next stage, groups, and progress payload.
     */
    public function test_configuration_dashboard_api_returns_summary_payload(): void
    {
        $response = $this->getJson('/api/practice/configuration-dashboard');

        $response
            ->assertOk()
            ->assertJsonPath('data.status.quality', 'ready')
            ->assertJsonPath('data.status.stage_count', 28)
            ->assertJsonPath('data.next_stage.label', 'Assessment')
            ->assertJsonPath('data.stage_groups.1.name', 'Repair the risks')
            ->assertJsonPath('data.stage_groups.3.name', 'Prove mastery')
            ->assertJsonPath('data.archive.archive_id', 'configuration-contract-promote-100')
            ->assertJsonStructure([
                'data' => [
                    'title',
                    'summary',
                    'status',
                    'next_stage',
                    'stage_groups',
                    'archive',
                    'progress_payload',
                    'commands',
                ],
            ]);
    }
}
