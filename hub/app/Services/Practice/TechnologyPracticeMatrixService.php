<?php

declare(strict_types=1);

namespace App\Services\Practice;

use App\Repositories\Contracts\LearningContentRepositoryInterface;

final class TechnologyPracticeMatrixService
{
    /**
     * Create a matrix from content records, technologies, and native exercises.
     */
    public function __construct(
        private readonly LearningContentRepositoryInterface $content,
        private readonly ContentPracticeMapperService $mapper,
        private readonly ContentPracticeWorkbenchLinkService $workbenches,
    ) {}

    /**
     * Build a technology matrix backed by JSON content and question records.
     *
     * @param  array{family?: string|null, language?: string|null, search?: string|null}  $filters
     * @return array{items: array<int, array<string, mixed>>, meta: array<string, mixed>}
     */
    public function build(array $filters = []): array
    {
        $filters = $filters + ['language' => 'en'];
        $records = collect($this->content->questions($filters))
            ->filter(fn (array $record): bool => filled($record['title'] ?? null))
            ->map(fn (array $record): array => $record + [
                'technology' => $this->mapper->inferTechnology($record),
            ]);

        $items = $records
            ->groupBy('technology')
            ->map(fn ($group, string $technology): array => $this->toMatrixItem($technology, $group->values()->all()))
            ->sortByDesc('record_count')
            ->values()
            ->all();

        return [
            'items' => $items,
            'meta' => [
                'filters' => $filters,
                'record_count' => $records->count(),
                'technology_count' => count($items),
            ],
        ];
    }

    /**
     * Convert grouped records into one matrix row.
     *
     * @param  array<int, array<string, mixed>>  $records
     * @return array<string, mixed>
     */
    private function toMatrixItem(string $technology, array $records): array
    {
        $sourcePaths = collect($records)
            ->pluck('source_path')
            ->unique()
            ->sort()
            ->values();

        $families = collect($records)
            ->pluck('family')
            ->unique()
            ->sort()
            ->values();

        $sample = $records[0];

        return [
            'technology' => $technology,
            'record_count' => count($records),
            'source_count' => $sourcePaths->count(),
            'families' => $families->all(),
            'sources' => $sourcePaths->take(5)->all(),
            'sample' => [
                'record_id' => $sample['id'],
                'title' => $sample['title'],
                'source_key' => $sample['source_key'],
                'source_path' => $sample['source_path'],
            ],
            'practice' => $this->mapper->practiceSummaryFor($technology),
            'related_workbench' => $this->workbenches->linkForTechnologyContext($technology, $this->recordsHaystack($records)),
            'focus_checks' => $this->focusChecksFor($technology, $records),
            'drill_query' => [
                'record_id' => $sample['id'],
                'source_key' => $sample['source_key'],
                'technology' => $technology,
            ],
        ];
    }

    /**
     * Return concise checks learners should see before opening a full pipeline.
     *
     * @return array<int, string>
     */
    private function focusChecksFor(string $technology, array $records = []): array
    {
        if ($technology === 'javascript-closures' && $this->isArrowThisRecords($records)) {
            return [
                'Trace the surrounding scope that supplies arrow-function `this` before describing syntax.',
                'Compare normal function call-site `this` with arrow lexical `this` using an object method example.',
                'Use obj.arrow(), call/apply/bind limitations, and callback use cases as interview anchors.',
            ];
        }

        $recordsHaystack = $this->recordsHaystack($records);

        if ($technology === 'llm-foundations' && AiAgentMemoryTopicService::matchesText($recordsHaystack)) {
            return [
                'Split agent memory records into working, episodic, semantic, and procedural contracts.',
                'Require source, freshness, confidence, permission, retention, and correction metadata before reuse.',
                'Use stale-memory, private-memory, procedural-mismatch, and working-memory leak checks as promotion gates.',
            ];
        }

        if ($technology === 'llm-foundations' && AiTypeComparisonTopicService::matchesText($recordsHaystack)) {
            return [
                'Classify the topic by output contract before discussing model capability.',
                'For Predictive AI, connect scores, labels, forecasts, rankings, and risk decisions to historical data and metrics.',
                'For Generative AI, connect prompts, context, generated output, groundedness, safety, and human review.',
            ];
        }

        if ($technology === 'database-eloquent' && DatabaseLockingTopicService::matchesRecord(['records' => $records])) {
            return [
                'Classify the write invariant before adding locks: inventory, balance, booking, or workflow state.',
                'Use `DB::transaction()` and `lockForUpdate()` together before protected state is checked or changed.',
                'Use concurrent replay, deadlock retry, lock timeout, hot-row contention, indexed lookup, and lock-wait monitoring as promotion gates.',
            ];
        }

        if ($technology === 'database-eloquent' && CoveringIndexTopicService::matchesArtifact(['records' => $records])) {
            return [
                'Read the query plan before changing the index: selected columns, filters, ordering, Heap Fetches, and buffer reads.',
                'Use key columns for filter/order and INCLUDE only for projected hot-query columns.',
                'Check visibility map, VACUUM or autovacuum health, index size, bloat risk, write overhead, and rollback before promotion.',
            ];
        }

        if ($technology === 'rag-systems') {
            return [
                'Classify the chatbot context path as RAG, Long Context, CAG, or hybrid routing before implementation.',
                'Require an answer contract with citations, source version, cache version, token budget, fallback reason, and missing evidence.',
                'Use permission-scoped retrieval, cache invalidation, packed-context limits, source freshness, and fallback tests as promotion gates.',
            ];
        }

        return match ($technology) {
            'php' => [
                'Explain whether the topic is syntax, runtime behavior, memory, or testability.',
                'For stack versus heap, connect call frames, object lifetime, references, and memory pressure.',
                'Use a small PHP example before turning the topic into interview notes.',
            ],
            'oauth-flow' => [
                'Choose the OAuth flow from client type and secret-storage ability.',
                'For public clients, require Authorization Code with PKCE and S256.',
                'Do not expose code_verifier in authorize URLs, logs, or rendered pages.',
            ],
            'javascript-closures' => [
                'Trace lexical scope, inner function, and captured binding before describing use cases.',
                'Use createCounter(), private state, var versus let, and stale closures as interview anchors.',
            ],
            'xss-defense' => [
                'Match escaping to HTML, attribute, JavaScript, URL, or rich-text context.',
                'Treat raw HTML as a trust-boundary decision.',
            ],
            'idor-access-control' => [
                'Map every object ID route to owner, tenant, team, role, or public access rules.',
                'Require scoped lookup before returning the model and policy checks before response, download, or export.',
                'Use two-user or two-tenant denial tests as the promotion gate.',
            ],
            'sql-injection-defense' => [
                'Bind values and allowlist identifiers separately.',
                'Test malicious payloads at the request boundary.',
            ],
            'security-misconfiguration' => [
                'Separate unsafe defaults from environment-specific drift.',
                'Check debug mode, secret exposure, CORS, headers, cookies, proxies, HTTPS, and public storage.',
                'Require release smoke checks that fail closed when production config is unsafe.',
            ],
            default => [
                'Open the workbench and implementation lab before preparing portfolio evidence.',
                'Keep source records, changed files, and verification commands connected.',
            ],
        };
    }

    /**
     * Detect arrow-function `this` records inside the broader JavaScript closure lane.
     *
     * @param  array<int, array<string, mixed>>  $records
     */
    private function isArrowThisRecords(array $records): bool
    {
        $haystack = strtolower(implode(' ', collect($records)
            ->flatMap(fn (array $record): array => [
                $record['title'] ?? '',
                $record['summary'] ?? '',
                $record['body'] ?? '',
                $record['source_path'] ?? '',
            ])
            ->all()));

        return str_contains($haystack, 'arrow')
            && (str_contains($haystack, 'this') || str_contains($haystack, 'lexical'));
    }

    /**
     * Build searchable text from matrix records.
     *
     * @param  array<int, array<string, mixed>>  $records
     */
    private function recordsHaystack(array $records): string
    {
        return implode(' ', collect($records)
            ->flatMap(fn (array $record): array => [
                $record['title'] ?? '',
                $record['summary'] ?? '',
                $record['body'] ?? '',
                $record['source_path'] ?? '',
            ])
            ->all());
    }
}
