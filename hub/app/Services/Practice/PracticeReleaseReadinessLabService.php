<?php

declare(strict_types=1);

namespace App\Services\Practice;

final class PracticeReleaseReadinessLabService
{
    /**
     * Build release readiness checklists from refactor tasks.
     */
    public function __construct(
        private readonly PracticeRefactorLabService $refactors,
        private readonly PracticeProgressPayloadService $progressPayload,
    ) {}

    /**
     * Create release readiness artifacts after refactor verification.
     *
     * @param  array{family?: string|null, language?: string|null, search?: string|null, limit?: int|string|null, phase_limit?: int|string|null, tasks_per_phase?: int|string|null, days?: int|string|null}  $filters
     * @return array<string, mixed>
     */
    public function build(array $filters = []): array
    {
        $refactor = $this->refactors->build($filters);

        $releaseItems = collect($refactor['refactor_tasks'])
            ->map(fn (array $task): array => [
                'round' => $task['round'],
                'technology_segment' => $task['technology_segment'],
                'changed_area' => $task['target_file'],
                'release_note' => $this->releaseNoteFor($task),
                'smoke_check' => $this->smokeCheckFor($task),
                'rollback_note' => $this->rollbackNoteFor($task),
                'verification_command' => $task['verification_command'],
                'evidence' => $task['evidence'],
            ])
            ->all();

        return [
            'title' => 'Laravel release readiness practice lab',
            'source_refactor_lab' => [
                'title' => $refactor['title'],
                'technology_coverage' => $refactor['source_bug_fix_lab']['technology_coverage'],
                'task_count' => count($refactor['refactor_tasks']),
            ],
            'release_rules' => [
                'Ship only after focused tests and Pint are green.',
                'Write a release note that names the user-facing behavior or practice workflow.',
                'Add one smoke check that can be run after deployment.',
                'Keep a rollback note for route, API, view, or service changes.',
            ],
            'release_items' => $releaseItems,
            'handoff_checklist' => [
                'Route or API URL to test after release.',
                'Focused test command and result.',
                'Pint result or formatting evidence.',
                'Known risk and rollback note.',
            ],
            'progress_payload' => $this->progressPayload->fromRows(
                $releaseItems,
                fn (array $item): string => sprintf('Release ready: %s', $item['technology_segment'])
            ),
        ];
    }

    /**
     * Create a release note for one refactor task.
     *
     * @param  array{technology_segment: string, refactor_goal: string}  $task
     */
    private function releaseNoteFor(array $task): string
    {
        if ($this->isIdorSegment((string) $task['technology_segment'])) {
            return sprintf(
                'Improved `%s` IDOR object-authorization practice flow: %s',
                $task['technology_segment'],
                $task['refactor_goal'],
            );
        }

        if ($this->isClosureSegment((string) $task['technology_segment'])) {
            return sprintf(
                'Improved `%s` closure practice flow: %s',
                $task['technology_segment'],
                $task['refactor_goal'],
            );
        }

        return sprintf(
            'Improved `%s` practice flow: %s',
            $task['technology_segment'],
            $task['refactor_goal'],
        );
    }

    /**
     * Return a smoke check for one changed area.
     *
     * @param  array{target_file: string, verification_command: string}  $task
     */
    private function smokeCheckFor(array $task): string
    {
        if ($this->isIdorSegment((string) $task['technology_segment'])) {
            return sprintf('Run %s and confirm scoped lookup, object policy, ID-swap denial, and 403 or 404 evidence still appear in the payload.', $task['verification_command']);
        }

        if ($this->isClosureSegment((string) $task['technology_segment'])) {
            return sprintf('Run %s and confirm lexical-scope, captured-binding, and stale-closure evidence still appear in the payload.', $task['verification_command']);
        }

        if (str_contains($task['target_file'], 'routes/')) {
            return 'Run php artisan route:list --path=practice and open the affected route.';
        }

        if (str_contains($task['target_file'], 'views/')) {
            return 'Open the affected practice page and confirm key headings still render.';
        }

        if (str_contains($task['target_file'], 'tests/')) {
            return sprintf('Run %s and confirm the assertion still describes public behavior.', $task['verification_command']);
        }

        return sprintf('Run %s and inspect the API payload shape.', $task['verification_command']);
    }

    /**
     * Create a rollback note for one changed area.
     *
     * @param  array{target_file: string}  $task
     */
    private function rollbackNoteFor(array $task): string
    {
        if ($this->isIdorSegment((string) $task['technology_segment'])) {
            return sprintf(
                'If IDOR denial evidence disappears, revert the scoped change in `%s` and rerun the object-authorization verification command.',
                $task['target_file'],
            );
        }

        if ($this->isClosureSegment((string) $task['technology_segment'])) {
            return sprintf(
                'If closure evidence disappears, revert the scoped change in `%s` and rerun the closure-focused verification command.',
                $task['target_file'],
            );
        }

        return sprintf(
            'If the smoke check fails, revert the scoped change in `%s` and rerun the focused verification command.',
            $task['target_file'],
        );
    }

    /**
     * Determine whether a task belongs to JavaScript closure practice.
     */
    private function isClosureSegment(string $technology): bool
    {
        return str_contains($technology, 'javascript-closures');
    }

    /**
     * Determine whether a task belongs to IDOR object-authorization practice.
     */
    private function isIdorSegment(string $technology): bool
    {
        return str_contains($technology, 'idor-access-control');
    }
}
