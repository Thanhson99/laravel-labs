<?php

declare(strict_types=1);

namespace App\Services\Practice;

final class PracticeInterviewDefenseLabService
{
    /**
     * Build interview defense drills from release readiness evidence.
     */
    public function __construct(
        private readonly PracticeReleaseReadinessLabService $releases,
        private readonly PracticeProgressPayloadService $progressPayload,
    ) {}

    /**
     * Create interview/review answers from release readiness items.
     *
     * @param  array{family?: string|null, language?: string|null, search?: string|null, limit?: int|string|null, phase_limit?: int|string|null, tasks_per_phase?: int|string|null, days?: int|string|null}  $filters
     * @return array<string, mixed>
     */
    public function build(array $filters = []): array
    {
        $release = $this->releases->build($filters);

        $defenses = collect($release['release_items'])
            ->map(fn (array $item): array => [
                'round' => $item['round'],
                'technology_segment' => $item['technology_segment'],
                'question' => $this->questionFor($item),
                'answer_outline' => $this->answerOutlineFor($item),
                'evidence_to_cite' => [
                    $item['release_note'],
                    $item['smoke_check'],
                    $item['verification_command'],
                    $item['evidence'],
                ],
                'follow_up_risk' => $this->followUpRiskFor($item),
            ])
            ->all();

        return [
            'title' => 'Laravel interview defense practice lab',
            'source_release_lab' => [
                'title' => $release['title'],
                'technology_coverage' => $release['source_refactor_lab']['technology_coverage'],
                'release_item_count' => count($release['release_items']),
            ],
            'defense_rules' => [
                'Answer from evidence, not memory.',
                'Name the Laravel layer and why it owns the behavior.',
                'Mention the focused test or smoke check that proves the claim.',
                'Close with a risk, rollback, or next improvement.',
            ],
            'defense_cards' => $defenses,
            'scoring_rubric' => [
                ['label' => 'Names the source-backed technology clearly', 'points' => 20],
                ['label' => 'Explains the Laravel layer decision', 'points' => 25],
                ['label' => 'Cites verification evidence', 'points' => 25],
                ['label' => 'Mentions risk or rollback path', 'points' => 20],
                ['label' => 'Keeps the answer concise and concrete', 'points' => 10],
            ],
            'progress_payload' => $this->progressPayload->fromRows(
                $defenses,
                fn (array $card): string => sprintf('Defended %s', $card['technology_segment'])
            ),
        ];
    }

    /**
     * Create an interview question from one release item.
     *
     * @param  array{technology_segment: string, changed_area: string}  $item
     */
    private function questionFor(array $item): string
    {
        return sprintf(
            'Why was `%s` the right area to change for `%s`, and how did you prove it?',
            $item['changed_area'],
            $item['technology_segment'],
        );
    }

    /**
     * Build an answer outline from release evidence.
     *
     * @param  array{release_note: string, smoke_check: string, rollback_note: string}  $item
     * @return array<int, string>
     */
    private function answerOutlineFor(array $item): array
    {
        return [
            sprintf('Start with the behavior: %s', $item['release_note']),
            sprintf('Explain the proof: %s', $item['smoke_check']),
            sprintf('Close with rollback: %s', $item['rollback_note']),
        ];
    }

    /**
     * Create a follow-up risk prompt from one release item.
     *
     * @param  array{changed_area: string}  $item
     */
    private function followUpRiskFor(array $item): string
    {
        return sprintf(
            'If `%s` changes again, what test or smoke check should fail first?',
            $item['changed_area'],
        );
    }
}
