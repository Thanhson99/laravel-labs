<?php

declare(strict_types=1);

namespace App\Services\Practice;

final class PracticeProgressChecklistService
{
    /**
     * Summarize practice checklist progress.
     *
     * @param  array<int, array{label: string, done: bool}>  $items
     * @return array{status: string, completed: int, total: int, percent: int, next_item: string|null, items: array<int, array{label: string, done: bool}>}
     */
    public function summarize(array $items): array
    {
        $total = count($items);
        $completed = collect($items)
            ->filter(fn (array $item): bool => $item['done'])
            ->count();

        $nextItem = collect($items)
            ->first(fn (array $item): bool => ! $item['done']);

        return [
            'status' => $total > 0 && $completed === $total ? 'complete' : 'in-progress',
            'completed' => $completed,
            'total' => $total,
            'percent' => $total === 0 ? 0 : (int) round(($completed / $total) * 100),
            'next_item' => $nextItem['label'] ?? null,
            'items' => $items,
        ];
    }
}
