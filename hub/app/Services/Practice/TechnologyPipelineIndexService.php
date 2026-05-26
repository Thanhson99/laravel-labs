<?php

declare(strict_types=1);

namespace App\Services\Practice;

final class TechnologyPipelineIndexService
{
    /**
     * Create a discoverable index of technology learning pipelines.
     */
    public function __construct(
        private readonly TechnologyPracticeMatrixService $matrix,
    ) {}

    /**
     * Build technology pipeline cards from the JSON-backed technology matrix.
     *
     * @param  array{family?: string|null, language?: string|null, search?: string|null}  $filters
     * @return array{items: array<int, array<string, mixed>>, meta: array<string, mixed>}
     */
    public function build(array $filters = []): array
    {
        $matrix = $this->matrix->build($filters + ['language' => 'en']);
        $queryFilters = $this->queryFilters($matrix['meta']['filters']);

        $items = collect($matrix['items'])
            ->map(fn (array $item): array => $this->toPipelineItem($item, $queryFilters))
            ->values()
            ->all();

        return [
            'items' => $items,
            'meta' => [
                ...$matrix['meta'],
                'query' => $queryFilters,
                'pipeline_count' => count($items),
            ],
        ];
    }

    /**
     * Convert one matrix row into a pipeline index item.
     *
     * @param  array<string, mixed>  $item
     * @param  array<string, int|string>  $queryFilters
     * @return array<string, mixed>
     */
    private function toPipelineItem(array $item, array $queryFilters): array
    {
        $technology = (string) $item['technology'];
        $query = http_build_query($queryFilters);

        return [
            'technology' => $technology,
            'record_count' => $item['record_count'],
            'source_count' => $item['source_count'],
            'families' => $item['families'],
            'sources' => $item['sources'],
            'sample' => $item['sample'],
            'practice' => $item['practice'],
            'pipeline_route' => $this->path("/practice/technology-learning-pipeline/{$technology}", $query),
            'api_pipeline_route' => $this->path("/api/practice/technology-learning-pipeline/{$technology}", $query),
            'code_examples_route' => $this->path("/practice/technology-code-examples/{$technology}", $query),
        ];
    }

    /**
     * Keep only filled filters that should travel to pipeline links.
     *
     * @param  array<string, mixed>  $filters
     * @return array<string, int|string>
     */
    private function queryFilters(array $filters): array
    {
        return collect($filters)
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
}
