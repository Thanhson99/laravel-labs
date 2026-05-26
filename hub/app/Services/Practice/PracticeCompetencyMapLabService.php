<?php

declare(strict_types=1);

namespace App\Services\Practice;

final class PracticeCompetencyMapLabService
{
    /**
     * Build competency maps from mastery evidence.
     */
    public function __construct(
        private readonly PracticeMasteryEvidenceLabService $evidence,
        private readonly PracticeProgressPayloadService $progressPayload,
    ) {}

    /**
     * Create competency cards that connect evidence to practical capability.
     *
     * @param  array{family?: string|null, language?: string|null, search?: string|null, limit?: int|string|null, phase_limit?: int|string|null, tasks_per_phase?: int|string|null, days?: int|string|null}  $filters
     * @return array<string, mixed>
     */
    public function build(array $filters = []): array
    {
        $mastery = $this->evidence->build($filters);

        $competencies = collect($mastery['evidence_cards'])
            ->map(fn (array $card): array => [
                'technology_segment' => $card['technology_segment'],
                'readiness' => $card['status'],
                'evidence_score' => $card['evidence_score'],
                'competency_levels' => $this->levelsFor($card),
                'proof_summary' => $this->proofSummaryFor($card),
                'next_action' => $this->nextActionFor($card),
            ])
            ->all();

        return [
            'title' => 'Laravel competency map practice lab',
            'source_mastery_lab' => [
                'title' => $mastery['title'],
                'technology_coverage' => $mastery['source_repetition_lab']['technology_coverage'],
                'evidence_card_count' => count($mastery['evidence_cards']),
            ],
            'competency_rules' => [
                'A competency needs implementation, verification, explanation, and release evidence.',
                'Evidence score decides whether the next action is reinforcement or a harder lab.',
                'Every capability must point back to a source-backed technology segment.',
                'Use the map to choose the next practice route, not to stop practicing.',
            ],
            'competencies' => $competencies,
            'map_summary' => [
                'ready_count' => collect($competencies)->where('readiness', 'ready-for-harder-lab')->count(),
                'needs_practice_count' => collect($competencies)->where('readiness', 'needs-more-practice')->count(),
            ],
            'progress_payload' => $this->progressPayload->fromRows(
                $competencies,
                fn (array $competency): string => sprintf('Mapped competency: %s', $competency['technology_segment'])
            ),
        ];
    }

    /**
     * Build skill levels from one evidence card.
     *
     * @param  array{evidence_score: int, proof_items: array<int, string>}  $card
     * @return array<int, array{skill: string, level: string}>
     */
    private function levelsFor(array $card): array
    {
        $level = $card['evidence_score'] >= 85 ? 'ready' : 'reinforce';

        return [
            ['skill' => 'implementation', 'level' => $level],
            ['skill' => 'verification', 'level' => count($card['proof_items']) >= 3 ? 'ready' : 'reinforce'],
            ['skill' => 'explanation', 'level' => $level],
            ['skill' => 'release-readiness', 'level' => $level],
        ];
    }

    /**
     * Summarize proof items for one competency.
     *
     * @param  array{review_days: array<int, int>, proof_items: array<int, string>}  $card
     */
    private function proofSummaryFor(array $card): string
    {
        return sprintf(
            'Covered review days %s with %d proof items.',
            implode(', ', $card['review_days']),
            count($card['proof_items']),
        );
    }

    /**
     * Pick the next practice action for one competency.
     *
     * @param  array{status: string, technology_segment: string}  $card
     */
    private function nextActionFor(array $card): string
    {
        if ($card['status'] === 'ready-for-harder-lab') {
            return sprintf('Move `%s` to checkpoint exam or capstone practice.', $card['technology_segment']);
        }

        return sprintf('Repeat `%s` in spaced repetition before attempting a harder lab.', $card['technology_segment']);
    }
}
