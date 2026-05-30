<?php

declare(strict_types=1);

namespace App\Services\Practice;

final class ConfigurationMaintenanceRoadmapService
{
    /**
     * Build long-running maintenance guidance from archive refresh plans.
     */
    public function __construct(
        private readonly ConfigurationArchiveRefreshPlanService $refreshPlan,
    ) {}

    /**
     * Build a maintenance roadmap for configuration practice evidence.
     *
     * @return array<string, mixed>
     */
    public function build(): array
    {
        $refreshPlan = $this->refreshPlan->build();

        return [
            'title' => 'Configuration Maintenance Roadmap',
            'summary' => 'Turn configuration archive refresh work into a durable maintenance cadence with owners, signals, escalation, and recurring checks.',
            'archive_id' => $refreshPlan['archive_id'],
            'roadmap_status' => $refreshPlan['refresh_status'] === 'scheduled' ? 'active' : 'blocked',
            'cadence' => [
                [
                    'window' => 'Weekly',
                    'focus' => 'Refresh high-value proof.',
                    'action' => 'Run archive and pipeline tests, then update stale notes.',
                ],
                [
                    'window' => 'Before each release',
                    'focus' => 'Refresh Security Misconfiguration release blockers.',
                    'action' => 'Confirm debug, secrets, CORS, headers, cookies, proxies, and storage controls still have fail-closed smoke evidence.',
                ],
                [
                    'window' => 'Before each config PR',
                    'focus' => 'Protect app/auth contract changes.',
                    'action' => 'Review refresh triggers and remediation triggers before requesting review.',
                ],
                [
                    'window' => 'After each incident drill',
                    'focus' => 'Keep recovery evidence current.',
                    'action' => 'Refresh postmortem references and incident recovery source keys.',
                ],
            ],
            'owners' => [
                'Configuration learner' => 'Runs refresh commands and updates proof notes.',
                'Security reviewer' => 'Confirms release blockers, owners, rollback actions, and smoke evidence are still valid.',
                'Reviewer' => 'Checks that refreshed evidence still maps to routes, commands, and quality status.',
                'Maintainer' => 'Moves repeated refresh failures into remediation tasks.',
            ],
            'health_signals' => [
                'Quality-gate status remains ready.',
                'Security Misconfiguration release blockers still map to owners, rollback actions, and fail-closed smoke checks.',
                'Archive retrieval still finds incident and postmortem evidence.',
                'Portfolio brief still cites current source keys.',
                'Route documentation test still passes after route changes.',
            ],
            'escalation_paths' => collect($refreshPlan['remediation_triggers'])
                ->map(fn (string $trigger): array => [
                    'trigger' => $trigger,
                    'route' => '/practice/configuration-remediation-plan',
                    'action' => 'Open remediation before publishing updated evidence.',
                ])
                ->values()
                ->all(),
            'commands' => [
                ...$refreshPlan['commands'],
                'php artisan test --filter ConfigurationMaintenanceRoadmapTest',
            ],
            'quality_gate' => $refreshPlan['quality_gate'],
        ];
    }
}
