<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Services\Practice\ContentPracticeMapperService;
use Tests\TestCase;

final class TechnologyPracticeTaxonomyTest extends TestCase
{
    /**
     * The technology taxonomy page renders supported keys and workbench links.
     */
    public function test_technology_taxonomy_page_renders_supported_keys(): void
    {
        $response = $this->get('/practice/technology-taxonomy');

        $response
            ->assertOk()
            ->assertSee('Supported technology keys for content-backed practice.')
            ->assertSee('api-validation')
            ->assertSee('jwt-revocation')
            ->assertSee('LLM')
            ->assertSee('Markov decision process')
            ->assertSee('Graph RAG')
            ->assertSee('Cloud Engineer AI')
            ->assertSee('GraphQL')
            ->assertSee('IDOR')
            ->assertSee('reverse proxy')
            ->assertSee('searchable aliases')
            ->assertSee('Search taxonomy')
            ->assertSee('Open API validation workbench')
            ->assertSee('Open taxonomy API');
    }

    /**
     * The technology taxonomy API returns every supported technology with practice metadata.
     */
    public function test_technology_taxonomy_api_returns_complete_catalog(): void
    {
        $expectedCount = count($this->app->make(ContentPracticeMapperService::class)->technologies());

        $response = $this->getJson('/api/practice/technology-taxonomy');

        $response
            ->assertOk()
            ->assertJsonPath('data.meta.technology_count', $expectedCount)
            ->assertJsonPath('data.items.0.technology', 'php')
            ->assertJsonPath('data.items.0.practice.slug', 'php-cli-input-normalizer')
            ->assertJsonPath('data.items.0.related_workbench.path', '/workbench/name-normalizer')
            ->assertJsonStructure([
                'data' => [
                    'items' => [
                        '*' => [
                            'technology',
                            'aliases',
                            'concepts',
                            'learning_goal',
                            'matched_by',
                            'match_score',
                            'match_strength',
                            'practice',
                            'related_workbench',
                            'starter_files',
                            'starter_count',
                            'links',
                        ],
                    ],
                    'meta' => [
                        'technology_count',
                        'total_technology_count',
                        'workbench_count',
                        'starter_file_count',
                        'alias_count',
                        'filtered',
                        'has_results',
                        'empty_state',
                        'filters',
                        'suggested_searches',
                        'strength_counts',
                    ],
                ],
            ]);
    }

    /**
     * The LLM foundations taxonomy item exposes AI-specific aliases and concepts.
     */
    public function test_llm_foundations_taxonomy_metadata_is_discoverable(): void
    {
        $items = $this->getJson('/api/practice/technology-taxonomy')
            ->assertOk()
            ->json('data.items');

        $llmItem = collect($items)->firstWhere('technology', 'llm-foundations');

        $this->assertIsArray($llmItem);
        $this->assertContains('Markov decision process', $llmItem['aliases']);
        $this->assertContains('Predictive AI', $llmItem['aliases']);
        $this->assertContains('Generative AI', $llmItem['aliases']);
        $this->assertContains('AI agent memory', $llmItem['aliases']);
        $this->assertContains('procedural memory', $llmItem['aliases']);
        $this->assertContains('generated content', $llmItem['concepts']);
        $this->assertContains('workflow playbook', $llmItem['concepts']);
        $this->assertContains('PPO', $llmItem['aliases']);
        $this->assertContains('reward', $llmItem['concepts']);
        $this->assertSame('/workbench/llm-decision-loop-plan', $llmItem['related_workbench']['path']);
        $this->assertStringContainsString('predictive outputs', $llmItem['learning_goal']);
    }

    /**
     * Agent-memory searches specialize the LLM taxonomy card to the dedicated memory workbench.
     */
    public function test_agent_memory_taxonomy_search_uses_memory_workbench_and_starters(): void
    {
        $response = $this->getJson('/api/practice/technology-taxonomy?search=agent%20memory');

        $response
            ->assertOk()
            ->assertJsonPath('data.items.0.technology', 'llm-foundations')
            ->assertJsonPath('data.items.0.related_workbench.path', '/workbench/ai-agent-memory-plan')
            ->assertJsonPath('data.items.0.related_workbench.route_name', 'practice.workbench.ai-agent-memory-plan')
            ->assertJsonPath('data.items.0.starter_files.0', 'app/Services/Practice/AiAgentMemoryPlanService.php')
            ->assertJsonPath('data.items.0.starter_files.1', 'tests/Feature/AiAgentMemoryPlanTest.php');

        $this->assertContains('agent memory', $response->json('data.meta.suggested_searches'));
    }

    /**
     * The AI Cloud interview taxonomy item exposes interview and verification aliases.
     */
    public function test_ai_cloud_interview_taxonomy_metadata_is_discoverable(): void
    {
        $items = $this->getJson('/api/practice/technology-taxonomy')
            ->assertOk()
            ->json('data.items');

        $aiInterviewItem = collect($items)->firstWhere('technology', 'ai-cloud-interview');

        $this->assertIsArray($aiInterviewItem);
        $this->assertContains('Cloud Engineer AI', $aiInterviewItem['aliases']);
        $this->assertContains('AWS documentation', $aiInterviewItem['aliases']);
        $this->assertContains('AI failure story', $aiInterviewItem['concepts']);
        $this->assertSame('/workbench/ai-cloud-interview-rubric', $aiInterviewItem['related_workbench']['path']);
        $this->assertStringContainsString('IaC verification', $aiInterviewItem['learning_goal']);
    }

    /**
     * The RAG systems taxonomy item exposes retrieval pattern aliases.
     */
    public function test_rag_systems_taxonomy_metadata_is_discoverable(): void
    {
        $items = $this->getJson('/api/practice/technology-taxonomy')
            ->assertOk()
            ->json('data.items');

        $ragItem = collect($items)->firstWhere('technology', 'rag-systems');

        $this->assertIsArray($ragItem);
        $this->assertContains('Graph RAG', $ragItem['aliases']);
        $this->assertContains('Agentic RAG', $ragItem['aliases']);
        $this->assertContains('groundedness evaluation', $ragItem['concepts']);
        $this->assertSame('/workbench/rag-strategy-plan', $ragItem['related_workbench']['path']);
        $this->assertStringContainsString('evidence requirements', $ragItem['learning_goal']);
    }

    /**
     * The GraphQL API taxonomy item exposes API contract tradeoff aliases.
     */
    public function test_graphql_api_taxonomy_metadata_is_discoverable(): void
    {
        $items = $this->getJson('/api/practice/technology-taxonomy')
            ->assertOk()
            ->json('data.items');

        $graphqlItem = collect($items)->firstWhere('technology', 'graphql-api');

        $this->assertIsArray($graphqlItem);
        $this->assertContains('GraphQL', $graphqlItem['aliases']);
        $this->assertContains('resolver', $graphqlItem['aliases']);
        $this->assertContains('N+1 risk', $graphqlItem['concepts']);
        $this->assertSame('/workbench/graphql-rest-decision', $graphqlItem['related_workbench']['path']);
        $this->assertStringContainsString('resolver performance', $graphqlItem['learning_goal']);
    }

    /**
     * The RESTful API naming taxonomy item exposes endpoint-contract concepts.
     */
    public function test_restful_api_naming_taxonomy_metadata_is_discoverable(): void
    {
        $items = $this->getJson('/api/practice/technology-taxonomy')
            ->assertOk()
            ->json('data.items');

        $item = collect($items)->firstWhere('technology', 'restful-api-naming');

        $this->assertIsArray($item);
        $this->assertContains('RESTful API naming', $item['aliases']);
        $this->assertContains('nested resources', $item['aliases']);
        $this->assertContains('HTTP verb semantics', $item['concepts']);
        $this->assertSame('/workbench/restful-api-naming-plan', $item['related_workbench']['path']);
        $this->assertStringContainsString('stable route names', $item['learning_goal']);
    }

    /**
     * The JavaScript closure taxonomy item exposes lexical-scope interview concepts.
     */
    public function test_javascript_closure_taxonomy_metadata_is_discoverable(): void
    {
        $items = $this->getJson('/api/practice/technology-taxonomy')
            ->assertOk()
            ->json('data.items');

        $closureItem = collect($items)->firstWhere('technology', 'javascript-closures');

        $this->assertIsArray($closureItem);
        $this->assertContains('JavaScript closure', $closureItem['aliases']);
        $this->assertContains('JavaScript hoisting', $closureItem['aliases']);
        $this->assertContains('lexical scope', $closureItem['aliases']);
        $this->assertContains('captured binding', $closureItem['concepts']);
        $this->assertContains('temporal dead zone', $closureItem['concepts']);
        $this->assertSame('/workbench/react-render-optimization-plan', $closureItem['related_workbench']['path']);
        $this->assertStringContainsString('captured bindings', $closureItem['learning_goal']);
    }

    /**
     * The Security Misconfiguration taxonomy item exposes configuration-hardening concepts.
     */
    public function test_security_misconfiguration_taxonomy_metadata_is_discoverable(): void
    {
        $items = $this->getJson('/api/practice/technology-taxonomy')
            ->assertOk()
            ->json('data.items');

        $item = collect($items)->firstWhere('technology', 'security-misconfiguration');

        $this->assertIsArray($item);
        $this->assertContains('Security Misconfiguration', $item['aliases']);
        $this->assertContains('APP_DEBUG', $item['aliases']);
        $this->assertContains('secret exposure', $item['concepts']);
        $this->assertSame('/practice/configuration-readiness', $item['related_workbench']['path']);
        $this->assertStringContainsString('unsafe runtime setup', $item['learning_goal']);
    }

    /**
     * The database taxonomy item exposes covering-index optimization concepts.
     */
    public function test_database_eloquent_taxonomy_metadata_is_discoverable(): void
    {
        $items = $this->getJson('/api/practice/technology-taxonomy')
            ->assertOk()
            ->json('data.items');

        $item = collect($items)->firstWhere('technology', 'database-eloquent');

        $this->assertIsArray($item);
        $this->assertContains('covering index', $item['aliases']);
        $this->assertContains('Index Only Scan', $item['aliases']);
        $this->assertContains('EXPLAIN ANALYZE', $item['aliases']);
        $this->assertContains('VACUUM ANALYZE', $item['aliases']);
        $this->assertContains('heap fetch cost', $item['concepts']);
        $this->assertContains('rollback migration', $item['concepts']);
        $this->assertSame('/workbench/collection-filter-preview', $item['related_workbench']['path']);
        $this->assertStringContainsString('Heap Fetches evidence', $item['learning_goal']);

        $suggestions = $this->getJson('/api/practice/technology-taxonomy')
            ->assertOk()
            ->json('data.meta.suggested_searches');

        $this->assertContains('heap fetch', $suggestions);
        $this->assertContains('explain analyze', $suggestions);
        $this->assertContains('vacuum analyze', $suggestions);
    }

    /**
     * The reverse proxy taxonomy item exposes edge and origin-reachability concepts.
     */
    public function test_reverse_proxy_edge_taxonomy_metadata_is_discoverable(): void
    {
        $items = $this->getJson('/api/practice/technology-taxonomy')
            ->assertOk()
            ->json('data.items');

        $proxyItem = collect($items)->firstWhere('technology', 'reverse-proxy-edge');

        $this->assertIsArray($proxyItem);
        $this->assertContains('Cloudflare', $proxyItem['aliases']);
        $this->assertContains('origin server', $proxyItem['aliases']);
        $this->assertContains('fail-small design', $proxyItem['concepts']);
        $this->assertSame('/workbench/reverse-proxy-failure-plan', $proxyItem['related_workbench']['path']);
        $this->assertStringContainsString('healthy origins can be unreachable', $proxyItem['learning_goal']);
    }

    /**
     * The System Design tradeoff taxonomy item exposes interview decision-making concepts.
     */
    public function test_system_design_tradeoffs_taxonomy_metadata_is_discoverable(): void
    {
        $items = $this->getJson('/api/practice/technology-taxonomy')
            ->assertOk()
            ->json('data.items');

        $tradeoffItem = collect($items)->firstWhere('technology', 'system-design-tradeoffs');

        $this->assertIsArray($tradeoffItem);
        $this->assertContains('System Design', $tradeoffItem['aliases']);
        $this->assertContains('Kafka', $tradeoffItem['aliases']);
        $this->assertContains('operational complexity', $tradeoffItem['concepts']);
        $this->assertSame('/workbench/system-design-tradeoff-plan', $tradeoffItem['related_workbench']['path']);
        $this->assertStringContainsString('rejected alternatives', $tradeoffItem['learning_goal']);
    }

    /**
     * The taxonomy can be filtered by technology aliases and concepts.
     */
    public function test_technology_taxonomy_filters_by_alias_search(): void
    {
        $response = $this->getJson('/api/practice/technology-taxonomy?search=ppo');

        $response
            ->assertOk()
            ->assertJsonPath('data.meta.filters.search', 'ppo')
            ->assertJsonPath('data.meta.filters.strength', null)
            ->assertJsonPath('data.meta.suggested_searches.0', 'ppo')
            ->assertJsonPath('data.meta.filtered', true)
            ->assertJsonPath('data.meta.has_results', true)
            ->assertJsonPath('data.meta.empty_state', null)
            ->assertJsonPath('data.meta.technology_count', 1)
            ->assertJsonPath('data.meta.total_technology_count', count($this->app->make(ContentPracticeMapperService::class)->technologies()))
            ->assertJsonPath('data.items.0.technology', 'llm-foundations')
            ->assertJsonPath('data.items.0.match_score', 110)
            ->assertJsonPath('data.items.0.match_strength', 'strong')
            ->assertJsonPath('data.items.0.related_workbench.path', '/workbench/llm-decision-loop-plan');

        $matches = collect($response->json('data.items.0.matched_by'));

        $this->assertTrue($matches->contains(fn (array $match): bool => $match['field'] === 'alias' && $match['value'] === 'PPO'));
        $this->assertTrue($matches->contains(fn (array $match): bool => $match['field'] === 'learning_goal'));
    }

    /**
     * Predictive AI searches resolve to the LLM foundations taxonomy item.
     */
    public function test_technology_taxonomy_filters_by_predictive_ai_alias(): void
    {
        $response = $this->getJson('/api/practice/technology-taxonomy?search=Predictive%20AI');

        $response
            ->assertOk()
            ->assertJsonPath('data.items.0.technology', 'llm-foundations')
            ->assertJsonPath('data.items.0.related_workbench.path', '/workbench/llm-decision-loop-plan')
            ->assertJsonPath('data.items.0.match_strength', 'direct');

        $matches = collect($response->json('data.items.0.matched_by'));

        $this->assertTrue($matches->contains(fn (array $match): bool => $match['field'] === 'alias' && $match['value'] === 'Predictive AI'));
    }

    /**
     * GraphQL searches resolve to the API contract taxonomy item.
     */
    public function test_technology_taxonomy_filters_by_graphql_alias(): void
    {
        $response = $this->getJson('/api/practice/technology-taxonomy?search=graphql');

        $response
            ->assertOk()
            ->assertJsonPath('data.items.0.technology', 'graphql-api')
            ->assertJsonPath('data.items.0.related_workbench.path', '/workbench/graphql-rest-decision')
            ->assertJsonPath('data.items.0.match_strength', 'strong');

        $matches = collect($response->json('data.items.0.matched_by'));

        $this->assertTrue($matches->contains(fn (array $match): bool => $match['field'] === 'technology'));
        $this->assertTrue($matches->contains(fn (array $match): bool => $match['field'] === 'alias' && $match['value'] === 'GraphQL'));
    }

    /**
     * React render searches resolve to the frontend performance taxonomy item.
     */
    public function test_technology_taxonomy_filters_by_react_render_alias(): void
    {
        $response = $this->getJson('/api/practice/technology-taxonomy?search=useMemo');

        $response
            ->assertOk()
            ->assertJsonPath('data.items.0.technology', 'react-render-performance')
            ->assertJsonPath('data.items.0.related_workbench.path', '/workbench/react-render-optimization-plan')
            ->assertJsonPath('data.items.0.match_strength', 'strong');

        $matches = collect($response->json('data.items.0.matched_by'));

        $this->assertTrue($matches->contains(fn (array $match): bool => $match['field'] === 'alias' && $match['value'] === 'useMemo'));
    }

    /**
     * JavaScript closure searches resolve to the closure interview taxonomy item.
     */
    public function test_technology_taxonomy_filters_by_javascript_closure_alias(): void
    {
        $response = $this->getJson('/api/practice/technology-taxonomy?search=JavaScript%20closure');

        $response
            ->assertOk()
            ->assertJsonPath('data.items.0.technology', 'javascript-closures')
            ->assertJsonPath('data.items.0.related_workbench.path', '/workbench/react-render-optimization-plan')
            ->assertJsonPath('data.items.0.match_strength', 'direct');

        $matches = collect($response->json('data.items.0.matched_by'));

        $this->assertTrue($matches->contains(fn (array $match): bool => $match['field'] === 'alias' && $match['value'] === 'JavaScript closure'));
    }

    /**
     * Security Misconfiguration searches resolve to the configuration-hardening taxonomy item.
     */
    public function test_technology_taxonomy_filters_by_security_misconfiguration_alias(): void
    {
        $response = $this->getJson('/api/practice/technology-taxonomy?search=Security%20Misconfiguration');

        $response
            ->assertOk()
            ->assertJsonPath('data.items.0.technology', 'security-misconfiguration')
            ->assertJsonPath('data.items.0.related_workbench.path', '/practice/configuration-readiness')
            ->assertJsonPath('data.items.0.match_strength', 'direct');

        $matches = collect($response->json('data.items.0.matched_by'));

        $this->assertTrue($matches->contains(fn (array $match): bool => $match['field'] === 'alias' && $match['value'] === 'Security Misconfiguration'));
    }

    /**
     * Covering Index searches resolve to the database taxonomy item.
     */
    public function test_technology_taxonomy_filters_by_covering_index_alias(): void
    {
        $response = $this->getJson('/api/practice/technology-taxonomy?search=covering%20index');

        $response
            ->assertOk()
            ->assertJsonPath('data.items.0.technology', 'database-eloquent')
            ->assertJsonPath('data.items.0.related_workbench.path', '/workbench/collection-filter-preview')
            ->assertJsonPath('data.items.0.match_strength', 'direct');

        $matches = collect($response->json('data.items.0.matched_by'));

        $this->assertTrue($matches->contains(fn (array $match): bool => $match['field'] === 'alias' && $match['value'] === 'covering index'));
    }

    /**
     * EXPLAIN and VACUUM searches resolve to the database query-plan taxonomy item.
     */
    public function test_technology_taxonomy_filters_by_covering_index_operations_aliases(): void
    {
        $this->getJson('/api/practice/technology-taxonomy?search=EXPLAIN%20ANALYZE')
            ->assertOk()
            ->assertJsonPath('data.items.0.technology', 'database-eloquent')
            ->assertJsonPath('data.items.0.match_strength', 'strong');

        $response = $this->getJson('/api/practice/technology-taxonomy?search=VACUUM%20ANALYZE');

        $response
            ->assertOk()
            ->assertJsonPath('data.items.0.technology', 'database-eloquent')
            ->assertJsonPath('data.items.0.related_workbench.path', '/workbench/collection-filter-preview')
            ->assertJsonPath('data.items.0.match_strength', 'direct');

        $matches = collect($response->json('data.items.0.matched_by'));

        $this->assertTrue($matches->contains(fn (array $match): bool => $match['field'] === 'alias' && $match['value'] === 'VACUUM ANALYZE'));
    }

    /**
     * Covering Index taxonomy searches keep context in links and starter files.
     */
    public function test_technology_taxonomy_covering_index_search_uses_contextual_starters_and_links(): void
    {
        foreach (['heap fetch', 'EXPLAIN ANALYZE', 'INCLUDE columns', 'index bloat'] as $search) {
            $response = $this->getJson('/api/practice/technology-taxonomy?search='.urlencode($search));

            $response
                ->assertOk()
                ->assertJsonPath('data.items.0.technology', 'database-eloquent')
                ->assertJsonPath('data.items.0.starter_files.0', 'database/migrations/xxxx_xx_xx_add_orders_covering_index.php')
                ->assertJsonPath('data.items.0.starter_files.1', 'app/Services/Practice/CoveringIndexReviewService.php')
                ->assertJsonPath('data.items.0.links.board', '/practice/technology-board?technology=database-eloquent&search='.str_replace('%20', '+', urlencode(strtolower($search))))
                ->assertJsonPath('data.items.0.links.pipeline', '/practice/technology-learning-pipeline/database-eloquent?search='.str_replace('%20', '+', urlencode(strtolower($search))))
                ->assertJsonPath('data.items.0.links.code_examples', '/practice/technology-code-examples/database-eloquent?search='.str_replace('%20', '+', urlencode(strtolower($search))));
        }
    }

    /**
     * Database locking searches resolve to the database taxonomy item with locking starters.
     */
    public function test_technology_taxonomy_database_locking_search_uses_locking_starters(): void
    {
        $response = $this->getJson('/api/practice/technology-taxonomy?search='.urlencode('lockForUpdate'));

        $response
            ->assertOk()
            ->assertJsonPath('data.items.0.technology', 'database-eloquent')
            ->assertJsonPath('data.items.0.starter_files.0', 'app/Services/Practice/InventoryReservationService.php')
            ->assertJsonPath('data.items.0.starter_files.1', 'tests/Feature/InventoryReservationConcurrencyTest.php')
            ->assertJsonPath('data.items.0.links.pipeline', '/practice/technology-learning-pipeline/database-eloquent?search=lockforupdate')
            ->assertJsonPath('data.items.0.match_strength', 'direct');

        $matches = collect($response->json('data.items.0.matched_by'));

        $this->assertTrue($matches->contains(fn (array $match): bool => $match['field'] === 'alias' && $match['value'] === 'lockForUpdate'));
    }

    /**
     * BFS and DFS searches resolve to the graph traversal taxonomy item.
     */
    public function test_technology_taxonomy_filters_by_bfs_dfs_alias(): void
    {
        $response = $this->getJson('/api/practice/technology-taxonomy?search=BFS%20DFS');

        $response
            ->assertOk()
            ->assertJsonPath('data.items.0.technology', 'graph-traversal')
            ->assertJsonPath('data.items.0.practice.slug', 'graph-traversal-plan-workbench')
            ->assertJsonPath('data.items.0.related_workbench.path', '/workbench/graph-traversal-plan')
            ->assertJsonPath('data.items.0.starter_files.0', 'app/Services/Practice/GraphTraversalPlanService.php')
            ->assertJsonPath('data.items.0.links.pipeline', '/practice/technology-learning-pipeline/graph-traversal?search=bfs+dfs')
            ->assertJsonPath('data.items.0.match_strength', 'direct');

        $matches = collect($response->json('data.items.0.matched_by'));

        $this->assertTrue($matches->contains(fn (array $match): bool => $match['field'] === 'alias' && $match['value'] === 'BFS DFS'));
    }

    /**
     * SQL Injection searches resolve to the parameterized-query defense taxonomy item.
     */
    public function test_technology_taxonomy_filters_by_sql_injection_alias(): void
    {
        $response = $this->getJson('/api/practice/technology-taxonomy?search=SQL%20Injection');

        $response
            ->assertOk()
            ->assertJsonPath('data.items.0.technology', 'sql-injection-defense')
            ->assertJsonPath('data.items.0.related_workbench.path', '/workbench/sql-injection-defense-plan')
            ->assertJsonPath('data.items.0.match_strength', 'strong');
    }

    /**
     * CSRF searches resolve to the browser request-intent taxonomy item.
     */
    public function test_technology_taxonomy_filters_by_csrf_alias(): void
    {
        $response = $this->getJson('/api/practice/technology-taxonomy?search=csrf');

        $response
            ->assertOk()
            ->assertJsonPath('data.items.0.technology', 'csrf-protection')
            ->assertJsonPath('data.items.0.related_workbench.path', '/workbench/csrf-protection-plan')
            ->assertJsonPath('data.items.0.match_strength', 'strong');

        $matches = collect($response->json('data.items.0.matched_by'));

        $this->assertTrue($matches->contains(fn (array $match): bool => $match['field'] === 'technology'));
        $this->assertTrue($matches->contains(fn (array $match): bool => $match['field'] === 'alias' && $match['value'] === 'CSRF'));
    }

    /**
     * XSS searches resolve to the safe browser rendering taxonomy item.
     */
    public function test_technology_taxonomy_filters_by_xss_alias(): void
    {
        $response = $this->getJson('/api/practice/technology-taxonomy?search=xss');

        $response
            ->assertOk()
            ->assertJsonPath('data.items.0.technology', 'xss-defense')
            ->assertJsonPath('data.items.0.related_workbench.path', '/workbench/security-escape-preview')
            ->assertJsonPath('data.items.0.match_strength', 'strong');

        $matches = collect($response->json('data.items.0.matched_by'));

        $this->assertTrue($matches->contains(fn (array $match): bool => $match['field'] === 'technology'));
        $this->assertTrue($matches->contains(fn (array $match): bool => $match['field'] === 'alias' && $match['value'] === 'XSS'));
    }

    /**
     * IDOR searches resolve to the object-level authorization taxonomy item.
     */
    public function test_technology_taxonomy_filters_by_idor_alias(): void
    {
        $response = $this->getJson('/api/practice/technology-taxonomy?search=idor');

        $response
            ->assertOk()
            ->assertJsonPath('data.items.0.technology', 'idor-access-control')
            ->assertJsonPath('data.items.0.practice.slug', 'idor-access-review-workbench')
            ->assertJsonPath('data.items.0.related_workbench.path', '/workbench/idor-access-review')
            ->assertJsonPath('data.items.0.match_strength', 'strong');

        $matches = collect($response->json('data.items.0.matched_by'));

        $this->assertTrue($matches->contains(fn (array $match): bool => $match['field'] === 'technology'));
        $this->assertTrue($matches->contains(fn (array $match): bool => $match['field'] === 'alias' && $match['value'] === 'IDOR'));
    }

    /**
     * Arrow-function this searches resolve to the JavaScript closure taxonomy item.
     */
    public function test_technology_taxonomy_filters_by_arrow_this_alias(): void
    {
        $response = $this->getJson('/api/practice/technology-taxonomy?search=arrow%20this');

        $response
            ->assertOk()
            ->assertJsonPath('data.items.0.technology', 'javascript-closures')
            ->assertJsonPath('data.items.0.practice.slug', 'react-render-optimization-workbench')
            ->assertJsonPath('data.items.0.related_workbench.path', '/workbench/react-render-optimization-plan')
            ->assertJsonPath('data.items.0.match_strength', 'direct');

        $matches = collect($response->json('data.items.0.matched_by'));

        $this->assertTrue($matches->contains(fn (array $match): bool => $match['field'] === 'alias' && $match['value'] === 'arrow this'));
    }

    /**
     * Client Credentials searches resolve to the OAuth flow taxonomy item.
     */
    public function test_technology_taxonomy_filters_by_client_credentials_alias(): void
    {
        $response = $this->getJson('/api/practice/technology-taxonomy?search=Client%20Credentials');

        $response
            ->assertOk()
            ->assertJsonPath('data.items.0.technology', 'oauth-flow')
            ->assertJsonPath('data.items.0.related_workbench.path', '/workbench/oauth-flow-plan')
            ->assertJsonPath('data.items.0.match_strength', 'strong');

        $matches = collect($response->json('data.items.0.matched_by'));

        $this->assertTrue($matches->contains(fn (array $match): bool => $match['field'] === 'alias' && $match['value'] === 'Client Credentials'));
    }

    /**
     * PKCE searches resolve to the OAuth flow taxonomy item.
     */
    public function test_technology_taxonomy_filters_by_pkce_alias(): void
    {
        $response = $this->getJson('/api/practice/technology-taxonomy?search=PKCE');

        $response
            ->assertOk()
            ->assertJsonPath('data.items.0.technology', 'oauth-flow')
            ->assertJsonPath('data.items.0.related_workbench.path', '/workbench/oauth-flow-plan')
            ->assertJsonPath('data.items.0.match_strength', 'strong');

        $matches = collect($response->json('data.items.0.matched_by'));

        $this->assertTrue($matches->contains(fn (array $match): bool => $match['field'] === 'alias' && $match['value'] === 'PKCE'));
    }

    /**
     * Stack and heap searches resolve to the PHP runtime taxonomy item.
     */
    public function test_technology_taxonomy_filters_by_stack_heap_alias(): void
    {
        $response = $this->getJson('/api/practice/technology-taxonomy?search=stack%20heap');

        $response
            ->assertOk()
            ->assertJsonPath('data.items.0.technology', 'php')
            ->assertJsonPath('data.items.0.related_workbench.path', '/workbench/name-normalizer')
            ->assertJsonPath('data.items.0.match_strength', 'direct');

        $matches = collect($response->json('data.items.0.matched_by'));

        $this->assertTrue($matches->contains(fn (array $match): bool => $match['field'] === 'alias' && $match['value'] === 'stack heap'));
    }

    /**
     * RAG searches resolve to the retrieval strategy taxonomy item.
     */
    public function test_technology_taxonomy_filters_by_rag_alias(): void
    {
        $response = $this->getJson('/api/practice/technology-taxonomy?search=rag');

        $response
            ->assertOk()
            ->assertJsonPath('data.items.0.technology', 'rag-systems')
            ->assertJsonPath('data.items.0.related_workbench.path', '/workbench/rag-strategy-plan')
            ->assertJsonPath('data.items.0.match_strength', 'strong');

        $matches = collect($response->json('data.items.0.matched_by'));

        $this->assertTrue($matches->contains(fn (array $match): bool => $match['field'] === 'technology'));
        $this->assertTrue($matches->contains(fn (array $match): bool => $match['field'] === 'alias' && $match['value'] === 'RAG'));
    }

    /**
     * Long Context and CAG searches resolve to the retrieval strategy taxonomy item.
     */
    public function test_technology_taxonomy_filters_by_chatbot_context_strategy_aliases(): void
    {
        $longContext = $this->getJson('/api/practice/technology-taxonomy?search=Long%20Context');

        $longContext
            ->assertOk()
            ->assertJsonPath('data.items.0.technology', 'rag-systems')
            ->assertJsonPath('data.items.0.match_strength', 'strong');

        $cag = $this->getJson('/api/practice/technology-taxonomy?search=CAG');

        $cag
            ->assertOk()
            ->assertJsonPath('data.items.0.technology', 'rag-systems')
            ->assertJsonPath('data.items.0.match_strength', 'strong');
    }

    /**
     * Reverse proxy searches resolve to the edge proxy taxonomy item.
     */
    public function test_technology_taxonomy_filters_by_reverse_proxy_alias(): void
    {
        $response = $this->getJson('/api/practice/technology-taxonomy?search=reverse%20proxy');

        $response
            ->assertOk()
            ->assertJsonPath('data.items.0.technology', 'reverse-proxy-edge')
            ->assertJsonPath('data.items.0.related_workbench.path', '/workbench/reverse-proxy-failure-plan')
            ->assertJsonPath('data.items.0.match_strength', 'strong');
    }

    /**
     * System Design searches resolve to the tradeoff taxonomy item.
     */
    public function test_technology_taxonomy_filters_by_system_design_alias(): void
    {
        $response = $this->getJson('/api/practice/technology-taxonomy?search=System%20Design%20tradeoff');

        $response
            ->assertOk()
            ->assertJsonPath('data.items.0.technology', 'system-design-tradeoffs')
            ->assertJsonPath('data.items.0.related_workbench.path', '/workbench/system-design-tradeoff-plan')
            ->assertJsonPath('data.items.0.match_strength', 'direct');
    }

    /**
     * SIEM and ELK searches resolve to the security observability taxonomy item.
     */
    public function test_technology_taxonomy_filters_by_siem_elk_alias(): void
    {
        $response = $this->getJson('/api/practice/technology-taxonomy?search=SIEM%20ELK');

        $response
            ->assertOk()
            ->assertJsonPath('data.items.0.technology', 'siem-elk-observability')
            ->assertJsonPath('data.items.0.related_workbench.path', '/workbench/siem-elk-plan')
            ->assertJsonPath('data.items.0.match_strength', 'direct');
    }

    /**
     * The taxonomy page keeps the search term visible after filtering.
     */
    public function test_technology_taxonomy_page_filters_by_search(): void
    {
        $response = $this->get('/practice/technology-taxonomy?search=ppo');

        $response
            ->assertOk()
            ->assertSee('value="ppo"', false)
            ->assertSee('Match strength')
            ->assertSee('strong (1)')
            ->assertSee('1 of')
            ->assertSee('llm-foundations')
            ->assertSee('Matched by:')
            ->assertSee('score:')
            ->assertSee('strength:')
            ->assertSee('Open LLM decision-loop workbench')
            ->assertDontSee('jwt-revocation');
    }

    /**
     * Search results are sorted by match relevance.
     */
    public function test_technology_taxonomy_search_sorts_by_match_score(): void
    {
        $items = $this->getJson('/api/practice/technology-taxonomy?search=jwt')
            ->assertOk()
            ->json('data.items');

        $this->assertSame('jwt-revocation', $items[0]['technology']);
        $this->assertSame('jwt-token-storage', $items[1]['technology']);
        $this->assertGreaterThanOrEqual($items[1]['match_score'], $items[0]['match_score']);
        $this->assertGreaterThan(0, $items[0]['match_score']);
    }

    /**
     * Search results can be filtered by match strength.
     */
    public function test_technology_taxonomy_filters_by_match_strength(): void
    {
        $response = $this->getJson('/api/practice/technology-taxonomy?search=jwt&strength=strong');

        $response
            ->assertOk()
            ->assertJsonPath('data.meta.filters.search', 'jwt')
            ->assertJsonPath('data.meta.filters.strength', 'strong')
            ->assertJsonPath('data.meta.strength_counts.strong', 2)
            ->assertJsonPath('data.meta.has_results', true);

        foreach ($response->json('data.items') as $item) {
            $this->assertSame('strong', $item['match_strength']);
        }
    }

    /**
     * Invalid match-strength filters are ignored rather than breaking search.
     */
    public function test_technology_taxonomy_ignores_invalid_match_strength(): void
    {
        $response = $this->getJson('/api/practice/technology-taxonomy?search=ppo&strength=exact');

        $response
            ->assertOk()
            ->assertJsonPath('data.meta.filters.strength', null)
            ->assertJsonPath('data.items.0.technology', 'llm-foundations');
    }

    /**
     * Empty taxonomy searches explain that no result matched and keep recovery suggestions visible.
     */
    public function test_technology_taxonomy_empty_search_returns_empty_state(): void
    {
        $response = $this->getJson('/api/practice/technology-taxonomy?search=not-a-real-technology');

        $response
            ->assertOk()
            ->assertJsonPath('data.meta.filtered', true)
            ->assertJsonPath('data.meta.has_results', false)
            ->assertJsonPath('data.meta.technology_count', 0)
            ->assertJsonPath('data.meta.empty_state.title', "No technology matches 'not-a-real-technology'.")
            ->assertJsonPath('data.meta.empty_state.suggestions.0', 'ppo');
    }

    /**
     * The taxonomy page renders an empty state when a search has no matches.
     */
    public function test_technology_taxonomy_page_renders_empty_search_state(): void
    {
        $response = $this->get('/practice/technology-taxonomy?search=not-a-real-technology');

        $response
            ->assertOk()
            ->assertSee("No technology matches 'not-a-real-technology'.")
            ->assertSee('Try a shorter keyword')
            ->assertSee('ppo')
            ->assertDontSee('Open LLM decision-loop workbench');
    }

    /**
     * Every declared technology has an exercise, starter files, and a workbench link.
     */
    public function test_every_declared_technology_has_required_taxonomy_metadata(): void
    {
        $items = $this->getJson('/api/practice/technology-taxonomy')
            ->assertOk()
            ->json('data.items');

        foreach ($items as $item) {
            $this->assertNotNull($item['practice']['slug'], "Missing practice slug for {$item['technology']}.");
            $this->assertNotEmpty($item['aliases'], "Missing aliases for {$item['technology']}.");
            $this->assertNotEmpty($item['concepts'], "Missing concepts for {$item['technology']}.");
            $this->assertNotSame('', $item['learning_goal'], "Missing learning goal for {$item['technology']}.");
            $this->assertNotSame('', $item['practice']['title'], "Missing practice title for {$item['technology']}.");
            $this->assertNotNull($item['related_workbench'], "Missing workbench for {$item['technology']}.");
            $this->assertGreaterThan(0, $item['starter_count'], "Missing starter snippets for {$item['technology']}.");
            $this->assertNotEmpty($item['links']['board'], "Missing board link for {$item['technology']}.");
            $this->assertNotEmpty($item['links']['pipeline'], "Missing pipeline link for {$item['technology']}.");
        }
    }
}
