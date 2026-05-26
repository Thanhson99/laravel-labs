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
        private readonly PracticeCatalogService $catalog,
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
        $exercise = $this->exerciseFor($technology);

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
            'practice' => [
                'slug' => $exercise['slug'] ?? null,
                'title' => $exercise['title'] ?? 'Create a new practice exercise',
                'track' => $exercise['track'] ?? $technology,
            ],
            'drill_query' => [
                'record_id' => $sample['id'],
                'source_key' => $sample['source_key'],
                'technology' => $technology,
            ],
        ];
    }

    /**
     * Find the native practice exercise for one technology.
     *
     * @return array<string, mixed>|null
     */
    private function exerciseFor(string $technology): ?array
    {
        $slug = match ($technology) {
            'php' => 'php-cli-input-normalizer',
            'api-validation' => 'api-form-request-slice',
            'testing-quality' => 'feature-test-route-behavior',
            'docker-runtime' => 'docker-compose-smoke-check',
            default => 'laravel-thin-controller',
        };

        return $this->catalog->findExercise($slug);
    }
}
