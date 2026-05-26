<?php

declare(strict_types=1);

namespace App\Services\Practice;

final class PracticeQueueService
{
    /**
     * Create a queue of record-level practice workspaces.
     */
    public function __construct(
        private readonly QuestionDrillSetService $drillSets,
        private readonly PracticeProgressPayloadService $progressPayload,
    ) {}

    /**
     * Build an ordered queue from filtered question records.
     *
     * @param  array{family?: string|null, language?: string|null, search?: string|null, technology?: string|null, limit?: int|string|null}  $filters
     * @return array{items: array<int, array<string, mixed>>, meta: array<string, mixed>, progress_payload: array{items: array<int, array{label: string, done: bool}>}}
     */
    public function build(array $filters = []): array
    {
        $drills = $this->drillSets->build($filters);

        $items = collect($drills['items'])
            ->map(fn (array $item): array => [
                'position' => $item['position'],
                'record_id' => $item['record_id'],
                'question' => $item['question'],
                'technology' => $item['technology'],
                'source' => $item['source'],
                'practice' => $item['practice'],
                'task' => $item['task'],
                'workspace_query' => $item['drill_query'],
                'estimated_minutes' => $this->estimatedMinutes($item['technology']),
            ])
            ->values()
            ->all();

        return [
            'items' => $items,
            'meta' => $drills['meta'] + [
                'estimated_minutes' => collect($items)->sum('estimated_minutes'),
            ],
            'progress_payload' => $this->progressPayload->fromRows(
                $items,
                fn (array $item): string => sprintf('#%d %s', $item['position'], $item['question'])
            ),
        ];
    }

    /**
     * Estimate practice time by technology.
     */
    private function estimatedMinutes(string $technology): int
    {
        return match ($technology) {
            'api-validation' => 35,
            'testing-quality' => 25,
            'docker-runtime' => 30,
            'php' => 20,
            default => 30,
        };
    }
}
