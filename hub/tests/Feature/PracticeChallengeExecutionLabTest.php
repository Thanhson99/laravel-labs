<?php

declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;

final class PracticeChallengeExecutionLabTest extends TestCase
{
    /**
     * The challenge execution page renders executable challenge sections.
     */
    public function test_challenge_execution_lab_page_renders_execution_sections(): void
    {
        $response = $this->get('/practice/challenge-execution-lab?family=laravel&language=en&search=api&phase_limit=2&tasks_per_phase=2&days=3');

        $response
            ->assertOk()
            ->assertSee('Challenge execution turns next challenge recommendations into concrete coding steps.')
            ->assertSee('Execution Rules')
            ->assertSee('Execution Steps')
            ->assertSee('Challenge Execution Progress Payload');
    }

    /**
     * The challenge execution API returns executable steps from challenge cards.
     */
    public function test_challenge_execution_lab_api_returns_execution_payload(): void
    {
        $response = $this->getJson('/api/practice/challenge-execution-lab?family=laravel&language=en&search=api&phase_limit=2&tasks_per_phase=2&days=3');

        $response
            ->assertOk()
            ->assertJsonPath('data.source_challenge_lab.challenge_count', 3)
            ->assertJsonPath('data.session_summary.total_estimated_minutes', 135)
            ->assertJsonPath('data.execution_steps.0.route_name', 'practice.checkpoint-exam-lab')
            ->assertJsonPath('data.execution_steps.0.estimated_minutes', 45)
            ->assertJsonStructure([
                'data' => [
                    'title',
                    'source_challenge_lab',
                    'execution_rules',
                    'execution_steps' => [
                        '*' => [
                            'step',
                            'technology_segment',
                            'route_name',
                            'challenge_type',
                            'estimated_minutes',
                            'execution_order',
                            'verification_command',
                            'evidence_to_submit',
                            'exit_criteria',
                        ],
                    ],
                    'session_summary',
                    'progress_payload' => [
                        'items',
                    ],
                ],
            ]);
    }
}
