<?php

declare(strict_types=1);

namespace App\Services\Learning;

use App\Repositories\Contracts\LearningContentRepositoryInterface;

final class LearningAnalyticsService
{
    /**
     * Create a service for computing content analytics.
     */
    public function __construct(private readonly LearningContentRepositoryInterface $content) {}

    /**
     * Build analytics from source metadata and extracted records.
     *
     * @return array<string, mixed>
     */
    public function report(): array
    {
        $sources = collect($this->content->sources());
        $questions = collect($this->content->questions());
        $englishSources = $sources->where('language', 'en')->values();
        $englishQuestions = $questions->where('language', 'en')->values();

        return [
            'summary' => $this->content->statistics(),
            'families' => $this->groupCounts($questions, 'family'),
            'languages' => $this->groupCounts($questions, 'language'),
            'types' => $this->groupCounts($questions, 'type'),
            'source_density' => $this->sourceDensity($englishSources, $englishQuestions),
            'code_density' => $this->codeDensity($englishQuestions),
        ];
    }

    /**
     * Count extracted records by one record key.
     *
     * @return array<int, array{key: string, count: int}>
     */
    private function groupCounts($records, string $key): array
    {
        return $records
            ->groupBy(fn (array $record): string => (string) ($record[$key] ?? 'unknown'))
            ->map(fn ($items, string $group): array => [
                'key' => $group !== '' ? $group : 'unknown',
                'count' => $items->count(),
            ])
            ->sortByDesc('count')
            ->values()
            ->all();
    }

    /**
     * Return the largest source files by extracted record count.
     *
     * @return array<int, array<string, mixed>>
     */
    private function sourceDensity($sources, $questions): array
    {
        $questionsBySource = $questions->groupBy('source_key');

        return $sources
            ->map(function (array $source) use ($questionsBySource): array {
                $items = collect($questionsBySource->get($source['key'], []));

                return [
                    'source_key' => $source['key'],
                    'source_path' => $source['path'],
                    'title' => $source['title'],
                    'family' => $source['family'],
                    'topic' => $source['topic'],
                    'language' => $source['language'],
                    'records' => $items->count(),
                    'code_snippets' => $items->filter(fn (array $item): bool => filled($item['code'] ?? null))->count(),
                ];
            })
            ->filter(fn (array $source): bool => $source['records'] > 0)
            ->sortByDesc('records')
            ->take(12)
            ->values()
            ->all();
    }

    /**
     * Return code-heavy source files.
     *
     * @return array<int, array<string, mixed>>
     */
    private function codeDensity($questions): array
    {
        return $questions
            ->filter(fn (array $item): bool => filled($item['code'] ?? null))
            ->groupBy('source_key')
            ->map(function ($items, string $sourceKey): array {
                $first = $items->first();

                return [
                    'source_key' => $sourceKey,
                    'source_path' => $first['source_path'] ?? '',
                    'family' => $first['family'] ?? '',
                    'language' => $first['language'] ?? '',
                    'code_snippets' => $items->count(),
                ];
            })
            ->sortByDesc('code_snippets')
            ->take(12)
            ->values()
            ->all();
    }
}
