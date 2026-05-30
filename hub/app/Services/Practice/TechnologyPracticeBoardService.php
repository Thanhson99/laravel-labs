<?php

declare(strict_types=1);

namespace App\Services\Practice;

final class TechnologyPracticeBoardService
{
    /**
     * Create a technology-focused board from content-to-practice mappings.
     */
    public function __construct(
        private readonly ContentPracticeMapperService $mapper,
        private readonly ContentPracticeWorkbenchLinkService $workbenches,
    ) {}

    /**
     * Build a practice board for one inferred technology.
     *
     * @param  array{technology?: string|null, family?: string|null, language?: string|null, search?: string|null, limit?: int|string|null}  $filters
     * @return array{technology: string, sources: array<int, array<string, mixed>>, queue_query: array<string, mixed>, meta: array<string, mixed>}
     */
    public function build(array $filters = []): array
    {
        $technology = $filters['technology'] ?? 'api-validation';
        $mapped = $this->mapper->map([
            'technology' => $technology,
            'family' => $filters['family'] ?? null,
            'language' => $filters['language'] ?? null,
            'search' => $filters['search'] ?? null,
            'limit' => $filters['limit'] ?? 50,
        ]);

        $sources = collect($mapped['items'])
            ->groupBy(fn (array $item): string => $item['source']['key'])
            ->map(fn ($items): array => $this->toSourceGroup($items->values()->all()))
            ->sortByDesc('record_count')
            ->values()
            ->all();

        return [
            'technology' => $technology,
            'practice' => $this->mapper->practiceSummaryFor($technology),
            'related_workbench' => $this->workbenches->linkForTechnologyContext($technology, $this->topicHaystack($mapped['items'], $filters)),
            'focus_checks' => $this->focusChecksFor($technology, $mapped['items'], $filters),
            'readiness_signal' => $this->readinessSignalFor($technology, $mapped['items'], $filters),
            'sources' => $sources,
            'queue_query' => [
                'technology' => $technology,
                'family' => $filters['family'] ?? null,
                'language' => $filters['language'] ?? null,
                'search' => $filters['search'] ?? null,
                'limit' => min((int) ($filters['limit'] ?? 10), 10),
            ],
            'meta' => [
                'filters' => $mapped['meta']['filters'],
                'record_count' => $mapped['meta']['count'],
                'source_count' => count($sources),
            ],
        ];
    }

    /**
     * Convert mapped records from one source into a board group.
     *
     * @param  array<int, array<string, mixed>>  $items
     * @return array<string, mixed>
     */
    private function toSourceGroup(array $items): array
    {
        $first = $items[0];

        return [
            'source' => $first['source'],
            'record_count' => count($items),
            'practice' => $first['practice'],
            'sample_records' => collect($items)
                ->take(3)
                ->map(fn (array $item): array => [
                    'record_id' => $item['id'],
                    'title' => $item['content']['title'],
                    'task' => $item['task'],
                    'workspace_query' => [
                        'record_id' => $item['id'],
                        'source_key' => $item['source']['key'],
                        'technology' => $item['technology'],
                    ],
                ])
                ->values()
                ->all(),
        ];
    }

    /**
     * Return concise checks shown before a learner opens source records.
     *
     * @return array<int, string>
     */
    private function focusChecksFor(string $technology, array $items = [], array $filters = []): array
    {
        if ($technology === 'javascript-closures' && $this->isArrowThisTopic($items, $filters)) {
            return [
                'Identify where the arrow function is created and which surrounding scope supplies `this`.',
                'Compare a normal object method with an arrow property before explaining callback use.',
                'Review obj.arrow(), call/apply/bind limitations, and when a normal method is safer before interview practice.',
            ];
        }

        if ($technology === 'llm-foundations' && AiAgentMemoryTopicService::matchesText($this->topicHaystack($items, $filters))) {
            return [
                'Classify memory as working, episodic, semantic, or procedural before adding storage or retrieval.',
                'Require scope, source, freshness, confidence, permission, retention, and correction metadata before reuse.',
                'Promote the board only after stale semantic memory and private episodic memory have explicit guardrails.',
            ];
        }

        if ($technology === 'llm-foundations' && AiTypeComparisonTopicService::matchesText($this->topicHaystack($items, $filters))) {
            return [
                'Classify each source record by output contract before naming model capability.',
                'For Predictive AI, require score, label, forecast, ranking, or risk-decision evidence plus metrics.',
                'For Generative AI, require prompt, context, generated output, groundedness, safety, and review evidence.',
            ];
        }

        if ($technology === 'database-eloquent' && DatabaseLockingTopicService::matchesRecord(['items' => $items, 'filters' => $filters])) {
            return [
                'Name the concurrent-write invariant before choosing row locks.',
                'Use `DB::transaction()` plus `lockForUpdate()` before reading and changing protected state.',
                'Check indexed lookup, short transaction scope, deadlock retry, lock timeout, and hot-row contention before promotion.',
            ];
        }

        if ($technology === 'database-eloquent' && CoveringIndexTopicService::matchesArtifact(['items' => $items, 'filters' => $filters])) {
            return [
                'Start from EXPLAIN (ANALYZE, BUFFERS) before proposing a new index.',
                'Separate filter/order key columns from INCLUDE payload columns for the hot query.',
                'Check visibility map, VACUUM or autovacuum health, bloat risk, write overhead, and rollback before promotion.',
            ];
        }

        if ($technology === 'rag-systems') {
            return [
                'Choose RAG, Long Context, CAG, or hybrid routing before selecting a retrieval pattern.',
                'Define the answer contract with context path, citations, source version, cache version, token budget, fallback reason, and missing evidence.',
                'Test tenant-scoped retrieval, packed-context limits, CAG invalidation, stale sources, missing citations, and hybrid route selection.',
            ];
        }

        return match ($technology) {
            'php' => [
                'Separate language syntax from runtime behavior.',
                'For stack versus heap, identify call frames, local variables, object lifetime, references, and cleanup.',
                'Use one small PHP example before writing the interview answer.',
            ],
            'oauth-flow' => [
                'Choose the OAuth flow from client type, secret storage, and user delegation.',
                'For public clients, require Authorization Code with PKCE and S256 code_challenge.',
                'Keep code_verifier private until token exchange and reject callback token leakage.',
            ],
            'graph-traversal' => [
                'Classify the problem as nearest result, shortest unweighted path, branch exploration, dependency reasoning, or subtree validation.',
                'Use BFS with a queue for level-order and nearest-hop work; use DFS with a stack or recursion for depth-first branch work.',
                'Add visited-set, cycle, depth, fan-out, batching, pagination, and memory checks before touching real APIs or database hierarchies.',
            ],
            'javascript-closures' => [
                'Identify the lexical scope where the closure is created.',
                'Trace the captured binding through repeated calls such as createCounter().',
                'Review var versus let loops, stale closures, and practical callback use before interview practice.',
            ],
            'sql-injection-defense' => [
                'Bind user-controlled values instead of concatenating SQL.',
                'Allowlist dynamic identifiers before query construction.',
                'Test malicious payloads at the request boundary.',
            ],
            'csrf-protection' => [
                'Keep state-changing routes off GET.',
                'Require CSRF token proof for cookie-authenticated browser flows.',
                'Test missing or stale tokens without mutating state.',
            ],
            'xss-defense' => [
                'Match escaping to the browser output context.',
                'Review raw HTML through sanitizer or trust-boundary rules.',
                'Test script and DOM payloads as harmless rendered data.',
            ],
            'idor-access-control' => [
                'Identify every route parameter, download, export, nested resource, and signed URL that points at an object.',
                'Require scoped queries and policy or Gate checks for the exact object, not just the authenticated user.',
                'Prove denial with two-user or two-tenant tests and keep replay notes as merge evidence.',
            ],
            'security-misconfiguration' => [
                'Inventory production-sensitive settings before writing code.',
                'Check debug mode, exposed secrets, public storage, CORS, headers, cookie flags, HTTPS, and trusted proxies.',
                'Require fail-closed smoke checks, owners, and rollback notes before promoting the topic.',
            ],
            default => [
                'Open source records before implementation work.',
                'Keep changed files, verification commands, and interview evidence connected.',
            ],
        };
    }

    /**
     * Return the board-level signal that tells a learner when to move forward.
     */
    private function readinessSignalFor(string $technology, array $items = [], array $filters = []): string
    {
        if ($technology === 'javascript-closures' && $this->isArrowThisTopic($items, $filters)) {
            return 'Ready when the learner can prove arrow lexical `this`, normal function call-site `this`, obj.arrow() behavior, call/apply/bind limits, and one practical callback use.';
        }

        if ($technology === 'llm-foundations' && AiAgentMemoryTopicService::matchesText($this->topicHaystack($items, $filters))) {
            return 'Ready when the learner can separate working, episodic, semantic, and procedural memory and prove source, freshness, confidence, permission, retention, and correction guardrails.';
        }

        if ($technology === 'llm-foundations' && AiTypeComparisonTopicService::matchesText($this->topicHaystack($items, $filters))) {
            return 'Ready when the learner can compare Predictive AI and Generative AI by output contract, input evidence, evaluation metrics, and failure modes.';
        }

        if ($technology === 'database-eloquent' && DatabaseLockingTopicService::matchesRecord(['items' => $items, 'filters' => $filters])) {
            return 'Ready when the learner can prove the invariant under concurrent requests, place `lockForUpdate()` inside `DB::transaction()`, keep the lock query indexed and narrow, and explain deadlock, timeout, and contention behavior.';
        }

        if ($technology === 'database-eloquent' && CoveringIndexTopicService::matchesArtifact(['items' => $items, 'filters' => $filters])) {
            return 'Ready when the learner can prove Heap Fetches dropped, justify INCLUDE columns, explain visibility-map or VACUUM caveats, and name index bloat or write-overhead tradeoffs.';
        }

        if ($technology === 'rag-systems') {
            return 'Ready when the learner can choose RAG, Long Context, CAG, or hybrid routing and prove citations, permissions, token budgets, cache freshness, source versions, and fallback behavior.';
        }

        return match ($technology) {
            'php' => 'Ready when the learner can explain the PHP topic with a small code example, the runtime model, and one practical failure mode.',
            'oauth-flow' => 'Ready when the learner can explain why PKCE binds the authorization code to the original public client and can name the callback failure tests.',
            'graph-traversal' => 'Ready when the learner can choose BFS or DFS from the goal, prove traversal order, and name cycle, depth, fan-out, pagination, rate-limit, and memory guardrails.',
            'javascript-closures' => 'Ready when the learner can trace lexical scope, captured binding, createCounter() output, var versus let behavior, stale closure risk, and real callback use.',
            'sql-injection-defense' => 'Ready when the learner can show bindings, identifier allowlists, and payload tests from the selected source records.',
            'csrf-protection' => 'Ready when the learner can explain forged browser intent, token proof, SameSite limits, and failure tests.',
            'xss-defense' => 'Ready when the learner can map each payload to an output context and prove it cannot execute.',
            'idor-access-control' => 'Ready when the learner can show object-level authorization on every object route, explain 403 versus 404, and prove cross-user or cross-tenant denial with tests.',
            'security-misconfiguration' => 'Ready when the learner can prove unsafe production configuration fails closed and can cite owners, rollback notes, and environment-specific evidence.',
            default => sprintf('Ready when the learner can map `%s` source records to Laravel files, tests, and a concise explanation.', $technology),
        };
    }

    /**
     * Build a searchable text context from current board records and filters.
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
                    $item['content']['summary'] ?? '',
                    $item['content']['body'] ?? '',
                    $item['task'] ?? '',
                    $item['source']['path'] ?? '',
                ])
                ->all(),
        ]);
    }

    /**
     * Detect arrow-function `this` content inside the broader JavaScript closure lane.
     */
    private function isArrowThisTopic(array $items, array $filters): bool
    {
        $haystack = strtolower($this->topicHaystack($items, $filters));

        return str_contains($haystack, 'arrow')
            && (str_contains($haystack, 'this') || str_contains($haystack, 'lexical'));
    }
}
