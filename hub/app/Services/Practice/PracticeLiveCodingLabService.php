<?php

declare(strict_types=1);

namespace App\Services\Practice;

final class PracticeLiveCodingLabService
{
    /**
     * Build live coding sessions from demo scripts.
     */
    public function __construct(
        private readonly PracticeDemoScriptLabService $scripts,
        private readonly PracticeProgressPayloadService $progressPayload,
    ) {}

    /**
     * Create a timed live coding plan from a demo script.
     *
     * @param  array{family?: string|null, language?: string|null, search?: string|null, limit?: int|string|null, phase_limit?: int|string|null, tasks_per_phase?: int|string|null, days?: int|string|null}  $filters
     * @return array<string, mixed>
     */
    public function build(array $filters = []): array
    {
        $script = $this->scripts->build($filters);

        $rounds = collect($script['script_steps'])
            ->values()
            ->map(fn (array $step, int $index): array => [
                'round' => $index + 1,
                'timebox_minutes' => $this->timeboxFor($index),
                'technology_segment' => $step['segment'],
                'coding_prompt' => $step['do'],
                'narration_prompt' => $step['say'],
                'verification_command' => $this->commandFor($step['verify']),
                'pass_signal' => $step['verify'],
                'evidence' => $step['evidence'],
            ])
            ->all();

        return [
            'title' => 'Laravel live coding practice lab',
            'demo_script' => [
                'title' => $script['title'],
                'goal' => $script['demo_goal'],
                'day_count' => $script['weekly_report']['day_count'],
                'technology_coverage' => $script['weekly_report']['technology_coverage'],
            ],
            'session_rules' => [
                'Start from one source-backed practice task, not a blank idea.',
                'Say the Laravel layer before editing the file.',
                'Run one focused verification command after each round.',
                'Write down the next improvement when a round fails.',
            ],
            'rounds' => $rounds,
            'scorecard' => [
                ['label' => 'Explained the source-backed task', 'points' => 20],
                ['label' => 'Changed the correct Laravel file', 'points' => 25],
                ['label' => 'Ran a focused verification command', 'points' => 25],
                ['label' => 'Named evidence and next improvement', 'points' => 20],
                ['label' => 'Kept the controller/service boundary clear', 'points' => 10],
            ],
            'failure_recovery' => [
                'If a route fails, check route name, controller import, and request filters.',
                'If JSON shape fails, compare the API test expected path with the service payload.',
                'If Pint fails, run Pint once, inspect the diff, then rerun targeted tests.',
            ],
            'progress_payload' => $this->progressPayload->fromRows(
                $rounds,
                fn (array $round): string => sprintf('Round %d live coded: %s', $round['round'], $round['technology_segment'])
            ),
        ];
    }

    /**
     * Return a practical timebox for one live coding round.
     */
    private function timeboxFor(int $index): int
    {
        return match ($index % 3) {
            0 => 15,
            1 => 10,
            default => 8,
        };
    }

    /**
     * Convert a verification instruction into a runnable command hint.
     */
    private function commandFor(string $verification): string
    {
        if (str_contains($verification, 'Pint')) {
            return 'php artisan test --filter PracticeLiveCodingLabTest && vendor/bin/pint --test';
        }

        if (str_contains($verification, 'lab test')) {
            return 'php artisan test --filter PracticeDemoScriptLabTest';
        }

        return 'php artisan test --filter PracticeDemoScriptLabTest';
    }
}
