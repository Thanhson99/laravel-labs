<?php

declare(strict_types=1);

namespace Tests\Unit\Practice;

use App\Services\Practice\AiHallucinationGuardPlanService;
use PHPUnit\Framework\TestCase;

final class AiHallucinationGuardPlanServiceTest extends TestCase
{
    /**
     * High-risk generation remains high risk when evidence is only partial.
     */
    public function test_high_risk_generation_requires_evidence_and_runtime_checks(): void
    {
        $plan = (new AiHallucinationGuardPlanService)->plan([
            'ai_task' => 'code-generation',
            'risk_level' => 'high',
            'evidence_sources' => 'partial',
            'runtime_checks' => 'yes',
            'human_review' => 'yes',
        ]);

        $this->assertSame('Use evidence-first AI workflow for code-generation work.', $plan['summary']['decision']);
        $this->assertSame('high', $plan['risk_score']['level']);
        $this->assertContains('Partial evidence leaves room for version drift and missing edge cases.', $plan['risk_score']['reasons']);
        $this->assertSame('task risk', $plan['score_breakdown'][0]['signal']);
        $this->assertSame(60, $plan['score_breakdown'][0]['points']);
        $this->assertSame('evidence sources', $plan['score_breakdown'][1]['signal']);
        $this->assertSame(10, $plan['score_breakdown'][1]['points']);
        $this->assertSame('Risk score', $plan['decision_matrix'][4]['signal']);
        $this->assertSame('70/high', $plan['decision_matrix'][4]['current_value']);
        $this->assertSame('blocked', $plan['release_readiness']['status']);
        $this->assertContains('Lower risk with stronger evidence or require strict senior review before release.', $plan['release_readiness']['required_before_release']);
        $this->assertSame('route, controller, service, or config exists', $plan['claim_validation_rules'][0]['claim']);
        $this->assertStringContainsString('route:list', $plan['claim_validation_rules'][0]['verify_with']);
        $this->assertSame('claim', $plan['evidence_ledger_template'][0]['field']);
        $this->assertSame('evidence_reference', $plan['evidence_ledger_template'][2]['field']);
        $this->assertSame('route surface check', $plan['runtime_verification_commands'][0]['name']);
        $this->assertSame('php artisan route:list --path=<touched-path>', $plan['runtime_verification_commands'][0]['command']);
        $this->assertSame('senior reviewer', $plan['escalation_policy'][2]['escalate_to']);
        $this->assertSame('assumptions and unknowns', $plan['safe_output_contract'][0]['section']);
        $this->assertSame('verification record', $plan['safe_output_contract'][3]['section']);
        $this->assertContains('Require the model to list assumptions before producing code.', $plan['guardrails']);
        $this->assertContains('Reject patches that introduce new package APIs without proving the dependency and version exist.', $plan['code_controls']);
        $this->assertSame('before patch', $plan['verification_plan'][0]['stage']);
        $this->assertSame('block until evidence and verification pass', $plan['acceptance_gate']['decision']);
        $this->assertContains('At least one runtime, test, route-list, static analysis, or smoke-check command has been run.', $plan['acceptance_gate']['required_before_accept']);
        $this->assertSame('repo structure', $plan['evidence_requirements'][0]['claim_type']);
        $this->assertContains('file path', $plan['evidence_requirements'][0]['acceptable_evidence']);
        $this->assertSame('evidence map', $plan['required_artifacts'][1]['name']);
        $this->assertSame('strict-blocking', $plan['ci_policy']['mode']);
        $this->assertContains('targeted-feature-test', $plan['ci_policy']['required_checks']);
        $this->assertContains('senior-review-required', $plan['ci_policy']['manual_gates']);
        $this->assertContains('minimal file paths, function names, route names, and sanitized snippets needed for the task', $plan['data_safety_plan']['allowed_context']);
        $this->assertContains('production secrets, API keys, tokens, passwords, private keys, and session cookies', $plan['data_safety_plan']['blocked_inputs']);
        $this->assertSame('preflight before code', $plan['prompt_examples'][0]['name']);
        $this->assertStringContainsString('Do not write code yet', $plan['prompt_examples'][0]['prompt']);
        $this->assertContains('ai_generated_change_error_total by route, feature, and exception class', $plan['observability_plan']['metrics']);
        $this->assertContains('Alert when verification gaps are recorded for high-risk tasks.', $plan['observability_plan']['alerts']);
        $this->assertSame('contain', $plan['rollback_playbook'][1]['phase']);
        $this->assertStringContainsString('Rollback or disable', $plan['rollback_playbook'][1]['action']);
        $this->assertSame('release owner', $plan['ownership_model'][3]['role']);
        $this->assertStringContainsString('rollback', $plan['ownership_model'][3]['owns']);
        $this->assertSame('operational', $plan['maturity_roadmap'][3]['level']);
        $this->assertSame('High-risk AI changes have monitoring and rollback evidence before release.', $plan['maturity_roadmap'][3]['exit_criteria']);
        $this->assertContains('Add a prompt preflight step that asks for assumptions, missing context, and required files before code generation.', $plan['implementation_steps']);
        $this->assertSame('Invented framework or package API.', $plan['red_team_scenarios'][0]['planted_risk']);
        $this->assertSame('pull request template', $plan['automation_hooks'][0]['hook']);
        $this->assertStringContainsString('evidence map', $plan['automation_hooks'][0]['example_check']);
        $this->assertStringContainsString('high-risk code-generation', $plan['interview_answer']);
    }

    /**
     * Data answers without retrieval are treated as high-risk factual output.
     */
    public function test_data_answer_without_evidence_requires_source_ids(): void
    {
        $plan = (new AiHallucinationGuardPlanService)->plan([
            'ai_task' => 'data-answer',
            'risk_level' => 'high',
            'evidence_sources' => 'none',
            'runtime_checks' => 'no',
            'human_review' => 'yes',
        ]);

        $this->assertSame('high', $plan['risk_score']['level']);
        $this->assertContains('No evidence sources means the model may invent APIs, files, schema, or behavior.', $plan['risk_score']['reasons']);
        $this->assertSame(25, $plan['score_breakdown'][1]['points']);
        $this->assertSame(15, $plan['score_breakdown'][2]['points']);
        $this->assertSame('Evidence sources', $plan['decision_matrix'][1]['signal']);
        $this->assertSame('factual answer, total, citation, or source-backed statement is true', $plan['claim_validation_rules'][4]['claim']);
        $this->assertSame('source_id', $plan['evidence_ledger_template'][5]['field']);
        $this->assertSame('source ledger check', $plan['runtime_verification_commands'][3]['name']);
        $this->assertSame('domain owner', $plan['escalation_policy'][3]['escalate_to']);
        $this->assertSame('source ledger', $plan['safe_output_contract'][5]['section']);
        $this->assertContains('Add an evidence map with repo files, official docs, logs, tests, source rows, or retrieval IDs.', $plan['release_readiness']['required_before_release']);
        $this->assertContains('Generated facts or datasets that are not backed by retrieval, database rows, or source citations.', $plan['hallucination_vectors']);
        $this->assertContains('Use retrieval, database constraints, or source IDs before showing factual answers to users.', $plan['code_controls']);
        $this->assertSame('factual data answer', $plan['evidence_requirements'][4]['claim_type']);
        $this->assertContains('retrieval source ID', $plan['evidence_requirements'][4]['acceptable_evidence']);
        $this->assertStringContainsString('source ledger quality', $plan['ownership_model'][2]['owns']);
        $this->assertSame('source ledger', $plan['required_artifacts'][4]['name']);
        $this->assertContains('raw datasets without source IDs, sampling rules, or permission to use them', $plan['data_safety_plan']['blocked_inputs']);
        $this->assertSame('source-grounded answer', $plan['prompt_examples'][3]['name']);
        $this->assertContains('evidence-map-required', $plan['ci_policy']['required_checks']);
        $this->assertContains('runtime-verification-required', $plan['ci_policy']['required_checks']);
        $this->assertContains('ai_source_missing_total for factual answers without source IDs', $plan['observability_plan']['metrics']);
        $this->assertSame('Fabricated factual data presented as if it came from a source.', $plan['red_team_scenarios'][3]['planted_risk']);
        $this->assertContains('Runtime verification is added before production acceptance.', $plan['acceptance_gate']['required_before_accept']);
        $this->assertContains('The output is marked unverified until runtime checks are added.', $plan['tests']);
    }

    /**
     * Code review with tests and docs can become a controlled AI workflow.
     */
    public function test_review_with_tests_and_docs_is_controlled(): void
    {
        $plan = (new AiHallucinationGuardPlanService)->plan([
            'ai_task' => 'code-review',
            'risk_level' => 'medium',
            'evidence_sources' => 'tests-docs',
            'runtime_checks' => 'yes',
            'human_review' => 'yes',
        ]);

        $this->assertSame('controlled', $plan['risk_score']['level']);
        $this->assertSame(-10, $plan['score_breakdown'][1]['points']);
        $this->assertSame('25/controlled', $plan['decision_matrix'][4]['current_value']);
        $this->assertSame('ready-for-review', $plan['release_readiness']['status']);
        $this->assertContains('Keep the evidence map and verification result attached to the task or pull request.', $plan['release_readiness']['required_before_release']);
        $this->assertSame('code reviewer', $plan['escalation_policy'][0]['escalate_to']);
        $this->assertSame('evidence map', $plan['safe_output_contract'][1]['section']);
        $this->assertSame('accept only after evidence, scope, and runtime checks pass', $plan['acceptance_gate']['decision']);
        $this->assertSame('standard-blocking', $plan['ci_policy']['mode']);
        $this->assertContains('Repo context plus tests or docs reduces unsupported claims.', $plan['risk_score']['reasons']);
        $this->assertSame('learn', $plan['rollback_playbook'][3]['phase']);
        $this->assertSame('manual', $plan['maturity_roadmap'][0]['level']);
        $this->assertSame('strict review pass', $plan['prompt_examples'][2]['name']);
        $this->assertSame('runtime', $plan['review_checklist'][3]['lens']);
        $this->assertSame('dependency/version check', $plan['automation_hooks'][2]['hook']);
        $this->assertSame('php artisan test --filter AiHallucinationGuardPlan', $plan['commands'][1]);
    }
}
