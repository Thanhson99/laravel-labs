<?php

declare(strict_types=1);

namespace Tests\Unit\Practice;

use App\Services\Practice\ContentPracticeStarterSnippetService;
use PHPUnit\Framework\TestCase;

final class ContentPracticeStarterSnippetServiceTest extends TestCase
{
    /**
     * API validation drills include the full route, test, request, controller, and service slice.
     */
    public function test_api_validation_snippets_cover_full_laravel_slice(): void
    {
        $snippets = (new ContentPracticeStarterSnippetService)->snippetsFor([
            'technology' => 'api-validation',
        ]);

        $this->assertSame('routes/api/practice-actions.php', $snippets[0]['file']);
        $this->assertSame('tests/Feature/ContentBackedApiDrillTest.php', $snippets[1]['file']);
        $this->assertSame('app/Http/Requests/Api/StoreContentBackedDrillRequest.php', $snippets[2]['file']);
        $this->assertSame('app/Http/Controllers/Api/ContentBackedApiDrillController.php', $snippets[3]['file']);
        $this->assertSame('app/Services/Practice/ContentBackedApiDrillService.php', $snippets[4]['file']);
        $this->assertStringContainsString('assertJsonValidationErrors', $snippets[1]['code']);

    }

    /**
     * RESTful API naming drills include the route, request, service, and feature test.
     */
    public function test_restful_api_naming_snippets_cover_endpoint_contract_plan(): void
    {
        $snippets = (new ContentPracticeStarterSnippetService)->snippetsFor([
            'technology' => 'restful-api-naming',
        ]);

        $this->assertSame('routes/api/practice-actions.php', $snippets[0]['file']);
        $this->assertSame('app/Http/Requests/Api/PlanRestfulApiNamingRequest.php', $snippets[1]['file']);
        $this->assertSame('app/Services/Practice/RestfulApiNamingPlanService.php', $snippets[2]['file']);
        $this->assertSame('tests/Feature/RestfulApiNamingPlanWorkbenchTest.php', $snippets[3]['file']);
        $this->assertStringContainsString('restful-api-naming-plan', $snippets[0]['code']);
        $this->assertStringContainsString('current_endpoint', $snippets[1]['code']);
        $this->assertStringContainsString('quality_review', $snippets[2]['code']);
        $this->assertStringContainsString('contract_artifacts', $snippets[2]['code']);
        $this->assertStringContainsString('openapi_path', $snippets[2]['code']);
        $this->assertStringContainsString('implementation_blueprint', $snippets[2]['code']);
        $this->assertStringContainsString('OrderController', $snippets[2]['code']);
        $this->assertStringContainsString('migration_plan', $snippets[2]['code']);
        $this->assertStringContainsString('requires_deprecation', $snippets[2]['code']);
        $this->assertStringContainsString('response_contract', $snippets[2]['code']);
        $this->assertStringContainsString('success_responses', $snippets[2]['code']);
        $this->assertStringContainsString('operational_readiness', $snippets[2]['code']);
        $this->assertStringContainsString('api_requests_total', $snippets[2]['code']);
        $this->assertStringContainsString('naming_rubric', $snippets[2]['code']);
        $this->assertStringContainsString('Resource noun path', $snippets[2]['code']);
        $this->assertStringContainsString('client_examples', $snippets[2]['code']);
        $this->assertStringContainsString('curl -X GET', $snippets[2]['code']);
        $this->assertStringContainsString('Use plural resource nouns.', $snippets[2]['code']);
    }

    /**
     * PHP drills stay focused on pure service code and unit tests.
     */
    public function test_php_snippets_cover_pure_php_service_and_unit_test(): void
    {
        $snippets = (new ContentPracticeStarterSnippetService)->snippetsFor([
            'technology' => 'php',
        ]);

        $this->assertSame('app/Practice/Php/ContentBackedNormalizer.php', $snippets[0]['file']);
        $this->assertSame('tests/Unit/Practice/ContentBackedNormalizerTest.php', $snippets[1]['file']);
        $this->assertStringContainsString('declare(strict_types=1);', $snippets[0]['code']);
    }

    /**
     * PHP stack and heap snippets provide a runtime-memory example instead of the generic normalizer.
     */
    public function test_php_stack_heap_snippets_cover_runtime_memory_note(): void
    {
        $snippets = (new ContentPracticeStarterSnippetService)->snippetsFor([
            'technology' => 'php',
            'content' => [
                'title' => 'What is the difference between stack memory and heap memory?',
                'summary' => 'Explain call frames, heap-backed data, references, and cleanup.',
            ],
            'task' => 'Write a PHP runtime-memory note and small example.',
        ]);

        $this->assertSame('app/Practice/Php/RuntimeMemoryNote.php', $snippets[0]['file']);
        $this->assertSame('tests/Unit/Practice/RuntimeMemoryNoteTest.php', $snippets[1]['file']);
        $this->assertStringContainsString('stack_frame', $snippets[0]['code']);
        $this->assertStringContainsString('heap_backed', $snippets[0]['code']);
        $this->assertStringContainsString('reference counting', $snippets[0]['code']);
        $this->assertStringContainsString('long-running workers', strtolower($snippets[0]['code']));
    }

    /**
     * Predictive AI versus Generative AI snippets provide a comparison service and focused test.
     */
    public function test_predictive_generative_ai_snippets_cover_ai_type_comparison(): void
    {
        $snippets = (new ContentPracticeStarterSnippetService)->snippetsFor([
            'technology' => 'llm-foundations',
            'content' => [
                'title' => 'What is the difference between Predictive AI and Generative AI?',
                'body' => 'Predictive AI estimates outcomes while Generative AI creates new content.',
            ],
            'task' => 'Compare Predictive AI and Generative AI output contracts.',
        ]);

        $this->assertSame('app/Services/Practice/AiTypeComparisonPlanService.php', $snippets[0]['file']);
        $this->assertSame('tests/Feature/AiTypeComparisonPlanTest.php', $snippets[1]['file']);
        $this->assertStringContainsString('predictive_ai', $snippets[0]['code']);
        $this->assertStringContainsString('generative_ai', $snippets[0]['code']);
        $this->assertStringContainsString('calibration', $snippets[0]['code']);
        $this->assertStringContainsString('groundedness', $snippets[0]['code']);
    }

    /**
     * AI agent memory snippets separate memory types and governance checks.
     */
    public function test_ai_agent_memory_snippets_cover_memory_contracts(): void
    {
        $snippets = (new ContentPracticeStarterSnippetService)->snippetsFor([
            'technology' => 'llm-foundations',
            'content' => [
                'title' => '4 core AI agent memory types that help it work like a developer',
                'body' => 'Working memory, episodic memory, semantic memory, and procedural memory need different contracts.',
            ],
            'task' => 'Create an AI agent memory plan.',
        ]);

        $this->assertSame('app/Services/Practice/AiAgentMemoryPlanService.php', $snippets[0]['file']);
        $this->assertSame('tests/Feature/AiAgentMemoryPlanTest.php', $snippets[1]['file']);
        $this->assertStringContainsString('working_memory', $snippets[0]['code']);
        $this->assertStringContainsString('episodic_memory', $snippets[0]['code']);
        $this->assertStringContainsString('procedural_memory', $snippets[0]['code']);
        $this->assertStringContainsString('store source, freshness, confidence, and correction path', $snippets[0]['code']);
    }

    /**
     * RAG system snippets expose chatbot context strategy code, not a generic planner stub.
     */
    public function test_rag_system_snippets_cover_context_strategy_router(): void
    {
        $snippets = (new ContentPracticeStarterSnippetService)->snippetsFor([
            'technology' => 'rag-systems',
            'content' => [
                'title' => 'RAG, Long Context, or CAG: which should power an AI chatbot?',
                'body' => 'Choose RAG, Long Context, CAG, or hybrid context routing.',
            ],
            'task' => 'Create a chatbot context strategy plan.',
        ]);

        $this->assertSame('app/Services/Practice/RagStrategyPlanService.php', $snippets[0]['file']);
        $this->assertSame('app/Http/Requests/Api/PlanRagStrategyRequest.php', $snippets[1]['file']);
        $this->assertSame('app/Services/Rag/ChatbotContextRouter.php', $snippets[2]['file']);
        $this->assertSame('tests/Feature/RagStrategyPlanWorkbenchTest.php', $snippets[3]['file']);
        $this->assertStringContainsString('context_strategy_plan', $snippets[0]['code']);
        $this->assertStringContainsString("'long-context'", $snippets[1]['code']);
        $this->assertStringContainsString('permission-aware cached context', $snippets[2]['code']);
        $this->assertStringContainsString('route context path', $snippets[2]['code']);
    }

    /**
     * JavaScript closure snippets provide a counter example and interview checklist.
     */
    public function test_javascript_closure_snippets_cover_counter_and_interview_checks(): void
    {
        $snippets = (new ContentPracticeStarterSnippetService)->snippetsFor([
            'technology' => 'javascript-closures',
        ]);

        $this->assertSame('resources/js/closure-counter.js', $snippets[0]['file']);
        $this->assertSame('resources/js/closure-interview-checklist.js', $snippets[1]['file']);
        $this->assertStringContainsString('createCounter', $snippets[0]['code']);
        $this->assertStringContainsString('lexical scope', $snippets[1]['code']);
        $this->assertStringContainsString('stale closures', $snippets[1]['code']);
    }

    /**
     * JavaScript hoisting content receives hoisting-specific trace snippets.
     */
    public function test_javascript_hoisting_snippets_cover_declarations_var_and_temporal_dead_zone(): void
    {
        $snippets = (new ContentPracticeStarterSnippetService)->snippetsFor([
            'technology' => 'javascript-closures',
            'content' => [
                'title' => 'JavaScript hoisting explained simply',
                'body' => 'Explain var, let, const, function declarations, function expressions, and temporal dead zone.',
            ],
            'task' => 'Create a JavaScript hoisting explanation, visual code example, and interview checklist.',
        ]);

        $this->assertSame('resources/js/hoisting-trace.js', $snippets[0]['file']);
        $this->assertSame('resources/js/hoisting-interview-checklist.js', $snippets[1]['file']);
        $this->assertStringContainsString('sayHi();', $snippets[0]['code']);
        $this->assertStringContainsString('var is hoisted and initialized', $snippets[0]['code']);
        $this->assertStringContainsString('temporal dead zone', $snippets[1]['code']);
    }

    /**
     * Web drills include route, controller, service, view, and a feature test.
     */
    public function test_default_snippets_cover_web_slice(): void
    {
        $snippets = (new ContentPracticeStarterSnippetService)->snippetsFor([
            'technology' => 'laravel-http',
        ]);

        $this->assertSame('routes/web/practice-content.php', $snippets[0]['file']);
        $this->assertSame('tests/Feature/ContentBackedWebDrillTest.php', $snippets[1]['file']);
        $this->assertSame('app/Http/Controllers/Practice/ContentBackedWebDrillController.php', $snippets[2]['file']);
        $this->assertSame('app/Services/Practice/ContentBackedDrillService.php', $snippets[3]['file']);
        $this->assertSame('resources/views/practice/content-backed-drill.blade.php', $snippets[4]['file']);
        $this->assertStringContainsString("@extends('learning.layout'", $snippets[4]['code']);
    }

    /**
     * Laravel technology snippets turn content records into concrete code areas.
     */
    public function test_laravel_technology_snippets_cover_specialized_code_paths(): void
    {
        $service = new ContentPracticeStarterSnippetService;

        $this->assertSame('app/Policies/PracticeTaskPolicy.php', $service->snippetsFor(['technology' => 'auth-security'])[0]['file']);
        $this->assertSame('database/migrations/xxxx_xx_xx_create_practice_tasks_table.php', $service->snippetsFor(['technology' => 'database-eloquent'])[0]['file']);
        $this->assertSame('app/Http/Requests/Api/StorePracticeUploadRequest.php', $service->snippetsFor(['technology' => 'files-media'])[0]['file']);
        $this->assertSame('app/Events/PracticeTaskCompleted.php', $service->snippetsFor(['technology' => 'async-workflow'])[0]['file']);
        $this->assertSame('app/Services/Practice/PracticeDashboardSummaryService.php', $service->snippetsFor(['technology' => 'performance-cache'])[0]['file']);
        $this->assertSame('app/Contracts/PracticeTaskReporter.php', $service->snippetsFor(['technology' => 'container-architecture'])[0]['file']);
        $this->assertSame('app/Events/PracticeProgressUpdated.php', $service->snippetsFor(['technology' => 'realtime-events'])[0]['file']);
        $this->assertSame('resources/views/practice/card.blade.php', $service->snippetsFor(['technology' => 'blade-ui'])[1]['file']);
    }

    /**
     * Covering Index records receive PostgreSQL-specific review snippets.
     */
    public function test_covering_index_snippets_cover_plan_verification_and_bloat_risks(): void
    {
        $snippets = (new ContentPracticeStarterSnippetService)->snippetsFor([
            'technology' => 'database-eloquent',
            'content' => [
                'title' => 'Covering Index: why an indexed query can still be slow',
                'body' => 'Explain Index Only Scan, heap fetch, visibility map, VACUUM, and index bloat.',
            ],
            'task' => 'Create a covering-index and EXPLAIN verification plan.',
        ]);

        $this->assertSame('database/migrations/xxxx_xx_xx_add_orders_covering_index.php', $snippets[0]['file']);
        $this->assertSame('app/Services/Practice/CoveringIndexReviewService.php', $snippets[1]['file']);
        $this->assertSame('tests/Unit/Practice/CoveringIndexReviewServiceTest.php', $snippets[2]['file']);
        $this->assertStringContainsString('INCLUDE (status, total_amount)', $snippets[0]['code']);
        $this->assertStringContainsString('EXPLAIN (ANALYZE, BUFFERS) shows Index Only Scan.', $snippets[1]['code']);
        $this->assertStringContainsString('Heap Fetches is small or zero', $snippets[1]['code']);
        $this->assertStringContainsString('visibility map', $snippets[1]['code']);
        $this->assertStringContainsString('Avoid wide or high-churn included columns.', $snippets[2]['code']);
    }

    /**
     * Database locking records receive transaction-bound concurrency snippets.
     */
    public function test_database_locking_snippets_cover_transaction_and_contention_risks(): void
    {
        $snippets = (new ContentPracticeStarterSnippetService)->snippetsFor([
            'technology' => 'database-eloquent',
            'content' => [
                'title' => 'Locking trong database là gì?',
                'body' => 'Explain lockForUpdate, row lock, deadlock, and lock contention.',
            ],
            'task' => 'Create a database locking plan for inventory updates.',
        ]);

        $this->assertSame('app/Services/Practice/InventoryReservationService.php', $snippets[0]['file']);
        $this->assertSame('tests/Feature/InventoryReservationConcurrencyTest.php', $snippets[1]['file']);
        $this->assertStringContainsString('DB::transaction', $snippets[0]['code']);
        $this->assertStringContainsString('lockForUpdate()', $snippets[0]['code']);
        $this->assertStringContainsString('InsufficientStock', $snippets[0]['code']);
        $this->assertStringContainsString('external API calls', $snippets[1]['code']);
        $this->assertStringContainsString('holding lockForUpdate()', $snippets[1]['code']);
    }

    /**
     * Graph traversal snippets provide BFS and DFS planning guardrails.
     */
    public function test_graph_traversal_snippets_cover_bfs_dfs_decision_checks(): void
    {
        $snippets = (new ContentPracticeStarterSnippetService)->snippetsFor([
            'technology' => 'graph-traversal',
        ]);

        $this->assertSame('app/Services/Practice/GraphTraversalPlanService.php', $snippets[0]['file']);
        $this->assertSame('tests/Unit/Practice/GraphTraversalPlanServiceTest.php', $snippets[1]['file']);
        $this->assertStringContainsString("'bfs'", $snippets[0]['code']);
        $this->assertStringContainsString("'dfs'", $snippets[0]['code']);
        $this->assertStringContainsString('visited set', $snippets[0]['code']);
        $this->assertStringContainsString('max depth', $snippets[1]['code']);
    }

    /**
     * Planning workbench technologies point starters at their real service tests.
     */
    public function test_planning_workbench_snippets_cover_specialized_services(): void
    {
        $service = new ContentPracticeStarterSnippetService;

        $this->assertSame('app/Services/Practice/ReverseProxyFailurePlanService.php', $service->snippetsFor(['technology' => 'reverse-proxy-edge'])[0]['file']);
        $this->assertSame('app/Services/Practice/SiemElkPlanService.php', $service->snippetsFor(['technology' => 'siem-elk-observability'])[0]['file']);
        $this->assertSame('app/Services/Practice/LoadBalancerPlanService.php', $service->snippetsFor(['technology' => 'load-balancing'])[0]['file']);
        $this->assertSame('app/Services/Practice/KubernetesAnalogyPlanService.php', $service->snippetsFor(['technology' => 'kubernetes-orchestration'])[0]['file']);
        $this->assertSame('app/Services/Practice/JwtTokenStoragePlanService.php', $service->snippetsFor(['technology' => 'jwt-token-storage'])[0]['file']);
        $this->assertSame('app/Services/Practice/JwtRevocationPlanService.php', $service->snippetsFor(['technology' => 'jwt-revocation'])[0]['file']);
        $this->assertSame('app/Services/Practice/OauthFlowPlanService.php', $service->snippetsFor(['technology' => 'oauth-flow'])[0]['file']);
        $this->assertStringContainsString('pkce_sequence_simulation', $service->snippetsFor(['technology' => 'oauth-flow'])[0]['code']);
        $this->assertStringContainsString('code_verifier stays private', $service->snippetsFor(['technology' => 'oauth-flow'])[0]['code']);
        $this->assertStringContainsString('pkce_failure_drill', $service->snippetsFor(['technology' => 'oauth-flow'])[0]['code']);
        $this->assertStringContainsString('wrong verifier', $service->snippetsFor(['technology' => 'oauth-flow'])[0]['code']);
        $this->assertStringContainsString('client_credentials_checklist', $service->snippetsFor(['technology' => 'oauth-flow'])[0]['code']);
        $this->assertStringContainsString('client_credentials_access_contract', $service->snippetsFor(['technology' => 'oauth-flow'])[0]['code']);
        $this->assertStringContainsString('api://orders', $service->snippetsFor(['technology' => 'oauth-flow'])[0]['code']);
        $this->assertStringContainsString('client_credentials_token_validation_policy', $service->snippetsFor(['technology' => 'oauth-flow'])[0]['code']);
        $this->assertStringContainsString("'claim' => 'aud'", $service->snippetsFor(['technology' => 'oauth-flow'])[0]['code']);
        $this->assertStringContainsString('client_credentials_operational_plan', $service->snippetsFor(['technology' => 'oauth-flow'])[0]['code']);
        $this->assertStringContainsString('client_credentials_rotation_drill', $service->snippetsFor(['technology' => 'oauth-flow'])[0]['code']);
        $this->assertStringContainsString('revoke old secret', $service->snippetsFor(['technology' => 'oauth-flow'])[0]['code']);
        $this->assertStringContainsString('client_credentials_monitoring_signals', $service->snippetsFor(['technology' => 'oauth-flow'])[0]['code']);
        $this->assertStringContainsString('oauth_client_auth_failed_total', $service->snippetsFor(['technology' => 'oauth-flow'])[0]['code']);
        $this->assertStringContainsString('client_credentials_failure_playbook', $service->snippetsFor(['technology' => 'oauth-flow'])[0]['code']);
        $this->assertStringContainsString('wrong audience', $service->snippetsFor(['technology' => 'oauth-flow'])[0]['code']);
        $this->assertStringContainsString('client_credentials_security_review_rubric', $service->snippetsFor(['technology' => 'oauth-flow'])[0]['code']);
        $this->assertStringContainsString('confidential credential boundary', $service->snippetsFor(['technology' => 'oauth-flow'])[0]['code']);
        $this->assertStringContainsString('client_credentials_interview_checklist', $service->snippetsFor(['technology' => 'oauth-flow'])[0]['code']);
        $this->assertStringContainsString('machine-to-machine access', $service->snippetsFor(['technology' => 'oauth-flow'])[0]['code']);
        $this->assertStringContainsString('oauth_client_secret_rotated', $service->snippetsFor(['technology' => 'oauth-flow'])[0]['code']);
        $this->assertStringContainsString('service-to-service', $service->snippetsFor(['technology' => 'oauth-flow'])[1]['code']);
        $this->assertSame('app/Services/Practice/SystemDesignTradeoffPlanService.php', $service->snippetsFor(['technology' => 'system-design-tradeoffs'])[0]['file']);
        $this->assertSame('app/Services/Practice/LsmTreePlanService.php', $service->snippetsFor(['technology' => 'lsm-tree-storage'])[0]['file']);
        $this->assertSame('app/Services/Practice/LlmDecisionLoopPlanService.php', $service->snippetsFor(['technology' => 'llm-foundations'])[0]['file']);
        $this->assertSame('app/Services/Practice/RagStrategyPlanService.php', $service->snippetsFor(['technology' => 'rag-systems'])[0]['file']);
        $this->assertSame('app/Services/Practice/GraphqlRestDecisionService.php', $service->snippetsFor(['technology' => 'graphql-api'])[0]['file']);
        $this->assertSame('routes/api/practice-actions.php', $service->snippetsFor(['technology' => 'restful-api-naming'])[0]['file']);
        $this->assertSame('app/Services/Practice/GraphTraversalPlanService.php', $service->snippetsFor(['technology' => 'graph-traversal'])[0]['file']);
        $this->assertSame('resources/js/closure-counter.js', $service->snippetsFor(['technology' => 'javascript-closures'])[0]['file']);
        $this->assertSame('app/Services/Practice/ReactRenderOptimizationPlanService.php', $service->snippetsFor(['technology' => 'react-render-performance'])[0]['file']);
        $this->assertSame('app/Services/Practice/SqlInjectionDefensePlanService.php', $service->snippetsFor(['technology' => 'sql-injection-defense'])[0]['file']);
        $this->assertSame('app/Services/Practice/CsrfProtectionPlanService.php', $service->snippetsFor(['technology' => 'csrf-protection'])[0]['file']);
        $this->assertSame('app/Services/Practice/IdorAccessReviewService.php', $service->snippetsFor(['technology' => 'idor-access-control'])[0]['file']);
        $this->assertStringContainsString('idor-access-control', $service->snippetsFor(['technology' => 'idor-access-control'])[0]['code']);
        $this->assertSame('app/Policies/InvoicePolicy.php', $service->snippetsFor(['technology' => 'idor-access-control'])[1]['file']);
        $this->assertStringContainsString('object-level authorization', $service->snippetsFor(['technology' => 'idor-access-control'])[0]['code']);
        $this->assertStringContainsString('whereKey($invoiceId)->firstOrFail()', $service->snippetsFor(['technology' => 'idor-access-control'])[1]['code']);
        $this->assertStringContainsString('test_attacker_cannot_replay_victim_object_id', $service->snippetsFor(['technology' => 'idor-access-control'])[2]['code']);
        $this->assertSame('app/Services/Practice/ConfigurationReadinessService.php', $service->snippetsFor(['technology' => 'security-misconfiguration'])[0]['file']);
        $this->assertStringContainsString('security-misconfiguration', $service->snippetsFor(['technology' => 'security-misconfiguration'])[0]['code']);
        $this->assertSame('app/Services/Practice/SecurityEscapePreviewService.php', $service->snippetsFor(['technology' => 'xss-defense'])[0]['file']);
        $this->assertStringContainsString('rendering_context', $service->snippetsFor(['technology' => 'xss-defense'])[0]['code']);
        $this->assertStringContainsString('severity_summary', $service->snippetsFor(['technology' => 'xss-defense'])[0]['code']);
        $this->assertStringContainsString('release_decision', $service->snippetsFor(['technology' => 'xss-defense'])[0]['code']);
        $this->assertStringContainsString('secure_code_snippets', $service->snippetsFor(['technology' => 'xss-defense'])[0]['code']);
        $this->assertStringContainsString('review_checklist', $service->snippetsFor(['technology' => 'xss-defense'])[0]['code']);
        $this->assertStringContainsString('remediation_steps', $service->snippetsFor(['technology' => 'xss-defense'])[0]['code']);
        $this->assertStringContainsString('test_plan', $service->snippetsFor(['technology' => 'xss-defense'])[0]['code']);
        $this->assertStringContainsString('variant_review', $service->snippetsFor(['technology' => 'xss-defense'])[0]['code']);
        $this->assertStringContainsString('interview_answer_outline', $service->snippetsFor(['technology' => 'xss-defense'])[0]['code']);
        $this->assertStringContainsString('script-tag', $service->snippetsFor(['technology' => 'xss-defense'])[2]['code']);
        $this->assertSame('app/Services/Practice/AiCloudInterviewRubricService.php', $service->snippetsFor(['technology' => 'ai-cloud-interview'])[0]['file']);
    }
}
