<?php

declare(strict_types=1);

namespace App\Services\Practice;

final class ConfigurationReleaseEvidenceService
{
    /**
     * Create release evidence artifacts for configuration deployment work.
     */
    public function __construct(
        private readonly ConfigurationDeploymentPlanService $deploymentPlan,
    ) {}

    /**
     * Build a reusable evidence artifact from the current deployment plan.
     *
     * @return array<string, mixed>
     */
    public function build(): array
    {
        $plan = $this->deploymentPlan->build();

        return [
            'title' => 'Configuration Release Evidence',
            'summary' => 'Package configuration deployment proof into a PR-ready artifact with verification, smoke checks, rollback, and reuse notes.',
            'release_summary' => $this->releaseSummary($plan),
            'proof_points' => $this->proofPoints($plan),
            'api_evidence' => $this->apiEvidence($plan),
            'rollback_summary' => $this->rollbackSummary($plan),
            'portfolio_notes' => [
                'Explain how app and auth config were treated as a runtime contract.',
                'Explain how Security Misconfiguration controls block unsafe production defaults before deploy.',
                'Mention the quality gate as the shared readiness signal across practice pages.',
                'Cite the deployment plan, smoke checks, and rollback steps as release evidence.',
            ],
            'commands' => [
                ...$plan['commands'],
                'php artisan test --filter ConfigurationReleaseEvidenceTest',
            ],
            'quality_gate' => $plan['quality_gate'],
        ];
    }

    /**
     * Build a PR-ready release summary.
     *
     * @param  array<string, mixed>  $plan
     * @return array<string, mixed>
     */
    private function releaseSummary(array $plan): array
    {
        return [
            'headline' => 'Configuration runtime contract is ready for release.',
            'changed_areas' => collect($plan['source_files'])
                ->map(fn (string $file): string => $file)
                ->values()
                ->all(),
            'quality_status' => $plan['quality_gate']['status'],
            'risk_level' => $plan['quality_gate']['passed'] ? 'low' : 'medium',
            'review_focus' => [
                'Confirm config values are read through config().',
                'Confirm public practice routes still render without auth.',
                'Confirm rollback values are clear before deployment.',
            ],
            'security_review_focus' => [
                'Confirm debug mode, exposed secrets, broad CORS, missing headers, weak cookies, public storage, and proxy drift are release blockers.',
                'Confirm every Security Misconfiguration control has an owner and captured smoke-check evidence.',
            ],
        ];
    }

    /**
     * Convert deployment evidence into proof cards.
     *
     * @param  array<string, mixed>  $plan
     * @return array<int, array{label: string, proof: string}>
     */
    private function proofPoints(array $plan): array
    {
        return collect($plan['evidence'])
            ->map(fn (string $evidence): array => [
                'label' => $evidence,
                'proof' => 'Attach command output or API response screenshot to the release notes.',
            ])
            ->values()
            ->all();
    }

    /**
     * Build API evidence from smoke-check endpoints.
     *
     * @param  array<string, mixed>  $plan
     * @return array<int, array{endpoint: string, expected: string, capture: string}>
     */
    private function apiEvidence(array $plan): array
    {
        return collect($plan['smoke_checks'])
            ->map(fn (array $check): array => [
                'endpoint' => $check['endpoint'],
                'expected' => $check['expected'],
                'capture' => 'Record HTTP 200 and the expected payload or page text.',
            ])
            ->values()
            ->all();
    }

    /**
     * Build rollback evidence from deployment rollback steps.
     *
     * @param  array<string, mixed>  $plan
     * @return array<int, array{area: string, proof: string}>
     */
    private function rollbackSummary(array $plan): array
    {
        return collect($plan['rollback_steps'])
            ->map(fn (array $step): array => [
                'area' => $step['area'],
                'proof' => $step['action'],
            ])
            ->values()
            ->all();
    }
}
