<?php

declare(strict_types=1);

namespace App\Services\Practice;

final class PracticeBugFixLabService
{
    /**
     * Build bug-fix drills from live coding sessions.
     */
    public function __construct(
        private readonly PracticeLiveCodingLabService $liveCoding,
        private readonly PracticeProgressPayloadService $progressPayload,
    ) {}

    /**
     * Create bug-fix drills from live coding rounds.
     *
     * @param  array{family?: string|null, language?: string|null, search?: string|null, limit?: int|string|null, phase_limit?: int|string|null, tasks_per_phase?: int|string|null, days?: int|string|null}  $filters
     * @return array<string, mixed>
     */
    public function build(array $filters = []): array
    {
        $session = $this->liveCoding->build($filters);

        $drills = collect($session['rounds'])
            ->map(fn (array $round): array => [
                'round' => $round['round'],
                'technology_segment' => $round['technology_segment'],
                'bug_report' => $this->bugReportFor($round),
                'diagnosis_steps' => $this->diagnosisStepsFor($round),
                'patch_target' => $this->patchTargetFor($round),
                'verification_command' => $round['verification_command'],
                'pass_signal' => $round['pass_signal'],
                'evidence' => $round['evidence'],
            ])
            ->all();

        return [
            'title' => 'Laravel bug-fix practice lab',
            'source_session' => [
                'title' => $session['title'],
                'technology_coverage' => $session['demo_script']['technology_coverage'],
                'round_count' => count($session['rounds']),
            ],
            'debugging_rules' => [
                'Reproduce the failure before editing code.',
                'Identify whether the failure is route, request, service, view, or test shape.',
                'Change the smallest Laravel file that explains the failing behavior.',
                'Rerun the focused command before moving to the next drill.',
            ],
            'bug_drills' => $drills,
            'review_questions' => [
                'Which Laravel layer owned the bug?',
                'Which assertion proved the fix?',
                'What would break again if the same bug returned?',
                'Which source-backed technology did the bug reinforce?',
            ],
            'progress_payload' => $this->progressPayload->fromRows(
                $drills,
                fn (array $drill): string => sprintf('Bug fixed for %s', $drill['technology_segment'])
            ),
        ];
    }

    /**
     * Generate a realistic failure report for a live coding round.
     *
     * @param  array{technology_segment: string, verification_command: string}  $round
     */
    private function bugReportFor(array $round): string
    {
        return sprintf(
            'The live coding round for `%s` does not satisfy its verification command `%s`.',
            $round['technology_segment'],
            $round['verification_command'],
        );
    }

    /**
     * Return the ordered debugging checklist for one round.
     *
     * @param  array{pass_signal: string}  $round
     * @return array<int, string>
     */
    private function diagnosisStepsFor(array $round): array
    {
        return [
            'Read the failing assertion or missing browser text.',
            'Find the route/controller/service/view that owns the pass signal.',
            sprintf('Compare the current output with this pass signal: %s', $round['pass_signal']),
            'Patch one file, then rerun the focused command.',
        ];
    }

    /**
     * Infer the likely Laravel file area to patch.
     *
     * @param  array{coding_prompt: string, pass_signal: string}  $round
     */
    private function patchTargetFor(array $round): string
    {
        $text = $round['coding_prompt'].' '.$round['pass_signal'];

        if (str_contains($text, 'route')) {
            return 'routes/web.php or routes/api.php';
        }

        if (str_contains($text, 'portfolio')) {
            return 'resources/views/practice/*.blade.php';
        }

        if (str_contains($text, 'test') || str_contains($text, 'criteria')) {
            return 'tests/Feature/*Test.php';
        }

        return 'app/Services/Practice/*Service.php';
    }
}
