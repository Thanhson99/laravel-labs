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
            'session_rules' => $this->sessionRulesFor($script['weekly_report']['technology_coverage']),
            'rounds' => $rounds,
            'scorecard' => $this->scorecardFor($script['weekly_report']['technology_coverage']),
            'failure_recovery' => $this->failureRecoveryFor($script['weekly_report']['technology_coverage']),
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
        if (str_contains($verification, 'IDOR')) {
            return 'php artisan test --filter PracticeDemoScriptLabTest --filter=idor';
        }

        if (str_contains($verification, 'closure')) {
            return 'php artisan test --filter PracticeDemoScriptLabTest --filter=closure';
        }

        if (str_contains($verification, 'Pint')) {
            return 'php artisan test --filter PracticeLiveCodingLabTest && vendor/bin/pint --test';
        }

        if (str_contains($verification, 'lab test')) {
            return 'php artisan test --filter PracticeDemoScriptLabTest';
        }

        return 'php artisan test --filter PracticeDemoScriptLabTest';
    }

    /**
     * Return live-coding rules for the selected technologies.
     *
     * @param  array<int, string>  $technologies
     * @return array<int, string>
     */
    private function sessionRulesFor(array $technologies): array
    {
        if ($this->hasIdor($technologies)) {
            return [
                'Start from one source-backed IDOR route, not a generic security warning.',
                'Say the object id, owner or tenant scope, and action before editing evidence.',
                'Run one focused verification command after each round.',
                'Write down the next object route, nested resource, download, or export path when a round fails.',
            ];
        }

        if ($this->hasClosure($technologies)) {
            return [
                'Start from one source-backed closure question, not a generic definition.',
                'Say the lexical scope and captured binding before editing evidence.',
                'Run one focused verification command after each round.',
                'Write down the next closure trap to improve when a round fails.',
            ];
        }

        return [
            'Start from one source-backed practice task, not a blank idea.',
            'Say the Laravel layer before editing the file.',
            'Run one focused verification command after each round.',
            'Write down the next improvement when a round fails.',
        ];
    }

    /**
     * Return live-coding scorecard rows for the selected technologies.
     *
     * @param  array<int, string>  $technologies
     * @return array<int, array{label: string, points: int}>
     */
    private function scorecardFor(array $technologies): array
    {
        if ($this->hasIdor($technologies)) {
            return [
                ['label' => 'Explained the source-backed IDOR route', 'points' => 20],
                ['label' => 'Named object id, owner or tenant scope, and protected action', 'points' => 25],
                ['label' => 'Ran a focused verification command', 'points' => 25],
                ['label' => 'Covered scoped lookup, policy, denial test, and 403 or 404 risk', 'points' => 20],
                ['label' => 'Kept the answer concrete and interview-ready', 'points' => 10],
            ];
        }

        if ($this->hasClosure($technologies)) {
            return [
                ['label' => 'Explained the source-backed closure question', 'points' => 20],
                ['label' => 'Named lexical scope and captured binding', 'points' => 25],
                ['label' => 'Ran a focused verification command', 'points' => 25],
                ['label' => 'Covered var versus let or stale-closure risk', 'points' => 20],
                ['label' => 'Kept the answer concrete and interview-ready', 'points' => 10],
            ];
        }

        return [
            ['label' => 'Explained the source-backed task', 'points' => 20],
            ['label' => 'Changed the correct Laravel file', 'points' => 25],
            ['label' => 'Ran a focused verification command', 'points' => 25],
            ['label' => 'Named evidence and next improvement', 'points' => 20],
            ['label' => 'Kept the controller/service boundary clear', 'points' => 10],
        ];
    }

    /**
     * Return recovery prompts for the selected technologies.
     *
     * @param  array<int, string>  $technologies
     * @return array<int, string>
     */
    private function failureRecoveryFor(array $technologies): array
    {
        if ($this->hasIdor($technologies)) {
            return [
                'If the denial test fails, confirm the attacker and victim objects are owned by different users or tenants.',
                'If the scoped lookup is vague, rewrite it through the current user, tenant, organization, team, or parent resource.',
                'If the policy evidence is missing, add the exact ability and action being checked before changing the route.',
            ];
        }

        if ($this->hasClosure($technologies)) {
            return [
                'If the counter example fails, trace the captured binding before changing code.',
                'If the interview checklist is vague, add one var-versus-let or stale-closure example.',
                'If the payload shape fails, compare the API test expected path with the closure evidence payload.',
            ];
        }

        return [
            'If a route fails, check route name, controller import, and request filters.',
            'If JSON shape fails, compare the API test expected path with the service payload.',
            'If Pint fails, run Pint once, inspect the diff, then rerun targeted tests.',
        ];
    }

    /**
     * Determine whether this session covers JavaScript closures.
     *
     * @param  array<int, string>  $technologies
     */
    private function hasClosure(array $technologies): bool
    {
        return in_array('javascript-closures', $technologies, true);
    }

    /**
     * Determine whether this session covers IDOR object-authorization practice.
     *
     * @param  array<int, string>  $technologies
     */
    private function hasIdor(array $technologies): bool
    {
        return in_array('idor-access-control', $technologies, true);
    }
}
