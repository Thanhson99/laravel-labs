<?php

declare(strict_types=1);

namespace App\Services\Practice;

final class PracticeRotationLabService
{
    /**
     * Build repeatable practice rotations from mastery paths.
     */
    public function __construct(
        private readonly PracticeMasteryPathLabService $masteryPaths,
        private readonly PracticeProgressPayloadService $progressPayload,
        private readonly PracticeFilterNormalizerService $filters,
    ) {}

    /**
     * Create a day-by-day practice rotation from mastery milestones.
     *
     * @param  array{family?: string|null, language?: string|null, search?: string|null, limit?: int|string|null, phase_limit?: int|string|null, tasks_per_phase?: int|string|null, days?: int|string|null}  $filters
     * @return array<string, mixed>
     */
    public function build(array $filters = []): array
    {
        $days = $this->filters->positiveInt($filters['days'] ?? null, 5);
        $path = $this->masteryPaths->build($filters);

        $milestones = collect($path['milestones']);

        $schedule = collect(range(1, $days))
            ->map(function (int $day) use ($milestones): array {
                $milestone = $milestones->get(($day - 1) % max($milestones->count(), 1), [
                    'technology' => 'api-validation',
                    'capstone_query' => ['technology' => 'api-validation'],
                    'checkpoint_query' => ['technology' => 'api-validation'],
                    'mentor_feedback_query' => ['technology' => 'api-validation'],
                ]);

                return [
                    'day' => $day,
                    'technology' => $milestone['technology'],
                    'focus' => $this->focusForDay($day),
                    'capstone_query' => $milestone['capstone_query'],
                    'checkpoint_query' => $milestone['checkpoint_query'],
                    'mentor_feedback_query' => $milestone['mentor_feedback_query'],
                    'output' => $this->outputForDay($day, (string) $milestone['technology']),
                ];
            })
            ->all();

        return [
            'title' => 'Laravel content-backed practice rotation',
            'schedule' => $schedule,
            'meta' => [
                'filters' => $path['meta']['filters'] + ['days' => $days],
                'day_count' => count($schedule),
                'milestone_count' => $path['meta']['milestone_count'],
            ],
            'progress_payload' => $this->progressPayload->fromRows(
                $schedule,
                fn (array $item): string => sprintf('Day %d: %s', $item['day'], $item['output'])
            ),
        ];
    }

    /**
     * Return the focus for one rotation day.
     */
    private function focusForDay(int $day): string
    {
        return match (($day - 1) % 5) {
            0 => 'Build capstone task',
            1 => 'Run checkpoint exam',
            2 => 'Answer mentor feedback',
            3 => 'Package portfolio evidence',
            default => 'Retrospective and next improvement',
        };
    }

    /**
     * Return the required output for one rotation day.
     */
    private function outputForDay(int $day, string $technology): string
    {
        if ($technology === 'llm-foundations') {
            return match (($day - 1) % 5) {
                0 => 'AI type comparison table with output contracts',
                1 => 'Checkpoint evidence for metrics and failure modes',
                2 => 'Mentor feedback action item for predictive and generative evidence',
                3 => 'Portfolio entry updated with AI comparison proof',
                default => 'One AI evaluation improvement selected',
            };
        }

        if ($technology === 'javascript-closures') {
            return match (($day - 1) % 5) {
                0 => 'Closure lexical-scope trace with createCounter() output',
                1 => 'Checkpoint evidence for captured bindings and stale closures',
                2 => 'Mentor feedback action item for closure interview traps',
                3 => 'Portfolio entry updated with closure proof',
                default => 'One closure explanation improvement selected',
            };
        }

        return match (($day - 1) % 5) {
            0 => 'Completed workspace with focused test',
            1 => 'Checkpoint pass criteria evidence',
            2 => 'Mentor feedback action item completed',
            3 => 'Portfolio entry updated',
            default => 'One next improvement selected',
        };
    }
}
