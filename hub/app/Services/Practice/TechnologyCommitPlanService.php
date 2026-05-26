<?php

declare(strict_types=1);

namespace App\Services\Practice;

use Illuminate\Support\Str;

final class TechnologyCommitPlanService
{
    /**
     * Create commit-ready artifacts from a technology implementation lab.
     */
    public function __construct(
        private readonly TechnologyImplementationLabService $labs,
        private readonly PracticeProgressPayloadService $progressPayload,
    ) {}

    /**
     * Build a branch, commit, evidence, and review plan for one technology lab.
     *
     * @param  array{family?: string|null, language?: string|null, search?: string|null, limit?: int|string|null}  $filters
     * @return array<string, mixed>
     */
    public function build(string $technology, array $filters = []): array
    {
        $lab = $this->labs->build($technology, $filters);
        $slug = Str::of($technology)->slug()->toString();
        $changedFiles = $this->changedFiles($lab);

        return [
            'title' => sprintf('Technology Commit Plan: %s', $technology),
            'technology' => $technology,
            'branch' => sprintf('practice/%s/content-backed-implementation', $slug),
            'commit_message' => sprintf('practice: implement %s content-backed lab', $slug),
            'lab' => $lab,
            'changed_files' => $changedFiles,
            'verification' => $lab['commands'],
            'evidence_checklist' => $this->evidenceChecklist($lab, $changedFiles),
            'review_checklist' => $this->reviewChecklist($technology),
            'progress_payload' => $this->progressPayload->fromLabels([
                'Create branch',
                'Implement files from source examples',
                'Run verification commands',
                'Capture evidence checklist',
                'Write commit message',
                'Review changed files',
            ]),
        ];
    }

    /**
     * Extract unique changed files from all source-example snippets.
     *
     * @return array<int, string>
     */
    private function changedFiles(array $lab): array
    {
        return collect($lab['source_examples']['items'])
            ->flatMap(fn (array $item): array => collect($item['snippets'])
                ->pluck('file')
                ->all())
            ->unique()
            ->values()
            ->all();
    }

    /**
     * Build evidence items that prove the technology implementation is complete.
     *
     * @param  array<int, string>  $changedFiles
     * @return array<int, string>
     */
    private function evidenceChecklist(array $lab, array $changedFiles): array
    {
        return [
            sprintf('Source records covered: %d.', $lab['meta']['task_count']),
            sprintf('Changed files listed: %s.', implode(', ', $changedFiles)),
            sprintf('Verification commands listed: %s.', implode(' | ', $lab['commands'])),
            'Workspace links were opened for each selected source record.',
            'No JSON content file was changed to fake implementation progress.',
        ];
    }

    /**
     * Build review checks specific to content-backed technology work.
     *
     * @return array<int, string>
     */
    private function reviewChecklist(string $technology): array
    {
        return [
            sprintf('The implementation matches the inferred `%s` technology.', $technology),
            'Routes only map URLs and stay commented for learners.',
            'Controllers stay thin and delegate behavior to services or requests.',
            'Validation, authorization, storage, queue, or cache responsibilities live in the correct Laravel layer.',
            'Tests prove behavior from the selected JSON records instead of only checking implementation details.',
        ];
    }
}
