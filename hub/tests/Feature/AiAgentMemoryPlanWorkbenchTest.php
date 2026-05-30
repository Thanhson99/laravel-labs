<?php

declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;

final class AiAgentMemoryPlanWorkbenchTest extends TestCase
{
    /**
     * The AI agent memory workbench renders the runnable form.
     */
    public function test_ai_agent_memory_plan_workbench_renders(): void
    {
        $response = $this->get('/workbench/ai-agent-memory-plan');

        $response
            ->assertOk()
            ->assertSee('AI Agent Memory Plan Workbench')
            ->assertSee('POST /api/practice/ai-agent-memory-plan')
            ->assertSee('AiAgentMemoryPlanService')
            ->assertSee('Developer agent, strict memory')
            ->assertSee('Plan agent memory');
    }

    /**
     * Strict developer-agent payloads return governed memory contracts.
     */
    public function test_ai_agent_memory_plan_api_returns_memory_contracts(): void
    {
        $response = $this->postJson('/api/practice/ai-agent-memory-plan', [
            'agent_profile' => 'developer-agent',
            'storage_scope' => 'project-scoped',
            'retention_policy' => 'reviewed-durable',
            'privacy_mode' => 'strict',
            'staleness_policy' => 'block-stale',
        ]);

        $response
            ->assertOk()
            ->assertJsonPath('data.memory_contracts.0.name', 'working_memory')
            ->assertJsonPath('data.memory_contracts.1.name', 'episodic_memory')
            ->assertJsonPath('data.memory_contracts.2.name', 'semantic_memory')
            ->assertJsonPath('data.memory_contracts.3.name', 'procedural_memory')
            ->assertJsonPath('data.failure_modes.0.risk', 'stale_semantic_memory')
            ->assertJsonPath('data.commands.1', 'php artisan test --filter AiAgentMemoryPlan');
    }

    /**
     * Session-only payloads keep episodic memory bounded to the current session.
     */
    public function test_ai_agent_memory_plan_api_handles_session_only_scope(): void
    {
        $response = $this->postJson('/api/practice/ai-agent-memory-plan', [
            'agent_profile' => 'research-agent',
            'storage_scope' => 'session-only',
            'retention_policy' => 'short-lived',
            'privacy_mode' => 'strict',
            'staleness_policy' => 'refresh-before-use',
        ]);

        $response
            ->assertOk()
            ->assertJsonPath('data.memory_contracts.1.retrieval_rule', 'Retrieve only from the current session transcript and do not reuse across projects.')
            ->assertJsonPath('data.retrieval_order.4', 'Refresh stale semantic facts from source files or tests before using them in an action.');
    }

    /**
     * Invalid memory planner payloads return validation errors.
     */
    public function test_ai_agent_memory_plan_api_validates_payload(): void
    {
        $response = $this->postJson('/api/practice/ai-agent-memory-plan', [
            'agent_profile' => 'magic-agent',
            'storage_scope' => 'global',
            'retention_policy' => 'forever',
            'privacy_mode' => 'none',
            'staleness_policy' => 'trust-all',
        ]);

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors([
                'agent_profile',
                'storage_scope',
                'retention_policy',
                'privacy_mode',
                'staleness_policy',
            ]);
    }
}
