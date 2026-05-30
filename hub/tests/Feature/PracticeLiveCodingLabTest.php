<?php

declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;

final class PracticeLiveCodingLabTest extends TestCase
{
    /**
     * The live coding page renders timed rounds and scorecard sections.
     */
    public function test_live_coding_lab_page_renders_session_sections(): void
    {
        $response = $this->get('/practice/live-coding-lab?family=laravel&language=en&search=api&phase_limit=2&tasks_per_phase=2&days=3');

        $response
            ->assertOk()
            ->assertSee('Live coding lab turns a demo script into a timeboxed practice session.')
            ->assertSee('Session Rules')
            ->assertSee('Live Coding Rounds')
            ->assertSee('Scorecard')
            ->assertSee('Failure Recovery')
            ->assertSee('Live Coding Progress Payload');
    }

    /**
     * The live coding API returns timed rounds generated from demo script steps.
     */
    public function test_live_coding_lab_api_returns_session_payload(): void
    {
        $response = $this->getJson('/api/practice/live-coding-lab?family=laravel&language=en&search=api&phase_limit=2&tasks_per_phase=2&days=3');

        $response
            ->assertOk()
            ->assertJsonPath('data.demo_script.day_count', 3)
            ->assertJsonPath('data.demo_script.technology_coverage.0', 'api-validation')
            ->assertJsonPath('data.rounds.0.timebox_minutes', 15)
            ->assertJsonPath('data.rounds.0.verification_command', 'php artisan test --filter PracticeLiveCodingLabTest && vendor/bin/pint --test')
            ->assertJsonStructure([
                'data' => [
                    'title',
                    'demo_script',
                    'session_rules',
                    'rounds' => [
                        '*' => [
                            'round',
                            'timebox_minutes',
                            'technology_segment',
                            'coding_prompt',
                            'narration_prompt',
                            'verification_command',
                            'pass_signal',
                            'evidence',
                        ],
                    ],
                    'scorecard',
                    'failure_recovery',
                    'progress_payload' => [
                        'items',
                    ],
                ],
            ]);
    }

    /**
     * JavaScript closure live coding uses closure-specific rules, scorecard, and recovery.
     */
    public function test_javascript_closure_live_coding_uses_scope_scorecard(): void
    {
        $response = $this->getJson('/api/practice/live-coding-lab?family=laravel&language=en&search=JavaScript%20closure&phase_limit=1&tasks_per_phase=1&days=3');

        $response
            ->assertOk()
            ->assertJsonPath('data.demo_script.technology_coverage.0', 'javascript-closures')
            ->assertJsonPath('data.session_rules.1', 'Say the lexical scope and captured binding before editing evidence.')
            ->assertJsonPath('data.scorecard.1.label', 'Named lexical scope and captured binding')
            ->assertJsonPath('data.failure_recovery.1', 'If the interview checklist is vague, add one var-versus-let or stale-closure example.')
            ->assertJsonPath('data.rounds.0.verification_command', 'php artisan test --filter PracticeDemoScriptLabTest --filter=closure');
    }

    /**
     * IDOR live coding uses object-authorization rules, scorecard, and recovery.
     */
    public function test_idor_live_coding_uses_object_authorization_scorecard(): void
    {
        $response = $this->getJson('/api/practice/live-coding-lab?family=laravel&language=en&search=IDOR&phase_limit=1&tasks_per_phase=1&days=3');

        $response
            ->assertOk()
            ->assertJsonPath('data.demo_script.technology_coverage.0', 'idor-access-control')
            ->assertJsonPath('data.session_rules.1', 'Say the object id, owner or tenant scope, and action before editing evidence.')
            ->assertJsonPath('data.scorecard.1.label', 'Named object id, owner or tenant scope, and protected action')
            ->assertJsonPath('data.failure_recovery.1', 'If the scoped lookup is vague, rewrite it through the current user, tenant, organization, team, or parent resource.')
            ->assertJsonPath('data.rounds.0.verification_command', 'php artisan test --filter PracticeDemoScriptLabTest --filter=idor');
    }
}
