<?php

declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;

final class TechnologyQualityPlanTest extends TestCase
{
    /**
     * The technology quality plan page renders verification guidance.
     */
    public function test_technology_quality_plan_page_renders_verification_guidance(): void
    {
        $response = $this->get('/practice/technology-quality-plan?family=laravel&language=en&search=api');

        $response
            ->assertOk()
            ->assertSee('Quality gates for technology pipelines.')
            ->assertSee('api-validation')
            ->assertSee('php artisan test --filter TechnologyApiValidationTest')
            ->assertSee('vendor\\bin\\pint --test')
            ->assertSee('Evidence checks')
            ->assertSee('Open quality API')
            ->assertSee('Open taxonomy')
            ->assertSee('Open API validation workbench')
            ->assertSee('Open pipeline');
    }

    /**
     * The technology quality plan API returns quality gates and commands.
     */
    public function test_technology_quality_plan_api_returns_quality_gate_payloads(): void
    {
        $response = $this->getJson('/api/practice/technology-quality-plan?family=laravel&language=en&search=api');

        $response
            ->assertOk()
            ->assertJsonPath('data.items.0.technology', 'api-validation')
            ->assertJsonPath('data.items.0.practice.slug', 'api-form-request-slice')
            ->assertJsonPath('data.items.0.related_workbench.path', '/workbench/topic-intake')
            ->assertJsonPath('data.items.0.quality_gate.status', 'ready')
            ->assertJsonPath('data.items.0.quality_gate.passed', true)
            ->assertJsonPath('data.items.0.commands.2', 'vendor\\bin\\pint --test')
            ->assertJsonPath('data.meta.minimum_status', 'ready')
            ->assertJsonStructure([
                'data' => [
                    'items' => [
                        '*' => [
                            'technology',
                            'record_count',
                            'source_count',
                            'practice',
                            'related_workbench',
                            'pipeline_route',
                            'api_pipeline_route',
                            'quality_gate',
                            'baseline',
                            'commands',
                            'acceptance_checks',
                            'evidence_checks',
                            'risk_note',
                        ],
                    ],
                    'meta' => [
                        'filters',
                        'query',
                        'quality_plan_count',
                        'minimum_status',
                    ],
                ],
            ]);
    }

    /**
     * Specialized technology quality plans include focused acceptance checks.
     */
    public function test_specialized_technology_quality_plan_includes_domain_checks(): void
    {
        $this->getJson('/api/practice/technology-quality-plan?language=en&search=LSM%20Tree')
            ->assertOk()
            ->assertJsonPath('data.items.0.technology', 'lsm-tree-storage')
            ->assertJsonPath('data.items.0.related_workbench.path', '/workbench/lsm-tree-plan')
            ->assertJsonPath('data.items.0.acceptance_checks.0', 'Write path covers WAL, memtable, flush, SSTable, and compaction.');

        $this->getJson('/api/practice/technology-quality-plan?language=en&search=localStorage')
            ->assertOk()
            ->assertJsonPath('data.items.0.technology', 'jwt-token-storage')
            ->assertJsonPath('data.items.0.related_workbench.path', '/workbench/jwt-token-storage-plan')
            ->assertJsonPath('data.items.0.acceptance_checks.0', 'Storage choice names XSS, CSRF, token lifetime, and client type tradeoffs.');

        $this->getJson('/api/practice/technology-quality-plan?language=en&search=large%20language%20models')
            ->assertOk()
            ->assertJsonPath('data.items.0.technology', 'llm-foundations')
            ->assertJsonPath('data.items.0.related_workbench.path', '/workbench/llm-decision-loop-plan')
            ->assertJsonPath('data.items.0.acceptance_checks.0', 'The explanation separates next-token probability from the full decision loop.');

        $this->getJson('/api/practice/technology-quality-plan?language=en&search=Predictive%20AI')
            ->assertOk()
            ->assertJsonPath('data.items.0.technology', 'llm-foundations')
            ->assertJsonPath('data.items.0.related_workbench.path', '/workbench/llm-decision-loop-plan')
            ->assertJsonPath('data.items.0.acceptance_checks.0', 'Predictive AI outputs are described as scores, labels, forecasts, rankings, or risk decisions, not generated content.')
            ->assertJsonPath('data.items.0.acceptance_checks.2', 'The answer uses separate evaluation language for prediction metrics and generation quality checks.')
            ->assertJsonPath('data.items.0.evidence_checks.0', 'A comparison table or test fixture separates output contract, input data, evaluation metric, and failure mode for both AI types.')
            ->assertJsonPath('data.items.0.risk_note', 'Predictive AI versus Generative AI explanations become weak when they reuse one evaluation checklist and ignore that prediction errors, drift, hallucination, and unsafe generated code need different controls.');

        $this->getJson('/api/practice/technology-quality-plan?language=en&search=agent%20memory')
            ->assertOk()
            ->assertJsonPath('data.items.0.technology', 'llm-foundations')
            ->assertJsonPath('data.items.0.related_workbench.path', '/workbench/ai-agent-memory-plan')
            ->assertJsonPath('data.items.0.acceptance_checks.0', 'The plan separates working, episodic, semantic, and procedural memory before storage or retrieval decisions.')
            ->assertJsonPath('data.items.0.acceptance_checks.2', 'Stale semantic memory, private episodic memory, procedural mismatch, and working-memory leakage have explicit rejection checks.')
            ->assertJsonPath('data.items.0.evidence_checks.0', 'A memory contract table names working, episodic, semantic, and procedural memory with allowed data and retrieval rules.')
            ->assertJsonPath('data.items.0.risk_note', 'AI agent memory work is risky when one prompt-history bucket silently mixes temporary task state, private session notes, stale repo facts, and unreviewed playbooks.');

        $this->getJson('/api/practice/technology-quality-plan?language=en&search=Graph%20RAG')
            ->assertOk()
            ->assertJsonPath('data.items.0.technology', 'rag-systems')
            ->assertJsonPath('data.items.0.related_workbench.path', '/workbench/rag-strategy-plan')
            ->assertJsonPath('data.items.0.acceptance_checks.0', 'The plan names whether RAG, Long Context, CAG, or hybrid routing fits the chatbot context strategy.')
            ->assertJsonPath('data.items.0.acceptance_checks.2', 'Retrieval output keeps source IDs, citations, freshness, and groundedness checks separate from generated text.')
            ->assertJsonPath('data.items.0.evidence_checks.0', 'A context strategy plan records whether the chatbot used retrieval, context packing, cached context, or hybrid routing.')
            ->assertJsonPath('data.items.0.risk_note', 'RAG, Long Context, and CAG systems fail quietly when teams improve prompts while retrieval quality, source markers, cache freshness, citations, permissions, and fallback behavior remain untested.');

        $this->getJson('/api/practice/technology-quality-plan?language=en&search=Long%20Context')
            ->assertOk()
            ->assertJsonPath('data.items.0.technology', 'rag-systems')
            ->assertJsonPath('data.items.0.acceptance_checks.3', 'Long Context uses token budgets and source markers; CAG uses permission-aware cache keys and stale-cache invalidation.')
            ->assertJsonPath('data.items.0.evidence_checks.2', 'Long Context examples include token-budget and lost-in-the-middle checks; CAG examples include cache version and permission-key checks.');

        $this->getJson('/api/practice/technology-quality-plan?language=en&search=candidate%20really%20uses%20AI')
            ->assertOk()
            ->assertJsonPath('data.items.0.technology', 'ai-cloud-interview')
            ->assertJsonPath('data.items.0.related_workbench.path', '/workbench/ai-cloud-interview-rubric')
            ->assertJsonPath('data.items.0.acceptance_checks.0', 'The answer includes one recent AI-assisted Cloud task with prompt shape, output edits, and result.');

        $this->getJson('/api/practice/technology-quality-plan?language=en&search=GraphQL%20vs%20REST')
            ->assertOk()
            ->assertJsonPath('data.items.0.technology', 'graphql-api')
            ->assertJsonPath('data.items.0.related_workbench.path', '/workbench/graphql-rest-decision')
            ->assertJsonPath('data.items.0.acceptance_checks.0', 'The decision names why REST endpoints or GraphQL schema/query shape fits the client workflow.');

        $this->getJson('/api/practice/technology-quality-plan?language=en&search=React%20re-renders')
            ->assertOk()
            ->assertJsonPath('data.items.0.technology', 'react-render-performance')
            ->assertJsonPath('data.items.0.related_workbench.path', '/workbench/react-render-optimization-plan')
            ->assertJsonPath('data.items.0.acceptance_checks.0', 'React Profiler evidence exists before memoization is added.');

        $this->getJson('/api/practice/technology-quality-plan?language=en&search=JavaScript%20closure')
            ->assertOk()
            ->assertJsonPath('data.items.0.technology', 'javascript-closures')
            ->assertJsonPath('data.items.0.related_workbench.path', '/workbench/react-render-optimization-plan')
            ->assertJsonPath('data.items.0.acceptance_checks.0', 'The answer defines closure as a function plus access to variables from its lexical scope.')
            ->assertJsonPath('data.items.0.evidence_checks.0', 'A small JavaScript snippet shows an outer variable staying reachable after the outer function returns.')
            ->assertJsonPath('data.items.0.risk_note', 'Closure answers are weak when they say "inner function remembers variables" but cannot trace lexical scope, captured bindings, stale closures, or var versus let behavior.');

        $this->getJson('/api/practice/technology-quality-plan?language=en&search=arrow%20function%20this')
            ->assertOk()
            ->assertJsonPath('data.items.0.technology', 'javascript-closures')
            ->assertJsonPath('data.items.0.acceptance_checks.0', 'The answer states that arrow functions do not create their own `this`.')
            ->assertJsonPath('data.items.0.acceptance_checks.2', '`obj.arrow()`, `call`, `apply`, and `bind` traps are explained without claiming the arrow can be rebound.')
            ->assertJsonPath('data.items.0.evidence_checks.0', 'A JavaScript snippet compares `obj.normal()` with `obj.arrow()` and records the different output.')
            ->assertJsonPath('data.items.0.risk_note', 'Arrow-function `this` answers are weak when they say arrow syntax is just shorter, but cannot explain lexical `this`, normal function call-site `this`, obj.arrow() traps, or call/apply/bind limitations.');

        $this->getJson('/api/practice/technology-quality-plan?language=en&search=Client%20Credentials')
            ->assertOk()
            ->assertJsonPath('data.items.0.technology', 'oauth-flow')
            ->assertJsonPath('data.items.0.related_workbench.path', '/workbench/oauth-flow-plan')
            ->assertJsonPath('data.items.0.acceptance_checks.1', 'Client Credentials is used only for confidential machine-to-machine clients, never browser or mobile public clients.');

        $this->getJson('/api/practice/technology-quality-plan?language=en&search=PKCE')
            ->assertOk()
            ->assertJsonPath('data.items.0.technology', 'oauth-flow')
            ->assertJsonPath('data.items.0.related_workbench.path', '/workbench/oauth-flow-plan')
            ->assertJsonPath('data.items.0.acceptance_checks.2', 'The code verifier is generated per login attempt, stored only for the pending authorization request, and cleared after token exchange.')
            ->assertJsonPath('data.items.0.evidence_checks.0', 'A captured authorize URL contains code_challenge and code_challenge_method=S256, but never code_verifier.')
            ->assertJsonPath('data.items.0.evidence_checks.2', 'Callback handling rejects access_token, id_token, token_type, or expires_in in URL input for browser clients.');

        $this->getJson('/api/practice/technology-quality-plan?language=en&search=SQL%20Injection')
            ->assertOk()
            ->assertJsonPath('data.items.0.technology', 'sql-injection-defense')
            ->assertJsonPath('data.items.0.related_workbench.path', '/workbench/sql-injection-defense-plan')
            ->assertJsonPath('data.items.0.acceptance_checks.0', 'User-controlled values are passed through bindings or query builder parameters.');

        $this->getJson('/api/practice/technology-quality-plan?language=en&search=CSRF')
            ->assertOk()
            ->assertJsonPath('data.items.0.technology', 'csrf-protection')
            ->assertJsonPath('data.items.0.related_workbench.path', '/workbench/csrf-protection-plan')
            ->assertJsonPath('data.items.0.acceptance_checks.0', 'State-changing browser routes require CSRF token validation or an explicit non-cookie auth reason.');

        $this->getJson('/api/practice/technology-quality-plan?language=en&search=XSS')
            ->assertOk()
            ->assertJsonPath('data.items.0.technology', 'xss-defense')
            ->assertJsonPath('data.items.0.related_workbench.path', '/workbench/security-escape-preview')
            ->assertJsonPath('data.items.0.acceptance_checks.0', 'Untrusted output is rendered with context-aware escaping for HTML, attributes, JavaScript, and URLs.');

        $this->getJson('/api/practice/technology-quality-plan?language=en&search=IDOR')
            ->assertOk()
            ->assertJsonPath('data.items.0.technology', 'idor-access-control')
            ->assertJsonPath('data.items.0.related_workbench.path', '/workbench/idor-access-review')
            ->assertJsonPath('data.items.0.acceptance_checks.0', 'Every object route maps to owner, tenant, team, role, or public access rules before implementation.')
            ->assertJsonPath('data.items.0.evidence_checks.1', 'A failing-first or replay note shows the exact ID swap request the fix is meant to block.')
            ->assertJsonPath('data.items.0.risk_note', 'IDOR prevention is incomplete when teams check only authentication, roles, hidden UI, or random IDs but do not prove object-level authorization with scoped queries, policies, and two-user or two-tenant denial tests.');

        $this->getJson('/api/practice/technology-quality-plan?language=en&search=Security%20Misconfiguration')
            ->assertOk()
            ->assertJsonPath('data.items.0.technology', 'security-misconfiguration')
            ->assertJsonPath('data.items.0.related_workbench.path', '/practice/configuration-readiness')
            ->assertJsonPath('data.items.0.acceptance_checks.0', 'Production runtime flags such as APP_DEBUG, APP_ENV, logging, cache, and config cache are checked before deploy.')
            ->assertJsonPath('data.items.0.evidence_checks.1', 'A smoke test or config audit rejects APP_DEBUG=true, exposed .env files, permissive CORS, missing security headers, or trusted-proxy drift in production.')
            ->assertJsonPath('data.items.0.risk_note', 'Security Misconfiguration work is incomplete when teams fix code vulnerabilities but deploy with debug mode, exposed secrets, weak headers, broad CORS, public storage, or incorrect proxy trust.');

        $this->getJson('/api/practice/technology-quality-plan?language=en&search=Broken%20Authentication')
            ->assertOk()
            ->assertJsonPath('data.items.0.technology', 'auth-security')
            ->assertJsonPath('data.items.0.related_workbench.path', '/workbench/authorization-policy-plan')
            ->assertJsonPath('data.items.0.acceptance_checks.0', 'The review maps login, session creation, remember-me, password reset, MFA recovery, logout, token refresh, and revocation as one authentication lifecycle.')
            ->assertJsonPath('data.items.0.acceptance_checks.2', 'Failure-path tests prove brute force, stale reset token, reused reset token, old session reuse, logged-out session reuse, and revoked token reuse fail closed.')
            ->assertJsonPath('data.items.0.evidence_checks.0', 'A lifecycle checklist names each identity boundary and the Laravel file responsible for the control.')
            ->assertJsonPath('data.items.0.risk_note', 'Broken Authentication work is incomplete when teams test only successful login and ignore session fixation, brute force, stale reset tokens, logout invalidation, token revocation, and sensitive auth logging.');

        $this->getJson('/api/practice/technology-quality-plan?language=en&search=reverse%20proxy')
            ->assertOk()
            ->assertJsonPath('data.items.0.technology', 'reverse-proxy-edge')
            ->assertJsonPath('data.items.0.related_workbench.path', '/workbench/reverse-proxy-failure-plan')
            ->assertJsonPath('data.items.0.acceptance_checks.0', 'The request path names proxy, edge module, load balancer, and origin responsibilities.');

        $this->getJson('/api/practice/technology-quality-plan?language=en&search=SIEM')
            ->assertOk()
            ->assertJsonPath('data.items.0.technology', 'siem-elk-observability')
            ->assertJsonPath('data.items.0.related_workbench.path', '/workbench/siem-elk-plan')
            ->assertJsonPath('data.items.0.acceptance_checks.0', 'The plan distinguishes SIEM operating capability from ELK component names.');

        $this->getJson('/api/practice/technology-quality-plan?family=interview&language=en&search=technically%20correct%20System%20Design')
            ->assertOk()
            ->assertJsonPath('data.items.0.technology', 'system-design-tradeoffs')
            ->assertJsonPath('data.items.0.related_workbench.path', '/workbench/system-design-tradeoff-plan')
            ->assertJsonPath('data.items.0.acceptance_checks.0', 'The answer asks clarifying questions before choosing Kafka, WebSocket, Redis, or any other technology.');

        $this->getJson('/api/practice/technology-quality-plan?language=en&search=stack%20memory')
            ->assertOk()
            ->assertJsonPath('data.items.0.technology', 'php')
            ->assertJsonPath('data.items.0.related_workbench.path', '/workbench/name-normalizer')
            ->assertJsonPath('data.items.0.acceptance_checks.1', 'Runtime-memory topics distinguish call frames, heap-backed data, references, and cleanup.')
            ->assertJsonPath('data.items.0.evidence_checks.0', 'A small code sample marks local call-frame state versus heap-pressure data.')
            ->assertJsonPath('data.items.0.evidence_checks.2', 'A failure-mode note covers recursion depth, large arrays, retained references, or worker state.')
            ->assertJsonPath('data.items.0.risk_note', 'PHP runtime answers are weak when they describe stack and heap as manual allocation instead of a mental model for calls, references, GC, and memory pressure.');

        $this->getJson('/api/practice/technology-quality-plan?language=en&search=Covering%20Index')
            ->assertOk()
            ->assertJsonPath('data.items.0.technology', 'database-eloquent')
            ->assertJsonPath('data.items.0.related_workbench.path', '/workbench/collection-filter-preview')
            ->assertJsonPath('data.items.0.acceptance_checks.0', 'Before and after EXPLAIN (ANALYZE, BUFFERS) output proves Heap Fetches dropped for the hot query.')
            ->assertJsonPath('data.items.0.evidence_checks.0', 'A query-plan fixture or captured EXPLAIN output records Index Scan versus Index Only Scan and Heap Fetches.')
            ->assertJsonPath('data.items.0.risk_note', 'Covering Index work is risky when an index is added without proving heap fetch reduction, checking visibility-map health, or accounting for index bloat and write overhead.');

        $this->getJson('/api/practice/technology-quality-plan?language=en&search=lockForUpdate')
            ->assertOk()
            ->assertJsonPath('data.items.0.technology', 'database-eloquent')
            ->assertJsonPath('data.items.0.acceptance_checks.0', 'The invariant names the race condition being protected, such as stock cannot go below zero or balance cannot be spent twice.')
            ->assertJsonPath('data.items.0.acceptance_checks.2', '`lockForUpdate()` is applied inside the transaction on a selective, indexed row lookup before the protected value is checked or changed.')
            ->assertJsonPath('data.items.0.evidence_checks.0', 'A concurrency fixture describes two simultaneous requests and the exact row, account, order, or workflow state they compete for.')
            ->assertJsonPath('data.items.0.risk_note', 'Database locking work is risky when teams add `lockForUpdate()` without a transaction boundary, indexed lookup, short critical section, deadlock plan, or proof that concurrent requests cannot double-spend or oversell.');

        $this->getJson('/api/practice/technology-quality-plan?language=en&search=BFS%20DFS')
            ->assertOk()
            ->assertJsonPath('data.items.0.technology', 'graph-traversal')
            ->assertJsonPath('data.items.0.related_workbench.path', '/workbench/graph-traversal-plan')
            ->assertJsonPath('data.items.0.acceptance_checks.0', 'The answer chooses BFS or DFS from the traversal goal instead of claiming one is always faster.')
            ->assertJsonPath('data.items.0.acceptance_checks.3', 'API and database examples include visited set, cycle detection, max depth, fan-out limits, batching, pagination, rate limits, and memory tradeoffs.')
            ->assertJsonPath('data.items.0.evidence_checks.0', 'A traversal fixture proves BFS order with a queue and DFS order with a stack or recursion.')
            ->assertJsonPath('data.items.0.risk_note', 'BFS and DFS answers are weak when they describe traversal order but ignore the actual goal, cycles, depth limits, fan-out, pagination, rate limits, and memory pressure.');
    }
}
