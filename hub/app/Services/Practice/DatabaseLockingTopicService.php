<?php

declare(strict_types=1);

namespace App\Services\Practice;

/**
 * Detect learning records that describe database locking and concurrency control.
 */
final class DatabaseLockingTopicService
{
    /**
     * Determine whether a mapped practice item is about database locking.
     *
     * @param  array<string, mixed>  $item
     * @param  array<string, mixed>  $filters
     */
    public static function matchesRecord(array $item, array $filters = []): bool
    {
        return self::containsLockingLanguage(self::textFromValue([$item, $filters]));
    }

    /**
     * Determine whether implementation-lab tasks are about database locking.
     *
     * @param  array<int, array<string, mixed>>  $tasks
     * @param  array<string, mixed>  $filters
     */
    public static function matchesTasks(array $tasks, array $filters = []): bool
    {
        return self::containsLockingLanguage(self::textFromValue([$tasks, $filters]));
    }

    /**
     * Check for high-signal database-locking language.
     */
    private static function containsLockingLanguage(string $haystack): bool
    {
        return str_contains($haystack, 'database locking')
            || str_contains($haystack, 'locking in database')
            || str_contains($haystack, 'lockforupdate')
            || str_contains($haystack, 'lock for update')
            || str_contains($haystack, 'row lock')
            || str_contains($haystack, 'table lock')
            || str_contains($haystack, 'deadlock')
            || str_contains($haystack, 'lock contention')
            || str_contains($haystack, 'race condition')
            || str_contains($haystack, 'concurrent request')
            || str_contains($haystack, 'concurrency control');
    }

    /**
     * Flatten nested payloads into searchable lowercase text.
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
