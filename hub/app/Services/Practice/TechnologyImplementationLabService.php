<?php

declare(strict_types=1);

namespace App\Services\Practice;

final class TechnologyImplementationLabService
{
    /**
     * Create implementation labs from technology-specific code examples.
     */
    public function __construct(
        private readonly TechnologyCodeExampleService $examples,
        private readonly PracticeProgressPayloadService $progressPayload,
    ) {}

    /**
     * Build a sequential implementation lab for one inferred technology.
     *
     * @param  array{family?: string|null, language?: string|null, search?: string|null, limit?: int|string|null}  $filters
     * @return array<string, mixed>
     */
    public function build(string $technology, array $filters = []): array
    {
        $examples = $this->examples->buildForTechnology($technology, $filters + ['limit' => 5]);
        $tasks = collect($examples['items'])
            ->take(5)
            ->map(fn (array $item, int $index): array => $this->taskFromExample($item, $index + 1))
            ->values()
            ->all();

        $phases = $this->phasesFor($technology, $tasks);

        return [
            'title' => sprintf('Technology Implementation Lab: %s', $technology),
            'technology' => $technology,
            'source_examples' => $examples,
            'phases' => $phases,
            'commands' => $this->commandsFor($technology),
            'progress_payload' => $this->progressPayload->fromRows(
                $phases,
                fn (array $phase): string => $phase['label'],
            ),
            'progress_api' => '/api/practice/progress-checklist',
            'meta' => [
                'filters' => $examples['meta']['filters'],
                'task_count' => count($tasks),
            ],
        ];
    }

    /**
     * Convert one code example into an implementation task.
     *
     * @return array<string, mixed>
     */
    private function taskFromExample(array $item, int $step): array
    {
        return [
            'step' => $step,
            'record_id' => $item['record_id'],
            'title' => $item['content']['title'],
            'source_path' => $item['source']['path'],
            'task' => $item['task'],
            'workspace_query' => $item['workspace_query'],
            'files' => collect($item['snippets'])
                ->pluck('file')
                ->unique()
                ->values()
                ->all(),
        ];
    }

    /**
     * Build lab phases for the selected technology.
     *
     * @param  array<int, array<string, mixed>>  $tasks
     * @return array<int, array<string, mixed>>
     */
    private function phasesFor(string $technology, array $tasks): array
    {
        return [
            [
                'label' => 'Read source records',
                'goal' => sprintf('Understand the JSON records currently mapped to %s.', $technology),
                'tasks' => collect($tasks)
                    ->map(fn (array $task): string => sprintf('Read %s from %s.', $task['title'], $task['source_path']))
                    ->all(),
            ],
            [
                'label' => 'Create focused implementation files',
                'goal' => 'Create the smallest Laravel files needed for the selected records.',
                'tasks' => collect($tasks)
                    ->map(fn (array $task): string => sprintf('Implement step %d using: %s.', $task['step'], implode(', ', $task['files'])))
                    ->all(),
            ],
            [
                'label' => 'Connect route, service, and verification',
                'goal' => 'Wire the example into a route or API path and keep controller logic thin.',
                'tasks' => collect($tasks)
                    ->map(fn (array $task): string => sprintf('Open the workspace for %s and verify its route, service, and test plan.', $task['record_id']))
                    ->all(),
            ],
            [
                'label' => 'Run focused checks',
                'goal' => 'Prove the implementation works before moving to the next content record.',
                'tasks' => $this->commandsFor($technology),
            ],
        ];
    }

    /**
     * Return verification commands for one inferred technology.
     *
     * @return array<int, string>
     */
    private function commandsFor(string $technology): array
    {
        return match ($technology) {
            'php' => [
                'php artisan test --filter ContentBackedNormalizer',
                'php artisan test --filter NameNormalizer',
            ],
            'api-validation' => [
                'php artisan test --filter ContentBackedApiDrill',
                'php artisan route:list --path=api/practice',
            ],
            'auth-security' => [
                'php artisan test --filter Authorization',
                'php artisan route:list --path=practice',
            ],
            'database-eloquent' => [
                'php artisan test --filter Database',
                'php artisan migrate:status',
            ],
            'files-media' => [
                'php artisan test --filter FileStorage',
                'php artisan storage:link',
            ],
            'async-workflow' => [
                'php artisan test --filter Event',
                'php artisan queue:work --once',
            ],
            'performance-cache' => [
                'php artisan test --filter Cache',
                'php artisan cache:clear',
            ],
            default => [
                'php artisan test --filter TechnologyImplementationLab',
                'php artisan route:list --path=practice',
                'vendor\\bin\\pint --test',
            ],
        };
    }
}
