<?php

declare(strict_types=1);

namespace App\Services\Practice;

final class PracticeRefactorLabService
{
    /**
     * Build refactor drills from bug-fix drills.
     */
    public function __construct(
        private readonly PracticeBugFixLabService $bugFixes,
        private readonly PracticeProgressPayloadService $progressPayload,
    ) {}

    /**
     * Create refactor tasks after bug fixes are verified.
     *
     * @param  array{family?: string|null, language?: string|null, search?: string|null, limit?: int|string|null, phase_limit?: int|string|null, tasks_per_phase?: int|string|null, days?: int|string|null}  $filters
     * @return array<string, mixed>
     */
    public function build(array $filters = []): array
    {
        $bugFix = $this->bugFixes->build($filters);

        $tasks = collect($bugFix['bug_drills'])
            ->map(fn (array $drill): array => [
                'round' => $drill['round'],
                'technology_segment' => $drill['technology_segment'],
                'refactor_goal' => $this->goalFor($drill),
                'target_file' => $drill['patch_target'],
                'safe_steps' => $this->safeStepsFor($drill),
                'guardrails' => $this->guardrailsFor($drill),
                'verification_command' => $drill['verification_command'],
                'evidence' => $drill['evidence'],
            ])
            ->all();

        return [
            'title' => 'Laravel refactor practice lab',
            'source_bug_fix_lab' => [
                'title' => $bugFix['title'],
                'technology_coverage' => $bugFix['source_session']['technology_coverage'],
                'drill_count' => count($bugFix['bug_drills']),
            ],
            'refactor_rules' => [
                'Refactor only after the bug-fix verification command is green.',
                'Keep public route, API shape, and visible text stable unless the task says otherwise.',
                'Move behavior toward the Laravel layer that owns it.',
                'Run the same focused command after every small refactor.',
            ],
            'refactor_tasks' => $tasks,
            'architecture_checks' => [
                'Controller stays thin and delegates behavior.',
                'Service owns workflow and transformation logic.',
                'Blade renders prepared data without hidden business logic.',
                'Feature test asserts behavior, not private implementation details.',
            ],
            'progress_payload' => $this->progressPayload->fromRows(
                $tasks,
                fn (array $task): string => sprintf('Refactored %s', $task['technology_segment'])
            ),
        ];
    }

    /**
     * Return the refactor goal for one fixed bug.
     *
     * @param  array{patch_target: string, technology_segment: string}  $drill
     */
    private function goalFor(array $drill): string
    {
        return sprintf(
            'Improve the `%s` implementation without changing the verified behavior for `%s`.',
            $drill['patch_target'],
            $drill['technology_segment'],
        );
    }

    /**
     * Return safe refactor steps for one target.
     *
     * @param  array{patch_target: string}  $drill
     * @return array<int, string>
     */
    private function safeStepsFor(array $drill): array
    {
        return [
            'Run the verification command before editing.',
            sprintf('Open `%s` and identify one small readability or layering improvement.', $drill['patch_target']),
            'Make one scoped change without renaming public routes or response keys.',
            'Rerun the same verification command and compare evidence.',
        ];
    }

    /**
     * Return architecture guardrails for one refactor target.
     *
     * @param  array{patch_target: string}  $drill
     * @return array<int, string>
     */
    private function guardrailsFor(array $drill): array
    {
        if (str_contains($drill['patch_target'], 'routes/')) {
            return [
                'Keep route names stable.',
                'Do not put business logic in the route file.',
                'Keep controller imports explicit.',
            ];
        }

        if (str_contains($drill['patch_target'], 'tests/')) {
            return [
                'Assert public behavior and JSON shape.',
                'Keep test names behavior-focused.',
                'Avoid asserting private method details.',
            ];
        }

        if (str_contains($drill['patch_target'], 'views/')) {
            return [
                'Escape user-facing output through Blade echo.',
                'Do not calculate business rules in Blade.',
                'Keep section headings stable for feature tests.',
            ];
        }

        return [
            'Keep transformation logic in services.',
            'Use explicit array keys for API payloads.',
            'Split helpers only when they reduce real complexity.',
        ];
    }
}
