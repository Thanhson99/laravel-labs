<?php

declare(strict_types=1);

namespace App\Services\Practice;

final class ConfigurationRemediationPlanService
{
    /**
     * Create remediation tasks from configuration risks.
     */
    public function __construct(
        private readonly ConfigurationRiskRegisterService $riskRegister,
    ) {}

    /**
     * Build file-focused remediation tasks for configuration risks.
     *
     * @return array<string, mixed>
     */
    public function build(): array
    {
        $register = $this->riskRegister->build();
        $tasks = collect($register['risks'])
            ->map(fn (array $risk): array => $this->taskFor($risk))
            ->values()
            ->all();

        return [
            'title' => 'Configuration Remediation Plan',
            'summary' => 'Turn configuration risks into concrete repair tasks with target files, actions, verification commands, and completion criteria.',
            'status' => $register['status'],
            'risk_count' => $register['risk_count'],
            'task_count' => count($tasks),
            'tasks' => $tasks,
            'completion_criteria' => [
                'Every high-severity risk has a focused test or assertion.',
                'Config cache is cleared before runtime verification.',
                'Quality-gate consumers still receive status, passed, checks, and next_action.',
                'Release evidence can cite smoke checks, rollback, and Pint output.',
            ],
            'commands' => [
                ...$register['commands'],
                'php artisan test --filter ConfigurationRemediationPlanTest',
            ],
        ];
    }

    /**
     * Convert one risk card into a remediation task.
     *
     * @param  array<string, mixed>  $risk
     * @return array<string, mixed>
     */
    private function taskFor(array $risk): array
    {
        return [
            'risk_key' => $risk['key'],
            'severity' => $risk['severity'],
            'title' => $this->titleFor((string) $risk['key']),
            'target_files' => $this->targetFilesFor((string) $risk['key']),
            'action' => $risk['mitigation'],
            'owner_route' => $risk['owner_route'],
            'verification' => $this->verificationFor((string) $risk['key']),
            'done_signal' => $this->doneSignalFor((string) $risk['key']),
        ];
    }

    /**
     * Return a learner-friendly task title.
     */
    private function titleFor(string $key): string
    {
        return match ($key) {
            'config-cache-stale' => 'Refresh stale configuration cache before verification.',
            'auth-contract-drift' => 'Restore auth contract assertions before auth behavior expands.',
            'quality-gate-shape-break' => 'Protect the shared quality-gate response shape.',
            'release-evidence-missing' => 'Rebuild release evidence before reuse.',
            default => 'Repair configuration risk.',
        };
    }

    /**
     * Return files that should be inspected for one risk.
     *
     * @return array<int, string>
     */
    private function targetFilesFor(string $key): array
    {
        return match ($key) {
            'config-cache-stale' => [
                'hub/config/app.php',
                'hub/tests/Feature/ConfigurationReadinessTest.php',
            ],
            'auth-contract-drift' => [
                'hub/config/auth.php',
                'hub/tests/Feature/ConfigurationTestPlanTest.php',
            ],
            'quality-gate-shape-break' => [
                'hub/app/Services/Practice/PracticeQualityGateService.php',
                'hub/tests/Unit/Practice/PracticeQualityGateServiceTest.php',
            ],
            'release-evidence-missing' => [
                'hub/app/Services/Practice/ConfigurationReleaseEvidenceService.php',
                'hub/app/Services/Practice/ConfigurationEvidenceArchiveService.php',
            ],
            default => ['hub/INTEGRATION_NOTES.md'],
        };
    }

    /**
     * Return verification commands for one risk.
     *
     * @return array<int, string>
     */
    private function verificationFor(string $key): array
    {
        return match ($key) {
            'config-cache-stale' => [
                'php artisan config:clear',
                'php artisan test --filter ConfigurationReadinessTest',
            ],
            'auth-contract-drift' => [
                'php artisan test --filter ConfigurationTestPlanTest',
                'php artisan test --filter ConfigurationChangeChecklistTest',
            ],
            'quality-gate-shape-break' => [
                'php artisan test --filter PracticeQualityGate',
                'php artisan test --filter ConfigurationPracticeDashboardTest',
            ],
            'release-evidence-missing' => [
                'php artisan test --filter ConfigurationReleaseEvidenceTest',
                'php artisan test --filter ConfigurationEvidenceArchiveTest',
            ],
            default => ['vendor\\bin\\pint --test'],
        };
    }

    /**
     * Return the observable done signal for one risk.
     */
    private function doneSignalFor(string $key): string
    {
        return match ($key) {
            'config-cache-stale' => 'Readiness API reflects current config values after cache clear.',
            'auth-contract-drift' => 'Auth guard, provider, model, and broker expectations are covered by tests.',
            'quality-gate-shape-break' => 'Quality-gate payload keeps its shared ready/needs-work contract.',
            'release-evidence-missing' => 'Release evidence and archive cite smoke checks, rollback, and style proof.',
            default => 'The risk owner route renders and focused tests pass.',
        };
    }
}
