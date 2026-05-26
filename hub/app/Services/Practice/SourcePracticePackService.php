<?php

declare(strict_types=1);

namespace App\Services\Practice;

use App\Repositories\Contracts\LearningContentRepositoryInterface;

final class SourcePracticePackService
{
    /**
     * Create source-level practice packs from JSON content.
     */
    public function __construct(
        private readonly LearningContentRepositoryInterface $content,
        private readonly ContentPracticeMapperService $mapper,
    ) {}

    /**
     * Build a practice pack for one JSON source file.
     *
     * @return array<string, mixed>|null
     */
    public function build(string $sourceKey): ?array
    {
        $source = $this->content->findSource($sourceKey);

        if ($source === null) {
            return null;
        }

        $mapped = collect($this->mapper->map([
            'source_key' => $sourceKey,
            'limit' => 50,
        ])['items']);

        $technologies = $mapped
            ->groupBy('technology')
            ->map(fn ($items, string $technology): array => [
                'technology' => $technology,
                'record_count' => $items->count(),
                'practice' => $items->first()['practice'],
                'sample' => [
                    'record_id' => $items->first()['id'],
                    'title' => $items->first()['content']['title'],
                    'task' => $items->first()['task'],
                ],
                'drill_query' => [
                    'record_id' => $items->first()['id'],
                    'source_key' => $sourceKey,
                    'technology' => $technology,
                ],
            ])
            ->sortByDesc('record_count')
            ->values()
            ->all();

        return [
            'source' => [
                'key' => $source['key'],
                'path' => $source['path'],
                'family' => $source['family'],
                'topic' => $source['topic'],
                'language' => $source['language'],
                'title' => $source['title'],
            ],
            'record_count' => $mapped->count(),
            'technology_count' => count($technologies),
            'technologies' => $technologies,
            'commands' => [
                'php artisan test --filter SourcePracticePackTest',
                'php artisan test',
                'vendor\\bin\\pint --test',
            ],
            'workflow' => [
                'Open the source JSON and read one question or topic.',
                'Open the matching sample drill for its inferred technology.',
                'Implement the smallest Laravel change in the suggested files.',
                'Run the focused test, then the full suite and Pint.',
            ],
        ];
    }
}
