<?php

declare(strict_types=1);

namespace App\Services\Practice;

final class PracticeChallengePromotionLabService
{
    /**
     * Build promotion decisions from challenge evidence review cards.
     */
    public function __construct(
        private readonly PracticeChallengeEvidenceReviewLabService $reviews,
        private readonly PracticeProgressPayloadService $progressPayload,
    ) {}

    /**
     * Create challenge promotion decisions after evidence review.
     *
     * @param  array{family?: string|null, language?: string|null, search?: string|null, limit?: int|string|null, phase_limit?: int|string|null, tasks_per_phase?: int|string|null, days?: int|string|null}  $filters
     * @return array<string, mixed>
     */
    public function build(array $filters = []): array
    {
        $reviewLab = $this->reviews->build($filters);

        $decisions = collect($reviewLab['evidence_cards'])
            ->values()
            ->map(fn (array $card): array => [
                'step' => $card['step'],
                'technology_segment' => $card['technology_segment'],
                'source_route_name' => $card['route_name'],
                'decision' => $this->decisionFor($card),
                'next_route_name' => $this->nextRouteFor($card),
                'verification_command' => $card['verification_command'],
                'promotion_proof' => $this->promotionProofFor($card),
                'repeat_triggers' => $this->repeatTriggersFor($card),
                'learner_note_prompt' => $this->learnerNotePromptFor($card),
            ])
            ->all();

        return [
            'title' => 'Laravel challenge promotion practice lab',
            'source_review_lab' => [
                'title' => $reviewLab['title'],
                'review_card_count' => $reviewLab['review_summary']['review_card_count'],
                'required_evidence_count' => $reviewLab['review_summary']['required_evidence_count'],
            ],
            'promotion_rules' => [
                'Promote only when command output, changed file evidence, and explanation evidence all exist.',
                'Repeat the challenge if any pass signal is missing or vague.',
                'Keep the next route tied to the same technology segment.',
                'Write one learner note before starting the next lab.',
            ],
            'promotion_decisions' => $decisions,
            'promotion_summary' => [
                'decision_count' => count($decisions),
                'promoted_count' => collect($decisions)->where('decision', 'promote-to-next-lab')->count(),
                'repeat_count' => collect($decisions)->where('decision', 'repeat-with-stronger-evidence')->count(),
                'next_routes' => collect($decisions)->pluck('next_route_name')->unique()->values()->all(),
            ],
            'progress_payload' => $this->progressPayload->fromRows(
                $decisions,
                fn (array $decision): string => sprintf('Promotion decision %d: %s', $decision['step'], $decision['technology_segment'])
            ),
        ];
    }

    /**
     * Decide whether the reviewed challenge can be promoted.
     *
     * @param  array{challenge_type: string}  $card
     */
    private function decisionFor(array $card): string
    {
        return $card['challenge_type'] === 'harder-lab'
            ? 'promote-to-next-lab'
            : 'repeat-with-stronger-evidence';
    }

    /**
     * Pick the next route after the promotion decision.
     *
     * @param  array{challenge_type: string, route_name: string}  $card
     */
    private function nextRouteFor(array $card): string
    {
        return $card['challenge_type'] === 'harder-lab'
            ? 'practice.portfolio-lab'
            : $card['route_name'];
    }

    /**
     * Build proof checklist for promotion.
     *
     * @param  array{pass_signals: array<int, string>, risk_checks: array<int, string>}  $card
     * @return array<int, string>
     */
    private function promotionProofFor(array $card): array
    {
        return [
            $card['pass_signals'][0],
            $card['pass_signals'][1],
            $card['risk_checks'][0],
            'Learner note explains what to carry into the next lab.',
        ];
    }

    /**
     * Build repeat triggers when evidence is not strong enough.
     *
     * @param  array{review_questions: array<int, string>}  $card
     * @return array<int, string>
     */
    private function repeatTriggersFor(array $card): array
    {
        return [
            'Verification output is missing or copied without context.',
            'Changed files or route names are not listed.',
            sprintf('The learner cannot answer: %s', $card['review_questions'][1]),
        ];
    }

    /**
     * Create a short learner note prompt for the decision.
     *
     * @param  array{technology_segment: string, follow_up_action: string}  $card
     */
    private function learnerNotePromptFor(array $card): string
    {
        return sprintf(
            'Write two sentences for %s: what evidence passed, and why the next action is `%s`.',
            $card['technology_segment'],
            $card['follow_up_action'],
        );
    }
}
