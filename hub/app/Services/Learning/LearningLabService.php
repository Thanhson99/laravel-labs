<?php

declare(strict_types=1);

namespace App\Services\Learning;

use App\Repositories\Contracts\LearningContentRepositoryInterface;
use Illuminate\Support\Collection;

final class LearningLabService
{
    private const DEFAULT_LIMIT = 8;

    private const MAX_LIMIT = 20;

    /**
     * Create a service for generating hands-on labs from learning content.
     */
    public function __construct(private readonly LearningContentRepositoryInterface $content) {}

    /**
     * Build practice labs from filtered content.
     *
     * @param  array{language?: string|null, family?: string|null, track?: string|null, search?: string|null, limit?: int|string|null}  $filters
     * @return array{labs: array<int, array<string, mixed>>, meta: array<string, mixed>}
     */
    public function build(array $filters = []): array
    {
        $limit = $this->normalizeLimit($filters['limit'] ?? null);
        $track = $filters['track'] ?? null;
        $language = $filters['language'] ?? 'en';
        $questionFilters = [
            'language' => $language,
            'family' => $filters['family'] ?? null,
            'search' => $filters['search'] ?? null,
        ];

        $records = collect($this->content->questions($questionFilters))
            ->filter(fn (array $record): bool => filled($record['title'] ?? null))
            ->when($track, fn (Collection $items, string $trackName): Collection => $items->filter(
                fn (array $record): bool => $this->trackFor($record) === $trackName
            ))
            ->values();

        $labs = $records
            ->groupBy(fn (array $record): string => (string) $record['source_key'])
            ->map(fn (Collection $items): array => $this->toLab($items))
            ->sortByDesc('record_count')
            ->take($limit)
            ->values()
            ->map(fn (array $lab, int $index): array => $lab + ['number' => $index + 1])
            ->all();

        return [
            'labs' => $labs,
            'meta' => [
                'filters' => $questionFilters + [
                    'track' => $track,
                    'limit' => $limit,
                ],
                'available_records' => $records->count(),
                'count' => count($labs),
                'tracks' => $this->tracks(),
            ],
        ];
    }

    /**
     * Return supported hands-on practice tracks.
     *
     * @return array<int, string>
     */
    public function tracks(): array
    {
        return [
            'php',
            'laravel',
            'api',
            'testing',
            'docker-devops',
            'ai-workflow',
            'interview',
        ];
    }

    /**
     * Convert grouped source records into one hands-on lab.
     *
     * @param  Collection<int, array<string, mixed>>  $records
     * @return array<string, mixed>
     */
    private function toLab(Collection $records): array
    {
        $first = $records->first();
        $track = $this->trackFor($first);
        $template = $this->templateFor($track);
        $topic = (string) ($first['topic'] ?? 'topic');

        return [
            'id' => sprintf('lab-%s-%s', $track, $first['source_key']),
            'track' => $track,
            'title' => $this->titleFor($track, (string) ($first['source_title'] ?? $topic)),
            'source' => [
                'key' => $first['source_key'],
                'path' => $first['source_path'],
                'family' => $first['family'],
                'topic' => $topic,
                'language' => $first['language'],
            ],
            'record_count' => $records->count(),
            'goal' => sprintf($template['goal'], $topic),
            'tasks' => $this->tasksFor($template, $records),
            'files' => $template['files'],
            'commands' => $template['commands'],
            'done' => $template['done'],
        ];
    }

    /**
     * Build concrete task list from representative records.
     *
     * @param  array<string, mixed>  $template
     * @param  Collection<int, array<string, mixed>>  $records
     * @return array<int, string>
     */
    private function tasksFor(array $template, Collection $records): array
    {
        $recordTasks = $records
            ->pluck('title')
            ->filter()
            ->take(4)
            ->map(fn (string $title): string => sprintf('Apply this concept in code: %s', $title))
            ->values()
            ->all();

        return array_values(array_merge($template['tasks'], $recordTasks));
    }

    /**
     * Infer a practical technology track from a JSON record.
     */
    private function trackFor(array $record): string
    {
        $haystack = mb_strtolower(implode(' ', [
            $record['family'] ?? '',
            $record['topic'] ?? '',
            $record['source_path'] ?? '',
            $record['title'] ?? '',
            $record['body'] ?? '',
        ]));

        return match (true) {
            str_contains($haystack, 'test') || str_contains($haystack, 'quality') => 'testing',
            str_contains($haystack, 'api') || str_contains($haystack, 'webhook') || str_contains($haystack, 'sanctum') => 'api',
            str_contains($haystack, 'docker') || str_contains($haystack, 'sail') || str_contains($haystack, 'deploy') || str_contains($haystack, 'devops') => 'docker-devops',
            str_contains($haystack, 'ai') || str_contains($haystack, 'prompt') || str_contains($haystack, 'review') => 'ai-workflow',
            ($record['family'] ?? '') === 'interview' => 'interview',
            ($record['family'] ?? '') === 'php' => 'php',
            default => 'laravel',
        };
    }

    /**
     * Return lab template for one technology track.
     *
     * @return array{goal: string, tasks: array<int, string>, files: array<int, string>, commands: array<int, string>, done: array<int, string>}
     */
    private function templateFor(string $track): array
    {
        return match ($track) {
            'php' => [
                'goal' => 'Build a small PHP script that demonstrates the %s topic with input, output, and safe handling.',
                'tasks' => [
                    'Create a small standalone PHP file for the topic.',
                    'Add at least one function with typed parameters and return type.',
                    'Handle invalid input with a clear guard clause.',
                ],
                'files' => ['practice/php/{topic}.php'],
                'commands' => ['php practice/php/{topic}.php'],
                'done' => ['The script runs from CLI.', 'Invalid input is handled.', 'Output is easy to inspect.'],
            ],
            'api' => [
                'goal' => 'Build a Laravel API slice for %s with request validation and a predictable JSON response.',
                'tasks' => [
                    'Create a route under routes/api.php.',
                    'Create a Form Request for validation.',
                    'Return a stable JSON shape with data and meta or message.',
                ],
                'files' => ['routes/api.php', 'app/Http/Requests', 'app/Http/Controllers/Api'],
                'commands' => ['php artisan route:list', 'php artisan test --filter Api'],
                'done' => ['Validation errors return 422.', 'Success response returns JSON.', 'Feature test covers the route.'],
            ],
            'testing' => [
                'goal' => 'Add test coverage around the %s behavior and make the failure mode visible.',
                'tasks' => [
                    'Write one happy-path feature test.',
                    'Write one validation or failure-path test.',
                    'Run the targeted test before and after the implementation.',
                ],
                'files' => ['tests/Feature', 'tests/Unit'],
                'commands' => ['php artisan test', 'vendor/bin/pint --test'],
                'done' => ['Tests fail for the right reason before the fix when possible.', 'Tests pass after the fix.', 'Assertions verify behavior, not only status codes.'],
            ],
            'docker-devops' => [
                'goal' => 'Make the %s workflow reproducible through Docker or environment configuration.',
                'tasks' => [
                    'Document the required env values.',
                    'Add or update compose/service configuration.',
                    'Add a smoke-check command that proves the app is reachable.',
                ],
                'files' => ['Dockerfile', 'docker-compose.yml', '.env.example', 'README.md'],
                'commands' => ['docker-compose config', 'docker-compose up --build'],
                'done' => ['Compose config validates.', 'The app starts from a clean environment.', 'Runtime port and content path are documented.'],
            ],
            'ai-workflow' => [
                'goal' => 'Use AI-assisted coding discipline to implement or review the %s topic without skipping verification.',
                'tasks' => [
                    'Write a short prompt with scope, files, and acceptance criteria.',
                    'Review generated changes for security, tests, and maintainability.',
                    'Record verification commands and any blockers.',
                ],
                'files' => ['docs/ai-change-log.md', 'docs/ai-review-checklist.md'],
                'commands' => ['git diff --check', 'php artisan test'],
                'done' => ['Prompt is specific.', 'Diff is reviewed.', 'Verification is recorded.'],
            ],
            'interview' => [
                'goal' => 'Turn the %s interview topic into a practical explanation plus code-backed example.',
                'tasks' => [
                    'Write a concise answer in your own words.',
                    'Create a tiny Laravel or PHP example that proves the concept.',
                    'List one production failure mode related to the answer.',
                ],
                'files' => ['practice/interview/{topic}.md', 'practice/examples'],
                'commands' => ['php artisan test', 'php -l path/to/example.php'],
                'done' => ['Answer is practical.', 'Example runs or is syntax-valid.', 'Failure mode is concrete.'],
            ],
            default => [
                'goal' => 'Build a Laravel feature slice that practices the %s topic end to end.',
                'tasks' => [
                    'Add a route and controller method.',
                    'Move non-trivial logic into a service.',
                    'Render or return escaped user-facing content.',
                    'Add a focused feature test.',
                ],
                'files' => ['routes/web.php', 'app/Http/Controllers', 'app/Services', 'tests/Feature'],
                'commands' => ['php artisan route:list', 'php artisan test', 'vendor/bin/pint --test'],
                'done' => ['Controller stays thin.', 'Service owns workflow logic.', 'Feature test passes.', 'Pint passes.'],
            ],
        };
    }

    /**
     * Build a readable lab title.
     */
    private function titleFor(string $track, string $sourceTitle): string
    {
        return sprintf('%s lab: %s', $this->displayTrackName($track), $sourceTitle);
    }

    /**
     * Build a human-readable track label.
     */
    private function displayTrackName(string $track): string
    {
        return match ($track) {
            'ai-workflow' => 'AI Workflow',
            'docker-devops' => 'Docker DevOps',
            default => str($track)->replace('-', ' ')->title()->toString(),
        };
    }

    /**
     * Normalize requested lab count into an allowed range.
     */
    private function normalizeLimit(int|string|null $limit): int
    {
        $value = is_numeric($limit) ? (int) $limit : self::DEFAULT_LIMIT;

        return max(1, min(self::MAX_LIMIT, $value));
    }
}
