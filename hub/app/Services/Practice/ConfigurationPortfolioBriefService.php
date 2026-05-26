<?php

declare(strict_types=1);

namespace App\Services\Practice;

final class ConfigurationPortfolioBriefService
{
    /**
     * Build a portfolio-ready artifact from the evidence reuse plan.
     */
    public function __construct(
        private readonly ConfigurationEvidenceReusePlanService $reusePlan,
    ) {}

    /**
     * Build a concise portfolio and interview brief for configuration practice.
     *
     * @return array<string, mixed>
     */
    public function build(): array
    {
        $reusePlan = $this->reusePlan->build();

        return [
            'title' => 'Configuration Portfolio Brief',
            'summary' => 'Package configuration practice evidence into a concise artifact for portfolio review, interview defense, and future code reviews.',
            'archive_id' => $reusePlan['archive_id'],
            'headline' => 'I treated Laravel app/auth configuration as a tested runtime contract with readiness checks, recovery drills, and reusable evidence.',
            'portfolio_paragraph' => $this->portfolioParagraph($reusePlan),
            'talking_points' => [
                'Configuration changes were routed through readiness, risk review, remediation, PR assessment, and deployment evidence.',
                'Quality-gate status stayed consistent across pages and APIs instead of becoming a one-off UI label.',
                'Incident recovery practice captured root cause, rollback thinking, postmortem evidence, and spaced-review prompts.',
            ],
            'proof_table' => $this->proofTable($reusePlan),
            'review_checklist' => [
                'The brief names the archive id and quality status.',
                'Each proof item maps to a source key or recovered incident route.',
                'The portfolio paragraph avoids vague claims and names concrete Laravel configuration behavior.',
                'Interview talking points include config(), env(), auth defaults, and quality-gate status.',
            ],
            'commands' => [
                ...$reusePlan['commands'],
                'php artisan test --filter ConfigurationPortfolioBriefTest',
            ],
            'quality_gate' => $reusePlan['quality_gate'],
        ];
    }

    /**
     * Build the portfolio paragraph from reuse evidence.
     *
     * @param  array<string, mixed>  $reusePlan
     */
    private function portfolioParagraph(array $reusePlan): string
    {
        return 'Built a configuration practice workflow around '.$reusePlan['archive_id'].' that verifies app/auth readiness, keeps quality-gate status consistent, and preserves incident recovery evidence for review and interviews.';
    }

    /**
     * Convert reuse tasks into a proof table.
     *
     * @param  array<string, mixed>  $reusePlan
     * @return array<int, array<string, string>>
     */
    private function proofTable(array $reusePlan): array
    {
        return collect($reusePlan['reuse_tasks'])
            ->map(fn (array $task): array => [
                'audience' => $task['audience'],
                'source_key' => $task['source_key'],
                'proof' => $task['proof'],
                'use' => $task['output'],
            ])
            ->values()
            ->all();
    }
}
