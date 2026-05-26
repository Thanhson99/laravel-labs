<?php

declare(strict_types=1);

namespace App\Services\Practice;

final class PracticeSprintService
{
    /**
     * Compose a runnable sprint from the content-backed syllabus and queues.
     */
    public function __construct(
        private readonly ContentPracticeSyllabusService $syllabus,
        private readonly PracticeQueueService $queues,
        private readonly PracticeProgressPayloadService $progressPayload,
        private readonly PracticeFilterNormalizerService $filters,
    ) {}

    /**
     * Build a short code-first sprint across technology phases.
     *
     * @param  array{family?: string|null, language?: string|null, search?: string|null, limit?: int|string|null, phase_limit?: int|string|null, tasks_per_phase?: int|string|null}  $filters
     * @return array{title: string, phases: array<int, array<string, mixed>>, meta: array<string, mixed>, progress_payload: array{items: array<int, array{label: string, done: bool}>}}
     */
    public function build(array $filters = []): array
    {
        $phaseLimit = $this->filters->positiveInt($filters['phase_limit'] ?? null, 2);
        $tasksPerPhase = $this->filters->positiveInt($filters['tasks_per_phase'] ?? null, 2);

        $syllabus = $this->syllabus->build([
            'family' => $filters['family'] ?? 'laravel',
            'language' => $filters['language'] ?? 'en',
            'search' => $filters['search'] ?? 'api',
            'limit' => $filters['limit'] ?? 5,
        ]);

        $phases = collect($syllabus['phases'])
            ->take($phaseLimit)
            ->map(function (array $phase) use ($filters, $tasksPerPhase): array {
                $queue = $this->queues->build([
                    'family' => $filters['family'] ?? 'laravel',
                    'language' => $filters['language'] ?? 'en',
                    'search' => $filters['search'] ?? 'api',
                    'technology' => $phase['technology'],
                    'limit' => $tasksPerPhase,
                ]);

                return [
                    'phase' => $phase['phase'],
                    'technology' => $phase['technology'],
                    'title' => $phase['title'],
                    'exercise' => $phase['exercise'],
                    'board_query' => [
                        'technology' => $phase['technology'],
                        'family' => $filters['family'] ?? 'laravel',
                        'language' => $filters['language'] ?? 'en',
                        'search' => $filters['search'] ?? 'api',
                    ],
                    'queue_query' => array_merge($phase['queue_query'], ['limit' => $tasksPerPhase]),
                    'tasks' => collect($queue['items'])
                        ->map(fn (array $task): array => [
                            'position' => $task['position'],
                            'record_id' => $task['record_id'],
                            'question' => $task['question'],
                            'source' => $task['source'],
                            'workspace_query' => $task['workspace_query'],
                            'verification_query' => $task['workspace_query'],
                            'estimated_minutes' => $task['estimated_minutes'],
                        ])
                        ->all(),
                    'estimated_minutes' => $queue['meta']['estimated_minutes'],
                ];
            })
            ->values()
            ->all();

        return [
            'title' => 'Content-backed Laravel practice sprint',
            'phases' => $phases,
            'meta' => [
                'filters' => [
                    'family' => $filters['family'] ?? 'laravel',
                    'language' => $filters['language'] ?? 'en',
                    'search' => $filters['search'] ?? 'api',
                    'limit' => $filters['limit'] ?? 5,
                    'phase_limit' => $phaseLimit,
                    'tasks_per_phase' => $tasksPerPhase,
                ],
                'phase_count' => count($phases),
                'task_count' => collect($phases)->sum(fn (array $phase): int => count($phase['tasks'])),
                'estimated_minutes' => collect($phases)->sum('estimated_minutes'),
            ],
            'progress_payload' => $this->progressPayload->fromLabels(
                collect($phases)
                    ->flatMap(fn (array $phase): array => collect($phase['tasks'])
                        ->map(fn (array $task): string => sprintf('Phase %d: %s', $phase['phase'], $task['question']))
                        ->all())
            ),
        ];
    }
}
