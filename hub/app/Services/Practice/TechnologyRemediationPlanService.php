<?php

declare(strict_types=1);

namespace App\Services\Practice;

final class TechnologyRemediationPlanService
{
    /**
     * Create remediation plans from scored technology assessments.
     */
    public function __construct(
        private readonly TechnologySkillAssessmentService $assessments,
        private readonly PracticeProgressPayloadService $progressPayload,
    ) {}

    /**
     * Build a remediation plan for one inferred technology.
     *
     * @param  array{family?: string|null, language?: string|null, search?: string|null, limit?: int|string|null}  $filters
     * @return array<string, mixed>
     */
    public function build(string $technology, array $filters = []): array
    {
        $assessment = $this->assessments->build($technology, $filters);
        $tasks = collect($assessment['rubric'])
            ->map(fn (array $row, int $index): array => $this->taskFromRubric($technology, $row, $index + 1))
            ->values()
            ->all();

        return [
            'title' => sprintf('Technology Remediation Plan: %s', $technology),
            'technology' => $technology,
            'assessment' => $assessment,
            'tasks' => $tasks,
            'commands' => $this->commandsFor($technology),
            'next_routes' => [
                'implementation_lab' => sprintf('/practice/technology-implementation-lab/%s', $technology),
                'interview_pack' => sprintf('/practice/technology-interview-pack/%s', $technology),
                'skill_assessment' => sprintf('/practice/technology-skill-assessment/%s', $technology),
            ],
            'progress_payload' => $this->progressPayload->fromRows(
                $tasks,
                fn (array $task): string => $task['label'],
            ),
        ];
    }

    /**
     * Convert one rubric criterion into a concrete remediation task.
     *
     * @return array{step: int, label: string, problem: string, action: string, evidence: string, focus_file: string}
     */
    private function taskFromRubric(string $technology, array $row, int $step): array
    {
        return [
            'step' => $step,
            'label' => sprintf('Repair %s', $row['criterion']),
            'problem' => $row['pass_signal'],
            'action' => $this->actionFor($technology, (string) $row['criterion']),
            'evidence' => $row['evidence'],
            'focus_file' => $this->focusFileFor((string) $row['criterion']),
        ];
    }

    /**
     * Build a remediation action for one assessment criterion.
     */
    private function actionFor(string $technology, string $criterion): string
    {
        return match ($criterion) {
            'Source understanding' => sprintf('Reopen the `%s` source records and write a one-paragraph reason why each record maps to this technology.', $technology),
            'Laravel layer placement' => 'Trace the route, controller, request, service, and test files; move misplaced behavior into the correct layer.',
            'Implementation evidence' => 'Open the changed files list and add or adjust the smallest code change that proves implementation happened in Laravel code.',
            'Verification discipline' => 'Run each verification command and record what behavior the command proves.',
            default => 'Practice a two-minute explanation that names the source record, changed files, commands, and tradeoffs.',
        };
    }

    /**
     * Choose the likely file area for one remediation criterion.
     */
    private function focusFileFor(string $criterion): string
    {
        return match ($criterion) {
            'Source understanding' => 'data/**/*.json and record workspace source panel',
            'Laravel layer placement' => 'routes/**, app/Http/Controllers/**, app/Services/**',
            'Implementation evidence' => 'generated snippet files and changed-file list',
            'Verification discipline' => 'tests/** and route-list output',
            default => 'portfolio artifact and interview pack',
        };
    }

    /**
     * Return commands for remediation verification.
     *
     * @return array<int, string>
     */
    private function commandsFor(string $technology): array
    {
        return [
            sprintf('php artisan test --filter TechnologySkillAssessmentTest --filter=%s', $technology),
            sprintf('php artisan route:list --path=technology-%s', str_replace('_', '-', $technology)),
            'vendor\\bin\\pint --test',
        ];
    }
}
