<?php

declare(strict_types=1);

namespace App\Services\Practice;

final class PracticeRetrospectiveLabService
{
    /**
     * Build learning retrospectives from assessment labs.
     */
    public function __construct(
        private readonly PracticeAssessmentLabService $assessments,
        private readonly PracticeProgressPayloadService $progressPayload,
    ) {}

    /**
     * Create retrospective prompts and next actions for one assessed implementation.
     *
     * @param  array{family?: string|null, language?: string|null, search?: string|null, technology?: string|null, source_key?: string|null, record_id?: string|null}  $filters
     * @return array<string, mixed>|null
     */
    public function build(array $filters): ?array
    {
        $assessment = $this->assessments->build($filters);

        if ($assessment === null) {
            return null;
        }

        $weakSpots = collect($assessment['rubric'])
            ->map(fn (array $criterion): array => [
                'label' => $criterion['label'],
                'prompt' => sprintf('Where could `%s` be made more obvious in the code, tests, or PR evidence?', $criterion['label']),
                'next_action' => $this->nextAction($criterion['label']),
            ])
            ->values()
            ->all();

        return [
            'title' => sprintf('Retrospective Lab: %s', $assessment['record']['title']),
            'source' => $assessment['source'],
            'technology' => $assessment['technology'],
            'record' => $assessment['record'],
            'score_total' => $assessment['score_total'],
            'wins' => [
                sprintf('The implementation is traceable to `%s`.', $assessment['source']['path']),
                sprintf('The practice branch `%s` scopes the work by technology and record.', $assessment['branch']),
                'The evidence includes changed files, verification commands, and review checklist items.',
            ],
            'weak_spots' => $weakSpots,
            'next_labs' => [
                [
                    'label' => 'Re-open remediation lab',
                    'route' => 'practice.remediation-lab',
                    'query' => $filters,
                ],
                [
                    'label' => 'Re-run assessment lab',
                    'route' => 'practice.assessment-lab',
                    'query' => $filters,
                ],
                [
                    'label' => 'Package PR evidence again',
                    'route' => 'practice.pull-request-lab',
                    'query' => $filters,
                ],
            ],
            'progress_payload' => $this->progressPayload->fromLabels([
                'Write one win from this implementation',
                'Pick one weak spot from the rubric',
                'Complete one next action',
                'Update PR evidence after the fix',
                'Re-run assessment lab',
            ]),
        ];
    }

    /**
     * Return a concrete next action for one rubric label.
     */
    private function nextAction(string $label): string
    {
        return match ($label) {
            'Content traceability' => 'Add one assertion or PR note that names the source record behavior explicitly.',
            'Laravel layer boundaries' => 'Move one misplaced responsibility into the correct route, request, controller, service, or test layer.',
            'Behavior-first tests' => 'Add one failing-first assertion for status, validation, or JSON shape.',
            'Verification evidence' => 'Paste the focused test, full suite, Pint, and route check results into the PR draft.',
            'Review readiness' => 'Tighten the PR summary so changed files, verification, and checklist tell one coherent story.',
            default => 'Choose the smallest improvement and verify it with a focused test.',
        };
    }
}
