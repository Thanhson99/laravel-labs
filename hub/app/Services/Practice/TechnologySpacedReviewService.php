<?php

declare(strict_types=1);

namespace App\Services\Practice;

final class TechnologySpacedReviewService
{
    /**
     * Create spaced review schedules from mastery checkpoints.
     */
    public function __construct(
        private readonly TechnologyMasteryCheckpointService $checkpoints,
        private readonly PracticeProgressPayloadService $progressPayload,
    ) {}

    /**
     * Build day 1, day 3, and day 7 review cards for one inferred technology.
     *
     * @param  array{family?: string|null, language?: string|null, search?: string|null, limit?: int|string|null}  $filters
     * @return array<string, mixed>
     */
    public function build(string $technology, array $filters = []): array
    {
        $checkpoint = $this->checkpoints->build($technology, $filters);
        $cards = $this->cardsFor($technology, $checkpoint);

        return [
            'title' => sprintf('Technology Spaced Review: %s', $technology),
            'technology' => $technology,
            'checkpoint' => $checkpoint,
            'cards' => $cards,
            'promotion_criteria' => [
                'Day 1 recall names the source record, changed files, and verification command without hints.',
                'Day 3 rebuild can complete one source-backed task with fewer generated prompts.',
                'Day 7 defense can explain the tradeoff and cite evidence in under two minutes.',
            ],
            'progress_payload' => $this->progressPayload->fromRows(
                $cards,
                fn (array $card): string => $card['label'],
            ),
        ];
    }

    /**
     * Build spaced review cards from checkpoint proof and handoff data.
     *
     * @return array<int, array{day: int, label: string, recall_prompt: string, evidence_recheck: string, coding_action: string}>
     */
    private function cardsFor(string $technology, array $checkpoint): array
    {
        return [
            [
                'day' => 1,
                'label' => 'Day 1 recall',
                'recall_prompt' => sprintf('Explain what `%s` implementation you built and which JSON source record started it.', $technology),
                'evidence_recheck' => $checkpoint['proof_checklist'][0] ?? 'Confirm first proof item exists.',
                'coding_action' => 'Open the implementation lab and identify the route, controller, service, and test without editing.',
            ],
            [
                'day' => 3,
                'label' => 'Day 3 rebuild',
                'recall_prompt' => 'Describe the Laravel layer placement before opening the generated snippets.',
                'evidence_recheck' => $checkpoint['proof_checklist'][1] ?? 'Confirm repair proof still exists.',
                'coding_action' => sprintf('Rebuild one `%s` task from the implementation lab with only the source title visible.', $technology),
            ],
            [
                'day' => 7,
                'label' => 'Day 7 defense',
                'recall_prompt' => 'Answer the interview pack questions using source, changed-file, and verification evidence.',
                'evidence_recheck' => $checkpoint['handoff']['evidence_to_keep'],
                'coding_action' => 'Open the next challenge and decide whether to promote or repeat.',
            ],
        ];
    }
}
