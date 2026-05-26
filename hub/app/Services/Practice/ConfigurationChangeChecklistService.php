<?php

declare(strict_types=1);

namespace App\Services\Practice;

final class ConfigurationChangeChecklistService
{
    /**
     * Create review checklists for Laravel configuration changes.
     */
    public function __construct(
        private readonly ConfigurationTestPlanService $testPlan,
    ) {}

    /**
     * Build a configuration change checklist from the current test plan.
     *
     * @return array<string, mixed>
     */
    public function build(): array
    {
        $testPlan = $this->testPlan->build();

        return [
            'title' => 'Configuration Change Checklist',
            'summary' => 'Review app and auth configuration changes before they affect routes, generated URLs, sessions, password resets, or tests.',
            'source_files' => $testPlan['source_files'],
            'change_cards' => [
                $this->appCard(),
                $this->authCard(),
                $this->qualityGateCard($testPlan),
            ],
            'review_questions' => [
                'Does this change belong in config instead of a service or controller?',
                'Will cached config behave the same after deployment?',
                'Which feature test proves the new runtime contract?',
                'Is there a rollback value that restores the previous behavior safely?',
            ],
            'commands' => [
                ...$testPlan['commands'],
                'php artisan config:clear',
                'php artisan route:list --path=practice',
            ],
            'quality_gate' => $testPlan['quality_gate'],
        ];
    }

    /**
     * Return checklist guidance for app configuration changes.
     *
     * @return array<string, mixed>
     */
    private function appCard(): array
    {
        return [
            'area' => 'Application runtime',
            'file' => 'hub/config/app.php',
            'watch_values' => [
                'app.name',
                'app.url',
                'app.locale',
                'app.fallback_locale',
                'app.debug',
            ],
            'impact' => 'Generated links, locale-sensitive output, error visibility, and runtime identity can change.',
            'before_change' => [
                'Record the current value and the environment variable that controls it.',
                'Decide whether the value is stable enough for a config assertion.',
                'Check whether the value affects rendered learning pages or API payloads.',
            ],
            'after_change' => [
                'Clear cached config before verifying behavior.',
                'Run the configuration readiness and test plan feature tests.',
                'Open the readiness API and confirm the quality gate still reports ready.',
            ],
            'rollback' => 'Restore the previous environment value or config default, then clear cached config.',
        ];
    }

    /**
     * Return checklist guidance for auth configuration changes.
     *
     * @return array<string, mixed>
     */
    private function authCard(): array
    {
        return [
            'area' => 'Authentication contract',
            'file' => 'hub/config/auth.php',
            'watch_values' => [
                'auth.defaults.guard',
                'auth.guards.web.driver',
                'auth.providers.users.driver',
                'auth.providers.users.model',
                'auth.passwords.users.throttle',
            ],
            'impact' => 'Session behavior, user lookup, password reset throttling, and future protected progress routes can change.',
            'before_change' => [
                'Identify whether the change affects public read-only pages or future authenticated progress.',
                'Keep model/provider changes aligned with migrations and factories before enabling auth flows.',
                'Confirm throttle changes still protect reset endpoints from rapid repeated requests.',
            ],
            'after_change' => [
                'Run auth configuration assertions before adding feature behavior.',
                'Check that public practice pages still render without authentication.',
                'Document any new env key in setup notes before relying on it.',
            ],
            'rollback' => 'Restore the previous guard, provider, broker, or throttle value and rerun the readiness checks.',
        ];
    }

    /**
     * Return checklist guidance for quality-gate changes.
     *
     * @param  array<string, mixed>  $testPlan
     * @return array<string, mixed>
     */
    private function qualityGateCard(array $testPlan): array
    {
        return [
            'area' => 'Quality gate contract',
            'file' => 'hub/app/Services/Practice/PracticeQualityGateService.php',
            'watch_values' => [
                'tests_exist',
                'assertions_exist',
                'tests_pass',
                'style_passes',
                'next_action',
            ],
            'impact' => 'Every readiness, plan, and pipeline page that reports ready or needs-work depends on this contract.',
            'before_change' => [
                'Add or update a unit test for each new quality-gate check.',
                'Confirm generated plans still expose actionable next actions.',
                'Keep the result shape stable for existing API consumers.',
            ],
            'after_change' => [
                'Run focused quality-gate tests and the configuration test plan.',
                'Confirm '.$testPlan['quality_gate']['status'].' remains the expected status for the current baseline.',
                'Run Pint because quality-gate guidance explicitly includes style checks.',
            ],
            'rollback' => 'Revert the added check or restore the previous response shape, then rerun all quality-gate consumers.',
        ];
    }
}
