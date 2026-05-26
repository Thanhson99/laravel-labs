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
}
