<?php

declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;

final class ConfigurationRemediationPlanTest extends TestCase
{
    /**
     * The configuration remediation plan page renders repair tasks.
     */
    public function test_configuration_remediation_plan_page_renders_tasks(): void
    {
        $response = $this->get('/practice/configuration-remediation-plan');

        $response
            ->assertOk()
            ->assertSee('Configuration Remediation Plan')
            ->assertSee('Restore auth contract assertions')
            ->assertSee('Protect the shared quality-gate response shape')
            ->assertSee('Completion Criteria')
            ->assertSee('Open remediation API');
    }

    /**
     * The configuration remediation plan API returns file-focused tasks.
     */
    public function test_configuration_remediation_plan_api_returns_tasks(): void
    {
        $response = $this->getJson('/api/practice/configuration-remediation-plan');

        $response
            ->assertOk()
            ->assertJsonPath('data.task_count', 4)
            ->assertJsonPath('data.tasks.1.risk_key', 'auth-contract-drift')
            ->assertJsonPath('data.tasks.2.target_files.0', 'hub/app/Services/Practice/PracticeQualityGateService.php')
            ->assertJsonPath('data.status.quality', 'ready')
            ->assertJsonStructure([
                'data' => [
                    'title',
                    'summary',
                    'status',
                    'risk_count',
                    'task_count',
                    'tasks' => [
                        '*' => [
                            'risk_key',
                            'severity',
                            'title',
                            'target_files',
                            'action',
                            'owner_route',
                            'verification',
                            'done_signal',
                        ],
                    ],
                    'completion_criteria',
                    'commands',
                ],
            ]);
    }
}
