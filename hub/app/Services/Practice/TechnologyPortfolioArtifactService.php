<?php

declare(strict_types=1);

namespace App\Services\Practice;

use Illuminate\Support\Str;

final class TechnologyPortfolioArtifactService
{
    /**
     * Create portfolio-ready artifacts from technology commit plans.
     */
    public function __construct(
        private readonly TechnologyCommitPlanService $commitPlans,
        private readonly PracticeProgressPayloadService $progressPayload,
    ) {}

    /**
     * Build a portfolio entry for one inferred technology.
     *
     * @param  array{family?: string|null, language?: string|null, search?: string|null, limit?: int|string|null}  $filters
     * @return array<string, mixed>
     */
    public function build(string $technology, array $filters = []): array
    {
        $commitPlan = $this->commitPlans->build($technology, $filters);
        $sourceItems = $commitPlan['lab']['source_examples']['items'];

        return [
            'title' => sprintf('Technology Portfolio Artifact: %s', $technology),
            'technology' => $technology,
            'commit_plan' => $commitPlan,
            'portfolio' => [
                'headline' => sprintf('Implemented content-backed %s practice in Laravel', $technology),
                'summary' => $this->summaryFor($technology, $sourceItems),
                'source_coverage' => $this->sourceCoverage($sourceItems),
                'changed_files' => $commitPlan['changed_files'],
                'verification' => $commitPlan['verification'],
                'interview_talking_points' => $this->talkingPoints($technology, $sourceItems),
                'readme_template' => $this->readmeTemplate($technology, $commitPlan),
            ],
            'progress_payload' => $this->progressPayload->fromLabels([
                'Write portfolio headline',
                'List source records covered',
                'Attach changed files and verification commands',
                'Prepare interview talking points',
                'Save README-style artifact',
            ]),
        ];
    }

    /**
     * Summarize what the learner implemented from source records.
     *
     * @param  array<int, array<string, mixed>>  $sourceItems
     * @return array<int, string>
     */
    private function summaryFor(string $technology, array $sourceItems): array
    {
        return [
            sprintf('Built a Laravel practice slice focused on `%s`.', $technology),
            sprintf('Mapped %d JSON content records into code-first implementation tasks.', count($sourceItems)),
            'Kept source content read-only while implementing behavior in Laravel code.',
            'Connected snippets, workspaces, verification commands, and commit evidence into one learning artifact.',
        ];
    }

    /**
     * Build source coverage rows for the portfolio artifact.
     *
     * @param  array<int, array<string, mixed>>  $sourceItems
     * @return array<int, array{record_id: string, source_path: string, title: string, task: string}>
     */
    private function sourceCoverage(array $sourceItems): array
    {
        return collect($sourceItems)
            ->map(fn (array $item): array => [
                'record_id' => (string) $item['record_id'],
                'source_path' => (string) $item['source']['path'],
                'title' => (string) $item['content']['title'],
                'task' => (string) $item['task'],
            ])
            ->values()
            ->all();
    }

    /**
     * Build interview talking points from source-backed implementation work.
     *
     * @param  array<int, array<string, mixed>>  $sourceItems
     * @return array<int, string>
     */
    private function talkingPoints(string $technology, array $sourceItems): array
    {
        $sampleTitle = (string) ($sourceItems[0]['content']['title'] ?? 'the selected content record');

        return [
            sprintf('I can explain how `%s` appears in a real Laravel code path, not only as theory.', $technology),
            sprintf('I used `%s` as the source record and turned it into concrete files, tests, and verification.', $sampleTitle),
            'I separated route mapping, controller orchestration, service behavior, validation, and verification evidence.',
            'I can show the branch, changed files, commands, and review checklist that prove the work is traceable.',
        ];
    }

    /**
     * Create a reusable README-style portfolio template.
     */
    private function readmeTemplate(string $technology, array $commitPlan): string
    {
        $title = Str::headline($technology);
        $files = collect($commitPlan['changed_files'])
            ->map(fn (string $file): string => "- `{$file}`")
            ->implode("\n");
        $commands = collect($commitPlan['verification'])
            ->map(fn (string $command): string => "- `{$command}`")
            ->implode("\n");

        return <<<MARKDOWN
# {$title} Practice Artifact

## Goal
Implement a content-backed Laravel practice slice for `{$technology}`.

## Branch
`{$commitPlan['branch']}`

## Commit
`{$commitPlan['commit_message']}`

## Changed Files
{$files}

## Verification
{$commands}

## Evidence
- Source records were read from JSON content.
- Laravel code owns the implementation.
- Tests and route checks prove the behavior.
MARKDOWN;
    }
}
