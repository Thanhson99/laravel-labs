<?php

declare(strict_types=1);

namespace App\Services\Practice;

final class PracticeDemoScriptLabService
{
    /**
     * Build demo scripts from weekly practice reports.
     */
    public function __construct(private readonly PracticeWeeklyReportLabService $reports) {}

    /**
     * Create a demo script for presenting one content-backed practice week.
     *
     * @param  array{family?: string|null, language?: string|null, search?: string|null, limit?: int|string|null, phase_limit?: int|string|null, tasks_per_phase?: int|string|null, days?: int|string|null}  $filters
     * @return array<string, mixed>
     */
    public function build(array $filters = []): array
    {
        $report = $this->reports->build($filters);
        $dailyOutputs = collect($report['daily_outputs']);

        return [
            'title' => 'Laravel practice demo script',
            'weekly_report' => [
                'title' => $report['title'],
                'day_count' => $report['rotation']['day_count'],
                'technology_coverage' => $report['technology_coverage'],
            ],
            'demo_goal' => 'Present one week of Laravel practice as a repeatable code demo with commands, evidence, and next actions.',
            'script_steps' => $dailyOutputs
                ->map(fn (array $output): array => [
                    'segment' => sprintf('Day %d demo: %s', $output['day'], $output['technology']),
                    'say' => $this->speakerNoteFor($output),
                    'do' => $this->actionFor($output),
                    'verify' => $this->verificationFor($output),
                    'evidence' => $output['evidence_to_attach'],
                ])
                ->all(),
            'opening_lines' => [
                'This demo starts from JSON-backed Laravel Labs content and ends in working Laravel code.',
                'I will show the source-backed task, the Laravel file I changed, the test command, and the evidence.',
            ],
            'closing_lines' => [
                'The strongest result is the implementation that has both source traceability and passing verification.',
                'The next improvement is selected from the weekly blockers and next-week plan.',
            ],
            'rehearsal_checklist' => [
                'Open the related practice workspace before starting the demo.',
                'Keep one terminal ready with the focused test command.',
                'Show the changed Laravel file before showing the browser result.',
                'End each segment with the evidence that proves the work.',
            ],
            'handoff_payload' => [
                'items' => collect($report['progress_payload']['items'])
                    ->map(fn (array $item): array => [
                        'label' => sprintf('Demo ready: %s', $item['label']),
                        'done' => false,
                    ])
                    ->all(),
            ],
        ];
    }

    /**
     * Create a concise speaker note for one daily output.
     *
     * @param  array{day: int, technology: string, focus: string, output: string, evidence_to_attach: string}  $output
     */
    private function speakerNoteFor(array $output): string
    {
        return sprintf(
            'I practiced `%s` by doing `%s`, then turned it into `%s`.',
            $output['technology'],
            $output['focus'],
            $output['output'],
        );
    }

    /**
     * Return the concrete demo action for one focus.
     *
     * @param  array{focus: string}  $output
     */
    private function actionFor(array $output): string
    {
        return match ($output['focus']) {
            'Build capstone task' => 'Open the capstone workspace, show the target Laravel class, and explain the smallest implementation slice.',
            'Run checkpoint exam' => 'Open the checkpoint exam, answer the warmup, then show the coding task and pass criteria.',
            'Answer mentor feedback' => 'Open the mentor feedback lab and show the before/after change from one action item.',
            'Package portfolio evidence' => 'Open the portfolio lab and show the source reference, skills, and verification evidence.',
            default => 'Open the retrospective lab and select one concrete next improvement.',
        };
    }

    /**
     * Return the verification action for one daily output.
     *
     * @param  array{focus: string}  $output
     */
    private function verificationFor(array $output): string
    {
        return match ($output['focus']) {
            'Build capstone task' => 'Run the focused feature test and Pint for the changed files.',
            'Run checkpoint exam' => 'Read the pass criteria and show which criteria are complete.',
            'Answer mentor feedback' => 'Show the changed file and rerun the related lab test.',
            'Package portfolio evidence' => 'Show the portfolio entry and the linked verification command.',
            default => 'Record the next command to run in the next practice session.',
        };
    }
}
