<?php

declare(strict_types=1);

namespace App\Services\Practice;

/**
 * Detect learning records that describe AI agent memory contracts.
 */
final class AiAgentMemoryTopicService
{
    /**
     * Determine whether text describes AI agent memory types.
     */
    public static function matchesText(string $text): bool
    {
        $haystack = strtolower($text);

        return str_contains($haystack, 'agent memory')
            || str_contains($haystack, 'ai agent memory')
            || str_contains($haystack, 'working memory')
            || str_contains($haystack, 'episodic memory')
            || str_contains($haystack, 'semantic memory')
            || str_contains($haystack, 'procedural memory');
    }

    /**
     * Determine whether a mapped content item is about AI agent memory.
     *
     * @param  array<string, mixed>  $item
     * @param  array<string, mixed>  $filters
     */
    public static function matchesRecord(array $item, array $filters = []): bool
    {
        return self::matchesText(implode(' ', [
            $filters['search'] ?? '',
            $item['content']['title'] ?? '',
            $item['content']['summary'] ?? '',
            $item['content']['body'] ?? '',
            $item['task'] ?? '',
        ]));
    }

    /**
     * Determine whether generated implementation tasks are about AI agent memory.
     *
     * @param  array<int, array<string, mixed>>  $tasks
     * @param  array<string, mixed>  $filters
     */
    public static function matchesTasks(array $tasks, array $filters = []): bool
    {
        return self::matchesText(implode(' ', [
            $filters['search'] ?? '',
            ...collect($tasks)
                ->flatMap(fn (array $task): array => [
                    $task['title'] ?? '',
                    $task['task'] ?? '',
                    $task['source_path'] ?? '',
                ])
                ->all(),
        ]));
    }

    /**
     * Determine whether source-backed records describe AI agent memory.
     *
     * @param  array<int, array<string, mixed>>  $sourceItems
     */
    public static function matchesSourceItems(array $sourceItems): bool
    {
        return self::matchesText(implode(' ', collect($sourceItems)
            ->flatMap(fn (array $item): array => [
                $item['content']['title'] ?? '',
                $item['content']['summary'] ?? '',
                $item['content']['body'] ?? '',
                $item['task'] ?? '',
                $item['source']['path'] ?? '',
            ])
            ->all()));
    }

    /**
     * Determine whether a generated portfolio artifact describes AI agent memory.
     *
     * @param  array<string, mixed>  $artifact
     */
    public static function matchesArtifact(array $artifact): bool
    {
        return self::matchesText(implode(' ', [
            $artifact['commit_plan']['branch'] ?? '',
            $artifact['portfolio']['headline'] ?? '',
            ...($artifact['portfolio']['summary'] ?? []),
            ...collect($artifact['portfolio']['source_coverage'] ?? [])
                ->flatMap(fn (array $record): array => [
                    $record['title'] ?? '',
                    $record['source_path'] ?? '',
                    $record['task'] ?? '',
                ])
                ->all(),
        ]));
    }

    /**
     * Determine whether a remediation payload describes AI agent memory.
     *
     * @param  array<string, mixed>  $remediation
     */
    public static function matchesRemediation(array $remediation): bool
    {
        return self::matchesText(implode(' ', collect($remediation['tasks'] ?? [])
            ->flatMap(fn (array $task): array => [
                $task['label'] ?? '',
                $task['problem'] ?? '',
                $task['action'] ?? '',
                $task['evidence'] ?? '',
                $task['focus_file'] ?? '',
            ])
            ->all()));
    }

    /**
     * Determine whether a spaced-review payload describes AI agent memory.
     *
     * @param  array<string, mixed>  $review
     */
    public static function matchesReview(array $review): bool
    {
        return self::matchesText(implode(' ', [
            $review['checkpoint']['title'] ?? '',
            ...collect($review['cards'] ?? [])
                ->flatMap(fn (array $card): array => [
                    $card['recall_prompt'] ?? '',
                    $card['coding_action'] ?? '',
                    $card['evidence_recheck'] ?? '',
                ])
                ->all(),
        ]));
    }
}
