<?php

declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;

final class PracticeRefactorLabTest extends TestCase
{
    /**
     * The refactor page renders rules, tasks, and architecture checks.
     */
    public function test_refactor_lab_page_renders_refactor_sections(): void
    {
        $response = $this->get('/practice/refactor-lab?family=laravel&language=en&search=api&phase_limit=2&tasks_per_phase=2&days=3');

        $response
            ->assertOk()
            ->assertSee('Refactor lab turns bug fixes into tested code improvement work.')
            ->assertSee('Refactor Rules')
            ->assertSee('Refactor Tasks')
            ->assertSee('Architecture Checks')
            ->assertSee('Refactor Progress Payload');
    }

    /**
     * The refactor API returns tasks generated from bug-fix drills.
     */
    public function test_refactor_lab_api_returns_refactor_payload(): void
    {
        $response = $this->getJson('/api/practice/refactor-lab?family=laravel&language=en&search=api&phase_limit=2&tasks_per_phase=2&days=3');

        $response
            ->assertOk()
            ->assertJsonPath('data.source_bug_fix_lab.drill_count', 3)
            ->assertJsonPath('data.source_bug_fix_lab.technology_coverage.0', 'api-validation')
            ->assertJsonPath('data.refactor_tasks.0.target_file', 'tests/Feature/*Test.php')
            ->assertJsonPath('data.refactor_tasks.0.guardrails.0', 'Assert public behavior and JSON shape.')
            ->assertJsonStructure([
                'data' => [
                    'title',
                    'source_bug_fix_lab',
                    'refactor_rules',
                    'refactor_tasks' => [
                        '*' => [
                            'round',
                            'technology_segment',
                            'refactor_goal',
                            'target_file',
                            'safe_steps',
                            'guardrails',
                            'verification_command',
                            'evidence',
                        ],
                    ],
                    'architecture_checks',
                    'progress_payload' => [
                        'items',
                    ],
                ],
            ]);
    }
}
