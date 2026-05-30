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
                'prompt' => $this->promptFor($feedback['technology'], (string) $item['question']),
                'source_path' => $item['source_path'],
                'workspace_query' => $item['workspace_query'],
                'expected_evidence' => $this->expectedEvidenceFor($feedback['technology'], (string) $item['question']),
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
            'pass_criteria' => $this->passCriteriaFor($feedback['technology']),
            'meta' => $feedback['meta'],
            'progress_payload' => $this->progressPayload->fromLabels(
                collect($codingTasks)
                    ->map(fn (array $task): string => sprintf('Complete checkpoint coding task %d', $task['position']))
                    ->prepend('Answer warmup questions')
                    ->push('Complete oral review')
            ),
        ];
    }

    /**
     * Return the coding prompt for one checkpoint task.
     */
    private function promptFor(string $technology, string $question): string
    {
        if ($technology === 'javascript-closures') {
            return sprintf('Implement the JavaScript closure evidence needed to answer `%s`.', $question);
        }

        return sprintf('Implement the Laravel behavior needed to answer `%s`.', $question);
    }

    /**
     * Return expected evidence for one checkpoint task.
     *
     * @return array<int, string>
     */
    private function expectedEvidenceFor(string $technology, string $question): array
    {
        if ($technology === 'llm-foundations' && AiTypeComparisonTopicService::matchesText($question)) {
            return [
                'A comparison table separates Predictive AI and Generative AI output contracts.',
                'At least one predictive metric and one generative quality check are named.',
                'Failure-mode evidence covers both prediction risk and generation risk.',
            ];
        }

        if ($technology === 'javascript-closures') {
            return [
                'A lexical-scope trace names the outer scope, returned inner function, and captured binding.',
                'A createCounter() or private-state example proves the value survives across repeated calls.',
                'Interview-trap evidence covers var versus let and one stale closure in async code, timers, callbacks, or hooks.',
            ];
        }

        return [
            'Focused feature test passes.',
            'Route, validation, controller, and service responsibilities are clear.',
            'Portfolio or PR evidence references the source record.',
        ];
    }

    /**
     * Return exam pass criteria for one technology.
     *
     * @return array<int, string>
     */
    private function passCriteriaFor(string $technology): array
    {
        if ($technology === 'llm-foundations') {
            return [
                'All coding tasks include output-contract evidence.',
                'At least one task has portfolio-ready predictive and generative evaluation evidence.',
                'You can explain metric fit, groundedness checks, and failure modes without reading the page.',
                'You can name the source record that drove each AI type comparison.',
            ];
        }

        if ($technology === 'javascript-closures') {
            return [
                'All coding tasks include lexical-scope and captured-binding evidence.',
                'At least one task has portfolio-ready createCounter(), private-state, or callback closure evidence.',
                'You can explain var versus let and stale closures without reading the page.',
                'You can name the source record that drove each closure example.',
            ];
        }

        return [
            'All coding tasks have focused verification evidence.',
            'At least one task has portfolio-ready evidence.',
            'You can explain validation, controller, service, and test boundaries without reading the code line by line.',
            'You can name the source record that drove each task.',
        ];
    }
}
