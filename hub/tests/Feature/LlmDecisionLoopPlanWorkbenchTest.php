<?php

declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;

final class LlmDecisionLoopPlanWorkbenchTest extends TestCase
{
    /**
     * The LLM decision-loop workbench renders the runnable form.
     */
    public function test_llm_decision_loop_plan_workbench_renders(): void
    {
        $response = $this->get('/workbench/llm-decision-loop-plan');

        $response
            ->assertOk()
            ->assertSee('LLM Decision Loop Plan Workbench')
            ->assertSee('POST /api/practice/llm-decision-loop-plan')
            ->assertSee('LlmDecisionLoopPlanService')
            ->assertSee('Coding agent with tests')
            ->assertSee('Plan LLM decision loop');
    }

    /**
     * A coding assistant with runtime feedback returns the full decision-loop model.
     */
    public function test_llm_decision_loop_plan_api_returns_decision_loop(): void
    {
        $response = $this->postJson('/api/practice/llm-decision-loop-plan', [
            'model_role' => 'coding-assistant',
            'training_stage' => 'rlhf',
            'feedback_signal' => 'tests-runtime',
            'tool_use' => 'yes',
            'agi_claim' => 'conservative',
        ]);

        $response
            ->assertOk()
            ->assertJsonPath('data.markov_model.0.name', 'state')
            ->assertJsonPath('data.markov_model.2.name', 'action')
            ->assertJsonPath('data.markov_model.4.name', 'reward')
            ->assertJsonPath('data.reward_policy.signal', 'tests-runtime')
            ->assertJsonPath('data.ppo_plan.mechanism', 'PPO updates the model policy while constraining changes so the model improves toward reward without drifting too far in one step.')
            ->assertJsonPath('data.feedback_loop.2.loop', 'environment interaction')
            ->assertJsonPath('data.misconception_checks.0.misconception', 'LLMs are just autocomplete.')
            ->assertJsonPath('data.commands.1', 'php artisan test --filter LlmDecisionLoopPlan');
    }

    /**
     * Chat-only explanations stay honest about missing feedback.
     */
    public function test_llm_decision_loop_plan_api_handles_no_feedback_chat(): void
    {
        $response = $this->postJson('/api/practice/llm-decision-loop-plan', [
            'model_role' => 'chat-assistant',
            'training_stage' => 'sft',
            'feedback_signal' => 'none',
            'tool_use' => 'no',
            'agi_claim' => 'conservative',
        ]);

        $response
            ->assertOk()
            ->assertJsonPath('data.reward_policy.signal', 'none')
            ->assertJsonCount(2, 'data.training_stack')
            ->assertJsonCount(2, 'data.feedback_loop')
            ->assertJsonPath('data.verification_plan.2.verify_with', 'State that no external environment feedback is available in this scenario.');
    }

    /**
     * Overstated AGI claims are downgraded into verification language.
     */
    public function test_llm_decision_loop_plan_api_guards_overstated_agi_claims(): void
    {
        $response = $this->postJson('/api/practice/llm-decision-loop-plan', [
            'model_role' => 'tool-agent',
            'training_stage' => 'rlhf',
            'feedback_signal' => 'environment-reward',
            'tool_use' => 'yes',
            'agi_claim' => 'overstated',
        ]);

        $response
            ->assertOk()
            ->assertJsonPath('data.summary.agi_note', 'Do not claim AGI from fluency alone; require evidence of generalization, autonomy, reliability, and environment-grounded learning.')
            ->assertJsonPath('data.agi_guardrails.3', 'Rewrite AGI claims as hypotheses and require benchmarks, reliability evidence, and safety evaluation.');
    }

    /**
     * Invalid decision-loop payloads return validation errors.
     */
    public function test_llm_decision_loop_plan_api_validates_payload(): void
    {
        $response = $this->postJson('/api/practice/llm-decision-loop-plan', [
            'model_role' => 'oracle',
            'training_stage' => 'magic',
            'feedback_signal' => 'vibes',
            'tool_use' => 'maybe',
            'agi_claim' => 'certain',
        ]);

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors([
                'model_role',
                'training_stage',
                'feedback_signal',
                'tool_use',
                'agi_claim',
            ]);
    }
}
