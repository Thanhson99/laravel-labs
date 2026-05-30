<?php

declare(strict_types=1);

namespace App\Services\Practice;

final class ConfigurationDeploymentPlanService
{
    /**
     * Create deployment plans for app, auth, and quality-gate configuration changes.
     */
    public function __construct(
        private readonly ConfigurationChangeChecklistService $checklist,
        private readonly ConfigurationReadinessService $readiness,
    ) {}

    /**
     * Build a deployment plan from the current configuration checklist.
     *
     * @return array<string, mixed>
     */
    public function build(): array
    {
        $checklist = $this->checklist->build();
        $readiness = $this->readiness->build();

        return [
            'title' => 'Configuration Deployment Plan',
            'summary' => 'Move configuration changes from local checks to runtime verification with preflight, deploy, smoke, and rollback steps.',
            'source_files' => $checklist['source_files'],
            'preflight' => $this->preflight($checklist),
            'deploy_steps' => $this->deploySteps(),
            'smoke_checks' => $this->smokeChecks($readiness),
            'security_misconfiguration_controls' => $readiness['misconfiguration_controls'],
            'release_blockers' => $readiness['release_blockers'],
            'rollback_steps' => $this->rollbackSteps($checklist),
            'evidence' => [
                'Configuration readiness API returns ready.',
                'Configuration readiness API exposes Security Misconfiguration controls and fail-closed smoke checks.',
                'Configuration test plan API exposes the expected target test.',
                'Practice route list still includes configuration pages.',
                'Pint reports no style changes pending.',
            ],
            'commands' => [
                ...$checklist['commands'],
                'php artisan test --filter ConfigurationDeploymentPlanTest',
            ],
            'quality_gate' => $checklist['quality_gate'],
        ];
    }

    /**
     * Build preflight items from checklist review questions.
     *
     * @param  array<string, mixed>  $checklist
     * @return array<int, array{label: string, proof: string}>
     */
    private function preflight(array $checklist): array
    {
        return collect($checklist['review_questions'])
            ->map(fn (string $question): array => [
                'label' => $question,
                'proof' => 'Record the answer in the pull request summary before deployment.',
            ])
            ->values()
            ->all();
    }

    /**
     * Return ordered deployment steps for configuration changes.
     *
     * @return array<int, array{step: int, label: string, command: string|null}>
     */
    private function deploySteps(): array
    {
        return [
            [
                'step' => 1,
                'label' => 'Apply the config or environment value change in the smallest deployable patch.',
                'command' => null,
            ],
            [
                'step' => 2,
                'label' => 'Clear cached configuration before reading runtime values.',
                'command' => 'php artisan config:clear',
            ],
            [
                'step' => 3,
                'label' => 'Run focused configuration and quality-gate tests.',
                'command' => 'php artisan test --filter Configuration',
            ],
            [
                'step' => 4,
                'label' => 'Confirm practice routes are still discoverable.',
                'command' => 'php artisan route:list --path=practice',
            ],
        ];
    }

    /**
     * Return smoke checks for runtime verification.
     *
     * @return array<int, array{endpoint: string, expected: string}>
     */
    private function smokeChecks(array $readiness): array
    {
        $readinessSmoke = collect($readiness['deployment_smoke_matrix'])
            ->map(fn (array $smoke): array => [
                'endpoint' => '/api/practice/configuration-readiness',
                'expected' => sprintf('%s: %s', $smoke['check'], $smoke['fail_closed_action']),
            ])
            ->values()
            ->all();

        return [
            [
                'endpoint' => '/practice/configuration-readiness',
                'expected' => 'Page renders a ready quality status.',
            ],
            [
                'endpoint' => '/api/practice/configuration-readiness',
                'expected' => 'JSON data.quality_gate.status is ready.',
            ],
            [
                'endpoint' => '/api/practice/configuration-change-checklist',
                'expected' => 'JSON includes change_cards for app, auth, and quality gate.',
            ],
            ...$readinessSmoke,
        ];
    }

    /**
     * Build rollback steps from checklist cards.
     *
     * @param  array<string, mixed>  $checklist
     * @return array<int, array{area: string, action: string}>
     */
    private function rollbackSteps(array $checklist): array
    {
        return collect($checklist['change_cards'])
            ->map(fn (array $card): array => [
                'area' => $card['area'],
                'action' => $card['rollback'],
            ])
            ->values()
            ->all();
    }
}
