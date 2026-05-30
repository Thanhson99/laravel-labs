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
                'mentor_comment' => $this->mentorCommentFor($capstone['technology'], (string) $task['question']),
                'risk' => $this->riskFor($capstone['technology']),
                'action_item' => $this->actionItemFor($capstone['technology'], (int) $task['position']),
                'workspace_query' => $task['workspace_query'],
            ])
            ->values()
            ->all();

        return [
            'title' => sprintf('Mentor Feedback Lab: %s', $capstone['technology']),
            'technology' => $capstone['technology'],
            'mission' => $capstone['mission'],
            'feedback_items' => $feedbackItems,
            'mentor_questions' => $this->mentorQuestionsFor($capstone['technology']),
            'review_focus' => $this->reviewFocusFor($capstone['technology']),
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
            'llm-foundations' => 'The explanation may collapse prediction and generation into one generic AI story without separate evidence.',
            'javascript-closures' => 'The explanation may stop at "inner function remembers variables" without tracing lexical scope, captured bindings, var versus let, or stale closures.',
            'testing-quality' => 'Assertions may verify implementation details instead of behavior.',
            'docker-runtime' => 'Runtime checks may pass locally while missing container-specific assumptions.',
            default => 'The implementation may become generic unless it stays tied to the selected source record.',
        };
    }

    /**
     * Return a task-level mentor comment.
     */
    private function mentorCommentFor(string $technology, string $question): string
    {
        if ($technology === 'llm-foundations' && AiTypeComparisonTopicService::matchesText($question)) {
            return sprintf('Explain how `%s` separates Predictive AI from Generative AI by output contract, input evidence, evaluation metric, and failure mode.', $question);
        }

        if ($technology === 'javascript-closures') {
            return sprintf('Explain how `%s` proves closure behavior through lexical scope, captured bindings, repeated calls, and one interview trap.', $question);
        }

        return sprintf('Explain how your implementation answers `%s` and where that behavior is protected by a test.', $question);
    }

    /**
     * Return a concrete action item for one task.
     */
    private function actionItemFor(string $technology, int $position): string
    {
        if ($technology === 'llm-foundations') {
            return sprintf('Open the workspace for task %d, add one predictive metric and one generative quality check, then update portfolio evidence.', $position);
        }

        if ($technology === 'javascript-closures') {
            return sprintf('Open the workspace for task %d, add one createCounter() trace and one stale-closure or var-versus-let note, then update portfolio evidence.', $position);
        }

        return sprintf('Open the workspace for task %d, tighten one assertion, then update portfolio evidence.', $position);
    }

    /**
     * Return mentor questions for one technology.
     *
     * @return array<int, string>
     */
    private function mentorQuestionsFor(string $technology): array
    {
        if ($technology === 'llm-foundations') {
            return [
                'Which source record most clearly separates prediction output from generation output?',
                'Which metric proves the Predictive AI side is useful?',
                'Which groundedness, safety, citation, test, or human-review evidence proves the Generative AI side is useful?',
                'Which failure mode should become portfolio evidence and why?',
            ];
        }

        if ($technology === 'javascript-closures') {
            return [
                'Which source record most clearly explains closure as lexical scope plus captured binding?',
                'Where does createCounter() keep state between calls, and which binding is captured?',
                'Which var versus let, stale closure, callback, debounce, throttle, or hook example proves practical use?',
                'Which closure explanation should become portfolio evidence and why?',
            ];
        }

        return [
            'Which source record drove the most important implementation decision?',
            'Which Laravel layer would be easiest to test in isolation next?',
            'What evidence would convince another developer this behavior is done?',
            'Which task should become portfolio evidence and why?',
        ];
    }

    /**
     * Return review focus areas for one technology.
     *
     * @return array<int, string>
     */
    private function reviewFocusFor(string $technology): array
    {
        if ($technology === 'llm-foundations') {
            return [
                'Traceability from source record to AI type classification.',
                'Output contracts are separated before model capability claims.',
                'Predictive metrics and generative quality checks are not reused interchangeably.',
                'Failure-mode evidence covers both drift or biased labels and hallucination or unsafe generation.',
            ];
        }

        if ($technology === 'javascript-closures') {
            return [
                'Traceability from source record to lexical-scope explanation.',
                'Captured bindings are named before practical use cases are discussed.',
                'createCounter(), private state, callbacks, debounce, throttle, or hook closures are backed by examples.',
                'Interview-trap evidence covers var versus let and stale closures.',
            ];
        }

        return [
            'Traceability from source record to code behavior.',
            'Thin controller and explicit validation boundary.',
            'Focused tests that fail for meaningful regressions.',
            'Verification and portfolio evidence that match the work.',
        ];
    }
}
