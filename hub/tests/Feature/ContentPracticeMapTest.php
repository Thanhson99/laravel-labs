<?php

declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;

final class ContentPracticeMapTest extends TestCase
{
    /**
     * The content map page links JSON learning content to native practice exercises.
     */
    public function test_content_practice_map_page_renders_mapped_tasks(): void
    {
        $response = $this->get('/practice/content-map?family=laravel&language=en&search=api&technology=api-validation&limit=5');

        $response
            ->assertOk()
            ->assertSee('Learning content is connected directly to Laravel coding work.')
            ->assertSee('api-validation')
            ->assertSee('Build a validated practice API endpoint')
            ->assertSee('Open source JSON');
    }

    /**
     * The content map API returns source, content, technology, and practice data.
     */
    public function test_content_practice_map_api_returns_mapped_tasks(): void
    {
        $response = $this->getJson('/api/practice/content-map?family=laravel&language=en&search=api&technology=api-validation&limit=3');

        $response
            ->assertOk()
            ->assertJsonPath('data.meta.filters.family', 'laravel')
            ->assertJsonPath('data.meta.filters.language', 'en')
            ->assertJsonPath('data.meta.filters.technology', 'api-validation')
            ->assertJsonPath('data.items.0.technology', 'api-validation')
            ->assertJsonPath('data.items.0.practice.slug', 'api-form-request-slice')
            ->assertJsonStructure([
                'data' => [
                    'items' => [
                        '*' => [
                            'id',
                            'technology',
                            'source' => [
                                'key',
                                'path',
                                'family',
                                'topic',
                                'language',
                                'title',
                            ],
                            'content' => [
                                'title',
                                'type',
                                'group',
                            ],
                            'practice' => [
                                'slug',
                                'title',
                                'track',
                            ],
                            'task',
                        ],
                    ],
                    'meta' => [
                        'filters',
                        'count',
                        'technologies',
                    ],
                ],
            ]);
    }

    /**
     * RESTful API naming content maps to the endpoint contract review lane.
     */
    public function test_content_practice_map_api_returns_restful_api_naming_tasks(): void
    {
        $this->getJson('/api/practice/content-map?family=laravel&language=en&search=RESTful%20API%20naming&technology=restful-api-naming&limit=3')
            ->assertOk()
            ->assertJsonPath('data.meta.filters.technology', 'restful-api-naming')
            ->assertJsonPath('data.items.0.technology', 'restful-api-naming')
            ->assertJsonPath('data.items.0.source.path', 'laravel/api-integration.en.json')
            ->assertJsonPath('data.items.0.practice.slug', 'restful-api-naming-review')
            ->assertJsonPath('data.items.0.task', 'Create a RESTful endpoint naming review and Laravel route map for: 151. RESTful API naming: what does \'clean\' mean?');
    }

    /**
     * The content map routes XSS source records to the safe rendering practice.
     */
    public function test_content_practice_map_api_returns_xss_defense_tasks(): void
    {
        $this->getJson('/api/practice/content-map?family=laravel&language=en&search=XSS&technology=xss-defense&limit=3')
            ->assertOk()
            ->assertJsonPath('data.meta.filters.technology', 'xss-defense')
            ->assertJsonPath('data.items.0.technology', 'xss-defense')
            ->assertJsonPath('data.items.0.source.path', 'laravel/auth-security.en.json')
            ->assertJsonPath('data.items.0.practice.slug', 'blade-escaping-xss-preview')
            ->assertJsonPath('data.items.0.task', 'Create an XSS output-escaping and safe-rendering plan for: 71. Why is XSS still a real Laravel problem even though Blade escapes by default?');
    }

    /**
     * IDOR content maps to the object-level authorization review lane.
     */
    public function test_content_practice_map_api_returns_idor_access_control_tasks(): void
    {
        $this->getJson('/api/practice/content-map?family=laravel&language=en&search=IDOR&technology=idor-access-control&limit=3')
            ->assertOk()
            ->assertJsonPath('data.meta.filters.technology', 'idor-access-control')
            ->assertJsonPath('data.items.0.technology', 'idor-access-control')
            ->assertJsonPath('data.items.0.source.path', 'laravel/auth-security.en.json')
            ->assertJsonPath('data.items.0.practice.slug', 'idor-access-review-workbench')
            ->assertJsonPath('data.items.0.task', 'Create an IDOR object-level authorization review for: 113. Understand IDOR through a real API example');
    }

    /**
     * Broken authentication content maps to the auth-security review lane.
     */
    public function test_content_practice_map_api_returns_broken_authentication_tasks(): void
    {
        $this->getJson('/api/practice/content-map?family=laravel&language=en&search=Broken%20Authentication&technology=auth-security&limit=3')
            ->assertOk()
            ->assertJsonPath('data.meta.filters.technology', 'auth-security')
            ->assertJsonPath('data.items.0.technology', 'auth-security')
            ->assertJsonPath('data.items.0.source.path', 'laravel/auth-security.en.json')
            ->assertJsonPath('data.items.0.practice.slug', 'authorization-policy-plan-workbench')
            ->assertJsonPath('data.items.0.task', 'Create a broken authentication lifecycle review for: 114. Common Broken Authentication Mistakes');
    }

    /**
     * Predictive AI and Generative AI content maps to the LLM foundations workbench lane.
     */
    public function test_content_practice_map_api_returns_predictive_generative_ai_tasks(): void
    {
        $this->getJson('/api/practice/content-map?family=vibe-coding&language=en&search=Predictive%20AI&technology=llm-foundations&limit=3')
            ->assertOk()
            ->assertJsonPath('data.meta.filters.technology', 'llm-foundations')
            ->assertJsonPath('data.items.0.technology', 'llm-foundations')
            ->assertJsonPath('data.items.0.source.path', 'vibe-coding/prompting.en.json')
            ->assertJsonPath('data.items.0.practice.slug', 'llm-decision-loop-plan-workbench')
            ->assertJsonPath('data.items.0.task', 'Create an LLM, Predictive AI, Generative AI, and feedback model explanation for: What is the difference between Predictive AI and Generative AI?');
    }

    /**
     * AI agent memory content maps to the LLM foundations workbench lane.
     */
    public function test_content_practice_map_api_returns_ai_agent_memory_tasks(): void
    {
        $this->getJson('/api/practice/content-map?family=vibe-coding&language=en&search=agent%20memory&technology=llm-foundations&limit=3')
            ->assertOk()
            ->assertJsonPath('data.meta.filters.technology', 'llm-foundations')
            ->assertJsonPath('data.items.0.technology', 'llm-foundations')
            ->assertJsonPath('data.items.0.source.path', 'vibe-coding/prompting.en.json')
            ->assertJsonPath('data.items.0.practice.slug', 'llm-decision-loop-plan-workbench')
            ->assertJsonPath('data.items.0.task', 'Create an LLM, Predictive AI, Generative AI, and feedback model explanation for: 4 core AI agent memory types that help it work like a developer');
    }

    /**
     * Chatbot context-strategy content maps to the RAG systems workbench lane.
     */
    public function test_content_practice_map_api_returns_rag_long_context_cag_tasks(): void
    {
        $this->getJson('/api/practice/content-map?family=vibe-coding&language=en&search=Long%20Context&technology=rag-systems&limit=3')
            ->assertOk()
            ->assertJsonPath('data.meta.filters.technology', 'rag-systems')
            ->assertJsonPath('data.items.0.technology', 'rag-systems')
            ->assertJsonPath('data.items.0.source.path', 'vibe-coding/prompting.en.json')
            ->assertJsonPath('data.items.0.practice.slug', 'rag-strategy-plan-workbench')
            ->assertJsonPath('data.items.0.task', 'Create a RAG, Long Context, CAG, or hybrid chatbot context strategy decision for: RAG, Long Context, or CAG: which should power an AI chatbot?');
    }

    /**
     * JavaScript closure content maps to its own frontend interview lane.
     */
    public function test_content_practice_map_api_returns_javascript_closure_tasks(): void
    {
        $this->getJson('/api/practice/content-map?family=laravel&language=en&search=JavaScript%20closure&technology=javascript-closures&limit=3')
            ->assertOk()
            ->assertJsonPath('data.meta.filters.technology', 'javascript-closures')
            ->assertJsonPath('data.items.0.technology', 'javascript-closures')
            ->assertJsonPath('data.items.0.source.path', 'laravel/frontend.en.json')
            ->assertJsonPath('data.items.0.practice.slug', 'react-render-optimization-workbench')
            ->assertJsonPath('data.items.0.task', 'Create a JavaScript closure explanation, example, and interview checklist for: What is a closure in JavaScript, and why is it important?');
    }

    /**
     * JavaScript hoisting content maps to the frontend interview practice lane.
     */
    public function test_content_practice_map_api_returns_javascript_hoisting_tasks(): void
    {
        $this->getJson('/api/practice/content-map?family=laravel&language=en&search=JavaScript%20hoisting&technology=javascript-closures&limit=3')
            ->assertOk()
            ->assertJsonPath('data.meta.filters.technology', 'javascript-closures')
            ->assertJsonPath('data.items.0.technology', 'javascript-closures')
            ->assertJsonPath('data.items.0.source.path', 'laravel/frontend.en.json')
            ->assertJsonPath('data.items.0.practice.slug', 'react-render-optimization-workbench')
            ->assertJsonPath('data.items.0.task', 'Create a JavaScript hoisting explanation, visual code example, and interview checklist for: JavaScript hoisting explained simply');
    }

    /**
     * Arrow-function this content maps to the frontend interview practice lane.
     */
    public function test_content_practice_map_api_returns_arrow_this_tasks(): void
    {
        $this->getJson('/api/practice/content-map?family=laravel&language=en&search=arrow%20function%20this&technology=javascript-closures&limit=3')
            ->assertOk()
            ->assertJsonPath('data.meta.filters.technology', 'javascript-closures')
            ->assertJsonPath('data.items.0.technology', 'javascript-closures')
            ->assertJsonPath('data.items.0.source.path', 'laravel/frontend.en.json')
            ->assertJsonPath('data.items.0.practice.slug', 'react-render-optimization-workbench')
            ->assertJsonPath('data.items.0.task', 'Create a JavaScript arrow-function `this` comparison, code example, and interview checklist for: 494. Arrow function `this` versus normal function `this` in JavaScript');
    }

    /**
     * JavaScript hoisting interview content maps to the same frontend practice lane.
     */
    public function test_content_practice_map_api_returns_javascript_hoisting_interview_tasks(): void
    {
        $this->getJson('/api/practice/content-map?family=interview&language=en&search=JavaScript%20hoisting&technology=javascript-closures&limit=3')
            ->assertOk()
            ->assertJsonPath('data.meta.filters.family', 'interview')
            ->assertJsonPath('data.meta.filters.technology', 'javascript-closures')
            ->assertJsonPath('data.items.0.technology', 'javascript-closures')
            ->assertJsonPath('data.items.0.source.path', 'interview/junior.en.json')
            ->assertJsonPath('data.items.0.practice.slug', 'react-render-optimization-workbench')
            ->assertJsonPath('data.items.0.task', 'Create a JavaScript hoisting explanation, visual code example, and interview checklist for: Explain JavaScript hoisting in a simple way with code.');
    }

    /**
     * Security Misconfiguration content maps to configuration readiness practice.
     */
    public function test_content_practice_map_api_returns_security_misconfiguration_tasks(): void
    {
        $this->getJson('/api/practice/content-map?family=laravel&language=en&search=Security%20Misconfiguration&technology=security-misconfiguration&limit=3')
            ->assertOk()
            ->assertJsonPath('data.meta.filters.technology', 'security-misconfiguration')
            ->assertJsonPath('data.items.0.technology', 'security-misconfiguration')
            ->assertJsonPath('data.items.0.source.path', 'laravel/auth-security.en.json')
            ->assertJsonPath('data.items.0.practice.slug', 'authorization-policy-plan-workbench')
            ->assertJsonPath('data.items.0.task', 'Create a security misconfiguration readiness checklist for: 112. Common Security Misconfiguration');
    }

    /**
     * Covering Index content maps to the database and query-practice lane.
     */
    public function test_content_practice_map_api_returns_covering_index_tasks(): void
    {
        $this->getJson('/api/practice/content-map?family=laravel&language=en&search=Covering%20Index&technology=database-eloquent&limit=3')
            ->assertOk()
            ->assertJsonPath('data.meta.filters.technology', 'database-eloquent')
            ->assertJsonPath('data.items.0.technology', 'database-eloquent')
            ->assertJsonPath('data.items.0.source.path', 'laravel/performance-search.en.json')
            ->assertJsonPath('data.items.0.practice.slug', 'laravel-thin-controller')
            ->assertJsonPath('data.items.0.task', 'Create a covering-index and EXPLAIN verification plan for: 164. Covering Index: why an indexed query can still be slow');
    }

    /**
     * BFS and DFS content maps to the graph traversal practice lane.
     */
    public function test_content_practice_map_api_returns_bfs_dfs_graph_traversal_tasks(): void
    {
        $this->getJson('/api/practice/content-map?family=laravel&language=en&search=BFS%20DFS&technology=graph-traversal&limit=3')
            ->assertOk()
            ->assertJsonPath('data.meta.filters.technology', 'graph-traversal')
            ->assertJsonPath('data.items.0.technology', 'graph-traversal')
            ->assertJsonPath('data.items.0.source.path', 'laravel/performance-search.en.json')
            ->assertJsonPath('data.items.0.practice.slug', 'graph-traversal-plan-workbench')
            ->assertJsonPath('data.items.0.task', 'Create a BFS/DFS traversal plan with API and database guardrails for: 165. BFS vs DFS: choosing the right traversal strategy');
    }
}
