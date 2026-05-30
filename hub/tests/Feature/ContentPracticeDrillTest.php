<?php

declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;

final class ContentPracticeDrillTest extends TestCase
{
    /**
     * The content drill page turns one source record into concrete coding instructions.
     */
    public function test_content_practice_drill_page_renders_coding_instructions(): void
    {
        $response = $this->get('/practice/content-drill?source_key=laravel-api-integration-en-json&technology=api-validation');

        $response
            ->assertOk()
            ->assertSee('Content Drill')
            ->assertSee('laravel/api-integration.en.json')
            ->assertSee('Create a Form Request and API response')
            ->assertSee('Implementation Steps')
            ->assertSee('Files')
            ->assertSee('Starter Code')
            ->assertSee('Runnable workbench')
            ->assertSee('Open API validation workbench')
            ->assertSee('ContentBackedApiDrillController')
            ->assertSee('ContentBackedApiDrillService')
            ->assertSee('StoreContentBackedDrillRequest')
            ->assertSee('Open starter kit');
    }

    /**
     * The content drill API returns source, practice, steps, files, and commands.
     */
    public function test_content_practice_drill_api_returns_drill_plan(): void
    {
        $response = $this->getJson('/api/practice/content-drill?record_id=laravel-api-integration-en-json-item-1&technology=api-validation');

        $response
            ->assertOk()
            ->assertJsonPath('data.technology', 'api-validation')
            ->assertJsonPath('data.source.path', 'laravel/api-integration.en.json')
            ->assertJsonPath('data.content.title', '1. What is an API in a Laravel context?')
            ->assertJsonPath('data.practice.slug', 'api-form-request-slice')
            ->assertJsonStructure([
                'data' => [
                    'title',
                    'technology',
                    'source',
                    'content',
                    'practice',
                    'goal',
                    'implementation_steps',
                    'files',
                    'related_workbench',
                    'starter_snippets',
                    'commands',
                    'acceptance',
                ],
            ]);

        $response
            ->assertJsonPath('data.starter_snippets.0.file', 'routes/api/practice-actions.php')
            ->assertJsonPath('data.starter_snippets.3.file', 'app/Http/Controllers/Api/ContentBackedApiDrillController.php')
            ->assertJsonPath('data.starter_snippets.4.file', 'app/Services/Practice/ContentBackedApiDrillService.php')
            ->assertJsonPath('data.related_workbench.path', '/workbench/topic-intake')
            ->assertJsonPath('data.commands.0', 'php artisan test --filter ContentBackedDrill');
    }

    /**
     * Exact record links from quiz cards should not be blocked by default Laravel API filters.
     */
    public function test_content_practice_drill_exact_record_lookup_ignores_default_filters(): void
    {
        $response = $this->getJson('/api/practice/content-drill?record_id=php-starter-en-json-phase-topic-1&source_key=php-starter-en-json');

        $response
            ->assertOk()
            ->assertJsonPath('data.technology', 'php')
            ->assertJsonPath('data.source.path', 'php/starter.en.json')
            ->assertJsonStructure([
                'data' => [
                    'starter_snippets' => [
                        '*' => [
                            'label',
                            'file',
                            'code',
                        ],
                    ],
                ],
            ]);
    }

    /**
     * Container architecture content links to the layered architecture workbench and exercise.
     */
    public function test_container_architecture_drill_links_to_layered_architecture_workbench(): void
    {
        $response = $this->getJson('/api/practice/content-drill?record_id=laravel-container-architecture-en-json-item-90&source_key=laravel-container-architecture-en-json');

        $response
            ->assertOk()
            ->assertJsonPath('data.technology', 'container-architecture')
            ->assertJsonPath('data.practice.slug', 'clean-architecture-layering-tradeoff')
            ->assertJsonPath('data.related_workbench.path', '/workbench/layered-architecture-decision')
            ->assertJsonPath('data.related_workbench.route_name', 'practice.workbench.layered-architecture-decision');
    }

    /**
     * System and auth topics route content drills to their specialized workbenches.
     */
    public function test_specialized_system_topics_link_to_matching_workbenches(): void
    {
        $cases = [
            [
                '/api/practice/content-drill?source_key=interview-devops-en-json&search=load%20balancer%20algorithms',
                'load-balancing',
                'load-balancer-algorithm-plan-workbench',
                '/workbench/load-balancer-plan',
            ],
            [
                '/api/practice/content-drill?source_key=interview-devops-en-json&search=reverse%20proxy%20outage',
                'reverse-proxy-edge',
                'reverse-proxy-failure-plan-workbench',
                '/workbench/reverse-proxy-failure-plan',
            ],
            [
                '/api/practice/content-drill?source_key=interview-devops-en-json&search=SIEM',
                'siem-elk-observability',
                'siem-elk-plan-workbench',
                '/workbench/siem-elk-plan',
            ],
            [
                '/api/practice/content-drill?source_key=interview-devops-en-json&search=Kubernetes%20in%20one%20minute',
                'kubernetes-orchestration',
                'kubernetes-analogy-plan-workbench',
                '/workbench/kubernetes-analogy-plan',
            ],
            [
                '/api/practice/content-drill?source_key=laravel-auth-security-en-json&search=localStorage',
                'jwt-token-storage',
                'jwt-token-storage-plan-workbench',
                '/workbench/jwt-token-storage-plan',
            ],
            [
                '/api/practice/content-drill?source_key=laravel-auth-security-en-json&search=JWT%20is%20stateless',
                'jwt-revocation',
                'jwt-revocation-plan-workbench',
                '/workbench/jwt-revocation-plan',
            ],
            [
                '/api/practice/content-drill?source_key=laravel-auth-security-en-json&search=Implicit%20Flow',
                'oauth-flow',
                'oauth-flow-plan-workbench',
                '/workbench/oauth-flow-plan',
            ],
            [
                '/api/practice/content-drill?source_key=laravel-auth-security-en-json&search=Client%20Credentials',
                'oauth-flow',
                'oauth-flow-plan-workbench',
                '/workbench/oauth-flow-plan',
            ],
            [
                '/api/practice/content-drill?source_key=laravel-auth-security-en-json&search=PKCE',
                'oauth-flow',
                'oauth-flow-plan-workbench',
                '/workbench/oauth-flow-plan',
            ],
            [
                '/api/practice/content-drill?source_key=laravel-auth-security-en-json&search=SQL%20Injection',
                'sql-injection-defense',
                'sql-injection-defense-plan-workbench',
                '/workbench/sql-injection-defense-plan',
            ],
            [
                '/api/practice/content-drill?source_key=laravel-auth-security-en-json&search=CSRF%20Protection',
                'csrf-protection',
                'csrf-protection-plan-workbench',
                '/workbench/csrf-protection-plan',
            ],
            [
                '/api/practice/content-drill?source_key=laravel-auth-security-en-json&search=XSS',
                'xss-defense',
                'blade-escaping-xss-preview',
                '/workbench/security-escape-preview',
            ],
            [
                '/api/practice/content-drill?source_key=laravel-performance-search-en-json&search=LSM%20Tree',
                'lsm-tree-storage',
                'lsm-tree-plan-workbench',
                '/workbench/lsm-tree-plan',
            ],
            [
                '/api/practice/content-drill?source_key=vibe-coding-prompting-en-json&search=large%20language%20models',
                'llm-foundations',
                'llm-decision-loop-plan-workbench',
                '/workbench/llm-decision-loop-plan',
            ],
            [
                '/api/practice/content-drill?source_key=vibe-coding-prompting-en-json&search=Graph%20RAG',
                'rag-systems',
                'rag-strategy-plan-workbench',
                '/workbench/rag-strategy-plan',
            ],
            [
                '/api/practice/content-drill?source_key=interview-devops-en-json&search=Cloud%20Engineer%20candidate%20really%20uses%20AI',
                'ai-cloud-interview',
                'ai-cloud-interview-rubric-workbench',
                '/workbench/ai-cloud-interview-rubric',
            ],
            [
                '/api/practice/content-drill?source_key=laravel-api-integration-en-json&search=GraphQL%20vs%20REST',
                'graphql-api',
                'graphql-rest-decision-workbench',
                '/workbench/graphql-rest-decision',
            ],
            [
                '/api/practice/content-drill?source_key=laravel-frontend-en-json&search=React%20re-renders',
                'react-render-performance',
                'react-render-optimization-workbench',
                '/workbench/react-render-optimization-plan',
            ],
            [
                '/api/practice/content-drill?source_key=laravel-frontend-en-json&search=JavaScript%20closure',
                'javascript-closures',
                'react-render-optimization-workbench',
                '/workbench/react-render-optimization-plan',
            ],
            [
                '/api/practice/content-drill?source_key=interview-senior-en-json&search=technically%20correct%20System%20Design',
                'system-design-tradeoffs',
                'system-design-tradeoff-plan-workbench',
                '/workbench/system-design-tradeoff-plan',
            ],
        ];

        foreach ($cases as [$url, $technology, $slug, $path]) {
            $response = $this->getJson($url);

            $response
                ->assertOk()
                ->assertJsonPath('data.technology', $technology)
                ->assertJsonPath('data.practice.slug', $slug)
                ->assertJsonPath('data.related_workbench.path', $path);
        }
    }

    /**
     * CSRF content drills include token, SameSite, method, and stale-token implementation guidance.
     */
    public function test_csrf_content_drill_returns_security_specific_steps_and_files(): void
    {
        $this->getJson('/api/practice/content-drill?source_key=laravel-auth-security-en-json&search=CSRF%20Protection')
            ->assertOk()
            ->assertJsonPath('data.technology', 'csrf-protection')
            ->assertJsonPath('data.implementation_steps.0', 'Identify the browser flow where a logged-in user can trigger a state-changing request.')
            ->assertJsonPath('data.implementation_steps.1', 'Add token validation, SameSite cookie review, and safe HTTP method boundaries for that flow.')
            ->assertJsonPath('data.implementation_steps.2', 'Write missing-token, stale-token, and cross-site request tests before accepting the defense.')
            ->assertJsonPath('data.files.0', 'app/Services/Practice/CsrfProtectionPlanService.php')
            ->assertJsonPath('data.files.1', 'app/Http/Requests/Api/PlanCsrfProtectionRequest.php')
            ->assertJsonPath('data.files.2', 'resources/views/practice/workbench/csrf-protection-plan.blade.php')
            ->assertJsonPath('data.files.3', 'tests/Feature/CsrfProtectionPlanWorkbenchTest.php');
    }

    /**
     * PKCE content drills include verifier, challenge, callback, and replay guidance.
     */
    public function test_pkce_content_drill_returns_verifier_specific_steps_and_acceptance(): void
    {
        $this->getJson('/api/practice/content-drill?source_key=laravel-auth-security-en-json&search=PKCE')
            ->assertOk()
            ->assertJsonPath('data.technology', 'oauth-flow')
            ->assertJsonPath('data.practice.slug', 'oauth-flow-plan-workbench')
            ->assertJsonPath('data.implementation_steps.0', 'Generate a high-entropy code_verifier and derive an S256 code_challenge for one login attempt.')
            ->assertJsonPath('data.implementation_steps.1', 'Build the authorize request with response_type=code, state, redirect_uri, code_challenge, and code_challenge_method=S256.')
            ->assertJsonPath('data.implementation_steps.2', 'Validate callback state, reject token-bearing callbacks, exchange code plus code_verifier, then clear verifier state.')
            ->assertJsonPath('data.implementation_steps.3', 'Add tests for missing verifier, wrong verifier, reused authorization code, and state mismatch.')
            ->assertJsonPath('data.acceptance.5', 'The PKCE verifier is never logged, rendered, or persisted beyond the login attempt.')
            ->assertJsonPath('data.acceptance.6', 'The token exchange fails when code_verifier is missing, wrong, expired, or reused.')
            ->assertJsonPath('data.acceptance.7', 'The callback rejects access_token, id_token, token_type, or expires_in in URL input.')
            ->assertJsonPath('data.starter_snippets.0.file', 'app/Services/Practice/OauthFlowPlanService.php');
    }

    /**
     * Stack versus heap content drills return PHP runtime-memory steps and acceptance checks.
     */
    public function test_stack_heap_content_drill_returns_runtime_memory_steps_and_acceptance(): void
    {
        $this->getJson('/api/practice/content-drill?source_key=php-advanced-en-json&search=stack%20memory')
            ->assertOk()
            ->assertJsonPath('data.technology', 'php')
            ->assertJsonPath('data.practice.slug', 'php-cli-input-normalizer')
            ->assertJsonPath('data.related_workbench.path', '/workbench/name-normalizer')
            ->assertJsonPath('data.implementation_steps.0', 'Trace the call stack: function call, parameters, local variables, return value, and frame cleanup.')
            ->assertJsonPath('data.implementation_steps.1', 'Identify heap-backed data such as arrays, objects, strings, object graphs, and references.')
            ->assertJsonPath('data.implementation_steps.4', 'Name one practical failure mode: deep recursion, large arrays, retained references, or long-running worker memory.')
            ->assertJsonPath('data.acceptance.5', 'The answer does not claim PHP developers manually allocate stack or heap memory in normal application code.')
            ->assertJsonPath('data.acceptance.6', 'The explanation distinguishes stack call frames from heap-backed array, object, and string lifetime.')
            ->assertJsonPath('data.acceptance.7', 'The drill names at least one PHP memory risk: deep recursion, large arrays, retained references, or long-running worker state.');
    }

    /**
     * Predictive AI versus Generative AI content drills separate output contracts and evaluation.
     */
    public function test_predictive_generative_ai_content_drill_returns_ai_type_steps_and_acceptance(): void
    {
        $this->getJson('/api/practice/content-drill?source_key=vibe-coding-prompting-en-json&search=Predictive%20AI')
            ->assertOk()
            ->assertJsonPath('data.technology', 'llm-foundations')
            ->assertJsonPath('data.practice.slug', 'llm-decision-loop-plan-workbench')
            ->assertJsonPath('data.related_workbench.path', '/workbench/llm-decision-loop-plan')
            ->assertJsonPath('data.implementation_steps.0', 'Define the output contract first: score, label, forecast, or ranking for Predictive AI; generated text, image, code, summary, or answer for Generative AI.')
            ->assertJsonPath('data.implementation_steps.2', 'Choose separate evaluation evidence: precision, recall, calibration, AUC, error rate, or business lift for prediction; groundedness, safety, citations, tests, human review, and output constraints for generation.')
            ->assertJsonPath('data.implementation_steps.4', 'Use the LLM decision-loop workbench to explain where probability, feedback, reward, and verification fit without mixing the two output contracts.')
            ->assertJsonPath('data.acceptance.5', 'The answer separates Predictive AI output contracts from Generative AI output contracts.')
            ->assertJsonPath('data.acceptance.6', 'The evaluation plan names at least two predictive metrics and two generative quality checks.')
            ->assertJsonPath('data.acceptance.7', 'The drill names failure modes for both sides instead of treating all AI risk as hallucination.');
    }

    /**
     * AI agent memory drills separate working, episodic, semantic, and procedural memory.
     */
    public function test_ai_agent_memory_content_drill_returns_memory_contract_steps(): void
    {
        $this->getJson('/api/practice/content-drill?source_key=vibe-coding-prompting-en-json&search=agent%20memory')
            ->assertOk()
            ->assertJsonPath('data.technology', 'llm-foundations')
            ->assertJsonPath('data.practice.slug', 'llm-decision-loop-plan-workbench')
            ->assertJsonPath('data.related_workbench.path', '/workbench/ai-agent-memory-plan')
            ->assertJsonPath('data.implementation_steps.0', 'Classify memory into four separate contracts: working memory, episodic memory, semantic memory, and procedural memory.')
            ->assertJsonPath('data.implementation_steps.3', 'Add guardrails for source, freshness, confidence, permission scope, private context, and stale-memory fallback.')
            ->assertJsonPath('data.files.0', 'app/Services/Practice/AiAgentMemoryPlanService.php')
            ->assertJsonPath('data.files.4', 'resources/views/practice/workbench/ai-agent-memory-plan.blade.php')
            ->assertJsonPath('data.starter_snippets.0.file', 'app/Services/Practice/AiAgentMemoryPlanService.php')
            ->assertJsonPath('data.acceptance.5', 'The answer separates working, episodic, semantic, and procedural memory instead of treating memory as one prompt bucket.');
    }

    /**
     * RAG context-strategy drills return Long Context, CAG, and hybrid routing guidance.
     */
    public function test_rag_context_strategy_content_drill_returns_context_routing_steps_and_files(): void
    {
        $this->getJson('/api/practice/content-drill?source_key=vibe-coding-prompting-en-json&search=Long%20Context')
            ->assertOk()
            ->assertJsonPath('data.technology', 'rag-systems')
            ->assertJsonPath('data.practice.slug', 'rag-strategy-plan-workbench')
            ->assertJsonPath('data.related_workbench.path', '/workbench/rag-strategy-plan')
            ->assertJsonPath('data.implementation_steps.0', 'Classify the selected topic as RAG, Long Context, CAG, or hybrid context routing before choosing a RAG pattern.')
            ->assertJsonPath('data.implementation_steps.1', 'Define the context contract: retrieval chunks, packed documents, cached context, metadata, source IDs, freshness, permissions, and citations.')
            ->assertJsonPath('data.files.2', 'app/Services/Rag/ChatbotContextRouter.php')
            ->assertJsonPath('data.starter_snippets.2.file', 'app/Services/Rag/ChatbotContextRouter.php');
    }

    /**
     * Covering Index drills return plan-reading, INCLUDE, VACUUM, and bloat guidance.
     */
    public function test_covering_index_content_drill_returns_query_plan_steps_and_acceptance(): void
    {
        $this->getJson('/api/practice/content-drill?source_key=laravel-performance-search-en-json&search=Covering%20Index')
            ->assertOk()
            ->assertJsonPath('data.technology', 'database-eloquent')
            ->assertJsonPath('data.practice.slug', 'laravel-thin-controller')
            ->assertJsonPath('data.related_workbench.path', '/workbench/collection-filter-preview')
            ->assertJsonPath('data.implementation_steps.0', 'Capture the current query with selected columns, filters, ordering, limit, and realistic row count before changing indexes.')
            ->assertJsonPath('data.implementation_steps.1', 'Run `EXPLAIN (ANALYZE, BUFFERS)` and record whether the plan uses Index Scan, Index Only Scan, heap reads, and `Heap Fetches`.')
            ->assertJsonPath('data.implementation_steps.2', 'Design the covering index with key columns for filter/order and `INCLUDE` only for returned columns needed by the hot query.')
            ->assertJsonPath('data.implementation_steps.3', 'Check visibility-map health through VACUUM/autovacuum behavior before assuming Index Only Scan will avoid heap fetches.')
            ->assertJsonPath('data.acceptance.5', 'The plan proves whether `Heap Fetches` dropped after the covering index.')
            ->assertJsonPath('data.acceptance.6', 'The `INCLUDE` columns are limited to fields returned by the hot query, not every convenient column.')
            ->assertJsonPath('data.acceptance.7', 'The release notes mention visibility map, VACUUM/autovacuum health, index bloat, write overhead, and rollback.')
            ->assertJsonPath('data.starter_snippets.0.file', 'database/migrations/xxxx_xx_xx_add_orders_covering_index.php');
    }

    /**
     * Database locking drills return transaction, row-lock, and contention guidance.
     */
    public function test_database_locking_content_drill_returns_concurrency_steps_and_acceptance(): void
    {
        $this->getJson('/api/practice/content-drill?source_key=laravel-data-en-json&search=lockForUpdate')
            ->assertOk()
            ->assertJsonPath('data.technology', 'database-eloquent')
            ->assertJsonPath('data.practice.slug', 'laravel-thin-controller')
            ->assertJsonPath('data.related_workbench.path', '/workbench/database-locking-plan')
            ->assertJsonPath('data.implementation_steps.0', 'Name the protected invariant first: no negative stock, no double-spent balance, or no duplicate terminal workflow state.')
            ->assertJsonPath('data.implementation_steps.2', 'Call `lockForUpdate()` on a selective, indexed row lookup before checking and mutating the protected value.')
            ->assertJsonPath('data.acceptance.5', 'The drill states the exact invariant being protected before introducing `lockForUpdate()`.')
            ->assertJsonPath('data.acceptance.7', 'The plan documents indexed lookup, short lock window, deadlock or timeout behavior, hot-row contention, and lock-wait monitoring.')
            ->assertJsonPath('data.starter_snippets.0.file', 'app/Services/Practice/InventoryReservationService.php');
    }

    /**
     * BFS and DFS drills link to the dedicated graph traversal workbench files.
     */
    public function test_graph_traversal_content_drill_returns_workbench_files(): void
    {
        $this->getJson('/api/practice/content-drill?source_key=laravel-performance-search-en-json&search=BFS%20DFS')
            ->assertOk()
            ->assertJsonPath('data.technology', 'graph-traversal')
            ->assertJsonPath('data.practice.slug', 'graph-traversal-plan-workbench')
            ->assertJsonPath('data.related_workbench.path', '/workbench/graph-traversal-plan')
            ->assertJsonPath('data.files.0', 'app/Services/Practice/GraphTraversalPlanService.php')
            ->assertJsonPath('data.files.1', 'app/Http/Requests/Api/PlanGraphTraversalRequest.php')
            ->assertJsonPath('data.files.2', 'resources/views/practice/workbench/graph-traversal-plan.blade.php')
            ->assertJsonPath('data.files.3', 'tests/Feature/GraphTraversalPlanWorkbenchTest.php')
            ->assertJsonPath('data.files.4', 'tests/Unit/Practice/GraphTraversalPlanServiceTest.php');
    }

    /**
     * JavaScript closure drills return lexical-scope examples and interview checks.
     */
    public function test_javascript_closure_content_drill_returns_closure_steps_and_files(): void
    {
        $this->getJson('/api/practice/content-drill?source_key=laravel-frontend-en-json&search=JavaScript%20closure')
            ->assertOk()
            ->assertJsonPath('data.technology', 'javascript-closures')
            ->assertJsonPath('data.practice.slug', 'react-render-optimization-workbench')
            ->assertJsonPath('data.related_workbench.path', '/workbench/react-render-optimization-plan')
            ->assertJsonPath('data.implementation_steps.0', 'Define the lexical scope that creates the closure and name the captured variables.')
            ->assertJsonPath('data.implementation_steps.1', 'Write a small function factory example such as createCounter() that proves the inner function keeps state.')
            ->assertJsonPath('data.implementation_steps.3', 'Add interview checks for var versus let in loops, stale closures, and why the variable binding stays reachable.')
            ->assertJsonPath('data.files.0', 'resources/js/closure-counter.js')
            ->assertJsonPath('data.starter_snippets.0.file', 'resources/js/closure-counter.js');
    }

    /**
     * JavaScript hoisting drills link to the dedicated hoisting lab.
     */
    public function test_javascript_hoisting_content_drill_returns_hoisting_lab_link(): void
    {
        $this->getJson('/api/practice/content-drill?source_key=laravel-frontend-en-json&search=JavaScript%20hoisting')
            ->assertOk()
            ->assertJsonPath('data.technology', 'javascript-closures')
            ->assertJsonPath('data.related_workbench.path', '/workbench/javascript-hoisting-lab')
            ->assertJsonPath('data.related_workbench.route_name', 'practice.workbench.javascript-hoisting-lab')
            ->assertJsonPath('data.starter_snippets.0.file', 'resources/js/hoisting-trace.js');
    }

    /**
     * Arrow-function this drills return lexical-this steps, files, and acceptance checks.
     */
    public function test_arrow_this_content_drill_returns_lexical_this_steps_and_files(): void
    {
        $this->getJson('/api/practice/content-drill?source_key=laravel-frontend-en-json&search=arrow%20function%20this')
            ->assertOk()
            ->assertJsonPath('data.technology', 'javascript-closures')
            ->assertJsonPath('data.implementation_steps.0', 'Mark the scope where the arrow function is created and identify which `this` value it captures.')
            ->assertJsonPath('data.implementation_steps.1', 'Compare a normal object method with an arrow object method to show why call-site `this` and lexical `this` differ.')
            ->assertJsonPath('data.implementation_steps.3', 'Write the interview trap notes: obj.arrow() does not bind `this` to obj, and call/apply/bind cannot rebind arrow `this`.')
            ->assertJsonPath('data.files.0', 'resources/js/arrow-this-comparison.js')
            ->assertJsonPath('data.files.1', 'resources/js/arrow-this-interview-checklist.js')
            ->assertJsonPath('data.related_workbench.path', '/workbench/javascript-arrow-this-lab')
            ->assertJsonPath('data.related_workbench.route_name', 'practice.workbench.javascript-arrow-this-lab')
            ->assertJsonPath('data.starter_snippets.0.file', 'resources/js/arrow-this-comparison.js')
            ->assertJsonPath('data.acceptance.5', 'The comparison shows normal function `this` comes from the call site while arrow `this` comes from lexical scope.')
            ->assertJsonPath('data.acceptance.7', 'The answer states that call(), apply(), and bind() cannot rebind arrow `this`.');
    }

    /**
     * Broken authentication drills return lifecycle, control, and failure-test steps.
     */
    public function test_broken_authentication_content_drill_returns_lifecycle_steps_and_files(): void
    {
        $this->getJson('/api/practice/content-drill?source_key=laravel-auth-security-en-json&search=Broken%20Authentication')
            ->assertOk()
            ->assertJsonPath('data.technology', 'auth-security')
            ->assertJsonPath('data.content.title', '114. Common Broken Authentication Mistakes')
            ->assertJsonPath('data.implementation_steps.0', 'Map the authentication lifecycle: login, session creation, remember-me, password reset, MFA recovery, logout, token refresh, and revocation.')
            ->assertJsonPath('data.implementation_steps.2', 'Add Laravel controls for the selected risk: session regeneration, RateLimiter, reset-token expiry, secure cookie flags, token rotation, or session invalidation.')
            ->assertJsonPath('data.implementation_steps.4', 'Prepare a two-minute interview answer that separates authentication lifecycle from authorization decisions.')
            ->assertJsonPath('data.files.0', 'app/Services/Practice/BrokenAuthenticationReviewService.php')
            ->assertJsonPath('data.files.3', 'tests/Feature/Auth/BrokenAuthenticationLifecycleTest.php')
            ->assertJsonPath('data.acceptance.5', 'The drill reviews authentication as a lifecycle, not only a successful login form.')
            ->assertJsonPath('data.acceptance.7', 'The answer clearly separates authentication from authorization and names one logging or monitoring signal for suspicious authentication behavior.');
    }

    /**
     * The content drill API returns 404 when no source matches.
     */
    public function test_content_practice_drill_api_returns_not_found_for_unknown_source(): void
    {
        $response = $this->getJson('/api/practice/content-drill?source_key=missing-source');

        $response->assertNotFound();
    }
}
