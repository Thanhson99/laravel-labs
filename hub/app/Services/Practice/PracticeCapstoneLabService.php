<?php

declare(strict_types=1);

namespace App\Services\Practice;

final class PracticeCapstoneLabService
{
    /**
     * Build technology-level capstone labs from boards and queues.
     */
    public function __construct(
        private readonly TechnologyPracticeBoardService $boards,
        private readonly PracticeQueueService $queues,
        private readonly PracticeProgressPayloadService $progressPayload,
        private readonly PracticeFilterNormalizerService $filters,
    ) {}

    /**
     * Create a capstone lab for one technology and content filter.
     *
     * @param  array{technology?: string|null, family?: string|null, language?: string|null, search?: string|null, limit?: int|string|null}  $filters
     * @return array<string, mixed>
     */
    public function build(array $filters = []): array
    {
        $technology = $filters['technology'] ?? 'api-validation';
        $limit = $this->filters->positiveInt($filters['limit'] ?? null, 3);

        $board = $this->boards->build([
            'technology' => $technology,
            'family' => $filters['family'] ?? 'laravel',
            'language' => $filters['language'] ?? 'en',
            'search' => $filters['search'] ?? 'api',
            'limit' => 50,
        ]);

        $queue = $this->queues->build([
            'technology' => $technology,
            'family' => $filters['family'] ?? 'laravel',
            'language' => $filters['language'] ?? 'en',
            'search' => $filters['search'] ?? 'api',
            'limit' => $limit,
        ]);

        $tasks = collect($queue['items'])
            ->map(fn (array $item): array => [
                'position' => $item['position'],
                'record_id' => $item['record_id'],
                'question' => $item['question'],
                'source' => $item['source'],
                'workspace_query' => $item['workspace_query'],
                'estimated_minutes' => $item['estimated_minutes'],
                'deliverable' => $this->deliverableFor($technology, (string) $item['question']),
            ])
            ->values()
            ->all();

        return [
            'title' => sprintf('Capstone Lab: %s', $technology),
            'technology' => $technology,
            'mission' => $this->missionFor($technology, count($tasks)),
            'source_coverage' => collect($board['sources'])
                ->take(5)
                ->map(fn (array $source): array => [
                    'path' => $source['source']['path'],
                    'title' => $source['source']['title'],
                    'record_count' => $source['record_count'],
                    'sample_workspace_query' => $source['sample_records'][0]['workspace_query'] ?? null,
                ])
                ->values()
                ->all(),
            'tasks' => $tasks,
            'deliverables' => $this->deliverablesFor($technology),
            'artifact_queries' => collect($tasks)
                ->map(fn (array $task): array => [
                    'record_id' => $task['record_id'],
                    'workspace_query' => $task['workspace_query'],
                    'tdd_query' => $task['workspace_query'],
                    'portfolio_query' => $task['workspace_query'],
                ])
                ->all(),
            'meta' => [
                'filters' => [
                    'technology' => $technology,
                    'family' => $filters['family'] ?? 'laravel',
                    'language' => $filters['language'] ?? 'en',
                    'search' => $filters['search'] ?? 'api',
                    'limit' => $limit,
                ],
                'source_count' => $board['meta']['source_count'],
                'record_count' => $board['meta']['record_count'],
                'task_count' => count($tasks),
                'estimated_minutes' => collect($tasks)->sum('estimated_minutes'),
            ],
            'progress_payload' => $this->progressPayload->fromLabels(
                collect($tasks)
                    ->map(fn (array $task): string => sprintf('Complete capstone task %d', $task['position']))
                    ->push('Package strongest task as portfolio evidence')
            ),
        ];
    }

    /**
     * Build a capstone mission tuned to the selected technology.
     */
    private function missionFor(string $technology, int $taskCount): string
    {
        if ($technology === 'llm-foundations') {
            return sprintf('Build an AI explanation capstone from %d `%s` records and package output contracts, metrics, and failure-mode evidence for review.', $taskCount, $technology);
        }

        if ($technology === 'javascript-closures') {
            return sprintf('Build a JavaScript closure interview capstone from %d `%s` records and package lexical-scope traces, code examples, and trap evidence for review.', $taskCount, $technology);
        }

        return sprintf('Build a small Laravel capstone from %d `%s` records and package the work as reviewable evidence.', $taskCount, $technology);
    }

    /**
     * Build the record-level deliverable for one capstone task.
     */
    private function deliverableFor(string $technology, string $question): string
    {
        if ($technology === 'llm-foundations' && AiTypeComparisonTopicService::matchesText($question)) {
            return sprintf('Create and verify an AI type comparison table for `%s` with output contract, input evidence, metric, and failure mode.', $question);
        }

        if ($technology === 'javascript-closures') {
            return sprintf('Create and verify a JavaScript closure explanation for `%s` with lexical scope, captured binding, createCounter(), and interview trap evidence.', $question);
        }

        return sprintf('Implement and verify a Laravel slice for `%s`.', $question);
    }

    /**
     * Return capstone-level deliverables.
     *
     * @return array<int, string>
     */
    private function deliverablesFor(string $technology): array
    {
        if ($technology === 'llm-foundations') {
            return [
                'Complete each record workspace in the task list.',
                'Produce one Predictive AI versus Generative AI comparison table.',
                'Attach predictive metrics, generative quality checks, and failure-mode evidence.',
                'Create one assessment and retrospective before moving to the next technology.',
            ];
        }

        if ($technology === 'javascript-closures') {
            return [
                'Complete each closure workspace in the task list.',
                'Produce one lexical-scope trace that names the outer scope, inner function, and captured binding.',
                'Attach createCounter(), private state, var versus let, and stale-closure evidence.',
                'Create one interview answer and retrospective before moving to the next technology.',
            ];
        }

        return [
            'Complete each record workspace in the task list.',
            'Run a verification plan for each task.',
            'Create one PR lab artifact for the strongest task.',
            'Create one assessment and retrospective before moving to the next technology.',
        ];
    }
}
