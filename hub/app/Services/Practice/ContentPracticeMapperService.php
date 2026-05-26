<?php

declare(strict_types=1);

namespace App\Services\Practice;

use App\Repositories\Contracts\LearningContentRepositoryInterface;
use Illuminate\Support\Str;

final class ContentPracticeMapperService
{
    /**
     * Create a mapper from learning content records to native practice exercises.
     */
    public function __construct(
        private readonly LearningContentRepositoryInterface $content,
        private readonly PracticeCatalogService $catalog,
    ) {}

    /**
     * Build content-to-practice mappings from JSON question records.
     *
     * @param  array{family?: string|null, language?: string|null, search?: string|null, technology?: string|null, source_key?: string|null, record_id?: string|null, limit?: int|string|null}  $filters
     * @return array{items: array<int, array<string, mixed>>, meta: array<string, mixed>}
     */
    public function map(array $filters = []): array
    {
        $limit = $this->normalizeLimit($filters['limit'] ?? null);
        $technology = $filters['technology'] ?? null;
        $sourceKey = $filters['source_key'] ?? null;
        $recordId = $filters['record_id'] ?? null;

        $records = collect($this->content->questions([
            'family' => $filters['family'] ?? null,
            'language' => $filters['language'] ?? null,
            'search' => $filters['search'] ?? null,
        ]))
            ->filter(fn (array $record): bool => filled($record['title'] ?? null))
            ->when($sourceKey, fn ($items, string $value) => $items->where('source_key', $value))
            ->when($recordId, fn ($items, string $value) => $items->where('id', $value))
            ->map(fn (array $record): array => $this->toPracticeItem($record))
            ->when($technology, fn ($items, string $value) => $items->where('technology', $value))
            ->take($limit)
            ->values()
            ->all();

        return [
            'items' => $records,
            'meta' => [
                'filters' => [
                    'family' => $filters['family'] ?? null,
                    'language' => $filters['language'] ?? null,
                    'search' => $filters['search'] ?? null,
                    'technology' => $technology,
                    'source_key' => $sourceKey,
                    'record_id' => $recordId,
                    'limit' => $limit,
                ],
                'count' => count($records),
                'technologies' => $this->technologies(),
            ],
        ];
    }

    /**
     * Return technology filters supported by the mapper.
     *
     * @return array<int, string>
     */
    public function technologies(): array
    {
        return [
            'php',
            'laravel-http',
            'blade-ui',
            'database-eloquent',
            'api-validation',
            'auth-security',
            'files-media',
            'async-workflow',
            'performance-cache',
            'container-architecture',
            'realtime-events',
            'testing-quality',
            'docker-runtime',
            'ai-workflow',
            'interview',
        ];
    }

    /**
     * Infer a practice technology for a raw content record.
     */
    public function inferTechnology(array $record): string
    {
        return $this->technologyFor($record);
    }

    /**
     * Convert one content record to a code-first practice item.
     *
     * @return array<string, mixed>
     */
    private function toPracticeItem(array $record): array
    {
        $technology = $this->technologyFor($record);
        $exercise = $this->exerciseFor($technology);

        return [
            'id' => (string) $record['id'],
            'technology' => $technology,
            'source' => [
                'key' => $record['source_key'],
                'path' => $record['source_path'],
                'family' => $record['family'],
                'topic' => $record['topic'],
                'language' => $record['language'],
                'title' => $record['source_title'],
            ],
            'content' => [
                'title' => $record['title'],
                'type' => $record['type'],
                'group' => $record['group'],
            ],
            'practice' => [
                'slug' => $exercise['slug'] ?? null,
                'title' => $exercise['title'] ?? 'Create a new practice exercise',
                'track' => $exercise['track'] ?? $technology,
            ],
            'task' => $this->taskFor($technology, (string) $record['title']),
        ];
    }

    /**
     * Infer the closest practice technology from content text and source path.
     */
    private function technologyFor(array $record): string
    {
        $haystack = Str::lower(implode(' ', [
            $record['family'] ?? '',
            $record['topic'] ?? '',
            $record['source_path'] ?? '',
            $record['title'] ?? '',
            $record['body'] ?? '',
            $record['answer'] ?? '',
        ]));

        return match (true) {
            ($record['family'] ?? '') === 'php' => 'php',
            ($record['family'] ?? '') === 'interview' => 'interview',
            str_contains($haystack, 'test') || str_contains($haystack, 'pint') || str_contains($haystack, 'quality') => 'testing-quality',
            str_contains($haystack, 'api') || str_contains($haystack, 'request') || str_contains($haystack, 'validation') || str_contains($haystack, 'sanctum') => 'api-validation',
            str_contains($haystack, 'policy') || str_contains($haystack, 'gate') || str_contains($haystack, 'auth') || str_contains($haystack, 'security') || str_contains($haystack, 'csrf') || str_contains($haystack, 'xss') => 'auth-security',
            str_contains($haystack, 'migration') || str_contains($haystack, 'eloquent') || str_contains($haystack, 'database') || str_contains($haystack, 'model') || str_contains($haystack, 'query') => 'database-eloquent',
            str_contains($haystack, 'upload') || str_contains($haystack, 'file') || str_contains($haystack, 'storage') || str_contains($haystack, 'media') || str_contains($haystack, 'filesystem') => 'files-media',
            str_contains($haystack, 'queue') || str_contains($haystack, 'job') || str_contains($haystack, 'event') || str_contains($haystack, 'listener') || str_contains($haystack, 'mail') || str_contains($haystack, 'notification') => 'async-workflow',
            str_contains($haystack, 'cache') || str_contains($haystack, 'performance') || str_contains($haystack, 'meilisearch') || str_contains($haystack, 'search') || str_contains($haystack, 'index') => 'performance-cache',
            str_contains($haystack, 'container') || str_contains($haystack, 'binding') || str_contains($haystack, 'provider') || str_contains($haystack, 'dependency injection') => 'container-architecture',
            str_contains($haystack, 'broadcast') || str_contains($haystack, 'websocket') || str_contains($haystack, 'realtime') || str_contains($haystack, 'pusher') => 'realtime-events',
            str_contains($haystack, 'docker') || str_contains($haystack, 'sail') || str_contains($haystack, 'deploy') || str_contains($haystack, 'devops') => 'docker-runtime',
            str_contains($haystack, 'blade') || str_contains($haystack, 'livewire') || str_contains($haystack, 'vite') || str_contains($haystack, 'frontend') || str_contains($haystack, 'alpine') => 'blade-ui',
            str_contains($haystack, 'blade') || str_contains($haystack, 'controller') || str_contains($haystack, 'route') => 'laravel-http',
            preg_match('/\bai\b/', $haystack) === 1 || str_contains($haystack, 'prompt') || str_contains($haystack, 'review') => 'ai-workflow',
            default => 'laravel-http',
        };
    }

    /**
     * Find the current native exercise that best matches a technology.
     *
     * @return array<string, mixed>|null
     */
    private function exerciseFor(string $technology): ?array
    {
        $slug = match ($technology) {
            'php' => 'php-cli-input-normalizer',
            'api-validation' => 'api-form-request-slice',
            'testing-quality' => 'feature-test-route-behavior',
            'docker-runtime' => 'docker-compose-smoke-check',
            default => 'laravel-thin-controller',
        };

        return $this->catalog->findExercise($slug);
    }

    /**
     * Build one concrete coding task from a content topic.
     */
    private function taskFor(string $technology, string $title): string
    {
        return match ($technology) {
            'php' => sprintf('Write a typed PHP function that demonstrates: %s', $title),
            'api-validation' => sprintf('Create a Form Request and API response for: %s', $title),
            'auth-security' => sprintf('Create a policy-backed authorization example for: %s', $title),
            'database-eloquent' => sprintf('Create a migration/model/query example for: %s', $title),
            'files-media' => sprintf('Create a validated storage lifecycle example for: %s', $title),
            'async-workflow' => sprintf('Create a queued job or event/listener example for: %s', $title),
            'performance-cache' => sprintf('Create a cache or query-performance example for: %s', $title),
            'container-architecture' => sprintf('Create a container binding and dependency-injection example for: %s', $title),
            'realtime-events' => sprintf('Create an event and broadcast planning example for: %s', $title),
            'blade-ui' => sprintf('Create a Blade view/component example for: %s', $title),
            'testing-quality' => sprintf('Write a failing-first test that proves: %s', $title),
            'docker-runtime' => sprintf('Add or verify a runtime smoke check for: %s', $title),
            'ai-workflow' => sprintf('Write an implementation prompt and review checklist for: %s', $title),
            'interview' => sprintf('Turn this interview answer into a tiny code-backed example: %s', $title),
            default => sprintf('Build a thin Laravel route/controller/service slice for: %s', $title),
        };
    }

    /**
     * Normalize requested mapping count.
     */
    private function normalizeLimit(int|string|null $limit): int
    {
        $value = is_numeric($limit) ? (int) $limit : 12;

        return max(1, min(50, $value));
    }
}
