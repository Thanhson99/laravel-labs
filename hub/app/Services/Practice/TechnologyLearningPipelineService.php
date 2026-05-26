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
        $stages = $this->stagesFor($technology, $filters);

        return [
            'title' => sprintf('Technology Learning Pipeline: %s', $technology),
            'technology' => $technology,
            'record_count' => $mapped['meta']['count'],
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
     * @return array<int, array{step: int, label: string, purpose: string, route: string, api_route: string}>
     */
    private function stagesFor(string $technology, array $filters): array
    {
        $query = http_build_query(array_filter($filters, fn (int|string|null $value): bool => $value !== null && $value !== ''));

        return collect([
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
        ])
            ->map(fn (array $stage, int $index): array => [
                'step' => $index + 1,
                'label' => $stage[0],
                'purpose' => $stage[1],
                'route' => $this->path('/practice/'.$stage[2], $query),
                'api_route' => $this->path('/api/practice/'.$stage[2], $query),
            ])
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
