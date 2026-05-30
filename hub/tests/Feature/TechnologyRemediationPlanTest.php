<?php

declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;

final class TechnologyRemediationPlanTest extends TestCase
{
    /**
     * The remediation plan page renders repair tasks from the skill rubric.
     */
    public function test_technology_remediation_plan_page_renders_repair_tasks(): void
    {
        $response = $this->get('/practice/technology-remediation-plan/api-validation?family=laravel&language=en&search=api&limit=3');

        $response
            ->assertOk()
            ->assertSee('Technology Remediation Plan: api-validation')
            ->assertSee('Repair Source understanding')
            ->assertSee('Repair Laravel layer placement')
            ->assertSee('Verification Commands')
            ->assertSee('Open remediation API');
    }

    /**
     * The remediation plan API returns tasks, focus files, commands, and progress payload.
     */
    public function test_technology_remediation_plan_api_returns_tasks_and_progress_payload(): void
    {
        $response = $this->getJson('/api/practice/technology-remediation-plan/api-validation?family=laravel&language=en&search=api&limit=3');

        $response
            ->assertOk()
            ->assertJsonPath('data.technology', 'api-validation')
            ->assertJsonPath('data.tasks.0.label', 'Repair Source understanding')
            ->assertJsonPath('data.tasks.0.focus_file', 'data/**/*.json and record workspace source panel')
            ->assertJsonPath('data.progress_payload.items.0.label', 'Repair Source understanding')
            ->assertJsonStructure([
                'data' => [
                    'title',
                    'technology',
                    'assessment',
                    'tasks' => [
                        '*' => [
                            'step',
                            'label',
                            'problem',
                            'action',
                            'evidence',
                            'focus_file',
                        ],
                    ],
                    'commands',
                    'next_routes',
                    'progress_payload',
                ],
            ]);
    }

    /**
     * React render remediation tasks stay focused on profiler and memoization evidence.
     */
    public function test_react_render_remediation_plan_uses_frontend_performance_tasks(): void
    {
        $this->getJson('/api/practice/technology-remediation-plan/react-render-performance?language=en&search=React%20re-renders&limit=3')
            ->assertOk()
            ->assertJsonPath('data.technology', 'react-render-performance')
            ->assertJsonPath('data.tasks.0.label', 'Repair Profiler baseline')
            ->assertJsonPath('data.tasks.0.focus_file', 'React Profiler capture and target component file')
            ->assertJsonPath('data.tasks.1.action', 'Rewrite the recommendation as one chosen tool and one rejected alternative with the reason.')
            ->assertJsonPath('data.tasks.3.focus_file', 'useMemo/useCallback dependency arrays');
    }

    /**
     * JavaScript closure remediation tasks stay focused on lexical scope and closure traps.
     */
    public function test_javascript_closure_remediation_plan_uses_scope_tasks(): void
    {
        $this->getJson('/api/practice/technology-remediation-plan/javascript-closures?language=en&search=JavaScript%20closure&limit=3')
            ->assertOk()
            ->assertJsonPath('data.technology', 'javascript-closures')
            ->assertJsonPath('data.tasks.0.label', 'Repair Lexical scope model')
            ->assertJsonPath('data.tasks.0.focus_file', 'resources/js/closure-counter.js and lexical-scope explanation notes')
            ->assertJsonPath('data.tasks.1.action', 'Trace createCounter() across repeated calls and explain why count is a binding that remains reachable.')
            ->assertJsonPath('data.tasks.3.focus_file', 'var versus let loop callback and stale-closure notes');
    }

    /**
     * Arrow-function this remediation tasks stay focused on lexical this and binding traps.
     */
    public function test_arrow_this_remediation_plan_uses_lexical_this_tasks(): void
    {
        $this->getJson('/api/practice/technology-remediation-plan/javascript-closures?language=en&search=arrow%20function%20this&limit=3')
            ->assertOk()
            ->assertJsonPath('data.technology', 'javascript-closures')
            ->assertJsonPath('data.tasks.0.label', 'Repair Lexical this model')
            ->assertJsonPath('data.tasks.0.focus_file', 'resources/js/arrow-this-comparison.js and lexical-this explanation notes')
            ->assertJsonPath('data.tasks.1.action', 'Trace one object normal method and one object arrow property, then explain why only the normal method receives call-site `this`.')
            ->assertJsonPath('data.tasks.2.focus_file', 'obj.arrow(), call(), apply(), bind(), and interview trap notes')
            ->assertJsonPath('data.tasks.3.action', 'Add one timer, event handler, array callback, or class callback where an arrow intentionally keeps outer `this`.');
    }

    /**
     * SQL Injection remediation tasks stay focused on bindings and allowlist tests.
     */
    public function test_sql_injection_remediation_plan_uses_security_tasks(): void
    {
        $this->getJson('/api/practice/technology-remediation-plan/sql-injection-defense?language=en&search=SQL%20Injection&limit=3')
            ->assertOk()
            ->assertJsonPath('data.technology', 'sql-injection-defense')
            ->assertJsonPath('data.tasks.0.label', 'Repair Injection mechanism')
            ->assertJsonPath('data.tasks.1.focus_file', 'query builder, Eloquent where clauses, or DB::select bindings')
            ->assertJsonPath('data.tasks.2.action', 'Add an explicit allowlist for dynamic sort, column, or table choices before building the query.')
            ->assertJsonPath('data.tasks.3.focus_file', 'tests/Feature security payload cases');
    }

    /**
     * CSRF remediation tasks stay focused on token proof and cookie boundaries.
     */
    public function test_csrf_remediation_plan_uses_security_tasks(): void
    {
        $this->getJson('/api/practice/technology-remediation-plan/csrf-protection?language=en&search=CSRF&limit=3')
            ->assertOk()
            ->assertJsonPath('data.technology', 'csrf-protection')
            ->assertJsonPath('data.tasks.0.label', 'Repair Attack mechanism')
            ->assertJsonPath('data.tasks.1.focus_file', 'Blade forms, XSRF headers, Sanctum CSRF bootstrap, and middleware verification')
            ->assertJsonPath('data.tasks.2.action', 'Document SameSite, Secure, Domain, and Path behavior without treating SameSite as a token replacement.')
            ->assertJsonPath('data.tasks.3.focus_file', 'tests/Feature CSRF token and 419 behavior cases');
    }

    /**
     * XSS remediation tasks stay focused on output context and payload rendering.
     */
    public function test_xss_remediation_plan_uses_security_tasks(): void
    {
        $this->getJson('/api/practice/technology-remediation-plan/xss-defense?language=en&search=XSS&limit=3')
            ->assertOk()
            ->assertJsonPath('data.technology', 'xss-defense')
            ->assertJsonPath('data.tasks.0.label', 'Repair Attack variants')
            ->assertJsonPath('data.tasks.1.focus_file', 'Blade `{{ }}`, attributes, URLs, and JavaScript serialization')
            ->assertJsonPath('data.tasks.2.action', 'Remove one `{!! !!}` path or document the sanitizer and trust boundary that makes it acceptable.')
            ->assertJsonPath('data.tasks.3.focus_file', 'tests/Feature XSS payload rendering cases');
    }

    /**
     * Security Misconfiguration remediation tasks stay focused on production readiness.
     */
    public function test_security_misconfiguration_remediation_plan_uses_configuration_tasks(): void
    {
        $this->getJson('/api/practice/technology-remediation-plan/security-misconfiguration?language=en&search=Security%20Misconfiguration&limit=3')
            ->assertOk()
            ->assertJsonPath('data.technology', 'security-misconfiguration')
            ->assertJsonPath('data.tasks.0.label', 'Repair Unsafe default detection')
            ->assertJsonPath('data.tasks.0.focus_file', 'config/app.php, .env.example, exception rendering, storage visibility, and secret exposure notes')
            ->assertJsonPath('data.tasks.1.action', 'Write expected local, CI, staging, and production values with an owner for every setting that changes by environment.')
            ->assertJsonPath('data.tasks.2.focus_file', 'CORS, security headers, HTTPS, cookie flags, trusted proxies, and public storage settings')
            ->assertJsonPath('data.tasks.3.action', 'Add a fail-closed smoke check that blocks release when debug mode, exposed secrets, missing headers, broad CORS, or proxy drift is detected.');
    }

    /**
     * IDOR remediation tasks stay focused on scoped lookup, policies, and denial tests.
     */
    public function test_idor_remediation_plan_uses_object_authorization_tasks(): void
    {
        $this->getJson('/api/practice/technology-remediation-plan/idor-access-control?language=en&search=IDOR&limit=3')
            ->assertOk()
            ->assertJsonPath('data.technology', 'idor-access-control')
            ->assertJsonPath('data.tasks.0.label', 'Repair Object surface inventory')
            ->assertJsonPath('data.tasks.0.focus_file', 'routes/api/**, routes/web/**, controllers, route parameters, nested resources, downloads, and exports')
            ->assertJsonPath('data.tasks.1.action', 'Replace one direct model lookup with a query constrained through the current user, tenant, organization, team, or parent relationship before returning the object.')
            ->assertJsonPath('data.tasks.2.focus_file', 'Policy, Gate, FormRequest authorize(), controller authorize(), and domain authorization service')
            ->assertJsonPath('data.tasks.3.action', 'Add tests where user A replays user B identifiers across read, update, delete, download, export, and nested-resource flows.');
    }

    /**
     * Broken Authentication remediation tasks stay focused on lifecycle controls and failure paths.
     */
    public function test_broken_authentication_remediation_plan_uses_lifecycle_tasks(): void
    {
        $this->getJson('/api/practice/technology-remediation-plan/auth-security?language=en&search=Broken%20Authentication&limit=3')
            ->assertOk()
            ->assertJsonPath('data.technology', 'auth-security')
            ->assertJsonPath('data.tasks.0.label', 'Repair Lifecycle model')
            ->assertJsonPath('data.tasks.0.focus_file', 'authentication lifecycle notes across login, session, remember-me, password reset, MFA recovery, logout, refresh, and revocation')
            ->assertJsonPath('data.tasks.1.action', 'Add or inspect throttling, session regeneration, reset-token expiry, logout invalidation, token rotation, token revocation, and remember-me boundaries.')
            ->assertJsonPath('data.tasks.2.focus_file', 'tests/Feature/Auth broken-authentication lifecycle, reset-token, session reuse, and token revocation cases')
            ->assertJsonPath('data.tasks.3.action', 'Add suspicious-login logging fields without storing passwords, reset tokens, session IDs, bearer tokens, or full sensitive request payloads.');
    }

    /**
     * OAuth remediation tasks stay focused on flow selection, PKCE proof, callback validation, and token boundaries.
     */
    public function test_oauth_remediation_plan_uses_pkce_tasks(): void
    {
        $this->getJson('/api/practice/technology-remediation-plan/oauth-flow?language=en&search=PKCE&limit=3')
            ->assertOk()
            ->assertJsonPath('data.technology', 'oauth-flow')
            ->assertJsonPath('data.tasks.0.label', 'Repair Flow fit')
            ->assertJsonPath('data.tasks.1.label', 'Repair PKCE proof')
            ->assertJsonPath('data.tasks.1.focus_file', 'authorize URL builder, verifier storage, S256 challenge derivation, and token exchange')
            ->assertJsonPath('data.tasks.2.action', 'Add failure cases for state mismatch, unexpected redirect URI, reused authorization code, and token fields arriving in the browser callback URL.')
            ->assertJsonPath('data.tasks.3.focus_file', 'token exchange service, storage decision, refresh rotation, and log redaction notes');
    }

    /**
     * Graph traversal remediation tasks stay focused on goals, state, cycles, and system limits.
     */
    public function test_graph_traversal_remediation_plan_uses_bfs_dfs_tasks(): void
    {
        $this->getJson('/api/practice/technology-remediation-plan/graph-traversal?language=en&search=BFS%20DFS&limit=3')
            ->assertOk()
            ->assertJsonPath('data.technology', 'graph-traversal')
            ->assertJsonPath('data.tasks.0.label', 'Repair Traversal goal')
            ->assertJsonPath('data.tasks.0.focus_file', 'BFS versus DFS decision note and selected graph fixture')
            ->assertJsonPath('data.tasks.1.action', 'Add one example that shows BFS queue frontier order and one example that shows DFS stack or recursion path order.')
            ->assertJsonPath('data.tasks.2.focus_file', 'visited set, cyclic graph fixture, max-depth, and max-node tests')
            ->assertJsonPath('data.tasks.3.action', 'Map the traversal choice to one API crawling case and one database hierarchy case with batching, pagination, rate limits, fan-out, timeout, and memory notes.');
    }

    /**
     * PHP stack and heap remediation tasks stay focused on runtime-memory corrections.
     */
    public function test_php_stack_heap_remediation_plan_uses_runtime_memory_tasks(): void
    {
        $this->getJson('/api/practice/technology-remediation-plan/php?language=en&search=stack%20memory&limit=3')
            ->assertOk()
            ->assertJsonPath('data.technology', 'php')
            ->assertJsonPath('data.tasks.0.label', 'Repair Call-frame model')
            ->assertJsonPath('data.tasks.0.focus_file', 'PHP function example and runtime-memory interview notes')
            ->assertJsonPath('data.tasks.1.action', 'Add a PHP example with a large array or object graph and explain why references can keep that data alive beyond one call frame.')
            ->assertJsonPath('data.tasks.2.focus_file', 'unset(), scope exit, reference counting, GC, and memory_get_usage notes')
            ->assertJsonPath('data.tasks.3.action', 'Add a note for deep recursion, large arrays, circular references, retained references, or long-running worker stale state.');
    }

    /**
     * AI type comparison remediation tasks repair contracts, inputs, metrics, and failures.
     */
    public function test_predictive_generative_ai_remediation_plan_uses_ai_type_tasks(): void
    {
        $this->getJson('/api/practice/technology-remediation-plan/llm-foundations?language=en&search=Predictive%20AI&limit=3')
            ->assertOk()
            ->assertJsonPath('data.technology', 'llm-foundations')
            ->assertJsonPath('data.tasks.0.label', 'Repair Output contract')
            ->assertJsonPath('data.tasks.0.focus_file', 'AI type comparison table and product output notes')
            ->assertJsonPath('data.tasks.1.action', 'Add one input path for each side: historical data and labels for prediction; prompt, context, retrieval evidence, and constraints for generation.')
            ->assertJsonPath('data.tasks.2.focus_file', 'predictive metrics and generative quality-check plan')
            ->assertJsonPath('data.tasks.3.action', 'Name separate controls for drift, overfitting, biased labels, hallucination, fabricated citations, prompt injection, and unsafe code.');
    }

    /**
     * RAG system remediation tasks repair context routing, contracts, guardrails, and failure tests.
     */
    public function test_rag_context_strategy_remediation_plan_uses_chatbot_tasks(): void
    {
        $this->getJson('/api/practice/technology-remediation-plan/rag-systems?language=en&search=Long%20Context%20CAG&limit=3')
            ->assertOk()
            ->assertJsonPath('data.technology', 'rag-systems')
            ->assertJsonPath('data.tasks.0.label', 'Repair Context strategy fit')
            ->assertJsonPath('data.tasks.0.focus_file', 'chatbot context routing table for RAG, Long Context, CAG, and hybrid decisions')
            ->assertJsonPath('data.tasks.1.action', 'Add selected_context_path, rag_pattern, token_budget, cache_version, source_version, citations, fallback_reason, and missing_evidence to the chatbot response contract.')
            ->assertJsonPath('data.tasks.2.focus_file', 'app/Services/Rag/ChatbotContextRouter.php, tenant-scoped retrieval filters, cache keys, packed documents, and citation policy')
            ->assertJsonPath('data.tasks.3.action', 'Add tests for stale cache, unauthorized chunks, missing citations, oversized context, low-confidence retrieval, and hybrid route selection.');
    }

    /**
     * Covering Index remediation tasks stay focused on EXPLAIN, INCLUDE, visibility, and cost.
     */
    public function test_covering_index_remediation_plan_uses_query_plan_tasks(): void
    {
        $this->getJson('/api/practice/technology-remediation-plan/database-eloquent?language=en&search=Covering%20Index&limit=3')
            ->assertOk()
            ->assertJsonPath('data.technology', 'database-eloquent')
            ->assertJsonPath('data.tasks.0.label', 'Repair Plan baseline')
            ->assertJsonPath('data.tasks.0.focus_file', 'EXPLAIN (ANALYZE, BUFFERS), Heap Fetches, buffer reads, and query-plan notes')
            ->assertJsonPath('data.tasks.1.action', 'Rewrite the index proposal so filter and order columns are index keys while only returned hot-query columns are listed in INCLUDE.')
            ->assertJsonPath('data.tasks.2.focus_file', 'visibility map, VACUUM, autovacuum, and heap-access caveats')
            ->assertJsonPath('data.tasks.3.action', 'Estimate index size, write overhead, bloat risk, rollback command, and the condition that would make this covering index not worth shipping.');
    }

    /**
     * Database locking remediation tasks stay focused on invariants, row locks, and contention.
     */
    public function test_database_locking_remediation_plan_uses_concurrency_tasks(): void
    {
        $this->getJson('/api/practice/technology-remediation-plan/database-eloquent?language=en&search=lockForUpdate&limit=3')
            ->assertOk()
            ->assertJsonPath('data.technology', 'database-eloquent')
            ->assertJsonPath('data.tasks.0.label', 'Repair Protected invariant')
            ->assertJsonPath('data.tasks.0.focus_file', 'database locking invariant note for inventory, balance, booking, or workflow-state writes')
            ->assertJsonPath('data.tasks.1.action', 'Move the critical read, validation, and write into one `DB::transaction()` and apply `lockForUpdate()` before protected state is checked.')
            ->assertJsonPath('data.tasks.2.focus_file', 'selective indexed lock query, short critical section, consistent multi-row lock ordering, and no external calls inside the transaction')
            ->assertJsonPath('data.tasks.3.action', 'Add concurrent-request, insufficient-state, deadlock retry, lock timeout, hot-row contention, and lock-wait monitoring evidence.');
    }
}
