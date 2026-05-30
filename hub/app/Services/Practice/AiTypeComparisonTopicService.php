<?php

declare(strict_types=1);

namespace App\Services\Practice;

final class AiTypeComparisonTopicService
{
    /**
     * Determine whether free text compares Predictive AI and Generative AI.
     */
    public static function matchesText(string $text): bool
    {
        $haystack = strtolower($text);

        if (str_contains($haystack, 'predictive ai') && str_contains($haystack, 'generative ai')) {
            return true;
        }

        foreach (self::needles() as $needle) {
            if (str_contains($haystack, $needle)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Determine whether a mapped content record compares AI output types.
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
            $item['content']['body'] ?? '',
            $item['task'] ?? '',
        ]));
    }

    /**
     * Determine whether generated implementation tasks compare AI output types.
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
     * Determine whether source-backed records compare AI output types.
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
     * Determine whether a pipeline or quality-plan item compares AI output types.
     *
     * @param  array<string, mixed>  $item
     */
    public static function matchesPipelineItem(array $item): bool
    {
        return self::matchesText(implode(' ', [
            $item['sample']['title'] ?? '',
            $item['sample']['task'] ?? '',
            ...($item['sources'] ?? []),
        ]));
    }

    /**
     * Determine whether a portfolio artifact compares AI output types.
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
     * Determine whether an interview pack compares AI output types.
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
            ...collect($pack['questions'] ?? [])
                ->flatMap(fn (array $question): array => [
                    $question['question'] ?? '',
                    ...($question['answer_outline'] ?? []),
                ])
                ->all(),
        ]));
    }

    /**
     * Determine whether a remediation payload compares AI output types.
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
     * Determine whether a spaced review payload compares AI output types.
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
     * Return text signals used to recognize this topic across generated workflows.
     *
     * @return array<int, string>
     */
    private static function needles(): array
    {
        return [
            'predictive ai',
            'generative ai',
            'forecasting model',
            'classification model',
            'prediction score',
            'classification label',
            'generated content',
            'output contract',
        ];
    }
}
