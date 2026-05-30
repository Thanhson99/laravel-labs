<?php

declare(strict_types=1);

namespace App\Repositories\Json;

use App\Repositories\Contracts\LearningContentRepositoryInterface;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\File;
use JsonException;
use RuntimeException;

final class JsonLearningContentRepository implements LearningContentRepositoryInterface
{
    /**
     * Cache decoded source data for the current request.
     *
     * @var array<int, array<string, mixed>>|null
     */
    private ?array $sources = null;

    /**
     * Create a repository that reads the existing Laravel Labs JSON content.
     */
    public function __construct(private readonly string $contentPath) {}

    /**
     * Return all JSON sources discovered from the configured content path.
     *
     * @return array<int, array<string, mixed>>
     */
    public function sources(): array
    {
        if ($this->sources !== null) {
            return $this->sources;
        }

        if (! File::isDirectory($this->contentPath)) {
            throw new RuntimeException(sprintf('Learning content path does not exist: %s', $this->contentPath));
        }

        $files = collect(File::allFiles($this->contentPath))
            ->filter(fn ($file): bool => $file->getExtension() === 'json')
            ->sortBy(fn ($file): string => $file->getRelativePathname())
            ->values();

        $this->sources = $files
            ->map(fn ($file): array => $this->decodeSource($file->getPathname(), $file->getRelativePathname()))
            ->all();

        return $this->sources;
    }

    /**
     * Return a flattened question and topic item bank from all JSON content.
     *
     * @param  array{language?: string|null, family?: string|null, search?: string|null}  $filters
     * @return array<int, array<string, mixed>>
     */
    public function questions(array $filters = []): array
    {
        $language = $filters['language'] ?? null;
        $family = $filters['family'] ?? null;
        $search = mb_strtolower(trim((string) ($filters['search'] ?? '')));

        return collect($this->sources())
            ->flatMap(fn (array $source): array => $this->extractQuestions($source))
            ->when($language, fn ($items) => $items->where('language', $language))
            ->when($family, fn ($items) => $items->where('family', $family))
            ->when($search !== '', function ($items) use ($search) {
                return $items->filter(fn (array $item): bool => $this->matchesSearch($item, $search));
            })
            ->values()
            ->all();
    }

    /**
     * Find one JSON source by its stable source key.
     *
     * @return array<string, mixed>|null
     */
    public function findSource(string $sourceKey): ?array
    {
        return collect($this->sources())
            ->first(fn (array $source): bool => $source['key'] === $sourceKey);
    }

    /**
     * Return integration statistics for the hub dashboard.
     *
     * @return array<string, int>
     */
    public function statistics(): array
    {
        $sources = $this->sources();
        $questions = $this->questions();

        return [
            'json_files' => count($sources),
            'families' => collect($sources)->pluck('family')->unique()->count(),
            'languages' => collect($sources)->pluck('language')->filter()->unique()->count(),
            'question_items' => count($questions),
            'code_snippets' => collect($questions)->filter(fn (array $item): bool => filled($item['code'] ?? null))->count(),
        ];
    }

    /**
     * Decode and normalize one source file.
     *
     * @return array<string, mixed>
     */
    private function decodeSource(string $path, string $relativePath): array
    {
        try {
            $payload = json_decode(File::get($path), true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException $exception) {
            throw new RuntimeException(sprintf('Invalid JSON source: %s', $relativePath), previous: $exception);
        }

        $normalizedPath = str_replace('\\', '/', $relativePath);
        $pathParts = explode('/', $normalizedPath);
        $fileName = basename($normalizedPath, '.json');
        $language = $this->detectLanguage($fileName);
        $topic = preg_replace('/\.(en|vi)$/', '', $fileName) ?: $fileName;
        $family = count($pathParts) > 1 ? $pathParts[0] : 'site';

        return [
            'key' => str_replace(['/', '.'], '-', $normalizedPath),
            'path' => $normalizedPath,
            'family' => $family,
            'topic' => $topic,
            'language' => $language,
            'title' => $this->sourceTitle($payload, $topic),
            'payload' => $payload,
        ];
    }

    /**
     * Extract question-like records from a decoded source.
     *
     * @return array<int, array<string, mixed>>
     */
    private function extractQuestions(array $source): array
    {
        $payload = $source['payload'];
        $records = [];

        foreach ((array) Arr::get($payload, 'questions', []) as $index => $question) {
            if (is_array($question)) {
                $records[] = $this->questionRecord($source, $question, $index, 'question');
            }
        }

        foreach ((array) Arr::get($payload, 'items', []) as $index => $item) {
            if (is_array($item)) {
                $records[] = $this->questionRecord($source, $item, $index, 'item');
            }
        }

        foreach ((array) Arr::get($payload, 'phases', []) as $phaseIndex => $phase) {
            if (! is_array($phase)) {
                continue;
            }

            foreach ((array) Arr::get($phase, 'topics', []) as $topicIndex => $topic) {
                if (is_array($topic)) {
                    $records[] = $this->questionRecord($source, $topic, $topicIndex, 'phase-topic', (string) ($phase['title'] ?? ''));
                }
            }

            foreach ((array) Arr::get($phase, 'examples', []) as $exampleIndex => $example) {
                if (is_array($example)) {
                    $records[] = $this->questionRecord($source, $example, $exampleIndex, 'example', (string) ($phase['title'] ?? ''));
                }
            }
        }

        foreach ((array) Arr::get($payload, 'sections', []) as $sectionIndex => $section) {
            if (! is_array($section)) {
                continue;
            }

            foreach ((array) Arr::get($section, 'items', []) as $itemIndex => $item) {
                if (is_array($item)) {
                    $records[] = $this->questionRecord($source, $item, $itemIndex, 'section-item', (string) ($section['heading'] ?? $sectionIndex));
                }
            }
        }

        return $records;
    }

    /**
     * Normalize one JSON item into a renderable question-bank record.
     *
     * @return array<string, mixed>
     */
    private function questionRecord(array $source, array $item, int $index, string $type, string $group = ''): array
    {
        $title = (string) ($item['question'] ?? $item['title'] ?? $item['term'] ?? $item['label'] ?? 'Untitled item');

        return [
            'id' => sprintf('%s-%s-%d', $source['key'], $type, $index + 1),
            'source_key' => $source['key'],
            'source_path' => $source['path'],
            'source_title' => $source['title'],
            'family' => $source['family'],
            'topic' => $source['topic'],
            'language' => $source['language'],
            'type' => $type,
            'group' => $group,
            'title' => $title,
            'body' => (string) ($item['body'] ?? $item['description'] ?? ''),
            'answer' => (string) ($item['answer'] ?? ''),
            'note' => (string) ($item['note'] ?? ''),
            'tip' => (string) ($item['tip'] ?? ''),
            'code' => (string) ($item['code'] ?? ''),
            'bullets' => array_values(array_filter((array) ($item['bullets'] ?? []), 'is_string')),
        ];
    }

    /**
     * Match natural user searches against content text, examples, and supporting notes.
     */
    private function matchesSearch(array $item, string $search): bool
    {
        $haystack = mb_strtolower(implode(' ', array_filter([
            $item['title'] ?? '',
            $item['body'] ?? '',
            $item['answer'] ?? '',
            $item['note'] ?? '',
            $item['tip'] ?? '',
            $item['code'] ?? '',
            implode(' ', (array) ($item['bullets'] ?? [])),
            $item['source_title'] ?? '',
            $item['family'] ?? '',
        ])));

        if (str_contains($haystack, $search)) {
            return true;
        }

        $normalizedHaystack = $this->normalizeSearchText($haystack);
        $normalizedSearch = $this->normalizeSearchText($search);

        if ($normalizedSearch === '') {
            return true;
        }

        if (str_contains($normalizedHaystack, $normalizedSearch)) {
            return true;
        }

        $plainSearch = trim(preg_replace('/\s+/u', ' ', mb_strtolower($search)) ?? '');

        if ($normalizedSearch === $plainSearch) {
            return false;
        }

        $tokens = array_values(array_unique(array_filter(explode(' ', $normalizedSearch))));

        foreach ($tokens as $token) {
            if (! str_contains(" {$normalizedHaystack} ", " {$token} ")) {
                return false;
            }
        }

        return $tokens !== [];
    }

    /**
     * Normalize punctuation-heavy terms like `this`, @vite(...), and call/apply/bind for search.
     */
    private function normalizeSearchText(string $value): string
    {
        $normalized = preg_replace('/[^\p{L}\p{N}]+/u', ' ', mb_strtolower($value)) ?? '';

        return trim(preg_replace('/\s+/u', ' ', $normalized) ?? '');
    }

    /**
     * Detect the language suffix used by the content file.
     */
    private function detectLanguage(string $fileName): ?string
    {
        if (str_ends_with($fileName, '.en')) {
            return 'en';
        }

        if (str_ends_with($fileName, '.vi')) {
            return 'vi';
        }

        return null;
    }

    /**
     * Choose a readable source title from known JSON shapes.
     */
    private function sourceTitle(array $payload, string $fallback): string
    {
        $title = Arr::get($payload, 'heading')
            ?? Arr::get($payload, 'title')
            ?? Arr::get($payload, 'pages.landing.title')
            ?? $fallback;

        return is_string($title) ? $title : $fallback;
    }
}
