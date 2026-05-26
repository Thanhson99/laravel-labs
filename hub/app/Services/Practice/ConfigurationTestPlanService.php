<?php

declare(strict_types=1);

namespace App\Services\Practice;

final class ConfigurationTestPlanService
{
    /**
     * Create implementation-ready test plans for configuration contracts.
     */
    public function __construct(
        private readonly ConfigurationReadinessService $readiness,
    ) {}

    /**
     * Build PHPUnit-focused test guidance from the current readiness report.
     *
     * @return array<string, mixed>
     */
    public function build(): array
    {
        $readiness = $this->readiness->build();
        $checks = collect($readiness['checks']);

        return [
            'title' => 'Configuration Test Plan',
            'summary' => 'Turn app and auth configuration expectations into focused feature tests before changing runtime defaults.',
            'target_test' => 'hub/tests/Feature/ConfigurationReadinessTest.php',
            'source_files' => $readiness['practice_targets'],
            'test_groups' => [
                $this->groupFor('Application runtime contract', $checks->where('file', 'hub/config/app.php')->values()->all()),
                $this->groupFor('Authentication contract', $checks->where('file', 'hub/config/auth.php')->values()->all()),
                $this->groupFor('Quality-gate contract', [
                    [
                        'key' => 'quality_gate_ready_contract',
                        'label' => 'Quality gate marks the current configuration baseline as ready.',
                        'value' => $readiness['quality_gate']['status'],
                        'passed' => $readiness['quality_gate']['passed'],
                        'file' => 'hub/app/Services/Practice/PracticeQualityGateService.php',
                    ],
                ]),
            ],
            'snippet' => $this->snippet(),
            'commands' => [
                'php artisan test --filter ConfigurationReadinessTest',
                'php artisan test --filter ConfigurationTestPlanTest',
                'vendor\\bin\\pint --test',
            ],
            'quality_gate' => $readiness['quality_gate'],
            'baseline' => $readiness['baseline'],
        ];
    }

    /**
     * Build one grouped test-plan section.
     *
     * @param  array<int, array<string, mixed>>  $checks
     * @return array<string, mixed>
     */
    private function groupFor(string $name, array $checks): array
    {
        return [
            'name' => $name,
            'checks' => $checks,
            'assertions' => collect($checks)
                ->map(fn (array $check): string => $this->assertionFor($check))
                ->values()
                ->all(),
        ];
    }

    /**
     * Convert a readiness check into a concise PHPUnit assertion idea.
     *
     * @param  array<string, mixed>  $check
     */
    private function assertionFor(array $check): string
    {
        return match ($check['key']) {
            'app_name_present' => "expect(config('app.name'))->not->toBeEmpty();",
            'app_url_present' => "expect(config('app.url'))->not->toBeEmpty();",
            'locale_contract_present' => "expect(config('app.locale'))->toBe('en');",
            'web_guard_uses_session' => "expect(config('auth.guards.web.driver'))->toBe('session');",
            'users_provider_uses_eloquent' => "expect(config('auth.providers.users.driver'))->toBe('eloquent');",
            'password_broker_has_throttle' => "expect(config('auth.passwords.users.throttle'))->toBeGreaterThan(0);",
            'quality_gate_ready_contract' => "expect((new PracticeQualityGateService)->evaluate(\$baseline)['status'])->toBe('ready');",
            default => sprintf('expect(%s)->toBeTrue();', var_export((string) $check['key'], true)),
        };
    }

    /**
     * Return a starter test snippet learners can adapt.
     */
    private function snippet(): string
    {
        return <<<'PHP'
public function test_app_and_auth_configuration_contract_is_ready(): void
{
    $this->assertNotEmpty(config('app.name'));
    $this->assertNotEmpty(config('app.url'));
    $this->assertSame('en', config('app.locale'));
    $this->assertSame('session', config('auth.guards.web.driver'));
    $this->assertSame('eloquent', config('auth.providers.users.driver'));
    $this->assertGreaterThan(0, config('auth.passwords.users.throttle'));
}
PHP;
    }
}
