<?php

declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;

final class TechnologySpacedReviewTest extends TestCase
{
    /**
     * The spaced review page renders day-based recall and rebuild cards.
     */
    public function test_technology_spaced_review_page_renders_review_cards(): void
    {
        $response = $this->get('/practice/technology-spaced-review/api-validation?family=laravel&language=en&search=api&limit=3');

        $response
            ->assertOk()
            ->assertSee('Technology Spaced Review: api-validation')
            ->assertSee('Day 1 recall')
            ->assertSee('Day 3 rebuild')
            ->assertSee('Day 7 defense')
            ->assertSee('Promotion Criteria')
            ->assertSee('Open review API');
    }

    /**
     * The spaced review API returns review cards, criteria, and progress payload.
     */
    public function test_technology_spaced_review_api_returns_cards_and_progress_payload(): void
    {
        $response = $this->getJson('/api/practice/technology-spaced-review/api-validation?family=laravel&language=en&search=api&limit=3');

        $response
            ->assertOk()
            ->assertJsonPath('data.technology', 'api-validation')
            ->assertJsonPath('data.cards.0.label', 'Day 1 recall')
            ->assertJsonPath('data.cards.1.label', 'Day 3 rebuild')
            ->assertJsonPath('data.progress_payload.items.0.label', 'Day 1 recall')
            ->assertJsonStructure([
                'data' => [
                    'title',
                    'technology',
                    'checkpoint',
                    'cards' => [
                        '*' => [
                            'day',
                            'label',
                            'recall_prompt',
                            'evidence_recheck',
                            'coding_action',
                        ],
                    ],
                    'promotion_criteria',
                    'progress_payload',
                ],
            ]);
    }

    /**
     * SQL Injection spaced review asks for boundary, binding, allowlist, and payload recall.
     */
    public function test_sql_injection_spaced_review_uses_security_cards(): void
    {
        $this->getJson('/api/practice/technology-spaced-review/sql-injection-defense?language=en&search=SQL%20Injection&limit=3')
            ->assertOk()
            ->assertJsonPath('data.technology', 'sql-injection-defense')
            ->assertJsonPath('data.cards.0.recall_prompt', 'Explain SQL Injection as input becoming SQL logic, then name the exact binding or query builder defense you used.')
            ->assertJsonPath('data.cards.1.coding_action', 'Write one new failing payload test for OR 1=1 or invalid sort input, then make it pass.')
            ->assertJsonPath('data.cards.2.recall_prompt', 'Answer the interview question using mechanism, parameterized query, identifier allowlist, payload tests, and changed-file evidence.');
    }

    /**
     * CSRF spaced review asks for browser intent, token proof, and SameSite recall.
     */
    public function test_csrf_spaced_review_uses_security_cards(): void
    {
        $this->getJson('/api/practice/technology-spaced-review/csrf-protection?language=en&search=CSRF&limit=3')
            ->assertOk()
            ->assertJsonPath('data.technology', 'csrf-protection')
            ->assertJsonPath('data.cards.0.recall_prompt', 'Explain CSRF as forged browser intent, then name why cookies being sent automatically matters.')
            ->assertJsonPath('data.cards.1.coding_action', 'Write one new failing test for missing token or stale token behavior, then make it pass.')
            ->assertJsonPath('data.cards.2.recall_prompt', 'Answer the interview question using browser cookies, request intent, CSRF token, SameSite, failure tests, and changed-file evidence.');
    }

    /**
     * XSS spaced review asks for variant, escaping, sanitizer, and payload recall.
     */
    public function test_xss_spaced_review_uses_security_cards(): void
    {
        $this->getJson('/api/practice/technology-spaced-review/xss-defense?language=en&search=XSS&limit=3')
            ->assertOk()
            ->assertJsonPath('data.technology', 'xss-defense')
            ->assertJsonPath('data.cards.0.recall_prompt', 'Explain reflected, stored, and DOM-based XSS as untrusted data reaching executable browser context.')
            ->assertJsonPath('data.cards.1.coding_action', 'Write one new failing payload test for a script tag, event handler, or unsafe DOM sink, then make it pass.')
            ->assertJsonPath('data.cards.2.recall_prompt', 'Answer the interview question using XSS variant, output context, safe rendering boundary, payload tests, CSP limits, and changed-file evidence.');
    }

    /**
     * Security Misconfiguration spaced review asks for unsafe defaults and release smoke checks.
     */
    public function test_security_misconfiguration_spaced_review_uses_configuration_cards(): void
    {
        $this->getJson('/api/practice/technology-spaced-review/security-misconfiguration?language=en&search=Security%20Misconfiguration&limit=3')
            ->assertOk()
            ->assertJsonPath('data.technology', 'security-misconfiguration')
            ->assertJsonPath('data.cards.0.recall_prompt', 'Explain Security Misconfiguration as unsafe runtime or deployment setup, then name one dangerous production default.')
            ->assertJsonPath('data.cards.1.coding_action', 'Write one new failing configuration smoke check for debug mode, exposed secrets, broad CORS, missing headers, or proxy drift, then make it pass.')
            ->assertJsonPath('data.cards.2.recall_prompt', 'Answer the interview question using unsafe defaults, environment drift, CORS, headers, cookies, proxies, smoke checks, owners, and rollback evidence.');
    }

    /**
     * IDOR spaced review asks for object authorization, scoped lookup, and denial-test recall.
     */
    public function test_idor_spaced_review_uses_object_authorization_cards(): void
    {
        $this->getJson('/api/practice/technology-spaced-review/idor-access-control?language=en&search=IDOR&limit=3')
            ->assertOk()
            ->assertJsonPath('data.technology', 'idor-access-control')
            ->assertJsonPath('data.cards.0.recall_prompt', 'Explain IDOR as broken object-level authorization, then name why a logged-in user can still be unauthorized for a specific object.')
            ->assertJsonPath('data.cards.1.coding_action', 'Write one new failing test where user A replays user B object ID for read, update, download, export, or nested-resource access, then make it pass.')
            ->assertJsonPath('data.cards.2.recall_prompt', 'Answer the interview question using authentication versus authorization, scoped lookup, object policy, ID-swap tests, 403 or 404 behavior, and monitoring evidence.');
    }

    /**
     * OAuth spaced review asks for PKCE verifier, challenge, callback, and token-boundary recall.
     */
    public function test_oauth_spaced_review_uses_pkce_cards(): void
    {
        $this->getJson('/api/practice/technology-spaced-review/oauth-flow?language=en&search=PKCE&limit=3')
            ->assertOk()
            ->assertJsonPath('data.technology', 'oauth-flow')
            ->assertJsonPath('data.cards.0.recall_prompt', 'Explain PKCE as public-client proof, then name why code_verifier must stay private while code_challenge is sent.')
            ->assertJsonPath('data.cards.1.coding_action', 'Write one new failing test for missing verifier, wrong verifier, reused code, or token fields in callback input, then make it pass.')
            ->assertJsonPath('data.cards.2.recall_prompt', 'Answer the interview question using client type, Authorization Code with PKCE, S256 challenge, callback validation, token boundary, and changed-file evidence.');
    }

    /**
     * Graph traversal spaced review asks for BFS/DFS goal, state, and guardrail recall.
     */
    public function test_graph_traversal_spaced_review_uses_bfs_dfs_cards(): void
    {
        $this->getJson('/api/practice/technology-spaced-review/graph-traversal?language=en&search=BFS%20DFS&limit=3')
            ->assertOk()
            ->assertJsonPath('data.technology', 'graph-traversal')
            ->assertJsonPath('data.cards.0.recall_prompt', 'Explain BFS versus DFS from traversal goal first, then name queue frontier, stack or recursion path, and shortest unweighted path behavior.')
            ->assertJsonPath('data.cards.1.coding_action', 'Write one new cyclic graph fixture and prove max depth, max nodes, or visited-set handling prevents repeated work.')
            ->assertJsonPath('data.cards.2.recall_prompt', 'Answer the interview question using traversal goal, BFS queue, DFS stack or recursion, visited set, shortest path, API crawling, database hierarchy, and memory evidence.');
    }

    /**
     * JavaScript closure spaced review asks for lexical scope, bindings, and trap recall.
     */
    public function test_javascript_closure_spaced_review_uses_scope_cards(): void
    {
        $this->getJson('/api/practice/technology-spaced-review/javascript-closures?language=en&search=JavaScript%20closure&limit=3')
            ->assertOk()
            ->assertJsonPath('data.technology', 'javascript-closures')
            ->assertJsonPath('data.cards.0.recall_prompt', 'Explain a JavaScript closure as function plus lexical scope, then identify the captured binding in createCounter().')
            ->assertJsonPath('data.cards.1.coding_action', 'Write one new var versus let loop callback example and one stale-closure note for async code, timers, or hooks.')
            ->assertJsonPath('data.cards.2.recall_prompt', 'Answer the interview question using lexical scope, captured binding, private state, var versus let, stale closures, and practical callback use.');
    }

    /**
     * Arrow-function this spaced review asks for lexical-this and binding-trap recall.
     */
    public function test_arrow_this_spaced_review_uses_lexical_this_cards(): void
    {
        $this->getJson('/api/practice/technology-spaced-review/javascript-closures?language=en&search=arrow%20function%20this&limit=3')
            ->assertOk()
            ->assertJsonPath('data.technology', 'javascript-closures')
            ->assertJsonPath('data.cards.0.recall_prompt', 'Explain arrow-function `this` as lexical this, then name the surrounding scope that supplies `this`.')
            ->assertJsonPath('data.cards.1.coding_action', 'Write one new obj.arrow() trap note and one call/apply/bind limitation note.')
            ->assertJsonPath('data.cards.2.recall_prompt', 'Answer the interview question using lexical this, normal function call-site this, obj.arrow() trap, call/apply/bind limits, and callback use.');
    }

    /**
     * PHP stack and heap spaced review asks for call-frame, heap, cleanup, and caveat recall.
     */
    public function test_php_stack_heap_spaced_review_uses_runtime_memory_cards(): void
    {
        $this->getJson('/api/practice/technology-spaced-review/php?language=en&search=stack%20memory&limit=3')
            ->assertOk()
            ->assertJsonPath('data.technology', 'php')
            ->assertJsonPath('data.cards.0.recall_prompt', 'Explain stack memory as active PHP call frames, then name parameters, local variables, return values, and frame cleanup.')
            ->assertJsonPath('data.cards.1.coding_action', 'Write one tiny PHP example with a large array or object reference and add a memory-risk note for long-running workers.')
            ->assertJsonPath('data.cards.2.recall_prompt', 'Answer the interview question using call frames, heap-backed data, references, GC, cleanup, and PHP caveats.');
    }

    /**
     * AI type comparison spaced review asks for contract, metric, and failure recall.
     */
    public function test_predictive_generative_ai_spaced_review_uses_ai_type_cards(): void
    {
        $this->getJson('/api/practice/technology-spaced-review/llm-foundations?language=en&search=Predictive%20AI&limit=3')
            ->assertOk()
            ->assertJsonPath('data.technology', 'llm-foundations')
            ->assertJsonPath('data.cards.0.recall_prompt', 'Explain Predictive AI as scores, labels, forecasts, rankings, or risk decisions, then contrast it with Generative AI output.')
            ->assertJsonPath('data.cards.1.coding_action', 'Write one new Predictive AI example and one Generative AI example with separate metrics and failure controls.')
            ->assertJsonPath('data.cards.2.recall_prompt', 'Answer the interview question using product fit, output contract, input evidence, predictive metrics, generative checks, and failure-mode evidence.');
    }

    /**
     * AI agent memory spaced review asks for memory contracts, governance metadata, and failure recall.
     */
    public function test_ai_agent_memory_spaced_review_uses_memory_contract_cards(): void
    {
        $this->getJson('/api/practice/technology-spaced-review/llm-foundations?language=en&search=agent%20memory&limit=3')
            ->assertOk()
            ->assertJsonPath('data.technology', 'llm-foundations')
            ->assertJsonPath('data.cards.0.recall_prompt', 'Explain AI agent memory as four separate contracts: working, episodic, semantic, and procedural memory.')
            ->assertJsonPath('data.cards.1.coding_action', 'Write one new memory contract row with source, freshness, confidence, permission scope, retention rule, and correction path.')
            ->assertJsonPath('data.cards.2.recall_prompt', 'Answer the interview question using working memory limits, episodic privacy boundaries, semantic freshness, procedural review, stale fallback, and private-memory blocking.');
    }

    /**
     * Covering Index spaced review asks for EXPLAIN, INCLUDE, visibility, and bloat recall.
     */
    public function test_covering_index_spaced_review_uses_query_plan_cards(): void
    {
        $this->getJson('/api/practice/technology-spaced-review/database-eloquent?language=en&search=Covering%20Index&limit=3')
            ->assertOk()
            ->assertJsonPath('data.technology', 'database-eloquent')
            ->assertJsonPath('data.cards.0.recall_prompt', 'Explain why Index Scan can still be slow when Heap Fetches force random IO back to the table heap.')
            ->assertJsonPath('data.cards.1.coding_action', 'Write one new CREATE INDEX proposal with INCLUDE, then remove any included column that is not projected by the hot query.')
            ->assertJsonPath('data.cards.2.recall_prompt', 'Answer the interview question using EXPLAIN, Heap Fetches, Index Only Scan, INCLUDE, visibility map, VACUUM, bloat, write overhead, and rollback evidence.');
    }

    /**
     * Database locking spaced review asks for invariant, transaction, and contention recall.
     */
    public function test_database_locking_spaced_review_uses_concurrency_cards(): void
    {
        $this->getJson('/api/practice/technology-spaced-review/database-eloquent?language=en&search=lockForUpdate&limit=3')
            ->assertOk()
            ->assertJsonPath('data.technology', 'database-eloquent')
            ->assertJsonPath('data.cards.0.recall_prompt', 'Explain database locking from the protected invariant first, then name why concurrent requests can oversell inventory or double-spend balance.')
            ->assertJsonPath('data.cards.1.coding_action', 'Write one new locking example and remove any external call, queue dispatch, sleep, or slow computation from inside the transaction.')
            ->assertJsonPath('data.cards.2.recall_prompt', 'Answer the interview question using invariant, row lock, transaction boundary, indexed lookup, deadlock retry, timeout, contention, and monitoring evidence.');
    }
}
