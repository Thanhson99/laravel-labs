<?php

declare(strict_types=1);

namespace App\Services\Practice;

final class PracticeAssessmentLabService
{
    /**
     * Build self-assessment rubrics from pull-request practice artifacts.
     */
    public function __construct(
        private readonly PracticePullRequestLabService $pullRequests,
        private readonly PracticeProgressPayloadService $progressPayload,
    ) {}

    /**
     * Create a scoring rubric for one content-backed practice implementation.
     *
     * @param  array{family?: string|null, language?: string|null, search?: string|null, technology?: string|null, source_key?: string|null, record_id?: string|null}  $filters
     * @return array<string, mixed>|null
     */
    public function build(array $filters): ?array
    {
        $pullRequest = $this->pullRequests->build($filters);

        if ($pullRequest === null) {
            return null;
        }

        $rubric = $this->rubricFor($pullRequest);

        return [
            'title' => sprintf('Assessment Lab: %s', $pullRequest['record']['title']),
            'source' => $pullRequest['source'],
            'technology' => $pullRequest['technology'],
            'record' => $pullRequest['record'],
            'branch' => $pullRequest['branch'],
            'commit_message' => $pullRequest['commit_message'],
            'rubric' => $rubric,
            'score_total' => collect($rubric)->sum('points'),
            'evidence' => [
                'changed_files' => $pullRequest['pull_request']['changed_files'],
                'verification' => $pullRequest['pull_request']['verification'],
                'review_checklist' => $pullRequest['pull_request']['review_checklist'],
                'quality_gate_payload' => $pullRequest['quality_gate_payload'],
            ],
            'progress_payload' => $this->progressPayload->fromRows(
                $rubric,
                fn (array $item): string => sprintf('Assess %s', $item['label'])
            ),
        ];
    }

    /**
     * Create one rubric criterion.
     *
     * @return array{label: string, points: int, evidence: string, self_check: string}
     */
    private function criterion(string $label, int $points, string $evidence): array
    {
        return [
            'label' => $label,
            'points' => $points,
            'evidence' => $evidence,
            'self_check' => sprintf('Give full points only when `%s` is visible in code, tests, or PR evidence.', $label),
        ];
    }

    /**
     * Build a scoring rubric for one pull request artifact.
     *
     * @param  array<string, mixed>  $pullRequest
     * @return array<int, array{label: string, points: int, evidence: string, self_check: string}>
     */
    private function rubricFor(array $pullRequest): array
    {
        if ($pullRequest['technology'] === 'javascript-closures') {
            return [
                $this->criterion('Content traceability', 20, sprintf('Implementation and PR summary reference `%s` and answer `%s`.', $pullRequest['source']['path'], $pullRequest['record']['title'])),
                $this->criterion('Lexical scope model', 20, 'The explanation names the outer scope, returned inner function, and captured binding.'),
                $this->criterion('Closure behavior tests', 20, 'Feature or focused tests protect repeated calls, private state, var-versus-let behavior, or stale-closure output.'),
                $this->criterion('Verification evidence', 20, 'Focused test, route check, full suite, and style check are listed and ready to run.'),
                $this->criterion('Interview readiness', 20, 'Changed files, checklist, branch, commit, and PR summary make the closure answer defensible in an interview.'),
            ];
        }

        return [
            $this->criterion('Content traceability', 20, sprintf('Implementation and PR summary reference `%s` and answer `%s`.', $pullRequest['source']['path'], $pullRequest['record']['title'])),
            $this->criterion('Laravel layer boundaries', 20, 'Route, Form Request, controller, service, and test responsibilities stay separated.'),
            $this->criterion('Behavior-first tests', 20, 'Feature test protects status code and JSON shape for the generated route.'),
            $this->criterion('Verification evidence', 20, 'Focused test, route check, full suite, and style check are listed and ready to run.'),
            $this->criterion('Review readiness', 20, 'Changed files, checklist, branch, commit, and PR summary are coherent and scoped.'),
        ];
    }
}
