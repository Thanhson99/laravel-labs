<?php

declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;

final class AuthorizationPolicyPlanWorkbenchTest extends TestCase
{
    /**
     * The authorization policy workbench renders the access-control planning loop.
     */
    public function test_authorization_policy_plan_workbench_renders(): void
    {
        $response = $this->get('/workbench/authorization-policy-plan');

        $response
            ->assertOk()
            ->assertSee('Authorization Policy Plan Workbench')
            ->assertSee('POST /api/practice/authorization-policy-plan')
            ->assertSee('AuthorizationPolicyPlanService')
            ->assertSee('Plan policy');
    }

    /**
     * The authorization policy API returns policy, controller usage, and tests.
     */
    public function test_authorization_policy_plan_api_returns_plan(): void
    {
        $response = $this->postJson('/api/practice/authorization-policy-plan', [
            'model_name' => 'Practice Task',
            'ability' => 'update',
            'actor_role' => 'owner',
            'rule' => 'Only the owner can update an unfinished practice task.',
        ]);

        $response
            ->assertOk()
            ->assertJsonPath('data.policy.class', 'PracticeTaskPolicy')
            ->assertJsonPath('data.policy.method', 'update')
            ->assertJsonPath('data.policy.model', 'PracticeTask')
            ->assertJsonPath('data.decision_rule.actor_role', 'owner')
            ->assertJsonPath('data.tests.1', 'Forbidden actor receives HTTP 403.');
    }

    /**
     * Invalid authorization policy payloads return validation errors.
     */
    public function test_authorization_policy_plan_api_validates_payload(): void
    {
        $response = $this->postJson('/api/practice/authorization-policy-plan', [
            'model_name' => '<bad>',
            'ability' => 'x',
            'actor_role' => '',
            'rule' => 'short',
        ]);

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['model_name', 'ability', 'actor_role', 'rule']);
    }
}
