<?php

declare(strict_types=1);

namespace App\Services\Practice;

final class ConfigurationReadinessService
{
    /**
     * Create a readiness plan for core Laravel configuration.
     */
    public function __construct(
        private readonly PracticeQualityGateService $qualityGate,
    ) {}

    /**
     * Build a read-only readiness report for app and auth configuration.
     *
     * @return array<string, mixed>
     */
    public function build(): array
    {
        $checks = $this->checks();
        $baseline = $this->baselineFor($checks);

        return [
            'title' => 'Configuration Readiness Plan',
            'summary' => 'Read app and auth config as code, then protect the expected runtime contract with focused tests.',
            'checks' => $checks,
            'quality_gate' => $this->qualityGate->evaluate($baseline),
            'baseline' => $baseline,
            'commands' => [
                'php artisan test --filter ConfigurationReadinessTest',
                'php artisan test --filter PracticeQualityGate',
                'vendor\\bin\\pint --test',
            ],
            'practice_targets' => [
                'hub/config/app.php',
                'hub/config/auth.php',
                'hub/app/Services/Practice/PracticeQualityGateService.php',
            ],
            'next_steps' => [
                'Assert the default locale, fallback locale, and app URL in a feature test.',
                'Assert the default guard, user provider, and password broker contract.',
                'Keep environment-specific values in config and read them through config(), not env() inside services.',
            ],
        ];
    }

    /**
     * Return readiness checks for the current configuration snapshot.
     *
     * @return array<int, array{key: string, label: string, value: mixed, passed: bool, file: string}>
     */
    private function checks(): array
    {
        return [
            [
                'key' => 'app_name_present',
                'label' => 'Application name is configured.',
                'value' => config('app.name'),
                'passed' => filled(config('app.name')),
                'file' => 'hub/config/app.php',
            ],
            [
                'key' => 'app_url_present',
                'label' => 'Application URL is available for generated links.',
                'value' => config('app.url'),
                'passed' => filled(config('app.url')),
                'file' => 'hub/config/app.php',
            ],
            [
                'key' => 'locale_contract_present',
                'label' => 'Locale and fallback locale are defined.',
                'value' => config('app.locale').'/'.config('app.fallback_locale'),
                'passed' => filled(config('app.locale')) && filled(config('app.fallback_locale')),
                'file' => 'hub/config/app.php',
            ],
            [
                'key' => 'web_guard_uses_session',
                'label' => 'Web guard uses session authentication.',
                'value' => config('auth.guards.web.driver'),
                'passed' => config('auth.guards.web.driver') === 'session',
                'file' => 'hub/config/auth.php',
            ],
            [
                'key' => 'users_provider_uses_eloquent',
                'label' => 'Users provider resolves through Eloquent.',
                'value' => config('auth.providers.users.driver'),
                'passed' => config('auth.providers.users.driver') === 'eloquent',
                'file' => 'hub/config/auth.php',
            ],
            [
                'key' => 'password_broker_has_throttle',
                'label' => 'Password broker has a reset throttle.',
                'value' => config('auth.passwords.users.throttle'),
                'passed' => (int) config('auth.passwords.users.throttle') > 0,
                'file' => 'hub/config/auth.php',
            ],
        ];
    }

    /**
     * Convert configuration checks into the shared quality-gate input shape.
     *
     * @param  array<int, array{passed: bool}>  $checks
     * @return array{tests: int, assertions: int, failures: int, pint: bool}
     */
    private function baselineFor(array $checks): array
    {
        $failures = collect($checks)
            ->reject(fn (array $check): bool => $check['passed'])
            ->count();

        return [
            'tests' => count($checks),
            'assertions' => count($checks),
            'failures' => $failures,
            'pint' => true,
        ];
    }
}
