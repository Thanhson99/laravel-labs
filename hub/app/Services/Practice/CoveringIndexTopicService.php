<?php

declare(strict_types=1);

namespace App\Services\Practice;

final class CoveringIndexTopicService
{
    /**
     * Determine whether a mapped practice item is about PostgreSQL covering indexes.
     *
     * @param  array<string, mixed>  $item
     * @param  array<string, mixed>  $filters
     */
    public static function matchesRecord(array $item, array $filters = []): bool
    {
        return self::containsCoveringIndexLanguage(self::textFor($item, $filters));
    }

    /**
     * Determine whether implementation-lab tasks are about covering indexes.
     *
     * @param  array<int, array<string, mixed>>  $tasks
     * @param  array<string, mixed>  $filters
     */
    public static function matchesTasks(array $tasks, array $filters = []): bool
    {
        $haystack = strtolower(implode(' ', [
            $filters['search'] ?? '',
            ...collect($tasks)
                ->flatMap(fn (array $task): array => [
                    $task['title'] ?? '',
                    $task['task'] ?? '',
                    $task['source_path'] ?? '',
                    implode(' ', $task['files'] ?? []),
                ])
                ->all(),
        ]));

        return self::containsCoveringIndexLanguage($haystack);
    }

    /**
     * Determine whether a portfolio artifact is about covering indexes.
     *
     * @param  array<string, mixed>  $artifact
     */
    public static function matchesArtifact(array $artifact): bool
    {
        return self::containsCoveringIndexLanguage(self::textFromValue($artifact));
    }

    /**
     * Determine whether a pipeline item is about covering indexes.
     *
     * @param  array<string, mixed>  $item
     */
    public static function matchesPipelineItem(array $item): bool
    {
        return self::containsCoveringIndexLanguage(self::textFromValue($item));
    }

    /**
     * Determine whether a spaced-review payload is about covering indexes.
     *
     * @param  array<string, mixed>  $review
     */
    public static function matchesReview(array $review): bool
    {
        return self::containsCoveringIndexLanguage(self::textFromValue($review));
    }

    /**
     * Determine whether an interview pack is about covering indexes.
     *
     * @param  array<string, mixed>  $pack
     * @param  array<string, mixed>  $filters
     */
    public static function matchesPack(array $pack, array $filters = []): bool
    {
        return self::containsCoveringIndexLanguage(self::textFromValue([$pack, $filters]));
    }

    /**
     * Determine whether a remediation plan is about covering indexes.
     *
     * @param  array<string, mixed>  $remediation
     */
    public static function matchesRemediation(array $remediation): bool
    {
        return self::containsCoveringIndexLanguage(self::textFromValue($remediation));
    }

    /**
     * Build normalized searchable text for one mapped item.
     *
     * @param  array<string, mixed>  $item
     * @param  array<string, mixed>  $filters
     */
    private static function textFor(array $item, array $filters = []): string
    {
        $content = $item['content'] ?? [];

        return strtolower(implode(' ', [
            $filters['search'] ?? '',
            $item['task'] ?? '',
            is_array($content) ? ($content['title'] ?? '') : '',
            is_array($content) ? ($content['body'] ?? '') : '',
            is_array($content) ? ($content['summary'] ?? '') : '',
            is_array($content) ? ($content['group'] ?? '') : '',
        ]));
    }

    /**
     * Check for high-signal covering-index language.
     */
    private static function containsCoveringIndexLanguage(string $haystack): bool
    {
        return str_contains($haystack, 'covering index')
            || str_contains($haystack, 'index only scan')
            || str_contains($haystack, 'heap fetch')
            || str_contains($haystack, 'visibility map')
            || str_contains($haystack, 'include columns')
            || str_contains($haystack, 'included columns')
            || str_contains($haystack, 'explain analyze')
            || str_contains($haystack, 'explain (analyze')
            || str_contains($haystack, 'vacuum analyze')
            || str_contains($haystack, 'autovacuum')
            || str_contains($haystack, 'index bloat')
            || str_contains($haystack, 'write overhead');
    }

    /**
     * Flatten nested service payloads into searchable text.
     */
    private static function textFromValue(mixed $value): string
    {
        if (is_array($value)) {
            return strtolower(implode(' ', collect($value)
                ->map(fn (mixed $entry): string => self::textFromValue($entry))
                ->all()));
        }

        if (is_scalar($value) || $value === null) {
            return strtolower((string) $value);
        }

        return '';
    }
}
