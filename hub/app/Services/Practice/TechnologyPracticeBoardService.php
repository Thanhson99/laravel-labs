<?php

declare(strict_types=1);

namespace App\Services\Practice;

final class TechnologyPracticeBoardService
{
    /**
     * Create a technology-focused board from content-to-practice mappings.
     */
    public function __construct(private readonly ContentPracticeMapperService $mapper) {}

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
}
