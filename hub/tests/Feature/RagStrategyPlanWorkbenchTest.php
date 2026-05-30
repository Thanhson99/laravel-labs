<?php

declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;

final class RagStrategyPlanWorkbenchTest extends TestCase
{
    /**
     * The RAG strategy workbench renders the runnable form.
     */
    public function test_rag_strategy_plan_workbench_renders(): void
    {
        $response = $this->get('/workbench/rag-strategy-plan');

        $response
            ->assertOk()
            ->assertSee('RAG Strategy Plan Workbench')
            ->assertSee('POST /api/practice/rag-strategy-plan')
            ->assertSee('RagStrategyPlanService')
            ->assertSee('Graph incident knowledge')
            ->assertSee('Long context document pack')
            ->assertSee('CAG stable FAQ chatbot')
            ->assertSee('Hybrid support chatbot')
            ->assertSee('Rollout gates')
            ->assertSee('Context routing')
            ->assertSee('Answer contract')
            ->assertSee('Cost controls')
            ->assertSee('Benchmark plan')
            ->assertSee('Test fixtures')
            ->assertSee('Implementation backlog')
            ->assertSee('Owners')
            ->assertSee('SLO policy')
            ->assertSee('Release checklist')
            ->assertSee('Evidence packet')
            ->assertSee('Laravel blueprint')
            ->assertSee('API contract')
            ->assertSee('CI gates')
            ->assertSee('Injection tests')
            ->assertSee('Capacity plan')
            ->assertSee('Threat model')
            ->assertSee('Failure runbook')
            ->assertSee('Plan RAG strategy');
    }

    /**
     * Document Q&A returns a classic RAG plan.
     */
    public function test_rag_strategy_plan_api_returns_classic_plan(): void
    {
        $response = $this->postJson('/api/practice/rag-strategy-plan', [
            'knowledge_shape' => 'documents',
            'relationship_need' => 'low',
            'tool_use' => 'none',
            'freshness' => 'periodic',
            'risk_level' => 'medium',
            'answer_style' => 'citations',
        ]);

        $response
            ->assertOk()
            ->assertJsonPath('data.summary.recommendation', 'classic-rag')
            ->assertJsonPath('data.context_strategy_plan.recommendation', 'rag')
            ->assertJsonPath('data.context_strategy_plan.rag_pattern', 'classic-rag')
            ->assertJsonPath('data.decision_matrix.0.style', 'classic-rag')
            ->assertJsonPath('data.decision_matrix.3.style', 'long-context')
            ->assertJsonPath('data.readiness_score.status', 'ready-for-shadow-mode')
            ->assertJsonPath('data.data_model_contract.0.name', 'source_id')
            ->assertJsonPath('data.api_response_example.citations.0.source_id', 'src_knowledge_001')
            ->assertJsonPath('data.openapi_contract.path', '/api/rag/answers')
            ->assertJsonPath('data.openapi_contract.request_schema.properties.max_results.maximum', 20)
            ->assertJsonPath('data.laravel_integration_blueprint.0.file', 'routes/api.php')
            ->assertJsonPath('data.laravel_integration_blueprint.3.file', 'app/Services/Rag/RagAnswerService.php')
            ->assertJsonPath('data.threat_model.0.threat', 'prompt injection inside retrieved content')
            ->assertJsonPath('data.prompt_injection_tests.0.name', 'retrieved instruction override')
            ->assertJsonPath('data.privacy_compliance_plan.data_minimization', 'Index only the fields needed for retrieval and answer grounding; keep raw sensitive payloads out of vector stores when possible.')
            ->assertJsonPath('data.slo_policy.latency', 'p95 <= 3s')
            ->assertJsonPath('data.capacity_plan.0.resource', 'embedding jobs')
            ->assertJsonPath('data.feedback_schema.failure_type.0', 'missing-source')
            ->assertJsonPath('data.versioning_policy.source_version', 'Version source snapshots so every answer can be replayed against the exact indexed content.')
            ->assertJsonPath('data.release_checklist.0.item', 'Golden questions pass')
            ->assertJsonPath('data.evidence_packet.0', 'Golden question results with expected source IDs and actual retrieved source IDs.')
            ->assertJsonPath('data.audit_artifact.strategy', 'classic-rag')
            ->assertJsonPath('data.ci_quality_gates.0.command', 'php artisan test --filter RagAnswerApiTest')
            ->assertJsonPath('data.benchmark_plan.0.name', 'source recall benchmark')
            ->assertJsonPath('data.test_fixture_plan.0.name', 'happy path source set')
            ->assertJsonPath('data.feedback_loop.0.event', 'thumbs-down or correction')
            ->assertJsonPath('data.ownership_matrix.0.role', 'content owner')
            ->assertJsonPath('data.migration_path.0.to', 'classic-rag')
            ->assertJsonPath('data.decommission_plan.0', 'Keep a feature flag that can disable generated answers while preserving retrieval logs for analysis.')
            ->assertJsonPath('data.implementation_backlog.0.milestone', 'contract first')
            ->assertJsonPath('data.review_checklist.0', 'Does every material answer claim map to retrieved source IDs?')
            ->assertJsonPath('data.style_catalog.0.name', 'classic-rag')
            ->assertJsonPath('data.evaluation_plan.0.metric', 'retrieval precision')
            ->assertJsonPath('data.golden_question_set.0.question', 'Which source supports the main answer, and what timestamp or version does it have?')
            ->assertJsonPath('data.answer_contract.citations', 'Array of source IDs with title, section, URL or path, updated_at, and matched snippet.')
            ->assertJsonPath('data.source_lifecycle.0.stage', 'ingest')
            ->assertJsonPath('data.observability_plan.0.signal', 'retrieval_miss_rate')
            ->assertJsonPath('data.rollout_plan.0.stage', 'offline evaluation')
            ->assertJsonPath('data.prompt_templates.refusal_prompt', 'If evidence is missing, unauthorized, stale beyond SLA, or outside scope, refuse briefly and list the next safe evidence needed.')
            ->assertJsonPath('data.failure_runbook.0.symptom', 'Answer has no citation or cites the wrong source.')
            ->assertJsonPath('data.commands.1', 'php artisan test --filter RagStrategyPlan');

        $this->assertStringContainsString('Implement a Laravel AI chatbot answer API using rag context strategy and classic-rag retrieval pattern.', $response->json('data.implementation_prompt'));
        $this->assertStringContainsString('ADR: Adopt rag context strategy with classic-rag', $response->json('data.adr_summary_markdown'));
    }

    /**
     * Explicit Long Context planning returns chatbot context routing guardrails.
     */
    public function test_rag_strategy_plan_api_returns_long_context_strategy_plan(): void
    {
        $response = $this->postJson('/api/practice/rag-strategy-plan', [
            'knowledge_shape' => 'documents',
            'relationship_need' => 'medium',
            'tool_use' => 'none',
            'freshness' => 'static',
            'risk_level' => 'medium',
            'answer_style' => 'summary',
            'context_strategy' => 'long-context',
        ]);

        $response
            ->assertOk()
            ->assertJsonPath('data.context_strategy_plan.requested', 'long-context')
            ->assertJsonPath('data.context_strategy_plan.recommendation', 'long-context')
            ->assertJsonPath('data.context_strategy_plan.routing_sequence.1.step', 'pack context')
            ->assertJsonPath('data.context_strategy_plan.guardrails.0', 'Set a strict context budget and trim by source priority before generation.')
            ->assertJsonPath('data.style_catalog.3.name', 'long-context');
    }

    /**
     * Explicit CAG planning returns cache-specific chatbot controls.
     */
    public function test_rag_strategy_plan_api_returns_cag_strategy_plan(): void
    {
        $response = $this->postJson('/api/practice/rag-strategy-plan', [
            'knowledge_shape' => 'documents',
            'relationship_need' => 'low',
            'tool_use' => 'none',
            'freshness' => 'static',
            'risk_level' => 'low',
            'answer_style' => 'summary',
            'context_strategy' => 'cag',
        ]);

        $response
            ->assertOk()
            ->assertJsonPath('data.context_strategy_plan.requested', 'cag')
            ->assertJsonPath('data.context_strategy_plan.recommendation', 'cag')
            ->assertJsonPath('data.context_strategy_plan.routing_sequence.1.step', 'read cache')
            ->assertJsonPath('data.context_strategy_plan.guardrails.0', 'Use permission-aware cache keys and expire cache entries when source policy changes.')
            ->assertJsonPath('data.style_catalog.4.name', 'cag');
    }

    /**
     * Entity relationship scenarios return a Graph RAG plan.
     */
    public function test_rag_strategy_plan_api_returns_graph_plan(): void
    {
        $response = $this->postJson('/api/practice/rag-strategy-plan', [
            'knowledge_shape' => 'entities',
            'relationship_need' => 'high',
            'tool_use' => 'retrieval-tools',
            'freshness' => 'periodic',
            'risk_level' => 'high',
            'answer_style' => 'citations',
        ]);

        $response
            ->assertOk()
            ->assertJsonPath('data.summary.recommendation', 'graph-rag')
            ->assertJsonPath('data.architecture_plan.3.phase', 'graph layer')
            ->assertJsonPath('data.golden_question_set.3.question', 'Which entity relationship explains the answer path?')
            ->assertJsonPath('data.observability_plan.4.signal', 'edge_provenance_gap')
            ->assertJsonPath('data.source_lifecycle.4.stage', 'edge review')
            ->assertJsonPath('data.readiness_score.status', 'needs-hardening')
            ->assertJsonPath('data.data_model_contract.6.name', 'entity_edges')
            ->assertJsonPath('data.api_response_example.graph_path.0.edge', 'depends_on')
            ->assertJsonPath('data.openapi_contract.request_schema.properties.entity_hint.nullable', true)
            ->assertJsonPath('data.laravel_integration_blueprint.5.file', 'app/Services/Rag/GraphEvidenceService.php')
            ->assertJsonPath('data.threat_model.4.threat', 'poisoned or inferred graph edge changes answer meaning')
            ->assertJsonPath('data.prompt_injection_tests.3.name', 'graph edge poisoning')
            ->assertJsonPath('data.capacity_plan.4.resource', 'graph traversal')
            ->assertJsonPath('data.feedback_schema.edge_id', 'Optional graph edge ID that was wrong, missing, or unsupported.')
            ->assertJsonPath('data.versioning_policy.style_specific_version', 'Version entity extraction rules, edge confidence thresholds, and graph traversal policy.')
            ->assertJsonPath('data.release_checklist.4.item', 'Graph edge review is complete')
            ->assertJsonPath('data.audit_artifact.graph_version', 'graph:v1')
            ->assertJsonPath('data.ci_quality_gates.4.command', 'php artisan rag:evaluate --suite=graph-edges')
            ->assertJsonPath('data.benchmark_plan.4.name', 'edge quality benchmark')
            ->assertJsonPath('data.test_fixture_plan.4.name', 'bad edge set')
            ->assertJsonPath('data.feedback_loop.3.event', 'relationship correction')
            ->assertJsonPath('data.ownership_matrix.4.role', 'domain owner')
            ->assertJsonPath('data.implementation_backlog.3.milestone', 'graph hardening')
            ->assertJsonPath('data.decommission_plan.4', 'Remove graph traversal first, fall back to classic retrieval, then prune entity and edge indexes after audit.')
            ->assertJsonPath('data.failure_runbook.3.symptom', 'Graph answer follows a wrong entity edge.')
            ->assertJsonPath('data.prompt_templates.style_specific_prompt', 'When using graph evidence, include the entity path and cite the source that proves each edge.')
            ->assertJsonPath('data.risk_controls.3', 'Store edge provenance and confidence so graph relationships can be audited.');
    }

    /**
     * Workflow and tool-use scenarios return an Agentic RAG plan.
     */
    public function test_rag_strategy_plan_api_returns_agentic_plan(): void
    {
        $response = $this->postJson('/api/practice/rag-strategy-plan', [
            'knowledge_shape' => 'workflows',
            'relationship_need' => 'medium',
            'tool_use' => 'multi-step-agent',
            'freshness' => 'real-time',
            'risk_level' => 'high',
            'answer_style' => 'actions',
        ]);

        $response
            ->assertOk()
            ->assertJsonPath('data.summary.recommendation', 'agentic-rag')
            ->assertJsonPath('data.architecture_plan.3.phase', 'agent loop')
            ->assertJsonPath('data.golden_question_set.3.question', 'Which tool calls were required, and why did the agent stop?')
            ->assertJsonPath('data.observability_plan.4.signal', 'tool_iteration_limit_hits')
            ->assertJsonPath('data.answer_contract.follow_up', 'Next safe action the agent can take, including the required tool and approval level.')
            ->assertJsonPath('data.data_model_contract.6.name', 'tool_call_log')
            ->assertJsonPath('data.api_response_example.tool_calls.0.tool', 'approved_retriever')
            ->assertJsonPath('data.openapi_contract.request_schema.properties.allow_tools.default', false)
            ->assertJsonPath('data.laravel_integration_blueprint.5.file', 'app/Services/Rag/RagToolRunner.php')
            ->assertJsonPath('data.threat_model.4.threat', 'agent tool call performs an unsafe action')
            ->assertJsonPath('data.prompt_injection_tests.3.name', 'tool escalation injection')
            ->assertJsonPath('data.slo_policy.latency', 'p95 <= 8s')
            ->assertJsonPath('data.capacity_plan.4.resource', 'tool execution')
            ->assertJsonPath('data.feedback_schema.tool_call_id', 'Optional tool call ID that failed, looped, or should have required approval.')
            ->assertJsonPath('data.versioning_policy.style_specific_version', 'Version tool allowlists, tool schemas, iteration budgets, and approval policy.')
            ->assertJsonPath('data.release_checklist.4.item', 'Tool safety review is complete')
            ->assertJsonPath('data.audit_artifact.tool_policy_version', 'tools:v1')
            ->assertJsonPath('data.ci_quality_gates.4.command', 'php artisan rag:evaluate --suite=tool-safety')
            ->assertJsonPath('data.benchmark_plan.4.name', 'tool loop benchmark')
            ->assertJsonPath('data.test_fixture_plan.4.name', 'tool loop set')
            ->assertJsonPath('data.feedback_loop.3.event', 'unsafe tool behavior')
            ->assertJsonPath('data.ownership_matrix.4.role', 'tool owner')
            ->assertJsonPath('data.implementation_backlog.3.milestone', 'agent hardening')
            ->assertJsonPath('data.decommission_plan.4', 'Disable write-capable tools first, then disable agent planning, while keeping read-only retrieval available.')
            ->assertJsonPath('data.failure_runbook.3.symptom', 'Agent loops through tools or proposes an unsafe action.')
            ->assertJsonPath('data.risk_controls.3', 'Cap agent iterations, restrict tools by allowlist, and require human approval for side-effecting actions.');
    }

    /**
     * Invalid RAG strategy payloads return validation errors.
     */
    public function test_rag_strategy_plan_api_validates_payload(): void
    {
        $response = $this->postJson('/api/practice/rag-strategy-plan', [
            'knowledge_shape' => 'memory',
            'relationship_need' => 'magic',
            'tool_use' => 'unlimited',
            'freshness' => 'eventually',
            'risk_level' => 'unknown',
            'answer_style' => 'poetry',
            'context_strategy' => 'magic',
        ]);

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors([
                'knowledge_shape',
                'relationship_need',
                'tool_use',
                'freshness',
                'risk_level',
                'answer_style',
                'context_strategy',
            ]);
    }
}
