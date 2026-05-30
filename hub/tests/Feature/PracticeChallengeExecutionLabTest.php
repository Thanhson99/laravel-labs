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

    /**
     * JavaScript closure challenge execution asks for closure evidence, not Laravel-layer evidence.
     */
    public function test_javascript_closure_challenge_execution_uses_scope_exit_criteria(): void
    {
        $response = $this->getJson('/api/practice/challenge-execution-lab?family=laravel&language=en&search=JavaScript%20closure&phase_limit=1&tasks_per_phase=1&days=3');

        $response
            ->assertOk()
            ->assertJsonPath('data.source_challenge_lab.technology_coverage.0', 'javascript-closures')
            ->assertJsonPath('data.execution_steps.0.technology_segment', 'Day 1 demo: javascript-closures')
            ->assertJsonPath('data.execution_steps.0.execution_order.2', 'Make the smallest closure evidence change or answer the required checkpoint.')
            ->assertJsonPath('data.execution_steps.0.exit_criteria.2', 'The learner can explain lexical scope and the captured binding involved.')
            ->assertJsonPath('data.execution_steps.0.exit_criteria.3', 'Risk note covers var versus let, stale closures, or accidental shared mutable state.');
    }

    /**
     * IDOR challenge execution asks for object-authorization evidence.
     */
    public function test_idor_challenge_execution_uses_object_authorization_exit_criteria(): void
    {
        $response = $this->getJson('/api/practice/challenge-execution-lab?family=laravel&language=en&search=IDOR&phase_limit=1&tasks_per_phase=1&days=3');

        $response
            ->assertOk()
            ->assertJsonPath('data.source_challenge_lab.technology_coverage.0', 'idor-access-control')
            ->assertJsonPath('data.execution_steps.0.technology_segment', 'Day 1 demo: idor-access-control')
            ->assertJsonPath('data.execution_steps.0.execution_order.2', 'Make the smallest object-authorization change: route inventory, scoped lookup, policy check, or denial test.')
            ->assertJsonPath('data.execution_steps.0.exit_criteria.2', 'The learner can explain why authentication alone does not authorize this object.')
            ->assertJsonPath('data.execution_steps.0.exit_criteria.3', 'Risk note covers direct lookup, missing policy, nested-resource leakage, downloads, exports, or 403 versus 404 behavior.');
    }
}
