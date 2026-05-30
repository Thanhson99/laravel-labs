<?php

declare(strict_types=1);

namespace Tests\Unit\Practice;

use App\Services\Practice\LlmDecisionLoopPlanService;
use PHPUnit\Framework\TestCase;

final class LlmDecisionLoopPlanServiceTest extends TestCase
{
    /**
     * Tool-backed coding assistants include Markov, reward, PPO, and environment loops.
     */
    public function test_coding_assistant_plan_explains_decision_loop(): void
    {
        $plan = (new LlmDecisionLoopPlanService)->plan([
            'model_role' => 'coding-assistant',
            'training_stage' => 'rlhf',
            'feedback_signal' => 'tests-runtime',
            'tool_use' => 'yes',
            'agi_claim' => 'conservative',
        ]);

        $this->assertSame('state', $plan['markov_model'][0]['name']);
        $this->assertSame('policy', $plan['markov_model'][1]['name']);
        $this->assertSame('action', $plan['markov_model'][2]['name']);
        $this->assertStringContainsString('tool call', $plan['markov_model'][2]['llm_mapping']);
        $this->assertSame('reward', $plan['markov_model'][4]['name']);
        $this->assertSame('tests-runtime', $plan['reward_policy']['signal']);
        $this->assertSame('environment interaction', $plan['feedback_loop'][2]['loop']);
        $this->assertStringContainsString('PPO updates the model policy', $plan['ppo_plan']['mechanism']);
        $this->assertContains('Attention lets the model weigh relationships between tokens instead of reading the prompt as a flat string.', $plan['attention_model']);
        $this->assertSame('LLMs are just autocomplete.', $plan['misconception_checks'][0]['misconception']);
        $this->assertSame('php artisan test --filter LlmDecisionLoopPlan', $plan['commands'][1]);
    }

    /**
     * Pretraining-only explanations stay narrow and do not invent feedback loops.
     */
    public function test_pretraining_only_plan_limits_training_stack(): void
    {
        $plan = (new LlmDecisionLoopPlanService)->plan([
            'model_role' => 'chat-assistant',
            'training_stage' => 'pretraining',
            'feedback_signal' => 'none',
            'tool_use' => 'no',
            'agi_claim' => 'conservative',
        ]);

        $this->assertCount(1, $plan['training_stack']);
        $this->assertSame('pretraining', $plan['training_stack'][0]['stage']);
        $this->assertSame('none', $plan['reward_policy']['signal']);
        $this->assertCount(2, $plan['feedback_loop']);
        $this->assertStringContainsString('no external environment feedback', $plan['verification_plan'][2]['verify_with']);
    }

    /**
     * Overstated AGI claims are converted into measurable criteria.
     */
    public function test_overstated_agi_claims_are_guarded(): void
    {
        $plan = (new LlmDecisionLoopPlanService)->plan([
            'model_role' => 'tool-agent',
            'training_stage' => 'rlhf',
            'feedback_signal' => 'environment-reward',
            'tool_use' => 'yes',
            'agi_claim' => 'overstated',
        ]);

        $this->assertSame(
            'Do not claim AGI from fluency alone; require evidence of generalization, autonomy, reliability, and environment-grounded learning.',
            $plan['summary']['agi_note']
        );
        $this->assertContains(
            'Rewrite AGI claims as hypotheses and require benchmarks, reliability evidence, and safety evaluation.',
            $plan['agi_guardrails']
        );
        $this->assertSame('environment-reward', $plan['reward_policy']['signal']);
    }
}
