<?php

declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;

final class TechnologyPortfolioArtifactTest extends TestCase
{
    /**
     * The technology portfolio page renders a reusable learning artifact.
     */
    public function test_technology_portfolio_page_renders_source_coverage_and_readme_template(): void
    {
        $response = $this->get('/practice/technology-portfolio-artifact/api-validation?family=laravel&language=en&search=api&limit=3');

        $response
            ->assertOk()
            ->assertSee('Technology Portfolio Artifact: api-validation')
            ->assertSee('Implemented content-backed api-validation practice in Laravel')
            ->assertSee('Source Coverage')
            ->assertSee('Interview Talking Points')
            ->assertSee('README Template')
            ->assertSee('practice/api-validation/content-backed-implementation')
            ->assertSee('Open portfolio API');
    }

    /**
     * The technology portfolio API returns source coverage, talking points, and README output.
     */
    public function test_technology_portfolio_api_returns_portfolio_artifact(): void
    {
        $response = $this->getJson('/api/practice/technology-portfolio-artifact/api-validation?family=laravel&language=en&search=api&limit=3');

        $response
            ->assertOk()
            ->assertJsonPath('data.technology', 'api-validation')
            ->assertJsonPath('data.portfolio.headline', 'Implemented content-backed api-validation practice in Laravel')
            ->assertJsonPath('data.portfolio.source_coverage.0.source_path', 'laravel/api-integration.en.json')
            ->assertJsonPath('data.progress_payload.items.0.label', 'Write portfolio headline')
            ->assertJsonStructure([
                'data' => [
                    'title',
                    'technology',
                    'commit_plan',
                    'portfolio' => [
                        'headline',
                        'summary',
                        'source_coverage',
                        'changed_files',
                        'verification',
                        'interview_talking_points',
                        'readme_template',
                    ],
                    'progress_payload',
                ],
            ]);
    }

    /**
     * React render portfolio artifacts explain measurement-first optimization evidence.
     */
    public function test_react_render_portfolio_artifact_is_frontend_specific(): void
    {
        $this->getJson('/api/practice/technology-portfolio-artifact/react-render-performance?language=en&search=React%20re-renders&limit=3')
            ->assertOk()
            ->assertJsonPath('data.technology', 'react-render-performance')
            ->assertJsonPath('data.portfolio.headline', 'Built a measurement-first React render optimization workbench')
            ->assertJsonPath('data.portfolio.summary.0', 'Built a React render optimization practice artifact around measurement-first performance work.')
            ->assertJsonPath('data.portfolio.interview_talking_points.1', 'I start from React Profiler evidence instead of assuming every render is a bug.')
            ->assertJsonPath(
                'data.portfolio.readme_template',
                fn (string $readme): bool => str_contains($readme, 'React Render Optimization Practice Artifact')
                    && str_contains($readme, 'State/context/list rendering problems are considered before blanket memoization.')
            );
    }

    /**
     * JavaScript closure portfolio artifacts explain lexical scope and captured bindings.
     */
    public function test_javascript_closure_portfolio_artifact_is_scope_specific(): void
    {
        $this->getJson('/api/practice/technology-portfolio-artifact/javascript-closures?language=en&search=JavaScript%20closure&limit=3')
            ->assertOk()
            ->assertJsonPath('data.technology', 'javascript-closures')
            ->assertJsonPath('data.portfolio.headline', 'Built a JavaScript closure lexical-scope interview artifact')
            ->assertJsonPath('data.portfolio.summary.0', 'Built a JavaScript closure interview artifact around lexical scope and captured bindings.')
            ->assertJsonPath('data.portfolio.interview_talking_points.1', 'I explain closure as a function keeping access to variables from the lexical scope where it was created.')
            ->assertJsonPath(
                'data.portfolio.readme_template',
                fn (string $readme): bool => str_contains($readme, 'JavaScript Closure Practice Artifact')
                    && str_contains($readme, 'Closure is explained as function plus access to variables from lexical scope.')
                    && str_contains($readme, 'Interview evidence covers private state, var versus let loop behavior, stale closures, debounce, throttle, memoization, and React hook dependencies.')
            );
    }

    /**
     * Arrow-function this portfolio artifacts explain lexical this and call-site traps.
     */
    public function test_arrow_this_portfolio_artifact_is_lexical_this_specific(): void
    {
        $this->getJson('/api/practice/technology-portfolio-artifact/javascript-closures?language=en&search=arrow%20function%20this&limit=3')
            ->assertOk()
            ->assertJsonPath('data.technology', 'javascript-closures')
            ->assertJsonPath('data.portfolio.headline', 'Built a JavaScript arrow-function lexical-this interview artifact')
            ->assertJsonPath('data.portfolio.summary.0', 'Built a JavaScript arrow-function this artifact around lexical this and call-site comparison.')
            ->assertJsonPath('data.portfolio.interview_talking_points.1', 'I explain that arrow functions do not create their own `this`; they read `this` from the lexical scope where they are created.')
            ->assertJsonPath(
                'data.portfolio.readme_template',
                fn (string $readme): bool => str_contains($readme, 'JavaScript Arrow Function This Practice Artifact')
                    && str_contains($readme, 'Arrow functions are explained as having no own `this`; they read `this` from the lexical scope where they are created.')
                    && str_contains($readme, 'Interview evidence covers `obj.arrow()` traps, `call`/`apply`/`bind` limitations, timer callbacks, class callbacks, and when a normal method is safer.')
            );
    }

    /**
     * SQL Injection portfolio artifacts explain binding and allowlist evidence.
     */
    public function test_sql_injection_portfolio_artifact_is_security_specific(): void
    {
        $this->getJson('/api/practice/technology-portfolio-artifact/sql-injection-defense?language=en&search=SQL%20Injection&limit=3')
            ->assertOk()
            ->assertJsonPath('data.technology', 'sql-injection-defense')
            ->assertJsonPath('data.portfolio.headline', 'Built a parameterized-query SQL Injection defense workbench')
            ->assertJsonPath('data.portfolio.summary.0', 'Built a SQL Injection defense artifact around parameterized queries and boundary review.')
            ->assertJsonPath('data.portfolio.interview_talking_points.1', 'I explain SQL Injection as user input crossing a boundary and becoming SQL logic.')
            ->assertJsonPath(
                'data.portfolio.readme_template',
                fn (string $readme): bool => str_contains($readme, 'SQL Injection Defense Practice Artifact')
                    && str_contains($readme, 'Dynamic identifiers are checked against allowlists before reaching SQL.')
            );
    }

    /**
     * CSRF portfolio artifacts explain token proof and SameSite evidence.
     */
    public function test_csrf_portfolio_artifact_is_security_specific(): void
    {
        $this->getJson('/api/practice/technology-portfolio-artifact/csrf-protection?language=en&search=CSRF&limit=3')
            ->assertOk()
            ->assertJsonPath('data.technology', 'csrf-protection')
            ->assertJsonPath('data.portfolio.headline', 'Built a CSRF token and SameSite protection workbench')
            ->assertJsonPath('data.portfolio.summary.0', 'Built a CSRF protection artifact around browser intent, token proof, and cookie boundaries.')
            ->assertJsonPath('data.portfolio.interview_talking_points.1', 'I explain CSRF as browser intent being forged while cookies are sent automatically.')
            ->assertJsonPath(
                'data.portfolio.readme_template',
                fn (string $readme): bool => str_contains($readme, 'CSRF Protection Practice Artifact')
                    && str_contains($readme, 'SameSite cookie behavior is documented as a supporting control, not a token replacement.')
            );
    }

    /**
     * XSS portfolio artifacts explain safe rendering and payload-test evidence.
     */
    public function test_xss_portfolio_artifact_is_security_specific(): void
    {
        $this->getJson('/api/practice/technology-portfolio-artifact/xss-defense?language=en&search=XSS&limit=3')
            ->assertOk()
            ->assertJsonPath('data.technology', 'xss-defense')
            ->assertJsonPath('data.portfolio.headline', 'Built an XSS-safe Blade escaping and payload-test workbench')
            ->assertJsonPath('data.portfolio.summary.0', 'Built an XSS defense artifact around context-aware escaping and safe browser rendering.')
            ->assertJsonPath('data.portfolio.interview_talking_points.1', 'I explain XSS as untrusted data reaching an executable browser context.')
            ->assertJsonPath(
                'data.portfolio.readme_template',
                fn (string $readme): bool => str_contains($readme, 'XSS Defense Practice Artifact')
                    && str_contains($readme, 'Raw HTML is removed, sanitized, or isolated behind a trusted-content boundary.')
            );
    }

    /**
     * Security Misconfiguration portfolio artifacts explain production readiness evidence.
     */
    public function test_security_misconfiguration_portfolio_artifact_is_configuration_specific(): void
    {
        $this->getJson('/api/practice/technology-portfolio-artifact/security-misconfiguration?language=en&search=Security%20Misconfiguration&limit=3')
            ->assertOk()
            ->assertJsonPath('data.technology', 'security-misconfiguration')
            ->assertJsonPath('data.portfolio.headline', 'Built a Security Misconfiguration production-readiness artifact')
            ->assertJsonPath('data.portfolio.summary.0', 'Built a Security Misconfiguration artifact around production configuration readiness.')
            ->assertJsonPath('data.portfolio.interview_talking_points.1', 'I explain misconfiguration as unsafe runtime or deployment setup that exposes data or weakens defenses.')
            ->assertJsonPath(
                'data.portfolio.readme_template',
                fn (string $readme): bool => str_contains($readme, 'Security Misconfiguration Practice Artifact')
                    && str_contains($readme, 'Production readiness checks cover `APP_DEBUG`, `APP_ENV`, config cache, verbose exception output, and log redaction.')
                    && str_contains($readme, 'Smoke checks fail release when configuration is missing, unsafe, or broader than documented.')
            );
    }

    /**
     * IDOR portfolio artifacts explain object-level authorization and denial-test evidence.
     */
    public function test_idor_portfolio_artifact_is_object_authorization_specific(): void
    {
        $this->getJson('/api/practice/technology-portfolio-artifact/idor-access-control?language=en&search=IDOR&limit=3')
            ->assertOk()
            ->assertJsonPath('data.technology', 'idor-access-control')
            ->assertJsonPath('data.portfolio.headline', 'Built an IDOR object-level authorization review artifact')
            ->assertJsonPath('data.portfolio.summary.0', 'Built an IDOR defense artifact around object-level authorization and scoped lookup.')
            ->assertJsonPath('data.portfolio.interview_talking_points.1', 'I explain IDOR as broken object-level authorization where a valid user changes an object identifier to access someone else data.')
            ->assertJsonPath(
                'data.portfolio.readme_template',
                fn (string $readme): bool => str_contains($readme, 'IDOR Object-Level Authorization Practice Artifact')
                    && str_contains($readme, 'Object lookup is scoped through the current user, tenant, organization, team, or parent resource before data is returned.')
                    && str_contains($readme, 'Cross-user and cross-tenant tests prove ID swaps fail for read, update, delete, download, export, and nested-resource flows.')
            );
    }

    /**
     * Broken Authentication portfolio artifacts explain lifecycle hardening and failure-path evidence.
     */
    public function test_broken_authentication_portfolio_artifact_is_lifecycle_specific(): void
    {
        $this->getJson('/api/practice/technology-portfolio-artifact/auth-security?language=en&search=Broken%20Authentication&limit=3')
            ->assertOk()
            ->assertJsonPath('data.technology', 'auth-security')
            ->assertJsonPath('data.portfolio.headline', 'Built a Broken Authentication lifecycle hardening artifact')
            ->assertJsonPath('data.portfolio.summary.0', 'Built a Broken Authentication artifact around authentication lifecycle hardening.')
            ->assertJsonPath('data.portfolio.interview_talking_points.1', 'I explain broken authentication as any weakness that lets the wrong person become, stay, or act as a user.')
            ->assertJsonPath(
                'data.portfolio.readme_template',
                fn (string $readme): bool => str_contains($readme, 'Broken Authentication Lifecycle Practice Artifact')
                    && str_contains($readme, 'Authentication is reviewed as a lifecycle: login, session creation, remember-me, password reset, MFA recovery, logout, token refresh, and revocation.')
                    && str_contains($readme, 'Failure-path tests prove brute force, stale reset token, reused reset token, logged-out session reuse, and revoked token reuse fail closed.')
            );
    }

    /**
     * OAuth portfolio artifacts explain PKCE verifier proof and callback validation evidence.
     */
    public function test_oauth_portfolio_artifact_is_pkce_specific(): void
    {
        $this->getJson('/api/practice/technology-portfolio-artifact/oauth-flow?language=en&search=PKCE&limit=3')
            ->assertOk()
            ->assertJsonPath('data.technology', 'oauth-flow')
            ->assertJsonPath('data.portfolio.headline', 'Built an OAuth Authorization Code with PKCE security workbench')
            ->assertJsonPath('data.portfolio.summary.0', 'Built an OAuth PKCE artifact around public-client login proof and callback validation.')
            ->assertJsonPath('data.portfolio.interview_talking_points.1', 'I explain PKCE as proof that the client exchanging the authorization code is the client that started the login.')
            ->assertJsonPath(
                'data.portfolio.readme_template',
                fn (string $readme): bool => str_contains($readme, 'OAuth Authorization Code with PKCE Practice Artifact')
                    && str_contains($readme, 'The authorize URL contains `code_challenge` and `code_challenge_method=S256`, but never `code_verifier`.')
                    && str_contains($readme, 'Callback validation covers state mismatch, redirect URI mismatch, reused code, and token fields in the URL.')
            );
    }

    /**
     * Graph traversal portfolio artifacts explain BFS/DFS goal, state, and guardrail evidence.
     */
    public function test_graph_traversal_portfolio_artifact_is_bfs_dfs_specific(): void
    {
        $this->getJson('/api/practice/technology-portfolio-artifact/graph-traversal?language=en&search=BFS%20DFS&limit=3')
            ->assertOk()
            ->assertJsonPath('data.technology', 'graph-traversal')
            ->assertJsonPath('data.portfolio.headline', 'Built a BFS versus DFS traversal decision artifact')
            ->assertJsonPath('data.portfolio.summary.0', 'Built a BFS versus DFS graph traversal artifact around goal-first algorithm choice.')
            ->assertJsonPath('data.portfolio.interview_talking_points.1', 'I explain BFS and DFS as traversal choices driven by goal, graph shape, and production constraints.')
            ->assertJsonPath(
                'data.portfolio.readme_template',
                fn (string $readme): bool => str_contains($readme, 'BFS Versus DFS Graph Traversal Practice Artifact')
                    && str_contains($readme, 'BFS is explained through queue frontier, level-order traversal, nearest result, and shortest unweighted path behavior.')
                    && str_contains($readme, 'Visited set, cycle detection, max depth, max nodes, fan-out, pagination, batching, rate limits, timeouts, and memory pressure are documented before promotion.')
            );
    }

    /**
     * PHP stack and heap portfolio artifacts explain runtime-memory evidence.
     */
    public function test_php_stack_heap_portfolio_artifact_is_runtime_memory_specific(): void
    {
        $this->getJson('/api/practice/technology-portfolio-artifact/php?language=en&search=stack%20memory&limit=3')
            ->assertOk()
            ->assertJsonPath('data.technology', 'php')
            ->assertJsonPath('data.portfolio.headline', 'Built a PHP stack versus heap runtime-memory artifact')
            ->assertJsonPath('data.portfolio.summary.0', 'Built a PHP runtime-memory artifact around stack call frames and heap-backed data.')
            ->assertJsonPath('data.portfolio.interview_talking_points.1', 'I explain stack memory as active call frames and heap memory as backing larger arrays, objects, strings, references, and object graphs.')
            ->assertJsonPath(
                'data.portfolio.readme_template',
                fn (string $readme): bool => str_contains($readme, 'PHP Stack Versus Heap Practice Artifact')
                    && str_contains($readme, 'Heap-backed memory is explained through arrays, objects, strings, references, and object graphs.')
                    && str_contains($readme, 'Cleanup evidence covers scope exit, unset(), reference counting, garbage collection, and long-running worker risk.')
            );
    }

    /**
     * AI type comparison portfolio artifacts explain output contracts and separate quality evidence.
     */
    public function test_predictive_generative_ai_portfolio_artifact_is_ai_type_specific(): void
    {
        $this->getJson('/api/practice/technology-portfolio-artifact/llm-foundations?language=en&search=Predictive%20AI&limit=3')
            ->assertOk()
            ->assertJsonPath('data.technology', 'llm-foundations')
            ->assertJsonPath('data.portfolio.headline', 'Built a Predictive AI versus Generative AI comparison artifact')
            ->assertJsonPath('data.portfolio.summary.0', 'Built an AI type comparison artifact around Predictive AI and Generative AI output contracts.')
            ->assertJsonPath('data.portfolio.interview_talking_points.1', 'I explain Predictive AI as score, label, forecast, ranking, or risk-decision output from existing data.')
            ->assertJsonPath(
                'data.portfolio.readme_template',
                fn (string $readme): bool => str_contains($readme, 'Predictive AI Versus Generative AI Practice Artifact')
                    && str_contains($readme, 'Predictive AI is explained through scores, labels, forecasts, rankings, and risk decisions from existing data.')
                    && str_contains($readme, 'Quality evidence separates predictive metrics from generative checks and names failure modes for both sides.')
            );
    }

    /**
     * AI agent memory portfolio artifacts explain memory contracts and governance evidence.
     */
    public function test_ai_agent_memory_portfolio_artifact_is_memory_contract_specific(): void
    {
        $this->getJson('/api/practice/technology-portfolio-artifact/llm-foundations?language=en&search=agent%20memory&limit=3')
            ->assertOk()
            ->assertJsonPath('data.technology', 'llm-foundations')
            ->assertJsonPath('data.portfolio.headline', 'Built an AI agent memory contract and governance artifact')
            ->assertJsonPath('data.portfolio.summary.0', 'Built an AI agent memory artifact around working, episodic, semantic, and procedural memory contracts.')
            ->assertJsonPath('data.portfolio.interview_talking_points.1', 'I explain working, episodic, semantic, and procedural memory as separate contracts instead of one prompt-history bucket.')
            ->assertJsonPath(
                'data.portfolio.readme_template',
                fn (string $readme): bool => str_contains($readme, 'AI Agent Memory Practice Artifact')
                    && str_contains($readme, 'Working memory is bounded to current task state and expires before it becomes durable knowledge.')
                    && str_contains($readme, 'Procedural memory points to reviewed playbooks and runbooks instead of letting the agent invent hidden workflow rules.')
            );
    }

    /**
     * RAG system portfolio artifacts explain context routing and answer-contract evidence.
     */
    public function test_rag_context_strategy_portfolio_artifact_is_chatbot_specific(): void
    {
        $this->getJson('/api/practice/technology-portfolio-artifact/rag-systems?language=en&search=Long%20Context%20CAG&limit=3')
            ->assertOk()
            ->assertJsonPath('data.technology', 'rag-systems')
            ->assertJsonPath('data.portfolio.headline', 'Built a RAG, Long Context, and CAG chatbot strategy artifact')
            ->assertJsonPath('data.portfolio.summary.0', 'Built an AI chatbot context-strategy artifact around RAG, Long Context, CAG, and hybrid routing.')
            ->assertJsonPath('data.portfolio.interview_talking_points.1', 'I explain RAG, Long Context, CAG, and hybrid routing as different ways to supply trustworthy context to an AI chatbot.')
            ->assertJsonPath(
                'data.portfolio.readme_template',
                fn (string $readme): bool => str_contains($readme, 'RAG, Long Context, and CAG Chatbot Strategy Artifact')
                    && str_contains($readme, 'Context strategy is selected from corpus freshness, size, stability, permissions, latency, cost, and evidence requirements.')
                    && str_contains($readme, 'Guardrails cover tenant-scoped retrieval, packed-document token limits, CAG cache invalidation, source freshness, unauthorized chunks, and hybrid route selection.')
            );
    }

    /**
     * Covering Index portfolio artifacts explain heap-fetch and INCLUDE evidence.
     */
    public function test_covering_index_portfolio_artifact_is_query_plan_specific(): void
    {
        $this->getJson('/api/practice/technology-portfolio-artifact/database-eloquent?language=en&search=Covering%20Index&limit=3')
            ->assertOk()
            ->assertJsonPath('data.technology', 'database-eloquent')
            ->assertJsonPath('data.portfolio.headline', 'Built a PostgreSQL covering-index and heap-fetch evidence artifact')
            ->assertJsonPath('data.portfolio.summary.0', 'Built a PostgreSQL covering-index artifact around heap-fetch reduction and Index Only Scan evidence.')
            ->assertJsonPath('data.portfolio.interview_talking_points.1', 'I explain heap fetch as the extra table-read step that can keep an indexed query slow.')
            ->assertJsonPath(
                'data.portfolio.readme_template',
                fn (string $readme): bool => str_contains($readme, 'PostgreSQL Covering Index Practice Artifact')
                    && str_contains($readme, 'Before and after `EXPLAIN (ANALYZE, BUFFERS)` output records Index Scan versus Index Only Scan and Heap Fetches.')
                    && str_contains($readme, 'Visibility map, VACUUM or autovacuum expectations, index size, bloat risk, write overhead, and rollback are documented before promotion.')
            );
    }

    /**
     * Database locking portfolio artifacts explain transactions, row locks, and contention evidence.
     */
    public function test_database_locking_portfolio_artifact_is_concurrency_specific(): void
    {
        $this->getJson('/api/practice/technology-portfolio-artifact/database-eloquent?language=en&search=lockForUpdate&limit=3')
            ->assertOk()
            ->assertJsonPath('data.technology', 'database-eloquent')
            ->assertJsonPath('data.portfolio.headline', 'Built a database locking and lockForUpdate concurrency artifact')
            ->assertJsonPath('data.portfolio.summary.0', 'Built a database locking artifact around transaction-safe concurrency control.')
            ->assertJsonPath('data.portfolio.interview_talking_points.1', 'I explain database locking as the mechanism that stops concurrent requests from reading and changing the same critical row unsafely.')
            ->assertJsonPath(
                'data.portfolio.readme_template',
                fn (string $readme): bool => str_contains($readme, 'Database Locking Practice Artifact')
                    && str_contains($readme, '`lockForUpdate()` runs inside `DB::transaction()` on a selective, indexed row lookup before the protected value is read and changed.')
                    && str_contains($readme, 'Deadlock retry, lock timeout, hot-row contention, and lock-wait monitoring are reviewed before promotion.')
            );
    }
}
