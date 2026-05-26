<?php

declare(strict_types=1);

namespace App\Services\Practice;

use App\Repositories\Contracts\LearningContentRepositoryInterface;

final class SourcePracticePackIndexService
{
    /**
     * Create an index of JSON source files that can become practice packs.
     */
    public function __construct(
        private readonly LearningContentRepositoryInterface $content,
        private readonly ContentPracticeMapperService $mapper,
    ) {}

    /**
     * Build an index of source files with record and technology summaries.
     *
     * @param  array{family?: string|null, language?: string|null, search?: string|null, limit?: int|string|null}  $filters
     * @return array{items: array<int, array<string, mixed>>, meta: array<string, mixed>}
     */
    public function build(array $filters = []): array
    {
        $limit = $this->normalizeLimit($filters['limit'] ?? null);
        $language = $filters['language'] ?? 'en';
        $sources = collect($this->content->sources())
            ->when($filters['family'] ?? null, fn ($items, string $family) => $items->where('family', $family))
            ->where('language', $language);

        $questions = collect($this->content->questions([
            'family' => $filters['family'] ?? null,
            'language' => $language,
            'search' => $filters['search'] ?? null,
        ]))
            ->filter(fn (array $record): bool => filled($record['title'] ?? null));

        $items = $sources
            ->map(fn (array $source): array => $this->toIndexItem($source, $questions->where('source_key', $source['key'])->values()->all()))
            ->filter(fn (array $item): bool => $item['record_count'] > 0)
            ->sortByDesc('record_count')
            ->take($limit)
            ->values()
            ->all();

        return [
            'items' => $items,
            'meta' => [
                'filters' => [
                    'family' => $filters['family'] ?? null,
                    'language' => $language,
                    'search' => $filters['search'] ?? null,
                    'limit' => $limit,
                ],
                'count' => count($items),
                'available_sources' => $sources->count(),
            ],
        ];
    }

    /**
     * Convert a source and its records into one index item.
     *
     * @param  array<string, mixed>  $source
     * @param  array<int, array<string, mixed>>  $records
     * @return array<string, mixed>
     */
    private function toIndexItem(array $source, array $records): array
    {
        $technologies = collect($records)
            ->map(fn (array $record): string => $this->mapper->inferTechnology($record))
            ->countBy()
            ->sortDesc()
            ->map(fn (int $count, string $technology): array => [
                'technology' => $technology,
                'record_count' => $count,
            ])
            ->values()
            ->all();

        $sample = $records[0] ?? null;
        $primaryTechnology = $technologies[0]['technology'] ?? null;

        return [
            'source' => [
                'key' => $source['key'],
                'path' => $source['path'],
                'family' => $source['family'],
                'topic' => $source['topic'],
                'language' => $source['language'],
                'title' => $source['title'],
            ],
            'record_count' => count($records),
            'technologies' => $technologies,
            'sample_workspace_query' => $sample === null ? null : [
                'record_id' => $sample['id'],
                'source_key' => $source['key'],
                'technology' => $primaryTechnology,
            ],
        ];
    }

    /**
     * Normalize source index limit.
     */
    private function normalizeLimit(int|string|null $limit): int
    {
        $value = is_numeric($limit) ? (int) $limit : 20;

        return max(1, min(100, $value));
    }
}
