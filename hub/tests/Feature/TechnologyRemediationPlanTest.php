<?php

declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;

final class TechnologyRemediationPlanTest extends TestCase
{
    /**
     * The remediation plan page renders repair tasks from the skill rubric.
     */
    public function test_technology_remediation_plan_page_renders_repair_tasks(): void
    {
        $response = $this->get('/practice/technology-remediation-plan/api-validation?family=laravel&language=en&search=api&limit=3');

        $response
            ->assertOk()
            ->assertSee('Technology Remediation Plan: api-validation')
            ->assertSee('Repair Source understanding')
            ->assertSee('Repair Laravel layer placement')
            ->assertSee('Verification Commands')
            ->assertSee('Open remediation API');
    }

    /**
     * The remediation plan API returns tasks, focus files, commands, and progress payload.
     */
    public function test_technology_remediation_plan_api_returns_tasks_and_progress_payload(): void
    {
        $response = $this->getJson('/api/practice/technology-remediation-plan/api-validation?family=laravel&language=en&search=api&limit=3');

        $response
            ->assertOk()
            ->assertJsonPath('data.technology', 'api-validation')
            ->assertJsonPath('data.tasks.0.label', 'Repair Source understanding')
            ->assertJsonPath('data.tasks.0.focus_file', 'data/**/*.json and record workspace source panel')
            ->assertJsonPath('data.progress_payload.items.0.label', 'Repair Source understanding')
            ->assertJsonStructure([
                'data' => [
                    'title',
                    'technology',
                    'assessment',
                    'tasks' => [
                        '*' => [
                            'step',
                            'label',
                            'problem',
                            'action',
                            'evidence',
                            'focus_file',
                        ],
                    ],
                    'commands',
                    'next_routes',
                    'progress_payload',
                ],
            ]);
    }
}
