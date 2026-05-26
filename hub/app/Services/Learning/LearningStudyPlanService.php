<?php

declare(strict_types=1);

namespace App\Services\Learning;

use App\Repositories\Contracts\LearningContentRepositoryInterface;
use Illuminate\Support\Collection;

final class LearningStudyPlanService
{
    private const DEFAULT_LIMIT = 8;

    private const MAX_LIMIT = 24;

    /**
     * Create a service for building study plans from JSON-backed content.
     */
    public function __construct(private readonly LearningContentRepositoryInterface $content) {}

    /**
     * Build a topic-level study plan.
     *
     * @param  array{language?: string|null, family?: string|null, search?: string|null, limit?: int|string|null}  $filters
     * @return array{modules: array<int, array<string, mixed>>, meta: array<string, mixed>}
     */
    public function build(array $filters = []): array
    {
        $limit = $this->normalizeLimit($filters['limit'] ?? null);
        $normalizedFilters = [
            'language' => $filters['language'] ?? 'en',
            'family' => $filters['family'] ?? null,
            'search' => $filters['search'] ?? null,
            'limit' => $limit,
        ];

        $sources = $this->filteredSources($normalizedFilters);
        $questionsBySource = collect($this->content->questions([
            'language' => $normalizedFilters['language'],
            'family' => $normalizedFilters['family'],
            'search' => $normalizedFilters['search'],
        ]))->groupBy('source_key');

        $modules = $sources
            ->map(fn (array $source): array => $this->toModule($source, $questionsBySource->get($source['key'], collect())))
            ->filter(fn (array $module): bool => $module['item_count'] > 0)
            ->sortByDesc('item_count')
            ->take($limit)
            ->values()
            ->map(fn (array $module, int $index): array => $module + ['step' => $index + 1])
            ->all();

        return [
            'modules' => $modules,
            'meta' => [
                'filters' => $normalizedFilters,
                'available_sources' => $sources->count(),
                'count' => count($modules),
                'estimated_minutes' => collect($modules)->sum('estimated_minutes'),
            ],
        ];
    }

    /**
     * Return sources matching the requested plan filters.
     *
     * @param  array{language?: string|null, family?: string|null, search?: string|null, limit?: int}  $filters
     * @return Collection<int, array<string, mixed>>
     */
    private function filteredSources(array $filters): Collection
    {
        $search = mb_strtolower(trim((string) ($filters['search'] ?? '')));

        return collect($this->content->sources())
            ->when($filters['language'] ?? null, fn (Collection $sources, string $language) => $sources->where('language', $language))
            ->when($filters['family'] ?? null, fn (Collection $sources, string $family) => $sources->where('family', $family))
            ->when($search !== '', function (Collection $sources) use ($search): Collection {
                return $sources->filter(function (array $source) use ($search): bool {
                    $haystack = mb_strtolower(implode(' ', [
                        $source['title'] ?? '',
                        $source['path'] ?? '',
                        $source['family'] ?? '',
                        $source['topic'] ?? '',
                    ]));

                    return str_contains($haystack, $search);
                });
            })
            ->values();
    }

    /**
     * Convert one source into a study-plan module.
     *
     * @param  Collection<int, array<string, mixed>>  $questions
     * @return array<string, mixed>
     */
    private function toModule(array $source, Collection $questions): array
    {
        $itemCount = $questions->count();

        return [
            'source_key' => $source['key'],
            'source_path' => $source['path'],
            'family' => $source['family'],
            'topic' => $source['topic'],
            'language' => $source['language'],
            'title' => $source['title'],
            'item_count' => $itemCount,
            'estimated_minutes' => $this->estimatedMinutes($itemCount),
            'outcomes' => $questions
                ->pluck('title')
                ->filter()
                ->take(4)
                ->values()
                ->all(),
        ];
    }

    /**
     * Estimate study time from the number of extracted records.
     */
    private function estimatedMinutes(int $itemCount): int
    {
        if ($itemCount <= 0) {
            return 0;
        }

        return max(15, min(120, $itemCount * 3));
    }

    /**
     * Normalize requested module count into an allowed range.
     */
    private function normalizeLimit(int|string|null $limit): int
    {
        $value = is_numeric($limit) ? (int) $limit : self::DEFAULT_LIMIT;

        return max(1, min(self::MAX_LIMIT, $value));
    }
}
