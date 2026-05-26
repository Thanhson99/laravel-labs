<?php

declare(strict_types=1);

namespace App\Services\Learning;

use App\Repositories\Contracts\LearningContentRepositoryInterface;
use Illuminate\Support\Collection;

final class LearningQuizService
{
    private const DEFAULT_LIMIT = 10;

    private const MAX_LIMIT = 50;

    /**
     * Create a service for building practice sets from JSON-backed content.
     */
    public function __construct(private readonly LearningContentRepositoryInterface $content) {}

    /**
     * Build a filtered practice set.
     *
     * @param  array{language?: string|null, family?: string|null, search?: string|null, limit?: int|string|null}  $filters
     * @return array{items: array<int, array<string, mixed>>, meta: array<string, mixed>}
     */
    public function build(array $filters = []): array
    {
        $limit = $this->normalizeLimit($filters['limit'] ?? null);
        $questionFilters = [
            'language' => $filters['language'] ?? 'en',
            'family' => $filters['family'] ?? null,
            'search' => $filters['search'] ?? null,
        ];

        $availableItems = collect($this->content->questions($questionFilters))
            ->filter(fn (array $item): bool => $this->isPracticeReady($item))
            ->values();

        return [
            'items' => $this->selectItems($availableItems, $limit),
            'meta' => [
                'filters' => $questionFilters + ['limit' => $limit],
                'available' => $availableItems->count(),
                'count' => min($availableItems->count(), $limit),
            ],
        ];
    }

    /**
     * Decide whether a record has enough content for practice mode.
     */
    private function isPracticeReady(array $item): bool
    {
        return filled($item['title'] ?? null)
            && (
                filled($item['body'] ?? null)
                || filled($item['answer'] ?? null)
                || filled($item['note'] ?? null)
                || filled($item['tip'] ?? null)
                || filled($item['code'] ?? null)
            );
    }

    /**
     * Select a randomized set of items for a quiz attempt.
     *
     * @param  Collection<int, array<string, mixed>>  $items
     * @return array<int, array<string, mixed>>
     */
    private function selectItems(Collection $items, int $limit): array
    {
        return $items
            ->shuffle()
            ->take($limit)
            ->map(fn (array $item, int $index): array => $this->toPracticeCard($item, $index))
            ->values()
            ->all();
    }

    /**
     * Convert a question-bank record into a practice card.
     *
     * @return array<string, mixed>
     */
    private function toPracticeCard(array $item, int $index): array
    {
        return [
            'position' => $index + 1,
            'id' => $item['id'],
            'prompt' => $item['title'],
            'context' => $item['body'],
            'answer' => $item['answer'] ?: $item['note'] ?: $item['tip'],
            'code' => $item['code'],
            'source' => [
                'key' => $item['source_key'],
                'path' => $item['source_path'],
                'family' => $item['family'],
                'topic' => $item['topic'],
                'language' => $item['language'],
            ],
        ];
    }

    /**
     * Normalize requested quiz size into an allowed range.
     */
    private function normalizeLimit(int|string|null $limit): int
    {
        $value = is_numeric($limit) ? (int) $limit : self::DEFAULT_LIMIT;

        return max(1, min(self::MAX_LIMIT, $value));
    }
}
