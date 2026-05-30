<?php

declare(strict_types=1);

namespace App\Services\Practice;

final class PracticeRemediationLabService
{
    /**
     * Build fix-it exercises from review labs.
     */
    public function __construct(
        private readonly PracticeReviewLabService $reviews,
        private readonly PracticeProgressPayloadService $progressPayload,
    ) {}

    /**
     * Create remediation tasks for one reviewed implementation.
     *
     * @param  array{family?: string|null, language?: string|null, search?: string|null, technology?: string|null, source_key?: string|null, record_id?: string|null}  $filters
     * @return array<string, mixed>|null
     */
    public function build(array $filters): ?array
    {
        $review = $this->reviews->build($filters);

        if ($review === null) {
            return null;
        }

        $tasks = collect($review['review_items'])
            ->map(fn (array $item, int $index): array => [
                'position' => $index + 1,
                'label' => $item['label'],
                'file' => $item['file'],
                'problem_to_check' => $item['question'],
                'fix_action' => $this->fixAction($item['label'], $review['route']['path'], $review['technology']),
                'verification' => $this->verificationFor($item['label'], $review['commands']),
            ])
            ->values()
            ->all();

        return [
            'title' => sprintf('Remediation Lab: %s', $review['record']['title']),
            'source' => $review['source'],
            'technology' => $review['technology'],
            'record' => $review['record'],
            'route' => $review['route'],
            'tasks' => $tasks,
            'quality_gate_payload' => $review['quality_gate_payload'],
            'progress_payload' => $this->progressPayload->fromRows(
                $tasks,
                fn (array $task): string => sprintf('Fix %s', $task['label'])
            ),
        ];
    }

    /**
     * Explain the concrete fix action for a review label.
     */
    private function fixAction(string $label, string $routePath, string $technology): string
    {
        if ($technology === 'javascript-closures') {
            return match ($label) {
                'Content traceability' => 'Replace simplified behavior with closure evidence that directly matches the selected source record.',
                'Route contract' => sprintf('Register or correct the route so %s resolves to the generated closure evidence controller.', $routePath),
                'Validation boundary' => 'Keep input assumptions explicit and add an invalid or missing evidence assertion if the payload accepts filters.',
                'Thin controller' => 'Keep the controller limited to request handling, service call, and response shape for closure evidence.',
                'Service behavior',
                'Closure behavior' => 'Move lexical-scope, createCounter(), var-versus-let, or stale-closure transformation into the service and cover it through the feature test.',
                'Behavior test' => 'Tighten assertions so the test fails when closure output, captured-binding text, or stale-closure evidence drifts.',
                'Verification evidence' => 'Run the focused closure test, route check, full suite, Pint, and smoke request after each fix.',
                default => 'Make the smallest closure-evidence change that satisfies the review question.',
            };
        }

        return match ($label) {
            'Content traceability' => 'Replace simplified behavior with logic that directly matches the selected source record.',
            'Route contract' => sprintf('Register or correct the route so %s resolves to the generated controller.', $routePath),
            'Validation boundary' => 'Move input rules into the Form Request and add an invalid payload assertion.',
            'Thin controller' => 'Keep the controller limited to request validation, service call, and response shape.',
            'Service behavior' => 'Move transformation or decision logic into the service and cover it through the feature test.',
            'Behavior test' => 'Tighten assertions so the test fails when the response body or status drifts.',
            'Verification evidence' => 'Run the focused test, route check, full suite, Pint, and smoke request after each fix.',
            default => 'Make the smallest code change that satisfies the review question.',
        };
    }

    /**
     * Choose the verification command for a remediation item.
     *
     * @param  array<int, array{label: string, command: string}>  $commands
     */
    private function verificationFor(string $label, array $commands): string
    {
        if ($label === 'Route contract') {
            return $this->commandByLabel($commands, 'Route check');
        }

        if ($label === 'Verification evidence') {
            return $this->commandByLabel($commands, 'Full suite');
        }

        return $this->commandByLabel($commands, 'Focused test');
    }

    /**
     * Read a command by label from the generated verification plan.
     *
     * @param  array<int, array{label: string, command: string}>  $commands
     */
    private function commandByLabel(array $commands, string $label): string
    {
        return collect($commands)
            ->first(fn (array $command): bool => $command['label'] === $label, ['command' => 'php artisan test'])['command'];
    }
}
