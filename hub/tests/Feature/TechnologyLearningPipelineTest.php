<?php

declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;

final class TechnologyLearningPipelineTest extends TestCase
{
    /**
     * The technology learning pipeline page renders ordered stage links.
     */
    public function test_technology_learning_pipeline_page_renders_full_stage_flow(): void
    {
        $response = $this->get('/practice/technology-learning-pipeline/api-validation?family=laravel&language=en&search=api&limit=3');

        $response
            ->assertOk()
            ->assertSee('Technology Learning Pipeline: api-validation')
            ->assertSee('Open API validation workbench')
            ->assertSee('Runnable workbench')
            ->assertSee('Code examples')
            ->assertSee('Implementation lab')
            ->assertSee('Evidence archive')
            ->assertSee('Sample Records')
            ->assertSee('Open pipeline API');
    }

    /**
     * The technology learning pipeline API returns stage routes and progress payload.
     */
    public function test_technology_learning_pipeline_api_returns_stages_and_progress_payload(): void
    {
        $response = $this->getJson('/api/practice/technology-learning-pipeline/api-validation?family=laravel&language=en&search=api&limit=3');

        $response
            ->assertOk()
            ->assertJsonPath('data.technology', 'api-validation')
            ->assertJsonPath('data.practice.slug', 'api-form-request-slice')
            ->assertJsonPath('data.related_workbench.path', '/workbench/topic-intake')
            ->assertJsonPath('data.focus_checks.0', 'Start from source records before changing Laravel code.')
            ->assertJsonPath('data.stages.0.label', 'Runnable workbench')
            ->assertJsonPath('data.stages.0.route', '/workbench/topic-intake?family=laravel&language=en&search=api&limit=3')
            ->assertJsonPath('data.stages.0.api_route', null)
            ->assertJsonPath('data.stages.1.label', 'Code examples')
            ->assertJsonPath('data.stages.11.label', 'Evidence archive')
            ->assertJsonPath('data.progress_payload.items.0.label', 'Runnable workbench')
            ->assertJsonStructure([
                'data' => [
                    'title',
                    'technology',
                    'practice',
                    'related_workbench',
                    'record_count',
                    'focus_checks',
                    'sample_records',
                    'stages' => [
                        '*' => [
                            'step',
                            'label',
                            'purpose',
                            'route',
                            'api_route',
                        ],
                    ],
                    'progress_payload',
                    'meta',
                ],
            ]);
    }

    /**
     * Security learning pipelines start from their dedicated defense workbench.
     */
    public function test_security_learning_pipelines_link_to_dedicated_workbenches(): void
    {
        $cases = [
            [
                '/api/practice/technology-learning-pipeline/sql-injection-defense?language=en&search=SQL%20Injection&limit=3',
                'sql-injection-defense',
                'sql-injection-defense-plan-workbench',
                '/workbench/sql-injection-defense-plan',
                '/workbench/sql-injection-defense-plan?family=laravel&language=en&search=SQL+Injection&limit=3',
            ],
            [
                '/api/practice/technology-learning-pipeline/csrf-protection?language=en&search=CSRF&limit=3',
                'csrf-protection',
                'csrf-protection-plan-workbench',
                '/workbench/csrf-protection-plan',
                '/workbench/csrf-protection-plan?family=laravel&language=en&search=CSRF&limit=3',
            ],
            [
                '/api/practice/technology-learning-pipeline/xss-defense?language=en&search=XSS&limit=3',
                'xss-defense',
                'blade-escaping-xss-preview',
                '/workbench/security-escape-preview',
                '/workbench/security-escape-preview?family=laravel&language=en&search=XSS&limit=3',
            ],
            [
                '/api/practice/technology-learning-pipeline/idor-access-control?language=en&search=IDOR&limit=3',
                'idor-access-control',
                'idor-access-review-workbench',
                '/workbench/idor-access-review',
                '/workbench/idor-access-review?family=laravel&language=en&search=IDOR&limit=3',
            ],
            [
                '/api/practice/technology-learning-pipeline/security-misconfiguration?language=en&search=Security%20Misconfiguration&limit=3',
                'security-misconfiguration',
                'authorization-policy-plan-workbench',
                '/practice/configuration-readiness',
                '/practice/configuration-readiness?family=laravel&language=en&search=Security+Misconfiguration&limit=3',
            ],
        ];

        foreach ($cases as [$url, $technology, $slug, $workbench, $stageRoute]) {
            $this->getJson($url)
                ->assertOk()
                ->assertJsonPath('data.technology', $technology)
                ->assertJsonPath('data.practice.slug', $slug)
                ->assertJsonPath('data.related_workbench.path', $workbench)
                ->assertJsonPath('data.stages.0.label', 'Runnable workbench')
                ->assertJsonPath('data.stages.0.route', $stageRoute);
        }
    }

    /**
     * IDOR learning pipelines expose object-level authorization focus checks.
     */
    public function test_idor_learning_pipeline_exposes_object_authorization_focus_checks(): void
    {
        $this->getJson('/api/practice/technology-learning-pipeline/idor-access-control?language=en&search=IDOR&limit=3')
            ->assertOk()
            ->assertJsonPath('data.technology', 'idor-access-control')
            ->assertJsonPath('data.focus_checks.0', 'Start by listing object IDs in route parameters, nested resources, downloads, exports, batch actions, and signed URL generation.')
            ->assertJsonPath('data.focus_checks.2', 'Add two-user or two-tenant tests for read, update, delete, download, and export before promoting the topic.')
            ->assertJsonPath('data.stages.0.route', '/workbench/idor-access-review?family=laravel&language=en&search=IDOR&limit=3');
    }

    /**
     * OAuth learning pipelines expose PKCE-specific focus checks.
     */
    public function test_oauth_learning_pipeline_exposes_pkce_focus_checks(): void
    {
        $this->get('/practice/technology-learning-pipeline/oauth-flow?language=en&search=PKCE&limit=3')
            ->assertOk()
            ->assertSee('Focus Checks')
            ->assertSee('Use Authorization Code with PKCE for public clients and derive code_challenge with S256.');

        $this->getJson('/api/practice/technology-learning-pipeline/oauth-flow?language=en&search=PKCE&limit=3')
            ->assertOk()
            ->assertJsonPath('data.technology', 'oauth-flow')
            ->assertJsonPath('data.related_workbench.path', '/workbench/oauth-flow-plan')
            ->assertJsonPath('data.focus_checks.0', 'Classify the client as public or confidential before choosing the OAuth flow.')
            ->assertJsonPath('data.focus_checks.2', 'Keep code_verifier private until token exchange and clear it after use.');
    }

    /**
     * Security Misconfiguration learning pipelines expose production-readiness focus checks.
     */
    public function test_security_misconfiguration_learning_pipeline_exposes_configuration_focus_checks(): void
    {
        $this->getJson('/api/practice/technology-learning-pipeline/security-misconfiguration?language=en&search=Security%20Misconfiguration&limit=3')
            ->assertOk()
            ->assertJsonPath('data.technology', 'security-misconfiguration')
            ->assertJsonPath('data.related_workbench.path', '/practice/configuration-readiness')
            ->assertJsonPath('data.focus_checks.0', 'Start by separating local-safe defaults from production release blockers.')
            ->assertJsonPath('data.focus_checks.1', 'Check debug mode, secret exposure, public storage, CORS, security headers, cookie flags, HTTPS, and trusted proxies.')
            ->assertJsonPath('data.focus_checks.2', 'Use fail-closed smoke checks with owners and rollback notes before promoting the topic.');
    }

    /**
     * Broken Authentication learning pipelines expose lifecycle-level focus checks.
     */
    public function test_broken_authentication_learning_pipeline_exposes_lifecycle_focus_checks(): void
    {
        $this->getJson('/api/practice/technology-learning-pipeline/auth-security?language=en&search=Broken%20Authentication&limit=3')
            ->assertOk()
            ->assertJsonPath('data.technology', 'auth-security')
            ->assertJsonPath('data.practice.slug', 'authorization-policy-plan-workbench')
            ->assertJsonPath('data.related_workbench.path', '/workbench/authorization-policy-plan')
            ->assertJsonPath('data.focus_checks.0', 'Map the authentication lifecycle before discussing the login form.')
            ->assertJsonPath('data.focus_checks.1', 'Check session regeneration, login throttling, reset-token expiry, remember-me storage, MFA recovery, logout, token refresh, and revocation.')
            ->assertJsonPath('data.focus_checks.2', 'Require tests for brute force, stale reset token, old session reuse, revoked token reuse, and suspicious-login logging.');
    }

    /**
     * JavaScript closure learning pipelines expose lexical-scope focus checks.
     */
    public function test_javascript_closure_learning_pipeline_exposes_scope_focus_checks(): void
    {
        $this->getJson('/api/practice/technology-learning-pipeline/javascript-closures?language=en&search=JavaScript%20closure&limit=3')
            ->assertOk()
            ->assertJsonPath('data.technology', 'javascript-closures')
            ->assertJsonPath('data.related_workbench.path', '/workbench/react-render-optimization-plan')
            ->assertJsonPath('data.focus_checks.0', 'Start by identifying the outer scope, inner function, and captured variable bindings.')
            ->assertJsonPath('data.focus_checks.1', 'Use createCounter() or a function factory to prove state persists across repeated calls.')
            ->assertJsonPath('data.focus_checks.2', 'Review var versus let loop behavior and stale closures in async callbacks, timers, event handlers, or hooks.');
    }

    /**
     * Arrow-function this learning pipelines expose lexical-this focus checks.
     */
    public function test_arrow_this_learning_pipeline_exposes_lexical_this_focus_checks(): void
    {
        $this->getJson('/api/practice/technology-learning-pipeline/javascript-closures?language=en&search=arrow%20function%20this&limit=3')
            ->assertOk()
            ->assertJsonPath('data.technology', 'javascript-closures')
            ->assertJsonPath('data.related_workbench.path', '/workbench/react-render-optimization-plan')
            ->assertJsonPath('data.focus_checks.0', 'Start by naming the scope where the arrow function is created; that scope supplies lexical `this`.')
            ->assertJsonPath('data.focus_checks.1', 'Compare one normal object method with one arrow object method so the call-site difference is visible.')
            ->assertJsonPath('data.focus_checks.2', 'Review callback, timer, class, object-method, and call/apply/bind traps before promoting the topic.');
    }

    /**
     * PHP learning pipelines expose runtime memory focus checks.
     */
    public function test_php_learning_pipeline_exposes_stack_heap_focus_checks(): void
    {
        $this->getJson('/api/practice/technology-learning-pipeline/php?language=en&search=stack%20memory&limit=3')
            ->assertOk()
            ->assertJsonPath('data.technology', 'php')
            ->assertJsonPath('data.related_workbench.path', '/workbench/name-normalizer')
            ->assertJsonPath('data.focus_checks.1', 'For stack versus heap, explain call frames, local variables, object lifetime, references, and cleanup.');
    }

    /**
     * AI type comparison learning pipelines expose contract and metric focus checks.
     */
    public function test_predictive_generative_ai_learning_pipeline_exposes_ai_type_focus_checks(): void
    {
        $this->getJson('/api/practice/technology-learning-pipeline/llm-foundations?language=en&search=Predictive%20AI&limit=3')
            ->assertOk()
            ->assertJsonPath('data.technology', 'llm-foundations')
            ->assertJsonPath('data.related_workbench.path', '/workbench/llm-decision-loop-plan')
            ->assertJsonPath('data.focus_checks.0', 'Start by naming the output contract: score, label, forecast, ranking, generated text, code, image, summary, or answer.')
            ->assertJsonPath('data.focus_checks.2', 'Use different quality checks for prediction metrics and generation review before promoting the topic.');
    }

    /**
     * Agent memory learning pipelines open the dedicated memory workbench and guardrails.
     */
    public function test_ai_agent_memory_learning_pipeline_exposes_memory_focus_checks(): void
    {
        $this->getJson('/api/practice/technology-learning-pipeline/llm-foundations?language=en&search=agent%20memory&limit=3')
            ->assertOk()
            ->assertJsonPath('data.technology', 'llm-foundations')
            ->assertJsonPath('data.related_workbench.path', '/workbench/ai-agent-memory-plan')
            ->assertJsonPath('data.stages.0.route', '/workbench/ai-agent-memory-plan?family=laravel&language=en&search=agent+memory&limit=3')
            ->assertJsonPath('data.focus_checks.0', 'Separate working, episodic, semantic, and procedural memory before designing storage or retrieval.')
            ->assertJsonPath('data.focus_checks.2', 'Block stale semantic memory and private episodic memory from silently steering the next agent action.');
    }

    /**
     * Covering Index learning pipelines expose query-plan and operations focus checks.
     */
    public function test_covering_index_learning_pipeline_exposes_query_plan_focus_checks(): void
    {
        $this->getJson('/api/practice/technology-learning-pipeline/database-eloquent?language=en&search=Covering%20Index&limit=3')
            ->assertOk()
            ->assertJsonPath('data.technology', 'database-eloquent')
            ->assertJsonPath('data.related_workbench.path', '/workbench/collection-filter-preview')
            ->assertJsonPath('data.focus_checks.0', 'Start from EXPLAIN (ANALYZE, BUFFERS) and record Index Scan, Index Only Scan, Heap Fetches, and buffer reads.')
            ->assertJsonPath('data.focus_checks.1', 'Design the covering index with filter/order keys first and INCLUDE payload columns only when the hot query projects them.')
            ->assertJsonPath('data.focus_checks.2', 'Treat visibility map, VACUUM or autovacuum, index size, bloat risk, write overhead, and rollback as release evidence.');
    }

    /**
     * Database locking learning pipelines expose transaction and contention focus checks.
     */
    public function test_database_locking_learning_pipeline_exposes_concurrency_focus_checks(): void
    {
        $this->getJson('/api/practice/technology-learning-pipeline/database-eloquent?language=en&search=lockForUpdate&limit=3')
            ->assertOk()
            ->assertJsonPath('data.technology', 'database-eloquent')
            ->assertJsonPath('data.related_workbench.path', '/workbench/database-locking-plan')
            ->assertJsonPath('data.focus_checks.0', 'Start from the protected invariant before adding a lock: no negative stock, no double spend, or no duplicate state transition.')
            ->assertJsonPath('data.focus_checks.1', 'Keep `DB::transaction()` and `lockForUpdate()` in the same critical section, with the lock taken before protected state is checked.')
            ->assertJsonPath('data.focus_checks.2', 'Treat indexed lookup, short lock window, deadlock retry, timeout behavior, contention, and lock-wait monitoring as release evidence.');
    }

    /**
     * Graph traversal learning pipelines expose BFS and DFS guardrails.
     */
    public function test_graph_traversal_learning_pipeline_exposes_bfs_dfs_focus_checks(): void
    {
        $this->getJson('/api/practice/technology-learning-pipeline/graph-traversal?language=en&search=BFS%20DFS&limit=3')
            ->assertOk()
            ->assertJsonPath('data.technology', 'graph-traversal')
            ->assertJsonPath('data.practice.slug', 'graph-traversal-plan-workbench')
            ->assertJsonPath('data.related_workbench.path', '/workbench/graph-traversal-plan')
            ->assertJsonPath('data.record_count', 1)
            ->assertJsonPath('data.focus_checks.0', 'Start by naming the traversal goal: nearest match, shortest unweighted path, full branch exploration, or subtree validation.')
            ->assertJsonPath('data.focus_checks.1', 'Choose BFS for level-order search, fewest hops, and nearest result; choose DFS for branch exploration, dependency reasoning, and backtracking.')
            ->assertJsonPath('data.focus_checks.2', 'Require visited set, cycle detection, max depth, max nodes, batching, pagination, and rate-limit notes before applying traversal to real APIs or database trees.');
    }
}
