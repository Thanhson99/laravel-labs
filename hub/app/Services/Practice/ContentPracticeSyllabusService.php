<?php

declare(strict_types=1);

namespace App\Services\Practice;

final class ContentPracticeSyllabusService
{
    /**
     * Create a content-backed practice syllabus.
     */
    public function __construct(
        private readonly TechnologyPracticeMatrixService $matrix,
        private readonly SourcePracticePackIndexService $sourceIndex,
    ) {}

    /**
     * Build a syllabus from content coverage and source packs.
     *
     * @param  array{family?: string|null, language?: string|null, search?: string|null, limit?: int|string|null}  $filters
     * @return array{phases: array<int, array<string, mixed>>, source_packs: array<int, array<string, mixed>>, meta: array<string, mixed>}
     */
    public function build(array $filters = []): array
    {
        $matrix = $this->matrix->build([
            'family' => $filters['family'] ?? 'laravel',
            'language' => $filters['language'] ?? 'en',
            'search' => $filters['search'] ?? null,
        ]);

        $sourcePacks = $this->sourceIndex->build([
            'family' => $filters['family'] ?? 'laravel',
            'language' => $filters['language'] ?? 'en',
            'search' => $filters['search'] ?? null,
            'limit' => $filters['limit'] ?? 10,
        ]);

        $phases = collect($matrix['items'])
            ->values()
            ->map(fn (array $item, int $index): array => [
                'phase' => $index + 1,
                'technology' => $item['technology'],
                'title' => sprintf('Practice %s through Laravel code', $item['technology']),
                'record_count' => $item['record_count'],
                'source_count' => $item['source_count'],
                'exercise' => $item['practice'],
                'sample_drill_query' => $item['drill_query'],
                'queue_query' => [
                    'technology' => $item['technology'],
                    'family' => $filters['family'] ?? 'laravel',
                    'language' => $filters['language'] ?? 'en',
                    'search' => $filters['search'] ?? null,
                    'limit' => 5,
                ],
                'done_when' => [
                    'Open one source pack for this technology.',
                    'Complete at least one record workspace.',
                    'Run the generated verification plan.',
                ],
            ])
            ->all();

        return [
            'phases' => $phases,
            'source_packs' => $sourcePacks['items'],
            'meta' => [
                'filters' => $sourcePacks['meta']['filters'],
                'phase_count' => count($phases),
                'source_pack_count' => count($sourcePacks['items']),
                'record_count' => $matrix['meta']['record_count'],
            ],
        ];
    }
}
