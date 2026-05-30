<?php

declare(strict_types=1);

namespace App\Services\Practice;

use Illuminate\Support\Str;

final class PracticePullRequestLabService
{
    /**
     * Build pull-request practice artifacts from remediation labs.
     */
    public function __construct(
        private readonly PracticeRemediationLabService $remediationLabs,
        private readonly PracticeProgressPayloadService $progressPayload,
    ) {}

    /**
     * Create branch, commit, PR, and verification artifacts for one content-backed task.
     *
     * @param  array{family?: string|null, language?: string|null, search?: string|null, technology?: string|null, source_key?: string|null, record_id?: string|null}  $filters
     * @return array<string, mixed>|null
     */
    public function build(array $filters): ?array
    {
        $remediation = $this->remediationLabs->build($filters);

        if ($remediation === null) {
            return null;
        }

        $slug = Str::of($remediation['record']['title'])
            ->replaceMatches('/^\d+\.\s*/', '')
            ->slug()
            ->limit(54, '')
            ->toString();

        $changedFiles = collect($remediation['tasks'])
            ->pluck('file')
            ->reject(fn (string $file): bool => str_contains($file, '.json') || $file === 'verification commands')
            ->unique()
            ->values()
            ->all();

        $verificationCommands = collect($remediation['tasks'])
            ->pluck('verification')
            ->unique()
            ->values()
            ->all();

        return [
            'title' => sprintf('PR Lab: %s', $remediation['record']['title']),
            'source' => $remediation['source'],
            'technology' => $remediation['technology'],
            'record' => $remediation['record'],
            'branch' => sprintf('practice/%s/%s', $remediation['technology'], $slug),
            'commit_message' => sprintf('practice: implement %s lab', $slug),
            'pull_request' => [
                'title' => sprintf('Practice %s: %s', $remediation['technology'], $remediation['record']['title']),
                'summary' => $this->summaryFor($remediation),
                'changed_files' => $changedFiles,
                'verification' => $verificationCommands,
                'review_checklist' => $this->reviewChecklistFor($remediation['technology'], $remediation['tasks']),
            ],
            'quality_gate_payload' => $remediation['quality_gate_payload'],
            'progress_payload' => $this->progressPayload->fromLabels($this->progressLabelsFor($remediation['technology'])),
        ];
    }

    /**
     * Return PR summary lines for one remediation payload.
     *
     * @param  array<string, mixed>  $remediation
     * @return array<int, string>
     */
    private function summaryFor(array $remediation): array
    {
        if ($remediation['technology'] === 'idor-access-control') {
            return [
                sprintf('Implements an IDOR object-authorization artifact for `%s`.', $remediation['record']['title']),
                sprintf('Keeps the IDOR review traceable to `%s`.', $remediation['source']['path']),
                'Includes route inventory, scoped lookup evidence, policy or Gate behavior, ID-swap denial tests, status-code rationale, and verification evidence.',
            ];
        }

        if ($remediation['technology'] === 'javascript-closures') {
            return [
                sprintf('Implements a JavaScript closure interview artifact for `%s`.', $remediation['record']['title']),
                sprintf('Keeps the closure explanation traceable to `%s`.', $remediation['source']['path']),
                'Includes route, closure evidence service behavior, tests, lexical-scope proof, and verification evidence.',
            ];
        }

        return [
            sprintf('Implements a Laravel practice slice for `%s`.', $remediation['record']['title']),
            sprintf('Keeps the work traceable to `%s`.', $remediation['source']['path']),
            'Includes route, validation, controller/service behavior, tests, and verification evidence.',
        ];
    }

    /**
     * Return PR review checklist lines.
     *
     * @param  array<int, array{label: string, file: string}>  $tasks
     * @return array<int, string>
     */
    private function reviewChecklistFor(string $technology, array $tasks): array
    {
        if ($technology === 'idor-access-control') {
            return collect($tasks)
                ->map(fn (array $task): string => sprintf('[ ] %s object-authorization evidence fixed in %s', $task['label'], $task['file']))
                ->all();
        }

        if ($technology === 'javascript-closures') {
            return collect($tasks)
                ->map(fn (array $task): string => sprintf('[ ] %s closure evidence fixed in %s', $task['label'], $task['file']))
                ->all();
        }

        return collect($tasks)
            ->map(fn (array $task): string => sprintf('[ ] %s fixed in %s', $task['label'], $task['file']))
            ->all();
    }

    /**
     * Return progress labels for one technology.
     *
     * @return array<int, string>
     */
    private function progressLabelsFor(string $technology): array
    {
        if ($technology === 'idor-access-control') {
            return [
                'Create IDOR practice branch',
                'Commit object-authorization evidence files',
                'Write PR summary from source record',
                'Paste scoped lookup, policy, and ID-swap denial verification evidence',
                'Complete IDOR review checklist',
            ];
        }

        if ($technology === 'javascript-closures') {
            return [
                'Create closure practice branch',
                'Commit closure evidence files',
                'Write PR summary from source record',
                'Paste lexical-scope and stale-closure verification evidence',
                'Complete closure review checklist',
            ];
        }

        return [
            'Create practice branch',
            'Commit implementation files',
            'Write PR summary from source record',
            'Paste verification evidence',
            'Complete review checklist',
        ];
    }
}
