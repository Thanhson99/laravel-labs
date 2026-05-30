<?php

declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;

final class TechnologyPracticeMatrixTest extends TestCase
{
    /**
     * The technology matrix page groups content records by practice technology.
     */
    public function test_technology_matrix_page_renders_content_coverage(): void
    {
        $response = $this->get('/practice/technology-matrix?family=laravel&language=en&search=api');

        $response
            ->assertOk()
            ->assertSee('Technology map from source files to coding exercises.')
            ->assertSee('api-validation')
            ->assertSee('Open sample drill')
            ->assertSee('Open API validation workbench')
            ->assertSee('Open taxonomy')
            ->assertSee('Drills for technology')
            ->assertSee('Open pipeline')
            ->assertSee('Open code examples');
    }

    /**
     * The technology matrix API returns counts, source samples, and practice links.
     */
    public function test_technology_matrix_api_returns_grouped_practice_data(): void
    {
        $response = $this->getJson('/api/practice/technology-matrix?family=laravel&language=en&search=api');

        $response
            ->assertOk()
            ->assertJsonPath('data.items.0.technology', 'api-validation')
            ->assertJsonPath('data.items.0.practice.slug', 'api-form-request-slice')
            ->assertJsonPath('data.items.0.related_workbench.path', '/workbench/topic-intake')
            ->assertJsonPath('data.items.0.focus_checks.0', 'Open the workbench and implementation lab before preparing portfolio evidence.')
            ->assertJsonPath('data.items.0.drill_query.technology', 'api-validation')
            ->assertJsonStructure([
                'data' => [
                    'items' => [
                        '*' => [
                            'technology',
                            'record_count',
                            'source_count',
                            'families',
                            'sources',
                            'sample',
                            'practice',
                            'related_workbench',
                            'focus_checks',
                            'drill_query',
                        ],
                    ],
                    'meta' => [
                        'filters',
                        'record_count',
                        'technology_count',
                    ],
                ],
            ]);
    }

    /**
     * The technology matrix reuses specialized technology-to-workbench mappings.
     */
    public function test_technology_matrix_uses_specialized_practice_mapping(): void
    {
        $this->getJson('/api/practice/technology-matrix?language=en&search=LSM%20Tree')
            ->assertOk()
            ->assertJsonPath('data.items.0.technology', 'lsm-tree-storage')
            ->assertJsonPath('data.items.0.practice.slug', 'lsm-tree-plan-workbench')
            ->assertJsonPath('data.items.0.related_workbench.path', '/workbench/lsm-tree-plan');

        $this->getJson('/api/practice/technology-matrix?language=en&search=localStorage')
            ->assertOk()
            ->assertJsonPath('data.items.0.technology', 'jwt-token-storage')
            ->assertJsonPath('data.items.0.practice.slug', 'jwt-token-storage-plan-workbench')
            ->assertJsonPath('data.items.0.related_workbench.path', '/workbench/jwt-token-storage-plan');

        $this->getJson('/api/practice/technology-matrix?language=en&search=SQL%20Injection')
            ->assertOk()
            ->assertJsonPath('data.items.0.technology', 'sql-injection-defense')
            ->assertJsonPath('data.items.0.practice.slug', 'sql-injection-defense-plan-workbench')
            ->assertJsonPath('data.items.0.related_workbench.path', '/workbench/sql-injection-defense-plan');

        $this->getJson('/api/practice/technology-matrix?language=en&search=CSRF')
            ->assertOk()
            ->assertJsonPath('data.items.0.technology', 'csrf-protection')
            ->assertJsonPath('data.items.0.practice.slug', 'csrf-protection-plan-workbench')
            ->assertJsonPath('data.items.0.related_workbench.path', '/workbench/csrf-protection-plan');

        $this->getJson('/api/practice/technology-matrix?language=en&search=XSS')
            ->assertOk()
            ->assertJsonPath('data.items.0.technology', 'xss-defense')
            ->assertJsonPath('data.items.0.practice.slug', 'blade-escaping-xss-preview')
            ->assertJsonPath('data.items.0.related_workbench.path', '/workbench/security-escape-preview');

        $this->getJson('/api/practice/technology-matrix?language=en&search=IDOR')
            ->assertOk()
            ->assertJsonPath('data.items.0.technology', 'idor-access-control')
            ->assertJsonPath('data.items.0.practice.slug', 'idor-access-review-workbench')
            ->assertJsonPath('data.items.0.related_workbench.path', '/workbench/idor-access-review')
            ->assertJsonPath('data.items.0.focus_checks.0', 'Map every object ID route to owner, tenant, team, role, or public access rules.');

        $this->getJson('/api/practice/technology-matrix?language=en&search=Security%20Misconfiguration')
            ->assertOk()
            ->assertJsonPath('data.items.0.technology', 'security-misconfiguration')
            ->assertJsonPath('data.items.0.practice.slug', 'authorization-policy-plan-workbench')
            ->assertJsonPath('data.items.0.related_workbench.path', '/practice/configuration-readiness')
            ->assertJsonPath('data.items.0.focus_checks.0', 'Separate unsafe defaults from environment-specific drift.')
            ->assertJsonPath('data.items.0.focus_checks.2', 'Require release smoke checks that fail closed when production config is unsafe.');

        $this->getJson('/api/practice/technology-matrix?language=en&search=PKCE')
            ->assertOk()
            ->assertJsonPath('data.items.0.technology', 'oauth-flow')
            ->assertJsonPath('data.items.0.practice.slug', 'oauth-flow-plan-workbench')
            ->assertJsonPath('data.items.0.related_workbench.path', '/workbench/oauth-flow-plan')
            ->assertJsonPath('data.items.0.focus_checks.1', 'For public clients, require Authorization Code with PKCE and S256.');

        $this->getJson('/api/practice/technology-matrix?language=en&search=JavaScript%20closure')
            ->assertOk()
            ->assertJsonPath('data.items.0.technology', 'javascript-closures')
            ->assertJsonPath('data.items.0.practice.slug', 'react-render-optimization-workbench')
            ->assertJsonPath('data.items.0.related_workbench.path', '/workbench/react-render-optimization-plan')
            ->assertJsonPath('data.items.0.focus_checks.0', 'Trace lexical scope, inner function, and captured binding before describing use cases.')
            ->assertJsonPath('data.items.0.focus_checks.1', 'Use createCounter(), private state, var versus let, and stale closures as interview anchors.');

        $this->getJson('/api/practice/technology-matrix?language=en&search=arrow%20function%20this')
            ->assertOk()
            ->assertJsonPath('data.items.0.technology', 'javascript-closures')
            ->assertJsonPath('data.items.0.practice.slug', 'react-render-optimization-workbench')
            ->assertJsonPath('data.items.0.focus_checks.0', 'Trace the surrounding scope that supplies arrow-function `this` before describing syntax.')
            ->assertJsonPath('data.items.0.focus_checks.2', 'Use obj.arrow(), call/apply/bind limitations, and callback use cases as interview anchors.');

        $this->getJson('/api/practice/technology-matrix?language=en&search=stack%20memory')
            ->assertOk()
            ->assertJsonPath('data.items.0.technology', 'php')
            ->assertJsonPath('data.items.0.practice.slug', 'php-cli-input-normalizer')
            ->assertJsonPath('data.items.0.related_workbench.path', '/workbench/name-normalizer')
            ->assertJsonPath('data.items.0.focus_checks.1', 'For stack versus heap, connect call frames, object lifetime, references, and memory pressure.');

        $this->getJson('/api/practice/technology-matrix?language=en&search=Predictive%20AI')
            ->assertOk()
            ->assertJsonPath('data.items.0.technology', 'llm-foundations')
            ->assertJsonPath('data.items.0.practice.slug', 'llm-decision-loop-plan-workbench')
            ->assertJsonPath('data.items.0.related_workbench.path', '/workbench/llm-decision-loop-plan')
            ->assertJsonPath('data.items.0.focus_checks.0', 'Classify the topic by output contract before discussing model capability.')
            ->assertJsonPath('data.items.0.focus_checks.2', 'For Generative AI, connect prompts, context, generated output, groundedness, safety, and human review.');

        $this->getJson('/api/practice/technology-matrix?language=en&search=agent%20memory')
            ->assertOk()
            ->assertJsonPath('data.items.0.technology', 'llm-foundations')
            ->assertJsonPath('data.items.0.related_workbench.path', '/workbench/ai-agent-memory-plan')
            ->assertJsonPath('data.items.0.focus_checks.0', 'Split agent memory records into working, episodic, semantic, and procedural contracts.')
            ->assertJsonPath('data.items.0.focus_checks.2', 'Use stale-memory, private-memory, procedural-mismatch, and working-memory leak checks as promotion gates.');

        $this->getJson('/api/practice/technology-matrix?language=en&search=Graph%20RAG')
            ->assertOk()
            ->assertJsonPath('data.items.0.technology', 'rag-systems')
            ->assertJsonPath('data.items.0.practice.slug', 'rag-strategy-plan-workbench')
            ->assertJsonPath('data.items.0.related_workbench.path', '/workbench/rag-strategy-plan')
            ->assertJsonPath('data.items.0.focus_checks.0', 'Classify the chatbot context path as RAG, Long Context, CAG, or hybrid routing before implementation.')
            ->assertJsonPath('data.items.0.focus_checks.2', 'Use permission-scoped retrieval, cache invalidation, packed-context limits, source freshness, and fallback tests as promotion gates.');

        $this->getJson('/api/practice/technology-matrix?language=en&search=Covering%20Index')
            ->assertOk()
            ->assertJsonPath('data.items.0.technology', 'database-eloquent')
            ->assertJsonPath('data.items.0.practice.slug', 'laravel-thin-controller')
            ->assertJsonPath('data.items.0.related_workbench.path', '/workbench/collection-filter-preview')
            ->assertJsonPath('data.items.0.focus_checks.0', 'Read the query plan before changing the index: selected columns, filters, ordering, Heap Fetches, and buffer reads.')
            ->assertJsonPath('data.items.0.focus_checks.2', 'Check visibility map, VACUUM or autovacuum health, index size, bloat risk, write overhead, and rollback before promotion.');

        $this->getJson('/api/practice/technology-matrix?language=en&search=lockForUpdate')
            ->assertOk()
            ->assertJsonPath('data.items.0.technology', 'database-eloquent')
            ->assertJsonPath('data.items.0.practice.slug', 'laravel-thin-controller')
            ->assertJsonPath('data.items.0.related_workbench.path', '/workbench/database-locking-plan')
            ->assertJsonPath('data.items.0.focus_checks.0', 'Classify the write invariant before adding locks: inventory, balance, booking, or workflow state.')
            ->assertJsonPath('data.items.0.focus_checks.2', 'Use concurrent replay, deadlock retry, lock timeout, hot-row contention, indexed lookup, and lock-wait monitoring as promotion gates.');
    }

    /**
     * The technology matrix page renders PKCE focus checks for OAuth records.
     */
    public function test_technology_matrix_page_renders_pkce_focus_checks(): void
    {
        $this->get('/practice/technology-matrix?language=en&search=PKCE')
            ->assertOk()
            ->assertSee('oauth-flow')
            ->assertSee('For public clients, require Authorization Code with PKCE and S256.')
            ->assertSee('Do not expose code_verifier in authorize URLs, logs, or rendered pages.');
    }

    /**
     * The technology matrix defaults to English records.
     */
    public function test_technology_matrix_defaults_to_english_records(): void
    {
        $this->get('/practice/technology-matrix?family=laravel&search=Sail')
            ->assertOk()
            ->assertDontSee('Sail, môi trường và triển khai')
            ->assertDontSee('từ local đồng nhất tới production sống khỏe');

        $this->getJson('/api/practice/technology-matrix?family=laravel&search=Sail')
            ->assertOk()
            ->assertJsonPath('data.meta.filters.language', 'en')
            ->assertJsonMissing([
                'source_path' => 'laravel/devops.vi.json',
            ]);
    }
}
