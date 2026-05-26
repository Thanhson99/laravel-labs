<?php

declare(strict_types=1);

namespace App\Services\Practice;

final class PracticeChallengeEvidenceReviewLabService
{
    /**
     * Build evidence review cards from executable challenge steps.
     */
    public function __construct(
        private readonly PracticeChallengeExecutionLabService $executions,
        private readonly PracticeProgressPayloadService $progressPayload,
    ) {}

    /**
     * Create a review board for submitted challenge evidence.
     *
     * @param  array{family?: string|null, language?: string|null, search?: string|null, limit?: int|string|null, phase_limit?: int|string|null, tasks_per_phase?: int|string|null, days?: int|string|null}  $filters
     * @return array<string, mixed>
     */
    public function build(array $filters = []): array
    {
        $executionLab = $this->executions->build($filters);

        $cards = collect($executionLab['execution_steps'])
            ->values()
            ->map(fn (array $step): array => [
                'step' => $step['step'],
                'technology_segment' => $step['technology_segment'],
                'route_name' => $step['route_name'],
                'challenge_type' => $step['challenge_type'],
                'verification_command' => $step['verification_command'],
                'required_evidence' => $step['evidence_to_submit'],
                'review_questions' => $this->reviewQuestionsFor($step),
                'pass_signals' => $this->passSignalsFor($step),
                'risk_checks' => $this->riskChecksFor($step),
                'follow_up_action' => $this->followUpActionFor($step),
            ])
            ->all();

        return [
            'title' => 'Laravel challenge evidence review lab',
            'source_execution_lab' => [
                'title' => $executionLab['title'],
                'challenge_count' => $executionLab['source_challenge_lab']['challenge_count'],
                'total_estimated_minutes' => $executionLab['session_summary']['total_estimated_minutes'],
            ],
            'review_rules' => [
                'Review evidence only after the verification command has been rerun.',
                'Reject vague notes that do not mention the changed Laravel layer.',
                'Keep one risk or rollback note attached to every challenge.',
                'Promote the challenge only when pass signals and exit criteria are both proven.',
            ],
            'evidence_cards' => $cards,
            'review_summary' => [
                'review_card_count' => count($cards),
                'required_evidence_count' => collect($cards)->sum(fn (array $card): int => count($card['required_evidence'])),
                'harder_lab_reviews' => collect($cards)->where('challenge_type', 'harder-lab')->count(),
                'commands_to_rerun' => collect($cards)->pluck('verification_command')->unique()->values()->all(),
            ],
            'progress_payload' => $this->progressPayload->fromRows(
                $cards,
                fn (array $card): string => sprintf('Reviewed challenge evidence %d: %s', $card['step'], $card['technology_segment'])
            ),
        ];
    }

    /**
     * Create questions that force proof instead of passive reading.
     *
     * @param  array{route_name: string, technology_segment: string}  $step
     * @return array<int, string>
     */
    private function reviewQuestionsFor(array $step): array
    {
        return [
            sprintf('What changed after opening `%s` for %s?', $step['route_name'], $step['technology_segment']),
            'Which Laravel layer carried the main behavior: route, request, controller, service, repository, view, or test?',
            'Which assertion or output proves the behavior is correct?',
        ];
    }

    /**
     * Define concrete pass signals for one evidence card.
     *
     * @param  array{verification_command: string}  $step
     * @return array<int, string>
     */
    private function passSignalsFor(array $step): array
    {
        return [
            sprintf('`%s` passes after the challenge is complete.', $step['verification_command']),
            'Evidence names at least one changed file or route.',
            'The explanation connects the code change to the source content or question prompt.',
        ];
    }

    /**
     * Define risk checks before promoting the challenge.
     *
     * @param  array{route_name: string}  $step
     * @return array<int, string>
     */
    private function riskChecksFor(array $step): array
    {
        return [
            sprintf('The `%s` route still renders or returns JSON.', $step['route_name']),
            'No unrelated practice files were changed.',
            'The rollback note says how to undo the smallest risky change.',
        ];
    }

    /**
     * Choose the next action after reviewing evidence.
     *
     * @param  array{challenge_type: string, route_name: string}  $step
     */
    private function followUpActionFor(array $step): string
    {
        if ($step['challenge_type'] === 'harder-lab') {
            return sprintf('Promote to the next checkpoint route after evidence passes: %s.', $step['route_name']);
        }

        return sprintf('Repeat the reinforcement route until the evidence is specific: %s.', $step['route_name']);
    }
}
