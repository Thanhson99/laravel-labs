<?php

declare(strict_types=1);

namespace App\Services\Practice;

final class TechnologyImplementationLabService
{
    /**
     * Create implementation labs from technology-specific code examples.
     */
    public function __construct(
        private readonly TechnologyCodeExampleService $examples,
        private readonly ContentPracticeMapperService $mapper,
        private readonly ContentPracticeWorkbenchLinkService $workbenches,
        private readonly PracticeProgressPayloadService $progressPayload,
    ) {}

    /**
     * Build a sequential implementation lab for one inferred technology.
     *
     * @param  array{family?: string|null, language?: string|null, search?: string|null, limit?: int|string|null}  $filters
     * @return array<string, mixed>
     */
    public function build(string $technology, array $filters = []): array
    {
        $examples = $this->examples->buildForTechnology($technology, $filters + ['limit' => 5]);
        $resolvedFilters = $examples['meta']['filters'];
        $tasks = collect($examples['items'])
            ->take(5)
            ->map(fn (array $item, int $index): array => $this->taskFromExample($item, $index + 1))
            ->values()
            ->all();

        $phases = $this->phasesFor($technology, $tasks, $resolvedFilters);
        $isAgentMemoryLab = $technology === 'llm-foundations' && AiAgentMemoryTopicService::matchesTasks($tasks, $resolvedFilters);
        $relatedWorkbench = $isAgentMemoryLab
            ? $this->workbenches->agentMemoryLink()
            : $this->workbenches->linkForTechnologyContext($technology, (string) ($resolvedFilters['search'] ?? ''));

        return [
            'title' => sprintf('Technology Implementation Lab: %s', $technology),
            'technology' => $technology,
            'practice' => $this->mapper->practiceSummaryFor($technology),
            'related_workbench' => $relatedWorkbench,
            'source_examples' => $examples,
            'phases' => $phases,
            'commands' => $isAgentMemoryLab ? [
                'php artisan test --filter AiAgentMemoryPlanWorkbenchTest',
                'php artisan test --filter AiAgentMemoryPlanServiceTest',
                'php artisan route:list --path=ai-agent-memory-plan',
            ] : $this->commandsFor($technology, $resolvedFilters),
            'progress_payload' => $this->progressPayload->fromRows(
                $phases,
                fn (array $phase): string => $phase['label'],
            ),
            'progress_api' => '/api/practice/progress-checklist',
            'next_actions' => $this->nextActionsFor($technology, $resolvedFilters),
            'meta' => [
                'filters' => $resolvedFilters,
                'task_count' => count($tasks),
            ],
        ];
    }

    /**
     * Convert one code example into an implementation task.
     *
     * @return array<string, mixed>
     */
    private function taskFromExample(array $item, int $step): array
    {
        return [
            'step' => $step,
            'record_id' => $item['record_id'],
            'title' => $item['content']['title'],
            'source_path' => $item['source']['path'],
            'task' => $item['task'],
            'workspace_query' => $item['workspace_query'],
            'files' => collect($item['snippets'])
                ->pluck('file')
                ->unique()
                ->values()
                ->all(),
        ];
    }

    /**
     * Build lab phases for the selected technology.
     *
     * @param  array<int, array<string, mixed>>  $tasks
     * @return array<int, array<string, mixed>>
     */
    private function phasesFor(string $technology, array $tasks, array $filters = []): array
    {
        if ($technology === 'php' && PhpRuntimeMemoryTopicService::matchesTasks($tasks, $filters)) {
            return [
                [
                    'label' => 'Trace call frames',
                    'goal' => 'Explain the stack side of the example through function calls, parameters, locals, return values, and frame cleanup.',
                    'tasks' => collect($tasks)
                        ->map(fn (array $task): string => sprintf('Read %s from %s and mark the active function frame for step %d.', $task['title'], $task['source_path'], $task['step']))
                        ->all(),
                ],
                [
                    'label' => 'Model heap-backed data',
                    'goal' => 'Show which arrays, objects, strings, references, or object graphs create memory pressure beyond one call frame.',
                    'tasks' => collect($tasks)
                        ->map(fn (array $task): string => sprintf('Implement a tiny PHP example using: %s.', implode(', ', $task['files'])))
                        ->all(),
                ],
                [
                    'label' => 'Observe cleanup behavior',
                    'goal' => 'Use scope exit, unset(), and optional memory_get_usage() notes without claiming manual heap allocation.',
                    'tasks' => [
                        'Compare a value that disappears when the function returns with heap-backed data that remains reachable through references.',
                        'Add a short note about reference counting, garbage collection, and why PHP developers manage this indirectly.',
                        'Document one request/response lifecycle case and one long-running worker memory-risk case.',
                    ],
                ],
                [
                    'label' => 'Run runtime-memory checks',
                    'goal' => 'Prove the PHP explanation is concrete enough to teach and defend.',
                    'tasks' => $this->commandsFor($technology, $filters),
                ],
            ];
        }

        if ($technology === 'oauth-flow') {
            return [
                [
                    'label' => 'Classify OAuth client and flow',
                    'goal' => 'Choose the OAuth flow from client type, secret-storage ability, and user delegation needs.',
                    'tasks' => collect($tasks)
                        ->map(fn (array $task): string => sprintf('Read %s from %s and decide whether it is public-client PKCE, confidential client, or machine-to-machine work.', $task['title'], $task['source_path']))
                        ->all(),
                ],
                [
                    'label' => 'Build PKCE authorize request',
                    'goal' => 'Generate a private verifier, derive S256 challenge, and send only the challenge to authorization.',
                    'tasks' => collect($tasks)
                        ->map(fn (array $task): string => sprintf('Implement step %d with verifier storage and authorize URL work in: %s.', $task['step'], implode(', ', $task['files'])))
                        ->all(),
                ],
                [
                    'label' => 'Validate callback and token exchange',
                    'goal' => 'Reject bad callback input before exchanging the authorization code with the verifier.',
                    'tasks' => [
                        'Validate state, redirect URI, authorization code presence, and unexpected token fields in callback input.',
                        'Exchange the code with code_verifier only at the token endpoint and clear the verifier after use.',
                        'Document token audience, scope, lifetime, refresh rotation, storage boundary, and log redaction.',
                    ],
                ],
                [
                    'label' => 'Run PKCE failure checks',
                    'goal' => 'Prove PKCE fails closed for verifier and callback mistakes.',
                    'tasks' => $this->commandsFor($technology, $filters),
                ],
            ];
        }

        if ($technology === 'graph-traversal') {
            return [
                [
                    'label' => 'Classify traversal goal',
                    'goal' => 'Choose BFS or DFS from nearest result, shortest unweighted path, branch exploration, dependency reasoning, or subtree validation.',
                    'tasks' => collect($tasks)
                        ->map(fn (array $task): string => sprintf('Read %s from %s and mark whether the goal is nearest-hop BFS work or depth-first DFS work.', $task['title'], $task['source_path']))
                        ->all(),
                ],
                [
                    'label' => 'Implement traversal state',
                    'goal' => 'Build a small fixture that proves BFS queue order and DFS stack or recursion order.',
                    'tasks' => collect($tasks)
                        ->map(fn (array $task): string => sprintf('Implement step %d with graph fixture, BFS order, DFS order, and visited-set behavior in: %s.', $task['step'], implode(', ', $task['files'])))
                        ->all(),
                ],
                [
                    'label' => 'Add cycle and system guardrails',
                    'goal' => 'Make traversal safe enough for API crawling and database hierarchy reads.',
                    'tasks' => [
                        'Add a cyclic graph case and prove visited-set handling prevents repeated work.',
                        'Document max depth, max nodes, fan-out limits, batching, pagination, timeout, rate limits, and memory pressure.',
                        'Map BFS to one nearest-hop API crawling case and DFS to one database subtree validation or nested menu case.',
                    ],
                ],
                [
                    'label' => 'Run traversal checks',
                    'goal' => 'Prove the BFS/DFS explanation is code-backed, bounded, and interview-ready.',
                    'tasks' => $this->commandsFor($technology, $filters),
                ],
            ];
        }

        if ($technology === 'javascript-closures' && $this->isArrowThisTopic($tasks, $filters)) {
            return [
                [
                    'label' => 'Trace lexical this',
                    'goal' => 'Identify the scope where each arrow function is created and explain why it does not receive a new `this` at call time.',
                    'tasks' => collect($tasks)
                        ->map(fn (array $task): string => sprintf('Read %s from %s and mark which examples use lexical `this` versus call-site `this`.', $task['title'], $task['source_path']))
                        ->all(),
                ],
                [
                    'label' => 'Compare method calls',
                    'goal' => 'Use paired examples to show normal function dynamic `this` and arrow function lexical `this` side by side.',
                    'tasks' => collect($tasks)
                        ->map(fn (array $task): string => sprintf('Implement step %d with normalMethod(), arrowMethod(), timer callback, and class-style callback examples in: %s.', $task['step'], implode(', ', $task['files'])))
                        ->all(),
                ],
                [
                    'label' => 'Review this-binding traps',
                    'goal' => 'Make the interview traps explicit before turning the answer into reusable guidance.',
                    'tasks' => [
                        'Explain why obj.arrow() does not make `this` point to obj.',
                        'Show why call(), apply(), and bind() can change a normal function but cannot rebind arrow `this`.',
                        'Name when arrow callbacks are useful: timers, event handlers, array callbacks, and class code that must keep outer `this`.',
                    ],
                ],
                [
                    'label' => 'Run arrow-this checks',
                    'goal' => 'Prove the arrow-function `this` comparison is code-backed, searchable, and interview-ready.',
                    'tasks' => $this->commandsFor($technology, $filters),
                ],
            ];
        }

        if ($technology === 'javascript-closures') {
            return [
                [
                    'label' => 'Trace lexical scope',
                    'goal' => 'Identify the outer scope, inner function, and captured variable bindings that create the closure.',
                    'tasks' => collect($tasks)
                        ->map(fn (array $task): string => sprintf('Read %s from %s and mark the outer variables captured by the closure.', $task['title'], $task['source_path']))
                        ->all(),
                ],
                [
                    'label' => 'Implement closure examples',
                    'goal' => 'Use small JavaScript examples to prove state persists through captured bindings.',
                    'tasks' => collect($tasks)
                        ->map(fn (array $task): string => sprintf('Implement step %d with createCounter(), private state, or a function factory in: %s.', $task['step'], implode(', ', $task['files'])))
                        ->all(),
                ],
                [
                    'label' => 'Review interview traps',
                    'goal' => 'Explain the common failure cases that make closure answers incomplete.',
                    'tasks' => [
                        'Compare var and let loop behavior through shared versus block-scoped bindings.',
                        'Write one stale-closure note for async callbacks, timers, or React hook dependencies.',
                        'Name one memory-retention risk where a closure keeps a large object reachable longer than expected.',
                    ],
                ],
                [
                    'label' => 'Run closure checks',
                    'goal' => 'Prove the closure explanation is searchable, code-backed, and interview-ready.',
                    'tasks' => $this->commandsFor($technology, $filters),
                ],
            ];
        }

        if ($technology === 'auth-security' && $this->isBrokenAuthenticationTopic($tasks, $filters)) {
            return [
                [
                    'label' => 'Map authentication lifecycle',
                    'goal' => 'Review identity proof across login, session creation, remember-me, password reset, MFA recovery, logout, token refresh, and revocation.',
                    'tasks' => collect($tasks)
                        ->map(fn (array $task): string => sprintf('Read %s from %s and mark every authentication lifecycle boundary mentioned by the record.', $task['title'], $task['source_path']))
                        ->all(),
                ],
                [
                    'label' => 'Harden session and reset flows',
                    'goal' => 'Turn broken-authentication risks into Laravel controls instead of treating login success as the whole feature.',
                    'tasks' => collect($tasks)
                        ->map(fn (array $task): string => sprintf('Implement step %d with throttling, session regeneration, reset-token expiry, token revocation, and logout invalidation notes in: %s.', $task['step'], implode(', ', $task['files'])))
                        ->all(),
                ],
                [
                    'label' => 'Prove failure paths',
                    'goal' => 'Write tests for the paths attackers use after the happy login path has already passed.',
                    'tasks' => [
                        'Assert brute force throttling blocks repeated failed login attempts.',
                        'Assert stale reset tokens, reused reset tokens, old session IDs, logged-out sessions, and revoked tokens stop working.',
                        'Document suspicious-login logging fields without storing passwords, reset tokens, session IDs, or bearer tokens.',
                    ],
                ],
                [
                    'label' => 'Run authentication lifecycle checks',
                    'goal' => 'Prove the Broken Authentication practice is code-backed, testable, and interview-ready.',
                    'tasks' => $this->commandsFor($technology, $filters),
                ],
            ];
        }

        if ($technology === 'security-misconfiguration') {
            return [
                [
                    'label' => 'Inventory runtime settings',
                    'goal' => 'Identify environment flags and deployment settings that can weaken production security.',
                    'tasks' => collect($tasks)
                        ->map(fn (array $task): string => sprintf('Read %s from %s and mark every debug, secret, storage, CORS, header, proxy, or cookie setting mentioned.', $task['title'], $task['source_path']))
                        ->all(),
                ],
                [
                    'label' => 'Build readiness checks',
                    'goal' => 'Turn unsafe defaults into explicit production readiness rules.',
                    'tasks' => collect($tasks)
                        ->map(fn (array $task): string => sprintf('Implement step %d as a configuration readiness check using: %s.', $task['step'], implode(', ', $task['files'])))
                        ->all(),
                ],
                [
                    'label' => 'Prove fail-closed behavior',
                    'goal' => 'Show that unsafe configuration blocks release instead of only creating a checklist item.',
                    'tasks' => [
                        'Reject APP_DEBUG=true, local APP_ENV, exposed .env files, and verbose exception output for production.',
                        'Check CORS allowlists, security headers, session cookie flags, HTTPS enforcement, trusted proxies, and public storage visibility.',
                        'Document the expected value, owner, rollback action, and smoke-test evidence for every environment-specific setting.',
                    ],
                ],
                [
                    'label' => 'Run configuration readiness checks',
                    'goal' => 'Prove the Security Misconfiguration practice is runnable and release-oriented.',
                    'tasks' => $this->commandsFor($technology, $filters),
                ],
            ];
        }

        if ($technology === 'idor-access-control') {
            return [
                [
                    'label' => 'Inventory object routes',
                    'goal' => 'Find every route where a user-controlled ID can point at a model, nested resource, download, export, batch action, or signed URL.',
                    'tasks' => collect($tasks)
                        ->map(fn (array $task): string => sprintf('Read %s from %s and list each object ID, parent resource, access model, and response surface.', $task['title'], $task['source_path']))
                        ->all(),
                ],
                [
                    'label' => 'Add scoped lookup and policy',
                    'goal' => 'Move the protection into backend object-level authorization instead of relying on hidden UI or unpredictable IDs.',
                    'tasks' => collect($tasks)
                        ->map(fn (array $task): string => sprintf('Implement step %d with scoped query, policy or Gate check, and denial response in: %s.', $task['step'], implode(', ', $task['files'])))
                        ->all(),
                ],
                [
                    'label' => 'Prove attacker ID swaps fail',
                    'goal' => 'Write tests that prove another user, tenant, or team cannot access the object by changing only an ID.',
                    'tasks' => [
                        'Create owner, attacker, and optional second-tenant fixtures.',
                        'Assert read, update, delete, download, export, and nested child routes return 403 or 404 for the attacker.',
                        'Document why the route uses 403 or 404 and whether object existence should be hidden.',
                    ],
                ],
                [
                    'label' => 'Run IDOR review checks',
                    'goal' => 'Prove the IDOR fix is code-backed, replayable, and review-ready.',
                    'tasks' => $this->commandsFor($technology, $filters),
                ],
            ];
        }

        if ($technology === 'database-eloquent' && DatabaseLockingTopicService::matchesTasks($tasks, $filters)) {
            return [
                [
                    'label' => 'Map the concurrency hazard',
                    'goal' => 'Identify the exact row, business invariant, and stale-read risk before adding locks.',
                    'tasks' => collect($tasks)
                        ->map(fn (array $task): string => sprintf('Read %s from %s and name the race condition step %d must prevent.', $task['title'], $task['source_path'], $task['step']))
                        ->all(),
                ],
                [
                    'label' => 'Add transaction-bound row locking',
                    'goal' => 'Protect the smallest useful row set with lockForUpdate inside a short DB transaction.',
                    'tasks' => collect($tasks)
                        ->map(fn (array $task): string => sprintf('Implement step %d with DB::transaction(), lockForUpdate(), state validation, and write evidence in: %s.', $task['step'], implode(', ', $task['files'])))
                        ->all(),
                ],
                [
                    'label' => 'Review deadlock and contention risk',
                    'goal' => 'Make the lock safe for production traffic instead of only correct in a single request.',
                    'tasks' => [
                        'Lock rows in a consistent order when the flow touches more than one product, account, order, or payout.',
                        'Keep external HTTP calls, mail, file IO, and slow queue dispatch outside the locked transaction.',
                        'Confirm the lock query uses a selective index so it does not lock more rows than intended.',
                    ],
                ],
                [
                    'label' => 'Run database-locking checks',
                    'goal' => 'Prove the concurrency fix prevents invalid state and documents the operational cost.',
                    'tasks' => [
                        'Add tests or a manual replay note for two concurrent requests targeting the same stock, balance, or workflow row.',
                        'php artisan test --filter DatabaseLocking',
                        'php artisan test --filter TechnologyImplementationLabTest',
                        'php artisan migrate:status',
                    ],
                ],
            ];
        }

        if ($technology === 'database-eloquent' && CoveringIndexTopicService::matchesTasks($tasks, $filters)) {
            return [
                [
                    'label' => 'Capture query-plan baseline',
                    'goal' => 'Freeze the slow query shape before changing indexes so the optimization has evidence.',
                    'tasks' => collect($tasks)
                        ->map(fn (array $task): string => sprintf('Read %s from %s and write the selected columns, WHERE clauses, ORDER BY, LIMIT, and expected row count.', $task['title'], $task['source_path']))
                        ->all(),
                ],
                [
                    'label' => 'Design the covering index',
                    'goal' => 'Use key columns for filtering and ordering, then add only returned columns through INCLUDE.',
                    'tasks' => collect($tasks)
                        ->map(fn (array $task): string => sprintf('Implement step %d as a PostgreSQL covering-index migration and query-plan note using: %s.', $task['step'], implode(', ', $task['files'])))
                        ->all(),
                ],
                [
                    'label' => 'Verify heap fetch removal',
                    'goal' => 'Prove the plan moved toward Index Only Scan without pretending INCLUDE alone removes all heap reads.',
                    'tasks' => [
                        'Run EXPLAIN (ANALYZE, BUFFERS) before and after the index and compare Index Scan, Index Only Scan, Buffers, and Heap Fetches.',
                        'Check VACUUM/autovacuum behavior and visibility-map health for the table before accepting the result.',
                        'Reject the change if included columns are wide, high-churn, or unrelated to the measured hot query.',
                    ],
                ],
                [
                    'label' => 'Plan rollout and bloat guardrails',
                    'goal' => 'Ship the index only with storage, write-overhead, rollback, and monitoring evidence.',
                    'tasks' => $this->commandsFor($technology, $filters),
                ],
            ];
        }

        if ($technology === 'llm-foundations' && AiAgentMemoryTopicService::matchesTasks($tasks, $filters)) {
            return [
                [
                    'label' => 'Separate memory contracts',
                    'goal' => 'Classify agent memory as working, episodic, semantic, or procedural before storing or retrieving it.',
                    'tasks' => collect($tasks)
                        ->map(fn (array $task): string => sprintf('Read %s from %s and mark which memory type step %d needs.', $task['title'], $task['source_path'], $task['step']))
                        ->all(),
                ],
                [
                    'label' => 'Design memory governance',
                    'goal' => 'Prevent stale, private, or low-confidence memory from silently steering the agent.',
                    'tasks' => [
                        'Define scope, owner, source, freshness, confidence, permission, retention, and correction rules for each memory type.',
                        'Keep current task state in working memory and durable repo facts in semantic memory instead of mixing both in one prompt bucket.',
                        'Require procedural memory to point to reviewed playbooks, tests, commands, and rollback steps.',
                    ],
                ],
                [
                    'label' => 'Implement memory planner',
                    'goal' => 'Create a small service that returns memory type, allowed contents, retrieval rule, and safety check for a developer-agent task.',
                    'tasks' => collect($tasks)
                        ->map(fn (array $task): string => sprintf('Implement step %d with memory classification, retrieval policy, and test evidence in: %s.', $task['step'], implode(', ', $task['files'])))
                        ->all(),
                ],
                [
                    'label' => 'Run agent-memory checks',
                    'goal' => 'Prove the agent memory design is useful without trusting stale or private context.',
                    'tasks' => [
                        'Add tests for stale semantic memory, private episodic memory, missing source, low confidence, and procedural playbook mismatch.',
                        'php artisan test --filter AiAgentMemoryPlanWorkbenchTest',
                        'php artisan test --filter AiAgentMemoryPlanServiceTest',
                        'php artisan route:list --path=ai-agent-memory-plan',
                    ],
                ],
            ];
        }

        if ($technology === 'llm-foundations' && AiTypeComparisonTopicService::matchesTasks($tasks, $filters)) {
            return [
                [
                    'label' => 'Separate AI output contracts',
                    'goal' => 'Classify each source record by whether it asks for a prediction contract or a generation contract.',
                    'tasks' => collect($tasks)
                        ->map(fn (array $task): string => sprintf('Read %s from %s and mark whether the expected output is score, label, forecast, ranking, generated text, generated code, image, summary, or answer.', $task['title'], $task['source_path']))
                        ->all(),
                ],
                [
                    'label' => 'Map inputs and evidence',
                    'goal' => 'Connect predictive work to historical data and labels, and generative work to prompt, context, retrieved evidence, and constraints.',
                    'tasks' => collect($tasks)
                        ->map(fn (array $task): string => sprintf('Implement step %d as an AI type decision note using: %s.', $task['step'], implode(', ', $task['files'])))
                        ->all(),
                ],
                [
                    'label' => 'Design separate evaluation checks',
                    'goal' => 'Use predictive metrics for prediction work and groundedness, safety, citation, test, and human-review checks for generation work.',
                    'tasks' => [
                        'List at least two predictive metrics such as precision, recall, calibration, AUC, error rate, or business lift.',
                        'List at least two generative checks such as groundedness, usefulness, safety, citation quality, tests, or human review.',
                        'Document failure modes separately: drift, overfitting, and biased labels for Predictive AI; hallucination, fabricated citations, prompt injection, and unsafe code for Generative AI.',
                    ],
                ],
                [
                    'label' => 'Run AI type verification',
                    'goal' => 'Prove the explanation can be defended without mixing prediction and generation risks.',
                    'tasks' => $this->commandsFor($technology, $filters),
                ],
            ];
        }

        if ($technology === 'rag-systems') {
            return [
                [
                    'label' => 'Select chatbot context path',
                    'goal' => 'Choose RAG, Long Context, CAG, or hybrid routing before committing to a retrieval pattern.',
                    'tasks' => collect($tasks)
                        ->map(fn (array $task): string => sprintf('Read %s from %s and decide whether step %d needs retrieval, packed context, cached context, or hybrid routing.', $task['title'], $task['source_path'], $task['step']))
                        ->all(),
                ],
                [
                    'label' => 'Define context contract',
                    'goal' => 'Make the answer API explicit about evidence, source freshness, permissions, token budget, and cache validity.',
                    'tasks' => collect($tasks)
                        ->map(fn (array $task): string => sprintf('Implement step %d with context_strategy, retrieval chunks, packed documents, cache keys, source IDs, and citation fields in: %s.', $task['step'], implode(', ', $task['files'])))
                        ->all(),
                ],
                [
                    'label' => 'Implement context router',
                    'goal' => 'Route each prompt through the cheapest grounded path that still protects tenant scope and answer quality.',
                    'tasks' => [
                        'Create a ChatbotContextRouter that selects RAG for broad or fresh corpora, Long Context for bounded document packs, CAG for stable FAQ or policy packs, and hybrid routing for mixed support flows.',
                        'Return selected_context_path, rag_pattern, token_budget, cache_version, source_version, fallback_reason, and missing_evidence fields from the answer contract.',
                        'Keep retrieval filters, cache keys, packed documents, and citations scoped by user, tenant, permission, locale, and content version.',
                    ],
                ],
                [
                    'label' => 'Run context strategy checks',
                    'goal' => 'Prove the chatbot context strategy is grounded, bounded, cache-aware, and interview-ready.',
                    'tasks' => [
                        'Add tests for CAG cache version, Long Context token budget, retrieval permissions, and hybrid route selection.',
                        'Verify stale cache, missing citations, unauthorized chunks, oversized context, and low-confidence retrieval fail closed or trigger fallback.',
                        ...$this->commandsFor($technology, $filters),
                    ],
                ],
            ];
        }

        return [
            [
                'label' => 'Read source records',
                'goal' => sprintf('Understand the JSON records currently mapped to %s.', $technology),
                'tasks' => collect($tasks)
                    ->map(fn (array $task): string => sprintf('Read %s from %s.', $task['title'], $task['source_path']))
                    ->all(),
            ],
            [
                'label' => 'Create focused implementation files',
                'goal' => 'Create the smallest Laravel files needed for the selected records.',
                'tasks' => collect($tasks)
                    ->map(fn (array $task): string => sprintf('Implement step %d using: %s.', $task['step'], implode(', ', $task['files'])))
                    ->all(),
            ],
            [
                'label' => 'Connect route, service, and verification',
                'goal' => 'Wire the example into a route or API path and keep controller logic thin.',
                'tasks' => collect($tasks)
                    ->map(fn (array $task): string => sprintf('Open the workspace for %s and verify its route, service, and test plan.', $task['record_id']))
                    ->all(),
            ],
            [
                'label' => 'Run focused checks',
                'goal' => 'Prove the implementation works before moving to the next content record.',
                'tasks' => $this->commandsFor($technology, $filters),
            ],
        ];
    }

    /**
     * Return verification commands for one inferred technology.
     *
     * @return array<int, string>
     */
    private function commandsFor(string $technology, array $filters = []): array
    {
        if ($technology === 'database-eloquent' && DatabaseLockingTopicService::matchesRecord(['content' => [], 'task' => ''], $filters)) {
            return [
                'php artisan test --filter DatabaseLocking',
                'php artisan test --filter TechnologyImplementationLabTest',
                'php artisan migrate:status',
            ];
        }

        if ($technology === 'database-eloquent' && CoveringIndexTopicService::matchesRecord(['content' => [], 'task' => ''], $filters)) {
            return [
                'php artisan test --filter CoveringIndex',
                'php artisan test --filter ContentPracticeDrillTest',
                'php artisan migrate:status',
            ];
        }

        if ($technology === 'auth-security' && $this->isBrokenAuthenticationTopic([], $filters)) {
            return [
                'php artisan test --filter BrokenAuthentication',
                'php artisan test --filter Auth',
                'php artisan route:list --path=login',
            ];
        }

        return match ($technology) {
            'php' => [
                'php artisan test --filter ContentBackedNormalizer',
                'php artisan test --filter NameNormalizer',
            ],
            'api-validation' => [
                'php artisan test --filter ContentBackedApiDrill',
                'php artisan route:list --path=api/practice',
            ],
            'graphql-api' => [
                'php artisan test --filter GraphqlRestDecisionWorkbenchTest',
                'php artisan route:list --path=graphql-rest-decision',
            ],
            'javascript-closures' => [
                'php artisan test --filter ContentPracticeDrillTest',
                'php artisan test --filter TechnologyInterviewPackTest',
                'php artisan test --filter TechnologyImplementationLabTest',
            ],
            'react-render-performance' => [
                'php artisan test --filter ReactRenderOptimizationPlan',
                'php artisan route:list --path=react-render-optimization-plan',
            ],
            'sql-injection-defense' => [
                'php artisan test --filter SqlInjectionDefensePlan',
                'php artisan route:list --path=sql-injection-defense-plan',
            ],
            'csrf-protection' => [
                'php artisan test --filter CsrfProtectionPlan',
                'php artisan route:list --path=csrf-protection-plan',
            ],
            'xss-defense' => [
                'php artisan test --filter SecurityEscapePreviewWorkbenchTest',
                'php artisan route:list --path=security-escape-preview',
            ],
            'idor-access-control' => [
                'php artisan test --filter IdorAccessReviewWorkbenchTest',
                'php artisan route:list --path=idor-access-review',
            ],
            'security-misconfiguration' => [
                'php artisan test --filter ConfigurationReadiness',
                'php artisan route:list --path=configuration-readiness',
            ],
            'auth-security' => [
                'php artisan test --filter Authorization',
                'php artisan route:list --path=practice',
            ],
            'database-eloquent' => [
                'php artisan test --filter Database',
                'php artisan migrate:status',
            ],
            'files-media' => [
                'php artisan test --filter FileStorage',
                'php artisan storage:link',
            ],
            'async-workflow' => [
                'php artisan test --filter Event',
                'php artisan queue:work --once',
            ],
            'performance-cache' => [
                'php artisan test --filter Cache',
                'php artisan cache:clear',
            ],
            'load-balancing' => [
                'php artisan test --filter LoadBalancerPlanWorkbenchTest',
                'php artisan route:list --path=load-balancer-plan',
            ],
            'system-design-tradeoffs' => [
                'php artisan test --filter SystemDesignTradeoffPlan',
                'php artisan route:list --path=system-design-tradeoff-plan',
            ],
            'reverse-proxy-edge' => [
                'php artisan test --filter ReverseProxyFailurePlanWorkbenchTest',
                'php artisan route:list --path=reverse-proxy-failure-plan',
            ],
            'siem-elk-observability' => [
                'php artisan test --filter SiemElkPlanWorkbenchTest',
                'php artisan route:list --path=siem-elk-plan',
            ],
            'kubernetes-orchestration' => [
                'php artisan test --filter KubernetesAnalogyPlanWorkbenchTest',
                'php artisan route:list --path=kubernetes-analogy-plan',
            ],
            'jwt-token-storage' => [
                'php artisan test --filter JwtTokenStoragePlanWorkbenchTest',
                'php artisan route:list --path=jwt-token-storage-plan',
            ],
            'jwt-revocation' => [
                'php artisan test --filter JwtRevocationPlanWorkbenchTest',
                'php artisan route:list --path=jwt-revocation-plan',
            ],
            'oauth-flow' => [
                'php artisan test --filter OauthFlowPlanWorkbenchTest',
                'php artisan route:list --path=oauth-flow-plan',
            ],
            'graph-traversal' => [
                'php artisan test --filter GraphTraversalPlanWorkbenchTest',
                'php artisan route:list --path=graph-traversal-plan',
                'php artisan test --filter TechnologyImplementationLabTest',
            ],
            'lsm-tree-storage' => [
                'php artisan test --filter LsmTreePlanWorkbenchTest',
                'php artisan route:list --path=lsm-tree-plan',
            ],
            'llm-foundations' => [
                'php artisan test --filter LlmDecisionLoopPlanWorkbenchTest',
                'php artisan route:list --path=llm-decision-loop-plan',
            ],
            'rag-systems' => [
                'php artisan test --filter RagStrategyPlanWorkbenchTest',
                'php artisan test --filter RagStrategyPlanServiceTest',
                'php artisan route:list --path=rag-strategy-plan',
            ],
            'ai-cloud-interview' => [
                'php artisan test --filter AiCloudInterviewRubricWorkbenchTest',
                'php artisan route:list --path=ai-cloud-interview-rubric',
            ],
            default => [
                'php artisan test --filter TechnologyImplementationLab',
                'php artisan route:list --path=practice',
                'vendor\\bin\\pint --test',
            ],
        };
    }

    /**
     * Return route handoffs that naturally follow an implementation lab.
     *
     * @param  array<string, mixed>  $filters
     * @return array<int, array{label: string, purpose: string, path: string, api_path: string|null}>
     */
    private function nextActionsFor(string $technology, array $filters): array
    {
        $query = http_build_query($this->queryFilters($filters));

        return [
            [
                'label' => 'Review code examples',
                'purpose' => 'Compare the implementation files against generated source-backed snippets.',
                'path' => $this->path("/practice/technology-code-examples/{$technology}", $query),
                'api_path' => $this->path("/api/practice/technology-code-examples/{$technology}", $query),
            ],
            [
                'label' => 'Prepare commit plan',
                'purpose' => 'Turn changed files, commands, and evidence into a review-ready branch plan.',
                'path' => $this->path("/practice/technology-commit-plan/{$technology}", $query),
                'api_path' => $this->path("/api/practice/technology-commit-plan/{$technology}", $query),
            ],
            [
                'label' => 'Create portfolio artifact',
                'purpose' => 'Convert the implementation into a reusable README-style project artifact.',
                'path' => $this->path("/practice/technology-portfolio-artifact/{$technology}", $query),
                'api_path' => $this->path("/api/practice/technology-portfolio-artifact/{$technology}", $query),
            ],
            [
                'label' => 'Practice interview defense',
                'purpose' => 'Practice explaining the implementation choices, tradeoffs, and verification evidence.',
                'path' => $this->path("/practice/technology-interview-pack/{$technology}", $query),
                'api_path' => $this->path("/api/practice/technology-interview-pack/{$technology}", $query),
            ],
        ];
    }

    /**
     * Keep only filled filters for generated route handoffs.
     *
     * @param  array<string, mixed>  $filters
     * @return array<string, int|string>
     */
    private function queryFilters(array $filters): array
    {
        return collect($filters)
            ->reject(fn (mixed $value, string $key): bool => $key === 'technology')
            ->filter(fn (mixed $value): bool => $value !== null && $value !== '')
            ->map(fn (mixed $value): int|string => is_int($value) ? $value : (string) $value)
            ->all();
    }

    /**
     * Append a query string when filters are present.
     */
    private function path(string $path, string $query): string
    {
        return $query === '' ? $path : sprintf('%s?%s', $path, $query);
    }

    /**
     * Detect whether the JavaScript closure lab should specialize for arrow-function `this`.
     *
     * @param  array<int, array<string, mixed>>  $tasks
     * @param  array<string, mixed>  $filters
     */
    private function isArrowThisTopic(array $tasks, array $filters): bool
    {
        $haystack = strtolower(implode(' ', [
            $filters['search'] ?? '',
            ...collect($tasks)
                ->flatMap(fn (array $task): array => [
                    $task['title'] ?? '',
                    $task['task'] ?? '',
                    implode(' ', (array) ($task['files'] ?? [])),
                ])
                ->all(),
        ]));

        return str_contains($haystack, 'arrow')
            && (str_contains($haystack, 'this') || str_contains($haystack, 'lexical'));
    }

    /**
     * Detect whether the auth-security lab should specialize for broken authentication.
     *
     * @param  array<int, array<string, mixed>>  $tasks
     * @param  array<string, mixed>  $filters
     */
    private function isBrokenAuthenticationTopic(array $tasks, array $filters): bool
    {
        $haystack = strtolower(implode(' ', [
            $filters['search'] ?? '',
            ...collect($tasks)
                ->flatMap(fn (array $task): array => [
                    $task['title'] ?? '',
                    $task['task'] ?? '',
                    $task['source_path'] ?? '',
                    implode(' ', (array) ($task['files'] ?? [])),
                ])
                ->all(),
        ]));

        return str_contains($haystack, 'broken authentication')
            || str_contains($haystack, 'authentication lifecycle')
            || str_contains($haystack, 'session fixation')
            || str_contains($haystack, 'reset token')
            || str_contains($haystack, 'brute force');
    }
}
