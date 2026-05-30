<?php

declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;

final class TechnologyPracticeBoardTest extends TestCase
{
    /**
     * The technology board page renders source groups and workspace links.
     */
    public function test_technology_board_page_renders_source_groups(): void
    {
        $response = $this->get('/practice/technology-board?technology=api-validation&family=laravel&language=en&search=api&limit=5');

        $response
            ->assertOk()
            ->assertSee('Technology practice board for source-backed Laravel drills.')
            ->assertSee('laravel/api-integration.en.json')
            ->assertSee('Open API validation workbench')
            ->assertSee('Readiness Signal')
            ->assertSee('Focus Checks')
            ->assertSee('Open source pack')
            ->assertSee('Open workspace');
    }

    /**
     * The technology board API returns grouped source records and queue query.
     */
    public function test_technology_board_api_returns_grouped_source_records(): void
    {
        $response = $this->getJson('/api/practice/technology-board?technology=api-validation&family=laravel&language=en&search=api&limit=5');

        $response
            ->assertOk()
            ->assertJsonPath('data.technology', 'api-validation')
            ->assertJsonPath('data.practice.slug', 'api-form-request-slice')
            ->assertJsonPath('data.related_workbench.path', '/workbench/topic-intake')
            ->assertJsonPath('data.focus_checks.0', 'Open source records before implementation work.')
            ->assertJsonPath('data.sources.0.source.path', 'laravel/api-integration.en.json')
            ->assertJsonPath('data.sources.0.sample_records.0.workspace_query.technology', 'api-validation')
            ->assertJsonPath('data.queue_query.technology', 'api-validation')
            ->assertJsonStructure([
                'data' => [
                    'technology',
                    'practice',
                    'related_workbench',
                    'focus_checks',
                    'readiness_signal',
                    'sources' => [
                        '*' => [
                            'source',
                            'record_count',
                            'practice',
                            'sample_records',
                        ],
                    ],
                    'queue_query',
                    'meta',
                ],
            ]);
    }

    /**
     * Specialized technology boards expose the matching runnable workbench.
     */
    public function test_specialized_technology_board_links_to_matching_workbench(): void
    {
        $this->getJson('/api/practice/technology-board?technology=lsm-tree-storage&language=en&search=LSM%20Tree&limit=5')
            ->assertOk()
            ->assertJsonPath('data.technology', 'lsm-tree-storage')
            ->assertJsonPath('data.practice.slug', 'lsm-tree-plan-workbench')
            ->assertJsonPath('data.related_workbench.path', '/workbench/lsm-tree-plan');

        $this->getJson('/api/practice/technology-board?technology=sql-injection-defense&language=en&search=SQL%20Injection&limit=5')
            ->assertOk()
            ->assertJsonPath('data.technology', 'sql-injection-defense')
            ->assertJsonPath('data.practice.slug', 'sql-injection-defense-plan-workbench')
            ->assertJsonPath('data.related_workbench.path', '/workbench/sql-injection-defense-plan')
            ->assertJsonPath('data.sources.0.sample_records.0.workspace_query.technology', 'sql-injection-defense');

        $this->getJson('/api/practice/technology-board?technology=csrf-protection&language=en&search=CSRF&limit=5')
            ->assertOk()
            ->assertJsonPath('data.technology', 'csrf-protection')
            ->assertJsonPath('data.practice.slug', 'csrf-protection-plan-workbench')
            ->assertJsonPath('data.related_workbench.path', '/workbench/csrf-protection-plan')
            ->assertJsonPath('data.sources.0.sample_records.0.workspace_query.technology', 'csrf-protection');

        $this->getJson('/api/practice/technology-board?technology=xss-defense&language=en&search=XSS&limit=5')
            ->assertOk()
            ->assertJsonPath('data.technology', 'xss-defense')
            ->assertJsonPath('data.practice.slug', 'blade-escaping-xss-preview')
            ->assertJsonPath('data.related_workbench.path', '/workbench/security-escape-preview')
            ->assertJsonPath('data.sources.0.sample_records.0.workspace_query.technology', 'xss-defense');

        $this->getJson('/api/practice/technology-board?technology=idor-access-control&language=en&search=IDOR&limit=5')
            ->assertOk()
            ->assertJsonPath('data.technology', 'idor-access-control')
            ->assertJsonPath('data.practice.slug', 'idor-access-review-workbench')
            ->assertJsonPath('data.related_workbench.path', '/workbench/idor-access-review')
            ->assertJsonPath('data.sources.0.sample_records.0.workspace_query.technology', 'idor-access-control')
            ->assertJsonPath('data.focus_checks.0', 'Identify every route parameter, download, export, nested resource, and signed URL that points at an object.')
            ->assertJsonPath('data.readiness_signal', 'Ready when the learner can show object-level authorization on every object route, explain 403 versus 404, and prove cross-user or cross-tenant denial with tests.');

        $this->getJson('/api/practice/technology-board?technology=security-misconfiguration&language=en&search=Security%20Misconfiguration&limit=5')
            ->assertOk()
            ->assertJsonPath('data.technology', 'security-misconfiguration')
            ->assertJsonPath('data.practice.slug', 'authorization-policy-plan-workbench')
            ->assertJsonPath('data.related_workbench.path', '/practice/configuration-readiness')
            ->assertJsonPath('data.focus_checks.1', 'Check debug mode, exposed secrets, public storage, CORS, headers, cookie flags, HTTPS, and trusted proxies.')
            ->assertJsonPath('data.readiness_signal', 'Ready when the learner can prove unsafe production configuration fails closed and can cite owners, rollback notes, and environment-specific evidence.');

        $this->getJson('/api/practice/technology-board?technology=oauth-flow&language=en&search=PKCE&limit=5')
            ->assertOk()
            ->assertJsonPath('data.technology', 'oauth-flow')
            ->assertJsonPath('data.practice.slug', 'oauth-flow-plan-workbench')
            ->assertJsonPath('data.related_workbench.path', '/workbench/oauth-flow-plan')
            ->assertJsonPath('data.focus_checks.1', 'For public clients, require Authorization Code with PKCE and S256 code_challenge.')
            ->assertJsonPath('data.readiness_signal', 'Ready when the learner can explain why PKCE binds the authorization code to the original public client and can name the callback failure tests.');

        $this->getJson('/api/practice/technology-board?technology=javascript-closures&language=en&search=JavaScript%20closure&limit=5')
            ->assertOk()
            ->assertJsonPath('data.technology', 'javascript-closures')
            ->assertJsonPath('data.practice.slug', 'react-render-optimization-workbench')
            ->assertJsonPath('data.related_workbench.path', '/workbench/react-render-optimization-plan')
            ->assertJsonPath('data.focus_checks.0', 'Identify the lexical scope where the closure is created.')
            ->assertJsonPath('data.focus_checks.2', 'Review var versus let loops, stale closures, and practical callback use before interview practice.')
            ->assertJsonPath('data.readiness_signal', 'Ready when the learner can trace lexical scope, captured binding, createCounter() output, var versus let behavior, stale closure risk, and real callback use.');

        $this->getJson('/api/practice/technology-board?technology=javascript-closures&language=en&search=arrow%20function%20this&limit=5')
            ->assertOk()
            ->assertJsonPath('data.technology', 'javascript-closures')
            ->assertJsonPath('data.focus_checks.0', 'Identify where the arrow function is created and which surrounding scope supplies `this`.')
            ->assertJsonPath('data.focus_checks.2', 'Review obj.arrow(), call/apply/bind limitations, and when a normal method is safer before interview practice.')
            ->assertJsonPath('data.readiness_signal', 'Ready when the learner can prove arrow lexical `this`, normal function call-site `this`, obj.arrow() behavior, call/apply/bind limits, and one practical callback use.');

        $this->getJson('/api/practice/technology-board?technology=php&language=en&search=stack%20memory&limit=5')
            ->assertOk()
            ->assertJsonPath('data.technology', 'php')
            ->assertJsonPath('data.practice.slug', 'php-cli-input-normalizer')
            ->assertJsonPath('data.related_workbench.path', '/workbench/name-normalizer')
            ->assertJsonPath('data.focus_checks.1', 'For stack versus heap, identify call frames, local variables, object lifetime, references, and cleanup.')
            ->assertJsonPath('data.readiness_signal', 'Ready when the learner can explain the PHP topic with a small code example, the runtime model, and one practical failure mode.');

        $this->getJson('/api/practice/technology-board?technology=llm-foundations&language=en&search=Predictive%20AI&limit=5')
            ->assertOk()
            ->assertJsonPath('data.technology', 'llm-foundations')
            ->assertJsonPath('data.practice.slug', 'llm-decision-loop-plan-workbench')
            ->assertJsonPath('data.related_workbench.path', '/workbench/llm-decision-loop-plan')
            ->assertJsonPath('data.focus_checks.0', 'Classify each source record by output contract before naming model capability.')
            ->assertJsonPath('data.focus_checks.2', 'For Generative AI, require prompt, context, generated output, groundedness, safety, and review evidence.')
            ->assertJsonPath('data.readiness_signal', 'Ready when the learner can compare Predictive AI and Generative AI by output contract, input evidence, evaluation metrics, and failure modes.');

        $this->getJson('/api/practice/technology-board?technology=llm-foundations&language=en&search=agent%20memory&limit=5')
            ->assertOk()
            ->assertJsonPath('data.technology', 'llm-foundations')
            ->assertJsonPath('data.related_workbench.path', '/workbench/ai-agent-memory-plan')
            ->assertJsonPath('data.focus_checks.0', 'Classify memory as working, episodic, semantic, or procedural before adding storage or retrieval.')
            ->assertJsonPath('data.focus_checks.2', 'Promote the board only after stale semantic memory and private episodic memory have explicit guardrails.')
            ->assertJsonPath('data.readiness_signal', 'Ready when the learner can separate working, episodic, semantic, and procedural memory and prove source, freshness, confidence, permission, retention, and correction guardrails.');

        $this->getJson('/api/practice/technology-board?technology=rag-systems&language=en&search=Long%20Context%20CAG&limit=5')
            ->assertOk()
            ->assertJsonPath('data.technology', 'rag-systems')
            ->assertJsonPath('data.practice.slug', 'rag-strategy-plan-workbench')
            ->assertJsonPath('data.related_workbench.path', '/workbench/rag-strategy-plan')
            ->assertJsonPath('data.focus_checks.0', 'Choose RAG, Long Context, CAG, or hybrid routing before selecting a retrieval pattern.')
            ->assertJsonPath('data.focus_checks.2', 'Test tenant-scoped retrieval, packed-context limits, CAG invalidation, stale sources, missing citations, and hybrid route selection.')
            ->assertJsonPath('data.readiness_signal', 'Ready when the learner can choose RAG, Long Context, CAG, or hybrid routing and prove citations, permissions, token budgets, cache freshness, source versions, and fallback behavior.');

        $this->getJson('/api/practice/technology-board?technology=database-eloquent&language=en&search=Covering%20Index&limit=5')
            ->assertOk()
            ->assertJsonPath('data.technology', 'database-eloquent')
            ->assertJsonPath('data.practice.slug', 'laravel-thin-controller')
            ->assertJsonPath('data.related_workbench.path', '/workbench/collection-filter-preview')
            ->assertJsonPath('data.focus_checks.0', 'Start from EXPLAIN (ANALYZE, BUFFERS) before proposing a new index.')
            ->assertJsonPath('data.focus_checks.2', 'Check visibility map, VACUUM or autovacuum health, bloat risk, write overhead, and rollback before promotion.')
            ->assertJsonPath('data.readiness_signal', 'Ready when the learner can prove Heap Fetches dropped, justify INCLUDE columns, explain visibility-map or VACUUM caveats, and name index bloat or write-overhead tradeoffs.');

        $this->getJson('/api/practice/technology-board?technology=database-eloquent&language=en&search=lockForUpdate&limit=5')
            ->assertOk()
            ->assertJsonPath('data.technology', 'database-eloquent')
            ->assertJsonPath('data.practice.slug', 'laravel-thin-controller')
            ->assertJsonPath('data.related_workbench.path', '/workbench/database-locking-plan')
            ->assertJsonPath('data.focus_checks.0', 'Name the concurrent-write invariant before choosing row locks.')
            ->assertJsonPath('data.focus_checks.2', 'Check indexed lookup, short transaction scope, deadlock retry, lock timeout, and hot-row contention before promotion.')
            ->assertJsonPath('data.readiness_signal', 'Ready when the learner can prove the invariant under concurrent requests, place `lockForUpdate()` inside `DB::transaction()`, keep the lock query indexed and narrow, and explain deadlock, timeout, and contention behavior.');

        $this->getJson('/api/practice/technology-board?technology=graph-traversal&language=en&search=BFS%20DFS&limit=5')
            ->assertOk()
            ->assertJsonPath('data.technology', 'graph-traversal')
            ->assertJsonPath('data.practice.slug', 'graph-traversal-plan-workbench')
            ->assertJsonPath('data.related_workbench.path', '/workbench/graph-traversal-plan')
            ->assertJsonPath('data.focus_checks.0', 'Classify the problem as nearest result, shortest unweighted path, branch exploration, dependency reasoning, or subtree validation.')
            ->assertJsonPath('data.focus_checks.1', 'Use BFS with a queue for level-order and nearest-hop work; use DFS with a stack or recursion for depth-first branch work.')
            ->assertJsonPath('data.readiness_signal', 'Ready when the learner can choose BFS or DFS from the goal, prove traversal order, and name cycle, depth, fan-out, pagination, rate-limit, and memory guardrails.');
    }

    /**
     * The technology board page renders OAuth PKCE readiness guidance.
     */
    public function test_technology_board_page_renders_pkce_readiness_guidance(): void
    {
        $this->get('/practice/technology-board?technology=oauth-flow&language=en&search=PKCE&limit=5')
            ->assertOk()
            ->assertSee('Open OAuth flow workbench')
            ->assertSee('For public clients, require Authorization Code with PKCE and S256 code_challenge.')
            ->assertSee('Ready when the learner can explain why PKCE binds the authorization code to the original public client');
    }
}
