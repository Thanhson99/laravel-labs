<?php

declare(strict_types=1);

namespace App\Services\Practice;

final class ConfigurationRiskRegisterService
{
    /**
     * Create a risk register for app, auth, and quality-gate configuration practice.
     */
    public function __construct(
        private readonly ConfigurationPracticeDashboardService $dashboard,
    ) {}

    /**
     * Build risk cards for the current configuration practice state.
     *
     * @return array<string, mixed>
     */
    public function build(): array
    {
        $dashboard = $this->dashboard->build();
        $risks = $this->risks($dashboard);

        return [
            'title' => 'Configuration Risk Register',
            'summary' => 'Track configuration risks that can affect generated links, public practice access, quality-gate status, deployment safety, and evidence reuse.',
            'status' => $dashboard['status'],
            'risk_count' => count($risks),
            'highest_severity' => collect($risks)->pluck('severity')->contains('high') ? 'high' : 'medium',
            'risks' => $risks,
            'review_cadence' => [
                'Before changing config/app.php or config/auth.php.',
                'Before changing PracticeQualityGateService response shape.',
                'After running configuration deployment or release evidence checks.',
                'During day 3 spaced review when rollback should be rebuilt from memory.',
            ],
            'commands' => [
                ...$dashboard['commands'],
                'php artisan test --filter ConfigurationRiskRegisterTest',
            ],
        ];
    }

    /**
     * Return risk cards from dashboard state.
     *
     * @param  array<string, mixed>  $dashboard
     * @return array<int, array<string, mixed>>
     */
    private function risks(array $dashboard): array
    {
        return [
            [
                'key' => 'config-cache-stale',
                'severity' => 'medium',
                'area' => 'Application runtime',
                'signal' => 'Readiness API does not match the expected app URL, locale, or fallback locale.',
                'mitigation' => 'Run php artisan config:clear, then rerun the configuration readiness test.',
                'owner_route' => '/practice/configuration-readiness',
            ],
            [
                'key' => 'auth-contract-drift',
                'severity' => 'high',
                'area' => 'Authentication contract',
                'signal' => 'Guard, provider, model, or password broker values change without a matching assertion.',
                'mitigation' => 'Update the configuration test plan before enabling protected progress routes.',
                'owner_route' => '/practice/configuration-test-plan',
            ],
            [
                'key' => 'quality-gate-shape-break',
                'severity' => 'high',
                'area' => 'Quality gate contract',
                'signal' => 'A consumer cannot read status, passed, checks, or next_action from the quality-gate payload.',
                'mitigation' => 'Keep the response shape stable and add focused PracticeQualityGate tests.',
                'owner_route' => '/practice/configuration-change-checklist',
            ],
            [
                'key' => 'security-misconfiguration-release-blocker',
                'severity' => 'high',
                'area' => 'Security Misconfiguration',
                'signal' => 'Debug output, exposed secrets, broad CORS, missing headers, weak cookies, proxy drift, or public storage exposure is not tied to a fail-closed smoke check.',
                'mitigation' => 'Map the unsafe signal to a readiness control, release blocker, owner, rollback action, and deployment smoke check before release.',
                'owner_route' => '/practice/configuration-readiness',
            ],
            [
                'key' => 'release-evidence-missing',
                'severity' => $dashboard['status']['passed'] ? 'medium' : 'high',
                'area' => 'Release evidence',
                'signal' => 'Archive '.$dashboard['status']['archive_id'].' cannot cite smoke checks, rollback, or Pint evidence.',
                'mitigation' => 'Refresh release evidence and evidence archive before using the work in portfolio or interview answers.',
                'owner_route' => '/practice/configuration-evidence-archive',
            ],
        ];
    }
}
