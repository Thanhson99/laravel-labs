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
                    'evidence_to_attach' => $this->evidenceFor($day['focus']),
                ])
                ->all(),
            'evidence_checklist' => [
                'Focused tests or checkpoint evidence for each coding day.',
                'Mentor feedback answers for feedback days.',
                'Portfolio or PR link for the strongest implementation.',
                'One retrospective note naming the next improvement.',
            ],
            'blockers_prompt' => [
                'Which source record was hardest to turn into code?',
                'Which Laravel layer caused the most rework?',
                'Which verification command failed or was skipped?',
            ],
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
    private function evidenceFor(string $focus): string
    {
        return match ($focus) {
            'Build capstone task' => 'Workspace URL, focused test command, and changed files.',
            'Run checkpoint exam' => 'Checkpoint pass criteria and oral review answers.',
            'Answer mentor feedback' => 'Completed mentor action item and before/after note.',
            'Package portfolio evidence' => 'Portfolio entry and source reference.',
            default => 'Retrospective note and one next improvement.',
        };
    }
}
