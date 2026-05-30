<?php

declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;

final class PracticeBugFixLabTest extends TestCase
{
    /**
     * The bug-fix page renders debugging rules and drill sections.
     */
    public function test_bug_fix_lab_page_renders_debugging_sections(): void
    {
        $response = $this->get('/practice/bug-fix-lab?family=laravel&language=en&search=api&phase_limit=2&tasks_per_phase=2&days=3');

        $response
            ->assertOk()
            ->assertSee('Bug fix lab turns live coding rounds into layer-aware debugging work.')
            ->assertSee('Debugging Rules')
            ->assertSee('Bug Drills')
            ->assertSee('Review Questions')
            ->assertSee('Bug Fix Progress Payload');
    }

    /**
     * The bug-fix API returns drills generated from live coding rounds.
     */
    public function test_bug_fix_lab_api_returns_drill_payload(): void
    {
        $response = $this->getJson('/api/practice/bug-fix-lab?family=laravel&language=en&search=api&phase_limit=2&tasks_per_phase=2&days=3');

        $response
            ->assertOk()
            ->assertJsonPath('data.source_session.round_count', 3)
            ->assertJsonPath('data.source_session.technology_coverage.0', 'api-validation')
            ->assertJsonPath('data.bug_drills.0.patch_target', 'tests/Feature/*Test.php')
            ->assertJsonPath('data.bug_drills.0.diagnosis_steps.0', 'Read the failing assertion or missing browser text.')
            ->assertJsonStructure([
                'data' => [
                    'title',
                    'source_session',
                    'debugging_rules',
                    'bug_drills' => [
                        '*' => [
                            'round',
                            'technology_segment',
                            'bug_report',
                            'diagnosis_steps',
                            'patch_target',
                            'verification_command',
                            'pass_signal',
                            'evidence',
                        ],
                    ],
                    'review_questions',
                    'progress_payload' => [
                        'items',
                    ],
                ],
            ]);
    }

    /**
     * Practice lab filters reject invalid numeric query values before service execution.
     */
    public function test_bug_fix_lab_api_validates_numeric_filters(): void
    {
        $response = $this->getJson('/api/practice/bug-fix-lab?limit=0&phase_limit=bad&days=31');

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors([
                'limit',
                'phase_limit',
                'days',
            ]);
    }

    /**
     * JavaScript closure bug-fix drills debug scope traces and interview outputs.
     */
    public function test_javascript_closure_bug_fix_lab_uses_scope_debugging(): void
    {
        $response = $this->getJson('/api/practice/bug-fix-lab?family=laravel&language=en&search=JavaScript%20closure&phase_limit=1&tasks_per_phase=1&days=3');

        $response
            ->assertOk()
            ->assertJsonPath('data.source_session.technology_coverage.0', 'javascript-closures')
            ->assertJsonPath('data.bug_drills.0.technology_segment', 'Day 1 demo: javascript-closures')
            ->assertJsonPath('data.bug_drills.0.diagnosis_steps.0', 'Read the failing closure assertion or missing interview output.')
            ->assertJsonPath('data.review_questions.0', 'Which closure binding or scope trace owned the bug?');

        $this->assertStringContainsString('closure practice round', $response->json('data.bug_drills.0.bug_report'));
        $this->assertSame('resources/js/closure-counter.js', $response->json('data.bug_drills.0.patch_target'));
    }
}
