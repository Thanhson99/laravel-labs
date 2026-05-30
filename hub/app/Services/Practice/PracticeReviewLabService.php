<?php

declare(strict_types=1);

namespace App\Services\Practice;

final class PracticeReviewLabService
{
    /**
     * Build code-review exercises from content-backed TDD labs.
     */
    public function __construct(
        private readonly PracticeTddLabService $tddLabs,
        private readonly PracticeProgressPayloadService $progressPayload,
    ) {}

    /**
     * Create a review checklist for one content-backed implementation.
     *
     * @param  array{family?: string|null, language?: string|null, search?: string|null, technology?: string|null, source_key?: string|null, record_id?: string|null}  $filters
     * @return array<string, mixed>|null
     */
    public function build(array $filters): ?array
    {
        $lab = $this->tddLabs->build($filters);

        if ($lab === null) {
            return null;
        }

        $items = $this->itemsFor($lab);

        return [
            'title' => sprintf('Review Lab: %s', $lab['record']['title']),
            'source' => $lab['source'],
            'technology' => $lab['technology'],
            'record' => $lab['record'],
            'route' => $lab['route'],
            'files' => $lab['files'],
            'review_items' => $items,
            'commands' => $lab['cycle'][2]['commands'],
            'quality_gate_payload' => $lab['cycle'][2]['quality_gate_payload'],
            'progress_payload' => $this->progressPayload->fromRows(
                $items,
                fn (array $item): string => $item['label']
            ),
        ];
    }

    /**
     * Create one review item.
     *
     * @return array{label: string, question: string, file: string}
     */
    private function item(string $label, string $question, string $file): array
    {
        return [
            'label' => $label,
            'question' => $question,
            'file' => $file,
        ];
    }

    /**
     * Find a generated file by path fragment.
     *
     * @param  array<int, string>  $files
     */
    private function fileContaining(array $files, string $fragment): string
    {
        return collect($files)
            ->first(fn (string $file): bool => str_contains($file, $fragment), 'generated file');
    }

    /**
     * Build review items for one TDD lab.
     *
     * @param  array<string, mixed>  $lab
     * @return array<int, array{label: string, question: string, file: string}>
     */
    private function itemsFor(array $lab): array
    {
        if ($lab['technology'] === 'javascript-closures') {
            return [
                $this->item('Content traceability', 'Confirm the closure explanation answers the selected source record, not a generic simplified definition.', $lab['source']['path']),
                $this->item('Route contract', sprintf('Confirm %s %s is registered and returns the closure evidence shape expected by the feature test.', $lab['route']['method'], $lab['route']['path']), 'routes/api.php'),
                $this->item('Evidence boundary', 'Confirm request handling stays separate from closure evidence construction.', $this->fileContaining($lab['files'], 'Requests')),
                $this->item('Thin controller', 'Confirm the controller delegates closure evidence construction to the service and only shapes the response.', $this->fileContaining($lab['files'], 'Controllers')),
                $this->item('Closure behavior', 'Confirm the service names lexical scope, captured binding, createCounter(), var-versus-let, and stale-closure evidence where relevant.', $this->fileContaining($lab['files'], 'Services')),
                $this->item('Behavior test', 'Confirm the feature test fails when closure output, captured-binding text, or stale-closure evidence drifts.', $this->fileContaining($lab['files'], 'tests/Feature')),
                $this->item('Verification evidence', 'Confirm focused closure test, full suite, Pint, and smoke request were run.', 'verification commands'),
            ];
        }

        return [
            $this->item('Content traceability', 'Confirm the implementation behavior answers the selected source record, not a generic simplified response.', $lab['source']['path']),
            $this->item('Route contract', sprintf('Confirm %s %s is registered and returns the response shape expected by the feature test.', $lab['route']['method'], $lab['route']['path']), 'routes/api.php'),
            $this->item('Validation boundary', 'Confirm invalid input is rejected before the service runs.', $this->fileContaining($lab['files'], 'Requests')),
            $this->item('Thin controller', 'Confirm the controller delegates business logic to the service and only shapes the response.', $this->fileContaining($lab['files'], 'Controllers')),
            $this->item('Service behavior', 'Confirm the service contains the smallest useful behavior required by the source record.', $this->fileContaining($lab['files'], 'Services')),
            $this->item('Behavior test', 'Confirm the feature test fails for a broken response and passes for the implemented behavior.', $this->fileContaining($lab['files'], 'tests/Feature')),
            $this->item('Verification evidence', 'Confirm focused test, full suite, Pint, and smoke request were run.', 'verification commands'),
        ];
    }
}
