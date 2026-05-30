<?php

declare(strict_types=1);

namespace App\Services\Practice;

final class TechnologyLearningPipelineService
{
    /**
     * Create a navigable pipeline for technology-specific learning flows.
     */
    public function __construct(
        private readonly ContentPracticeMapperService $mapper,
        private readonly ContentPracticeWorkbenchLinkService $workbenches,
        private readonly PracticeProgressPayloadService $progressPayload,
    ) {}

    /**
     * Build all stage links for one inferred technology.
     *
     * @param  array{family?: string|null, language?: string|null, search?: string|null, limit?: int|string|null}  $filters
     * @return array<string, mixed>
     */
    public function build(string $technology, array $filters = []): array
    {
        $filters = [
            'family' => $filters['family'] ?? 'laravel',
            'language' => $filters['language'] ?? 'en',
            'search' => $filters['search'] ?? null,
            'limit' => $filters['limit'] ?? 5,
        ];

        $mapped = $this->mapper->map([
            'technology' => $technology,
            ...$filters,
        ]);
        $relatedWorkbench = $this->workbenches->linkForTechnologyContext(
            $technology,
            $this->topicHaystack($mapped['items'], $filters),
        );
        $stages = $this->stagesFor($technology, $filters, $relatedWorkbench);

        return [
            'title' => sprintf('Technology Learning Pipeline: %s', $technology),
            'technology' => $technology,
            'practice' => $this->mapper->practiceSummaryFor($technology),
            'related_workbench' => $relatedWorkbench,
            'record_count' => $mapped['meta']['count'],
            'focus_checks' => $this->focusChecksFor($technology, $mapped['items'], $filters),
            'sample_records' => collect($mapped['items'])
                ->take(3)
                ->map(fn (array $item): array => [
                    'record_id' => $item['id'],
                    'title' => $item['content']['title'],
                    'source_path' => $item['source']['path'],
                    'task' => $item['task'],
                ])
                ->values()
                ->all(),
            'stages' => $stages,
            'progress_payload' => $this->progressPayload->fromRows(
                $stages,
                fn (array $stage): string => $stage['label'],
            ),
            'meta' => [
                'filters' => $filters,
                'available_technologies' => $this->mapper->technologies(),
            ],
        ];
    }

    /**
     * Build ordered stage definitions.
     *
     * @param  array<string, int|string|null>  $filters
     * @param  array{label: string, path: string, route_name: string|null, concept: string}|null  $relatedWorkbench
     * @return array<int, array{step: int, label: string, purpose: string, route: string, api_route: string|null}>
     */
    private function stagesFor(string $technology, array $filters, ?array $relatedWorkbench): array
    {
        $query = http_build_query(array_filter($filters, fn (int|string|null $value): bool => $value !== null && $value !== ''));

        $stages = [
            ['Code examples', 'Read generated snippets and source-backed examples.', 'technology-code-examples'],
            ['Record examples', 'Inspect multiple records for one technology.', "technology-code-examples/{$technology}"],
            ['Implementation lab', 'Code the records through ordered implementation phases.', "technology-implementation-lab/{$technology}"],
            ['Commit plan', 'Prepare branch, changed files, verification, and review evidence.', "technology-commit-plan/{$technology}"],
            ['Portfolio artifact', 'Turn the work into source coverage and a README-style artifact.', "technology-portfolio-artifact/{$technology}"],
            ['Interview pack', 'Practice defending the implementation with evidence.', "technology-interview-pack/{$technology}"],
            ['Skill assessment', 'Score readiness and identify weak criteria.', "technology-skill-assessment/{$technology}"],
            ['Remediation plan', 'Repair weak areas with file-focused tasks.', "technology-remediation-plan/{$technology}"],
            ['Mastery checkpoint', 'Decide whether to promote or repeat.', "technology-mastery-checkpoint/{$technology}"],
            ['Spaced review', 'Schedule day 1, day 3, and day 7 recall.', "technology-spaced-review/{$technology}"],
            ['Evidence archive', 'Store retrieval keys, proof bundle, and reuse prompts.', "technology-evidence-archive/{$technology}"],
        ];

        if ($relatedWorkbench !== null) {
            array_unshift($stages, [
                'Runnable workbench',
                $relatedWorkbench['concept'],
                $relatedWorkbench['path'],
                null,
            ]);
        }

        return collect($stages)
            ->map(function (array $stage, int $index) use ($query): array {
                $route = str_starts_with($stage[2], '/')
                    ? $this->path($stage[2], $query)
                    : $this->path('/practice/'.$stage[2], $query);

                return [
                    'step' => $index + 1,
                    'label' => $stage[0],
                    'purpose' => $stage[1],
                    'route' => $route,
                    'api_route' => array_key_exists(3, $stage) ? $stage[3] : $this->path('/api/practice/'.$stage[2], $query),
                ];
            })
            ->all();
    }

    /**
     * Return concise focus checks for the selected technology pipeline.
     *
     * @return array<int, string>
     */
    private function focusChecksFor(string $technology, array $items = [], array $filters = []): array
    {
        $topicContext = $this->topicHaystack($items, $filters);

        if ($technology === 'llm-foundations' && AiAgentMemoryTopicService::matchesText($topicContext)) {
            return [
                'Separate working, episodic, semantic, and procedural memory before designing storage or retrieval.',
                'Require source, freshness, confidence, permission, retention, and correction metadata for every reusable memory item.',
                'Block stale semantic memory and private episodic memory from silently steering the next agent action.',
            ];
        }

        if ($technology === 'llm-foundations' && AiTypeComparisonTopicService::matchesText($topicContext)) {
            return [
                'Start by naming the output contract: score, label, forecast, ranking, generated text, code, image, summary, or answer.',
                'Separate Predictive AI input evidence from Generative AI prompt, context, retrieval, and constraint evidence.',
                'Use different quality checks for prediction metrics and generation review before promoting the topic.',
            ];
        }

        if ($technology === 'database-eloquent' && DatabaseLockingTopicService::matchesRecord(['items' => $items, 'filters' => $filters])) {
            return [
                'Start from the protected invariant before adding a lock: no negative stock, no double spend, or no duplicate state transition.',
                'Keep `DB::transaction()` and `lockForUpdate()` in the same critical section, with the lock taken before protected state is checked.',
                'Treat indexed lookup, short lock window, deadlock retry, timeout behavior, contention, and lock-wait monitoring as release evidence.',
            ];
        }

        if ($technology === 'database-eloquent' && CoveringIndexTopicService::matchesArtifact(['items' => $items, 'filters' => $filters])) {
            return [
                'Start from EXPLAIN (ANALYZE, BUFFERS) and record Index Scan, Index Only Scan, Heap Fetches, and buffer reads.',
                'Design the covering index with filter/order keys first and INCLUDE payload columns only when the hot query projects them.',
                'Treat visibility map, VACUUM or autovacuum, index size, bloat risk, write overhead, and rollback as release evidence.',
            ];
        }

        if ($technology === 'auth-security' && $this->isBrokenAuthenticationTopic($items, $filters)) {
            return [
                'Map the authentication lifecycle before discussing the login form.',
                'Check session regeneration, login throttling, reset-token expiry, remember-me storage, MFA recovery, logout, token refresh, and revocation.',
                'Require tests for brute force, stale reset token, old session reuse, revoked token reuse, and suspicious-login logging.',
                'Keep authentication identity proof separate from authorization permission checks in the interview answer.',
            ];
        }

        return match ($technology) {
            'php' => [
                'Separate PHP syntax questions from runtime behavior questions.',
                'For stack versus heap, explain call frames, local variables, object lifetime, references, and cleanup.',
                'Use a tiny PHP example and one interview answer before promoting the topic.',
            ],
            'oauth-flow' => [
                'Classify the client as public or confidential before choosing the OAuth flow.',
                'Use Authorization Code with PKCE for public clients and derive code_challenge with S256.',
                'Keep code_verifier private until token exchange and clear it after use.',
                'Reject callback token leakage, state mismatch, redirect URI mismatch, and reused codes.',
            ],
            'graph-traversal' => [
                'Start by naming the traversal goal: nearest match, shortest unweighted path, full branch exploration, or subtree validation.',
                'Choose BFS for level-order search, fewest hops, and nearest result; choose DFS for branch exploration, dependency reasoning, and backtracking.',
                'Require visited set, cycle detection, max depth, max nodes, batching, pagination, and rate-limit notes before applying traversal to real APIs or database trees.',
            ],
            'javascript-closures' => $this->isArrowThisTopic($items, $filters)
                ? [
                    'Start by naming the scope where the arrow function is created; that scope supplies lexical `this`.',
                    'Compare one normal object method with one arrow object method so the call-site difference is visible.',
                    'Review callback, timer, class, object-method, and call/apply/bind traps before promoting the topic.',
                ]
                : [
                    'Start by identifying the outer scope, inner function, and captured variable bindings.',
                    'Use createCounter() or a function factory to prove state persists across repeated calls.',
                    'Review var versus let loop behavior and stale closures in async callbacks, timers, event handlers, or hooks.',
                ],
            'xss-defense' => [
                'Identify the browser output context before choosing an encoder.',
                'Avoid raw HTML unless sanitizer or trusted-content boundary is explicit.',
                'Prove payloads render harmlessly through tests.',
            ],
            'idor-access-control' => [
                'Start by listing object IDs in route parameters, nested resources, downloads, exports, batch actions, and signed URL generation.',
                'For each object route, choose owner, tenant, team, role, or public access and place the control in scoped query plus policy or Gate.',
                'Add two-user or two-tenant tests for read, update, delete, download, and export before promoting the topic.',
                'Record replay request, expected 403 or 404, route-list output, and monitoring fields as merge evidence.',
            ],
            'sql-injection-defense' => [
                'Keep SQL structure fixed while binding user-controlled values.',
                'Use allowlists for dynamic identifiers such as columns and sort direction.',
                'Prove malicious payloads remain data through tests.',
            ],
            'security-misconfiguration' => [
                'Start by separating local-safe defaults from production release blockers.',
                'Check debug mode, secret exposure, public storage, CORS, security headers, cookie flags, HTTPS, and trusted proxies.',
                'Use fail-closed smoke checks with owners and rollback notes before promoting the topic.',
            ],
            default => [
                'Start from source records before changing Laravel code.',
                'Keep implementation, verification, and interview evidence connected.',
            ],
        };
    }

    /**
     * Append a query string when filters are present.
     */
    private function path(string $path, string $query): string
    {
        return $query === '' ? $path : sprintf('%s?%s', $path, $query);
    }

    /**
     * Build searchable text from current pipeline records and filters.
     *
     * @param  array<int, array<string, mixed>>  $items
     * @param  array<string, mixed>  $filters
     */
    private function topicHaystack(array $items, array $filters): string
    {
        return implode(' ', [
            $filters['search'] ?? '',
            ...collect($items)
                ->flatMap(fn (array $item): array => [
                    $item['content']['title'] ?? '',
                    $item['content']['body'] ?? '',
                    $item['task'] ?? '',
                    $item['source']['path'] ?? '',
                ])
                ->all(),
        ]);
    }

    /**
     * Detect whether the selected JavaScript closure lane is really about arrow-function `this`.
     *
     * @param  array<int, array<string, mixed>>  $items
     * @param  array<string, mixed>  $filters
     */
    private function isArrowThisTopic(array $items, array $filters): bool
    {
        $haystack = strtolower(implode(' ', [
            $filters['search'] ?? '',
            ...collect($items)
                ->flatMap(fn (array $item): array => [
                    $item['content']['title'] ?? '',
                    $item['content']['body'] ?? '',
                    $item['task'] ?? '',
                ])
                ->all(),
        ]));

        return str_contains($haystack, 'arrow')
            && (str_contains($haystack, 'this') || str_contains($haystack, 'lexical'));
    }

    /**
     * Detect whether the selected auth-security lane is about broken authentication.
     *
     * @param  array<int, array<string, mixed>>  $items
     * @param  array<string, mixed>  $filters
     */
    private function isBrokenAuthenticationTopic(array $items, array $filters): bool
    {
        $haystack = strtolower(implode(' ', [
            $filters['search'] ?? '',
            ...collect($items)
                ->flatMap(fn (array $item): array => [
                    $item['content']['title'] ?? '',
                    $item['content']['body'] ?? '',
                    $item['task'] ?? '',
                    $item['source']['path'] ?? '',
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
