<?php

declare(strict_types=1);

namespace App\Services\Practice;

final class PracticeMentorFeedbackLabService
{
    /**
     * Build mentor-style feedback from technology capstones.
     */
    public function __construct(
        private readonly PracticeCapstoneLabService $capstones,
        private readonly PracticeProgressPayloadService $progressPayload,
    ) {}

    /**
     * Create feedback prompts for a technology-level capstone.
     *
     * @param  array{technology?: string|null, family?: string|null, language?: string|null, search?: string|null, limit?: int|string|null}  $filters
     * @return array<string, mixed>
     */
    public function build(array $filters = []): array
    {
        $capstone = $this->capstones->build($filters);

        $feedbackItems = collect($capstone['tasks'])
            ->map(fn (array $task): array => [
                'position' => $task['position'],
                'record_id' => $task['record_id'],
                'question' => $task['question'],
                'source_path' => $task['source']['path'],
                'mentor_comment' => sprintf('Explain how your implementation answers `%s` and where that behavior is protected by a test.', $task['question']),
                'risk' => $this->riskFor($capstone['technology']),
                'action_item' => sprintf('Open the workspace for task %d, tighten one assertion, then update portfolio evidence.', $task['position']),
                'workspace_query' => $task['workspace_query'],
            ])
            ->values()
            ->all();

        return [
            'title' => sprintf('Mentor Feedback Lab: %s', $capstone['technology']),
            'technology' => $capstone['technology'],
            'mission' => $capstone['mission'],
            'feedback_items' => $feedbackItems,
            'mentor_questions' => [
                'Which source record drove the most important implementation decision?',
                'Which Laravel layer would be easiest to test in isolation next?',
                'What evidence would convince another developer this behavior is done?',
                'Which task should become portfolio evidence and why?',
            ],
            'review_focus' => [
                'Traceability from source record to code behavior.',
                'Thin controller and explicit validation boundary.',
                'Focused tests that fail for meaningful regressions.',
                'Verification and portfolio evidence that match the work.',
            ],
            'meta' => $capstone['meta'],
            'progress_payload' => $this->progressPayload->fromLabels(
                collect($feedbackItems)
                    ->map(fn (array $item): string => sprintf('Address mentor feedback for task %d', $item['position']))
                    ->push('Answer mentor questions before the next capstone')
            ),
        ];
    }

    /**
     * Return the main risk for one technology.
     */
    private function riskFor(string $technology): string
    {
        return match ($technology) {
            'api-validation' => 'Validation, controller, and service responsibilities may blur if the slice grows too quickly.',
            'testing-quality' => 'Assertions may verify implementation details instead of behavior.',
            'docker-runtime' => 'Runtime checks may pass locally while missing container-specific assumptions.',
            default => 'The implementation may become generic unless it stays tied to the selected source record.',
        };
    }
}
