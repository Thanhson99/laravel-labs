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
            'related_workbench' => $item['related_workbench'],
            'pipeline_route' => $this->path("/practice/technology-learning-pipeline/{$technology}", $query),
            'api_pipeline_route' => $this->path("/api/practice/technology-learning-pipeline/{$technology}", $query),
            'code_examples_route' => $this->path("/practice/technology-code-examples/{$technology}", $query),
            'api_code_examples_route' => $this->path("/api/practice/technology-code-examples/{$technology}", $query),
            'implementation_lab_route' => $this->path("/practice/technology-implementation-lab/{$technology}", $query),
            'api_implementation_lab_route' => $this->path("/api/practice/technology-implementation-lab/{$technology}", $query),
            'commit_plan_route' => $this->path("/practice/technology-commit-plan/{$technology}", $query),
            'api_commit_plan_route' => $this->path("/api/practice/technology-commit-plan/{$technology}", $query),
            'portfolio_artifact_route' => $this->path("/practice/technology-portfolio-artifact/{$technology}", $query),
            'api_portfolio_artifact_route' => $this->path("/api/practice/technology-portfolio-artifact/{$technology}", $query),
            'interview_pack_route' => $this->path("/practice/technology-interview-pack/{$technology}", $query),
            'api_interview_pack_route' => $this->path("/api/practice/technology-interview-pack/{$technology}", $query),
            'mastery_checkpoint_route' => $this->path("/practice/technology-mastery-checkpoint/{$technology}", $query),
            'api_mastery_checkpoint_route' => $this->path("/api/practice/technology-mastery-checkpoint/{$technology}", $query),
            'evidence_archive_route' => $this->path("/practice/technology-evidence-archive/{$technology}", $query),
            'api_evidence_archive_route' => $this->path("/api/practice/technology-evidence-archive/{$technology}", $query),
            'workflow_steps' => $this->workflowSteps($technology, $query),
        ];
    }

    /**
     * Build ordered route handoffs for the full technology learning workflow.
     *
     * @return array<int, array{label: string, purpose: string, route: string, api_route: string}>
     */
    private function workflowSteps(string $technology, string $query): array
    {
        return [
            [
                'label' => 'Pipeline',
                'purpose' => 'Understand source coverage, focus checks, and the end-to-end learning path.',
                'route' => $this->path("/practice/technology-learning-pipeline/{$technology}", $query),
                'api_route' => $this->path("/api/practice/technology-learning-pipeline/{$technology}", $query),
            ],
            [
                'label' => 'Code examples',
                'purpose' => 'Inspect generated snippets for the selected source-backed records.',
                'route' => $this->path("/practice/technology-code-examples/{$technology}", $query),
                'api_route' => $this->path("/api/practice/technology-code-examples/{$technology}", $query),
            ],
            [
                'label' => 'Implementation lab',
                'purpose' => 'Turn the source records into ordered implementation phases and verification commands.',
                'route' => $this->path("/practice/technology-implementation-lab/{$technology}", $query),
                'api_route' => $this->path("/api/practice/technology-implementation-lab/{$technology}", $query),
            ],
            [
                'label' => 'Commit plan',
                'purpose' => 'Prepare changed files, evidence checks, review checks, and commit metadata.',
                'route' => $this->path("/practice/technology-commit-plan/{$technology}", $query),
                'api_route' => $this->path("/api/practice/technology-commit-plan/{$technology}", $query),
            ],
            [
                'label' => 'Portfolio artifact',
                'purpose' => 'Convert implementation evidence into a reusable README-style artifact.',
                'route' => $this->path("/practice/technology-portfolio-artifact/{$technology}", $query),
                'api_route' => $this->path("/api/practice/technology-portfolio-artifact/{$technology}", $query),
            ],
            [
                'label' => 'Interview pack',
                'purpose' => 'Practice concise explanations with source, tradeoffs, and verification evidence.',
                'route' => $this->path("/practice/technology-interview-pack/{$technology}", $query),
                'api_route' => $this->path("/api/practice/technology-interview-pack/{$technology}", $query),
            ],
            [
                'label' => 'Mastery checkpoint',
                'purpose' => 'Decide whether to promote the topic or repeat remediation work.',
                'route' => $this->path("/practice/technology-mastery-checkpoint/{$technology}", $query),
                'api_route' => $this->path("/api/practice/technology-mastery-checkpoint/{$technology}", $query),
            ],
            [
                'label' => 'Evidence archive',
                'purpose' => 'Save retrieval keys, proof bundle, and reuse prompts for later sessions.',
                'route' => $this->path("/practice/technology-evidence-archive/{$technology}", $query),
                'api_route' => $this->path("/api/practice/technology-evidence-archive/{$technology}", $query),
            ],
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
