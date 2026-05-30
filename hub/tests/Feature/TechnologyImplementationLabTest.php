<?php

declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;

final class TechnologyImplementationLabTest extends TestCase
{
    /**
     * The technology implementation lab page renders a sequential implementation path.
     */
    public function test_technology_implementation_lab_page_renders_phases_and_workspaces(): void
    {
        $response = $this->get('/practice/technology-implementation-lab/api-validation?family=laravel&language=en&search=api&limit=3');

        $response
            ->assertOk()
            ->assertSee('Technology Implementation Lab: api-validation')
            ->assertSee('Read source records')
            ->assertSee('Create focused implementation files')
            ->assertSee('Connect route, service, and verification')
            ->assertSee('Open API validation workbench')
            ->assertSee('php artisan test --filter ContentBackedApiDrill')
            ->assertSee('Prepare commit plan')
            ->assertSee('Create portfolio artifact')
            ->assertSee('Practice interview defense')
            ->assertSee('Open workspace')
            ->assertSee('Open lab API');
    }

    /**
     * The technology implementation lab API returns phases, commands, and progress data.
     */
    public function test_technology_implementation_lab_api_returns_phases_and_progress_payload(): void
    {
        $response = $this->getJson('/api/practice/technology-implementation-lab/api-validation?family=laravel&language=en&search=api&limit=3');

        $response
            ->assertOk()
            ->assertJsonPath('data.technology', 'api-validation')
            ->assertJsonPath('data.practice.slug', 'api-form-request-slice')
            ->assertJsonPath('data.related_workbench.path', '/workbench/topic-intake')
            ->assertJsonPath('data.phases.0.label', 'Read source records')
            ->assertJsonPath('data.commands.0', 'php artisan test --filter ContentBackedApiDrill')
            ->assertJsonPath('data.progress_payload.items.0.label', 'Read source records')
            ->assertJsonPath('data.next_actions.0.path', '/practice/technology-code-examples/api-validation?family=laravel&language=en&search=api&limit=3')
            ->assertJsonPath('data.next_actions.1.path', '/practice/technology-commit-plan/api-validation?family=laravel&language=en&search=api&limit=3')
            ->assertJsonPath('data.next_actions.2.api_path', '/api/practice/technology-portfolio-artifact/api-validation?family=laravel&language=en&search=api&limit=3')
            ->assertJsonStructure([
                'data' => [
                    'title',
                    'technology',
                    'practice',
                    'related_workbench',
                    'source_examples' => [
                        'items' => [
                            '*' => [
                                'record_id',
                                'workspace_query',
                                'snippets',
                            ],
                        ],
                    ],
                    'phases' => [
                        '*' => [
                            'label',
                            'goal',
                            'tasks',
                        ],
                    ],
                    'commands',
                    'progress_payload',
                    'progress_api',
                    'next_actions' => [
                        '*' => [
                            'label',
                            'purpose',
                            'path',
                            'api_path',
                        ],
                    ],
                    'meta',
                ],
            ]);
    }

    /**
     * Specialized implementation labs use their runnable workbench verification command.
     */
    public function test_specialized_implementation_lab_uses_workbench_commands(): void
    {
        $this->getJson('/api/practice/technology-implementation-lab/lsm-tree-storage?language=en&search=LSM%20Tree&limit=3')
            ->assertOk()
            ->assertJsonPath('data.technology', 'lsm-tree-storage')
            ->assertJsonPath('data.practice.slug', 'lsm-tree-plan-workbench')
            ->assertJsonPath('data.related_workbench.path', '/workbench/lsm-tree-plan')
            ->assertJsonPath('data.commands.0', 'php artisan test --filter LsmTreePlanWorkbenchTest');

        $this->getJson('/api/practice/technology-implementation-lab/jwt-revocation?language=en&search=JWT%20is%20stateless&limit=3')
            ->assertOk()
            ->assertJsonPath('data.technology', 'jwt-revocation')
            ->assertJsonPath('data.practice.slug', 'jwt-revocation-plan-workbench')
            ->assertJsonPath('data.related_workbench.path', '/workbench/jwt-revocation-plan')
            ->assertJsonPath('data.commands.0', 'php artisan test --filter JwtRevocationPlanWorkbenchTest');

        $this->getJson('/api/practice/technology-implementation-lab/oauth-flow?language=en&search=PKCE&limit=3')
            ->assertOk()
            ->assertJsonPath('data.technology', 'oauth-flow')
            ->assertJsonPath('data.practice.slug', 'oauth-flow-plan-workbench')
            ->assertJsonPath('data.related_workbench.path', '/workbench/oauth-flow-plan')
            ->assertJsonPath('data.commands.0', 'php artisan test --filter OauthFlowPlanWorkbenchTest')
            ->assertJsonPath('data.phases.0.label', 'Classify OAuth client and flow')
            ->assertJsonPath('data.phases.1.label', 'Build PKCE authorize request')
            ->assertJsonPath('data.phases.2.tasks.1', 'Exchange the code with code_verifier only at the token endpoint and clear the verifier after use.');

        $this->getJson('/api/practice/technology-implementation-lab/graph-traversal?language=en&search=BFS%20DFS&limit=3')
            ->assertOk()
            ->assertJsonPath('data.technology', 'graph-traversal')
            ->assertJsonPath('data.practice.slug', 'graph-traversal-plan-workbench')
            ->assertJsonPath('data.related_workbench.path', '/workbench/graph-traversal-plan')
            ->assertJsonPath('data.commands.0', 'php artisan test --filter GraphTraversalPlanWorkbenchTest')
            ->assertJsonPath('data.phases.0.label', 'Classify traversal goal')
            ->assertJsonPath('data.phases.1.label', 'Implement traversal state')
            ->assertJsonPath('data.phases.2.tasks.0', 'Add a cyclic graph case and prove visited-set handling prevents repeated work.');

        $this->getJson('/api/practice/technology-implementation-lab/llm-foundations?language=en&search=large%20language%20models&limit=3')
            ->assertOk()
            ->assertJsonPath('data.technology', 'llm-foundations')
            ->assertJsonPath('data.practice.slug', 'llm-decision-loop-plan-workbench')
            ->assertJsonPath('data.related_workbench.path', '/workbench/llm-decision-loop-plan')
            ->assertJsonPath('data.commands.0', 'php artisan test --filter LlmDecisionLoopPlanWorkbenchTest');

        $this->getJson('/api/practice/technology-implementation-lab/llm-foundations?language=en&search=Predictive%20AI&limit=3')
            ->assertOk()
            ->assertJsonPath('data.technology', 'llm-foundations')
            ->assertJsonPath('data.practice.slug', 'llm-decision-loop-plan-workbench')
            ->assertJsonPath('data.phases.0.label', 'Separate AI output contracts')
            ->assertJsonPath('data.phases.1.goal', 'Connect predictive work to historical data and labels, and generative work to prompt, context, retrieved evidence, and constraints.')
            ->assertJsonPath('data.phases.2.tasks.0', 'List at least two predictive metrics such as precision, recall, calibration, AUC, error rate, or business lift.')
            ->assertJsonPath('data.phases.2.tasks.2', 'Document failure modes separately: drift, overfitting, and biased labels for Predictive AI; hallucination, fabricated citations, prompt injection, and unsafe code for Generative AI.');

        $this->getJson('/api/practice/technology-implementation-lab/llm-foundations?language=en&search=agent%20memory&limit=3')
            ->assertOk()
            ->assertJsonPath('data.technology', 'llm-foundations')
            ->assertJsonPath('data.practice.slug', 'llm-decision-loop-plan-workbench')
            ->assertJsonPath('data.related_workbench.path', '/workbench/ai-agent-memory-plan')
            ->assertJsonPath('data.commands.0', 'php artisan test --filter AiAgentMemoryPlanWorkbenchTest')
            ->assertJsonPath('data.commands.1', 'php artisan test --filter AiAgentMemoryPlanServiceTest')
            ->assertJsonPath('data.phases.0.label', 'Separate memory contracts')
            ->assertJsonPath('data.phases.1.label', 'Design memory governance')
            ->assertJsonPath('data.phases.1.tasks.0', 'Define scope, owner, source, freshness, confidence, permission, retention, and correction rules for each memory type.')
            ->assertJsonPath('data.phases.3.tasks.0', 'Add tests for stale semantic memory, private episodic memory, missing source, low confidence, and procedural playbook mismatch.');

        $this->getJson('/api/practice/technology-implementation-lab/rag-systems?language=en&search=Graph%20RAG&limit=3')
            ->assertOk()
            ->assertJsonPath('data.technology', 'rag-systems')
            ->assertJsonPath('data.practice.slug', 'rag-strategy-plan-workbench')
            ->assertJsonPath('data.related_workbench.path', '/workbench/rag-strategy-plan')
            ->assertJsonPath('data.commands.0', 'php artisan test --filter RagStrategyPlanWorkbenchTest')
            ->assertJsonPath('data.commands.1', 'php artisan test --filter RagStrategyPlanServiceTest')
            ->assertJsonPath('data.phases.0.label', 'Select chatbot context path')
            ->assertJsonPath('data.phases.1.label', 'Define context contract')
            ->assertJsonPath('data.phases.2.label', 'Implement context router')
            ->assertJsonPath('data.phases.2.tasks.0', 'Create a ChatbotContextRouter that selects RAG for broad or fresh corpora, Long Context for bounded document packs, CAG for stable FAQ or policy packs, and hybrid routing for mixed support flows.')
            ->assertJsonPath('data.phases.3.tasks.0', 'Add tests for CAG cache version, Long Context token budget, retrieval permissions, and hybrid route selection.');

        $this->getJson('/api/practice/technology-implementation-lab/ai-cloud-interview?language=en&search=candidate%20really%20uses%20AI&limit=3')
            ->assertOk()
            ->assertJsonPath('data.technology', 'ai-cloud-interview')
            ->assertJsonPath('data.practice.slug', 'ai-cloud-interview-rubric-workbench')
            ->assertJsonPath('data.related_workbench.path', '/workbench/ai-cloud-interview-rubric')
            ->assertJsonPath('data.commands.0', 'php artisan test --filter AiCloudInterviewRubricWorkbenchTest');

        $this->getJson('/api/practice/technology-implementation-lab/graphql-api?language=en&search=GraphQL%20vs%20REST&limit=3')
            ->assertOk()
            ->assertJsonPath('data.technology', 'graphql-api')
            ->assertJsonPath('data.practice.slug', 'graphql-rest-decision-workbench')
            ->assertJsonPath('data.related_workbench.path', '/workbench/graphql-rest-decision')
            ->assertJsonPath('data.commands.0', 'php artisan test --filter GraphqlRestDecisionWorkbenchTest');

        $this->getJson('/api/practice/technology-implementation-lab/react-render-performance?language=en&search=React%20re-renders&limit=3')
            ->assertOk()
            ->assertJsonPath('data.technology', 'react-render-performance')
            ->assertJsonPath('data.practice.slug', 'react-render-optimization-workbench')
            ->assertJsonPath('data.related_workbench.path', '/workbench/react-render-optimization-plan')
            ->assertJsonPath('data.commands.0', 'php artisan test --filter ReactRenderOptimizationPlan');

        $this->getJson('/api/practice/technology-implementation-lab/javascript-closures?language=en&search=JavaScript%20closure&limit=3')
            ->assertOk()
            ->assertJsonPath('data.technology', 'javascript-closures')
            ->assertJsonPath('data.practice.slug', 'react-render-optimization-workbench')
            ->assertJsonPath('data.related_workbench.path', '/workbench/react-render-optimization-plan')
            ->assertJsonPath('data.commands.0', 'php artisan test --filter ContentPracticeDrillTest')
            ->assertJsonPath('data.phases.0.label', 'Trace lexical scope')
            ->assertJsonPath('data.phases.1.label', 'Implement closure examples')
            ->assertJsonPath('data.phases.2.tasks.0', 'Compare var and let loop behavior through shared versus block-scoped bindings.');

        $this->getJson('/api/practice/technology-implementation-lab/javascript-closures?language=en&search=arrow%20function%20this&limit=3')
            ->assertOk()
            ->assertJsonPath('data.technology', 'javascript-closures')
            ->assertJsonPath('data.phases.0.label', 'Trace lexical this')
            ->assertJsonPath('data.phases.0.goal', 'Identify the scope where each arrow function is created and explain why it does not receive a new `this` at call time.')
            ->assertJsonPath('data.phases.1.label', 'Compare method calls')
            ->assertJsonPath('data.phases.2.tasks.0', 'Explain why obj.arrow() does not make `this` point to obj.')
            ->assertJsonPath('data.phases.3.label', 'Run arrow-this checks');

        $this->getJson('/api/practice/technology-implementation-lab/sql-injection-defense?language=en&search=SQL%20Injection&limit=3')
            ->assertOk()
            ->assertJsonPath('data.technology', 'sql-injection-defense')
            ->assertJsonPath('data.practice.slug', 'sql-injection-defense-plan-workbench')
            ->assertJsonPath('data.related_workbench.path', '/workbench/sql-injection-defense-plan')
            ->assertJsonPath('data.commands.0', 'php artisan test --filter SqlInjectionDefensePlan');

        $this->getJson('/api/practice/technology-implementation-lab/csrf-protection?language=en&search=CSRF&limit=3')
            ->assertOk()
            ->assertJsonPath('data.technology', 'csrf-protection')
            ->assertJsonPath('data.practice.slug', 'csrf-protection-plan-workbench')
            ->assertJsonPath('data.related_workbench.path', '/workbench/csrf-protection-plan')
            ->assertJsonPath('data.commands.0', 'php artisan test --filter CsrfProtectionPlan');

        $this->getJson('/api/practice/technology-implementation-lab/xss-defense?language=en&search=XSS&limit=3')
            ->assertOk()
            ->assertJsonPath('data.technology', 'xss-defense')
            ->assertJsonPath('data.practice.slug', 'blade-escaping-xss-preview')
            ->assertJsonPath('data.related_workbench.path', '/workbench/security-escape-preview')
            ->assertJsonPath('data.commands.0', 'php artisan test --filter SecurityEscapePreviewWorkbenchTest');

        $this->getJson('/api/practice/technology-implementation-lab/idor-access-control?language=en&search=IDOR&limit=3')
            ->assertOk()
            ->assertJsonPath('data.technology', 'idor-access-control')
            ->assertJsonPath('data.practice.slug', 'idor-access-review-workbench')
            ->assertJsonPath('data.related_workbench.path', '/workbench/idor-access-review')
            ->assertJsonPath('data.commands.0', 'php artisan test --filter IdorAccessReviewWorkbenchTest')
            ->assertJsonPath('data.phases.0.label', 'Inventory object routes')
            ->assertJsonPath('data.phases.1.label', 'Add scoped lookup and policy')
            ->assertJsonPath('data.phases.2.tasks.1', 'Assert read, update, delete, download, export, and nested child routes return 403 or 404 for the attacker.');

        $this->getJson('/api/practice/technology-implementation-lab/security-misconfiguration?language=en&search=Security%20Misconfiguration&limit=3')
            ->assertOk()
            ->assertJsonPath('data.technology', 'security-misconfiguration')
            ->assertJsonPath('data.practice.slug', 'authorization-policy-plan-workbench')
            ->assertJsonPath('data.related_workbench.path', '/practice/configuration-readiness')
            ->assertJsonPath('data.commands.0', 'php artisan test --filter ConfigurationReadiness')
            ->assertJsonPath('data.phases.0.label', 'Inventory runtime settings')
            ->assertJsonPath('data.phases.1.label', 'Build readiness checks')
            ->assertJsonPath('data.phases.2.tasks.1', 'Check CORS allowlists, security headers, session cookie flags, HTTPS enforcement, trusted proxies, and public storage visibility.');

        $this->getJson('/api/practice/technology-implementation-lab/auth-security?language=en&search=Broken%20Authentication&limit=3')
            ->assertOk()
            ->assertJsonPath('data.technology', 'auth-security')
            ->assertJsonPath('data.practice.slug', 'authorization-policy-plan-workbench')
            ->assertJsonPath('data.related_workbench.path', '/workbench/authorization-policy-plan')
            ->assertJsonPath('data.commands.0', 'php artisan test --filter BrokenAuthentication')
            ->assertJsonPath('data.phases.0.label', 'Map authentication lifecycle')
            ->assertJsonPath('data.phases.1.label', 'Harden session and reset flows')
            ->assertJsonPath('data.phases.2.tasks.1', 'Assert stale reset tokens, reused reset tokens, old session IDs, logged-out sessions, and revoked tokens stop working.')
            ->assertJsonPath('data.phases.3.label', 'Run authentication lifecycle checks');

        $this->getJson('/api/practice/technology-implementation-lab/database-eloquent?language=en&search=Covering%20Index&limit=3')
            ->assertOk()
            ->assertJsonPath('data.technology', 'database-eloquent')
            ->assertJsonPath('data.practice.slug', 'laravel-thin-controller')
            ->assertJsonPath('data.related_workbench.path', '/workbench/collection-filter-preview')
            ->assertJsonPath('data.commands.0', 'php artisan test --filter CoveringIndex')
            ->assertJsonPath('data.phases.0.label', 'Capture query-plan baseline')
            ->assertJsonPath('data.phases.1.label', 'Design the covering index')
            ->assertJsonPath('data.phases.2.tasks.0', 'Run EXPLAIN (ANALYZE, BUFFERS) before and after the index and compare Index Scan, Index Only Scan, Buffers, and Heap Fetches.')
            ->assertJsonPath('data.phases.3.tasks.2', 'php artisan migrate:status');

        $this->getJson('/api/practice/technology-implementation-lab/database-eloquent?language=en&search=lockForUpdate%20deadlock&limit=3')
            ->assertOk()
            ->assertJsonPath('data.technology', 'database-eloquent')
            ->assertJsonPath('data.related_workbench.path', '/workbench/database-locking-plan')
            ->assertJsonPath('data.commands.0', 'php artisan test --filter DatabaseLocking')
            ->assertJsonPath('data.phases.0.label', 'Map the concurrency hazard')
            ->assertJsonPath('data.phases.1.label', 'Add transaction-bound row locking')
            ->assertJsonPath('data.phases.2.tasks.1', 'Keep external HTTP calls, mail, file IO, and slow queue dispatch outside the locked transaction.')
            ->assertJsonPath('data.phases.3.tasks.3', 'php artisan migrate:status');

        $this->getJson('/api/practice/technology-implementation-lab/reverse-proxy-edge?language=en&search=Cloudflare%20reverse%20proxy&limit=3')
            ->assertOk()
            ->assertJsonPath('data.technology', 'reverse-proxy-edge')
            ->assertJsonPath('data.practice.slug', 'reverse-proxy-failure-plan-workbench')
            ->assertJsonPath('data.related_workbench.path', '/workbench/reverse-proxy-failure-plan')
            ->assertJsonPath('data.commands.0', 'php artisan test --filter ReverseProxyFailurePlanWorkbenchTest');

        $this->getJson('/api/practice/technology-implementation-lab/siem-elk-observability?language=en&search=SIEM&limit=3')
            ->assertOk()
            ->assertJsonPath('data.technology', 'siem-elk-observability')
            ->assertJsonPath('data.practice.slug', 'siem-elk-plan-workbench')
            ->assertJsonPath('data.related_workbench.path', '/workbench/siem-elk-plan')
            ->assertJsonPath('data.commands.0', 'php artisan test --filter SiemElkPlanWorkbenchTest');

        $this->getJson('/api/practice/technology-implementation-lab/system-design-tradeoffs?language=en&search=System%20Design%20tradeoffs&limit=3')
            ->assertOk()
            ->assertJsonPath('data.technology', 'system-design-tradeoffs')
            ->assertJsonPath('data.practice.slug', 'system-design-tradeoff-plan-workbench')
            ->assertJsonPath('data.related_workbench.path', '/workbench/system-design-tradeoff-plan')
            ->assertJsonPath('data.commands.0', 'php artisan test --filter SystemDesignTradeoffPlan');
    }

    /**
     * PHP stack and heap implementation labs use runtime-memory phases.
     */
    public function test_php_stack_heap_implementation_lab_uses_runtime_memory_phases(): void
    {
        $this->getJson('/api/practice/technology-implementation-lab/php?language=en&search=stack%20memory&limit=3')
            ->assertOk()
            ->assertJsonPath('data.technology', 'php')
            ->assertJsonPath('data.practice.slug', 'php-cli-input-normalizer')
            ->assertJsonPath('data.related_workbench.path', '/workbench/name-normalizer')
            ->assertJsonPath('data.phases.0.label', 'Trace call frames')
            ->assertJsonPath('data.phases.1.label', 'Model heap-backed data')
            ->assertJsonPath('data.phases.2.tasks.1', 'Add a short note about reference counting, garbage collection, and why PHP developers manage this indirectly.')
            ->assertJsonPath('data.phases.3.label', 'Run runtime-memory checks');
    }
}
