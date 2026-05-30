<?php

declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;

final class TechnologySkillAssessmentTest extends TestCase
{
    /**
     * The technology skill assessment page renders a scored rubric and improvement tasks.
     */
    public function test_technology_skill_assessment_page_renders_rubric_and_improvement_tasks(): void
    {
        $response = $this->get('/practice/technology-skill-assessment/api-validation?family=laravel&language=en&search=api&limit=3');

        $response
            ->assertOk()
            ->assertSee('Technology Skill Assessment: api-validation')
            ->assertSee('Pass score 80 / 100')
            ->assertSee('Source understanding')
            ->assertSee('Laravel layer placement')
            ->assertSee('Improvement Tasks')
            ->assertSee('Open assessment API');
    }

    /**
     * The technology skill assessment API returns rubric rows and readiness signals.
     */
    public function test_technology_skill_assessment_api_returns_rubric_and_progress_payload(): void
    {
        $response = $this->getJson('/api/practice/technology-skill-assessment/api-validation?family=laravel&language=en&search=api&limit=3');

        $response
            ->assertOk()
            ->assertJsonPath('data.technology', 'api-validation')
            ->assertJsonPath('data.max_score', 100)
            ->assertJsonPath('data.pass_score', 80)
            ->assertJsonPath('data.rubric.0.criterion', 'Source understanding')
            ->assertJsonPath('data.progress_payload.items.0.label', 'Source understanding')
            ->assertJsonStructure([
                'data' => [
                    'title',
                    'technology',
                    'interview_pack',
                    'rubric' => [
                        '*' => [
                            'criterion',
                            'points',
                            'evidence',
                            'pass_signal',
                        ],
                    ],
                    'max_score',
                    'pass_score',
                    'readiness',
                    'improvement_tasks',
                    'progress_payload',
                ],
            ]);
    }

    /**
     * React render assessments score profiler evidence and memoization fit.
     */
    public function test_react_render_skill_assessment_uses_frontend_performance_rubric(): void
    {
        $this->getJson('/api/practice/technology-skill-assessment/react-render-performance?language=en&search=React%20re-renders&limit=3')
            ->assertOk()
            ->assertJsonPath('data.technology', 'react-render-performance')
            ->assertJsonPath('data.rubric.0.criterion', 'Profiler baseline')
            ->assertJsonPath('data.rubric.1.criterion', 'Memoization fit')
            ->assertJsonPath('data.rubric.2.criterion', 'Structural render control')
            ->assertJsonPath('data.improvement_tasks.0', 'Capture one React Profiler before/after note for the selected component.')
            ->assertJsonPath('data.progress_payload.items.0.label', 'Profiler baseline');
    }

    /**
     * JavaScript closure assessments score lexical scope, captured bindings, and traps.
     */
    public function test_javascript_closure_skill_assessment_uses_scope_rubric(): void
    {
        $this->getJson('/api/practice/technology-skill-assessment/javascript-closures?language=en&search=JavaScript%20closure&limit=3')
            ->assertOk()
            ->assertJsonPath('data.technology', 'javascript-closures')
            ->assertJsonPath('data.rubric.0.criterion', 'Lexical scope model')
            ->assertJsonPath('data.rubric.1.criterion', 'Captured binding behavior')
            ->assertJsonPath('data.rubric.2.criterion', 'Practical usage')
            ->assertJsonPath('data.rubric.3.criterion', 'Interview traps')
            ->assertJsonPath('data.improvement_tasks.0', 'Write one createCounter() example and trace which outer variable binding is captured.')
            ->assertJsonPath('data.progress_payload.items.0.label', 'Lexical scope model');
    }

    /**
     * Arrow-function this assessments score lexical this and binding traps.
     */
    public function test_arrow_this_skill_assessment_uses_lexical_this_rubric(): void
    {
        $this->getJson('/api/practice/technology-skill-assessment/javascript-closures?language=en&search=arrow%20function%20this&limit=3')
            ->assertOk()
            ->assertJsonPath('data.technology', 'javascript-closures')
            ->assertJsonPath('data.rubric.0.criterion', 'Lexical this model')
            ->assertJsonPath('data.rubric.1.criterion', 'Call-site comparison')
            ->assertJsonPath('data.rubric.2.criterion', 'Binding traps')
            ->assertJsonPath('data.rubric.3.criterion', 'Practical callback use')
            ->assertJsonPath('data.improvement_tasks.0', 'Write one object with a normal method and an arrow property, then trace why `obj.normal()` and `obj.arrow()` produce different `this` values.')
            ->assertJsonPath('data.progress_payload.items.0.label', 'Lexical this model');
    }

    /**
     * SQL Injection assessments score mechanism, binding, allowlists, and payload tests.
     */
    public function test_sql_injection_skill_assessment_uses_security_rubric(): void
    {
        $this->getJson('/api/practice/technology-skill-assessment/sql-injection-defense?language=en&search=SQL%20Injection&limit=3')
            ->assertOk()
            ->assertJsonPath('data.technology', 'sql-injection-defense')
            ->assertJsonPath('data.rubric.0.criterion', 'Injection mechanism')
            ->assertJsonPath('data.rubric.1.criterion', 'Parameterized values')
            ->assertJsonPath('data.rubric.2.criterion', 'Identifier allowlists')
            ->assertJsonPath('data.improvement_tasks.0', 'Rewrite one unsafe SQL example using query builder or raw SQL bindings.')
            ->assertJsonPath('data.progress_payload.items.0.label', 'Injection mechanism');
    }

    /**
     * CSRF assessments score attack mechanism, token proof, cookie boundary, and failure tests.
     */
    public function test_csrf_skill_assessment_uses_security_rubric(): void
    {
        $this->getJson('/api/practice/technology-skill-assessment/csrf-protection?language=en&search=CSRF&limit=3')
            ->assertOk()
            ->assertJsonPath('data.technology', 'csrf-protection')
            ->assertJsonPath('data.rubric.0.criterion', 'Attack mechanism')
            ->assertJsonPath('data.rubric.1.criterion', 'Token proof')
            ->assertJsonPath('data.rubric.2.criterion', 'Cookie boundary')
            ->assertJsonPath('data.improvement_tasks.0', 'Rewrite one state-changing GET route into POST, PUT, PATCH, or DELETE.')
            ->assertJsonPath('data.progress_payload.items.0.label', 'Attack mechanism');
    }

    /**
     * XSS assessments score attack variants, escaping, raw HTML boundaries, and payload tests.
     */
    public function test_xss_skill_assessment_uses_security_rubric(): void
    {
        $this->getJson('/api/practice/technology-skill-assessment/xss-defense?language=en&search=XSS&limit=3')
            ->assertOk()
            ->assertJsonPath('data.technology', 'xss-defense')
            ->assertJsonPath('data.rubric.0.criterion', 'Attack variants')
            ->assertJsonPath('data.rubric.1.criterion', 'Context-aware escaping')
            ->assertJsonPath('data.rubric.2.criterion', 'Raw HTML trust boundary')
            ->assertJsonPath('data.improvement_tasks.0', 'Rewrite one unsafe Blade or JavaScript output path with context-aware escaping.')
            ->assertJsonPath('data.progress_payload.items.0.label', 'Attack variants');
    }

    /**
     * Security Misconfiguration assessments score unsafe defaults, drift, boundaries, and smoke checks.
     */
    public function test_security_misconfiguration_skill_assessment_uses_configuration_rubric(): void
    {
        $this->getJson('/api/practice/technology-skill-assessment/security-misconfiguration?language=en&search=Security%20Misconfiguration&limit=3')
            ->assertOk()
            ->assertJsonPath('data.technology', 'security-misconfiguration')
            ->assertJsonPath('data.rubric.0.criterion', 'Unsafe default detection')
            ->assertJsonPath('data.rubric.1.criterion', 'Environment drift control')
            ->assertJsonPath('data.rubric.2.criterion', 'Boundary hardening')
            ->assertJsonPath('data.rubric.3.criterion', 'Deployment smoke checks')
            ->assertJsonPath('data.improvement_tasks.0', 'Create one production readiness checklist for APP_DEBUG, APP_ENV, config cache, exception output, and log redaction.')
            ->assertJsonPath('data.progress_payload.items.0.label', 'Unsafe default detection');
    }

    /**
     * IDOR assessments score object inventory, scoped lookup, object authorization, and denial tests.
     */
    public function test_idor_skill_assessment_uses_object_authorization_rubric(): void
    {
        $this->getJson('/api/practice/technology-skill-assessment/idor-access-control?language=en&search=IDOR&limit=3')
            ->assertOk()
            ->assertJsonPath('data.technology', 'idor-access-control')
            ->assertJsonPath('data.rubric.0.criterion', 'Object surface inventory')
            ->assertJsonPath('data.rubric.1.criterion', 'Scoped lookup')
            ->assertJsonPath('data.rubric.2.criterion', 'Object authorization')
            ->assertJsonPath('data.rubric.3.criterion', 'Cross-user denial tests')
            ->assertJsonPath('data.improvement_tasks.0', 'Inventory one route group that accepts object IDs and map owner, tenant, role, and allowed actions.')
            ->assertJsonPath('data.progress_payload.items.0.label', 'Object surface inventory');
    }

    /**
     * Broken Authentication assessments score lifecycle, controls, failure tests, and auth logging.
     */
    public function test_broken_authentication_skill_assessment_uses_lifecycle_rubric(): void
    {
        $this->getJson('/api/practice/technology-skill-assessment/auth-security?language=en&search=Broken%20Authentication&limit=3')
            ->assertOk()
            ->assertJsonPath('data.technology', 'auth-security')
            ->assertJsonPath('data.rubric.0.criterion', 'Lifecycle model')
            ->assertJsonPath('data.rubric.1.criterion', 'Session and reset controls')
            ->assertJsonPath('data.rubric.2.criterion', 'Failure-path tests')
            ->assertJsonPath('data.rubric.3.criterion', 'Sensitive auth logging')
            ->assertJsonPath('data.improvement_tasks.0', 'Draw the authentication lifecycle from login through session creation, remember-me, password reset, MFA recovery, logout, token refresh, and revocation.')
            ->assertJsonPath('data.progress_payload.items.0.label', 'Lifecycle model');
    }

    /**
     * OAuth assessments score flow fit, PKCE proof, callback validation, and token boundaries.
     */
    public function test_oauth_skill_assessment_uses_pkce_rubric(): void
    {
        $this->getJson('/api/practice/technology-skill-assessment/oauth-flow?language=en&search=PKCE&limit=3')
            ->assertOk()
            ->assertJsonPath('data.technology', 'oauth-flow')
            ->assertJsonPath('data.rubric.0.criterion', 'Flow fit')
            ->assertJsonPath('data.rubric.1.criterion', 'PKCE proof')
            ->assertJsonPath('data.rubric.2.criterion', 'Callback validation')
            ->assertJsonPath('data.rubric.3.criterion', 'Token boundary')
            ->assertJsonPath('data.improvement_tasks.1', 'Generate one high-entropy code_verifier and derive a S256 code_challenge without logging or rendering the verifier.')
            ->assertJsonPath('data.progress_payload.items.1.label', 'PKCE proof');
    }

    /**
     * Graph traversal assessments score goal, state, cycle safety, and system fit.
     */
    public function test_graph_traversal_skill_assessment_uses_bfs_dfs_rubric(): void
    {
        $this->getJson('/api/practice/technology-skill-assessment/graph-traversal?language=en&search=BFS%20DFS&limit=3')
            ->assertOk()
            ->assertJsonPath('data.technology', 'graph-traversal')
            ->assertJsonPath('data.rubric.0.criterion', 'Traversal goal')
            ->assertJsonPath('data.rubric.1.criterion', 'State model')
            ->assertJsonPath('data.rubric.2.criterion', 'Cycle safety')
            ->assertJsonPath('data.rubric.3.criterion', 'System fit')
            ->assertJsonPath('data.improvement_tasks.0', 'Write one graph fixture and show the different visit order produced by BFS and DFS.')
            ->assertJsonPath('data.progress_payload.items.0.label', 'Traversal goal');
    }

    /**
     * PHP stack and heap assessments score call frames, heap data, cleanup, and failure modes.
     */
    public function test_php_stack_heap_skill_assessment_uses_runtime_memory_rubric(): void
    {
        $this->getJson('/api/practice/technology-skill-assessment/php?language=en&search=stack%20memory&limit=3')
            ->assertOk()
            ->assertJsonPath('data.technology', 'php')
            ->assertJsonPath('data.rubric.0.criterion', 'Call-frame model')
            ->assertJsonPath('data.rubric.1.criterion', 'Heap-backed data')
            ->assertJsonPath('data.rubric.2.criterion', 'Cleanup and references')
            ->assertJsonPath('data.rubric.3.criterion', 'Failure modes')
            ->assertJsonPath('data.improvement_tasks.0', 'Write a PHP function example and mark which values belong to the active call frame.')
            ->assertJsonPath('data.progress_payload.items.0.label', 'Call-frame model');
    }

    /**
     * AI type comparison assessments score output contracts, evidence, metrics, and failures.
     */
    public function test_predictive_generative_ai_skill_assessment_uses_ai_type_rubric(): void
    {
        $this->getJson('/api/practice/technology-skill-assessment/llm-foundations?language=en&search=Predictive%20AI&limit=3')
            ->assertOk()
            ->assertJsonPath('data.technology', 'llm-foundations')
            ->assertJsonPath('data.rubric.0.criterion', 'Output contract')
            ->assertJsonPath('data.rubric.1.criterion', 'Input evidence')
            ->assertJsonPath('data.rubric.2.criterion', 'Evaluation fit')
            ->assertJsonPath('data.rubric.3.criterion', 'Failure modes')
            ->assertJsonPath('data.improvement_tasks.0', 'Write a two-column comparison that separates Predictive AI output contracts from Generative AI output contracts.')
            ->assertJsonPath('data.progress_payload.items.0.label', 'Output contract');
    }

    /**
     * RAG system assessments score context strategy, answer contracts, router guardrails, and failure tests.
     */
    public function test_rag_context_strategy_skill_assessment_uses_chatbot_rubric(): void
    {
        $this->getJson('/api/practice/technology-skill-assessment/rag-systems?language=en&search=Long%20Context%20CAG&limit=3')
            ->assertOk()
            ->assertJsonPath('data.technology', 'rag-systems')
            ->assertJsonPath('data.rubric.0.criterion', 'Context strategy fit')
            ->assertJsonPath('data.rubric.1.criterion', 'Answer contract')
            ->assertJsonPath('data.rubric.2.criterion', 'Router guardrails')
            ->assertJsonPath('data.rubric.3.criterion', 'Failure and evaluation tests')
            ->assertJsonPath('data.improvement_tasks.0', 'Write a routing table that chooses RAG, Long Context, CAG, or hybrid routing from corpus freshness, size, stability, permissions, latency, and cost.')
            ->assertJsonPath('data.progress_payload.items.0.label', 'Context strategy fit');
    }

    /**
     * Covering Index assessments score plan evidence, INCLUDE design, and operational guardrails.
     */
    public function test_covering_index_skill_assessment_uses_query_plan_rubric(): void
    {
        $this->getJson('/api/practice/technology-skill-assessment/database-eloquent?language=en&search=Covering%20Index&limit=3')
            ->assertOk()
            ->assertJsonPath('data.technology', 'database-eloquent')
            ->assertJsonPath('data.rubric.0.criterion', 'Plan baseline')
            ->assertJsonPath('data.rubric.1.criterion', 'INCLUDE design')
            ->assertJsonPath('data.rubric.2.criterion', 'Visibility health')
            ->assertJsonPath('data.rubric.3.criterion', 'Cost guardrails')
            ->assertJsonPath('data.improvement_tasks.0', 'Capture before and after EXPLAIN (ANALYZE, BUFFERS) output for the hot query and record Heap Fetches.')
            ->assertJsonPath('data.progress_payload.items.0.label', 'Plan baseline');
    }

    /**
     * Database locking assessments score invariants, transactions, lock scope, and contention evidence.
     */
    public function test_database_locking_skill_assessment_uses_concurrency_rubric(): void
    {
        $this->getJson('/api/practice/technology-skill-assessment/database-eloquent?language=en&search=lockForUpdate&limit=3')
            ->assertOk()
            ->assertJsonPath('data.technology', 'database-eloquent')
            ->assertJsonPath('data.rubric.0.criterion', 'Protected invariant')
            ->assertJsonPath('data.rubric.1.criterion', 'Transaction boundary')
            ->assertJsonPath('data.rubric.2.criterion', 'Lock scope and ordering')
            ->assertJsonPath('data.rubric.3.criterion', 'Failure and operations evidence')
            ->assertJsonPath('data.improvement_tasks.0', 'Write the protected invariant before code: no negative stock, no double spend, or no duplicate workflow transition.')
            ->assertJsonPath('data.progress_payload.items.0.label', 'Protected invariant');
    }
}
