<?php

declare(strict_types=1);

namespace Tests\Unit\Practice;

use App\Services\Practice\RagStrategyPlanService;
use PHPUnit\Framework\TestCase;

final class RagStrategyPlanServiceTest extends TestCase
{
    /**
     * Document-heavy retrieval starts with classic RAG.
     */
    public function test_document_qa_recommends_classic_rag(): void
    {
        $plan = (new RagStrategyPlanService)->plan([
            'knowledge_shape' => 'documents',
            'relationship_need' => 'low',
            'tool_use' => 'none',
            'freshness' => 'periodic',
            'risk_level' => 'medium',
            'answer_style' => 'citations',
        ]);

        $this->assertSame('classic-rag', $plan['summary']['recommendation']);
        $this->assertSame('auto', $plan['context_strategy_plan']['requested']);
        $this->assertSame('rag', $plan['context_strategy_plan']['recommendation']);
        $this->assertSame('classic-rag', $plan['context_strategy_plan']['rag_pattern']);
        $this->assertSame('retrieve evidence', $plan['context_strategy_plan']['routing_sequence'][1]['step']);
        $this->assertContains('Require citations for material claims.', $plan['context_strategy_plan']['guardrails']);
        $this->assertSame('recommended', $plan['decision_matrix'][0]['fit']);
        $this->assertSame('long-context', $plan['decision_matrix'][3]['style']);
        $this->assertSame('cag', $plan['decision_matrix'][4]['style']);
        $this->assertSame('hybrid', $plan['decision_matrix'][5]['style']);
        $this->assertSame('ready-for-shadow-mode', $plan['readiness_score']['status']);
        $this->assertSame('source_id', $plan['data_model_contract'][0]['name']);
        $this->assertSame('src_knowledge_001', $plan['api_response_example']['citations'][0]['source_id']);
        $this->assertSame('/api/rag/answers', $plan['openapi_contract']['path']);
        $this->assertSame(20, $plan['openapi_contract']['request_schema']['properties']['max_results']['maximum']);
        $this->assertSame('routes/api.php', $plan['laravel_integration_blueprint'][0]['file']);
        $this->assertSame('app/Services/Rag/RagAnswerService.php', $plan['laravel_integration_blueprint'][3]['file']);
        $this->assertSame('prompt injection inside retrieved content', $plan['threat_model'][0]['threat']);
        $this->assertSame('retrieved instruction override', $plan['prompt_injection_tests'][0]['name']);
        $this->assertStringContainsString('raw sensitive payloads', $plan['privacy_compliance_plan']['data_minimization']);
        $this->assertSame('p95 <= 3s', $plan['slo_policy']['latency']);
        $this->assertSame('embedding jobs', $plan['capacity_plan'][0]['resource']);
        $this->assertSame('missing-source', $plan['feedback_schema']['failure_type'][0]);
        $this->assertStringContainsString('source snapshots', $plan['versioning_policy']['source_version']);
        $this->assertSame('Golden questions pass', $plan['release_checklist'][0]['item']);
        $this->assertStringContainsString('Golden question results', $plan['evidence_packet'][0]);
        $this->assertSame('classic-rag', $plan['audit_artifact']['strategy']);
        $this->assertSame('php artisan test --filter RagAnswerApiTest', $plan['ci_quality_gates'][0]['command']);
        $this->assertSame('source recall benchmark', $plan['benchmark_plan'][0]['name']);
        $this->assertSame('happy path source set', $plan['test_fixture_plan'][0]['name']);
        $this->assertSame('thumbs-down or correction', $plan['feedback_loop'][0]['event']);
        $this->assertSame('content owner', $plan['ownership_matrix'][0]['role']);
        $this->assertSame('classic-rag', $plan['migration_path'][0]['to']);
        $this->assertStringContainsString('feature flag', $plan['decommission_plan'][0]);
        $this->assertStringContainsString('ADR: Adopt rag context strategy with classic-rag', $plan['adr_summary_markdown']);
        $this->assertStringContainsString('Implement a Laravel AI chatbot answer API using rag context strategy and classic-rag retrieval pattern.', $plan['implementation_prompt']);
        $this->assertSame('contract first', $plan['implementation_backlog'][0]['milestone']);
        $this->assertContains('Does every material answer claim map to retrieved source IDs?', $plan['review_checklist']);
        $this->assertGreaterThan($plan['score_breakdown']['graph_rag'], $plan['score_breakdown']['classic_rag']);
        $this->assertStringContainsString('source IDs', $plan['risk_controls'][0]);
        $this->assertSame('permission_filter', array_key_first($plan['access_control_plan']));
        $this->assertSame('Array of source IDs with title, section, URL or path, updated_at, and matched snippet.', $plan['answer_contract']['citations']);
        $this->assertSame('ingest', $plan['source_lifecycle'][0]['stage']);
        $this->assertSame('offline evaluation', $plan['rollout_plan'][0]['stage']);
        $this->assertSame('retrieval_miss_rate', $plan['observability_plan'][0]['signal']);
        $this->assertContains('Set top-k, maximum context tokens, and reranker limits from evaluation data instead of defaulting high.', $plan['cost_controls']);
        $this->assertStringContainsString('Do not answer yet', $plan['prompt_templates']['retrieval_query_prompt']);
        $this->assertSame('Answer has no citation or cites the wrong source.', $plan['failure_runbook'][0]['symptom']);
        $this->assertStringContainsString('retrieval precision', $plan['anti_patterns'][2]);
        $this->assertSame('php artisan test --filter RagStrategyPlan', $plan['commands'][1]);
    }

    /**
     * Stable low-risk knowledge can auto-select CAG for repeated chatbot answers.
     */
    public function test_static_low_risk_knowledge_auto_selects_cag_context_strategy(): void
    {
        $plan = (new RagStrategyPlanService)->plan([
            'knowledge_shape' => 'documents',
            'relationship_need' => 'low',
            'tool_use' => 'none',
            'freshness' => 'static',
            'risk_level' => 'low',
            'answer_style' => 'summary',
        ]);

        $this->assertSame('classic-rag', $plan['summary']['recommendation']);
        $this->assertSame('auto', $plan['context_strategy_plan']['requested']);
        $this->assertSame('cag', $plan['context_strategy_plan']['recommendation']);
        $this->assertSame('read cache', $plan['context_strategy_plan']['routing_sequence'][1]['step']);
        $this->assertContains('Use permission-aware cache keys and expire cache entries when source policy changes.', $plan['context_strategy_plan']['guardrails']);
        $this->assertGreaterThanOrEqual($plan['score_breakdown']['long_context'], $plan['score_breakdown']['cag']);
        $this->assertStringContainsString('CAG uses a permission-aware cache', $plan['interview_answer']);
        $this->assertStringContainsString('cag context strategy', $plan['implementation_prompt']);
        $this->assertStringContainsString('Adopt cag context strategy with classic-rag', $plan['adr_summary_markdown']);
    }

    /**
     * Explicit Long Context keeps the RAG pattern while changing the chatbot context path.
     */
    public function test_explicit_long_context_strategy_adds_pack_context_route(): void
    {
        $plan = (new RagStrategyPlanService)->plan([
            'knowledge_shape' => 'documents',
            'relationship_need' => 'medium',
            'tool_use' => 'none',
            'freshness' => 'static',
            'risk_level' => 'medium',
            'answer_style' => 'summary',
            'context_strategy' => 'long-context',
        ]);

        $this->assertSame('classic-rag', $plan['summary']['recommendation']);
        $this->assertSame('long-context', $plan['context_strategy_plan']['requested']);
        $this->assertSame('long-context', $plan['context_strategy_plan']['recommendation']);
        $this->assertSame('pack context', $plan['context_strategy_plan']['routing_sequence'][1]['step']);
        $this->assertStringContainsString('bounded document pack', $plan['context_strategy_plan']['use_when']);
        $this->assertContains('Measure lost-in-the-middle failures with long-context regression questions.', $plan['context_strategy_plan']['guardrails']);
        $this->assertSame('long-context', $plan['style_catalog'][3]['name']);
        $this->assertStringContainsString('Long Context packs a bounded document set', $plan['interview_answer']);
    }

    /**
     * Explicit hybrid strategy routes stable, fresh, and bounded context paths separately.
     */
    public function test_explicit_hybrid_strategy_keeps_agentic_rag_with_router_guardrails(): void
    {
        $plan = (new RagStrategyPlanService)->plan([
            'knowledge_shape' => 'workflows',
            'relationship_need' => 'medium',
            'tool_use' => 'multi-step-agent',
            'freshness' => 'real-time',
            'risk_level' => 'high',
            'answer_style' => 'actions',
            'context_strategy' => 'hybrid',
        ]);

        $this->assertSame('agentic-rag', $plan['summary']['recommendation']);
        $this->assertSame('hybrid', $plan['context_strategy_plan']['requested']);
        $this->assertSame('hybrid', $plan['context_strategy_plan']['recommendation']);
        $this->assertSame('agentic-rag', $plan['context_strategy_plan']['rag_pattern']);
        $this->assertSame('route context path', $plan['context_strategy_plan']['routing_sequence'][1]['step']);
        $this->assertContains('Route stable FAQ to CAG, fresh or private knowledge to RAG, and bounded analysis packs to Long Context.', $plan['context_strategy_plan']['guardrails']);
        $this->assertStringContainsString('agentic-rag for fresh evidence', $plan['context_strategy_plan']['routing_sequence'][1]['decision']);
        $this->assertStringContainsString('Hybrid routing combines those paths', $plan['interview_answer']);
    }

    /**
     * Entity-heavy relationship questions recommend Graph RAG.
     */
    public function test_entity_relationships_recommend_graph_rag(): void
    {
        $plan = (new RagStrategyPlanService)->plan([
            'knowledge_shape' => 'entities',
            'relationship_need' => 'high',
            'tool_use' => 'retrieval-tools',
            'freshness' => 'periodic',
            'risk_level' => 'high',
            'answer_style' => 'citations',
        ]);

        $this->assertSame('graph-rag', $plan['summary']['recommendation']);
        $this->assertSame('recommended', $plan['decision_matrix'][1]['fit']);
        $this->assertSame('needs-hardening', $plan['readiness_score']['status']);
        $this->assertStringContainsString('relationship', $plan['summary']['reason']);
        $this->assertContains('Store edge provenance and confidence so graph relationships can be audited.', $plan['risk_controls']);
        $this->assertStringContainsString('graph traversal', implode(' ', $plan['implementation_steps']));
        $this->assertSame('Which entity relationship explains the answer path?', $plan['golden_question_set'][3]['question']);
        $this->assertSame('edge_provenance_gap', $plan['observability_plan'][4]['signal']);
        $this->assertSame('edge review', $plan['source_lifecycle'][4]['stage']);
        $this->assertSame('entity_edges', $plan['data_model_contract'][6]['name']);
        $this->assertSame('depends_on', $plan['api_response_example']['graph_path'][0]['edge']);
        $this->assertTrue($plan['openapi_contract']['request_schema']['properties']['entity_hint']['nullable']);
        $this->assertSame('app/Services/Rag/GraphEvidenceService.php', $plan['laravel_integration_blueprint'][5]['file']);
        $this->assertSame('poisoned or inferred graph edge changes answer meaning', $plan['threat_model'][4]['threat']);
        $this->assertSame('graph edge poisoning', $plan['prompt_injection_tests'][3]['name']);
        $this->assertSame('graph traversal', $plan['capacity_plan'][4]['resource']);
        $this->assertSame('Optional graph edge ID that was wrong, missing, or unsupported.', $plan['feedback_schema']['edge_id']);
        $this->assertStringContainsString('entity extraction rules', $plan['versioning_policy']['style_specific_version']);
        $this->assertSame('Graph edge review is complete', $plan['release_checklist'][4]['item']);
        $this->assertSame('graph:v1', $plan['audit_artifact']['graph_version']);
        $this->assertSame('php artisan rag:evaluate --suite=graph-edges', $plan['ci_quality_gates'][4]['command']);
        $this->assertSame('edge quality benchmark', $plan['benchmark_plan'][4]['name']);
        $this->assertSame('bad edge set', $plan['test_fixture_plan'][4]['name']);
        $this->assertSame('relationship correction', $plan['feedback_loop'][3]['event']);
        $this->assertSame('domain owner', $plan['ownership_matrix'][4]['role']);
        $this->assertSame('graph hardening', $plan['implementation_backlog'][3]['milestone']);
        $this->assertStringContainsString('Remove graph traversal first', $plan['decommission_plan'][4]);
        $this->assertSame('Graph answer follows a wrong entity edge.', $plan['failure_runbook'][3]['symptom']);
        $this->assertStringContainsString('entity path', $plan['prompt_templates']['style_specific_prompt']);
        $this->assertStringContainsString('graph traversal depth', implode(' ', $plan['cost_controls']));
        $this->assertStringContainsString('source provenance', $plan['anti_patterns'][4]);
    }

    /**
     * Real-time multi-step workflows recommend Agentic RAG with tool controls.
     */
    public function test_workflows_with_tools_recommend_agentic_rag(): void
    {
        $plan = (new RagStrategyPlanService)->plan([
            'knowledge_shape' => 'workflows',
            'relationship_need' => 'medium',
            'tool_use' => 'multi-step-agent',
            'freshness' => 'real-time',
            'risk_level' => 'high',
            'answer_style' => 'actions',
        ]);

        $this->assertSame('agentic-rag', $plan['summary']['recommendation']);
        $this->assertSame('recommended', $plan['decision_matrix'][2]['fit']);
        $this->assertSame('needs-hardening', $plan['readiness_score']['status']);
        $this->assertGreaterThan($plan['score_breakdown']['classic_rag'], $plan['score_breakdown']['agentic_rag']);
        $this->assertContains('Cap agent iterations, restrict tools by allowlist, and require human approval for side-effecting actions.', $plan['risk_controls']);
        $this->assertSame('Which tool calls were required, and why did the agent stop?', $plan['golden_question_set'][3]['question']);
        $this->assertSame('tool_iteration_limit_hits', $plan['observability_plan'][4]['signal']);
        $this->assertStringContainsString('Next safe action', $plan['answer_contract']['follow_up']);
        $this->assertSame('tool_call_log', $plan['data_model_contract'][6]['name']);
        $this->assertSame('approved_retriever', $plan['api_response_example']['tool_calls'][0]['tool']);
        $this->assertFalse($plan['openapi_contract']['request_schema']['properties']['allow_tools']['default']);
        $this->assertSame('app/Services/Rag/RagToolRunner.php', $plan['laravel_integration_blueprint'][5]['file']);
        $this->assertSame('agent tool call performs an unsafe action', $plan['threat_model'][4]['threat']);
        $this->assertSame('tool escalation injection', $plan['prompt_injection_tests'][3]['name']);
        $this->assertSame('p95 <= 8s', $plan['slo_policy']['latency']);
        $this->assertSame('tool execution', $plan['capacity_plan'][4]['resource']);
        $this->assertSame('Optional tool call ID that failed, looped, or should have required approval.', $plan['feedback_schema']['tool_call_id']);
        $this->assertStringContainsString('tool allowlists', $plan['versioning_policy']['style_specific_version']);
        $this->assertSame('Tool safety review is complete', $plan['release_checklist'][4]['item']);
        $this->assertSame('tools:v1', $plan['audit_artifact']['tool_policy_version']);
        $this->assertSame('php artisan rag:evaluate --suite=tool-safety', $plan['ci_quality_gates'][4]['command']);
        $this->assertSame('tool loop benchmark', $plan['benchmark_plan'][4]['name']);
        $this->assertSame('tool loop set', $plan['test_fixture_plan'][4]['name']);
        $this->assertSame('unsafe tool behavior', $plan['feedback_loop'][3]['event']);
        $this->assertSame('tool owner', $plan['ownership_matrix'][4]['role']);
        $this->assertSame('agent hardening', $plan['implementation_backlog'][3]['milestone']);
        $this->assertStringContainsString('Disable write-capable tools first', $plan['decommission_plan'][4]);
        $this->assertSame('Agent loops through tools or proposes an unsafe action.', $plan['failure_runbook'][3]['symptom']);
        $this->assertStringContainsString('iteration budget', implode(' ', $plan['cost_controls']));
        $this->assertStringContainsString('Before each tool call', $plan['prompt_templates']['style_specific_prompt']);
        $this->assertStringContainsString('tool access', $plan['anti_patterns'][4]);
        $this->assertStringContainsString('agent tools', $plan['interview_answer']);
    }
}
