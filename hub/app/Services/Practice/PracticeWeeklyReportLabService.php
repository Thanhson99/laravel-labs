<?php

declare(strict_types=1);

namespace App\Services\Practice;

final class PracticeWeeklyReportLabService
{
    /**
     * Build weekly reports from practice rotations.
     */
    public function __construct(
        private readonly PracticeRotationLabService $rotations,
        private readonly PracticeProgressPayloadService $progressPayload,
    ) {}

    /**
     * Create a weekly report template from a day-by-day rotation.
     *
     * @param  array{family?: string|null, language?: string|null, search?: string|null, limit?: int|string|null, phase_limit?: int|string|null, tasks_per_phase?: int|string|null, days?: int|string|null}  $filters
     * @return array<string, mixed>
     */
    public function build(array $filters = []): array
    {
        $rotation = $this->rotations->build($filters);

        $technologies = collect($rotation['schedule'])
            ->pluck('technology')
            ->unique()
            ->values()
            ->all();

        return [
            'title' => 'Laravel practice weekly report',
            'rotation' => [
                'title' => $rotation['title'],
                'day_count' => $rotation['meta']['day_count'],
                'milestone_count' => $rotation['meta']['milestone_count'],
            ],
            'technology_coverage' => $technologies,
            'daily_outputs' => collect($rotation['schedule'])
                ->map(fn (array $day): array => [
                    'day' => $day['day'],
                    'technology' => $day['technology'],
                    'focus' => $day['focus'],
                    'output' => $day['output'],
                    'evidence_to_attach' => $this->evidenceFor($day['focus'], (string) $day['technology']),
                ])
                ->all(),
            'evidence_checklist' => $this->evidenceChecklistFor($technologies),
            'blockers_prompt' => $this->blockersPromptFor($technologies),
            'next_week_plan' => collect($technologies)
                ->map(fn (string $technology): string => sprintf('Repeat one capstone and one checkpoint for `%s` with stricter evidence.', $technology))
                ->all(),
            'progress_payload' => $this->progressPayload->fromLabels([
                'Attach daily outputs',
                'Attach verification evidence',
                'Write blockers and lessons',
                'Choose next week practice plan',
            ]),
        ];
    }

    /**
     * Return evidence to attach for one focus type.
     */
    private function evidenceFor(string $focus, string $technology): string
    {
        if ($technology === 'llm-foundations') {
            return match ($focus) {
                'Build capstone task' => 'AI type comparison table, metric choices, and source record.',
                'Run checkpoint exam' => 'Checkpoint output-contract, metric, and failure-mode evidence.',
                'Answer mentor feedback' => 'Completed mentor action item for predictive and generative evidence.',
                'Package portfolio evidence' => 'Portfolio entry with Predictive AI versus Generative AI proof.',
                default => 'Retrospective note naming one AI evaluation improvement.',
            };
        }

        if ($technology === 'javascript-closures') {
            return match ($focus) {
                'Build capstone task' => 'Closure lexical-scope trace, createCounter() output, and source record.',
                'Run checkpoint exam' => 'Checkpoint captured-binding, var-versus-let, and stale-closure evidence.',
                'Answer mentor feedback' => 'Completed mentor action item for closure interview traps.',
                'Package portfolio evidence' => 'Portfolio entry with JavaScript closure proof.',
                default => 'Retrospective note naming one closure explanation improvement.',
            };
        }

        return match ($focus) {
            'Build capstone task' => 'Workspace URL, focused test command, and changed files.',
            'Run checkpoint exam' => 'Checkpoint pass criteria and oral review answers.',
            'Answer mentor feedback' => 'Completed mentor action item and before/after note.',
            'Package portfolio evidence' => 'Portfolio entry and source reference.',
            default => 'Retrospective note and one next improvement.',
        };
    }

    /**
     * Return evidence checklist items for the weekly report.
     *
     * @param  array<int, string>  $technologies
     * @return array<int, string>
     */
    private function evidenceChecklistFor(array $technologies): array
    {
        if (in_array('llm-foundations', $technologies, true)) {
            return [
                'AI type comparison table for each AI-focused coding day.',
                'Predictive metrics and generative quality checks for checkpoint days.',
                'Mentor feedback answers for failure-mode and evidence gaps.',
                'One retrospective note naming the next AI evaluation improvement.',
            ];
        }

        if (in_array('javascript-closures', $technologies, true)) {
            return [
                'Lexical-scope trace for each closure-focused coding day.',
                'createCounter(), private state, var versus let, and stale-closure evidence for checkpoint days.',
                'Mentor feedback answers for closure interview-trap gaps.',
                'One retrospective note naming the next closure explanation improvement.',
            ];
        }

        return [
            'Focused tests or checkpoint evidence for each coding day.',
            'Mentor feedback answers for feedback days.',
            'Portfolio or PR link for the strongest implementation.',
            'One retrospective note naming the next improvement.',
        ];
    }

    /**
     * Return blocker prompts for the weekly report.
     *
     * @param  array<int, string>  $technologies
     * @return array<int, string>
     */
    private function blockersPromptFor(array $technologies): array
    {
        if (in_array('llm-foundations', $technologies, true)) {
            return [
                'Which source record was hardest to classify as prediction, generation, or both?',
                'Which metric or quality check was weakest?',
                'Which failure mode still needs better evidence?',
            ];
        }

        if (in_array('javascript-closures', $technologies, true)) {
            return [
                'Which source record was hardest to translate into lexical-scope evidence?',
                'Which captured binding, var versus let, or stale-closure example was weakest?',
                'Which practical closure use case still needs better interview evidence?',
            ];
        }

        return [
            'Which source record was hardest to turn into code?',
            'Which Laravel layer caused the most rework?',
            'Which verification command failed or was skipped?',
        ];
    }
}
