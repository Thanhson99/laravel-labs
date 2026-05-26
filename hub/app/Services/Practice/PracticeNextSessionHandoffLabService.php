<?php

declare(strict_types=1);

namespace App\Services\Practice;

final class PracticeNextSessionHandoffLabService
{
    /**
     * Build next-session handoffs from challenge promotion decisions.
     */
    public function __construct(
        private readonly PracticeChallengePromotionLabService $promotions,
        private readonly PracticeProgressPayloadService $progressPayload,
    ) {}

    /**
     * Create handoff cards for the next Laravel practice session.
     *
     * @param  array{family?: string|null, language?: string|null, search?: string|null, limit?: int|string|null, phase_limit?: int|string|null, tasks_per_phase?: int|string|null, days?: int|string|null}  $filters
     * @return array<string, mixed>
     */
    public function build(array $filters = []): array
    {
        $promotionLab = $this->promotions->build($filters);

        $handoffs = collect($promotionLab['promotion_decisions'])
            ->values()
            ->map(fn (array $decision): array => [
                'step' => $decision['step'],
                'technology_segment' => $decision['technology_segment'],
                'handoff_type' => $this->handoffTypeFor($decision),
                'open_route_name' => $decision['next_route_name'],
                'carry_over_route_name' => $decision['source_route_name'],
                'verification_command' => $decision['verification_command'],
                'session_goal' => $this->sessionGoalFor($decision),
                'preflight_checklist' => $this->preflightChecklistFor($decision),
                'coding_focus' => $this->codingFocusFor($decision),
                'done_evidence' => $this->doneEvidenceFor($decision),
                'handoff_note_prompt' => $decision['learner_note_prompt'],
            ])
            ->all();

        return [
            'title' => 'Laravel next session handoff practice lab',
            'source_promotion_lab' => [
                'title' => $promotionLab['title'],
                'decision_count' => $promotionLab['promotion_summary']['decision_count'],
                'promoted_count' => $promotionLab['promotion_summary']['promoted_count'],
                'repeat_count' => $promotionLab['promotion_summary']['repeat_count'],
            ],
            'handoff_rules' => [
                'Start the next session from the route selected by the promotion decision.',
                'Carry over the previous evidence before writing new code.',
                'Run the verification command before and after the next change.',
                'Write the handoff note at the end so the next session can resume quickly.',
            ],
            'handoff_cards' => $handoffs,
            'handoff_summary' => [
                'handoff_count' => count($handoffs),
                'promotion_handoffs' => collect($handoffs)->where('handoff_type', 'promotion-handoff')->count(),
                'repeat_handoffs' => collect($handoffs)->where('handoff_type', 'repeat-handoff')->count(),
                'routes_to_open' => collect($handoffs)->pluck('open_route_name')->unique()->values()->all(),
            ],
            'progress_payload' => $this->progressPayload->fromRows(
                $handoffs,
                fn (array $handoff): string => sprintf('Prepared next session handoff %d: %s', $handoff['step'], $handoff['technology_segment'])
            ),
        ];
    }

    /**
     * Classify the handoff from a promotion decision.
     *
     * @param  array{decision: string}  $decision
     */
    private function handoffTypeFor(array $decision): string
    {
        return $decision['decision'] === 'promote-to-next-lab'
            ? 'promotion-handoff'
            : 'repeat-handoff';
    }

    /**
     * Create a concrete next-session goal.
     *
     * @param  array{decision: string, technology_segment: string, next_route_name: string}  $decision
     */
    private function sessionGoalFor(array $decision): string
    {
        if ($decision['decision'] === 'promote-to-next-lab') {
            return sprintf('Open `%s` and convert the passed %s evidence into a portfolio-ready artifact.', $decision['next_route_name'], $decision['technology_segment']);
        }

        return sprintf('Reopen `%s` and strengthen the missing %s evidence before promotion.', $decision['next_route_name'], $decision['technology_segment']);
    }

    /**
     * Build preflight checklist before coding starts.
     *
     * @param  array{source_route_name: string, verification_command: string}  $decision
     * @return array<int, string>
     */
    private function preflightChecklistFor(array $decision): array
    {
        return [
            sprintf('Open the previous route `%s` and review the last evidence.', $decision['source_route_name']),
            sprintf('Run `%s` once before editing.', $decision['verification_command']),
            'Write down the one behavior you expect to improve in this session.',
        ];
    }

    /**
     * Build focused coding tasks for the next session.
     *
     * @param  array{decision: string, promotion_proof: array<int, string>, repeat_triggers: array<int, string>}  $decision
     * @return array<int, string>
     */
    private function codingFocusFor(array $decision): array
    {
        if ($decision['decision'] === 'promote-to-next-lab') {
            return [
                $decision['promotion_proof'][0],
                $decision['promotion_proof'][1],
                'Turn the proof into a short reusable portfolio or demo note.',
            ];
        }

        return [
            $decision['repeat_triggers'][0],
            $decision['repeat_triggers'][1],
            'Patch the weakest evidence before changing any other files.',
        ];
    }

    /**
     * Define evidence required to close the handoff.
     *
     * @param  array{verification_command: string, next_route_name: string}  $decision
     * @return array<int, string>
     */
    private function doneEvidenceFor(array $decision): array
    {
        return [
            sprintf('`%s` passes after the next-session change.', $decision['verification_command']),
            sprintf('The `%s` route output or page state is captured.', $decision['next_route_name']),
            'The final handoff note says what to do first in the following session.',
        ];
    }
}
