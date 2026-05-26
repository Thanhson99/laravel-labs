<?php

declare(strict_types=1);

namespace App\Services\Practice;

final class PracticeKnowledgeGapLabService
{
    /**
     * Build knowledge-gap drills from interview defense cards.
     */
    public function __construct(
        private readonly PracticeInterviewDefenseLabService $defenses,
        private readonly PracticeProgressPayloadService $progressPayload,
    ) {}

    /**
     * Create gap cards that send learners back to concrete coding practice.
     *
     * @param  array{family?: string|null, language?: string|null, search?: string|null, limit?: int|string|null, phase_limit?: int|string|null, tasks_per_phase?: int|string|null, days?: int|string|null}  $filters
     * @return array<string, mixed>
     */
    public function build(array $filters = []): array
    {
        $defense = $this->defenses->build($filters);

        $gapCards = collect($defense['defense_cards'])
            ->map(fn (array $card): array => [
                'round' => $card['round'],
                'technology_segment' => $card['technology_segment'],
                'gap' => $this->gapFor($card),
                'practice_action' => $this->practiceActionFor($card),
                'review_prompt' => $card['question'],
                'evidence_to_recheck' => $card['evidence_to_cite'],
                'verification_hint' => $this->verificationHintFor($card),
            ])
            ->all();

        return [
            'title' => 'Laravel knowledge gap practice lab',
            'source_defense_lab' => [
                'title' => $defense['title'],
                'technology_coverage' => $defense['source_release_lab']['technology_coverage'],
                'defense_card_count' => count($defense['defense_cards']),
            ],
            'gap_rules' => [
                'A weak answer becomes a coding task, not only a note.',
                'Tie every gap to a technology and one Laravel layer.',
                'Recheck evidence after practicing the gap.',
                'Keep the next action small enough to finish in one session.',
            ],
            'gap_cards' => $gapCards,
            'next_session_plan' => collect($gapCards)
                ->map(fn (array $card): string => sprintf('Practice `%s`: %s', $card['technology_segment'], $card['practice_action']))
                ->all(),
            'progress_payload' => $this->progressPayload->fromRows(
                $gapCards,
                fn (array $card): string => sprintf('Closed gap for %s', $card['technology_segment'])
            ),
        ];
    }

    /**
     * Create the concrete knowledge gap for one defense card.
     *
     * @param  array{technology_segment: string, follow_up_risk: string}  $card
     */
    private function gapFor(array $card): string
    {
        return sprintf(
            'Need stronger evidence for `%s`: %s',
            $card['technology_segment'],
            $card['follow_up_risk'],
        );
    }

    /**
     * Create one practice action from the defense evidence.
     *
     * @param  array{technology_segment: string}  $card
     */
    private function practiceActionFor(array $card): string
    {
        return sprintf(
            'Reopen the related lab, change one small Laravel file, and explain `%s` again with fresh verification.',
            $card['technology_segment'],
        );
    }

    /**
     * Return the verification hint for a gap card.
     *
     * @param  array{evidence_to_cite: array<int, string>}  $card
     */
    private function verificationHintFor(array $card): string
    {
        $command = collect($card['evidence_to_cite'])
            ->first(fn (string $evidence): bool => str_contains($evidence, 'php artisan test'));

        return $command ?: 'Run the focused practice test and attach the result.';
    }
}
