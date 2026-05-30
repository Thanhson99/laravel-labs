<?php

declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;

final class TechnologyEvidenceArchiveTest extends TestCase
{
    /**
     * The evidence archive page renders retrieval keys, proof, and reuse targets.
     */
    public function test_technology_evidence_archive_page_renders_archive_artifacts(): void
    {
        $response = $this->get('/practice/technology-evidence-archive/api-validation?family=laravel&language=en&search=api&limit=3');

        $response
            ->assertOk()
            ->assertSee('Technology Evidence Archive: api-validation')
            ->assertSee('Retrieval Keys')
            ->assertSee('Proof Bundle')
            ->assertSee('Reuse Targets')
            ->assertSee('technology:api-validation')
            ->assertSee('Open archive API');
    }

    /**
     * The evidence archive API returns archive id, retrieval keys, proof, and progress payload.
     */
    public function test_technology_evidence_archive_api_returns_archive_payload(): void
    {
        $response = $this->getJson('/api/practice/technology-evidence-archive/api-validation?family=laravel&language=en&search=api&limit=3');

        $response
            ->assertOk()
            ->assertJsonPath('data.technology', 'api-validation')
            ->assertJsonPath('data.retrieval_keys.0', 'technology:api-validation')
            ->assertJsonPath('data.progress_payload.items.0.label', 'Save archive id')
            ->assertJsonStructure([
                'data' => [
                    'title',
                    'technology',
                    'archive_id',
                    'review',
                    'retrieval_keys',
                    'proof_bundle' => [
                        '*' => [
                            'label',
                            'detail',
                        ],
                    ],
                    'reuse_targets',
                    'retrieval_prompts',
                    'progress_payload',
                ],
            ]);
    }

    /**
     * SQL Injection evidence archives add retrieval keys for bindings, allowlists, and payload tests.
     */
    public function test_sql_injection_evidence_archive_uses_security_retrieval_keys(): void
    {
        $this->getJson('/api/practice/technology-evidence-archive/sql-injection-defense?language=en&search=SQL%20Injection&limit=3')
            ->assertOk()
            ->assertJsonPath('data.technology', 'sql-injection-defense')
            ->assertJsonPath('data.retrieval_keys.5', 'security:sql-injection')
            ->assertJsonPath('data.retrieval_keys.6', 'defense:parameterized-query')
            ->assertJsonPath('data.retrieval_keys.7', 'defense:identifier-allowlist')
            ->assertJsonPath('data.retrieval_prompts.0', 'Explain SQL Injection from attacker input to changed query logic, then show how parameterized queries stop it.');
    }

    /**
     * CSRF evidence archives add retrieval keys for tokens, SameSite, and stale-token tests.
     */
    public function test_csrf_evidence_archive_uses_security_retrieval_keys(): void
    {
        $this->getJson('/api/practice/technology-evidence-archive/csrf-protection?language=en&search=CSRF&limit=3')
            ->assertOk()
            ->assertJsonPath('data.technology', 'csrf-protection')
            ->assertJsonPath('data.retrieval_keys.5', 'security:csrf')
            ->assertJsonPath('data.retrieval_keys.6', 'defense:csrf-token')
            ->assertJsonPath('data.retrieval_keys.7', 'defense:samesite-cookie')
            ->assertJsonPath('data.retrieval_prompts.0', 'Explain CSRF from forged browser intent to unwanted state change, then show how session-bound tokens stop it.');
    }

    /**
     * XSS evidence archives add retrieval keys for escaping, sanitization, and payload rendering.
     */
    public function test_xss_evidence_archive_uses_security_retrieval_keys(): void
    {
        $this->getJson('/api/practice/technology-evidence-archive/xss-defense?language=en&search=XSS&limit=3')
            ->assertOk()
            ->assertJsonPath('data.technology', 'xss-defense')
            ->assertJsonPath('data.retrieval_keys.5', 'security:xss')
            ->assertJsonPath('data.retrieval_keys.6', 'defense:context-aware-escaping')
            ->assertJsonPath('data.retrieval_keys.7', 'defense:sanitized-rich-text')
            ->assertJsonPath('data.retrieval_prompts.0', 'Explain XSS from untrusted input to executable browser context, then show how context-aware escaping stops it.');
    }

    /**
     * Security Misconfiguration evidence archives add retrieval keys for production readiness.
     */
    public function test_security_misconfiguration_evidence_archive_uses_configuration_retrieval_keys(): void
    {
        $this->getJson('/api/practice/technology-evidence-archive/security-misconfiguration?language=en&search=Security%20Misconfiguration&limit=3')
            ->assertOk()
            ->assertJsonPath('data.technology', 'security-misconfiguration')
            ->assertJsonPath('data.retrieval_keys.5', 'security:misconfiguration')
            ->assertJsonPath('data.retrieval_keys.6', 'config:production-readiness')
            ->assertJsonPath('data.retrieval_keys.7', 'defense:fail-closed-smoke-checks')
            ->assertJsonPath('data.retrieval_keys.8', 'risk:environment-drift')
            ->assertJsonPath('data.retrieval_prompts.0', 'Explain Security Misconfiguration from unsafe runtime setup to production exposure, then show how readiness checks stop release.')
            ->assertJsonPath('data.retrieval_prompts.2', 'Reuse one proof item in an interview answer that compares unsafe defaults, environment drift, boundary hardening, and fail-closed smoke checks.');
    }

    /**
     * IDOR evidence archives add retrieval keys for object authorization and ID-swap denial tests.
     */
    public function test_idor_evidence_archive_uses_object_authorization_retrieval_keys(): void
    {
        $this->getJson('/api/practice/technology-evidence-archive/idor-access-control?language=en&search=IDOR&limit=3')
            ->assertOk()
            ->assertJsonPath('data.technology', 'idor-access-control')
            ->assertJsonPath('data.retrieval_keys.5', 'security:idor')
            ->assertJsonPath('data.retrieval_keys.6', 'defense:object-level-authorization')
            ->assertJsonPath('data.retrieval_keys.7', 'defense:scoped-model-lookup')
            ->assertJsonPath('data.retrieval_keys.8', 'test:cross-user-id-swap-denial')
            ->assertJsonPath('data.retrieval_prompts.0', 'Explain IDOR as broken object-level authorization, then show how scoped lookup and policy checks stop ID swaps.')
            ->assertJsonPath('data.retrieval_prompts.2', 'Reuse one proof item in an interview answer that compares authentication, authorization, route model binding, policies, and audit evidence.');
    }

    /**
     * OAuth evidence archives add retrieval keys for PKCE verifier proof and callback leakage tests.
     */
    public function test_oauth_evidence_archive_uses_pkce_retrieval_keys(): void
    {
        $this->getJson('/api/practice/technology-evidence-archive/oauth-flow?language=en&search=PKCE&limit=3')
            ->assertOk()
            ->assertJsonPath('data.technology', 'oauth-flow')
            ->assertJsonPath('data.retrieval_keys.5', 'security:oauth-pkce')
            ->assertJsonPath('data.retrieval_keys.6', 'defense:s256-code-challenge')
            ->assertJsonPath('data.retrieval_keys.7', 'defense:private-code-verifier')
            ->assertJsonPath('data.retrieval_keys.8', 'test:callback-token-leakage')
            ->assertJsonPath('data.retrieval_prompts.0', 'Explain PKCE from generated code_verifier to S256 code_challenge, then show why the verifier must stay private.')
            ->assertJsonPath('data.retrieval_prompts.2', 'Reuse one proof item in an interview answer that compares Authorization Code with PKCE, Implicit Flow, and Client Credentials.');
    }

    /**
     * Graph traversal evidence archives add retrieval keys for BFS/DFS and cycle safety.
     */
    public function test_graph_traversal_evidence_archive_uses_bfs_dfs_retrieval_keys(): void
    {
        $this->getJson('/api/practice/technology-evidence-archive/graph-traversal?language=en&search=BFS%20DFS&limit=3')
            ->assertOk()
            ->assertJsonPath('data.technology', 'graph-traversal')
            ->assertJsonPath('data.retrieval_keys.5', 'algorithm:bfs-dfs')
            ->assertJsonPath('data.retrieval_keys.6', 'graph:traversal-order')
            ->assertJsonPath('data.retrieval_keys.7', 'defense:visited-set-cycle-safety')
            ->assertJsonPath('data.retrieval_keys.8', 'system:api-database-hierarchy-guardrails')
            ->assertJsonPath('data.retrieval_prompts.0', 'Explain BFS versus DFS from traversal goal, then show how queue frontier differs from stack or recursion path.')
            ->assertJsonPath('data.retrieval_prompts.2', 'Reuse one proof item in an interview answer that compares nearest-hop BFS, depth-first DFS, shortest unweighted path, memory pressure, and production limits.');
    }

    /**
     * JavaScript closure evidence archives add retrieval keys for lexical scope and closure traps.
     */
    public function test_javascript_closure_evidence_archive_uses_scope_retrieval_keys(): void
    {
        $this->getJson('/api/practice/technology-evidence-archive/javascript-closures?language=en&search=JavaScript%20closure&limit=3')
            ->assertOk()
            ->assertJsonPath('data.technology', 'javascript-closures')
            ->assertJsonPath('data.retrieval_keys.5', 'javascript:closure')
            ->assertJsonPath('data.retrieval_keys.6', 'runtime:lexical-scope')
            ->assertJsonPath('data.retrieval_keys.7', 'runtime:captured-binding')
            ->assertJsonPath('data.retrieval_keys.8', 'interview:var-let-stale-closure')
            ->assertJsonPath('data.retrieval_prompts.0', 'Explain JavaScript closure from lexical scope to captured binding, then trace createCounter() across repeated calls.')
            ->assertJsonPath('data.retrieval_prompts.2', 'Reuse one proof item in an interview answer that compares private state, callbacks, debounce, throttle, memoization, and React hook closures.');
    }

    /**
     * Arrow-function this evidence archives add retrieval keys for lexical this and binding traps.
     */
    public function test_arrow_this_evidence_archive_uses_lexical_this_retrieval_keys(): void
    {
        $this->getJson('/api/practice/technology-evidence-archive/javascript-closures?language=en&search=arrow%20function%20this&limit=3')
            ->assertOk()
            ->assertJsonPath('data.technology', 'javascript-closures')
            ->assertJsonPath('data.retrieval_keys.5', 'javascript:arrow-function-this')
            ->assertJsonPath('data.retrieval_keys.6', 'runtime:lexical-this')
            ->assertJsonPath('data.retrieval_keys.7', 'runtime:call-site-this')
            ->assertJsonPath('data.retrieval_keys.8', 'interview:obj-arrow-bind-traps')
            ->assertJsonPath('data.retrieval_prompts.0', 'Explain arrow-function `this` as lexical this, then contrast it with normal function call-site this.')
            ->assertJsonPath('data.retrieval_prompts.2', 'Reuse one proof item in an interview answer that compares arrow callbacks, normal object methods, obj.arrow() traps, and call/apply/bind limitations.');
    }

    /**
     * PHP stack and heap evidence archives add retrieval keys for runtime-memory recall.
     */
    public function test_php_stack_heap_evidence_archive_uses_runtime_memory_retrieval_keys(): void
    {
        $this->getJson('/api/practice/technology-evidence-archive/php?language=en&search=stack%20memory&limit=3')
            ->assertOk()
            ->assertJsonPath('data.technology', 'php')
            ->assertJsonPath('data.retrieval_keys.5', 'php:stack-heap-memory')
            ->assertJsonPath('data.retrieval_keys.6', 'runtime:call-frames')
            ->assertJsonPath('data.retrieval_keys.7', 'runtime:heap-backed-data')
            ->assertJsonPath('data.retrieval_keys.8', 'risk:long-running-worker-memory')
            ->assertJsonPath('data.retrieval_prompts.0', 'Explain stack memory as active call frames, then contrast it with heap-backed arrays, objects, strings, and references.')
            ->assertJsonPath('data.retrieval_prompts.2', 'Reuse one proof item in an interview answer that avoids manual-allocation claims and names PHP production memory risks.');
    }

    /**
     * AI type comparison evidence archives add retrieval keys for contracts, metrics, and failures.
     */
    public function test_predictive_generative_ai_evidence_archive_uses_ai_type_retrieval_keys(): void
    {
        $this->getJson('/api/practice/technology-evidence-archive/llm-foundations?language=en&search=Predictive%20AI&limit=3')
            ->assertOk()
            ->assertJsonPath('data.technology', 'llm-foundations')
            ->assertJsonPath('data.retrieval_keys.5', 'ai:predictive-generative-comparison')
            ->assertJsonPath('data.retrieval_keys.6', 'ai:output-contracts')
            ->assertJsonPath('data.retrieval_keys.7', 'ai:evaluation-metrics')
            ->assertJsonPath('data.retrieval_keys.8', 'risk:ai-failure-modes')
            ->assertJsonPath('data.retrieval_prompts.0', 'Explain Predictive AI by output contract first: score, label, forecast, ranking, or risk decision.')
            ->assertJsonPath('data.retrieval_prompts.2', 'Reuse one proof item in an interview answer that compares predictive metrics, generative checks, drift, hallucination, prompt injection, and unsafe generated code.');
    }

    /**
     * AI agent memory evidence archives add retrieval keys for contracts, governance, and failure tests.
     */
    public function test_ai_agent_memory_evidence_archive_uses_memory_contract_retrieval_keys(): void
    {
        $this->getJson('/api/practice/technology-evidence-archive/llm-foundations?language=en&search=agent%20memory&limit=3')
            ->assertOk()
            ->assertJsonPath('data.technology', 'llm-foundations')
            ->assertJsonPath('data.retrieval_keys.5', 'ai-agent:memory-contracts')
            ->assertJsonPath('data.retrieval_keys.6', 'memory:working-episodic-semantic-procedural')
            ->assertJsonPath('data.retrieval_keys.7', 'governance:source-freshness-confidence-permission')
            ->assertJsonPath('data.retrieval_keys.8', 'test:stale-private-procedural-memory')
            ->assertJsonPath('data.retrieval_prompts.0', 'Explain AI agent memory as four separate contracts: working, episodic, semantic, and procedural memory.')
            ->assertJsonPath('data.retrieval_prompts.2', 'Reuse one proof item in an interview answer that compares task state, session history, durable facts, reviewed playbooks, and why prompt history is not enough.');
    }

    /**
     * Covering Index evidence archives add retrieval keys for heap-fetch and visibility-map recall.
     */
    public function test_covering_index_evidence_archive_uses_query_plan_retrieval_keys(): void
    {
        $this->getJson('/api/practice/technology-evidence-archive/database-eloquent?language=en&search=Covering%20Index&limit=3')
            ->assertOk()
            ->assertJsonPath('data.technology', 'database-eloquent')
            ->assertJsonPath('data.retrieval_keys.5', 'database:covering-index')
            ->assertJsonPath('data.retrieval_keys.6', 'postgres:index-only-scan')
            ->assertJsonPath('data.retrieval_keys.7', 'postgres:heap-fetches')
            ->assertJsonPath('data.retrieval_keys.8', 'ops:visibility-map-vacuum')
            ->assertJsonPath('data.retrieval_prompts.0', 'Explain why an indexed query can still be slow when Index Scan performs many heap fetches.')
            ->assertJsonPath('data.retrieval_prompts.2', 'Reuse one proof item in an interview answer that compares Index Scan, Index Only Scan, heap fetch cost, bloat risk, and write overhead.');
    }

    /**
     * Database locking evidence archives add retrieval keys for transaction-safe concurrency recall.
     */
    public function test_database_locking_evidence_archive_uses_concurrency_retrieval_keys(): void
    {
        $this->getJson('/api/practice/technology-evidence-archive/database-eloquent?language=en&search=lockForUpdate&limit=3')
            ->assertOk()
            ->assertJsonPath('data.technology', 'database-eloquent')
            ->assertJsonPath('data.retrieval_keys.5', 'database:locking')
            ->assertJsonPath('data.retrieval_keys.6', 'laravel:lockforupdate-transaction')
            ->assertJsonPath('data.retrieval_keys.7', 'concurrency:deadlock-timeout-contention')
            ->assertJsonPath('data.retrieval_keys.8', 'test:concurrent-write-invariant')
            ->assertJsonPath('data.retrieval_prompts.0', 'Explain database locking from protected invariant to concurrent write failure, then show how transaction-bound row locking stops it.')
            ->assertJsonPath('data.retrieval_prompts.2', 'Reuse one proof item in an interview answer that compares row lock, table lock, deadlock, lock contention, indexed lookup, and short critical sections.');
    }
}
