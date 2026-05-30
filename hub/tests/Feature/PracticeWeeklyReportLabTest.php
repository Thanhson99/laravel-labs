<?php

declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;

final class PracticeWeeklyReportLabTest extends TestCase
{
    /**
     * The weekly report page renders daily outputs and evidence checklist.
     */
    public function test_weekly_report_lab_page_renders_report_sections(): void
    {
        $response = $this->get('/practice/weekly-report-lab?family=laravel&language=en&search=api&phase_limit=2&tasks_per_phase=2&days=3');

        $response
            ->assertOk()
            ->assertSee('Weekly report lab summarizes the rotation into a progress report.')
            ->assertSee('Daily Outputs')
            ->assertSee('Evidence Checklist')
            ->assertSee('Weekly Report Progress Payload');
    }

    /**
     * The weekly report API returns daily outputs and next week plan.
     */
    public function test_weekly_report_lab_api_returns_report_payload(): void
    {
        $response = $this->getJson('/api/practice/weekly-report-lab?family=laravel&language=en&search=api&phase_limit=2&tasks_per_phase=2&days=3');

        $response
            ->assertOk()
            ->assertJsonPath('data.rotation.day_count', 3)
            ->assertJsonPath('data.technology_coverage.0', 'api-validation')
            ->assertJsonPath('data.daily_outputs.0.evidence_to_attach', 'Workspace URL, focused test command, and changed files.')
            ->assertJsonStructure([
                'data' => [
                    'title',
                    'rotation',
                    'technology_coverage',
                    'daily_outputs' => [
                        '*' => [
                            'day',
                            'technology',
                            'focus',
                            'output',
                            'evidence_to_attach',
                        ],
                    ],
                    'evidence_checklist',
                    'blockers_prompt',
                    'next_week_plan',
                    'progress_payload' => [
                        'items',
                    ],
                ],
            ]);
    }

    /**
     * Predictive AI weekly reports attach AI type evidence.
     */
    public function test_predictive_generative_ai_weekly_report_uses_ai_type_evidence(): void
    {
        $this->getJson('/api/practice/weekly-report-lab?family=vibe-coding&language=en&search=Predictive%20AI&phase_limit=1&tasks_per_phase=1&days=3')
            ->assertOk()
            ->assertJsonPath('data.technology_coverage.0', 'llm-foundations')
            ->assertJsonPath('data.daily_outputs.0.evidence_to_attach', 'AI type comparison table, metric choices, and source record.')
            ->assertJsonPath('data.daily_outputs.1.evidence_to_attach', 'Checkpoint output-contract, metric, and failure-mode evidence.')
            ->assertJsonPath('data.evidence_checklist.0', 'AI type comparison table for each AI-focused coding day.')
            ->assertJsonPath('data.blockers_prompt.0', 'Which source record was hardest to classify as prediction, generation, or both?');
    }

    /**
     * JavaScript closure weekly reports attach closure-specific evidence.
     */
    public function test_javascript_closure_weekly_report_uses_scope_evidence(): void
    {
        $this->getJson('/api/practice/weekly-report-lab?family=laravel&language=en&search=JavaScript%20closure&phase_limit=1&tasks_per_phase=1&days=3')
            ->assertOk()
            ->assertJsonPath('data.technology_coverage.0', 'javascript-closures')
            ->assertJsonPath('data.daily_outputs.0.evidence_to_attach', 'Closure lexical-scope trace, createCounter() output, and source record.')
            ->assertJsonPath('data.daily_outputs.1.evidence_to_attach', 'Checkpoint captured-binding, var-versus-let, and stale-closure evidence.')
            ->assertJsonPath('data.evidence_checklist.0', 'Lexical-scope trace for each closure-focused coding day.')
            ->assertJsonPath('data.blockers_prompt.1', 'Which captured binding, var versus let, or stale-closure example was weakest?');
    }
}
