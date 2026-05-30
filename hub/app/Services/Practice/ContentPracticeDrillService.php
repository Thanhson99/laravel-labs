<?php

declare(strict_types=1);

namespace App\Services\Practice;

final class ContentPracticeDrillService
{
    /**
     * Create a drill service from mapped content practice items.
     */
    public function __construct(
        private readonly ContentPracticeMapperService $mapper,
        private readonly ContentPracticeStarterSnippetService $snippets,
        private readonly ContentPracticeWorkbenchLinkService $workbenches,
    ) {}

    /**
     * Build one focused practice drill from JSON content.
     *
     * @param  array{family?: string|null, language?: string|null, search?: string|null, technology?: string|null, source_key?: string|null, record_id?: string|null}  $filters
     * @return array<string, mixed>|null
     */
    public function build(array $filters): ?array
    {
        $result = $this->mapper->map($filters + ['limit' => 1]);
        $item = $result['items'][0] ?? null;

        if ($item === null) {
            return null;
        }

        return [
            'title' => sprintf('Content Drill: %s', $item['content']['title']),
            'technology' => $item['technology'],
            'source' => $item['source'],
            'content' => $item['content'],
            'practice' => $item['practice'],
            'goal' => $item['task'],
            'implementation_steps' => $this->stepsFor($item, $filters),
            'files' => $this->filesFor($item),
            'related_workbench' => $this->workbenches->linkForItem($item),
            'starter_snippets' => $this->snippets->snippetsFor($item),
            'commands' => [
                'php artisan test --filter ContentBackedDrill',
                'php artisan route:list --path=content-backed-drill',
                'php artisan test',
                'vendor\\bin\\pint --test',
            ],
            'acceptance' => $this->acceptanceFor($item, $filters),
        ];
    }

    /**
     * Return concrete coding steps for one technology.
     *
     * @return array<int, string>
     */
    private function stepsFor(array $item, array $filters = []): array
    {
        if ($this->isPkceRecord($item, $filters)) {
            return [
                'Generate a high-entropy code_verifier and derive an S256 code_challenge for one login attempt.',
                'Build the authorize request with response_type=code, state, redirect_uri, code_challenge, and code_challenge_method=S256.',
                'Validate callback state, reject token-bearing callbacks, exchange code plus code_verifier, then clear verifier state.',
                'Add tests for missing verifier, wrong verifier, reused authorization code, and state mismatch.',
            ];
        }

        if (PhpRuntimeMemoryTopicService::matchesRecord($item, $filters)) {
            return [
                'Trace the call stack: function call, parameters, local variables, return value, and frame cleanup.',
                'Identify heap-backed data such as arrays, objects, strings, object graphs, and references.',
                'Explain lifetime cleanup: stack frames unwind on return while heap data is released when references disappear or GC runs.',
                'Add a tiny PHP example with a function call, large array or object, unset(), and memory_get_usage() where useful.',
                'Name one practical failure mode: deep recursion, large arrays, retained references, or long-running worker memory.',
            ];
        }

        if (AiAgentMemoryTopicService::matchesRecord($item, $filters)) {
            return [
                'Classify memory into four separate contracts: working memory, episodic memory, semantic memory, and procedural memory.',
                'Define the data each memory type may store, who may read it, how long it lives, and how it is corrected when wrong.',
                'Map a developer-agent workflow that reads current task state, prior session decisions, durable repo knowledge, and repeatable playbooks before acting.',
                'Add guardrails for source, freshness, confidence, permission scope, private context, and stale-memory fallback.',
                'Use the LLM decision-loop workbench to connect memory, state, action, feedback, verification, and next-step planning.',
            ];
        }

        if (AiTypeComparisonTopicService::matchesRecord($item, $filters)) {
            return [
                'Define the output contract first: score, label, forecast, or ranking for Predictive AI; generated text, image, code, summary, or answer for Generative AI.',
                'Map the input path: historical structured data and labels for Predictive AI; prompt, context, retrieved evidence, and constraints for Generative AI.',
                'Choose separate evaluation evidence: precision, recall, calibration, AUC, error rate, or business lift for prediction; groundedness, safety, citations, tests, human review, and output constraints for generation.',
                'Name failure modes on both sides: overfitting, drift, and biased labels for Predictive AI; hallucination, fabricated citations, prompt injection, and unsafe code for Generative AI.',
                'Use the LLM decision-loop workbench to explain where probability, feedback, reward, and verification fit without mixing the two output contracts.',
            ];
        }

        if (DatabaseLockingTopicService::matchesRecord($item, $filters)) {
            return [
                'Name the protected invariant first: no negative stock, no double-spent balance, or no duplicate terminal workflow state.',
                'Wrap the critical read, validation, and write in `DB::transaction()` so they share one atomic boundary.',
                'Call `lockForUpdate()` on a selective, indexed row lookup before checking and mutating the protected value.',
                'Keep external API calls, emails, queue dispatch, sleeps, and slow computation outside the locked transaction.',
                'Add failure-path evidence for concurrent replay, insufficient state, deadlock retry, lock timeout, and hot-row contention.',
            ];
        }

        if (CoveringIndexTopicService::matchesRecord($item, $filters)) {
            return [
                'Capture the current query with selected columns, filters, ordering, limit, and realistic row count before changing indexes.',
                'Run `EXPLAIN (ANALYZE, BUFFERS)` and record whether the plan uses Index Scan, Index Only Scan, heap reads, and `Heap Fetches`.',
                'Design the covering index with key columns for filter/order and `INCLUDE` only for returned columns needed by the hot query.',
                'Check visibility-map health through VACUUM/autovacuum behavior before assuming Index Only Scan will avoid heap fetches.',
                'Measure index size, write overhead, bloat risk, and rollback path before shipping the migration.',
            ];
        }

        if ($this->isArrowThisRecord($item, $filters)) {
            return [
                'Mark the scope where the arrow function is created and identify which `this` value it captures.',
                'Compare a normal object method with an arrow object method to show why call-site `this` and lexical `this` differ.',
                'Add a timer or callback example where an arrow function intentionally keeps the outer `this`.',
                'Write the interview trap notes: obj.arrow() does not bind `this` to obj, and call/apply/bind cannot rebind arrow `this`.',
            ];
        }

        if ($this->isBrokenAuthenticationRecord($item, $filters)) {
            return [
                'Map the authentication lifecycle: login, session creation, remember-me, password reset, MFA recovery, logout, token refresh, and revocation.',
                'Identify one broken authentication risk in each boundary: weak session lifecycle, unsafe reset token, missing throttling, unsafe token storage, or missing suspicious-login logging.',
                'Add Laravel controls for the selected risk: session regeneration, RateLimiter, reset-token expiry, secure cookie flags, token rotation, or session invalidation.',
                'Write failure-path tests for brute force, stale reset token, old session reuse, missing logout invalidation, or revoked token reuse.',
                'Prepare a two-minute interview answer that separates authentication lifecycle from authorization decisions.',
            ];
        }

        return match ($item['technology']) {
            'api-validation' => [
                'Create or update a Form Request that validates the concept from the selected content record.',
                'Add a thin API controller method that returns a stable JSON response.',
                'Write one success API test and one 422 validation test.',
            ],
            'graphql-api' => [
                'Decide whether the selected use case should stay REST endpoint-oriented or move to GraphQL schema/query shape.',
                'Model the contract boundary: REST route/resource or GraphQL schema, resolver/service, validation, and authorization checks.',
                'Test overfetching or underfetching pressure, error shape, cache behavior, and N+1 resolver risk before implementation.',
            ],
            'javascript-closures' => [
                'Define the lexical scope that creates the closure and name the captured variables.',
                'Write a small function factory example such as createCounter() that proves the inner function keeps state.',
                'Explain one production use: event handler, callback, debounce, throttle, memoization, private state, or React hook closure.',
                'Add interview checks for var versus let in loops, stale closures, and why the variable binding stays reachable.',
            ],
            'react-render-performance' => [
                'Measure the render issue with React Profiler before adding memoization.',
                'Choose React.memo, useMemo, useCallback, state locality, or virtualization based on the measured cause.',
                'Verify dependencies and before/after render duration so optimization does not create stale UI.',
            ],
            'sql-injection-defense' => [
                'Identify where request input can influence SQL values or identifiers.',
                'Replace string-concatenated SQL with parameterized queries or query builder bindings.',
                'Add malicious payload tests and allowlist checks for dynamic order, column, or table choices.',
            ],
            'csrf-protection' => [
                'Identify the browser flow where a logged-in user can trigger a state-changing request.',
                'Add token validation, SameSite cookie review, and safe HTTP method boundaries for that flow.',
                'Write missing-token, stale-token, and cross-site request tests before accepting the defense.',
            ],
            'xss-defense' => [
                'Identify every output context where the selected content can reach the browser.',
                'Use escaped Blade output, safe JSON serialization, or sanitized rich text instead of raw HTML.',
                'Add reflected, stored, and DOM payload tests proving the payload renders as text and does not execute.',
            ],
            'auth-security' => [
                'Turn the selected security rule into a policy method or gate.',
                'Call authorization before the controller mutates or reveals protected data.',
                'Write one allowed test and one forbidden 403 test.',
            ],
            'database-eloquent' => [
                'Model the selected data concept with a migration or Eloquent relationship.',
                'Move reusable reads into a query or service method.',
                'Write a test that proves the stored shape or query result.',
            ],
            'files-media' => [
                'Validate file type, size, and visibility before storage.',
                'Store through a named disk and return only safe path metadata.',
                'Test success, validation failure, and cleanup expectations.',
            ],
            'async-workflow' => [
                'Name the event or job after a completed business action.',
                'Keep controller work synchronous and dispatch side effects after validation.',
                'Use Event::fake or Queue::fake in the focused test.',
            ],
            'performance-cache' => [
                'Create a scoped cache key for the selected read path.',
                'Keep the source query correct when the cache is empty.',
                'Test cache miss, cache hit, and invalidation ownership.',
            ],
            'container-architecture' => [
                'Extract a small contract for the selected dependency boundary.',
                'Bind the contract to an implementation in a service provider.',
                'Inject the contract into the controller or service that needs it.',
            ],
            'realtime-events' => [
                'Represent the selected realtime action as an event payload.',
                'Keep broadcast payloads small and safe for clients.',
                'Test dispatch behavior before wiring browser subscriptions.',
            ],
            'system-design-tradeoffs' => [
                'Ask clarifying questions before drawing an architecture.',
                'Compare at least two viable designs and state the cost of the chosen option.',
                'Use the System Design tradeoff workbench to generate L4, L5, L6, and L7 answer framing.',
            ],
            'load-balancing' => [
                'Choose the load-balancing algorithm that matches traffic shape and session needs.',
                'Document health-check behavior, sticky-session risk, and failure fallback.',
                'Test the planner output against at least two traffic scenarios.',
            ],
            'reverse-proxy-edge' => [
                'Map the request path from client to reverse proxy, edge module, load balancer, and origin server.',
                'Identify which proxy config, generated file, or hot-path module could make all healthy origins unreachable.',
                'Plan config validation, staged rollout, health gates, rollback, and blast-radius limits for the proxy layer.',
            ],
            'siem-elk-observability' => [
                'Inventory log sources and decide which security question the SIEM must answer first.',
                'Define normalized fields before building dashboards or detection rules.',
                'Plan parsing, retention, alert ownership, privacy controls, and an incident runbook for ELK.',
            ],
            'kubernetes-orchestration' => [
                'Map the content concept to the Kubernetes object that owns the responsibility.',
                'Describe rollout, probe, service, and ingress behavior before writing YAML.',
                'Test the plan with one stateless and one stateful workload scenario.',
            ],
            'jwt-token-storage' => [
                'Define the client type, XSS exposure, CSRF exposure, and token lifetime.',
                'Choose storage only after documenting refresh rotation and logout behavior.',
                'Test the recommendation against browser and non-browser API scenarios.',
            ],
            'jwt-revocation' => [
                'Pick a revocation model: denylist, token version, or short-lived access token.',
                'Plan middleware lookup, storage failure behavior, pruning, and observability.',
                'Test that old tokens stop working after logout, compromise, or account state changes.',
            ],
            'oauth-flow' => [
                'Identify whether the content flow uses Implicit Flow, Authorization Code with PKCE, confidential Authorization Code, or Client Credentials.',
                'Plan state validation, code exchange, service-token exchange, token storage, refresh rotation, scopes, audience, and client type.',
                'Test callback rejection, state mismatch, client authentication, audience/scope denial, and refresh-token reuse handling.',
            ],
            'lsm-tree-storage' => [
                'Map writes through WAL, memtable, flush, SSTable, and compaction.',
                'Map reads through memtable, Bloom filters, SSTables, and tombstones.',
                'Test the plan with write-heavy and read-heavy workload assumptions.',
            ],
            'llm-foundations' => [
                'Map the LLM loop as state, policy, action, transition, and reward.',
                'Explain how attention and token probabilities shape the next action.',
                'Use the LLM decision-loop workbench to separate mechanism, reward, PPO, and AGI claims.',
            ],
            'rag-systems' => [
                'Classify the selected topic as RAG, Long Context, CAG, or hybrid context routing before choosing a RAG pattern.',
                'Define the context contract: retrieval chunks, packed documents, cached context, metadata, source IDs, freshness, permissions, and citations.',
                'Use the RAG strategy workbench to connect retrieval quality, token budgets, cache freshness, groundedness, fallback behavior, and tool-loop controls.',
            ],
            'ai-cloud-interview' => [
                'Turn the interview question into a concrete AI usage story with task, prompt, output, edit, and result.',
                'List the verification evidence needed before trusting AI-generated Terraform, CloudFormation, IAM, or Kubernetes changes.',
                'Use the AI Cloud interview rubric workbench to score concrete task evidence, failure stories, and source-of-truth checks.',
            ],
            'blade-ui' => [
                'Prepare display data in a service or controller before rendering.',
                'Render escaped values in Blade and avoid business logic in the view.',
                'Write a feature test that checks the visible UI state.',
            ],
            'testing-quality' => [
                'Write a failing-first test that describes the selected content concept.',
                'Implement the smallest service or controller change that makes the test pass.',
                'Run Pint and refactor only after the behavior is protected.',
            ],
            'docker-runtime' => [
                'Turn the selected runtime concept into a smoke-check item.',
                'Expose the check through a service-backed API response.',
                'Document the command that proves the runtime path works.',
            ],
            'php' => [
                'Write a typed PHP service method for the selected concept.',
                'Cover empty or invalid input with a guard clause.',
                'Add a unit test for normal and edge-case input.',
            ],
            default => [
                'Create a named route for the selected concept.',
                'Keep controller logic thin and move workflow logic into a service.',
                'Render escaped output in Blade and cover the page with a feature test.',
            ],
        };
    }

    /**
     * Return done criteria for one drill, with concept-specific security checks.
     *
     * @param  array<string, mixed>  $item
     * @return array<int, string>
     */
    private function acceptanceFor(array $item, array $filters = []): array
    {
        $acceptance = [
            'The drill references the exact JSON source file.',
            'The implementation changes Laravel code, not the content file.',
            'A focused test proves the behavior.',
            'The route, request, controller, service, and view responsibilities stay separated.',
            'Full test suite and Pint pass.',
        ];

        if ($this->isPkceRecord($item, $filters)) {
            return [
                ...$acceptance,
                'The PKCE verifier is never logged, rendered, or persisted beyond the login attempt.',
                'The token exchange fails when code_verifier is missing, wrong, expired, or reused.',
                'The callback rejects access_token, id_token, token_type, or expires_in in URL input.',
            ];
        }

        if (PhpRuntimeMemoryTopicService::matchesRecord($item, $filters)) {
            return [
                ...$acceptance,
                'The answer does not claim PHP developers manually allocate stack or heap memory in normal application code.',
                'The explanation distinguishes stack call frames from heap-backed array, object, and string lifetime.',
                'The drill names at least one PHP memory risk: deep recursion, large arrays, retained references, or long-running worker state.',
            ];
        }

        if (AiAgentMemoryTopicService::matchesRecord($item, $filters)) {
            return [
                ...$acceptance,
                'The answer separates working, episodic, semantic, and procedural memory instead of treating memory as one prompt bucket.',
                'The plan names scope, lifetime, source, freshness, confidence, permission, and correction rules for stored memory.',
                'The drill explains how stale or private memory is prevented from silently steering the next agent action.',
            ];
        }

        if (AiTypeComparisonTopicService::matchesRecord($item, $filters)) {
            return [
                ...$acceptance,
                'The answer separates Predictive AI output contracts from Generative AI output contracts.',
                'The evaluation plan names at least two predictive metrics and two generative quality checks.',
                'The drill names failure modes for both sides instead of treating all AI risk as hallucination.',
            ];
        }

        if (DatabaseLockingTopicService::matchesRecord($item, $filters)) {
            return [
                ...$acceptance,
                'The drill states the exact invariant being protected before introducing `lockForUpdate()`.',
                '`lockForUpdate()` runs inside `DB::transaction()` and before the protected row is checked or changed.',
                'The plan documents indexed lookup, short lock window, deadlock or timeout behavior, hot-row contention, and lock-wait monitoring.',
            ];
        }

        if (CoveringIndexTopicService::matchesRecord($item, $filters)) {
            return [
                ...$acceptance,
                'The plan proves whether `Heap Fetches` dropped after the covering index.',
                'The `INCLUDE` columns are limited to fields returned by the hot query, not every convenient column.',
                'The release notes mention visibility map, VACUUM/autovacuum health, index bloat, write overhead, and rollback.',
            ];
        }

        if ($this->isArrowThisRecord($item, $filters)) {
            return [
                ...$acceptance,
                'The comparison shows normal function `this` comes from the call site while arrow `this` comes from lexical scope.',
                'The object-method trap explains why obj.arrow() does not make `this` point to obj.',
                'The answer states that call(), apply(), and bind() cannot rebind arrow `this`.',
            ];
        }

        if ($this->isBrokenAuthenticationRecord($item, $filters)) {
            return [
                ...$acceptance,
                'The drill reviews authentication as a lifecycle, not only a successful login form.',
                'The implementation proves session regeneration, throttling, reset-token expiry, token revocation, or logout invalidation with a focused test.',
                'The answer clearly separates authentication from authorization and names one logging or monitoring signal for suspicious authentication behavior.',
            ];
        }

        return $acceptance;
    }

    /**
     * Determine whether the mapped record is specifically about PKCE.
     *
     * @param  array<string, mixed>  $item
     */
    private function isPkceRecord(array $item, array $filters = []): bool
    {
        $haystack = strtolower(implode(' ', [
            $filters['search'] ?? '',
            $item['content']['title'] ?? '',
            $item['content']['group'] ?? '',
            $item['task'] ?? '',
        ]));

        return str_contains($haystack, 'pkce')
            || str_contains($haystack, 'code_verifier')
            || str_contains($haystack, 'code challenge');
    }

    /**
     * Determine whether the mapped JavaScript record is about arrow-function `this`.
     *
     * @param  array<string, mixed>  $item
     */
    private function isArrowThisRecord(array $item, array $filters = []): bool
    {
        if (($item['technology'] ?? null) !== 'javascript-closures') {
            return false;
        }

        $haystack = strtolower(implode(' ', [
            $filters['search'] ?? '',
            $item['content']['title'] ?? '',
            $item['content']['body'] ?? '',
            $item['task'] ?? '',
        ]));

        return str_contains($haystack, 'arrow')
            && (str_contains($haystack, 'this') || str_contains($haystack, 'lexical'));
    }

    /**
     * Determine whether the mapped auth-security record is about broken authentication.
     *
     * @param  array<string, mixed>  $item
     */
    private function isBrokenAuthenticationRecord(array $item, array $filters = []): bool
    {
        if (($item['technology'] ?? null) !== 'auth-security') {
            return false;
        }

        $haystack = strtolower(implode(' ', [
            $filters['search'] ?? '',
            $item['content']['title'] ?? '',
            $item['content']['body'] ?? '',
            $item['task'] ?? '',
        ]));

        return str_contains($haystack, 'broken authentication')
            || str_contains($haystack, 'authentication mistakes')
            || str_contains($haystack, 'session fixation')
            || str_contains($haystack, 'reset token')
            || str_contains($haystack, 'brute force');
    }

    /**
     * Return suggested files for one mapped item.
     *
     * @return array<int, string>
     */
    private function filesFor(array $item): array
    {
        if ($this->isArrowThisRecord($item)) {
            return [
                'resources/js/arrow-this-comparison.js',
                'resources/js/arrow-this-interview-checklist.js',
                'tests/Feature/JavaScriptArrowThisInterviewTest.js',
            ];
        }

        if ($this->isBrokenAuthenticationRecord($item)) {
            return [
                'app/Services/Practice/BrokenAuthenticationReviewService.php',
                'app/Http/Requests/Auth/LoginRequest.php',
                'config/session.php',
                'tests/Feature/Auth/BrokenAuthenticationLifecycleTest.php',
            ];
        }

        if (($item['technology'] ?? null) === 'llm-foundations' && AiAgentMemoryTopicService::matchesRecord($item)) {
            return [
                'app/Services/Practice/AiAgentMemoryPlanService.php',
                'app/Http/Requests/Api/PlanAiAgentMemoryRequest.php',
                'app/Http/Controllers/Api/AiAgentMemoryPlanController.php',
                'tests/Feature/AiAgentMemoryPlanTest.php',
                'resources/views/practice/workbench/ai-agent-memory-plan.blade.php',
                'tests/Feature/AiAgentMemoryPlanWorkbenchTest.php',
            ];
        }

        return match ($item['technology']) {
            'api-validation' => [
                'routes/api.php',
                'app/Http/Requests/Api',
                'app/Http/Controllers/Api',
                'tests/Feature',
            ],
            'graphql-api' => [
                'app/Services/Practice/GraphqlRestDecisionService.php',
                'app/Http/Requests/Api/PlanGraphqlRestDecisionRequest.php',
                'resources/views/practice/workbench/graphql-rest-decision.blade.php',
                'tests/Feature/GraphqlRestDecisionWorkbenchTest.php',
            ],
            'javascript-closures' => [
                'resources/js/closure-counter.js',
                'resources/js/closure-interview-checklist.js',
                'tests/Feature/JavaScriptClosureInterviewTest.js',
            ],
            'react-render-performance' => [
                'app/Services/Practice/ReactRenderOptimizationPlanService.php',
                'app/Http/Requests/Api/PlanReactRenderOptimizationRequest.php',
                'resources/views/practice/workbench/react-render-optimization-plan.blade.php',
                'tests/Feature/ReactRenderOptimizationPlanWorkbenchTest.php',
            ],
            'sql-injection-defense' => [
                'app/Services/Practice/SqlInjectionDefensePlanService.php',
                'app/Http/Requests/Api/PlanSqlInjectionDefenseRequest.php',
                'resources/views/practice/workbench/sql-injection-defense-plan.blade.php',
                'tests/Feature/SqlInjectionDefensePlanWorkbenchTest.php',
            ],
            'csrf-protection' => [
                'app/Services/Practice/CsrfProtectionPlanService.php',
                'app/Http/Requests/Api/PlanCsrfProtectionRequest.php',
                'resources/views/practice/workbench/csrf-protection-plan.blade.php',
                'tests/Feature/CsrfProtectionPlanWorkbenchTest.php',
            ],
            'xss-defense' => [
                'app/Services/Practice/SecurityEscapePreviewService.php',
                'app/Http/Requests/Api/PreviewEscapedContentRequest.php',
                'resources/views/practice/workbench/security-escape-preview.blade.php',
                'tests/Feature/SecurityEscapePreviewWorkbenchTest.php',
            ],
            'auth-security' => [
                'app/Policies',
                'app/Providers/AuthServiceProvider.php',
                'app/Http/Controllers',
                'tests/Feature',
            ],
            'database-eloquent' => [
                'database/migrations',
                'app/Models',
                'app/Services/Practice',
                'tests/Feature',
            ],
            'files-media' => [
                'app/Http/Requests/Api',
                'app/Services/Practice',
                'config/filesystems.php',
                'tests/Feature',
            ],
            'async-workflow' => [
                'app/Events',
                'app/Listeners',
                'app/Jobs',
                'tests/Feature',
            ],
            'performance-cache' => [
                'app/Services/Practice',
                'config/cache.php',
                'tests/Unit/Practice',
                'tests/Feature',
            ],
            'container-architecture' => [
                'app/Contracts',
                'app/Services',
                'app/Providers/AppServiceProvider.php',
                'tests/Unit/Practice',
            ],
            'realtime-events' => [
                'app/Events',
                'routes/channels.php',
                'config/broadcasting.php',
                'tests/Feature',
            ],
            'system-design-tradeoffs' => [
                'app/Services/Practice/SystemDesignTradeoffPlanService.php',
                'app/Http/Requests/Api/PlanSystemDesignTradeoffRequest.php',
                'resources/views/practice/workbench/system-design-tradeoff-plan.blade.php',
                'tests/Feature/SystemDesignTradeoffPlanWorkbenchTest.php',
            ],
            'load-balancing' => [
                'app/Services/Practice/LoadBalancerPlanService.php',
                'app/Http/Requests/Api/PlanLoadBalancerRequest.php',
                'resources/views/practice/workbench/load-balancer-plan.blade.php',
                'tests/Feature/LoadBalancerPlanWorkbenchTest.php',
            ],
            'reverse-proxy-edge' => [
                'app/Services/Practice/ReverseProxyFailurePlanService.php',
                'app/Http/Requests/Api/PlanReverseProxyFailureRequest.php',
                'resources/views/practice/workbench/reverse-proxy-failure-plan.blade.php',
                'tests/Feature/ReverseProxyFailurePlanWorkbenchTest.php',
            ],
            'siem-elk-observability' => [
                'app/Services/Practice/SiemElkPlanService.php',
                'app/Http/Requests/Api/PlanSiemElkRequest.php',
                'resources/views/practice/workbench/siem-elk-plan.blade.php',
                'tests/Feature/SiemElkPlanWorkbenchTest.php',
            ],
            'kubernetes-orchestration' => [
                'app/Services/Practice/KubernetesAnalogyPlanService.php',
                'app/Http/Requests/Api/PlanKubernetesAnalogyRequest.php',
                'resources/views/practice/workbench/kubernetes-analogy-plan.blade.php',
                'tests/Feature/KubernetesAnalogyPlanWorkbenchTest.php',
            ],
            'jwt-token-storage' => [
                'app/Services/Practice/JwtTokenStoragePlanService.php',
                'app/Http/Requests/Api/PlanJwtTokenStorageRequest.php',
                'resources/views/practice/workbench/jwt-token-storage-plan.blade.php',
                'tests/Feature/JwtTokenStoragePlanWorkbenchTest.php',
            ],
            'jwt-revocation' => [
                'app/Services/Practice/JwtRevocationPlanService.php',
                'app/Http/Requests/Api/PlanJwtRevocationRequest.php',
                'resources/views/practice/workbench/jwt-revocation-plan.blade.php',
                'tests/Feature/JwtRevocationPlanWorkbenchTest.php',
            ],
            'oauth-flow' => [
                'app/Services/Practice/OauthFlowPlanService.php',
                'app/Http/Requests/Api/PlanOauthFlowRequest.php',
                'resources/views/practice/workbench/oauth-flow-plan.blade.php',
                'tests/Feature/OauthFlowPlanWorkbenchTest.php',
            ],
            'graph-traversal' => [
                'app/Services/Practice/GraphTraversalPlanService.php',
                'app/Http/Requests/Api/PlanGraphTraversalRequest.php',
                'resources/views/practice/workbench/graph-traversal-plan.blade.php',
                'tests/Feature/GraphTraversalPlanWorkbenchTest.php',
                'tests/Unit/Practice/GraphTraversalPlanServiceTest.php',
            ],
            'lsm-tree-storage' => [
                'app/Services/Practice/LsmTreePlanService.php',
                'app/Http/Requests/Api/PlanLsmTreeRequest.php',
                'resources/views/practice/workbench/lsm-tree-plan.blade.php',
                'tests/Feature/LsmTreePlanWorkbenchTest.php',
            ],
            'llm-foundations' => [
                'app/Services/Practice/LlmDecisionLoopPlanService.php',
                'app/Http/Requests/Api/PlanLlmDecisionLoopRequest.php',
                'resources/views/practice/workbench/llm-decision-loop-plan.blade.php',
                'tests/Feature/LlmDecisionLoopPlanWorkbenchTest.php',
            ],
            'rag-systems' => [
                'app/Services/Practice/RagStrategyPlanService.php',
                'app/Http/Requests/Api/PlanRagStrategyRequest.php',
                'app/Services/Rag/ChatbotContextRouter.php',
                'resources/views/practice/workbench/rag-strategy-plan.blade.php',
                'tests/Feature/RagStrategyPlanWorkbenchTest.php',
            ],
            'ai-cloud-interview' => [
                'app/Services/Practice/AiCloudInterviewRubricService.php',
                'app/Http/Requests/Api/ScoreAiCloudInterviewRubricRequest.php',
                'resources/views/practice/workbench/ai-cloud-interview-rubric.blade.php',
                'tests/Feature/AiCloudInterviewRubricWorkbenchTest.php',
            ],
            'blade-ui' => [
                'app/Http/Controllers/Practice',
                'app/Services/Practice',
                'resources/views/practice',
                'tests/Feature',
            ],
            'testing-quality' => [
                'app/Services/Practice',
                'tests/Unit/Practice',
                'tests/Feature',
            ],
            'docker-runtime' => [
                'Dockerfile',
                'docker-compose.yml',
                '.env.example',
                'app/Services/Practice/RuntimeSmokeCheckService.php',
            ],
            'php' => [
                'app/Practice/Php',
                'tests/Unit/Practice',
            ],
            default => [
                'routes/web.php',
                'app/Http/Controllers/Practice',
                'app/Services/Practice',
                'resources/views/practice',
                'tests/Feature',
            ],
        };
    }
}
