<?php

declare(strict_types=1);

namespace App\Services\Practice;

final class ConfigurationEvidenceReusePlanService
{
    /**
     * Build reusable outputs from configuration archive retrieval cases.
     */
    public function __construct(
        private readonly ConfigurationArchiveRetrievalService $retrieval,
    ) {}

    /**
     * Build a plan for reusing retrieved configuration evidence.
     *
     * @return array<string, mixed>
     */
    public function build(): array
    {
        $retrieval = $this->retrieval->build();

        return [
            'title' => 'Configuration Evidence Reuse Plan',
            'summary' => 'Turn retrieved configuration evidence into portfolio notes, interview answers, review comments, and incident recovery notes.',
            'archive_id' => $retrieval['archive_id'],
            'reuse_tasks' => $this->reuseTasks($retrieval),
            'quality_checks' => [
                ...$retrieval['quality_checks'],
                'Final reuse output names the audience and the proof source.',
                'Final reuse output keeps app/auth configuration language precise.',
                'Security reuse output names the unsafe production default, release blocker, owner, and smoke evidence.',
            ],
            'handoff_notes' => [
                'Portfolio reuse should link the readiness route, quality gate, and archive id.',
                'Security review reuse should link release blockers to debug, secrets, CORS, headers, cookies, proxies, and storage exposure.',
                'Interview reuse should answer with one config key, one route, and one command.',
                'Incident reuse should cite the postmortem id and smallest recovery action.',
            ],
            'commands' => [
                ...$retrieval['commands'],
                'php artisan test --filter ConfigurationEvidenceReusePlanTest',
            ],
            'quality_gate' => $retrieval['quality_gate'],
        ];
    }

    /**
     * Convert retrieval cases into concrete reuse tasks.
     *
     * @param  array<string, mixed>  $retrieval
     * @return array<int, array<string, string>>
     */
    private function reuseTasks(array $retrieval): array
    {
        return collect($retrieval['retrieval_cases'])
            ->map(fn (array $case): array => [
                'audience' => $case['use_case'],
                'source_key' => $case['key'],
                'proof' => $case['proof'],
                'output' => $this->outputFor($case['use_case']),
            ])
            ->values()
            ->all();
    }

    /**
     * Return the target output for a reuse audience.
     */
    private function outputFor(string $useCase): string
    {
        return match ($useCase) {
            'Portfolio note' => 'Write a concise portfolio paragraph about treating Laravel configuration as a tested runtime contract.',
            'Security review' => 'Write a release-review note that explains which Security Misconfiguration signal blocks deployment and which smoke check proves it.',
            'Interview answer' => 'Prepare a two-minute answer that explains config(), env(), auth defaults, and quality-gate status.',
            'Incident recovery' => 'Write a recovery note that cites the postmortem, root cause, route, and smallest rollback path.',
            default => 'Turn the retrieved proof into a focused learning artifact.',
        };
    }
}
