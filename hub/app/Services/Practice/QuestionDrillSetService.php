<?php

declare(strict_types=1);

namespace App\Services\Practice;

final class QuestionDrillSetService
{
    /**
     * Create a drill-set service backed by content-to-practice mapping.
     */
    public function __construct(private readonly ContentPracticeMapperService $mapper) {}

    /**
     * Build a set of question-backed coding drills.
     *
     * @param  array{family?: string|null, language?: string|null, search?: string|null, technology?: string|null, limit?: int|string|null}  $filters
     * @return array{items: array<int, array<string, mixed>>, meta: array<string, mixed>}
     */
    public function build(array $filters = []): array
    {
        $mapped = $this->mapper->map($filters);

        return [
            'items' => collect($mapped['items'])
                ->map(fn (array $item, int $index): array => [
                    'position' => $index + 1,
                    'record_id' => $item['id'],
                    'question' => $item['content']['title'],
                    'technology' => $item['technology'],
                    'source' => $item['source'],
                    'practice' => $item['practice'],
                    'drill_query' => [
                        'record_id' => $item['id'],
                        'source_key' => $item['source']['key'],
                        'technology' => $item['technology'],
                    ],
                    'task' => $item['task'],
                ])
                ->values()
                ->all(),
            'meta' => $mapped['meta'],
        ];
    }
}
