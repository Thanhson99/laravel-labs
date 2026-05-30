<?php

declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;

final class PracticeDemoScriptLabTest extends TestCase
{
    /**
     * The demo script page renders script steps and rehearsal guidance.
     */
    public function test_demo_script_lab_page_renders_script_sections(): void
    {
        $response = $this->get('/practice/demo-script-lab?family=laravel&language=en&search=api&phase_limit=2&tasks_per_phase=2&days=3');

        $response
            ->assertOk()
            ->assertSee('Demo script lab turns a weekly report into a practice presentation script.')
            ->assertSee('Opening Lines')
            ->assertSee('Demo Steps')
            ->assertSee('Rehearsal Checklist')
            ->assertSee('Demo Handoff Payload');
    }

    /**
     * The demo script API returns script steps generated from weekly report output.
     */
    public function test_demo_script_lab_api_returns_script_payload(): void
    {
        $response = $this->getJson('/api/practice/demo-script-lab?family=laravel&language=en&search=api&phase_limit=2&tasks_per_phase=2&days=3');

        $response
            ->assertOk()
            ->assertJsonPath('data.weekly_report.day_count', 3)
            ->assertJsonPath('data.weekly_report.technology_coverage.0', 'api-validation')
            ->assertJsonPath('data.script_steps.0.verify', 'Run the focused feature test and Pint for the changed files.')
            ->assertJsonStructure([
                'data' => [
                    'title',
                    'weekly_report',
                    'demo_goal',
                    'script_steps' => [
                        '*' => [
                            'segment',
                            'say',
                            'do',
                            'verify',
                            'evidence',
                        ],
                    ],
                    'opening_lines',
                    'closing_lines',
                    'rehearsal_checklist',
                    'handoff_payload' => [
                        'items',
                    ],
                ],
            ]);
    }

    /**
     * JavaScript closure demo scripts present scope traces and closure evidence.
     */
    public function test_javascript_closure_demo_script_uses_scope_presentation(): void
    {
        $response = $this->getJson('/api/practice/demo-script-lab?family=laravel&language=en&search=JavaScript%20closure&phase_limit=1&tasks_per_phase=1&days=3');

        $response
            ->assertOk()
            ->assertJsonPath('data.weekly_report.technology_coverage.0', 'javascript-closures')
            ->assertJsonPath('data.opening_lines.0', 'This demo starts from JSON-backed Laravel Labs content and ends in a JavaScript closure interview artifact.')
            ->assertJsonPath('data.rehearsal_checklist.2', 'Show the closure snippet or payload before showing the rendered result.');

        $this->assertStringContainsString('lexical-scope trace', $response->json('data.script_steps.0.do'));
        $this->assertStringContainsString('createCounter()', $response->json('data.script_steps.0.verify'));
    }

    /**
     * IDOR demo scripts present scoped lookup, policy, and denial evidence.
     */
    public function test_idor_demo_script_uses_object_authorization_presentation(): void
    {
        $response = $this->getJson('/api/practice/demo-script-lab?family=laravel&language=en&search=IDOR&phase_limit=1&tasks_per_phase=1&days=3');

        $response
            ->assertOk()
            ->assertJsonPath('data.weekly_report.technology_coverage.0', 'idor-access-control')
            ->assertJsonPath('data.opening_lines.0', 'This demo starts from JSON-backed Laravel Labs content and ends in an IDOR object-authorization artifact.')
            ->assertJsonPath('data.rehearsal_checklist.2', 'Show the vulnerable direct lookup before showing the scoped lookup and policy check.');

        $this->assertStringContainsString('protected object route', $response->json('data.script_steps.0.do'));
        $this->assertStringContainsString('cross-user denial evidence', $response->json('data.script_steps.0.verify'));
    }
}
