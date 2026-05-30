<?php

declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;

final class TechnologyCommitPlanTest extends TestCase
{
    /**
     * The technology commit plan page renders branch, files, verification, and review evidence.
     */
    public function test_technology_commit_plan_page_renders_commit_artifacts(): void
    {
        $response = $this->get('/practice/technology-commit-plan/api-validation?family=laravel&language=en&search=api&limit=3');

        $response
            ->assertOk()
            ->assertSee('Technology Commit Plan: api-validation')
            ->assertSee('practice/api-validation/content-backed-implementation')
            ->assertSee('practice: implement api-validation content-backed lab')
            ->assertSee('routes/api/practice-actions.php')
            ->assertSee('php artisan test --filter ContentBackedApiDrill')
            ->assertSee('Open API validation workbench')
            ->assertSee('Evidence Checklist')
            ->assertSee('Review Checklist')
            ->assertSee('Next Actions')
            ->assertSee('Practice interview defense')
            ->assertSee('Run skill assessment')
            ->assertSee('Open commit API');
    }

    /**
     * The technology commit plan API returns commit-ready artifacts from the implementation lab.
     */
    public function test_technology_commit_plan_api_returns_commit_artifacts(): void
    {
        $response = $this->getJson('/api/practice/technology-commit-plan/api-validation?family=laravel&language=en&search=api&limit=3');

        $response
            ->assertOk()
            ->assertJsonPath('data.technology', 'api-validation')
            ->assertJsonPath('data.practice.slug', 'api-form-request-slice')
            ->assertJsonPath('data.related_workbench.path', '/workbench/topic-intake')
            ->assertJsonPath('data.branch', 'practice/api-validation/content-backed-implementation')
            ->assertJsonPath('data.commit_message', 'practice: implement api-validation content-backed lab')
            ->assertJsonPath('data.changed_files.0', 'routes/api/practice-actions.php')
            ->assertJsonPath('data.verification.0', 'php artisan test --filter ContentBackedApiDrill')
            ->assertJsonPath('data.next_actions.0.path', '/practice/technology-implementation-lab/api-validation?family=laravel&language=en&search=api&limit=3')
            ->assertJsonPath('data.next_actions.1.path', '/practice/technology-portfolio-artifact/api-validation?family=laravel&language=en&search=api&limit=3')
            ->assertJsonPath('data.next_actions.2.api_path', '/api/practice/technology-interview-pack/api-validation?family=laravel&language=en&search=api&limit=3')
            ->assertJsonPath('data.progress_payload.items.0.label', 'Create branch')
            ->assertJsonStructure([
                'data' => [
                    'title',
                    'technology',
                    'practice',
                    'related_workbench',
                    'branch',
                    'commit_message',
                    'lab',
                    'changed_files',
                    'verification',
                    'evidence_checklist',
                    'review_checklist',
                    'next_actions' => [
                        '*' => [
                            'label',
                            'purpose',
                            'path',
                            'api_path',
                        ],
                    ],
                    'progress_payload',
                ],
            ]);
    }

    /**
     * Specialized commit plans inherit workbench verification from implementation labs.
     */
    public function test_specialized_commit_plan_inherits_workbench_verification(): void
    {
        $this->getJson('/api/practice/technology-commit-plan/oauth-flow?language=en&search=Implicit%20Flow&limit=3')
            ->assertOk()
            ->assertJsonPath('data.technology', 'oauth-flow')
            ->assertJsonPath('data.practice.slug', 'oauth-flow-plan-workbench')
            ->assertJsonPath('data.related_workbench.path', '/workbench/oauth-flow-plan')
            ->assertJsonPath('data.verification.0', 'php artisan test --filter OauthFlowPlanWorkbenchTest')
            ->assertJsonPath('data.evidence_checklist.1', 'Authorization Code with PKCE is chosen from client type, secret-storage ability, and user delegation needs.')
            ->assertJsonPath('data.review_checklist.1', 'The authorize URL sends code_challenge and code_challenge_method=S256, never code_verifier.');

        $this->getJson('/api/practice/technology-commit-plan/load-balancing?language=en&search=load%20balancer%20algorithms&limit=3')
            ->assertOk()
            ->assertJsonPath('data.technology', 'load-balancing')
            ->assertJsonPath('data.practice.slug', 'load-balancer-algorithm-plan-workbench')
            ->assertJsonPath('data.related_workbench.path', '/workbench/load-balancer-plan')
            ->assertJsonPath('data.verification.0', 'php artisan test --filter LoadBalancerPlanWorkbenchTest');
    }

    /**
     * React render commit plans require profiler and memoization review evidence.
     */
    public function test_react_render_commit_plan_has_frontend_performance_review_checks(): void
    {
        $this->getJson('/api/practice/technology-commit-plan/react-render-performance?language=en&search=React%20re-renders&limit=3')
            ->assertOk()
            ->assertJsonPath('data.technology', 'react-render-performance')
            ->assertJsonPath('data.practice.slug', 'react-render-optimization-workbench')
            ->assertJsonPath('data.related_workbench.path', '/workbench/react-render-optimization-plan')
            ->assertJsonPath('data.evidence_checklist.1', 'React Profiler baseline and after-state evidence are captured before claiming improvement.')
            ->assertJsonPath('data.review_checklist.0', 'Profiler evidence exists before and after the optimization.')
            ->assertJsonPath('data.review_checklist.2', 'useMemo and useCallback dependency arrays are complete and do not create stale UI.');
    }

    /**
     * JavaScript closure commit plans require lexical-scope and interview-trap evidence.
     */
    public function test_javascript_closure_commit_plan_has_scope_review_checks(): void
    {
        $this->getJson('/api/practice/technology-commit-plan/javascript-closures?language=en&search=JavaScript%20closure&limit=3')
            ->assertOk()
            ->assertJsonPath('data.technology', 'javascript-closures')
            ->assertJsonPath('data.practice.slug', 'react-render-optimization-workbench')
            ->assertJsonPath('data.related_workbench.path', '/workbench/react-render-optimization-plan')
            ->assertJsonPath('data.evidence_checklist.1', 'Closure examples trace lexical scope, captured bindings, and repeated-call state before claiming interview readiness.')
            ->assertJsonPath('data.evidence_checklist.4', 'Interview evidence covers createCounter(), private state, var versus let, stale closures, and real callback usage.')
            ->assertJsonPath('data.review_checklist.0', 'The closure definition names lexical scope and captured variable bindings.')
            ->assertJsonPath('data.review_checklist.3', 'Interview traps include var versus let loop behavior and stale closures in async callbacks, timers, or hooks.');
    }

    /**
     * Arrow-function this commit plans require lexical-this and call-site trap evidence.
     */
    public function test_arrow_this_commit_plan_has_lexical_this_review_checks(): void
    {
        $this->getJson('/api/practice/technology-commit-plan/javascript-closures?language=en&search=arrow%20function%20this&limit=3')
            ->assertOk()
            ->assertJsonPath('data.technology', 'javascript-closures')
            ->assertJsonPath('data.practice.slug', 'react-render-optimization-workbench')
            ->assertJsonPath('data.evidence_checklist.1', 'Arrow-function examples prove lexical this, normal function dynamic this, object-method traps, and callback use before claiming interview readiness.')
            ->assertJsonPath('data.evidence_checklist.4', 'Interview evidence covers obj.arrow(), call/apply/bind limits, timer callbacks, class callbacks, and when not to use arrow methods.')
            ->assertJsonPath('data.review_checklist.0', 'The answer states that arrow functions do not create their own `this`.')
            ->assertJsonPath('data.review_checklist.3', '`call`, `apply`, and `bind` limitations are covered explicitly for arrow functions.');
    }

    /**
     * SQL Injection commit plans require binding and payload-test review evidence.
     */
    public function test_sql_injection_commit_plan_has_security_review_checks(): void
    {
        $this->getJson('/api/practice/technology-commit-plan/sql-injection-defense?language=en&search=SQL%20Injection&limit=3')
            ->assertOk()
            ->assertJsonPath('data.technology', 'sql-injection-defense')
            ->assertJsonPath('data.practice.slug', 'sql-injection-defense-plan-workbench')
            ->assertJsonPath('data.related_workbench.path', '/workbench/sql-injection-defense-plan')
            ->assertJsonPath('data.evidence_checklist.1', 'Unsafe SQL examples are replaced with parameterized values or query builder bindings.')
            ->assertJsonPath('data.review_checklist.0', 'No request input is concatenated into raw SQL strings.')
            ->assertJsonPath('data.review_checklist.2', 'Dynamic identifiers such as sort columns, table names, and directions use allowlists.');
    }

    /**
     * CSRF commit plans require token, SameSite, and failure-test review evidence.
     */
    public function test_csrf_commit_plan_has_security_review_checks(): void
    {
        $this->getJson('/api/practice/technology-commit-plan/csrf-protection?language=en&search=CSRF&limit=3')
            ->assertOk()
            ->assertJsonPath('data.technology', 'csrf-protection')
            ->assertJsonPath('data.practice.slug', 'csrf-protection-plan-workbench')
            ->assertJsonPath('data.related_workbench.path', '/workbench/csrf-protection-plan')
            ->assertJsonPath('data.evidence_checklist.1', 'State-changing browser flows include CSRF token proof or an explicit non-cookie auth reason.')
            ->assertJsonPath('data.review_checklist.0', 'State-changing routes do not use GET.')
            ->assertJsonPath('data.review_checklist.2', 'SameSite cookie settings are deliberate and do not replace token verification.');
    }

    /**
     * XSS commit plans require safe rendering and payload-test review evidence.
     */
    public function test_xss_commit_plan_has_security_review_checks(): void
    {
        $this->getJson('/api/practice/technology-commit-plan/xss-defense?language=en&search=XSS&limit=3')
            ->assertOk()
            ->assertJsonPath('data.technology', 'xss-defense')
            ->assertJsonPath('data.practice.slug', 'blade-escaping-xss-preview')
            ->assertJsonPath('data.related_workbench.path', '/workbench/security-escape-preview')
            ->assertJsonPath('data.evidence_checklist.1', 'Untrusted browser output is rendered with escaped Blade output, safe JSON handoff, or sanitized rich text.')
            ->assertJsonPath('data.review_checklist.0', 'Untrusted values use escaped Blade output or another context-specific encoder.')
            ->assertJsonPath('data.review_checklist.2', 'Server data passed to JavaScript uses safe JSON serialization instead of string concatenation.');
    }

    /**
     * Security Misconfiguration commit plans require production readiness evidence.
     */
    public function test_security_misconfiguration_commit_plan_has_configuration_review_checks(): void
    {
        $this->getJson('/api/practice/technology-commit-plan/security-misconfiguration?language=en&search=Security%20Misconfiguration&limit=3')
            ->assertOk()
            ->assertJsonPath('data.technology', 'security-misconfiguration')
            ->assertJsonPath('data.practice.slug', 'authorization-policy-plan-workbench')
            ->assertJsonPath('data.related_workbench.path', '/practice/configuration-readiness')
            ->assertJsonPath('data.verification.0', 'php artisan test --filter ConfigurationReadiness')
            ->assertJsonPath('data.evidence_checklist.1', 'Production configuration readiness checks cover debug mode, environment drift, exposed secrets, storage visibility, and verbose errors.')
            ->assertJsonPath('data.evidence_checklist.4', 'CORS, security headers, cookie flags, trusted proxies, HTTPS enforcement, owners, and rollback notes prove the release fails closed.')
            ->assertJsonPath('data.review_checklist.0', 'APP_DEBUG, APP_ENV, exception output, config cache, and log redaction are checked for production readiness.')
            ->assertJsonPath('data.review_checklist.3', 'Smoke checks fail the release when production configuration is missing, unsafe, or broader than documented.');
    }

    /**
     * IDOR commit plans require object-level authorization and denial-test evidence.
     */
    public function test_idor_commit_plan_has_object_authorization_review_checks(): void
    {
        $this->getJson('/api/practice/technology-commit-plan/idor-access-control?language=en&search=IDOR&limit=3')
            ->assertOk()
            ->assertJsonPath('data.technology', 'idor-access-control')
            ->assertJsonPath('data.practice.slug', 'idor-access-review-workbench')
            ->assertJsonPath('data.related_workbench.path', '/workbench/idor-access-review')
            ->assertJsonPath('data.verification.0', 'php artisan test --filter IdorAccessReviewWorkbenchTest')
            ->assertJsonPath('data.evidence_checklist.1', 'Object-level authorization evidence covers route inventory, scoped lookup, policy checks, and attacker ID-swap denial tests.')
            ->assertJsonPath('data.review_checklist.1', 'Controllers do not return models from direct findOrFail() before scoped lookup or policy authorization.')
            ->assertJsonPath('data.review_checklist.3', 'Tests prove user A cannot read, update, delete, download, export, or access nested resources owned by user B.');
    }

    /**
     * Broken Authentication commit plans require lifecycle and failure-path evidence.
     */
    public function test_broken_authentication_commit_plan_has_lifecycle_review_checks(): void
    {
        $this->getJson('/api/practice/technology-commit-plan/auth-security?language=en&search=Broken%20Authentication&limit=3')
            ->assertOk()
            ->assertJsonPath('data.technology', 'auth-security')
            ->assertJsonPath('data.practice.slug', 'authorization-policy-plan-workbench')
            ->assertJsonPath('data.related_workbench.path', '/workbench/authorization-policy-plan')
            ->assertJsonPath('data.verification.0', 'php artisan test --filter BrokenAuthentication')
            ->assertJsonPath('data.evidence_checklist.1', 'Authentication lifecycle evidence covers login, session creation, remember-me, password reset, MFA recovery, logout, token refresh, and revocation.')
            ->assertJsonPath('data.evidence_checklist.4', 'Failure-path evidence proves throttling, session regeneration, reset-token expiry, logout invalidation, token revocation, and sensitive-log redaction.')
            ->assertJsonPath('data.review_checklist.0', 'The review treats authentication as a lifecycle, not only a successful login form.')
            ->assertJsonPath('data.review_checklist.3', 'Password reset tests reject stale, reused, or mismatched reset tokens.');
    }

    /**
     * OAuth commit plans require PKCE verifier and callback-leakage review evidence.
     */
    public function test_oauth_commit_plan_has_pkce_review_checks(): void
    {
        $this->getJson('/api/practice/technology-commit-plan/oauth-flow?language=en&search=PKCE&limit=3')
            ->assertOk()
            ->assertJsonPath('data.technology', 'oauth-flow')
            ->assertJsonPath('data.practice.slug', 'oauth-flow-plan-workbench')
            ->assertJsonPath('data.related_workbench.path', '/workbench/oauth-flow-plan')
            ->assertJsonPath('data.evidence_checklist.4', 'Verifier failure and callback token-leakage tests prove the OAuth flow fails closed.')
            ->assertJsonPath('data.review_checklist.0', 'Public clients use Authorization Code with PKCE instead of Implicit Flow.')
            ->assertJsonPath('data.review_checklist.3', 'Tests cover missing verifier, wrong verifier, expired verifier, reused code, and unsupported PKCE providers.');
    }

    /**
     * Graph traversal commit plans require BFS/DFS traversal-order and guardrail evidence.
     */
    public function test_graph_traversal_commit_plan_has_bfs_dfs_review_checks(): void
    {
        $this->getJson('/api/practice/technology-commit-plan/graph-traversal?language=en&search=BFS%20DFS&limit=3')
            ->assertOk()
            ->assertJsonPath('data.technology', 'graph-traversal')
            ->assertJsonPath('data.practice.slug', 'graph-traversal-plan-workbench')
            ->assertJsonPath('data.related_workbench.path', '/workbench/graph-traversal-plan')
            ->assertJsonPath('data.verification.0', 'php artisan test --filter GraphTraversalPlanWorkbenchTest')
            ->assertJsonPath('data.evidence_checklist.1', 'BFS or DFS is chosen from traversal goal instead of a universal speed claim.')
            ->assertJsonPath('data.evidence_checklist.4', 'Traversal-order, visited-set, cycle, depth, fan-out, API crawling, database hierarchy, and memory guardrails are captured.')
            ->assertJsonPath('data.review_checklist.0', 'The answer chooses BFS or DFS from nearest result, shortest path, branch exploration, dependency reasoning, or subtree validation.')
            ->assertJsonPath('data.review_checklist.2', 'Visited-set behavior is tested with a cyclic graph or shared dependency fixture.');
    }

    /**
     * PHP stack and heap commit plans require runtime-memory evidence and review checks.
     */
    public function test_php_stack_heap_commit_plan_has_runtime_memory_review_checks(): void
    {
        $this->getJson('/api/practice/technology-commit-plan/php?language=en&search=stack%20memory&limit=3')
            ->assertOk()
            ->assertJsonPath('data.technology', 'php')
            ->assertJsonPath('data.practice.slug', 'php-cli-input-normalizer')
            ->assertJsonPath('data.related_workbench.path', '/workbench/name-normalizer')
            ->assertJsonPath('data.evidence_checklist.1', 'The example separates stack call frames from heap-backed arrays, objects, strings, and references.')
            ->assertJsonPath('data.evidence_checklist.4', 'Cleanup notes cover scope exit, unset(), reference counting, GC, and one PHP memory failure mode.')
            ->assertJsonPath('data.review_checklist.0', 'The answer treats stack and heap as a PHP runtime mental model, not manual allocation controls.')
            ->assertJsonPath('data.review_checklist.4', 'Production risk covers deep recursion, large arrays, retained references, circular references, or long-running worker state.');
    }

    /**
     * AI type comparison commit plans require separated contract, metric, and failure evidence.
     */
    public function test_predictive_generative_ai_commit_plan_has_ai_type_review_checks(): void
    {
        $this->getJson('/api/practice/technology-commit-plan/llm-foundations?language=en&search=Predictive%20AI&limit=3')
            ->assertOk()
            ->assertJsonPath('data.technology', 'llm-foundations')
            ->assertJsonPath('data.practice.slug', 'llm-decision-loop-plan-workbench')
            ->assertJsonPath('data.related_workbench.path', '/workbench/llm-decision-loop-plan')
            ->assertJsonPath('data.evidence_checklist.1', 'Predictive AI and Generative AI outputs are separated by output contract before model capability is discussed.')
            ->assertJsonPath('data.evidence_checklist.4', 'Quality evidence includes predictive metrics, generative review checks, and failure modes for both sides.')
            ->assertJsonPath('data.review_checklist.0', 'The explanation does not treat Predictive AI and Generative AI as interchangeable model labels.')
            ->assertJsonPath('data.review_checklist.3', 'Failure-mode review covers drift, overfitting, biased labels, hallucination, fabricated citations, prompt injection, and unsafe code.');
    }

    /**
     * AI agent memory commit plans require memory contract and governance evidence.
     */
    public function test_ai_agent_memory_commit_plan_has_memory_governance_review_checks(): void
    {
        $this->getJson('/api/practice/technology-commit-plan/llm-foundations?language=en&search=agent%20memory&limit=3')
            ->assertOk()
            ->assertJsonPath('data.technology', 'llm-foundations')
            ->assertJsonPath('data.related_workbench.path', '/workbench/ai-agent-memory-plan')
            ->assertJsonPath('data.verification.0', 'php artisan test --filter AiAgentMemoryPlanWorkbenchTest')
            ->assertJsonPath('data.evidence_checklist.1', 'AI agent memory is separated into working, episodic, semantic, and procedural contracts before storage is discussed.')
            ->assertJsonPath('data.evidence_checklist.4', 'Governance evidence covers source, freshness, confidence, permission, retention, correction path, stale-memory fallback, and private-memory blocking.')
            ->assertJsonPath('data.review_checklist.0', 'The plan does not treat memory as one prompt-history bucket.')
            ->assertJsonPath('data.review_checklist.4', 'Procedural memory points to reviewed playbooks, tests, commands, rollback steps, and mismatch checks.');
    }

    /**
     * RAG system commit plans require context-strategy routing and answer-contract evidence.
     */
    public function test_rag_context_strategy_commit_plan_has_router_review_checks(): void
    {
        $this->getJson('/api/practice/technology-commit-plan/rag-systems?language=en&search=Long%20Context%20CAG&limit=3')
            ->assertOk()
            ->assertJsonPath('data.technology', 'rag-systems')
            ->assertJsonPath('data.practice.slug', 'rag-strategy-plan-workbench')
            ->assertJsonPath('data.related_workbench.path', '/workbench/rag-strategy-plan')
            ->assertJsonPath('data.verification.0', 'php artisan test --filter RagStrategyPlanWorkbenchTest')
            ->assertJsonPath('data.verification.1', 'php artisan test --filter RagStrategyPlanServiceTest')
            ->assertJsonPath('data.evidence_checklist.1', 'Chatbot context strategy evidence chooses RAG, Long Context, CAG, or hybrid routing before selecting the retrieval pattern.')
            ->assertJsonPath('data.evidence_checklist.4', 'Evidence covers context contract fields, source freshness, tenant permissions, token budget, cache version, citations, fallback behavior, and missing-evidence handling.')
            ->assertJsonPath('data.review_checklist.0', 'The plan chooses RAG, Long Context, CAG, or hybrid routing from corpus freshness, size, stability, permissions, latency, and cost.')
            ->assertJsonPath('data.review_checklist.3', 'Tests cover CAG cache invalidation, Long Context token limits, unauthorized retrieval chunks, stale sources, missing citations, and hybrid route selection.');
    }

    /**
     * Covering Index commit plans require query-plan and operational review evidence.
     */
    public function test_covering_index_commit_plan_has_query_plan_review_checks(): void
    {
        $this->getJson('/api/practice/technology-commit-plan/database-eloquent?language=en&search=Covering%20Index&limit=3')
            ->assertOk()
            ->assertJsonPath('data.technology', 'database-eloquent')
            ->assertJsonPath('data.practice.slug', 'laravel-thin-controller')
            ->assertJsonPath('data.related_workbench.path', '/workbench/collection-filter-preview')
            ->assertJsonPath('data.verification.0', 'php artisan test --filter CoveringIndex')
            ->assertJsonPath('data.evidence_checklist.1', 'Before and after EXPLAIN (ANALYZE, BUFFERS) output proves whether Heap Fetches dropped.')
            ->assertJsonPath('data.evidence_checklist.4', 'INCLUDE column rationale, visibility-map health, VACUUM expectations, bloat risk, write overhead, and rollback notes are captured.')
            ->assertJsonPath('data.review_checklist.0', 'The query-plan baseline captures selected columns, filters, ordering, row count, Index Scan or Index Only Scan, Heap Fetches, and buffer reads.')
            ->assertJsonPath('data.review_checklist.4', 'Index size, bloat risk, write overhead, rollback command, and rollout timing are reviewed before merge.');
    }

    /**
     * Database locking commit plans require transaction, row-lock, and contention review evidence.
     */
    public function test_database_locking_commit_plan_has_concurrency_review_checks(): void
    {
        $this->getJson('/api/practice/technology-commit-plan/database-eloquent?language=en&search=lockForUpdate&limit=3')
            ->assertOk()
            ->assertJsonPath('data.technology', 'database-eloquent')
            ->assertJsonPath('data.practice.slug', 'laravel-thin-controller')
            ->assertJsonPath('data.verification.0', 'php artisan test --filter DatabaseLocking')
            ->assertJsonPath('data.evidence_checklist.1', 'Concurrency evidence proves the protected invariant, transaction boundary, row lock, and failure path before claiming safety.')
            ->assertJsonPath('data.evidence_checklist.4', 'Deadlock retry, lock timeout, hot-row contention, indexed lookup, and short critical-section notes are captured.')
            ->assertJsonPath('data.review_checklist.1', '`lockForUpdate()` is called inside `DB::transaction()` and before the protected row value is validated or mutated.')
            ->assertJsonPath('data.review_checklist.4', 'Tests or review notes cover insufficient state, concurrent request replay, deadlock retry, lock timeout, and lock-wait monitoring.');
    }
}
