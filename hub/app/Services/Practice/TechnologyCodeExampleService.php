<?php

declare(strict_types=1);

namespace App\Services\Practice;

final class TechnologyCodeExampleService
{
    /**
     * Create code examples from content records grouped by inferred technology.
     */
    public function __construct(
        private readonly ContentPracticeMapperService $mapper,
        private readonly ContentPracticeStarterSnippetService $snippets,
    ) {}

    /**
     * Build technology-specific code examples from JSON-backed content records.
     *
     * @param  array{family?: string|null, language?: string|null, search?: string|null, limit?: int|string|null}  $filters
     * @return array{items: array<int, array<string, mixed>>, meta: array<string, mixed>}
     */
    public function build(array $filters = []): array
    {
        $mapped = $this->mapper->map([
            'family' => $filters['family'] ?? null,
            'language' => $filters['language'] ?? 'en',
            'search' => $filters['search'] ?? null,
            'limit' => $filters['limit'] ?? 50,
        ]);

        $items = collect($mapped['items'])
            ->groupBy('technology')
            ->map(fn ($records, string $technology): array => $this->toExample($technology, $records->values()->all()))
            ->sortByDesc('record_count')
            ->values()
            ->all();

        return [
            'items' => $items,
            'meta' => [
                'filters' => $mapped['meta']['filters'],
                'record_count' => $mapped['meta']['count'],
                'technology_count' => count($items),
            ],
        ];
    }

    /**
     * Build code examples for one inferred technology across multiple source records.
     *
     * @param  array{family?: string|null, language?: string|null, search?: string|null, limit?: int|string|null}  $filters
     * @return array{technology: string, items: array<int, array<string, mixed>>, meta: array<string, mixed>}
     */
    public function buildForTechnology(string $technology, array $filters = []): array
    {
        $mapped = $this->mapper->map([
            'technology' => $technology,
            'family' => $filters['family'] ?? null,
            'language' => $filters['language'] ?? 'en',
            'search' => $filters['search'] ?? null,
            'limit' => $filters['limit'] ?? 12,
        ]);

        $items = collect($mapped['items'])
            ->map(fn (array $record): array => $this->toRecordExample($technology, $record))
            ->values()
            ->all();

        return [
            'technology' => $technology,
            'items' => $items,
            'meta' => [
                'filters' => $mapped['meta']['filters'],
                'record_count' => count($items),
            ],
        ];
    }

    /**
     * Convert one technology group into a readable code example set.
     *
     * @param  array<int, array<string, mixed>>  $records
     * @return array<string, mixed>
     */
    private function toExample(string $technology, array $records): array
    {
        $sample = $records[0];

        return [
            'technology' => $technology,
            'record_count' => count($records),
            'source' => $sample['source'],
            'content' => $sample['content'],
            'practice' => $sample['practice'],
            'task' => $sample['task'],
            'workspace_query' => [
                'record_id' => $sample['id'],
                'source_key' => $sample['source']['key'],
                'technology' => $technology,
            ],
            'snippets' => $this->snippets->snippetsFor($sample),
        ];
    }

    /**
     * Convert one mapped record into a record-specific code example.
     *
     * @return array<string, mixed>
     */
    private function toRecordExample(string $technology, array $record): array
    {
        return [
            'record_id' => $record['id'],
            'technology' => $technology,
            'source' => $record['source'],
            'content' => $record['content'],
            'practice' => $record['practice'],
            'task' => $record['task'],
            'workspace_query' => [
                'record_id' => $record['id'],
                'source_key' => $record['source']['key'],
                'technology' => $technology,
            ],
            'snippets' => $this->snippets->snippetsFor($record),
        ];
    }
}
