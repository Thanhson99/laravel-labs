<?php

declare(strict_types=1);

namespace Tests\Unit\Practice;

use App\Services\Practice\AiAgentMemoryPlanService;
use PHPUnit\Framework\TestCase;

final class AiAgentMemoryPlanServiceTest extends TestCase
{
    /**
     * Developer-agent plans separate memory contracts and strict governance.
     */
    public function test_developer_agent_plan_separates_memory_contracts(): void
    {
        $plan = (new AiAgentMemoryPlanService)->plan([
            'agent_profile' => 'developer-agent',
            'storage_scope' => 'project-scoped',
            'retention_policy' => 'reviewed-durable',
            'privacy_mode' => 'strict',
            'staleness_policy' => 'block-stale',
        ]);

        $this->assertSame('working_memory', $plan['memory_contracts'][0]['name']);
        $this->assertSame('episodic_memory', $plan['memory_contracts'][1]['name']);
        $this->assertSame('semantic_memory', $plan['memory_contracts'][2]['name']);
        $this->assertSame('procedural_memory', $plan['memory_contracts'][3]['name']);
        $this->assertStringContainsString('source path, freshness, confidence', $plan['memory_contracts'][2]['safety_check']);
        $this->assertContains('Redact secrets, personal data, customer data, and private session notes before storage.', $plan['governance_controls']);
        $this->assertContains('Require human or test-backed review before promoting memory into durable storage.', $plan['governance_controls']);
        $this->assertSame('php artisan test --filter AiAgentMemoryPlan', $plan['commands'][1]);
    }

    /**
     * Session-only memories stay bounded to the active session.
     */
    public function test_session_only_memory_rejects_cross_project_reuse(): void
    {
        $plan = (new AiAgentMemoryPlanService)->plan([
            'agent_profile' => 'research-agent',
            'storage_scope' => 'session-only',
            'retention_policy' => 'short-lived',
            'privacy_mode' => 'strict',
            'staleness_policy' => 'refresh-before-use',
        ]);

        $this->assertStringContainsString('current session transcript', $plan['memory_contracts'][1]['retrieval_rule']);
        $this->assertContains('Refresh stale semantic facts from source files or tests before using them in an action.', $plan['retrieval_order']);
        $this->assertStringContainsString('refresh-before-use stale handling', $plan['interview_answer']);
    }

    /**
     * Warn-on-stale mode lowers confidence instead of blocking immediately.
     */
    public function test_warn_on_stale_mode_returns_warning_guardrail(): void
    {
        $plan = (new AiAgentMemoryPlanService)->plan([
            'agent_profile' => 'support-agent',
            'storage_scope' => 'user-scoped',
            'retention_policy' => 'rolling-window',
            'privacy_mode' => 'balanced',
            'staleness_policy' => 'warn-on-stale',
        ]);

        $this->assertStringContainsString('warning and lower confidence', $plan['memory_contracts'][2]['retrieval_rule']);
        $this->assertSame('stale_semantic_memory', $plan['failure_modes'][0]['risk']);
        $this->assertStringContainsString('Warn, refresh, or lower confidence', $plan['failure_modes'][0]['guardrail']);
    }
}
