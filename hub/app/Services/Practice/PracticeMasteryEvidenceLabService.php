<?php

declare(strict_types=1);

namespace App\Services\Practice;

final class PracticeMasteryEvidenceLabService
{
    /**
     * Build mastery evidence from repeated practice checkpoints.
     */
    public function __construct(
        private readonly PracticeSpacedRepetitionLabService $repetitions,
        private readonly PracticeProgressPayloadService $progressPayload,
    ) {}

    /**
     * Create mastery evidence cards grouped by technology segment.
     *
     * @param  array{family?: string|null, language?: string|null, search?: string|null, limit?: int|string|null, phase_limit?: int|string|null, tasks_per_phase?: int|string|null, days?: int|string|null}  $filters
     * @return array<string, mixed>
     */
    public function build(array $filters = []): array
    {
        $repetition = $this->repetitions->build($filters);

        $evidenceCards = collect($repetition['review_schedule'])
            ->groupBy('technology_segment')
            ->map(fn ($items, string $technology): array => $this->evidenceFor($technology, $items->all()))
            ->values()
            ->all();

        return [
            'title' => 'Laravel mastery evidence practice lab',
            'source_repetition_lab' => [
                'title' => $repetition['title'],
                'technology_coverage' => $repetition['source_gap_lab']['technology_coverage'],
                'review_count' => count($repetition['review_schedule']),
            ],
            'mastery_rules' => [
                'Mastery requires repeated implementation evidence.',
                'A concept is not ready if it only has recall notes.',
                'Evidence must include code action, verification, and explanation.',
                'Promote only when every repetition checkpoint has a clear proof item.',
            ],
            'evidence_cards' => $evidenceCards,
            'next_harder_labs' => [
                'Run checkpoint exam for the same technology.',
                'Turn the strongest task into a capstone deliverable.',
                'Defend the implementation again using release evidence.',
            ],
            'progress_payload' => $this->progressPayload->fromRows(
                $evidenceCards,
                fn (array $card): string => sprintf('Mastery evidence complete: %s', $card['technology_segment'])
            ),
        ];
    }

    /**
     * Build one evidence card from repeated review checkpoints.
     *
     * @param  array<int, array<string, mixed>>  $items
     * @return array<string, mixed>
     */
    private function evidenceFor(string $technology, array $items): array
    {
        $reviewDays = collect($items)->pluck('day')->unique()->values()->all();
        $verificationHints = collect($items)->pluck('verification_hint')->unique()->values()->all();
        $score = min(100, count($reviewDays) * 25 + count($verificationHints) * 10);

        return [
            'technology_segment' => $technology,
            'review_days' => $reviewDays,
            'evidence_score' => $score,
            'status' => $score >= 85 ? 'ready-for-harder-lab' : 'needs-more-practice',
            'proof_items' => collect($items)
                ->map(fn (array $item): string => sprintf('Day %d: %s', $item['day'], $item['verification_hint']))
                ->all(),
            'missing_evidence' => $this->missingEvidenceFor($score),
        ];
    }

    /**
     * Return missing evidence notes for the current score.
     *
     * @return array<int, string>
     */
    private function missingEvidenceFor(int $score): array
    {
        if ($score >= 85) {
            return [
                'No blocking evidence gap. Move to a harder lab.',
            ];
        }

        return [
            'Add one fresh focused test result.',
            'Add one explanation from memory.',
            'Add one smoke check or API payload proof.',
        ];
    }
}
