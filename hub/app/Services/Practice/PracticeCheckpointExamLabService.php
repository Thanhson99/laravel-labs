<?php

declare(strict_types=1);

namespace App\Services\Practice;

final class PracticeCheckpointExamLabService
{
    /**
     * Build timed checkpoint exams from mentor feedback labs.
     */
    public function __construct(
        private readonly PracticeMentorFeedbackLabService $feedbackLabs,
        private readonly PracticeProgressPayloadService $progressPayload,
    ) {}

    /**
     * Create a checkpoint exam for one technology and content filter.
     *
     * @param  array{technology?: string|null, family?: string|null, language?: string|null, search?: string|null, limit?: int|string|null}  $filters
     * @return array<string, mixed>
     */
    public function build(array $filters = []): array
    {
        $feedback = $this->feedbackLabs->build($filters);
        $duration = max(45, (int) $feedback['meta']['estimated_minutes']);

        $codingTasks = collect($feedback['feedback_items'])
            ->map(fn (array $item): array => [
                'position' => $item['position'],
                'record_id' => $item['record_id'],
                'prompt' => sprintf('Implement the Laravel behavior needed to answer `%s`.', $item['question']),
                'source_path' => $item['source_path'],
                'workspace_query' => $item['workspace_query'],
                'expected_evidence' => [
                    'Focused feature test passes.',
                    'Route, validation, controller, and service responsibilities are clear.',
                    'Portfolio or PR evidence references the source record.',
                ],
            ])
            ->values()
            ->all();

        return [
            'title' => sprintf('Checkpoint Exam Lab: %s', $feedback['technology']),
            'technology' => $feedback['technology'],
            'duration_minutes' => $duration,
            'instructions' => [
                'Work from the source records, not from memory alone.',
                'Write or tighten one behavior-first test before changing implementation code.',
                'Use the generated workspace and TDD lab links for each task.',
                'Finish by answering the oral review questions.',
            ],
            'warmup_questions' => $feedback['mentor_questions'],
            'coding_tasks' => $codingTasks,
            'oral_review' => collect($feedback['review_focus'])
                ->map(fn (string $focus): string => sprintf('Explain your evidence for: %s', $focus))
                ->all(),
            'pass_criteria' => [
                'All coding tasks have focused verification evidence.',
                'At least one task has portfolio-ready evidence.',
                'You can explain validation, controller, service, and test boundaries without reading the code line by line.',
                'You can name the source record that drove each task.',
            ],
            'meta' => $feedback['meta'],
            'progress_payload' => $this->progressPayload->fromLabels(
                collect($codingTasks)
                    ->map(fn (array $task): string => sprintf('Complete checkpoint coding task %d', $task['position']))
                    ->prepend('Answer warmup questions')
                    ->push('Complete oral review')
            ),
        ];
    }
}
