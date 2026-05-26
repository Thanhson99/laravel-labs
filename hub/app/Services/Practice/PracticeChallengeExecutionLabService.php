<?php

declare(strict_types=1);

namespace App\Services\Practice;

final class PracticeChallengeExecutionLabService
{
    /**
     * Build executable practice sessions from next challenge cards.
     */
    public function __construct(
        private readonly PracticeNextChallengeLabService $challenges,
        private readonly PracticeProgressPayloadService $progressPayload,
    ) {}

    /**
     * Create an execution plan for the next recommended challenges.
     *
     * @param  array{family?: string|null, language?: string|null, search?: string|null, limit?: int|string|null, phase_limit?: int|string|null, tasks_per_phase?: int|string|null, days?: int|string|null}  $filters
     * @return array<string, mixed>
     */
    public function build(array $filters = []): array
    {
        $challengeLab = $this->challenges->build($filters);

        $steps = collect($challengeLab['challenge_cards'])
            ->values()
            ->map(fn (array $card, int $index): array => [
                'step' => $index + 1,
                'technology_segment' => $card['technology_segment'],
                'route_name' => $card['recommended_route'],
                'challenge_type' => $card['challenge_type'],
                'estimated_minutes' => $this->estimatedMinutesFor($card),
                'execution_order' => $this->executionOrderFor($card),
                'verification_command' => $card['verification_command'],
                'evidence_to_submit' => $card['evidence_to_submit'],
                'exit_criteria' => $this->exitCriteriaFor($card),
            ])
            ->all();

        return [
            'title' => 'Laravel challenge execution practice lab',
            'source_challenge_lab' => [
                'title' => $challengeLab['title'],
                'technology_coverage' => $challengeLab['source_competency_lab']['technology_coverage'],
                'challenge_count' => count($challengeLab['challenge_cards']),
            ],
            'execution_rules' => [
                'Open the recommended route before editing code.',
                'Complete one challenge step before starting another.',
                'Attach evidence immediately after running verification.',
                'Stop the session if exit criteria cannot be proven.',
            ],
            'execution_steps' => $steps,
            'session_summary' => [
                'total_estimated_minutes' => collect($steps)->sum('estimated_minutes'),
                'harder_lab_steps' => collect($steps)->where('challenge_type', 'harder-lab')->count(),
                'reinforcement_steps' => collect($steps)->where('challenge_type', 'reinforcement')->count(),
            ],
            'progress_payload' => $this->progressPayload->fromRows(
                $steps,
                fn (array $step): string => sprintf('Executed challenge step %d: %s', $step['step'], $step['technology_segment'])
            ),
        ];
    }

    /**
     * Estimate the time needed for one challenge.
     *
     * @param  array{challenge_type: string}  $card
     */
    private function estimatedMinutesFor(array $card): int
    {
        return $card['challenge_type'] === 'harder-lab' ? 45 : 25;
    }

    /**
     * Return the execution order for one challenge card.
     *
     * @param  array{recommended_route: string, why_this_challenge: string}  $card
     * @return array<int, string>
     */
    private function executionOrderFor(array $card): array
    {
        return [
            sprintf('Open `%s` with the same filters.', $card['recommended_route']),
            sprintf('Read the challenge reason: %s', $card['why_this_challenge']),
            'Make the smallest Laravel change or answer the required checkpoint.',
            'Run the verification command and capture evidence.',
        ];
    }

    /**
     * Return the exit criteria for one challenge.
     *
     * @param  array{verification_command: string}  $card
     * @return array<int, string>
     */
    private function exitCriteriaFor(array $card): array
    {
        return [
            sprintf('`%s` passes.', $card['verification_command']),
            'Evidence includes changed behavior or answer output.',
            'The learner can explain the Laravel layer involved.',
            'Risk or rollback note is written.',
        ];
    }
}
