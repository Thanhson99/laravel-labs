<?php

declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;

final class TechnologyCodeExampleTest extends TestCase
{
    /**
     * The technology code example page renders generated snippets from JSON records.
     */
    public function test_technology_code_example_page_renders_generated_snippets(): void
    {
        $response = $this->get('/practice/technology-code-examples?family=laravel&language=en&search=api&limit=10');

        $response
            ->assertOk()
            ->assertSee('Code samples generated from the technologies inside JSON content.')
            ->assertSee('api-validation')
            ->assertSee('ContentBackedApiDrillController')
            ->assertSee('StoreContentBackedDrillRequest')
            ->assertSee('Open pipeline')
            ->assertSee('Open technology detail')
            ->assertSee('Open workspace')
            ->assertSee('Open examples API');
    }

    /**
     * The technology code example API returns source-backed code snippets by technology.
     */
    public function test_technology_code_example_api_returns_snippets_by_technology(): void
    {
        $response = $this->getJson('/api/practice/technology-code-examples?family=laravel&language=en&search=api&limit=10');

        $response
            ->assertOk()
            ->assertJsonPath('data.items.0.technology', 'api-validation')
            ->assertJsonPath('data.items.0.snippets.0.file', 'routes/api/practice-actions.php')
            ->assertJsonPath('data.items.0.workspace_query.technology', 'api-validation')
            ->assertJsonStructure([
                'data' => [
                    'items' => [
                        '*' => [
                            'technology',
                            'record_count',
                            'source',
                            'content',
                            'practice',
                            'task',
                            'workspace_query',
                            'snippets' => [
                                '*' => [
                                    'label',
                                    'file',
                                    'code',
                                ],
                            ],
                        ],
                    ],
                    'meta' => [
                        'filters',
                        'record_count',
                        'technology_count',
                    ],
                ],
            ]);
    }

    /**
     * The technology detail page renders multiple source-backed records for one technology.
     */
    public function test_technology_code_example_detail_page_renders_record_level_examples(): void
    {
        $response = $this->get('/practice/technology-code-examples/api-validation?family=laravel&language=en&search=api&limit=3');

        $response
            ->assertOk()
            ->assertSee('Record-level code examples for api-validation.')
            ->assertSee('Open record workspace')
            ->assertSee('ContentBackedApiDrillController')
            ->assertSee('Open detail API');
    }

    /**
     * The technology detail API returns record-specific snippets and workspace links.
     */
    public function test_technology_code_example_detail_api_returns_record_level_examples(): void
    {
        $response = $this->getJson('/api/practice/technology-code-examples/api-validation?family=laravel&language=en&search=api&limit=3');

        $response
            ->assertOk()
            ->assertJsonPath('data.technology', 'api-validation')
            ->assertJsonPath('data.items.0.technology', 'api-validation')
            ->assertJsonPath('data.items.0.snippets.0.file', 'routes/api/practice-actions.php')
            ->assertJsonPath('data.items.0.workspace_query.technology', 'api-validation')
            ->assertJsonStructure([
                'data' => [
                    'technology',
                    'items' => [
                        '*' => [
                            'record_id',
                            'technology',
                            'source',
                            'content',
                            'practice',
                            'task',
                            'workspace_query',
                            'snippets',
                        ],
                    ],
                    'meta' => [
                        'filters',
                        'record_count',
                    ],
                ],
            ]);
    }

    /**
     * SQL Injection code examples point to the dedicated defense planner snippets.
     */
    public function test_sql_injection_code_examples_use_security_planner_snippets(): void
    {
        $this->getJson('/api/practice/technology-code-examples/sql-injection-defense?language=en&search=SQL%20Injection&limit=3')
            ->assertOk()
            ->assertJsonPath('data.technology', 'sql-injection-defense')
            ->assertJsonPath('data.items.0.practice.slug', 'sql-injection-defense-plan-workbench')
            ->assertJsonPath('data.items.0.snippets.0.file', 'app/Services/Practice/SqlInjectionDefensePlanService.php')
            ->assertJsonPath('data.items.0.workspace_query.technology', 'sql-injection-defense');
    }

    /**
     * CSRF code examples point to the dedicated protection planner snippets.
     */
    public function test_csrf_code_examples_use_security_planner_snippets(): void
    {
        $this->getJson('/api/practice/technology-code-examples/csrf-protection?language=en&search=CSRF&limit=3')
            ->assertOk()
            ->assertJsonPath('data.technology', 'csrf-protection')
            ->assertJsonPath('data.items.0.practice.slug', 'csrf-protection-plan-workbench')
            ->assertJsonPath('data.items.0.snippets.0.file', 'app/Services/Practice/CsrfProtectionPlanService.php')
            ->assertJsonPath('data.items.0.workspace_query.technology', 'csrf-protection');
    }

    /**
     * IDOR code examples use object-level authorization review snippets.
     */
    public function test_idor_code_examples_use_object_authorization_snippets(): void
    {
        $response = $this->getJson('/api/practice/technology-code-examples/idor-access-control?language=en&search=IDOR&limit=3');

        $response
            ->assertOk()
            ->assertJsonPath('data.technology', 'idor-access-control')
            ->assertJsonPath('data.items.0.practice.slug', 'idor-access-review-workbench')
            ->assertJsonPath('data.items.0.workspace_query.technology', 'idor-access-control')
            ->assertJsonPath('data.items.0.snippets.0.file', 'app/Services/Practice/IdorAccessReviewService.php')
            ->assertJsonPath('data.items.0.snippets.1.file', 'app/Policies/InvoicePolicy.php')
            ->assertJsonPath('data.items.0.snippets.2.file', 'tests/Feature/IdorAccessReviewWorkbenchTest.php');

        $this->assertStringContainsString(
            'object-level authorization',
            $response->json('data.items.0.snippets.0.code')
        );
        $this->assertStringContainsString(
            'whereKey($invoiceId)->firstOrFail()',
            $response->json('data.items.0.snippets.1.code')
        );
    }

    /**
     * XSS code examples point to the safe escaping preview snippets.
     */
    public function test_xss_code_examples_use_security_escape_snippets(): void
    {
        $this->getJson('/api/practice/technology-code-examples/xss-defense?language=en&search=XSS&limit=3')
            ->assertOk()
            ->assertJsonPath('data.technology', 'xss-defense')
            ->assertJsonPath('data.items.0.practice.slug', 'blade-escaping-xss-preview')
            ->assertJsonPath('data.items.0.snippets.0.file', 'app/Services/Practice/SecurityEscapePreviewService.php')
            ->assertJsonPath('data.items.0.workspace_query.technology', 'xss-defense');
    }

    /**
     * PKCE code examples point to the OAuth planner snippets with verifier drills.
     */
    public function test_pkce_code_examples_use_oauth_planner_snippets(): void
    {
        $response = $this->getJson('/api/practice/technology-code-examples/oauth-flow?language=en&search=PKCE&limit=3');

        $response
            ->assertOk()
            ->assertJsonPath('data.technology', 'oauth-flow')
            ->assertJsonPath('data.items.0.practice.slug', 'oauth-flow-plan-workbench')
            ->assertJsonPath('data.items.0.snippets.0.file', 'app/Services/Practice/OauthFlowPlanService.php')
            ->assertJsonPath('data.items.0.workspace_query.technology', 'oauth-flow');

        $this->assertStringContainsString(
            'pkce_sequence_simulation',
            $response->json('data.items.0.snippets.0.code')
        );
        $this->assertStringContainsString(
            'wrong verifier',
            $response->json('data.items.0.snippets.0.code')
        );
    }

    /**
     * Stack and heap code examples stay in the PHP runtime practice lane.
     */
    public function test_stack_heap_code_examples_use_php_runtime_lane(): void
    {
        $response = $this->getJson('/api/practice/technology-code-examples/php?language=en&search=stack%20memory&limit=3');

        $response
            ->assertOk()
            ->assertJsonPath('data.technology', 'php')
            ->assertJsonPath('data.items.0.practice.slug', 'php-cli-input-normalizer')
            ->assertJsonPath('data.items.0.workspace_query.technology', 'php')
            ->assertJsonPath('data.items.0.task', 'Write a PHP runtime-memory note and small example for: What is the difference between stack memory and heap memory?')
            ->assertJsonPath('data.items.0.snippets.0.file', 'app/Practice/Php/RuntimeMemoryNote.php')
            ->assertJsonPath('data.items.0.snippets.1.file', 'tests/Unit/Practice/RuntimeMemoryNoteTest.php');

        $this->assertStringContainsString(
            'heap-backed data',
            $response->json('data.items.0.snippets.0.code')
        );
        $this->assertStringContainsString(
            'reference counting',
            $response->json('data.items.0.snippets.0.code')
        );
    }

    /**
     * Predictive AI and Generative AI code examples use the AI type comparison snippets.
     */
    public function test_predictive_generative_ai_code_examples_use_ai_type_comparison_snippets(): void
    {
        $response = $this->getJson('/api/practice/technology-code-examples/llm-foundations?language=en&search=Predictive%20AI&limit=3');

        $response
            ->assertOk()
            ->assertJsonPath('data.technology', 'llm-foundations')
            ->assertJsonPath('data.items.0.practice.slug', 'llm-decision-loop-plan-workbench')
            ->assertJsonPath('data.items.0.workspace_query.technology', 'llm-foundations')
            ->assertJsonPath('data.items.0.snippets.0.file', 'app/Services/Practice/AiTypeComparisonPlanService.php')
            ->assertJsonPath('data.items.0.snippets.1.file', 'tests/Feature/AiTypeComparisonPlanTest.php');

        $this->assertStringContainsString(
            'predictive_ai',
            $response->json('data.items.0.snippets.0.code')
        );
        $this->assertStringContainsString(
            'groundedness',
            $response->json('data.items.0.snippets.0.code')
        );
    }

    /**
     * AI agent memory code examples use memory contract snippets.
     */
    public function test_ai_agent_memory_code_examples_use_memory_contract_snippets(): void
    {
        $response = $this->getJson('/api/practice/technology-code-examples/llm-foundations?language=en&search=agent%20memory&limit=3');

        $response
            ->assertOk()
            ->assertJsonPath('data.technology', 'llm-foundations')
            ->assertJsonPath('data.items.0.practice.slug', 'llm-decision-loop-plan-workbench')
            ->assertJsonPath('data.items.0.workspace_query.technology', 'llm-foundations')
            ->assertJsonPath('data.items.0.snippets.0.file', 'app/Services/Practice/AiAgentMemoryPlanService.php')
            ->assertJsonPath('data.items.0.snippets.1.file', 'tests/Feature/AiAgentMemoryPlanTest.php');

        $this->assertStringContainsString(
            'working_memory',
            $response->json('data.items.0.snippets.0.code')
        );
        $this->assertStringContainsString(
            'procedural_memory',
            $response->json('data.items.0.snippets.0.code')
        );
    }

    /**
     * RAG, Long Context, and CAG code examples use chatbot context strategy snippets.
     */
    public function test_rag_context_strategy_code_examples_use_rag_router_snippets(): void
    {
        $response = $this->getJson('/api/practice/technology-code-examples/rag-systems?language=en&search=Long%20Context&limit=3');

        $response
            ->assertOk()
            ->assertJsonPath('data.technology', 'rag-systems')
            ->assertJsonPath('data.items.0.practice.slug', 'rag-strategy-plan-workbench')
            ->assertJsonPath('data.items.0.workspace_query.technology', 'rag-systems')
            ->assertJsonPath('data.items.0.snippets.0.file', 'app/Services/Practice/RagStrategyPlanService.php')
            ->assertJsonPath('data.items.0.snippets.1.file', 'app/Http/Requests/Api/PlanRagStrategyRequest.php')
            ->assertJsonPath('data.items.0.snippets.2.file', 'app/Services/Rag/ChatbotContextRouter.php')
            ->assertJsonPath('data.items.0.snippets.3.file', 'tests/Feature/RagStrategyPlanWorkbenchTest.php');

        $this->assertStringContainsString(
            'context_strategy_plan',
            $response->json('data.items.0.snippets.0.code')
        );
        $this->assertStringContainsString(
            'permission-aware cached context',
            $response->json('data.items.0.snippets.2.code')
        );
    }

    /**
     * JavaScript closure code examples use the closure counter and checklist snippets.
     */
    public function test_javascript_closure_code_examples_use_closure_snippets(): void
    {
        $response = $this->getJson('/api/practice/technology-code-examples/javascript-closures?language=en&search=JavaScript%20closure&limit=3');

        $response
            ->assertOk()
            ->assertJsonPath('data.technology', 'javascript-closures')
            ->assertJsonPath('data.items.0.practice.slug', 'react-render-optimization-workbench')
            ->assertJsonPath('data.items.0.workspace_query.technology', 'javascript-closures')
            ->assertJsonPath('data.items.0.snippets.0.file', 'resources/js/closure-counter.js')
            ->assertJsonPath('data.items.0.snippets.1.file', 'resources/js/closure-interview-checklist.js');

        $this->assertStringContainsString(
            'createCounter',
            $response->json('data.items.0.snippets.0.code')
        );
        $this->assertStringContainsString(
            'lexical scope',
            $response->json('data.items.0.snippets.1.code')
        );
    }

    /**
     * Arrow-function this code examples use lexical-this snippets.
     */
    public function test_arrow_this_code_examples_use_lexical_this_snippets(): void
    {
        $response = $this->getJson('/api/practice/technology-code-examples/javascript-closures?language=en&search=arrow%20function%20this&limit=3');

        $response
            ->assertOk()
            ->assertJsonPath('data.technology', 'javascript-closures')
            ->assertJsonPath('data.items.0.workspace_query.technology', 'javascript-closures')
            ->assertJsonPath('data.items.0.snippets.0.file', 'resources/js/arrow-this-comparison.js')
            ->assertJsonPath('data.items.0.snippets.1.file', 'resources/js/arrow-this-interview-checklist.js');

        $this->assertStringContainsString(
            'normalMethod',
            $response->json('data.items.0.snippets.0.code')
        );
        $this->assertStringContainsString(
            'Normal function this is dynamic',
            $response->json('data.items.0.snippets.1.code')
        );
    }

    /**
     * Covering Index code examples use migration and query-plan review snippets.
     */
    public function test_covering_index_code_examples_use_query_plan_snippets(): void
    {
        $response = $this->getJson('/api/practice/technology-code-examples/database-eloquent?language=en&search=Covering%20Index&limit=3');

        $response
            ->assertOk()
            ->assertJsonPath('data.technology', 'database-eloquent')
            ->assertJsonPath('data.items.0.practice.slug', 'laravel-thin-controller')
            ->assertJsonPath('data.items.0.workspace_query.technology', 'database-eloquent')
            ->assertJsonPath('data.items.0.snippets.0.file', 'database/migrations/xxxx_xx_xx_add_orders_covering_index.php')
            ->assertJsonPath('data.items.0.snippets.1.file', 'app/Services/Practice/CoveringIndexReviewService.php');

        $this->assertStringContainsString(
            'INCLUDE',
            $response->json('data.items.0.snippets.0.code')
        );
        $this->assertStringContainsString(
            'Heap Fetches',
            $response->json('data.items.0.snippets.1.code')
        );
    }

    /**
     * Database locking code examples use transaction and lockForUpdate snippets.
     */
    public function test_database_locking_code_examples_use_transaction_snippets(): void
    {
        $response = $this->getJson('/api/practice/technology-code-examples/database-eloquent?language=en&search=lockForUpdate&limit=3');

        $response
            ->assertOk()
            ->assertJsonPath('data.technology', 'database-eloquent')
            ->assertJsonPath('data.items.0.practice.slug', 'laravel-thin-controller')
            ->assertJsonPath('data.items.0.workspace_query.technology', 'database-eloquent')
            ->assertJsonPath('data.items.0.snippets.0.file', 'app/Services/Practice/InventoryReservationService.php')
            ->assertJsonPath('data.items.0.snippets.1.file', 'tests/Feature/InventoryReservationConcurrencyTest.php');

        $this->assertStringContainsString(
            'DB::transaction',
            $response->json('data.items.0.snippets.0.code')
        );
        $this->assertStringContainsString(
            'lockForUpdate()',
            $response->json('data.items.0.snippets.0.code')
        );
    }

    /**
     * BFS and DFS code examples use graph traversal planning snippets.
     */
    public function test_bfs_dfs_code_examples_use_graph_traversal_snippets(): void
    {
        $response = $this->getJson('/api/practice/technology-code-examples/graph-traversal?language=en&search=BFS%20DFS&limit=3');

        $response
            ->assertOk()
            ->assertJsonPath('data.technology', 'graph-traversal')
            ->assertJsonPath('data.items.0.practice.slug', 'graph-traversal-plan-workbench')
            ->assertJsonPath('data.items.0.workspace_query.technology', 'graph-traversal')
            ->assertJsonPath('data.items.0.snippets.0.file', 'app/Services/Practice/GraphTraversalPlanService.php')
            ->assertJsonPath('data.items.0.snippets.1.file', 'tests/Unit/Practice/GraphTraversalPlanServiceTest.php');

        $this->assertStringContainsString(
            'visited set',
            $response->json('data.items.0.snippets.0.code')
        );
        $this->assertStringContainsString(
            "'bfs'",
            $response->json('data.items.0.snippets.0.code')
        );
    }
}
