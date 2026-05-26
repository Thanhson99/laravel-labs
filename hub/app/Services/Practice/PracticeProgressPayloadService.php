<?php

declare(strict_types=1);

namespace App\Services\Practice;

final class PracticeProgressPayloadService
{
    /**
     * Build a standard progress payload from practice rows.
     *
     * @param  iterable<int, array<string, mixed>>  $rows
     * @return array{items: array<int, array{label: string, done: bool}>}
     */
    public function fromRows(iterable $rows, callable $labelResolver): array
    {
        return $this->fromLabels(
            collect($rows)
                ->map(fn (array $row): string => (string) $labelResolver($row))
                ->all()
        );
    }

    /**
     * Build a standard progress payload from prepared labels.
     *
     * @param  iterable<int, string>  $labels
     * @return array{items: array<int, array{label: string, done: bool}>}
     */
    public function fromLabels(iterable $labels): array
    {
        return [
            'items' => collect($labels)
                ->map(fn (string $label): array => [
                    'label' => $label,
                    'done' => false,
                ])
                ->values()
                ->all(),
        ];
    }
}
