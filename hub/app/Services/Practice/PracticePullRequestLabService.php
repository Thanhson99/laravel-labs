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
                'summary' => [
                    sprintf('Implements a Laravel practice slice for `%s`.', $remediation['record']['title']),
                    sprintf('Keeps the work traceable to `%s`.', $remediation['source']['path']),
                    'Includes route, validation, controller/service behavior, tests, and verification evidence.',
                ],
                'changed_files' => $changedFiles,
                'verification' => $verificationCommands,
                'review_checklist' => collect($remediation['tasks'])
                    ->map(fn (array $task): string => sprintf('[ ] %s fixed in %s', $task['label'], $task['file']))
                    ->all(),
            ],
            'quality_gate_payload' => $remediation['quality_gate_payload'],
            'progress_payload' => $this->progressPayload->fromLabels([
                'Create practice branch',
                'Commit implementation files',
                'Write PR summary from source record',
                'Paste verification evidence',
                'Complete review checklist',
            ]),
        ];
    }
}
