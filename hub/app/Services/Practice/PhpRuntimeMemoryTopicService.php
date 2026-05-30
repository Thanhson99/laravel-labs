<?php

declare(strict_types=1);

namespace App\Services\Practice;

final class PhpRuntimeMemoryTopicService
{
    /**
     * Determine whether free text is about PHP stack versus heap runtime memory.
     */
    public static function matchesText(string $text): bool
    {
        $haystack = strtolower($text);

        foreach (self::needles() as $needle) {
            if (str_contains($haystack, $needle)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Determine whether a mapped content record is about stack versus heap memory.
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
            $item['content']['group'] ?? '',
            $item['task'] ?? '',
        ]));
    }

    /**
     * Determine whether source-backed example records are about stack versus heap memory.
     *
     * @param  array<int, array<string, mixed>>  $sourceItems
     */
    public static function matchesSourceItems(array $sourceItems): bool
    {
        return self::matchesText(implode(' ', collect($sourceItems)
            ->flatMap(fn (array $item): array => [
                $item['content']['title'] ?? '',
                $item['content']['summary'] ?? '',
                $item['task'] ?? '',
                $item['source']['path'] ?? '',
            ])
            ->all()));
    }

    /**
     * Determine whether implementation lab tasks are about stack versus heap memory.
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
     * Determine whether generated lab phases are about stack versus heap memory.
     */
    public static function matchesLab(array $lab): bool
    {
        return self::matchesText(implode(' ', collect($lab['phases'] ?? [])
            ->flatMap(fn (array $phase): array => [
                $phase['label'] ?? '',
                $phase['goal'] ?? '',
                ...($phase['tasks'] ?? []),
            ])
            ->all()));
    }

    /**
     * Determine whether an interview pack or portfolio artifact is about stack versus heap memory.
     */
    public static function matchesArtifact(array $artifact): bool
    {
        return self::matchesText(implode(' ', collect($artifact['portfolio']['source_coverage'] ?? [])
            ->flatMap(fn (array $record): array => [
                $record['title'] ?? '',
                $record['summary'] ?? '',
                $record['source_path'] ?? '',
                $record['task'] ?? '',
            ])
            ->all()));
    }

    /**
     * Determine whether an interview pack is about stack versus heap memory.
     *
     * @param  array<string, mixed>  $pack
     * @param  array<string, mixed>  $filters
     */
    public static function matchesPack(array $pack, array $filters = []): bool
    {
        return self::matchesText(implode(' ', [
            $filters['search'] ?? '',
            ...collect($pack['artifact']['portfolio']['source_coverage'] ?? [])
                ->flatMap(fn (array $record): array => [
                    $record['title'] ?? '',
                    $record['summary'] ?? '',
                    $record['source_path'] ?? '',
                    $record['task'] ?? '',
                ])
                ->all(),
        ]));
    }

    /**
     * Determine whether a spaced review payload is about stack versus heap memory.
     */
    public static function matchesReview(array $review): bool
    {
        return self::matchesText(implode(' ', collect($review['cards'] ?? [])
            ->flatMap(fn (array $card): array => [
                $card['recall_prompt'] ?? '',
                $card['coding_action'] ?? '',
                $card['evidence_recheck'] ?? '',
            ])
            ->all()));
    }

    /**
     * Determine whether a remediation payload is based on the PHP runtime-memory rubric.
     */
    public static function matchesRemediation(array $remediation): bool
    {
        $labels = collect($remediation['tasks'] ?? [])
            ->pluck('label')
            ->implode(' ');

        return self::matchesText($labels);
    }

    /**
     * Return all text signals used to recognize this topic across content and generated workflows.
     *
     * @return array<int, string>
     */
    private static function needles(): array
    {
        return [
            'stack memory',
            'heap memory',
            'stack vs heap',
            'call frame',
            'call frames',
            'heap-backed',
            'runtime-memory',
            'call-frame model',
            'heap-backed data',
            'cleanup and references',
            'vùng nhớ stack',
            'vùng nhớ heap',
        ];
    }
}
