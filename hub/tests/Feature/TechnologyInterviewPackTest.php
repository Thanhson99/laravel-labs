<?php

declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;

final class TechnologyInterviewPackTest extends TestCase
{
    /**
     * The technology interview pack page renders questions, evidence, and practice script.
     */
    public function test_technology_interview_pack_page_renders_questions_and_evidence(): void
    {
        $response = $this->get('/practice/technology-interview-pack/api-validation?family=laravel&language=en&search=api&limit=3');

        $response
            ->assertOk()
            ->assertSee('Technology Interview Pack: api-validation')
            ->assertSee('How did you turn `api-validation` learning content into Laravel code?')
            ->assertSee('Evidence To Cite')
            ->assertSee('Practice Script')
            ->assertSee('practice/api-validation/content-backed-implementation')
            ->assertSee('Open interview API');
    }

    /**
     * The technology interview pack API returns questions, answer outlines, evidence, and progress data.
     */
    public function test_technology_interview_pack_api_returns_questions_and_evidence(): void
    {
        $response = $this->getJson('/api/practice/technology-interview-pack/api-validation?family=laravel&language=en&search=api&limit=3');

        $response
            ->assertOk()
            ->assertJsonPath('data.technology', 'api-validation')
            ->assertJsonPath('data.questions.0.question', 'How did you turn `api-validation` learning content into Laravel code?')
            ->assertJsonPath('data.progress_payload.items.0.label', 'Answer architecture question')
            ->assertJsonStructure([
                'data' => [
                    'title',
                    'technology',
                    'artifact',
                    'questions' => [
                        '*' => [
                            'question',
                            'answer_outline',
                        ],
                    ],
                    'evidence_to_cite',
                    'practice_script',
                    'progress_payload',
                ],
            ]);
    }

    /**
     * React render packs ask about profiler evidence and memoization tradeoffs.
     */
    public function test_react_render_interview_pack_is_frontend_specific(): void
    {
        $this->getJson('/api/practice/technology-interview-pack/react-render-performance?language=en&search=React%20re-renders&limit=3')
            ->assertOk()
            ->assertJsonPath('data.technology', 'react-render-performance')
            ->assertJsonPath('data.questions.0.question', 'How do you decide whether React.memo, useMemo, or useCallback is the right fix?')
            ->assertJsonPath('data.questions.1.answer_outline.2', 'If a list renders too many DOM nodes, virtualization or pagination usually matters more.')
            ->assertJsonPath('data.practice_script.0', 'I would not start by wrapping everything in memo. I would capture a React Profiler baseline first.');
    }

    /**
     * JavaScript closure packs ask about lexical scope, captured bindings, and interview traps.
     */
    public function test_javascript_closure_interview_pack_is_scope_specific(): void
    {
        $this->getJson('/api/practice/technology-interview-pack/javascript-closures?language=en&search=JavaScript%20closure&limit=3')
            ->assertOk()
            ->assertJsonPath('data.technology', 'javascript-closures')
            ->assertJsonPath('data.questions.0.question', 'What is a JavaScript closure and why does lexical scope matter?')
            ->assertJsonPath('data.questions.0.answer_outline.3', 'The function keeps access to the variable binding, not just a one-time copied value.')
            ->assertJsonPath('data.questions.1.answer_outline.2', 'React hook callbacks can also expose stale-closure bugs when dependencies are wrong.')
            ->assertJsonPath('data.questions.2.answer_outline.0', 'Explain why var in a loop can share one binding while let creates a new block-scoped binding per iteration.')
            ->assertJsonPath('data.practice_script.0', 'A JavaScript closure is a function that keeps access to variables from the lexical scope where it was created.');
    }

    /**
     * Arrow-function this packs ask about lexical this and call-site traps.
     */
    public function test_arrow_this_interview_pack_is_lexical_this_specific(): void
    {
        $this->getJson('/api/practice/technology-interview-pack/javascript-closures?language=en&search=arrow%20function%20this&limit=3')
            ->assertOk()
            ->assertJsonPath('data.technology', 'javascript-closures')
            ->assertJsonPath('data.questions.0.question', 'Why does an arrow function not have its own `this`?')
            ->assertJsonPath('data.questions.0.answer_outline.1', 'The arrow function reads `this` from the surrounding scope where the arrow is created.')
            ->assertJsonPath('data.questions.1.answer_outline.3', 'Those APIs cannot rebind `this` for an arrow function because it was already taken lexically.')
            ->assertJsonPath('data.questions.2.answer_outline.0', 'Do not use an arrow as an object method when the method needs `this` to mean that object.')
            ->assertJsonPath('data.practice_script.0', 'Arrow functions do not create their own `this`; they read `this` from the lexical scope where they are created.');
    }

    /**
     * SQL Injection packs ask about bindings, identifier allowlists, and payload tests.
     */
    public function test_sql_injection_interview_pack_is_security_specific(): void
    {
        $this->getJson('/api/practice/technology-interview-pack/sql-injection-defense?language=en&search=SQL%20Injection&limit=3')
            ->assertOk()
            ->assertJsonPath('data.technology', 'sql-injection-defense')
            ->assertJsonPath('data.questions.0.question', 'What is SQL Injection and how do parameterized queries prevent it?')
            ->assertJsonPath('data.questions.1.answer_outline.1', 'Dynamic identifiers such as table names, column names, and sort directions need allowlists.')
            ->assertJsonPath('data.practice_script.0', 'SQL Injection is when user input becomes SQL logic instead of staying data.');
    }

    /**
     * CSRF packs ask about browser intent, SameSite, and token tests.
     */
    public function test_csrf_interview_pack_is_security_specific(): void
    {
        $this->getJson('/api/practice/technology-interview-pack/csrf-protection?language=en&search=CSRF&limit=3')
            ->assertOk()
            ->assertJsonPath('data.technology', 'csrf-protection')
            ->assertJsonPath('data.questions.0.question', 'What is CSRF and why does it matter for cookie-based auth?')
            ->assertJsonPath('data.questions.1.answer_outline.1', 'SameSite changes when cookies are sent in cross-site contexts.')
            ->assertJsonPath('data.practice_script.0', 'CSRF is when a logged-in browser is tricked into sending an unwanted state-changing request.');
    }

    /**
     * XSS packs ask about attack variants, output context, and payload tests.
     */
    public function test_xss_interview_pack_is_security_specific(): void
    {
        $this->getJson('/api/practice/technology-interview-pack/xss-defense?language=en&search=XSS&limit=3')
            ->assertOk()
            ->assertJsonPath('data.technology', 'xss-defense')
            ->assertJsonPath('data.questions.0.question', 'What is XSS and how do reflected, stored, and DOM-based XSS differ?')
            ->assertJsonPath('data.questions.1.answer_outline.1', 'Avoid `{!! !!}` unless the content is sanitized or fully trusted.')
            ->assertJsonPath('data.practice_script.0', 'XSS is when untrusted data reaches an executable browser context.');
    }

    /**
     * IDOR packs ask about object-level authorization, scoped queries, and denial tests.
     */
    public function test_idor_interview_pack_is_security_specific(): void
    {
        $this->getJson('/api/practice/technology-interview-pack/idor-access-control?language=en&search=IDOR&limit=3')
            ->assertOk()
            ->assertJsonPath('data.technology', 'idor-access-control')
            ->assertJsonPath('data.questions.0.question', 'What is IDOR and why is it not solved by login alone?')
            ->assertJsonPath('data.questions.1.answer_outline.0', 'Scope the query by owner, tenant, team, or account before returning the model.')
            ->assertJsonPath('data.questions.2.answer_outline.2', 'Repeat the denial test for sibling routes such as update, delete, download, export, and nested child routes.')
            ->assertJsonPath('data.practice_script.0', 'IDOR is object-level authorization failure: a logged-in user can change an object ID and reach data they should not access.');
    }

    /**
     * Security Misconfiguration packs ask about runtime settings, environment drift, and readiness checks.
     */
    public function test_security_misconfiguration_interview_pack_is_configuration_specific(): void
    {
        $this->getJson('/api/practice/technology-interview-pack/security-misconfiguration?language=en&search=Security%20Misconfiguration&limit=3')
            ->assertOk()
            ->assertJsonPath('data.technology', 'security-misconfiguration')
            ->assertJsonPath('data.questions.0.question', 'What is Security Misconfiguration and why is it common in production apps?')
            ->assertJsonPath('data.questions.1.answer_outline.2', 'Check session cookie flags, CORS allowlists, security headers, trusted proxies, and HTTPS enforcement.')
            ->assertJsonPath('data.questions.2.answer_outline.0', 'Run smoke checks that reject APP_DEBUG=true, exposed .env files, permissive CORS, and missing headers in production.')
            ->assertJsonPath('data.practice_script.0', 'Security Misconfiguration is unsafe runtime, deployment, or infrastructure setup that exposes data or weakens app defenses.');
    }

    /**
     * Broken Authentication packs ask about lifecycle controls and evidence.
     */
    public function test_broken_authentication_interview_pack_is_lifecycle_specific(): void
    {
        $this->getJson('/api/practice/technology-interview-pack/auth-security?language=en&search=Broken%20Authentication&limit=3')
            ->assertOk()
            ->assertJsonPath('data.technology', 'auth-security')
            ->assertJsonPath('data.questions.0.question', 'How do you explain broken authentication beyond a bad login form?')
            ->assertJsonPath('data.questions.0.answer_outline.1', 'Broken authentication can happen at login, session creation, remember-me, password reset, MFA recovery, token refresh, revocation, or logout.')
            ->assertJsonPath('data.questions.1.question', 'Which Laravel controls reduce broken authentication risk?')
            ->assertJsonPath('data.questions.2.answer_outline.0', 'Test brute force throttling, stale reset token rejection, old session reuse, logout invalidation, and revoked token reuse.')
            ->assertJsonPath('data.practice_script.0', 'Broken authentication is any weakness that lets the wrong person become, stay, or act as a user.');
    }

    /**
     * OAuth packs ask about flow selection, PKCE proof, callback checks, and token boundaries.
     */
    public function test_oauth_interview_pack_is_pkce_specific(): void
    {
        $this->getJson('/api/practice/technology-interview-pack/oauth-flow?language=en&search=PKCE&limit=3')
            ->assertOk()
            ->assertJsonPath('data.technology', 'oauth-flow')
            ->assertJsonPath('data.questions.0.question', 'When should you use Authorization Code with PKCE instead of Implicit Flow or Client Credentials?')
            ->assertJsonPath('data.questions.1.answer_outline.1', 'It sends only the derived S256 code_challenge in the authorize request.')
            ->assertJsonPath('data.questions.2.answer_outline.1', 'Reject access_token, id_token, token_type, and expires_in values arriving in browser callback input.')
            ->assertJsonPath('data.practice_script.0', 'PKCE protects public-client authorization code flow by proving the token exchange came from the client that started login.');
    }

    /**
     * Graph traversal packs ask about BFS, DFS, state, and production guardrails.
     */
    public function test_graph_traversal_interview_pack_is_bfs_dfs_specific(): void
    {
        $this->getJson('/api/practice/technology-interview-pack/graph-traversal?language=en&search=BFS%20DFS&limit=3')
            ->assertOk()
            ->assertJsonPath('data.technology', 'graph-traversal')
            ->assertJsonPath('data.questions.0.question', 'How do you choose between BFS and DFS in a real system?')
            ->assertJsonPath('data.questions.0.answer_outline.1', 'BFS fits nearest result, level-order traversal, and shortest path in an unweighted graph.')
            ->assertJsonPath('data.questions.1.answer_outline.0', 'BFS uses a queue and keeps a frontier of nodes at the current or next distance.')
            ->assertJsonPath('data.questions.2.answer_outline.2', 'Production code needs max depth, max nodes, fan-out limits, pagination, batching, timeout, and memory guardrails.')
            ->assertJsonPath('data.practice_script.0', 'BFS and DFS are traversal strategies chosen from the goal, not from a universal speed rule.');
    }

    /**
     * PHP stack and heap packs ask about call frames, heap-backed data, and runtime caveats.
     */
    public function test_php_stack_heap_interview_pack_is_runtime_memory_specific(): void
    {
        $this->getJson('/api/practice/technology-interview-pack/php?language=en&search=stack%20memory&limit=3')
            ->assertOk()
            ->assertJsonPath('data.technology', 'php')
            ->assertJsonPath('data.questions.0.question', 'How do stack memory and heap memory differ in a PHP runtime explanation?')
            ->assertJsonPath('data.questions.0.answer_outline.1', 'The stack tracks active function calls, parameters, local variables, return values, and frame cleanup.')
            ->assertJsonPath('data.questions.1.answer_outline.1', 'Create a large array or object graph to show heap pressure and reference lifetime.')
            ->assertJsonPath('data.questions.2.answer_outline.0', 'Do not say PHP developers manually choose stack or heap allocation in normal application code.')
            ->assertJsonPath('data.practice_script.0', 'Stack memory is the mental model for active function calls: parameters, local variables, return values, and frame cleanup.');
    }

    /**
     * AI type comparison packs ask about output contracts, metrics, and failure modes.
     */
    public function test_predictive_generative_ai_interview_pack_is_ai_type_specific(): void
    {
        $this->getJson('/api/practice/technology-interview-pack/llm-foundations?language=en&search=Predictive%20AI&limit=3')
            ->assertOk()
            ->assertJsonPath('data.technology', 'llm-foundations')
            ->assertJsonPath('data.questions.0.question', 'How do Predictive AI and Generative AI differ in practical product work?')
            ->assertJsonPath('data.questions.0.answer_outline.1', 'Predictive AI estimates scores, labels, forecasts, rankings, or risk decisions from existing data.')
            ->assertJsonPath('data.questions.1.answer_outline.0', 'Predictive AI needs metrics such as precision, recall, calibration, AUC, error rate, and business lift.')
            ->assertJsonPath('data.questions.2.answer_outline.1', 'Generative AI can fail through hallucination, fabricated citations, prompt injection, unsafe code, or style drift.')
            ->assertJsonPath('data.practice_script.0', 'Predictive AI estimates outcomes such as scores, labels, forecasts, rankings, or risk decisions from existing data.');
    }

    /**
     * AI agent memory packs ask about memory contracts, metadata, and failure tests.
     */
    public function test_ai_agent_memory_interview_pack_is_memory_contract_specific(): void
    {
        $this->getJson('/api/practice/technology-interview-pack/llm-foundations?language=en&search=agent%20memory&limit=3')
            ->assertOk()
            ->assertJsonPath('data.technology', 'llm-foundations')
            ->assertJsonPath('data.questions.0.question', 'What are the four core memory types an AI agent needs to work like a developer?')
            ->assertJsonPath('data.questions.0.answer_outline.1', 'Working memory stores current task state and should be bounded, short-lived, and easy to discard.')
            ->assertJsonPath('data.questions.1.answer_outline.0', 'Record source, freshness, confidence, permission scope, retention rule, and correction path.')
            ->assertJsonPath('data.questions.2.answer_outline.2', 'Test stale semantic facts, corrected facts, low-confidence facts, and unreviewed procedural playbooks.')
            ->assertJsonPath('data.practice_script.0', 'AI agent memory should be split into working, episodic, semantic, and procedural memory instead of treated as one prompt-history bucket.');
    }

    /**
     * RAG system packs ask about context strategy, answer contracts, and router tests.
     */
    public function test_rag_context_strategy_interview_pack_is_chatbot_specific(): void
    {
        $this->getJson('/api/practice/technology-interview-pack/rag-systems?language=en&search=Long%20Context%20CAG&limit=3')
            ->assertOk()
            ->assertJsonPath('data.technology', 'rag-systems')
            ->assertJsonPath('data.questions.0.question', 'How do you choose between RAG, Long Context, CAG, and hybrid routing for a chatbot?')
            ->assertJsonPath('data.questions.0.answer_outline.1', 'RAG fits broad, frequently changing, or permission-sensitive corpora where retrieval freshness matters.')
            ->assertJsonPath('data.questions.1.answer_outline.0', 'Return selected_context_path, rag_pattern, token_budget, cache_version, source_version, fallback_reason, and missing_evidence.')
            ->assertJsonPath('data.questions.2.answer_outline.2', 'Test CAG cache versioning, invalidation, tenant-specific cache keys, and stale-cache fallback.')
            ->assertJsonPath('data.practice_script.0', 'RAG, Long Context, and CAG are context strategies for grounding a chatbot, not competing model names.');
    }

    /**
     * Database packs ask about covering indexes, heap fetches, and EXPLAIN evidence.
     */
    public function test_covering_index_interview_pack_is_query_plan_specific(): void
    {
        $this->getJson('/api/practice/technology-interview-pack/database-eloquent?language=en&search=Covering%20Index&limit=3')
            ->assertOk()
            ->assertJsonPath('data.technology', 'database-eloquent')
            ->assertJsonPath('data.questions.0.question', 'Why can an indexed PostgreSQL query still be slow because of heap fetches?')
            ->assertJsonPath('data.questions.1.answer_outline.2', 'Index Only Scan still depends on the visibility map, so VACUUM and autovacuum health are part of the implementation story.')
            ->assertJsonPath('data.questions.2.answer_outline.2', 'The rollout note covers index size, bloat risk, write overhead, VACUUM visibility-map health, and rollback.')
            ->assertJsonPath('data.practice_script.0', 'A covering index helps a hot query read all needed values from the index instead of fetching rows from the heap.');
    }

    /**
     * Database locking packs ask about transactions, lockForUpdate, deadlocks, and contention.
     */
    public function test_database_locking_interview_pack_is_concurrency_specific(): void
    {
        $this->getJson('/api/practice/technology-interview-pack/database-eloquent?language=en&search=lockForUpdate&limit=3')
            ->assertOk()
            ->assertJsonPath('data.technology', 'database-eloquent')
            ->assertJsonPath('data.questions.0.question', 'What is database locking and why does it matter in real API writes?')
            ->assertJsonPath('data.questions.0.answer_outline.1', 'Database locking controls concurrent access so two requests cannot safely read the same old value and both write conflicting updates.')
            ->assertJsonPath('data.questions.1.answer_outline.0', 'Wrap the critical read, validation, and write in `DB::transaction()`.')
            ->assertJsonPath('data.questions.2.answer_outline.2', 'Review notes identify row lock versus table lock risk, hot-row contention, indexed lookup requirements, and lock-wait monitoring.')
            ->assertJsonPath('data.practice_script.0', 'Database locking protects a critical row or resource from unsafe concurrent writes, but it only works when the transaction boundary is correct.');
    }
}
