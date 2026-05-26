<?php

declare(strict_types=1);

namespace App\Services\Practice;

use Illuminate\Support\Str;

final class TechnologyEvidenceArchiveService
{
    /**
     * Create reusable archive entries from spaced reviews.
     */
    public function __construct(
        private readonly TechnologySpacedReviewService $reviews,
        private readonly PracticeProgressPayloadService $progressPayload,
    ) {}

    /**
     * Build an evidence archive entry for one inferred technology.
     *
     * @param  array{family?: string|null, language?: string|null, search?: string|null, limit?: int|string|null}  $filters
     * @return array<string, mixed>
     */
    public function build(string $technology, array $filters = []): array
    {
        $review = $this->reviews->build($technology, $filters);
        $archiveId = sprintf('tech-%s-%s', Str::slug($technology), now()->format('Ymd'));

        return [
            'title' => sprintf('Technology Evidence Archive: %s', $technology),
            'technology' => $technology,
            'archive_id' => $archiveId,
            'review' => $review,
            'retrieval_keys' => $this->retrievalKeys($technology, $review),
            'proof_bundle' => $this->proofBundle($review),
            'reuse_targets' => [
                'portfolio README update',
                'interview answer rehearsal',
                'next implementation challenge',
                'mentor review evidence',
                'weekly learning report',
            ],
            'retrieval_prompts' => $this->retrievalPrompts($technology),
            'progress_payload' => $this->progressPayload->fromLabels([
                'Save archive id',
                'Review retrieval keys',
                'Store proof bundle',
                'Choose reuse target',
                'Schedule next retrieval prompt',
            ]),
        ];
    }

    /**
     * Build search keys for finding the archive later.
     *
     * @return array<int, string>
     */
    private function retrievalKeys(string $technology, array $review): array
    {
        return [
            sprintf('technology:%s', $technology),
            sprintf('checkpoint:%s', $review['checkpoint']['title']),
            sprintf('next-challenge:%s', $review['checkpoint']['next_challenge']['route']),
            'evidence:changed-files',
            'evidence:verification-commands',
        ];
    }

    /**
     * Build durable proof references from review cards and criteria.
     *
     * @return array<int, array{label: string, detail: string}>
     */
    private function proofBundle(array $review): array
    {
        $cards = collect($review['cards'])
            ->map(fn (array $card): array => [
                'label' => $card['label'],
                'detail' => sprintf('%s Evidence: %s', $card['recall_prompt'], $card['evidence_recheck']),
            ])
            ->all();

        return [
            ...$cards,
            [
                'label' => 'Promotion criteria',
                'detail' => implode(' ', $review['promotion_criteria']),
            ],
        ];
    }

    /**
     * Build prompts used to retrieve the archive in later sessions.
     *
     * @return array<int, string>
     */
    private function retrievalPrompts(string $technology): array
    {
        return [
            sprintf('Explain `%s` from source record to Laravel implementation without opening snippets.', $technology),
            'Name the changed files, verification commands, and review evidence.',
            'Choose one proof item and reuse it in a portfolio or interview answer.',
        ];
    }
}
