<?php

declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;

final class QualityGateWorkbenchTest extends TestCase
{
    /**
     * The quality-gate workbench renders the testing-quality loop.
     */
    public function test_quality_gate_workbench_renders(): void
    {
        $response = $this->get('/workbench/quality-gate');

        $response
            ->assertOk()
            ->assertSee('Quality Gate Workbench')
            ->assertSee('POST /api/practice/quality-gate')
            ->assertSee('PracticeQualityGateService')
            ->assertSee('Evaluate quality gate');
    }

    /**
     * The quality-gate API marks verified practice work as ready.
     */
    public function test_quality_gate_api_marks_verified_work_ready(): void
    {
        $response = $this->postJson('/api/practice/quality-gate', [
            'tests' => 10,
            'assertions' => 25,
            'failures' => 0,
            'pint' => true,
        ]);

        $response
            ->assertOk()
            ->assertJsonPath('data.status', 'ready')
            ->assertJsonPath('data.passed', true)
            ->assertJsonPath('data.checks.tests_exist', true)
            ->assertJsonPath('data.checks.style_passes', true);
    }

    /**
     * The configured testing exercise links to the quality-gate workbench.
     */
    public function test_testing_quality_exercise_links_to_quality_gate_workbench(): void
    {
        $response = $this->get('/practice/feature-test-route-behavior');

        $response
            ->assertOk()
            ->assertSee('Write feature tests for a practice route')
            ->assertSee('Run the quality-gate workbench')
            ->assertSee('/workbench/quality-gate');
    }
}
