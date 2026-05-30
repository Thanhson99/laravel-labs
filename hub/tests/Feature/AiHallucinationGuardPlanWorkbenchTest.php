<?php

declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;

final class AiHallucinationGuardPlanWorkbenchTest extends TestCase
{
    /**
     * The AI hallucination guard workbench renders the planning loop.
     */
    public function test_ai_hallucination_guard_plan_workbench_renders(): void
    {
        $response = $this->get('/workbench/ai-hallucination-guard-plan');

        $response
            ->assertOk()
            ->assertSee('AI Hallucination Guard Plan Workbench')
            ->assertSee('POST /api/practice/ai-hallucination-guard-plan')
            ->assertSee('AiHallucinationGuardPlanService')
            ->assertSee('Scenario preset')
            ->assertSee('Data answer without retrieval')
            ->assertSee('Plan hallucination guardrails');
    }

    /**
     * The API returns an evidence-first hallucination guard plan.
     */
    public function test_ai_hallucination_guard_plan_api_returns_plan(): void
    {
        $response = $this->postJson('/api/practice/ai-hallucination-guard-plan', [
            'ai_task' => 'code-generation',
            'risk_level' => 'high',
            'evidence_sources' => 'partial',
            'runtime_checks' => 'yes',
            'human_review' => 'yes',
        ]);

        $response
            ->assertOk()
            ->assertJsonPath('data.summary.decision', 'Use evidence-first AI workflow for code-generation work.')
            ->assertJsonPath('data.risk_score.level', 'high')
            ->assertJsonPath('data.score_breakdown.0.points', 60)
            ->assertJsonPath('data.decision_matrix.4.current_value', '70/high')
            ->assertJsonPath('data.acceptance_gate.decision', 'block until evidence and verification pass')
            ->assertJsonPath('data.release_readiness.status', 'blocked')
            ->assertJsonPath('data.evidence_requirements.1.claim_type', 'framework or package behavior')
            ->assertJsonPath('data.claim_validation_rules.1.claim', 'framework or package API is available')
            ->assertJsonPath('data.evidence_ledger_template.2.field', 'evidence_reference')
            ->assertJsonPath('data.required_artifacts.2.name', 'scope diff summary')
            ->assertJsonPath('data.ci_policy.mode', 'strict-blocking')
            ->assertJsonPath('data.data_safety_plan.redact_before_prompt.0', 'user IDs, emails, phone numbers, names, addresses, and account identifiers')
            ->assertJsonPath('data.prompt_examples.1.name', 'minimal patch request')
            ->assertJsonPath('data.verification_plan.0.stage', 'before patch')
            ->assertJsonPath('data.runtime_verification_commands.0.name', 'route surface check')
            ->assertJsonPath('data.observability_plan.log_events.1', 'ai_output_blocked with blocked_reason, missing_artifact, and claim_type')
            ->assertJsonPath('data.rollback_playbook.2.phase', 'repair')
            ->assertJsonPath('data.ownership_model.1.role', 'code reviewer')
            ->assertJsonPath('data.escalation_policy.2.escalate_to', 'senior reviewer')
            ->assertJsonPath('data.maturity_roadmap.2.level', 'automated')
            ->assertJsonPath('data.implementation_steps.2', 'Add a review checklist item that rejects invented APIs, version drift, unrelated files, and unverified test claims.')
            ->assertJsonPath('data.red_team_scenarios.1.planted_risk', 'Confident root-cause story without evidence.')
            ->assertJsonPath('data.automation_hooks.1.hook', 'CI route and test check')
            ->assertJsonPath('data.safe_output_contract.1.section', 'evidence map')
            ->assertJsonPath('data.commands.1', 'php artisan test --filter AiHallucinationGuardPlan');
    }

    /**
     * Data-answer work without evidence exposes factual hallucination vectors.
     */
    public function test_ai_hallucination_guard_plan_api_handles_data_answers(): void
    {
        $response = $this->postJson('/api/practice/ai-hallucination-guard-plan', [
            'ai_task' => 'data-answer',
            'risk_level' => 'high',
            'evidence_sources' => 'none',
            'runtime_checks' => 'no',
            'human_review' => 'yes',
        ]);

        $response
            ->assertOk()
            ->assertJsonPath('data.risk_score.level', 'high')
            ->assertJsonPath(
                'data.hallucination_vectors.3',
                'Generated facts or datasets that are not backed by retrieval, database rows, or source citations.'
            );
    }

    /**
     * Invalid hallucination guard payloads return validation errors.
     */
    public function test_ai_hallucination_guard_plan_api_validates_payload(): void
    {
        $response = $this->postJson('/api/practice/ai-hallucination-guard-plan', [
            'ai_task' => 'invent',
            'risk_level' => 'critical',
            'evidence_sources' => 'guess',
            'runtime_checks' => 'maybe',
            'human_review' => 'maybe',
        ]);

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors([
                'ai_task',
                'risk_level',
                'evidence_sources',
                'runtime_checks',
                'human_review',
            ]);
    }
}
