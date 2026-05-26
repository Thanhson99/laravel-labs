<?php

declare(strict_types=1);

namespace App\Services\Practice;

final class PracticeSpacedRepetitionLabService
{
    /**
     * Build spaced repetition schedules from knowledge gaps.
     */
    public function __construct(
        private readonly PracticeKnowledgeGapLabService $gaps,
        private readonly PracticeProgressPayloadService $progressPayload,
    ) {}

    /**
     * Create repeated coding review sessions from knowledge-gap cards.
     *
     * @param  array{family?: string|null, language?: string|null, search?: string|null, limit?: int|string|null, phase_limit?: int|string|null, tasks_per_phase?: int|string|null, days?: int|string|null}  $filters
     * @return array<string, mixed>
     */
    public function build(array $filters = []): array
    {
        $gapLab = $this->gaps->build($filters);

        $reviewSchedule = collect($gapLab['gap_cards'])
            ->flatMap(fn (array $card): array => $this->scheduleFor($card))
            ->values()
            ->all();

        return [
            'title' => 'Laravel spaced repetition practice lab',
            'source_gap_lab' => [
                'title' => $gapLab['title'],
                'technology_coverage' => $gapLab['source_defense_lab']['technology_coverage'],
                'gap_count' => count($gapLab['gap_cards']),
            ],
            'review_rules' => [
                'Repeat the same concept by coding, not only by rereading.',
                'Increase confidence only after fresh verification passes.',
                'Keep every review tied to one technology segment and one evidence item.',
                'Promote a gap only when the learner can explain and rerun the check.',
            ],
            'review_schedule' => $reviewSchedule,
            'promotion_criteria' => [
                'Can identify the Laravel layer without looking at notes.',
                'Can make one small code change and keep the focused command green.',
                'Can explain the evidence and risk in under two minutes.',
                'Can name the next harder practice lab for the same technology.',
            ],
            'progress_payload' => $this->progressPayload->fromRows(
                $reviewSchedule,
                fn (array $item): string => sprintf('Review day %d: %s', $item['day'], $item['technology_segment'])
            ),
        ];
    }

    /**
     * Create repeated review checkpoints for one knowledge gap.
     *
     * @param  array{round: int, technology_segment: string, gap: string, practice_action: string, review_prompt: string, verification_hint: string}  $card
     * @return array<int, array<string, mixed>>
     */
    private function scheduleFor(array $card): array
    {
        return collect([1, 3, 7])
            ->map(fn (int $day): array => [
                'day' => $day,
                'round' => $card['round'],
                'technology_segment' => $card['technology_segment'],
                'review_goal' => $this->goalForDay($day, $card),
                'coding_action' => $card['practice_action'],
                'recall_prompt' => $card['review_prompt'],
                'verification_hint' => $card['verification_hint'],
            ])
            ->all();
    }

    /**
     * Return a review goal for one repetition day.
     *
     * @param  array{gap: string, technology_segment: string}  $card
     */
    private function goalForDay(int $day, array $card): string
    {
        return match ($day) {
            1 => sprintf('Rebuild the smallest fix for `%s` while reading the gap.', $card['technology_segment']),
            3 => sprintf('Repeat `%s` with fewer notes and rerun verification.', $card['technology_segment']),
            default => sprintf('Explain and implement `%s` from memory, then cite evidence.', $card['gap']),
        };
    }
}
